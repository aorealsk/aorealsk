<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Response;

class AutoshiftController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['contractor'],
                'rules' => [
                    ['actions' => ['contractor'], 'allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['contractor' => ['GET','POST']],
            ],
        ];
    }

    public function actionContractor()
    {
        $UserClass      = $this->resolveClass(['common\models\User','backend\models\User','app\models\User']);
        $PartnerClass   = $this->resolveClass(['common\models\Partner','backend\models\Partner','app\models\Partner']);
        $CompaniesClass = $this->resolveClass(['common\models\MyCompanies','backend\models\MyCompanies','app\models\MyCompanies']); // NEW
        $FormClass      = $this->resolveClass([
            'common\models\forms\ContractBatchForm',
            'backend\models\forms\ContractBatchForm',
            'app\models\forms\ContractBatchForm',
        ]);
        $GeneratorClass = $this->resolveClass([
            'common\services\ContractGenerator',
            'backend\services\ContractGenerator',
            'app\services\ContractGenerator',
        ]);

        if (!$UserClass || !$PartnerClass || !$FormClass) {
            throw new \RuntimeException('Missing classes. Ensure User, Partner and ContractBatchForm exist.');
        }

        $form = new $FormClass();
        if (method_exists($form, 'loadDefaultValues')) $form->loadDefaultValues();

        $users      = $UserClass::find()->orderBy(['name_last'=>SORT_ASC,'name_first'=>SORT_ASC])->all();
        $partners   = $PartnerClass::find()->orderBy(['partner_name'=>SORT_ASC])->all();
        $companies  = $CompaniesClass ? $CompaniesClass::find()->orderBy(['company_name'=>SORT_ASC])->all() : []; // NEW

        $action      = Yii::$app->request->post('action');
        $hasPreview  = false;
        $contracts   = [];
        $rendered    = [];

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate() && ($action === 'preview' || $action === 'generate')) {
                $hasPreview = true;

                $generator = $GeneratorClass
                    ? new $GeneratorClass($form)
                    : $this->makeFallbackGenerator($form, $UserClass, $PartnerClass);

                $contracts = $generator->buildContractsData();

                // Prepare preview HTML for each user (3 pages joined with page-breaks)
                foreach ($contracts as $payload) {
                    $html  = $generator->renderBundleHeaderCss();
                    $html .= Yii::$app->view->render('@backend/views/contract/_page1', $payload);
                    $html .= '<div class="page-break"></div>';
                    $html .= Yii::$app->view->render('@backend/views/contract/_page2', $payload);
                    $html .= '<div class="page-break"></div>';
                    $html .= Yii::$app->view->render('@backend/views/contract/_page3', $payload);
                    $rendered[$payload['user']->id] = $html;
                }

                if ($action === 'generate') {
                    // ---------- ZIP each user => its own 3-page PDF ----------
                    $postedContracts = Yii::$app->request->post('contracts', []);
                    $bundleCss       = $generator->renderBundleHeaderCss();

                    $tmpDir = Yii::getAlias('@runtime/contract_export_' . uniqid('', true));
                    FileHelper::createDirectory($tmpDir);

                    $pdfFiles = [];
                    foreach ($contracts as $payload) {
                        $uid   = $payload['user']->id;
                        $user  = $payload['user'];
                        $safe  = preg_replace('/[^A-Za-z0-9_\-]+/u', '_', trim(($user->name_last ?? '').'_'.($user->name_first ?? '')));
                        $file  = $tmpDir . DIRECTORY_SEPARATOR . ($safe ?: ('user_'.$uid)) . '.pdf';

                        $userHtml = $postedContracts[$uid] ?? $rendered[$uid];

                        $parts = preg_split('/<div\s+class="page-break"><\/div>/i', $userHtml) ?: [];
                        $parts = array_values(array_pad(array_slice($parts,0,3), 3, ''));

                        $mpdf = new \Mpdf\Mpdf([
                            'mode'              => 'utf-8',
                            'format'            => 'A4',
                            'margin_left'       => 10,
                            'margin_right'      => 10,
                            'margin_top'        => 10,
                            'margin_bottom'     => 10,
                            'default_font_size' => 8.5,
                            'default_font'      => 'dejavusans',
                        ]);

                        $mpdf->WriteHTML($bundleCss . $parts[0]);
                        for ($p=1; $p<3; $p++) {
                            $mpdf->AddPage();
                            $mpdf->WriteHTML($parts[$p]);
                        }
                        $mpdf->Output($file, \Mpdf\Output\Destination::FILE);
                        $pdfFiles[] = $file;
                    }

                    $zipPath = $tmpDir . DIRECTORY_SEPARATOR . 'contracts_' . date('Ymd_His') . '.zip';
                    $zipName = basename($zipPath);
                    $zip = new \ZipArchive();
                    if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
                        throw new \RuntimeException('Unable to create ZIP archive.');
                    }
                    foreach ($pdfFiles as $f) $zip->addFile($f, basename($f));
                    $zip->close();


                    // Archive a copy for History
                    $archiveDir = Yii::getAlias('@runtime/contracts_archive');
                    \yii\helpers\FileHelper::createDirectory($archiveDir);
                    $archivePath = $archiveDir . DIRECTORY_SEPARATOR . basename($zipPath);
                    @copy($zipPath, $archivePath);

                    Yii::$app->response->format = Response::FORMAT_RAW;
                    Yii::$app->response->headers->set('Content-Type', 'application/zip');
                    Yii::$app->response->headers->set('Content-Disposition', 'attachment; filename="' . $zipName . '"');
                    Yii::$app->response->headers->set('Content-Length', (string)filesize($zipPath));
                    Yii::$app->response->sendFile($zipPath, $zipName)->on(Response::EVENT_AFTER_SEND, function() use ($tmpDir) {
                        FileHelper::removeDirectory($tmpDir);
                    });
                    return;
                }
            }
        }

        return $this->render('contractor', [
            'model'      => $form,
            'users'      => $users,
            'partners'   => $partners,
            'companies'  => $companies,   // NEW: pass companies to the view
            'hasPreview' => $hasPreview,
            'contracts'  => $contracts,
            'rendered'   => $rendered,
        ]);
    }

    private function resolveClass(array $candidates): ?string
    {
        foreach ($candidates as $fqcn) {
            if (class_exists($fqcn)) return $fqcn;
        }
        return null;
    }

    public function actionContractDownload($t)
    {
    // $t is a base64url-encoded filename only (no path)
    $file = base64_decode(strtr($t, '-_', '+/'));
    if (!$file || preg_match('/[\/\\\\]/', $file)) {
        throw new \yii\web\BadRequestHttpException('Invalid file.');
    }
    $archiveDir = Yii::getAlias('@runtime/contracts_archive');
    $path = $archiveDir . DIRECTORY_SEPARATOR . $file;
    if (!is_file($path)) {
        throw new \yii\web\NotFoundHttpException('File not found.');
    }
    return Yii::$app->response->sendFile($path, $file);
    }


    private function makeFallbackGenerator($form, string $UserClass, string $PartnerClass)
    {
        $AttendanceClass = $this->resolveClass(['common\models\UserAttendance','backend\models\UserAttendance','app\models\UserAttendance']);
        $StudyItemClass  = $this->resolveClass(['common\models\StudyPlanItem','backend\models\StudyPlanItem','app\models\StudyPlanItem']);

        return new class($form,$UserClass,$PartnerClass,$AttendanceClass,$StudyItemClass) {
            private $form,$UserClass,$PartnerClass,$AttendanceClass,$StudyItemClass;
            public function __construct($form,$UserClass,$PartnerClass,$AttendanceClass,$StudyItemClass){
                $this->form=$form; $this->UserClass=$UserClass; $this->PartnerClass=$PartnerClass;
                $this->AttendanceClass=$AttendanceClass; $this->StudyItemClass=$StudyItemClass;
                @date_default_timezone_set('Europe/Bratislava');
            }
            public function buildContractsData(): array
            {
                $User   = $this->UserClass;
                $Partner= $this->PartnerClass;

                $users = $User::find()
                    ->where(['id'=>$this->form->userIds])
                    ->orderBy(['name_last'=>SORT_ASC,'name_first'=>SORT_ASC])
                    ->all();

                $supervisor = $User::findOne($this->form->supervisorId);
                $partner    = $Partner::findOne($this->form->partnerId);

                $attendanceByUser = [];
                if ($this->AttendanceClass) {
                    $Attendance = $this->AttendanceClass;
                    $atts = $Attendance::find()
                        ->where(['uaDate'=>$this->form->shiftDate,'userId'=>array_map(fn($u)=>$u->id,$users)])
                        ->all();
                    foreach ($atts as $a) { $attendanceByUser[$a->userId][] = $a; }
                }

                $itemsByType = [];
                if ($this->StudyItemClass) {
                    $Study = $this->StudyItemClass;
                    $items = $Study::find()
                        ->where(['month'=>$this->form->studyPlanMonth])
                        ->orderBy(['position'=>SORT_ASC])
                        ->all();
                    foreach ($items as $it) { $itemsByType[$it->type_id][] = $it; }
                }

                $contracts = [];
                foreach ($users as $user) {
                    $contracts[] = [
                        'user'        => $user,
                        'supervisor'  => $supervisor,
                        'partner'     => $partner,
                        'company'     => $this->form->companyName,
                        'shiftDate'   => $this->form->shiftDate,
                        'studyMonth'  => $this->form->studyPlanMonth,
                        'attendance'  => $attendanceByUser[$user->id] ?? [],
                        'studyPlan'   => $itemsByType[$user->study_plan_type_id] ?? [],
                        'batchUsers'  => $users,
                    ];
                }
                return $contracts;
            }
            public function renderBundleHeaderCss(): string
            {
                return <<<HTML
<style>
  body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12pt; }
  .page-break { page-break-after: always; }
  .grid { width:100%; border-collapse:collapse; }
  .grid th,.grid td { border:1px solid #000; padding:6px; vertical-align:top; }
</style>
HTML;
            }
        };
    }
}
