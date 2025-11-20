<?php
namespace backend\actions\documents;

use Yii;
use yii\base\Action;
use yii\db\Query;
use yii\web\Response;
use common\models\User;
use backend\components\DocTemplateRegistry;
use backend\components\HtmlPdfService;
use backend\components\PdfOverlayService;
use backend\components\XmlContractService;

// +++ HOZZÁADVA +++
use backend\components\DualVycvikPdf;
use backend\components\PdfTextSanitizer;
use backend\components\DualVycvikPdfUnicode;

class AutoGenerateAction extends Action
{
    public function run()
    {
        $req = Yii::$app->request;

        /* --- selectors for the form --- */
        $users = User::find()
            ->select(['id', 'username', 'email'])
            ->orderBy(['username' => SORT_ASC])
            ->asArray()
            ->all();

        $groups = (new Query())
            ->from('doc_group')
            ->select(['id','name','description'])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $templates = DocTemplateRegistry::all();

        // NEW: schools (for dropdown on the page)
        $schools = (new Query())
            ->from('school')
            ->select([
                'id','description','address','town','zip',
                'contactPersonFirstName','contactPersonLastName','email','phone'
            ])
            ->orderBy(['description' => SORT_ASC])
            ->all();

        // NEW: partners (TABLE: partners)
        $partners = (new Query())
            ->from('partners')
            ->select(['id','partner_name','address','town','zip','ICO','DIC','DICDPH','CEO','DELEGATE'])
            ->orderBy(['partner_name' => SORT_ASC])
            ->all();

        // NEW: our companies (TABLE: myCompanies)
        $myCompanies = (new Query())
            ->from('myCompanies')
            ->select(['id','company_name','address','zip','town','ICO','DIC','DICDPH','CEO','DELEGATE','email','phone','iban','bank_name'])
            ->orderBy(['company_name' => SORT_ASC])
            ->all();

        // ---------- NEW: task type options for the view ----------
        $taskTypeOptions = $this->getTaskTypeOptions();
        // ----------------------------------------------------------

        /* --- generate --- */
        if ($req->isPost) {
            $templateId = $req->post('template_id');
            $userId     = (int)$req->post('user_id');

            // make sure group IDs are integers
            $groupIds = array_values(array_filter(
                array_map('intval', (array)$req->post('group_ids', []))
            ));

            if (!isset($templates[$templateId])) {
                Yii::$app->session->setFlash('error', 'Neznámy šablón.');
                return $this->controller->refresh();
            }
            $tpl = $templates[$templateId];

            // build the list of user IDs to generate for (single + groups)
            $userIds = [];
            if ($userId > 0) { $userIds[] = $userId; }

            if ($groupIds) {
                $rows = (new Query())
                    ->select('u.id')
                    ->from(['gm' => 'doc_group_member'])
                    ->innerJoin(['u' => 'user'], 'u.id = gm.user_id')
                    ->where(['gm.group_id' => $groupIds])
                    ->groupBy('u.id')
                    ->column();
                $userIds = array_values(array_unique(array_merge($userIds, $rows)));
            }

            if (!$userIds) {
                Yii::$app->session->setFlash('error', 'Prosím, vyber používateľa alebo skupinu.');
                return $this->controller->refresh();
            }

            $schoolYear = trim((string)$req->post('school_year', ''));
            if ($schoolYear === '') {
                // simple auto school year like 2025/2026
                $y = (int)date('Y');
                $m = (int)date('n');
                $start = ($m >= 9) ? $y : $y - 1;
                $schoolYear = $start . '/' . ($start + 1);
            }

            // ---------------- NEW: optional contract date override ----------------
            $contractDateRaw = trim((string)$req->post('contract_date', ''));
            list($forcedTodayIso, $forcedTodaySk) = $this->parseUserDate($contractDateRaw);
            // ----------------------------------------------------------------------

            // ---------------- NEW: attendance inputs ----------------
            $useAttendance   = (bool)$req->post('use_attendance', false);
            $attendanceMonth = trim((string)$req->post('attendance_month', ''));
            if ($attendanceMonth === '') { $attendanceMonth = date('Y-m'); }
            // robust month boundaries (avoid explode mismatch)
            $attStart = date('Y-m-01', strtotime($attendanceMonth . '-01'));
            $attEnd   = date('Y-m-t',  strtotime($attendanceMonth . '-01'));
            // --------------------------------------------------------

            // ---------------- NEW: tasks type -----------------------
            $tasksType = (string)$req->post('tasks_type', array_key_first($taskTypeOptions));
            if (!isset($taskTypeOptions[$tasksType])) {
                $tasksType = array_key_first($taskTypeOptions);
            }
            // --------------------------------------------------------

            // ---------------- NEW: only names pages (22 + repeated) --
            $onlyNamesPages = (bool)$req->post('only_names_pages', false);
            // --------------------------------------------------------

            // selected school
            $schoolId  = (int)$req->post('school_id');
            $schoolRow = null;
            if ($schoolId > 0) {
                $schoolRow = (new Query())
                    ->from('school')
                    ->select([
                        'id','description','address','town','zip',
                        'contactPersonFirstName','contactPersonLastName','email','phone'
                    ])
                    ->where(['id' => $schoolId])
                    ->one();
            }

            // selected partner (TABLE: partners)
            $partnerId  = (int)$req->post('partner_id');
            $partnerRow = null;
            if ($partnerId > 0) {
                $partnerRow = (new Query())
                    ->from('partners')
                    ->select(['id','partner_name','address','town','zip','ICO','DIC','DICDPH','CEO','DELEGATE'])
                    ->where(['id' => $partnerId])
                    ->one();
            }

            // selected our company (TABLE: myCompanies)
            $myCompanyId  = (int)$req->post('mycompany_id');
            $myCompanyRow = null;
            if ($myCompanyId > 0) {
                $myCompanyRow = (new Query())
                    ->from('myCompanies')
                    ->select(['id','company_name','address','zip','town','ICO','DIC','DICDPH','CEO','DELEGATE','email','phone','iban','bank_name'])
                    ->where(['id' => $myCompanyId])
                    ->one();
            }

            $outDir = Yii::getAlias('@runtime/generated-docs');
            if (!is_dir($outDir)) { @mkdir($outDir, 0775, true); }

            // detect the dual vycvik contract by templateId and/or XML filename
            $isDualVycvik = false;
            $xmlPath = null; // will be ensured below
            if (isset($tpl['xml'])) {
                $xmlPath = Yii::getAlias((string)$tpl['xml']);
                $isDualVycvik = (strtolower(basename($xmlPath)) === 'dual_vycvik_zmluva.xml');
            }
            // Also treat friendly IDs as Dual Výcvik
            if (!$isDualVycvik) {
                $tid = strtolower((string)$templateId);
                if ($tid === 'dual_vycvik_zmluva' || $tid === 'xml_dual_vycvik_zmluva') {
                    $isDualVycvik = true;
                }
            }

            // ---------- FIX: ensure xmlPath for Dual Výcvik even if template entry lacks 'xml' ----------
            if ($isDualVycvik && (empty($xmlPath) || !is_file($xmlPath))) {
                $all = DocTemplateRegistry::all();
                if (isset($all['xml_dual_vycvik_zmluva']['xml'])) {
                    $candidate = Yii::getAlias((string)$all['xml_dual_vycvik_zmluva']['xml']);
                    if (is_file($candidate)) {
                        $xmlPath = $candidate;
                    }
                }
                if (empty($xmlPath) || !is_file($xmlPath)) {
                    $fallback = Yii::getAlias('@backend/templates/contracts/dual_vycvik_zmluva.xml');
                    if (is_file($fallback)) {
                        $xmlPath = $fallback;
                    }
                }
                \Yii::info('[dual_vycvik] XML resolved to: ' . (string)$xmlPath, __METHOD__);
            }
            // ---------------------------------------------------------------------------------------------

            // Build team list ONLY for dual_vycvik_zmluva
            $teamNames = [];
            $unicodeReady = false;
            $teamNamesRaw = [];
            $teamPeople = [];
            $teamPeopleSanitized = [];

            if ($isDualVycvik) {
                $teamUsers = User::find()
                    ->where(['id' => $userIds])
                    ->orderBy(['name_last' => SORT_ASC, 'name_first' => SORT_ASC, 'username' => SORT_ASC])
                    ->all();

                foreach ($teamUsers as $tu) {
                    $ln = trim((string)($tu->name_last ?? ''));
                    $fn = trim((string)($tu->name_first ?? ''));
                    $nm = trim($ln . ' ' . $fn);
                    if ($nm === '') { $nm = (string)$tu->username; }
                    $teamNames[] = $nm;

                    $dobIso = (string)($tu->birthdate ?? '');
                    $dobSk  = $dobIso ? date('d.m.Y', strtotime($dobIso)) : '';
                    $street = (string)($tu->street     ?? '');
                    $no     = (string)($tu->street_no  ?? '');
                    $zip    = (string)($tu->zip        ?? '');
                    $city   = (string)($tu->city       ?? '');
                    $addr   = trim(trim("$street $no") . ', ' . trim("$zip $city"), " ,");

                    $todayIso = $forcedTodayIso ?: date('Y-m-d');
                    $todaySk  = $forcedTodaySk  ?: date('d.m.Y');

                    // base person
                    $person = [
                        'name'           => $nm,
                        'dob'            => $dobSk,
                        'address'        => $addr,
                        'first_name'     => $fn,
                        'last_name'      => $ln,
                        'today'          => $todayIso,
                        'today_sk'       => $todaySk,
                        'current_month'  => self::monthSkFromIso($todayIso),
                    ];

                    // attach attendance rows + aggregates per user
                    if ($useAttendance) {
                        $rows = (new Query())
                            ->from('userAttendance')
                            ->select(['uaDate','inTime','outTime'])
                            ->where(['userId' => $tu->id])
                            ->andWhere(['between', 'uaDate', $attStart, $attEnd])
                            ->orderBy(['uaDate' => SORT_ASC])
                            ->all();

                        $shifts = [];
                        $totalHours = 0.0;
                        foreach ($rows as $r) {
                            $d  = (string)$r['uaDate'];
                            $in = trim((string)$r['inTime']);
                            $ou = trim((string)$r['outTime']);
                            if ($d === '' || $in === '' || $ou === '') { continue; }

                            $hrs = 0.0;
                            $tIn = strtotime("$d $in");
                            $tOu = strtotime("$d $ou");
                            if ($tIn && $tOu && $tOu > $tIn) {
                                $hrs = round(($tOu - $tIn) / 3600, 2);
                            }

                            $totalHours += $hrs;

                            $shifts[] = [
                                'date' => $d,
                                'from' => $in,
                                'to'   => $ou,
                                'hrs'  => $hrs,
                            ];
                        }
                        $person['shifts'] = $shifts;

                        $days = count($shifts);
                        $hoursStr = rtrim(rtrim(number_format($totalHours, 2, '.', ''), '0'), '.');
                        $summary  = $attendanceMonth . ': ' . $days . ' dní, ' . $hoursStr . ' h';

                        $person['shifts_month']       = $attendanceMonth;
                        $person['shifts_total_hours'] = $hoursStr;
                        $person['shifts_summary']     = $summary;
                    }

                    // --- NEW: attach monthly task list based on selected tasks type ---
                    $person['tasks']       = $this->getMonthlyTasks($attendanceMonth, $tasksType);
                    $person['tasks_month'] = $attendanceMonth;
                    $person['tasks_type']  = $tasksType;
                    // -----------------------------------------------------------------

                    $teamPeople[] = $person;
                }

                $unicodeReady = DualVycvikPdfUnicode::isAvailable();
                $teamNamesRaw = $teamNames;

                if (!$unicodeReady) {
                    $teamNames = PdfTextSanitizer::latinizeList($teamNames);
                }

                if (!$unicodeReady) {
                    foreach ($teamPeople as $p) {
                        $teamPeopleSanitized[] = [
                            'name'        => PdfTextSanitizer::latinize($p['name']        ?? ''),
                            'dob'         => PdfTextSanitizer::latinize($p['dob']         ?? ''),
                            'address'     => PdfTextSanitizer::latinize($p['address']     ?? ''),
                            'first_name'  => PdfTextSanitizer::latinize($p['first_name']  ?? ''),
                            'last_name'   => PdfTextSanitizer::latinize($p['last_name']   ?? ''),
                            'shifts'      => $p['shifts'] ?? [],
                            'shifts_month'       => (string)($p['shifts_month']       ?? ''),
                            'shifts_total_hours' => (string)($p['shifts_total_hours'] ?? ''),
                            'shifts_summary'     => PdfTextSanitizer::latinize((string)($p['shifts_summary'] ?? '')),
                            'current_month'      => PdfTextSanitizer::latinize((string)($p['current_month'] ?? '')),
                            'tasks'              => array_map([PdfTextSanitizer::class, 'latinize'], (array)($p['tasks'] ?? [])),
                            'tasks_month'        => (string)($p['tasks_month'] ?? ''),
                            'tasks_type'         => PdfTextSanitizer::latinize((string)($p['tasks_type'] ?? '')),
                        ];
                    }
                } else {
                    $teamPeopleSanitized = $teamPeople;
                }
            }

            $files = [];
            foreach ($userIds as $uid) {
                $user = User::findOne($uid);
                if (!$user) { continue; }

                // Pass team list only for dual vycvik; otherwise empty list (no extra keys)
                $payload = $this->buildPayload(
                    $user,
                    $schoolYear,
                    $isDualVycvik ? $teamNames : [],
                    $forcedTodayIso,
                    $forcedTodaySk
                );

                // --- NEW: also pass the chosen month's tasks + type into the payload (handy in XML) ---
                $payload += [
                    'tasks'       => $this->getMonthlyTasks($attendanceMonth, $tasksType),
                    'tasks_month' => $attendanceMonth,
                    'tasks_type'  => $tasksType,
                ];
                // ---------------------------------------------------------------------------------------

                // merge selected school
                if ($schoolRow) {
                    $sDesc  = (string)($schoolRow['description'] ?? '');
                    $sAddr  = (string)($schoolRow['address']     ?? '');
                    $sTown  = (string)($schoolRow['town']        ?? '');
                    $sZip   = (string)($schoolRow['zip']         ?? '');
                    $sCPfn  = (string)($schoolRow['contactPersonFirstName'] ?? '');
                    $sCPln  = (string)($schoolRow['contactPersonLastName']  ?? '');
                    $sEmail = (string)($schoolRow['email']       ?? '');
                    $sPhone = (string)($schoolRow['phone']       ?? '');

                    $payload += [
                        'school_id'                 => (string)$schoolRow['id'],
                        'school_description'        => $sDesc,
                        'school_address'            => $sAddr,
                        'school_town'               => $sTown,
                        'school_zip'                => $sZip,
                        'school_contact_first_name' => $sCPfn,
                        'school_contact_last_name'  => $sCPln,
                        'school_contact_full_name'  => trim($sCPfn.' '.$sCPln),
                        'school_email'              => $sEmail,
                        'school_phone'              => $sPhone,
                        'school_full_address'       => trim(trim($sAddr).', '.trim($sZip.' '.$sTown), " ,"),
                        'school_city'               => $sTown,
                    ];
                }

                // merge selected partner
                if ($partnerRow) {
                    $pName = (string)($partnerRow['partner_name'] ?? '');
                    $pAddr = (string)($partnerRow['address'] ?? '');
                    $pZip  = (string)($partnerRow['zip'] ?? '');
                    $pTown = (string)($partnerRow['town'] ?? '');

                    $payload += [
                        'partner_id'           => (string)$partnerRow['id'],
                        'partner_name'         => $pName,
                        'partner_ico'          => (string)($partnerRow['ICO'] ?? ''),
                        'partner_dic'          => (string)($partnerRow['DIC'] ?? ''),
                        'partner_dicdph'       => (string)($partnerRow['DICDPH'] ?? ''),
                        'partner_ceo'          => (string)($partnerRow['CEO'] ?? ''),
                        'partner_delegate'     => (string)($partnerRow['DELEGATE'] ?? ''),
                        'partner_address'      => $pAddr,
                        'partner_zip'          => $pZip,
                        'partner_town'         => $pTown,
                        'partner_city'         => $pTown,
                        'partner_full_address' => trim(trim($pAddr).', '.trim($pZip.' '.$pTown), " ,"),
                    ];
                }

                // merge selected our company
                if ($myCompanyRow) {
                    $cName = (string)($myCompanyRow['company_name'] ?? '');
                    $cAddr = (string)($myCompanyRow['address'] ?? '');
                    $cZip  = (string)($myCompanyRow['zip'] ?? '');
                    $cTown = (string)($myCompanyRow['town'] ?? '');

                    $payload += [
                        'myco_id'            => (string)$myCompanyRow['id'],
                        'myco_name'          => $cName,
                        'myco_address'       => $cAddr,
                        'myco_zip'           => $cZip,
                        'myco_town'          => $cTown,
                        'myco_city'          => $cTown,
                        'myco_full_address'  => trim(trim($cAddr).', '.trim($cZip.' '.$cTown), " ,"),
                        'myco_ico'           => (string)($myCompanyRow['ICO'] ?? ''),
                        'myco_dic'           => (string)($myCompanyRow['DIC'] ?? ''),
                        'myco_dicdph'        => (string)($myCompanyRow['DICDPH'] ?? ''),
                        'myco_ceo'           => (string)($myCompanyRow['CEO'] ?? ''),
                        'myco_delegate'      => (string)($myCompanyRow['DELEGATE'] ?? ''),
                        'myco_email'         => (string)($myCompanyRow['email'] ?? ''),
                        'myco_phone'         => (string)($myCompanyRow['phone'] ?? ''),
                        'myco_iban'          => (string)($myCompanyRow['iban'] ?? ''),
                        'myco_bank_name'     => (string)($myCompanyRow['bank_name'] ?? ''),
                    ];
                }

                // latinize payload only if DualVycvik without Unicode
                if ($isDualVycvik && !$unicodeReady) {
                    $payload = $this->sanitizeArrayText($payload);
                }

                $safeName = preg_replace('~[^a-z0-9_-]+~i', '_', $payload['student_full_name'] ?: ('user_' . $user->id));
                $out = $outDir . DIRECTORY_SEPARATOR . $safeName . '__' . $templateId . '__' . date('Ymd_His') . '.pdf';

                try {
                    // 1) GENERATE
                    $this->renderOne($tpl, $payload, $out, (bool)$req->get('debug', false));

                    // 2) SPECIAL POST-PROCESS for dual_vycvik
                    if ($isDualVycvik && is_file($out)) {
                        if ($unicodeReady) {
                            if (method_exists(\backend\components\DualVycvikPdfUnicode::class, 'repeatPagesFromFilledToStringWithDetails')) {
                                $binary = \backend\components\DualVycvikPdfUnicode
                                    ::repeatPagesFromFilledToStringWithDetails($out, $teamPeople, $xmlPath);
                            } else {
                                $binary = \backend\components\DualVycvikPdfUnicode
                                    ::repeatPagesFromFilledToString($out, $teamNamesRaw);
                            }
                        } else {
                            if (method_exists(\backend\components\DualVycvikPdf::class, 'repeatPagesFromFilledToStringWithDetails')) {
                                $binary = DualVycvikPdf
                                    ::repeatPagesFromFilledToStringWithDetails($out, $teamPeopleSanitized, $xmlPath);
                            } else {
                                $binary = DualVycvikPdf::repeatPagesFromFilledToString($out, $teamNames);
                            }
                        }
                        file_put_contents($out, $binary);

                        // --- NEW: keep only page 22 + repeated pages if requested
                        if ($onlyNamesPages) {
                            $peopleCount = count($unicodeReady ? $teamPeople : $teamPeopleSanitized);
                            $this->keepOnlyNamesPages($out, $peopleCount);
                        }
                    }

                    if (is_file($out)) { $files[] = $out; }
                } catch (\Throwable $e) {
                    Yii::error('[AutoGenerateAction] ' . $e->getMessage(), __METHOD__);
                    Yii::$app->session->setFlash('error', 'Generačná chyba: ' . $e->getMessage());
                    return $this->controller->refresh();
                }
            }

            if (!$files) {
                Yii::$app->session->setFlash('error', 'Nevznikol žiadny PDF súbor.');
                return $this->controller->refresh();
            }

            // one file → stream content + cleanup
            if (count($files) === 1) {
                $path = $files[0];
                $binary = @file_get_contents($path);
                if ($binary === false) {
                    Yii::$app->session->setFlash('error', 'A fájl olvasása nem sikerült.');
                    return $this->controller->refresh();
                }

                Yii::$app->response->on(Response::EVENT_AFTER_SEND, function() use ($path) {
                    @unlink($path);
                });

                $downloadName = basename($path);

                $resp = Yii::$app->response;
                $resp->format = Response::FORMAT_RAW;
                $resp->headers->set('Content-Type','application/pdf');
                $resp->headers->set('Content-Disposition','attachment; filename="'.$downloadName.'"');
                $resp->headers->set('Content-Length', (string)strlen($binary));
                $resp->content = $binary;
                return $resp;
            }

            // many files → zip + cleanup after send
            $zipPath = $outDir . DIRECTORY_SEPARATOR . 'contracts_' . date('Ymd_His') . '.zip';

            $zip = new \ZipArchive();
            $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            foreach ($files as $f) { $zip->addFile($f, basename($f)); }
            $zip->close();

            Yii::$app->response->on(Response::EVENT_AFTER_SEND, function() use ($files, $zipPath) {
                foreach ($files as $f) { @unlink($f); }
                @unlink($zipPath);
            });

            return Yii::$app->response->sendFile(
                $zipPath,
                basename($zipPath),
                ['mimeType' => 'application/zip', 'inline' => false]
            );
        }

        // render page
        return $this->controller->render('auto-generate', [
            'users'               => $users,
            'templates'           => $templates,
            'groups'              => $groups,
            'schools'             => $schools,
            'partners'            => $partners,
            'myCompanies'         => $myCompanies, // NEW
            'defaultContractDate' => '',           // optional prefill in the form
            'taskTypeOptions'     => $taskTypeOptions, // NEW
        ]);
    }

