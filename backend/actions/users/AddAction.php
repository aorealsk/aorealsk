<?php

namespace backend\actions\users;

use common\models\Agent;
use common\models\Commissions;
use common\models\User;
use common\models\users\UserGroups;
use Exception;
use Yii;
use yii\db\Expression;
use yii\helpers\Url;
use yii\base\Action;
use common\models\Office;

class AddAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        if (Yii::$app->request->isPost) {
            $userData = (array)Yii::$app->request->post('User', []);

            $tr = Yii::$app->db->beginTransaction();
            try {
                $userId = $this->saveUser($userData);

                // Irodák – ha nincs kiválasztva semmi, csinálunk 1 agent rekordot (office_id = NULL)
                $officeIds = (isset($userData['office_id']) && is_array($userData['office_id']) && count($userData['office_id']))
                    ? $userData['office_id']
                    : [null];

                foreach ($officeIds as $officeId) {
                    $this->saveAgent($userData, $officeId, $userId);
                }

                $tr->commit();
                return $this->controller->redirect(Url::to(['/users']));
            } catch (Exception $e) {
                $tr->rollBack();
                Yii::error('[AddAction] '.$e->getMessage(), __METHOD__);
                throw $e; // fejlesztéshez
            }
        }

        return $this->controller->render('add', [
            'usergroups'  => $this->getAuthItems(),
            'commissions' => $this->getCommissions(),
            'offices'     => $this->getOffices(),
        ]);
    }

    /**
     * Létrehozza a user rekordot + auth_assignment bejegyzést.
     * Visszaadja az új user ID-t.
     *
     * @throws \yii\db\Exception
     */
    private function saveUser(array $d): int
    {
        $user = new User();
        $user->auth_key   = Yii::$app->security->generateRandomString(32);
        $user->username   = trim((string)($d['username'] ?? ''));
        $user->email      = trim((string)($d['email'] ?? ''));
        $user->status     = User::STATUS_ACTIVE;
        $user->created_at = new Expression('NOW()');

        // Jelszó csak ha megadták
        if (!empty($d['password'])) {
            $user->setPassword((string)$d['password']);
        }

        // --- ÚJ MEZŐK a user táblában (csak ha a modelben léteznek) ---

        // Alap elérhetőség
        if (property_exists($user, 'phone') || $user->canSetProperty('phone')) {
            $user->phone = trim((string)($d['phone'] ?? ''));
        }

        // Lakcím
        foreach (['street','street_no','zip','city'] as $k) {
            if (property_exists($user, $k) || $user->canSetProperty($k)) {
                $user->{$k} = trim((string)($d[$k] ?? ''));
            }
        }

        // IBAN (nagybetű)
        if (property_exists($user, 'iban') || $user->canSetProperty('iban')) {
            $iban = trim((string)($d['iban'] ?? ''));
            $user->iban = $iban !== '' ? strtoupper($iban) : null;
        }

        // Születési dátum (ha van oszlop/attribútum, pl. date_of_birth)
        foreach (['date_of_birth','dob'] as $dobKey) {
            if ((property_exists($user, $dobKey) || $user->canSetProperty($dobKey)) && isset($d[$dobKey])) {
                $user->{$dobKey} = trim((string)$d[$dobKey]);
                break;
            }
        }

        // Ruhaméretek (ha már felvetted oszlopként)
        $sizeMap = [
            'pants_size' => 'pants_size',
            'shirt_size' => 'shirt_size',
            'shoe_size'  => 'shoe_size',
            // ha más néven vetted fel: 'nadrag' => 'pants_size', stb.
        ];
        foreach ($sizeMap as $formKey => $col) {
            if ((property_exists($user, $col) || $user->canSetProperty($col)) && isset($d[$formKey])) {
                $user->{$col} = trim((string)$d[$formKey]);
            }
        }

        // Törvényes képviselők – ha a user táblában tárolod ezeket
        $g1Map = [
            'guardian1_name'      => ['Guardian', 0, 'name'],
            'guardian1_relation'  => ['Guardian', 0, 'relation'],
            'guardian1_phone'     => ['Guardian', 0, 'phone'],
            'guardian1_email'     => ['Guardian', 0, 'email'],
            'guardian1_street'    => ['Guardian', 0, 'street'],
            'guardian1_street_no' => ['Guardian', 0, 'street_no'],
            'guardian1_zip'       => ['Guardian', 0, 'zip'],
            'guardian1_city'      => ['Guardian', 0, 'city'],
        ];
        $g2Map = [
            'guardian2_name'      => ['Guardian', 1, 'name'],
            'guardian2_relation'  => ['Guardian', 1, 'relation'],
            'guardian2_phone'     => ['Guardian', 1, 'phone'],
            'guardian2_email'     => ['Guardian', 1, 'email'],
            'guardian2_street'    => ['Guardian', 1, 'street'],
            'guardian2_street_no' => ['Guardian', 1, 'street_no'],
            'guardian2_zip'       => ['Guardian', 1, 'zip'],
            'guardian2_city'      => ['Guardian', 1, 'city'],
        ];

        // A formban Guardian[0][...] / Guardian[1][...] érkezik – próbáljuk onnan kiolvasni
        $guardianPosted = (array)Yii::$app->request->post('Guardian', []);
        foreach ([$g1Map, $g2Map] as $map) {
            foreach ($map as $col => [$root, $idx, $field]) {
                if ((property_exists($user, $col) || $user->canSetProperty($col)) &&
                    isset($guardianPosted[$idx][$field])) {
                    $user->{$col} = trim((string)$guardianPosted[$idx][$field]);
                }
            }
        }

        if (!$user->save(false)) {
            throw new Exception('User mentése sikertelen.');
        }

        // RBAC hozzárendelés (auth_assignment)
        $role = (string)($d['auth_assignment'] ?? '');
        if ($role !== '') {
            Yii::$app->db->createCommand(
                "INSERT INTO auth_assignment (item_name, user_id, created_at)
                 VALUES (:role, :uid, UNIX_TIMESTAMP())",
                [':role' => $role, ':uid' => (int)$user->id]
            )->execute();
        }

        return (int)$user->id;
    }

    /**
     * Létrehoz egy agent rekordot a név/telefon + irodakapcsolat mezőkkel.
     * $officeId lehet null is – ekkor is készül sor (office_id = NULL).
     * (Ha már az index az u.phone-t és u.name_first/last-ot mutatja, az Agent-be nem kötelező duplikálni.)
     */
    private function saveAgent(array $d, $officeId, int $userId): void
    {
        $agent = new Agent();
        $agent->user_id   = $userId;
        $agent->office_id = ($officeId !== '' ? $officeId : null);

        // A régi rendszerhez igazodva – ma még sok helyen Agentből olvasod a nevet/telefont
        $agent->name_first = trim((string)($d['name_first'] ?? ''));
        $agent->name_last  = trim((string)($d['name_last']  ?? ''));
        $agent->phone      = trim((string)($d['phone']      ?? ''));
        $agent->email      = trim((string)($d['email']      ?? ''));

        // Provízia – ha van ilyen oszlop az Agent-ben
        if (isset($d['commission']) && $d['commission'] !== '') {
            if (property_exists($agent, 'commission') || $agent->canSetProperty('commission')) {
                $agent->commission = $d['commission'];
            }
        }

        if (!$agent->save(false)) {
            throw new Exception('Agent mentése sikertelen.');
        }
    }

    private function getOffices()
    {
        return Office::find()->where(['status' => 1])->asArray()->all();
    }

    private function getAuthItems()
    {
        return UserGroups::find()->asArray()->all();
    }

    private function getCommissions()
    {
        return Commissions::find()->where(['status' => 1])->asArray()->all();
    }
}
