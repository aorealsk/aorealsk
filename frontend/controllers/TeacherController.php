<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;

use common\models\teachers\Teacher;
use common\models\User;
use common\models\partners\Partners;
use common\models\partners\PartnerType;
use common\models\schools\StudyField;

class TeacherController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'dual';

        if (Yii::$app->request->isPost) {
            $reg = Yii::$app->request->post('Reg', []);
            if (($reg['type'] ?? '') === 'teacher') {
                $t = $reg['teacher'] ?? [];
                $tx = Yii::$app->db->beginTransaction();
                try {
                    // 1) User létrehozása vagy előkeresése email alapján
                    $user = User::find()->where(['email' => $t['email'] ?? null])->one();
                    if ($user === null) {
                        $user = new User();
                        $user->username = $this->makeUsername($t['first_name'] ?? '', $t['last_name'] ?? '');
                        // ha már létezik ilyen username, kapjon suffixet
                        $user = $this->ensureUniqueUsername($user);

                        $plainPwd = $this->generateInitialPassword($t['first_name'] ?? '', $t['last_name'] ?? '');
                        $user->setPassword($plainPwd); // ha nincs setPassword, használd: $user->password_hash = Yii::$app->security->generatePasswordHash($plainPwd);
                        // auth_key biztonságosan
                        if (method_exists($user, 'generateAuthKey')) {
                            $user->generateAuthKey();
                        } else {
                            $user->auth_key = Yii::$app->security->generateRandomString();
                        }
                        $user->email = $t['email'] ?? null;
                        if (!$user->save()) {
                            throw new \Exception('User mentése sikertelen: '. json_encode($user->getFirstErrors()));
                        }
                    }

                    // 2) Teacher rekord
                    $teacher = new Teacher();
                    $teacher->UserID = $user->id;
                    $teacher->FirstName = $t['first_name'] ?? null;
                    $teacher->LastName  = $t['last_name'] ?? null;
                    $teacher->Gender    = $t['gender'] ?? null;

                    // BirthDate YYYY-MM-DD
                    if (!empty($t['birthdate']['year']) && !empty($t['birthdate']['month']) && !empty($t['birthdate']['day'])) {
                        $teacher->BirthDate = sprintf(
                            '%04d-%02d-%02d',
                            (int)$t['birthdate']['year'],
                            (int)$t['birthdate']['month'],
                            (int)$t['birthdate']['day']
                        );
                    }

                    // mértékek
                    $teacher->Height    = $t['height'] ?? null;
                    $teacher->Weight    = $t['weight'] ?? null; // a view-ben lesz weight mező
                    $teacher->FootSize  = $t['foot_size'] ?? null;
                    $teacher->ShirtSize = $t['shirt_size'] ?? null; // numerikus (pl. 44, 46, 48...)
                    $teacher->WaistLine = $t['waist'] ?? null;
                    $teacher->TrouserLenght = $t['trouser_length'] ?? null;

                    // pénzügy/nyelv
                    $teacher->IBAN            = $t['iban'] ?? null;
                    $teacher->PrimaryLanguage = $t['primary_language'] ?? null; // anyanyelv
                    $teacher->Languages       = $t['languages'] ?? null; // vesszővel elválasztott lista

                    // elérhetőség
                    $teacher->ContactStreet     = $t['contact_street'] ?? null;
                    $teacher->ContactBuildingNr = $t['contact_building_nr'] ?? null;
                    $teacher->ContactTown       = $t['contact_town'] ?? null;
                    $teacher->ContactTownID     = $t['contact_town_id'] ?? null; // itt használhatod a PSČ-t
                    $teacher->ContactCountry    = $t['contact_country'] ?? null;
                    $teacher->EmailAddress      = $t['email'] ?? null;
                    $teacher->PhoneNumber       = $t['phone'] ?? null;

                    // iskola + szak
                    $teacher->SchoolID     = !empty($t['school']) ? (int)$t['school'] : null;

                    // PrimaryStudy varchar: elmentheted a szak NEVÉT vagy az ID-t stringben. Itt a nevet mentem:
                    if (!empty($t['study_field'])) {
                        $sf = StudyField::findOne((int)$t['study_field']);
                        $teacher->PrimaryStudy = $sf ? $sf->name : (string)$t['study_field'];
                    }

                    $teacher->LeaderOfClass = $t['leader_of_class'] ?? null;

                    if (!$teacher->save()) {
                        throw new \Exception('Teacher mentése sikertelen: '. json_encode($teacher->getFirstErrors()));
                    }

                    $tx->commit();
                    return $this->redirect(Url::to(['/teacher/thank-you']));
                } catch (\Throwable $e) {
                    $tx->rollBack();
                    // ideiglenes hibajelzés — nálad mehet flash-be / logger-be
                    Yii::error($e->getMessage(), 'teacher');
                }
            }
        }

        return $this->render('index', [
            'fields'   => StudyField::find()->all(),
            'schools'  => Partners::find()->where('status = 1 and partner_type=' . PartnerType::SCHOOL)->all(),
        ]);
    }

    public function actionThankYou()
    {
        return $this->render('thank-you');
    }

    private function makeUsername(string $first, string $last): string
    {
        $u = \common\helpers\StringHelper::replaceAccents($first . $last);
        return strtolower(preg_replace('/\s+/', '', $u));
    }

    private function ensureUniqueUsername(User $user): User
    {
        $base = $user->username ?: 'user';
        $candidate = $base;
        $i = 1;
        while (User::find()->where(['username' => $candidate])->exists()) {
            $candidate = $base . $i;
            $i++;
        }
        $user->username = $candidate;
        return $user;
    }

    private function generateInitialPassword(string $first, string $last): string
    {
        return '+' . ucfirst($first) . ucfirst($last) . date('Y') . '+';
    }
}