    /**
     * Build the data available to templates.
     * Adds team_01..team_17 only when provided (dual_vycvik_zmluva case).
     */
    private function buildPayload(
        User $u,
        string $schoolYear,
        array $teamNames = [],
        ?string $overrideTodayIso = null,
        ?string $overrideTodaySk  = null
    ): array
    {
        $first = (string)($u->name_first ?? '');
        $last  = (string)($u->name_last ?? '');

        if ($first === '' && $u->agent) { $first = (string)($u->agent->name_first ?? ''); }
        if ($last  === '' && $u->agent) { $last  = (string)($u->agent->name_last  ?? ''); }

        $fullName = trim($first . ' ' . $last);
        if ($fullName === '') { $fullName = (string)$u->username; }

        $birthIso = (string)($u->birthdate ?? '');
        $birthSk  = $birthIso ? date('d.m.Y', strtotime($birthIso)) : '';

        $todayIso = $overrideTodayIso ?: date('Y-m-d');
        $todaySk  = $overrideTodaySk  ?: date('d.m.Y');

        $permStreet = (string)($u->street ?? '');
        $permNo     = (string)($u->street_no ?? '');
        $permZip    = (string)($u->zip ?? '');
        $permCity   = (string)($u->city ?? '');
        $fullAddr   = trim(trim("$permStreet $permNo") . ', ' . trim("$permZip $permCity"), " ,");

        $g  = $u->guardians ? array_values($u->guardians) : [];
        $g1 = $g[0] ?? null;
        $g2 = $g[1] ?? null;

        $g1Split = $g1 ? $this->splitName($g1->name ?? '') : ['first' => '', 'last' => ''];
        $g2Split = $g2 ? $this->splitName($g2->name ?? '') : ['first' => '', 'last' => ''];

        $g1Addr = $g1
            ? trim(trim((string)$g1->street . ' ' . (string)$g1->street_no) . ', ' .
                   trim((string)$g1->zip . ' ' . (string)$g1->city), " ,")
            : '';
        $g2Addr = $g2
            ? trim(trim((string)$g2->street . ' ' . (string)$g2->street_no) . ', ' .
                   trim((string)$g2->zip . ' ' . (string)$g2->city), " ,")
            : '';

        $currentMonthSk = self::monthSkFromIso($todayIso);

        $base = [
            'student_first_name' => $first,
            'student_last_name'  => $last,
            'student_full_name'  => $fullName,
            'student_dob'        => $birthIso,
            'student_dob_sk'     => $birthSk,
            'school_year'        => $schoolYear,

            'today'         => $todayIso,
            'today_sk'      => $todaySk,
            'current_month' => $currentMonthSk,

            'perm_street'  => $permStreet,
            'perm_no'      => $permNo,
            'perm_zip'     => $permZip,
            'perm_city'    => $permCity,
            'full_address' => $fullAddr,

            'phone' => (string)($u->phone ?? ''),
            'email' => (string)($u->email ?? ''),
            'iban'  => (string)($u->iban ?? ''),

            // guardian #1
            'g1_name'          => $g1->name      ?? '',
            'g1_first_name'    => $g1Split['first'],
            'g1_last_name'     => $g1Split['last'],
            'g1_relation'      => $g1->relation  ?? '',
            'g1_phone'         => $g1->phone     ?? '',
            'g1_email'         => $g1->email     ?? '',
            'g1_street'        => $g1->street    ?? '',
            'g1_street_no'     => $g1->street_no ?? '',
            'g1_zip'           => $g1->zip       ?? '',
            'g1_city'          => $g1->city      ?? '',
            'g1_full_address'  => $g1Addr,

            // guardian #2
            'g2_name'          => $g2->name      ?? '',
            'g2_first_name'    => $g2Split['first'],
            'g2_last_name'     => $g2Split['last'],
            'g2_relation'      => $g2->relation  ?? '',
            'g2_phone'         => $g2->phone     ?? '',
            'g2_email'         => $g2->email     ?? '',
            'g2_street'        => $g2->street    ?? '',
            'g2_street_no'     => $g2->street_no ?? '',
            'g2_zip'           => $g2->zip       ?? '',
            'g2_city'          => $g2->city      ?? '',
            'g2_full_address'  => $g2Addr,
        ];

        if (!empty($teamNames)) {
            for ($i = 1; $i <= 17; $i++) {
                $base[sprintf('team_%02d', $i)] = $teamNames[$i - 1] ?? '';
            }
            $base['team_count'] = (string)count($teamNames);
        }

        $aliases = [
            'first_name'       => $base['student_first_name'],
            'lastname'         => $base['student_last_name'],
            'last_name'        => $base['student_last_name'],

            'full_name'        => $base['student_full_name'],
            'name'             => $base['student_full_name'],
            'dob'              => $base['student_dob'],
            'date_of_birth'    => $base['student_dob'],
            'birthdate'        => $base['student_dob'],

            'firstname_guardian1' => $base['g1_first_name'],
            'lastname_guardian1'  => $base['g1_last_name'],
            'firstname_guardian2' => $base['g2_first_name'],
            'lastname_guardian2'  => $base['g2_last_name'],

            'student_street'   => $base['perm_street'],
            'student_stno'     => $base['perm_no'],
            'student_cono'     => $base['perm_zip'],
            'studnet_city'     => $base['perm_city'],
        ];

        return $base + $aliases;
    }

