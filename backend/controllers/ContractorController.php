<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use setasign\Fpdi\Fpdi;
use common\models\User;
use common\models\Partner;
use common\models\PdfTemplate;
use common\models\MyCompanies;
use common\models\UserGuardian;
use common\models\UserAttendance;
use common\models\StudyPlanType;
use common\models\StudyPlanItem;

/**
 * Contractor / document generator controller.
 */
class ContractorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Main page with editor.
     */
    public function actionIndex()
    {
        $users = User::find()
            ->orderBy(['name_last' => SORT_ASC, 'name_first' => SORT_ASC])
            ->all();

        $partners = Partner::find()
            ->orderBy(['partner_name' => SORT_ASC])
            ->all();

        $templates = PdfTemplate::find()
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('contractor', [
            'users'     => $users,
            'partners'  => $partners,
            'templates' => $templates,
        ]);
    }

    /**
     * Upload a new PDF template and store it as PdfTemplate.
     */
    public function actionUploadTemplate()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            /** @var UploadedFile|null $file */
            $file = UploadedFile::getInstanceByName('template_pdf');

            if ($file !== null && strtolower($file->extension) === 'pdf') {
                $saveDir = Yii::getAlias('@webroot') . '/uploads/pdf_templates';
                if (!is_dir($saveDir)) {
                    mkdir($saveDir, 0777, true);
                }

                $fileName = uniqid('tpl_', true) . '.' . $file->extension;
                $filePath = $saveDir . DIRECTORY_SEPARATOR . $fileName;

                if ($file->saveAs($filePath)) {
                    $tpl = new PdfTemplate();
                    $tpl->name       = $file->baseName . '.' . $file->extension;
                    $tpl->file_path  = '/uploads/pdf_templates/' . $fileName;
                    $tpl->created_at = date('Y-m-d H:i:s');

                    if (!$tpl->save()) {
                        Yii::error(
                            ['msg' => 'Failed to save PdfTemplate', 'errors' => $tpl->getErrors()],
                            __METHOD__
                        );
                    }
                }
            }
        }

        return $this->redirect(['contractor/index']);
    }

    /**
     * Stream raw PDF template (used by pdf.js).
     */
    public function actionPreviewTemplate($id)
    {
        /** @var PdfTemplate|null $tpl */
        $tpl = PdfTemplate::findOne((int)$id);
        if ($tpl === null) {
            throw new NotFoundHttpException('Template not found.');
        }

        $path = Yii::getAlias('@webroot') . $tpl->file_path;
        if (!is_file($path)) {
            throw new NotFoundHttpException('Template file is missing.');
        }

        return Yii::$app->response->sendFile($path, $tpl->name, [
            'inline' => true,
        ]);
    }

    /**
     * AJAX: resolve a single placeholder to real data for preview overlay.
     * POST: placeholder, userId, partnerId, date
     */
    public function actionResolvePlaceholder()
    {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$request->isPost) {
            return ['error' => 'POST required.'];
        }

        $placeholder = trim((string)$request->post('placeholder', ''));
        if ($placeholder === '') {
            return ['error' => 'Missing placeholder.'];
        }

        $userId    = $request->post('userId');
        $partnerId = $request->post('partnerId');
        $dateStr   = $request->post('date');

        /** @var User|null $user */
        $user = $userId ? User::findOne((int)$userId) : null;

        /** @var Partner|null $partner */
        $partner = $partnerId ? Partner::findOne((int)$partnerId) : null;

        $date = null;
        if (!empty($dateStr)) {
            try {
                $date = new \DateTime($dateStr);
            } catch (\Exception $e) {
                $date = null;
            }
        }

        // Basic validation
        if (strpos($placeholder, 'user.') === 0 && $user === null) {
            return ['error' => 'User not selected.'];
        }
        if (strpos($placeholder, 'partner.') === 0 && $partner === null) {
            return ['error' => 'Partner not selected.'];
        }
        if (strpos($placeholder, 'guardian.') === 0 && $user === null) {
            return ['error' => 'User not selected (guardian belongs to user).'];
        }
        if (strpos($placeholder, 'attendance.') === 0 && $user === null) {
            return ['error' => 'User not selected (attendance belongs to user).'];
        }
        if (strpos($placeholder, 'studyplan.') === 0 && $user === null) {
            return ['error' => 'User not selected (study plan belongs to user).'];
        }

        $value = $this->resolvePlaceholderValue($placeholder, $user, $partner, $date);

        return ['value' => $value];
    }

    /**
     * Generate PDF(s) for selected users with positioned placeholders.
     *
     * POST:
     *  - user_ids[] (array)
     *  - partner_id
     *  - template_id
     *  - contract_date
     *  - pages (string, e.g. "1-3,5,7-9" / empty = all)
     *  - layout_fields (JSON: [{placeholder, page, x, y, text_by_user: {userId:text}}, ...])
     */
    public function actionGenerate()
    {
    $request = Yii::$app->request;

    if (!$request->isPost) {
        throw new BadRequestHttpException('Invalid request method.');
    }

    $userIds     = (array)$request->post('user_ids', []);
    $partnerId   = $request->post('partner_id');
    $templateId  = $request->post('template_id');
    $dateStr     = (string)$request->post('contract_date', date('Y-m-d'));
    $pagesStr    = (string)$request->post('pages', '');
    $layoutJson  = (string)$request->post('layout_fields', '[]');

    if (empty($userIds) || empty($templateId)) {
        throw new BadRequestHttpException('Please select at least one user and a PDF template.');
    }

    /** @var PdfTemplate|null $tpl */
    $tpl = PdfTemplate::findOne((int)$templateId);
    if ($tpl === null) {
        throw new NotFoundHttpException('Template not found.');
    }

    $templatePath = Yii::getAlias('@webroot') . $tpl->file_path;
    if (!is_file($templatePath)) {
        throw new NotFoundHttpException('Template file is missing.');
    }

    /** @var User[] $users */
    $users = User::find()->where(['id' => $userIds])->all();
    if (empty($users)) {
        throw new BadRequestHttpException('No users were found.');
    }

    /** @var Partner|null $partner */
    $partner = $partnerId ? Partner::findOne((int)$partnerId) : null;

    try {
        $date = new \DateTime($dateStr ?: 'now');
    } catch (\Exception $e) {
        $date = new \DateTime();
    }

    $layoutFields = json_decode($layoutJson, true);
    if (!is_array($layoutFields)) {
        $layoutFields = [];
    }

    // Get total page count of template
    $tmpPdf = new Fpdi();
    $pageCount = $tmpPdf->setSourceFile($templatePath);
    unset($tmpPdf);

    // Parse pages string
    $pagesToUse = $this->parsePageSelection($pagesStr, $pageCount);
    $pagesMap = null;
    if ($pagesToUse !== null) {
        $pagesMap = [];
        foreach ($pagesToUse as $p) {
            $pagesMap[$p] = true;
        }
    }

    $pdf = new Fpdi();
    $pdf->SetAutoPageBreak(false);

    // Register DejaVu CP1250 font (DejaVuSansCondensed.php/.z must be in vendor/setasign/fpdf/font)
    $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.php');

    foreach ($users as $user) {
        $pdf->setSourceFile($templatePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            if ($pagesMap !== null && !isset($pagesMap[$pageNo])) {
                continue;
            }

            $tplIdx = $pdf->importPage($pageNo);
            $size   = $pdf->getTemplateSize($tplIdx);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplIdx);

            // Draw positioned placeholders for this page
            foreach ($layoutFields as $field) {
                $placeholderKey = isset($field['placeholder']) ? (string)$field['placeholder'] : '';
                if ($placeholderKey === '') {
                    continue;
                }

                $fieldPage = isset($field['page']) ? (int)$field['page'] : 1;
                if ($fieldPage !== $pageNo) {
                    continue;
                }

                $xPercent = isset($field['x']) ? (float)$field['x'] : 0.0;
                $yPercent = isset($field['y']) ? (float)$field['y'] : 0.0;

                // Per-user custom text
                $customText = '';
                if (isset($field['text_by_user']) && is_array($field['text_by_user'])) {
                    $uid = (string)$user->id;
                    if (isset($field['text_by_user'][$uid]) && $field['text_by_user'][$uid] !== '') {
                        $customText = (string)$field['text_by_user'][$uid];
                    }
                }
                if ($customText === '' && isset($field['text']) && $field['text'] !== '') {
                    $customText = (string)$field['text'];
                }

                if ($customText !== '') {
                    $value = $customText;
                } else {
                    $value = $this->resolvePlaceholderValue($placeholderKey, $user, $partner, $date);
                }

                if ($value === '') {
                    continue;
                }

                $x = $size['width']  * $xPercent / 100.0;
                $y = $size['height'] * $yPercent / 100.0;

                $textForPdf = $this->encodeForPdf($value);

                // --- special handling for multi-line / table-like fields (attendance, studyplan, etc.) ---
                $pdf->SetXY($x, $y);

                $isAttendance = (strpos($placeholderKey, 'attendance.') === 0);
                $isStudyPlan  = (strpos($placeholderKey, 'studyplan.') === 0);
                $isMultiline  = (strpos($value, "\n") !== false);

                if ($isAttendance || $isStudyPlan || $isMultiline) {
                    // Multi-line text
                    if ($isAttendance) {
                        // Attendance: monospaced so columns stay aligned (numbers only, no accents)
                        $pdf->SetFont('Courier', '', 9);
                    } else {
                        // Study plan and any other multi-line text: DejaVu with Slovak/HU chars
                        $pdf->SetFont('DejaVu', '', 9);
                    }

                    // Width: from current X to right edge minus small margin
                    $cellWidth = $size['width'] - $x - 10;
                    if ($cellWidth < 20) {
                        $cellWidth = $size['width'] - $x;
                    }

                    // MultiCell respects \n and creates one line per item
                    $pdf->MultiCell($cellWidth, 4, $textForPdf);
                } else {
                    // Single-line normal fields (names, addresses, etc.) with accents
                    $pdf->SetFont('DejaVu', '', 10);
                    $pdf->Write(5, $textForPdf);
                }
            }
        }
    }

    $fileName   = 'contract_' . date('Ymd_His') . '.pdf';
    $pdfContent = $pdf->Output('S');

    /** @var Response $response */
    $response = Yii::$app->response;
    $response->format = Response::FORMAT_RAW;
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    $response->content = $pdfContent;

    return $response;
    }

    /**
     * Resolve a placeholder key to a string value.
     */
    protected function resolvePlaceholderValue(
        string $key,
        ?User $user,
        ?Partner $partner,
        ?\DateTimeInterface $date = null
    ): string {
        // USER FIELDS
        if ($user && strpos($key, 'user.') === 0) {
            $field = substr($key, 5);

            switch ($field) {
                case 'id':
                    return (string)$user->id;
                case 'username':
                    return (string)$user->username;
                case 'name_first':
                    return (string)$user->name_first;
                case 'name_last':
                    return (string)$user->name_last;
                case 'full_name':
                    return trim($user->name_first . ' ' . $user->name_last);
                case 'email':
                    return (string)$user->email;
                case 'birthdate':
                    if (!empty($user->birthdate)) {
                        return date('d.m.Y', strtotime($user->birthdate));
                    }
                    return '';
                case 'shirt_size':
                    return (string)$user->shirt_size;
                case 'pants_size':
                    return (string)$user->pants_size;
                case 'shoe_size':
                    return (string)$user->shoe_size;
                case 'classroom':
                    return (string)$user->userclassroom;
                case 'street':
                    return (string)$user->street;
                case 'street_no':
                    return (string)$user->street_no;
                case 'zip':
                    return (string)$user->zip;
                case 'city':
                    return (string)$user->city;
                case 'address':
                    return trim($user->street . ' ' . $user->street_no . ', ' . $user->zip . ' ' . $user->city);
                case 'phone':
                    return (string)$user->phone;
                case 'iban':
                    return (string)$user->iban;
            }
        }

        // GUARDIAN FIELDS
        if ($user && strpos($key, 'guardian.') === 0) {
            /** @var UserGuardian|null $guardian */
            $guardian = UserGuardian::find()
                ->where(['user_id' => $user->id])
                ->orderBy(['id' => SORT_ASC])
                ->one();

            if ($guardian === null) {
                return '';
            }

            $field = substr($key, 9);

            switch ($field) {
                case 'name':
                    return (string)$guardian->name;
                case 'relation':
                    return (string)$guardian->relation;
                case 'phone':
                    return (string)$guardian->phone;
                case 'email':
                    return (string)$guardian->email;
                case 'street':
                    return (string)$guardian->street;
                case 'street_no':
                    return (string)$guardian->street_no;
                case 'zip':
                    return (string)$guardian->zip;
                case 'city':
                    return (string)$guardian->city;
                case 'address':
                    return trim($guardian->street . ' ' . $guardian->street_no . ', ' . $guardian->zip . ' ' . $guardian->city);
            }
        }

        // ATTENDANCE FIELDS
        if ($user && strpos($key, 'attendance.') === 0) {
            $d = $date ?: new \DateTime();

            $start = new \DateTime($d->format('Y-m-01'));
            $end   = (clone $start)->modify('+1 month');

            $attendances = UserAttendance::find()
                ->where(['userId' => $user->id])
                ->andWhere(['>=', 'uaDate', $start->format('Y-m-d')])
                ->andWhere(['<',  'uaDate', $end->format('Y-m-d')])
                ->orderBy(['uaDate' => SORT_ASC, 'inTime' => SORT_ASC])
                ->all();

            if (empty($attendances)) {
                return '';
            }

            $durationsSec = [];
            $durationsStr = [];
            $linesForList = [];

            foreach ($attendances as $a) {
                /** @var UserAttendance $a */
                $dateStr = (string)$a->uaDate;
                $in      = $a->inTime ? substr($a->inTime, 0, 5) : '--:--';
                $out     = $a->outTime ? substr($a->outTime, 0, 5) : '--:--';

                $durationSec = 0;
                $durationStr = '';

                if ($a->inTime && $a->outTime) {
                    $inSec  = strtotime($a->inTime);
                    $outSec = strtotime($a->outTime);
                    if ($outSec > $inSec) {
                        $diff        = $outSec - $inSec;
                        $hours       = floor($diff / 3600);
                        $mins        = floor(($diff % 3600) / 60);
                        $durationSec = $diff;
                        $durationStr = sprintf('%02d:%02d', $hours, $mins);
                    }
                }

                $durationsSec[] = $durationSec;
                $durationsStr[] = $durationStr;

                $linesForList[] = sprintf(
                    '%s    %s   %s    %s',
                    $dateStr,
                    $in,
                    $out,
                    $durationStr
                );
            }

            $sub = substr($key, 11); // strip "attendance."

            switch ($sub) {
                case 'current_month':
                    return implode("\n", $linesForList);

                case 'total_hours_month':
                    $totalSec = 0;
                    foreach ($durationsSec as $sec) {
                        $totalSec += (int)$sec;
                    }
                    if ($totalSec <= 0) {
                        return '00:00';
                    }
                    $hours = floor($totalSec / 3600);
                    $mins  = floor(($totalSec % 3600) / 60);
                    return sprintf('%02d:%02d', $hours, $mins);

                case 'hours_per_day':
                    $filtered = array_filter($durationsStr, function($v) {
                        return $v !== '';
                    });
                    if (empty($filtered)) {
                        return '';
                    }
                    return implode(', ', $filtered);
            }
        }

        // STUDY PLAN FIELDS
        if ($user && strpos($key, 'studyplan.') === 0) {
            $typeId = $user->study_plan_type_id ?? null;
            if (empty($typeId)) {
                return '';
            }

            /** @var StudyPlanType|null $planType */
            $planType = StudyPlanType::findOne((int)$typeId);
            if ($planType === null) {
                return '';
            }

            $field = substr($key, 10);

            switch ($field) {
                case 'name':
                    return (string)$planType->name;

                case 'current_month':
                    $d = $date ?: new \DateTime();
                    $month = (int)$d->format('n');

                    $items = StudyPlanItem::find()
                        ->where([
                            'type_id' => (int)$typeId,
                            'month'   => $month,
                        ])
                        ->orderBy(['position' => SORT_ASC])
                        ->all();

                    if (empty($items)) {
                        return '';
                    }

                    $lines = [];
                    foreach ($items as $item) {
                        /** @var StudyPlanItem $item */
                        $pos = $item->position ?: '';
                        if ($pos !== '') {
                            $lines[] = $pos . '. ' . $item->item;
                        } else {
                            $lines[] = (string)$item->item;
                        }
                    }

                    return implode("\n", $lines);
            }
        }

        // PARTNER FIELDS
        if ($partner && strpos($key, 'partner.') === 0) {
            $field = substr($key, 8);

            switch ($field) {
                case 'partner_name':
                    return (string)$partner->partner_name;
                case 'address':
                    return (string)$partner->address;
                case 'town':
                    return (string)$partner->town;
                case 'zip':
                    return (string)$partner->zip;
                case 'registration_number':
                    return (string)$partner->registration_number;
                case 'ICO':
                    return (string)$partner->ICO;
                case 'DIC':
                    return (string)$partner->DIC;
                case 'DICDPH':
                    return (string)$partner->DICDPH;
                case 'CEO':
                    return (string)$partner->CEO;
                case 'DELEGATE':
                    return (string)$partner->DELEGATE;
            }
        }

        // COMPANY FIELDS
        if (strpos($key, 'company.') === 0) {
            /** @var MyCompanies|null $company */
            $company = MyCompanies::find()
                ->orderBy(['id' => SORT_ASC])
                ->one();

            if ($company === null) {
                return '';
            }

            $field = substr($key, 8);

            switch ($field) {
                case 'company_name':
                    return (string)$company->company_name;
                case 'address':
                    return (string)$company->address;
                case 'zip':
                    return (string)$company->zip;
                case 'town':
                    return (string)$company->town;
                case 'ICO':
                    return (string)$company->ICO;
                case 'DIC':
                    return (string)$company->DIC;
                case 'DICDPH':
                    return (string)$company->DICDPH;
                case 'CEO':
                    return (string)$company->CEO;
                case 'DELEGATE':
                    return (string)$company->DELEGATE;
                case 'email':
                    return (string)$company->email;
                case 'phone':
                    return (string)$company->phone;
                case 'iban':
                    return (string)$company->iban;
                case 'bank_name':
                    return (string)$company->bank_name;
            }
        }

        // DATE FIELDS (incl. Slovak)
        if (
            $key === 'date' ||
            $key === 'date.long' ||
            $key === 'date.slovak' ||
            $key === 'date.slovak_month' ||
            $key === 'date.slovak_month_gen'
        ) {
            $d = $date ?: new \DateTime();

            if ($key === 'date' || $key === 'date.long') {
                return $d->format('d.m.Y');
            }

            $day   = (int)$d->format('j');
            $month = (int)$d->format('n');
            $year  = (int)$d->format('Y');

            $months = [
                1  => ['nom' => 'január',    'gen' => 'januára'],
                2  => ['nom' => 'február',   'gen' => 'februára'],
                3  => ['nom' => 'marec',     'gen' => 'marca'],
                4  => ['nom' => 'apríl',     'gen' => 'apríla'],
                5  => ['nom' => 'máj',       'gen' => 'mája'],
                6  => ['nom' => 'jún',       'gen' => 'júna'],
                7  => ['nom' => 'júl',       'gen' => 'júla'],
                8  => ['nom' => 'august',    'gen' => 'augusta'],
                9  => ['nom' => 'september', 'gen' => 'septembra'],
                10 => ['nom' => 'október',   'gen' => 'októbra'],
                11 => ['nom' => 'november',  'gen' => 'novembra'],
                12 => ['nom' => 'december',  'gen' => 'decembra'],
            ];

            $nom = $months[$month]['nom'];
            $gen = $months[$month]['gen'];

            if ($key === 'date.slovak') {
                return $day . '. ' . $gen . ' ' . $year;
            }

            if ($key === 'date.slovak_month') {
                return $nom;
            }

            if ($key === 'date.slovak_month_gen') {
                return $gen;
            }
        }

        return '';
    }

    /**
     * Convert UTF-8 text to something FPDF/FPDI can handle.
     */
    protected function encodeForPdf(string $text): string
    {
    if ($text === '') {
        return '';
    }

    $converted = @iconv('UTF-8', 'Windows-1250//TRANSLIT', $text);
    if ($converted === false) {
        $converted = utf8_decode($text);
    }

    return $converted;
    }


    /**
     * Parse page selection like "1-3,5,7-9" into array of ints within [1, $maxPage].
     * Empty / invalid => null (all pages).
     */
    protected function parsePageSelection(string $input, int $maxPage): ?array
    {
        $input = trim($input);
        if ($input === '') {
            return null;
        }

        $pages = [];
        $parts = preg_split('/[,\s]+/', $input);

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            if (strpos($part, '-') !== false) {
                list($start, $end) = explode('-', $part, 2);
                $start = (int)trim($start);
                $end   = (int)trim($end);

                if ($start <= 0 || $end <= 0) {
                    continue;
                }
                if ($start > $end) {
                    $tmp = $start;
                    $start = $end;
                    $end   = $tmp;
                }

                for ($i = $start; $i <= $end; $i++) {
                    if ($i >= 1 && $i <= $maxPage) {
                        $pages[$i] = true;
                    }
                }
            } else {
                $p = (int)$part;
                if ($p >= 1 && $p <= $maxPage) {
                    $pages[$p] = true;
                }
            }
        }

        if (empty($pages)) {
            return null;
        }

        $result = array_keys($pages);
        sort($result, SORT_NUMERIC);

        return $result;
    }
}
