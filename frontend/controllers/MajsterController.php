<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;

use common\models\majstery\Majstery;
use common\models\User;
use common\models\schools\StudyField;

class MajsterController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'dual';

        if (Yii::$app->request->isPost) {
            $reg = Yii::$app->request->post('Reg', []);
            if (($reg['type'] ?? '') === 'majster') {
                $t  = $reg['majster'] ?? [];
                $tx = Yii::$app->db->beginTransaction();
                try {
                    // 1) User: email alapján reuse vagy létrehozás
                    $user = User::find()->where(['email' => $t['email'] ?? null])->one();
                    if ($user === null) {
                        $user = new User();
                        $user->username = $this->makeUsername($t['first_name'] ?? '', $t['last_name'] ?? '');
                        $user = $this->ensureUniqueUsername($user);

                        $plainPwd = $this->generateInitialPassword($t['first_name'] ?? '', $t['last_name'] ?? '');
                        if (method_exists($user, 'setPassword')) {
                            $user->setPassword($plainPwd);
                        } else {
                            $user->password_hash = Yii::$app->security->generatePasswordHash($plainPwd);
                        }
                        if (method_exists($user, 'generateAuthKey')) {
                            $user->generateAuthKey();
                        } else {
                            $user->auth_key = Yii::$app->security->generateRandomString();
                        }
                        $user->email = $t['email'] ?? null;
                        if (!$user->save()) {
                            throw new \Exception('User mentése sikertelen: ' . json_encode($user->getFirstErrors()));
                        }
                    }

                    // 2) Majstery rekord feltöltése
                    $m = new Majstery();
                    $m->UserID      = $user->id;
                    $m->FirstName   = $t['first_name'] ?? null;
                    $m->LastName    = $t['last_name']  ?? null;
                    $m->Gender      = $t['gender']     ?? null;

                    if (!empty($t['birthdate']['year']) && !empty($t['birthdate']['month']) && !empty($t['birthdate']['day'])) {
                        $m->BirthDate = sprintf('%04d-%02d-%02d',
                            (int)$t['birthdate']['year'], (int)$t['birthdate']['month'], (int)$t['birthdate']['day']
                        );
                    }

                    $m->Height      = $t['height']     ?? null;
                    $m->Weight      = $t['weight']     ?? null;
                    $m->FootSize    = $t['foot_size']  ?? null;
                    $m->ShirtSize   = $t['shirt_size'] ?? null;
                    $m->WaistLine   = $t['waist']      ?? null;
                    $m->TrouserLenght = $t['trouser_length'] ?? null;

                    $m->IBAN            = $t['iban']             ?? null;
                    $m->PrimaryLanguage = $t['primary_language'] ?? null;
                    $m->Languages       = $t['languages']        ?? null;

                    $m->ContactStreet     = $t['contact_street']      ?? null;
                    $m->ContactBuildingNr = $t['contact_building_nr'] ?? null;
                    $m->ContactTown       = $t['contact_town']        ?? null;
                    $m->ContactTownID     = $t['contact_town_id']     ?? null;
                    $m->ContactCountry    = $t['contact_country']     ?? null;

                    $m->EmailAddress = $t['email'] ?? null;
                    $m->PhoneNumber  = $t['phone'] ?? null;

                    $m->LastFinishedSchool = $t['last_finished_school'] ?? null;

                    // StudyField dropdown: value = id → Majstery.TraineeFor (int)
                    $m->TraineeFor = !empty($t['trainee_for']) ? (int)$t['trainee_for'] : null;

                    if (!$m->save()) {
                        throw new \Exception('Majster mentése sikertelen: ' . json_encode($m->getFirstErrors()));
                    }

                    $tx->commit();
                    return $this->redirect(Url::to(['/majster/thank-you']));
                } catch (\Throwable $e) {
                    $tx->rollBack();
                    Yii::error($e->getMessage(), 'majster');
                }
            }
        }

        // StudyField lista a legördülőhöz: "Név (code)"
        $fields = StudyField::find()->select(['id','name','code'])->orderBy(['name'=>SORT_ASC])->asArray()->all();

        return $this->render('index', [
            'fields' => $fields,
        ]);
    }

    public function actionThankYou()
    {
        $this->layout = 'dual';
        return $this->render('thank-you');
    }

    private function makeUsername(string $first, string $last): string
    {
        $base = strtolower(preg_replace('/\s+/', '', $first . $last));
        return $base ?: 'user';
    }

    private function ensureUniqueUsername(User $user): User
    {
        $base = $user->username;
        $cand = $base;
        $i = 1;
        while (User::find()->where(['username' => $cand])->exists()) {
            $cand = $base . $i;
            $i++;
        }
        $user->username = $cand;
        return $user;
    }

    private function generateInitialPassword(string $first, string $last): string
    {
        return '+' . ucfirst($first) . ucfirst($last) . date('Y') . '+';
    }
}