    private function renderOne(array $tpl, array $data, string $out, bool $debugGrid): void
    {
        $engine = (string)($tpl['engine'] ?? 'overlay');

        if ($engine === DocTemplateRegistry::ENGINE_HTML) {
            $viewPath = Yii::getAlias((string)$tpl['view']);
            $html     = Yii::$app->view->renderFile($viewPath, ['d' => $data]);
            (new HtmlPdfService())->renderToFile($html, $out);
            return;
        }

        if ($engine === DocTemplateRegistry::ENGINE_XML) {
            if (empty($tpl['xml'])) {
                throw new \RuntimeException('XML path is missing in template registry.');
            }
            $xmlPath = Yii::getAlias((string)$tpl['xml']);
            (new XmlContractService())->renderToFile($xmlPath, $data, $out);
            return;
        }

        if (empty($tpl['pdf']) || empty($tpl['fields'])) {
            throw new \RuntimeException('Overlay template needs both pdf and fields.');
        }
        (new PdfOverlayService())->fill(
            Yii::getAlias((string)$tpl['pdf']),
            $data,
            (array)$tpl['fields'],
            $out,
            $debugGrid
        );
    }

    private function splitName($full): array
    {
        $full = trim((string)$full);
        if ($full === '') return ['first' => '', 'last' => ''];
        $full = preg_replace('/\s+/', ' ', $full);
        $parts = explode(' ', $full);
        if (count($parts) === 1) return ['first' => $parts[0], 'last' => ''];
        $last = array_pop($parts);
        $first = implode(' ', $parts);
        return ['first' => $first, 'last' => $last];
    }

