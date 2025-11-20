<?php

namespace backend\actions\users;

use common\models\settings\Privileges;
use common\models\users\PrivilegesUsers;
use common\models\users\UserGroups;
use common\models\users\UserWork;
use common\repositories\AgentRepository;
use common\repositories\AuthAssignmentRepository;
use common\repositories\OfficeRepository;
use common\repositories\UserRepository;
use Exception;
use Yii;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class EditAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        // Elfogadjuk: /users/edit?id=124 VAGY /users/edit?uid=124
        $req    = Yii::$app->request;
        $userId = (int)($req->get('id', $req->get('uid')));

        if ($userId <= 0) {
            throw new BadRequestHttpException('Hiányzó vagy érvénytelen felhasználó azonosító.');
        }

        $user = UserRepository::getById($userId);
        if (!$user) {
            throw new NotFoundHttpException('Felhasználó nem található.');
        }

        if ($req->isPost) {
            $userData = (array)$req->post('User', []);

            $tx = Yii::$app->db->beginTransaction();
            try {
                // 1) A USER mezők (közvetlenül a user táblába)
                $this->applyUserFields($user, $userData);
                $user->save(false); // validáció ha kell, átrakható save(true)-ra

                // 2) Agent táblában jelenleg a név/irodák élnek – ezt megtartjuk
                AgentRepository::save($userId, $userData);

                // 3) Munkaidő / foglalkoztatás
                $this->saveUserWorkData($userData, $userId);

                // 4) Csoport hozzárendelés (RBAC)
                AuthAssignmentRepository::save($userId, $userData);

                $tx->commit();

                return $this->controller->redirect(Url::to(['/users']));
            } catch (Exception $e) {
                $tx->rollBack();
                // Fejlesztéshez dobd tovább; élesben inkább logold és írj flash üzenetet
                throw $e;
            }
        }

        return $this->controller->render('edit', [
            'usergroups'   => $this->getAuthItems(),
            'user'         => $user,
            'agent'        => AgentRepository::getAllByUserId($userId),
            'offices'      => OfficeRepository::getAllActiveAsArray(),
            'mygroup'      => $this->getUsersGroup($userId),
            'privileges'   => Privileges::find()->asArray()->all(),
            'myprivileges' => $this->getUsersPrivileges($userId),
            // 'guardians' => ...  // ha később kell
        ]);
    }

    /**
     * A form mezőit átmásolja a User AR objektumba (user tábla).
     */
    private function applyUserFields(\common\models\User $user, array $d): void
    {
        // kötelező/általános
        if (isset($d['username'])) $user->username = trim((string)$d['username']);
        if (!empty($d['password'])) $user->setPassword((string)$d['password']); // csak ha megadták
        if (isset($d['email']))    $user->email    = trim((string)$d['email']);

        // új oszlopok a user táblában
        if (isset($d['phone']))     $user->phone     = trim((string)$d['phone']);
        if (isset($d['street']))    $user->street    = trim((string)$d['street']);
        if (isset($d['street_no'])) $user->street_no = trim((string)$d['street_no']);
        if (isset($d['zip']))       $user->zip       = trim((string)$d['zip']);
        if (isset($d['city']))      $user->city      = trim((string)$d['city']);
        if (isset($d['iban']))      $user->iban      = strtoupper(trim((string)$d['iban']));

        // ha a User modellben vannak safeRules/attributes(), ezek a kulcsok legyenek felsorolva
    }

    private function saveUserWorkData(array $data, int $userId): void
    {
        $work = UserWork::findOne(['userId' => $userId]);
        if (!$work) {
            $work = new UserWork();
            $work->userId = $userId;
        }

        if (array_key_exists('workType', $data)) {
            $work->workType = $data['workType'];
        }
        if (array_key_exists('basicWorktime', $data)) {
            $work->basicWorktime = str_replace(',', '.', (string)$data['basicWorktime']);
        }

        $work->save(false);
    }

    private function getUsersPrivileges(int $userId): array
    {
        $group  = (string)$this->getUsersGroup($userId);
        $access = [];
        if ($group === '') return $access;

        $rows = PrivilegesUsers::find()
            ->select(['privilegesId'])
            ->andWhere(['group' => $group])
            ->andWhere(['userId' => [0, $userId]])
            ->andWhere(['status' => 1])
            ->asArray()
            ->all();

        foreach ($rows as $r) {
            $access[$group][] = (int)$r['privilegesId'];
        }
        return $access;
    }

    private function getUsersGroup(int $userId): ?string
    {
        return Yii::$app->db->createCommand(
            'SELECT item_name FROM auth_assignment WHERE user_id = :id'
        )->bindValue(':id', $userId)->queryScalar() ?: null;
    }

    /** @return array|ActiveRecord[] */
    private function getAuthItems(): array
    {
        return UserGroups::find()->asArray()->all();
    }
}
