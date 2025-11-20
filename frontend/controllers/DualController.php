<?php

namespace frontend\controllers;

use common\helpers\StringHelper;
use common\models\Agent;
use common\models\partners\PartnerContactPersons;
use common\models\partners\PartnerDetails;
use common\models\partners\Partners;
use common\models\partners\PartnerType;
use common\models\schools\StudentLanguage;
use common\models\schools\StudentLegalRepresentative;
use common\models\schools\StudyField;
use SebastianBergmann\Type\Exception;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use common\models\schools\Students;
use common\models\User;

class DualController extends Controller
{
    public function actionIndex()
    {
        /*$language  = Yii::$app->request->get('language') ?? 'sk';
        Yii::$app->language = $language;*/
        $this->layout = 'dual';

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Reg');
            $tr = Yii::$app->db->beginTransaction();
            if ($data['type'] === 'dual_student') {
                $studentData = $data['student'];
                try {
                    $student = $this->saveStudent($studentData);
                    $this->saveLanguage($student, $studentData);
                    $this->saveParents($student, $studentData);
                    $user = $this->saveNewUser($student);
                    $student->userId = $user->id;
                    $student->update(false);
                    $this->saveAgentData($student, $user);
                    $tr->commit();
                    return $this->redirect(Url::to(['/dual/thank-you']));
                } catch (Exception $e) {
                    $tr->rollBack();
                }
            } else {
                try {
                    $partner = new Partners();
                    $partner->partner_type = PartnerType::getValue($data['type']);
                    $partner->partner_name = $data['name'];
                    $partner->address = $data['address'];
                    $partner->town = $data['town'];
                    $partner->zip = $data['zip'];
                    $partner->save();

                    $contactPerson = new PartnerContactPersons();
                    $contactPerson->partner_id = $partner->id;
                    $contactPerson->first_name = $data['contact_first_name'];
                    $contactPerson->last_name = $data['contact_last_name'];
                    $contactPerson->email = $data['contact_email'];
                    $contactPerson->phone = $data['contact_phone'];
                    $contactPerson->save();

                    $tr->commit();
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $tr->rollBack();
                }
            }
        }
        return $this->render('index', [
            'fields' => StudyField::find()->all(),
            'schools' => Partners::find()->where('status = 1 and partner_type=' . PartnerType::SCHOOL)->all(),
            'partners' => Partners::find()->where('status = 1 and partner_type=' . PartnerType::DUAL_COMPANY)->all(),
        ]);
    }

    protected function saveAgentData(Students $student, User $user): void
    {
        $agent = new Agent();
        $agent->user_id = $user->id;
        $agent->comission = 4;
        $agent->name_first = $student->firstName;
        $agent->name_last = $student->lastName;
        $agent->office_id = 3;
        $agent->phone = $student->phoneNumber;
        $agent->save();
    }

    private function saveNewUser(Students $student): User
    {
        $username = $this->makeUsername($student->firstName, $student->lastName);
        $password = '+' . ucfirst($student->firstName) . ucfirst($student->lastName) . date('Y') . '+';

        $user = new User();
        $user->username = $username;
        $user->email = $student->email;
        $user->auth_key = 1;
        $user->password_hash = Yii::$app->security->generatePasswordHash($password);
        $user->save();

        return $user;
    }

    protected function saveParents(Students $student, array $studentData): void
    {
        $mother = new StudentLegalRepresentative();
        $mother->studentId = $student->id;
        $mother->firstName = $studentData['mother']['first_name'];
        $mother->lastName = $studentData['mother']['last_name'];
        $mother->email = $studentData['mother']['email'];
        $mother->phoneNumber = $studentData['mother']['phone'];
        $mother->birthDate = $studentData['mother']['birthdate']['year'] . '-' .
            $studentData['mother']['birthdate']['month'] . '-' . $studentData['mother']['birthdate']['day'];
        $mother->ssn = $studentData['mother']['ssn'];
        $mother->save();

        $father = new StudentLegalRepresentative();
        $father->studentId = $student->id;
        $father->firstName = $studentData['father']['first_name'];
        $father->lastName = $studentData['father']['last_name'];
        $father->email = $studentData['father']['email'];
        $father->phoneNumber = $studentData['father']['phone'];
        $father->birthDate = $studentData['father']['birthdate']['year'] . '-' .
            $studentData['father']['birthdate']['month'] . '-' . $studentData['father']['birthdate']['day'];
        $father->save();
    }

    protected function saveLanguage(Students $student, array $studentData): void
    {
        $lang = new StudentLanguage();
        $lang->studentId = $student->id;
        $lang->motherLanguage = $studentData['jazyk'] ?? null;
        $lang->save();
    }

    protected function saveStudent(array $studentData): Students
    {
        $student = new Students();
        $student->gender = $studentData['gender'] ?? null;
        $student->firstName = $studentData['first_name'];
        $student->lastName = $studentData['last_name'];
        $student->ssn = $studentData['ssn'];
        $student->iban = $studentData['iban'] ?? null;
        $student->phoneNumber = $studentData['phone'] ?? null;
        $student->email = $studentData['email'];
        $student->partnerId = $studentData['school'];
        $student->birthDate = $studentData['birthdate']['year'] . '-' .
            $studentData['birthdate']['month'] . '-' . $studentData['birthdate']['day'];
        $student->studyFieldId = $studentData['study_field'];
        $student->height = $studentData['height'] ?? null;
        $student->footSize = $studentData['foot_size'] ?? null;
        $student->tshirt = $studentData['tshirt'] ?? null;
        $student->waist = $studentData['waist'] ?? null;
        $student->length = $studentData['length'] ?? null;
        $student->fullAddress = $studentData['address'] ?? null;
        $student->town = $studentData['town'] ?? null;
        $student->zip = $studentData['zip'] ?? null;
        $student->status = 1;
        $student->save();

        return $student;
    }

    protected function makeUsername(string $firstName, string $lastName): string
    {
        return strtolower(StringHelper::replaceAccents($firstName)) .
            strtolower(StringHelper::replaceAccents($lastName));
    }

    protected function sendEmailToPartner(Students $student): void
    {
        Yii::$app->mailer->compose(
            ['html' => 'registered-text'],
            [
                'customer' => $student->getFullName(),
            ]
        )
            ->setFrom('info@aoreal.sk')
            ->setTo($student->email)
            ->setSubject('')
            ->send();
    }

    protected function sendEmailToStudent(): void
    {
        //
    }

    public function actionThankYou()
    {
        return $this->render('thank-you');
    }


    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionGetStudyFields()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $schoolId = Yii::$app->request->post('school');

        $list = [];
        if ($schoolId > 0) {
            $rows = PartnerDetails::find()
                ->where([
                    'partner_id' => $schoolId,
                    'field_name' => 'field'
                ])
                ->all();
            foreach ($rows as $row) {
                $details = json_decode($row['field_value'], true);
                foreach ($details as $detail) {
                    $label = StudyField::find()
                        ->select('name')
                        ->where('id=' . $detail['id'])
                        ->one();
                    $list[] = [
                        'value' => $detail['id'],
                        'label' => $label['name'],
                    ];
                }
            }
        }

        return [
            'status' => 'ok',
            'list' => $list,
        ];
    }

    public function actionGetPartners()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $studyFieldId = Yii::$app->request->post('sfield');
        $partners = Partners::find()
            ->select(['id', 'partner_name','town'])
            ->where('status=1 and partner_type > 1')
            ->asArray()
            ->all();
        $pocetPartnerov = count($partners);
        return [
            'status' => 'ok',
            'list' => ''
        ];
    }
}