    private function sanitizeArrayText(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = PdfTextSanitizer::latinize($v);
            }
        }
        return $data;
    }

    private static function monthSkFromIso(string $iso): string
    {
        $ts = strtotime($iso ?: 'today');
        $m  = (int)date('n', $ts);
        $sk = [1=>'Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'];
        return $sk[$m] ?? '';
    }

    private function keepOnlyNamesPages(string $pdfPath, int $peopleCount): void
    {
        if ($peopleCount <= 0) return;
        try {
            $src   = $pdfPath;
            $fpdi  = new \setasign\Fpdi\Fpdi();
            $fpdi->SetAutoPageBreak(false);

            $pageCount = $fpdi->setSourceFile($src);

            $addPage = function(int $p) use ($fpdi) {
                $tpl = $fpdi->importPage($p);
                $s   = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($s['orientation'], [$s['width'], $s['height']]);
                $fpdi->useTemplate($tpl);
            };

            if ($pageCount < 22) return;

            $addPage(22);

            $lastRepeat = 22 + ($peopleCount * 2);
            if ($lastRepeat > $pageCount) {
                $lastRepeat = $pageCount;
            }
            for ($p = 23; $p <= $lastRepeat; $p++) {
                $addPage($p);
            }

            $binary = $fpdi->Output('S');
            @file_put_contents($src, $binary);
        } catch (\Throwable $e) {
            \Yii::error('[keepOnlyNamesPages] ' . $e->getMessage(), __METHOD__);
        }
    }

    private function parseUserDate(string $raw): array
    {
        $s = trim($raw);
        if ($s === '') return [null, null];

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $s, $m)) {
            $y=(int)$m[1]; $mo=(int)$m[2]; $d=(int)$m[3];
            if (checkdate($mo, $d, $y)) {
                $iso = sprintf('%04d-%02d-%02d', $y, $mo, $d);
                $sk  = sprintf('%02d.%02d.%04d', $d, $mo, $y);
                return [$iso, $sk];
            }
        }

        if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $s, $m)) {
            $d=(int)$m[1]; $mo=(int)$m[2]; $y=(int)$m[3];
            if (checkdate($mo, $d, $y)) {
                $iso = sprintf('%04d-%02d-%02d', $y, $mo, $d);
                $sk  = sprintf('%02d.%02d.%04d', $d, $mo, $y);
                return [$iso, $sk];
            }
        }

        $ts = strtotime($s);
        if ($ts !== false) {
            return [date('Y-m-d', $ts), date('d.m.Y', $ts)];
        }

        return [null, null];
    }

    /** Return array of tasks to print on page 23 for a given YYYY-MM and selected type. */
