<?php

namespace frontend\controllers;

use common\models\Mesto;
use common\models\partners\Partners;
use common\models\partners\PartnerType;
use common\models\schools\StudentDetails;
use common\models\schools\StudentSchool;
use common\models\sys\SysLog;
use Yii;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use common\models\schools\Students;

class OpenDaysController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $this->layout = 'dual';

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Reg', []);

            // Minimális ellenőrzés: ha nagyon hiányos az adat, ne is próbáljuk menteni
            if (
                empty($data['first_name']) ||
                empty($data['last_name'])  ||
                empty($data['email'])      ||
                empty($data['phone'])
            ) {
                return $this->render('form-error');
            }

            $tr = Yii::$app->db->beginTransaction();
            try {
                // Város és iskola azonosító ellenőrzése
                $townId   = $data['town']            ?? null;
                $schoolId = $data['primary_school']  ?? null;

                if (!$townId || !$schoolId) {
                    throw new \RuntimeException('Town or primary school not selected.');
                }

                $town = Mesto::findOne($townId);
                $zs   = Partners::findOne($schoolId);

                if (!$town || !$zs) {
                    throw new \RuntimeException('Town or primary school not found in DB.');
                }

                $student = new Students();
                $student->firstName         = $data['first_name'];
                $student->lastName          = $data['last_name'];
                $student->email             = $data['email'];
                $student->phoneNumber       = $data['phone'];
                $student->candidate         = Students::CANDIDATE;
                $student->primarySchoolTown = $town->nazov_obce;
                $student->primarySchoolName = $zs->partner_name;

                // Munkaruha adatok → student tábla mezők
                $student->height   = $data['height_range'] ?? null;  // pl.: "176-188"
                $student->footSize = $data['shoe_size']    ?? null;  // 35–48
                $student->tshirt   = $data['shirt_size']   ?? null;  // S, M, L, ...
                $student->waist    = $data['pants']        ?? null;  // nadrág méret
                $student->length   = $data['jacket']       ?? null;  // kabát méret

                if (!$student->save()) {
                    throw new \RuntimeException('Student save failed: ' . json_encode($student->errors));
                }

                foreach (StudentDetails::getFields() as $field) {
                    if (!array_key_exists($field, $data)) {
                        continue;
                    }

                    $details = new StudentDetails();
                    $details->student_id = $student->id;
                    $details->field_name = $field;

                    if (is_array($data[$field])) {
                        $details->field_value = json_encode($data[$field]);
                    } else {
                        $details->field_value = $data[$field];
                    }

                    if (!$details->save()) {
                        throw new \RuntimeException('StudentDetails save failed for field ' . $field);
                    }
                }

                $school = new StudentSchool();
                $school->student_id = $student->id;
                $school->school_id  = $zs->id;
                $school->year_from  = date("Y") - 1;
                $school->year_to    = date("Y");
                $school->class      = '9';

                if (!$school->save()) {
                    throw new \RuntimeException('StudentSchool save failed: ' . json_encode($school->errors));
                }

                $tr->commit();

                // Köszönő email a diáknak
                $this->sendThankYouEmail($student);

                // Admin email részletes adatokkal
                $this->sendAdminEmail($student, $data);

                // Siker – köszönő oldal
                return $this->redirect(Url::to(['/open-days/thank-you']));
            } catch (\Throwable $e) {
                $tr->rollBack();
                // Logoljuk, de a felhasználónak szép üzenet menjen
                SysLog::WriteError(getmypid(), __CLASS__, $e->getMessage(), __LINE__);
                Yii::error($e->getMessage(), __METHOD__);

                // Szép hibaoldal a usernek
                return $this->render('form-error');
            }
        }

        return $this->render('index');
    }

    /**
     * @throws Exception
     */
    public function actionGetCities()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $q = Yii::$app->request->post('q');

        $list = Yii::$app->db->createCommand("
            SELECT 
                m.id, 
                CONCAT(m.nazov_obce,' (okr. ',o.name,' / ',s.`iso_kod`,')') AS obec 
            FROM 
                mesto m 
            JOIN 
                okres o ON m.okres_id=o.id 
            JOIN
                stat s ON m.stat_id=s.id
            WHERE 
                m.nazov_obce LIKE '%$q%'
        ")->queryAll();

        $result = [];
        array_walk($list, function ($item) use (&$result) {
            $result[$item['id']] = $item['obec'];
        });

        return ['status'=>'ok','items'=>$result];
    }

    /**
     * @throws Exception
     */
    public function actionGetPrimarySchools()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $q = Yii::$app->request->post('sid');
        $list = Yii::$app
            ->db
            ->createCommand("
                select
                    id, partner_name, address
                from 
                    partners
                where
                    town in (select nazov_obce from mesto where id in ($q)) AND partner_type=". PartnerType::PRIMARY_SCHOOL)
            ->queryAll();
        $result = [];
        array_walk($list, function ($item) use (&$result) {
            $result[] = [
                'id' => $item['id'],
                'text' => $item['partner_name'] . ', ' . $item['address']
                ];
        });

        return ['status'=>'ok','items'=>$result];
    }

    public function actionThankYou()
    {
        return $this->render('thank-you');
    }

    /**
     * Köszönő email a diáknak (meglévő viselkedés).
     */
    protected function sendThankYouEmail(Students $student)
    {
        Yii::$app->mailer->compose(
            ['text' => 'prereg-text'],
            [
                'student_name' => $student->firstName,
            ]
        )
            ->setFrom('info@aoreal.sk')
            ->setCc('szabo.balazs@aoreal.sk')
            ->setTo($student->email)
            ->setSubject('Regisztráció / Registrácia')
            ->send();
    }

    /**
     * Szépen formázott, kétnyelvű admin email minden adattal.
     */
    protected function sendAdminEmail(Students $student, array $data): void
    {
        $facebook   = $data['facebook'] ?? '-';

        $height     = $data['height_range'] ?? '-';
        $pants      = $data['pants']        ?? '-';
        $jacket     = $data['jacket']       ?? '-';
        $gloves     = $data['gloves']       ?? '-';
        $shoeSize   = $data['shoe_size']    ?? '-';
        $shirtSize  = $data['shirt_size']   ?? '-';

        // Kollégium
        $internatRaw = $data['internat'] ?? null;
        if ($internatRaw === 'yes') {
            $internatText = 'Igényel kollégiumot / Chce internát';
        } elseif ($internatRaw === 'no') {
            $internatText = 'Nem igényel kollégiumot / Nechce internát';
        } else {
            $internatText = 'Nincs megadva / Nevyplnené';
        }

        // Menza
        $canteenRaw = $data['canteen'] ?? null;
        if ($canteenRaw === 'yes') {
            $canteenText = 'Szeretne menzára járni / Chce sa stravovať v školskej jedálni';
        } elseif ($canteenRaw === 'no') {
            $canteenText = 'Nem szeretne menzára járni / Nechce sa stravovať v školskej jedálni';
        } else {
            $canteenText = 'Nincs megadva / Nevyplnené';
        }

        // Marketing hozzájárulás
        $consentText = !empty($data['consent'])
            ? 'Megadta a marketing hozzájárulást / Udelil(a) marketingový súhlas'
            : 'NEM adta meg a marketing hozzájárulást / NEudelil(a) marketingový súhlas';

        // Partner / dual prax
        $partnerChoice = $data['partner']['choice'] ?? 'none';
        $partnerName    = $data['partner']['name']    ?? '';
        $partnerContact = $data['partner']['contact'] ?? '';
        $partnerEmail   = $data['partner']['email']   ?? '';
        $partnerPhone   = $data['partner']['phone']   ?? '';

        if ($partnerChoice === 'aors') {
            $partnerChoiceText = 'ALPHA-OMEGA REAL SOLUTIONS s.r.o.-nál szeretne praxolni / Chce absolvovať prax u firmy ALPHA-OMEGA REAL SOLUTIONS s.r.o.';
        } elseif ($partnerChoice === 'other') {
            $partnerChoiceText = 'Más cégnél szeretne praxolni / Chce absolvovať prax v inej firme';
        } else {
            $partnerChoiceText = 'Még nem döntött / Ešte nie je rozhodnutý(á)';
        }

        // Szakok – ID → név
        $fieldNames = [
            16 => 'kőműves / murár',
            1  => 'asztalos / stolár',
            5  => 'festő / maliar',
            3  => 'ács / tesár',
            17 => 'víz-, gáz- és központifűtés szerelő / inštalatér',
            19 => 'virágkötő / viazač – aranžér kvetín',
            11 => 'építészet / staviteľstvo',
            15 => 'épületgépész-technikus / mechanik stavebnoinštalačných zariadení',
            20 => 'műszaki líceum – építészeti irányzat / technické lýceum',
            21 => 'kerttervezés, dekoratőr és virágkötészet / záhradníctvo – viazačstvo a aranžérstvo',
        ];

        $fieldsText = '';
        if (!empty($data['fields']) && is_array($data['fields'])) {
            foreach ($data['fields'] as $id => $info) {
                $ord = $info['ord'] ?? 0;
                if ((int)$ord > 0) {
                    $label = $fieldNames[$id] ?? ('Odbor ID ' . $id);
                    $fieldsText .= "- {$label}: {$ord}\n";
                }
            }
        }
        if ($fieldsText === '') {
            $fieldsText = "Nincs megadott sorrend / Bez zadaného poradia\n";
        }

        $body  = "Új open day regisztráció / Nová registrácia na Deň otvorených dverí\n";
        $body .= "================================================================\n\n";

        $body .= "1) Személyes adatok / Osobné údaje\n";
        $body .= "----------------------------------\n";
        $body .= "Név / Meno: {$student->firstName} {$student->lastName}\n";
        $body .= "Email: {$student->email}\n";
        $body .= "Telefon / Telefón: {$student->phoneNumber}\n";
        $body .= "Facebook profil: " . ($facebook !== '' ? $facebook : '-') . "\n\n";

        $body .= "2) Munkaruha méretek / Veľkosť pracovného oblečenia\n";
        $body .= "--------------------------------------------------\n";
        $body .= "Magasság (cm) / Výška (cm): {$height}\n";
        $body .= "Nadrág / Nohavice (derék): {$pants}\n";
        $body .= "Kabát / Bunda (hossz): {$jacket}\n";
        $body .= "Cipő / Obuv: {$shoeSize}\n";
        $body .= "Trikó / Tričko: {$shirtSize}\n";
        $body .= "Kesztyű / Rukavice: {$gloves}\n\n";

        $body .= "3) Iskolák / Školy\n";
        $body .= "------------------\n";
        $body .= "Általános iskola város / ZŠ mesto: {$student->primarySchoolTown}\n";
        $body .= "Általános iskola neve / ZŠ názov: {$student->primarySchoolName}\n";
        $body .= "Középiskola / Stredná škola: SOŠ stavebná s VJM, Dunajská Streda, Ul. Gyulu Szabóa 1\n\n";

        $body .= "4) Kollégium és menza / Internát a školská jedáleň\n";
        $body .= "-------------------------------------------------\n";
        $body .= "Kollégium / Internát: {$internatText}\n";
        $body .= "Menza / Školská jedáleň: {$canteenText}\n\n";

        $body .= "5) Partner a praxhoz / Partner pre dual prax\n";
        $body .= "-------------------------------------------\n";
        $body .= "Választás / Voľba: {$partnerChoiceText}\n";
        if ($partnerName || $partnerContact || $partnerEmail || $partnerPhone) {
            $body .= "Cég neve / Názov firmy: {$partnerName}\n";
            $body .= "Kapcsolattartó / Kontaktná osoba: {$partnerContact}\n";
            $body .= "Cég email / Email firmy: {$partnerEmail}\n";
            $body .= "Cég telefon / Telefón firmy: {$partnerPhone}\n";
        } else {
            $body .= "Részletek a cégről nincsenek kitöltve / Detaily o firme nie sú vyplnené\n";
        }
        $body .= "\n";

        $body .= "6) Elérhető szakok – sorrend / Dostupné odbory – poradie\n";
        $body .= "--------------------------------------------------------\n";
        $body .= $fieldsText . "\n";

        $body .= "7) Adatkezelési hozzájárulás / Súhlas so spracovaním údajov\n";
        $body .= "---------------------------------------------------------\n";
        $body .= $consentText . "\n";

        $body .= "A megjelenített adatok bekerültek az adatbázisba!\n";

        Yii::$app->mailer->compose()
            ->setFrom('info@aoreal.sk')
            ->setTo('info@aoreal.sk')
            ->setCc('szabo.balazs@aoreal.sk')
            ->setSubject('Open Days – új regisztráció / nová registrácia')
            ->setTextBody($body)
            ->send();
    }
}