/** Return array of tasks to print on page 23 for a given YYYY-MM and selected type. */
private function getMonthlyTasks(?string $ym, string $type = ''): array
{
    if (!$ym || !preg_match('/^\d{4}-\d{2}$/', $ym)) {
        $ym = date('Y-m');
    }
    $m = (int)substr($ym, 5, 2);

    $maps = $this->getTaskMaps();                 // ← all task maps live here
    $typeKey = $type ?: 'default';
    $map  = $maps[$typeKey] ?? $maps['default'];  // fall back to default if unknown

    return $map[$m] ?? [];
}


    /** Values for the Tasks Type selector (key => label). */
    private function getTaskTypeOptions(): array
    {
        return [
            '3661_h_murar_1' => '3661 H Murár 1. ročník',
            '3661_h_murar_2' => '3661 H Murár 2. ročník',
            '3661_h_murar_3' => '3661 H Murár 3. ročník',
            '3675_h_maliar_1' => '3675 H Maliar 1. ročník',
            '3675_h_maliar_2' => '3675 H Maliar 2. ročník',
            '3675_h_maliar_3' => '3675 H Maliar 3. ročník',
            '3678_h_instalater_1' => '3678 H Inštalatér 1. ročník',
        ];
    }

    /** All month→tasks maps. Edit here to customize each tasks_type. */
    private function getTaskMaps(): array
    {
    // Base default used when a type isn’t provided or not found.
    $default = [
        1  => ['Úvod do BOZP', 'Základy pracoviska', 'Inventúra náradia'],
        2  => ['Príprava materiálu', 'Pomocné montáže', 'Upratovanie pracoviska'],
        3  => ['Merania (základy)', 'Zápisy do denníka', 'Asistencia pri expedícii'],
        4  => ['Montážny postup – krok 1', 'Kontrola kvality – vizuálne'],
        5  => ['Montážny postup – krok 2', 'Príprava dokumentácie'],
        6  => ['Základné servisné úkony', 'Záverečné zhrnutie'],
        7  => ['Letný režim – údržba', 'Skladové práce (ľahké)'],
        8  => ['Letný režim – pomocné práce', 'Príprava na nový školský rok'],
        9  => ['Školenie BOZP', 'Oboznámenie s pracoviskom', 'Sledovanie postupov'],
        10 => ['Jednoduché operácie', 'Práca s nástrojmi pod dohľadom'],
        11 => ['Merania – presnosť', 'Záznam do formulárov'],
        12 => ['Sumár činností', 'Odovzdanie pomôcok', 'Inventúra'],
    ];

    // IMPORTANT: keys must match the values from getTaskTypeOptions().
    return [
        'default' => $default,

        // 3661 H Murár 1. ročník
        '3661_h_murar_1' => [
            // copy default, then tweak months as needed:
            1  => ['Úvod do BOZP', 'Základné ustanovenia právnych noriem', 'Bezpečnosť pri miešaní malty'],
            2  => ['Miešanie malty', 'Príprava tehál', 'Upratovanie pracoviska'],
            3  => ['Základné murárske väzby', 'Zápisy do denníka', 'Asistencia pri expedícii'],
            4  => ['Zvislé a vodorovné škáry', 'Kontrola rovinnosti'],
            5  => ['Murovanie priečok', 'Príprava dokumentácie'],
            6  => ['Omietky – základy', 'Záverečné zhrnutie'],
            7  => ['Letný režim – údržba', 'Skladové práce (ľahké)'],
            8  => ['Letný režim – pomocné práce', 'Príprava na nový školský rok'],
            9  => ['Školenie BOZP', 'Oboznámenie s pracoviskom', 'Sledovanie postupov'],
            10 => ['Jednoduché operácie', 'Práca s nástrojmi pod dohľadom'],
            11 => ['Kontrolné merania', 'Záznam do formulárov'],
            12 => ['Sumár činností', 'Odovzdanie pomôcok', 'Inventúra'],
        ],

        // 3661 H Murár 2. ročník
        '3661_h_murar_2' => [
            1  => ['Opakovanie BOZP', 'Murovanie nosných stien', 'Kontrola kolmosti'],
            2  => ['Klenby a preklady', 'Vystužovanie', 'Priebežná údržba'],
            3  => ['Omietky – jadrové', 'Zápisy do denníka', 'Koordinácia s tímom'],
            4  => ['Omietky – štukové', 'Kontrola kvality – vizuálne'],
            5  => ['Vlhnutie a schnutie omietok', 'Príprava dokumentácie'],
            6  => ['Opravy muriva', 'Záverečné zhrnutie'],
            7  => ['Letný režim – údržba', 'Skladové práce (ľahké)'],
            8  => ['Letný režim – pomocné práce', 'Príprava na nový školský rok'],
            9  => ['Školenie BOZP', 'Oboznámenie s pracoviskom', 'Plánovanie prác'],
            10 => ['Zložitejšie operácie pod dohľadom', 'Práca s lešením (BOZP)'],
            11 => ['Detailné merania', 'Záznam do formulárov'],
            12 => ['Sumár činností', 'Odovzdanie pomôcok', 'Inventúra'],
        ],

        // 3661 H Murár 3. ročník
        '3661_h_murar_3' => [
            1  => ['BOZP – špecifiká prác', 'Organizácia pracoviska', 'Zodpovednosť za tím'],
            2  => ['Zatepľovacie systémy – príprava', 'Lepenie izolácie'],
            3  => ['Zatepľovanie – kotvenie, stierky', 'Zápisy do denníka'],
            4  => ['Fasádne úpravy', 'Kontrola kvality – detailné'],
            5  => ['Dlažby a obklady – základy', 'Dilatačné škáry'],
            6  => ['Dlažby a obklady – pokládka', 'Záverečné hodnotenie'],
            7  => ['Letný režim – údržba', 'Skladové práce (ľahké)'],
            8  => ['Letný režim – pomocné práce', 'Príprava na nový školský rok'],
            9  => ['Školenie BOZP', 'Plánovanie zákaziek', 'Rozdelenie zodpovedností'],
            10 => ['Samostatná práca pod dohľadom', 'Kontrola výsledku'],
            11 => ['Merania – presnosť', 'Kompletácia dokumentácie'],
            12 => ['Sumár činností', 'Odovzdanie pomôcok', 'Inventúra'],
        ],

        // 3675 H Maliar 1. ročník
        '3675_h_maliar_1' => [
            1  => ['Úvod do BOZP', 'Základy povrchových úprav', 'Príprava podkladov'],
            2  => ['Tmelovanie – základy', 'Brúsenie', 'Upratovanie pracoviska'],
            3  => ['Základné nátery', 'Zápisy do denníka', 'Asistencia pri expedícii'],
            4  => ['Valček vs. štetec', 'Kontrola kvality – vizuálne'],
            5  => ['Farebné odtiene – miešanie', 'Príprava dokumentácie'],
            6  => ['Lakovanie – základy', 'Záverečné zhrnutie'],
            7  => ['Letný režim – údržba', 'Skladové práce (ľahké)'],
            8  => ['Letný režim – pomocné práce', 'Príprava na nový školský rok'],
            9  => ['Školenie BOZP', 'Oboznámenie s pracoviskom', 'Sledovanie postupov'],
            10 => ['Jednoduché operácie', 'Práca s nástrojmi pod dohľadom'],
            11 => ['Kontrolné merania hrúbky', 'Záznam do formulárov'],
            12 => ['Sumár činností', 'Odovzdanie pomôcok', 'Inventúra'],
        ],

        // 3675 H Maliar 2. ročník
        '3675_h_maliar_2' => [
            // …edit months as potrebné…
        ],

        // 3675 H Maliar 3. ročník
        '3675_h_maliar_3' => [
            // …edit months as potrebné…
        ],

        // 3678 H Inštalatér 1. ročník
        '3678_h_instalater_1' => [
            1  => ['Vianočné prázdniny', 'Stavba jednoduchého lešenia pre murovanie (lešenárske kozy, a pod.)', 'Ručné opracovanie dreva - BOZ pri ručnom opracovaní dreva, PO predpisy', 'Oboznámenie sa s náradím, nástrojmi a pomôckami pre ručné opracovanie dreva', 'Oboznámenie sa s náradím, nástrojmi a pomôckami pre ručné opracovanie dreva', 'Príprava a rozmeranie materiálov', 'Príprava a rozmeranie materiálov', 'Príprava a rozmeranie materiálov', 'Opracovanie drevnej hmoty – rezanie, vŕtanie, dlabanie, hobľovanie, tesárske spoje, spájanie so spájacími prostriedkami', 'Opracovanie drevnej hmoty – rezanie, vŕtanie, dlabanie, hobľovanie, tesárske spoje, spájanie so spájacími prostriedkami'],

            2  => ['Opracovanie drevnej hmoty – rezanie, vŕtanie, dlabanie, hobľovanie, tesárske spoje, spájanie so spájacími prostriedkami', 'Opracovanie drevnej hmoty – rezanie, vŕtanie, dlabanie, hobľovanie, tesárske spoje, spájanie so spájacími prostriedkami', 'Výroba jednoduchých tesárskych výrobkov (maltovnice, podlahy, dielce...)', 'Výroba jednoduchých tesárskych výrobkov (maltovnice, podlahy, dielce...)', 'Výroba jednoduchých tesárskych výrobkov (maltovnice, podlahy, dielce...)', 'Výroba jednoduchých tesárskych výrobkov (maltovnice, podlahy, dielce...)', 'Výroba jednoduchých tesárskych výrobkov (maltovnice, podlahy, dielce...)', 'Ručné opracovanie kovov, plastov a sadrokartónu – BOZ pri ručnom opracovaní kovov, plastov a sadrokartónu', 'Oboznámenie sa s náradím, nástrojmi a pomôckami pre ručné opracovanie kovov, plastov a sadrokartónu', 'Oboznámenie sa s náradím, nástrojmi a pomôckami pre ručné opracovanie kovov, plastov a sadrokartónu'],


            3  => ['Príprava a rozmeranie materiálov', 'Príprava a rozmeranie materiálov', 'Príprava a rozmeranie materiálov', 'Základné pracovné operácie', 'Základné pracovné operácie', 'Základné pracovné operácie', 'Základné pracovné operácie', 'Jarné prázdniny', 'Jarné prázdniny', 'Jarné prázdniny'],

            4  => ['Základy spájania kovov, plastov a sadrokartónu', 'Veľkonočné prázdniny', 'Základy spájania kovov, plastov a sadrokartónu', 'Dekoračné výrobky', 'Dekoračné výrobky', 'Dekoračné výrobky', 'Základné maliarske a natieračské práce – BOZ a PO pri práci ', 
            'Oboznámenie sa so základným pracovným náradím a pomôckami', 'Oboznámenie sa so základným pracovným náradím a pomôckami', 'Príprava podkladov pod maľby a nátery'],


            5  => ['Príprava podkladov pod maľby a nátery', 'Príprava podkladov pod maľby a nátery', 'Príprava podkladov pod maľby a nátery', 'Príprava podkladov pod maľby a nátery',
             'Nácvik zhotovenia malieb a náterov', 'Nácvik zhotovenia malieb a náterov', 'Nácvik zhotovenia malieb a náterov', 'Nácvik zhotovenia malieb a náterov', 'Nácvik zhotovenia malieb a náterov',
             'Ošetrovanie pracovných pomôcok a náradia'],

            6  => ['Ošetrovanie pracovných pomôcok a náradia - súborná práca', 'Súborná práca', 'Súborná práca', 'Súborná práca', 'Súborná práca', 'Súborná práca', 'Súborná práca', 'Súborná práca', 
            'Opakovanie tematických celkov', 'Slávnostné rozdávanie vysvedčení'],

            7  => ['Letný režim – údržba', 'Skladové práce (ľahké)'],
            8  => ['Letný režim – pomocné práce', 'Príprava na nový školský rok'],

            9  => ['Úvod - Základné ustanovenia právnych noriem, predpisov a zásad o bezpečnosti a ochrane zdravia pri práci', 'Úlohy Inšpektorátu bezpečnosti práce SR a štátneho odborného dozoru',
             'Pracovisko odborného výcviku (ďalej len OV), organizácia práce a väzba na odborné predmety', 'Pravidlá správania sa na OV, druhy ohrození, riziká, príčiny úrazov a ich predchádzanie', 
             'Význam normalizácie a medzinárodných dohôd v technike, medzinárodná organizácia normalizácii (ISO) a použitie medzinárodnej sústavy jednotiek SI',
             'Význam normalizácie a medzinárodných dohôd v technike, medzinárodná organizácia normalizácii (ISO) a použitie medzinárodnej sústavy jednotiek SI', 'Výroba betónovej zmesi pre rôzne stavebné konštrukcie (druhy zmesi, zloženie, dávkovanie), doprava', 'Výroba betónovej zmesi pre rôzne stavebné konštrukcie (druhy zmesi, zloženie, dávkovanie), doprava', 'Výroba betónovej zmesi pre rôzne stavebné konštrukcie (druhy zmesi, zloženie, dávkovanie), doprava'],
            10 => ['Betónovanie jednoduchých konštrukcií z prostého betónu bez debnenia', 'Betónovanie jednoduchých konštrukcií z prostého betónu bez debnenia	', 'Betónovanie jednoduchých konštrukcií z prostého betónu bez debnenia	',
            '	Betónovanie jednoduchých konštrukcií z prostého betónu bez debnenia	', 'Príprava jednoduchých dielcov pre jednoduché debnenie', 'Príprava jednoduchých dielcov pre jednoduché debnenie', 
            'Príprava jednoduchých dielcov pre jednoduché debnenie', 'Príprava jednoduchých dielcov pre jednoduché debnenie', 'Betónovanie jednoduchých železobetónových konštrukcií (základové pásy, päty, dosky, trámy, stĺpy ...)', 'Betónovanie jednoduchých železobetónových konštrukcií (základové pásy, päty, dosky, trámy, stĺpy ...)' ],
            11 => ['Betónovanie jednoduchých železobetónových konštrukcií (základové pásy, päty, dosky, trámy, stĺpy ...)', 'Ošetrovanie betónu', '3. Cvičené murovanie -  Oboznámenie sa s bezpečnosťou práce a ochranou zdravia pri murovaní na cvičnom pracovisku. Kultúra pracovného prostredia. Hygiena práce', 'Oboznámenie sa so základným náradím a pracovnými pomôckami', 'Nácvik základných zručností pri murovaní (na sucho) na cvičnom pracovisku. Vyhotovenie väzieb – behúňovej, väzákovej...', 'Nácvik základných zručností pri murovaní (na sucho) na cvičnom pracovisku. Vyhotovenie väzieb – behúňovej, väzákovej...', 'Nácvik základných zručností pri murovaní (na sucho) na cvičnom pracovisku. Vyhotovenie väzieb – behúňovej, väzákovej...', 'Nácvik základných zručností pri murovaní (na sucho) na cvičnom pracovisku. Vyhotovenie väzieb – behúňovej, väzákovej...', 'Nácvik základných zručností pri murovaní (na sucho) na cvičnom pracovisku. Vyhotovenie väzieb – behúňovej, väzákovej...', 'Nácvik základných zručností pri murovaní (na sucho) na cvičnom pracovisku. Vyhotovenie väzieb – behúňovej, väzákovej...'],
            12 => ['Zakladanie muriva, izolácie, priame múry, pravouhlé pripojenie a kríženie', 'Zakladanie muriva, izolácie, priame múry, pravouhlé pripojenie a kríženie', 'Zakladanie muriva, izolácie, priame múry, pravouhlé pripojenie a kríženie', 'Zakladanie muriva, izolácie, priame múry, pravouhlé pripojenie a kríženie', 'Zásady murovania ostenia okien a dverí', 'Zásady murovania ostenia okien a dverí' , 'Zásady murovania ostenia okien a dverí' , 'Stavba jednoduchého lešenia pre murovanie (lešenárske kozy, a pod.)', 'Vianočné prázdniny', 'Vianočné prázdniny' ],
        ],
    ];
}



}
