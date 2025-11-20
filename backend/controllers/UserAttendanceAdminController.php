<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\SqlDataProvider;

use common\models\User;
use common\models\Agent;
use common\models\users\UserAttendance;
use common\models\users\UserFile;
use common\models\auth\AuthItem;
use common\models\auth\AuthAssignment;
use common\repositories\AuthAssignmentRepository;
use common\helpers\TimeHelper;
use DateTime;
use DateTimeImmutable;

class UserAttendanceAdminController extends Controller
{
    public function beforeAction($action)
    {
        if (is_null(Yii::$app->user->identity)) {
            $this->redirect(Url::to(['/site/login']));
            return false;
        }
        return parent::beforeAction($action);
    }

    /* ===================== Start / End / Departure ===================== */

    public function actionEnd(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $uid   = $this->requireUserIdFromRequest();
            $image = (string)Yii::$app->request->post('image', '');
            if ($image === '') {
                return ['ok' => false, 'error' => 'Missing selfie image.'];
            }

            $now  = new DateTime('now');
            $date = $now->format('Y-m-d');

            /** @var UserAttendance|null $rec */
            $rec = UserAttendance::find()
                ->andWhere(['uaDate' => $date, 'userId' => $uid])
                ->one();

            if (!$rec) {
                $rec = new UserAttendance();
                $rec->userId = $uid;
                $rec->uaType = $this->defaultWorkType();
                $rec->uaDate = $date;
            }

            $url = $this->saveSelfieDataUrl($image, $uid, 'end');

            $rec->outTime = $now->format('H:i:s');
            $rec->outIP   = Yii::$app->request->getUserIP();
            $rec->note    = trim((string)($rec->note ?? '') . "\n[END_SELFIE] {$url}\n");
            $rec->save(false);

            $this->recalcDiffTime((int)$rec->id);

            $rowsHtml = $this->renderPartial('tablebody', [
                'rows' => $rec->getListByUserId($uid)
            ]);

            return [
                'ok' => true,
                'rows' => $rowsHtml,
                'end_selfie' => $url,
                'day_total_time'   => $rec->getDailyWorkedHoursByUserId($uid, true),
                'month_total_time' => $rec->getMonthlyWorkedHoursByUserId($uid, true),
                'year_total_time'  => $rec->getYearlyWorkedHoursByUserId($uid, true)
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function actionDeparture(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = (int)Yii::$app->request->post('userId');
        $note   = $this->sanitizeString((string)Yii::$app->request->post('note', ''));
        $now    = new DateTime('now');
        $date   = $now->format('Y-m-d');

        /** @var UserAttendance|null $departure */
        $departure = UserAttendance::find()
            ->andWhere(['uaDate' => $date, 'userId' => $userId])
            ->one();

        if (!$departure) {
            $departure = new UserAttendance();
            $departure->userId = $userId;
            $departure->uaType = $this->defaultWorkType();
            $departure->uaDate = $date;
        }

        $departure->outTime = $now->format('H:i:s');
        $departure->note    = trim((string)($departure->note ?? '') . ($note ? "\n{$note}" : ''));
        $departure->outIP   = Yii::$app->request->getUserIP();
        $departure->save(false);

        $this->recalcDiffTime((int)$departure->id);

        $tableRows = $this->renderPartial('tablebody', [
            'rows' => $departure->getListByUserId($userId)
        ]);

        return [
            'ok' => true,
            'status' => 'ok',
            'rows' => $tableRows,
            'day_total_time'   => $departure->getDailyWorkedHoursByUserId($userId, true),
            'month_total_time' => $departure->getMonthlyWorkedHoursByUserId($userId, true),
            'year_total_time'  => $departure->getYearlyWorkedHoursByUserId($userId, true)
        ];
    }

    public function actionVacation()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = (int)Yii::$app->request->post('userId');
        $date   = new DateTime('now');

        $visit = UserAttendance::find()
            ->andWhere(['uaDate' => $date->format('Y-m-d'), 'userId' => $userId])
            ->one();

        if ($visit) {
            return [
                'ok' => false,
                'status' => 'error',
                'message' => 'Na dátum ' . $date->format('d.m.Y') . ' už existuje záznam. Kontaktujte svojho nadriadeného!',
            ];
        }

        $tr = Yii::$app->db->beginTransaction();

        try {
            $visit = new UserAttendance();
            $visit->userId = $userId;
            $visit->uaType = defined('common\models\users\UserAttendance::VACATION') ? UserAttendance::VACATION : 2;
            $visit->uaDate = $date->format('Y-m-d');
            $visit->inTime = '08:00:00';
            $visit->outTime = '16:30:00';
            $visit->outIP  = Yii::$app->request->getUserIP();
            $visit->save(false);

            $this->recalcDiffTime((int)$visit->id);
            $tr->commit();
        } catch (\Throwable $ex) {
            if ($tr->isActive) $tr->rollBack();
            return ['ok' => false,'status' => 'error','message' => $ex->getMessage()];
        }

        $tableRows = $this->renderPartial('tablebody', [
            'rows' => $visit->getListByUserId($userId),
            'uid'  => $userId,
        ]);

        return ['ok' => true,'status' => 'ok','rows' => $tableRows];
    }

    public function actionDoctorVisit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = (int)Yii::$app->request->post('userId');
        $date   = new DateTime('now');

        $visit = UserAttendance::find()
            ->andWhere(['uaDate' => $date->format('Y-m-d'), 'userId' => $userId])
            ->one();

        if ($visit) {
            return [
                'ok' => false,
                'status' => 'error',
                'message' => 'Na dátum ' . $date->format('d.m.Y') . ' už existuje záznam. Kontaktujte svojho nadriadeného!',
            ];
        }

        $tr = Yii::$app->db->beginTransaction();

        try {
            $visit = new UserAttendance();
            $visit->userId = $userId;
            $visit->uaType = defined('common\models\users\UserAttendance::DOCTOR_VISIT') ? UserAttendance::DOCTOR_VISIT : 3;
            $visit->uaDate = $date->format('Y-m-d');
            $visit->inTime = '08:00:00';
            $visit->outTime = '16:30:00';
            $visit->outIP  = Yii::$app->request->getUserIP();
            $visit->save(false);

            $this->recalcDiffTime((int)$visit->id);
            $tr->commit();
        } catch (\Throwable $ex) {
            if ($tr->isActive) $tr->rollBack();
            return ['ok' => false,'status' => 'error','message' => $ex->getMessage()];
        }

        $tableRows = $this->renderPartial('tablebody', [
            'rows' => $visit->getListByUserId($userId),
            'uid'  => $userId,
        ]);

        return ['ok' => true,'status' => 'ok','rows' => $tableRows];
    }

    /* ===================== Utilities ===================== */

    private function defaultWorkType(): int
    {
        // prefer model constant if present, otherwise safe fallback "1"
        return defined('common\models\users\UserAttendance::REGULAR_WORKTIME')
            ? UserAttendance::REGULAR_WORKTIME
            : 1;
    }

    private function requireUserIdFromRequest(): int
    {
        $uid = (int)Yii::$app->request->post('userId', 0);
        if ($uid > 0) return $uid;
        if (!Yii::$app->user->isGuest) return (int)Yii::$app->user->id;
        throw new \RuntimeException('Missing userId.');
    }

    /**
     * Accepts a data URL (image/png;base64,...) and saves it under /uploads/selfies/{uid}/YYYY-MM
     * Returns a relative URL that is consumable by actionSelfie via /user-attendance-admin/selfie?f=...
     */
    private function saveSelfieDataUrl(string $dataUrl, int $uid, string $kind = 'start'): string
    {
        if (!preg_match('~^data:image/(png|jpeg);base64,~i', $dataUrl, $m)) {
            throw new \InvalidArgumentException('Invalid image data URL');
        }
        $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
        $b64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $bin = base64_decode($b64, true);
        if ($bin === false) {
            throw new \RuntimeException('Invalid base64 payload');
        }

        $subdir = 'uploads/selfies/' . (int)$uid . '/' . date('Y-m');
        $absDir = Yii::getAlias('@webroot') . '/' . $subdir;
        if (!is_dir($absDir) && !@mkdir($absDir, 0775, true) && !is_dir($absDir)) {
            throw new \RuntimeException('Cannot create directory: ' . $absDir);
        }

        $fname = sprintf('%s_%s_%s.%s', $kind, date('Ymd_His'), bin2hex(random_bytes(4)), $ext);
        $abs   = $absDir . '/' . $fname;

        if (file_put_contents($abs, $bin) === false) {
            throw new \RuntimeException('Failed to write file');
        }

        // Return a path consumable by actionSelfie (strip leading @webroot, keep starting slash)
        $rel = '/' . ltrim($subdir . '/' . $fname, '/');
        return $rel;
    }

    /**
     * Persist diffTime in seconds for a row (if both inTime/outTime exist)
     */
    private function recalcDiffTime(int $id): void
    {
        Yii::$app->db->createCommand("
            UPDATE userAttendance
            SET diffTime = CASE
                WHEN inTime IS NOT NULL AND outTime IS NOT NULL
                THEN GREATEST(0, TIME_TO_SEC(TIMEDIFF(outTime, inTime)))
                ELSE diffTime
            END
            WHERE id = :id
        ", [':id' => $id])->execute();
    }

    private function selfieViewerUrl(?string $raw): ?string
    {
        if (!$raw) return null;
        $u = trim((string)$raw);
        if (preg_match('~^https?://~i', $u)) return $u;
        $u = preg_replace('~^@web/?~i', '/', $u);
        $u = preg_replace('~^/web/backend/web~i', '', $u);
        if (!preg_match('~^/uploads/~i', $u)) {
            $u = '/uploads' . ((substr($u, 0, 1) === '/') ? '' : '/') . ltrim($u, '/');
        }
        return Url::to(['/user-attendance-admin/selfie', 'f' => ltrim($u, '/')], true);
    }

    /* ===================== Actions & Index ===================== */

    public function actions()
    {
        return [
            'error'     => ['class' => 'yii\web\ErrorAction'],
            'documents' => ['class' => 'backend\actions\userattendanceadmin\DocumentsAction'],
        ];
    }

    public function actionIndex(): string
    {
    $request = Yii::$app->request;

    // 🔹 Filters from GET
    $filterName  = trim((string)$request->get('name', ''));
    $filterStart = trim((string)$request->get('start', ''));
    $filterEnd   = trim((string)$request->get('end', ''));
    $filterTop   = (bool)$request->get('top5', false);

    // 🔹 Embedded roster (attendance grid)
    $rosterHtml = Yii::$app->runAction('user-attendance-admin/roster', ['embed' => 1]);

    // 🔹 Base user query
    $usersQuery = User::find()->where(['status' => User::STATUS_ACTIVE]);
    if ($filterName !== '') {
        $usersQuery->andWhere([
            'or',
            ['like', 'username', $filterName],
            ['like', 'name_first', $filterName],
            ['like', 'name_last', $filterName],
        ]);
    }
    $users = $usersQuery->all();

    $dashboardData = [];

    // 🗓 Define current school year range (Sept → July)
    $now = new \DateTime();
    $year = (int)$now->format('Y');
    $schoolStart = new \DateTime(($now->format('m') >= 9 ? $year : $year - 1) . '-09-01');
    $schoolEnd   = (clone $schoolStart)->modify('+11 months')->modify('+30 days'); // → July 31

    // 🔹 Compute reward data
    foreach ($users as $user) {
        // --- Attendance filtering ---
        $attendanceQuery = UserAttendance::find()
            ->where(['userId' => $user->id])
            ->andWhere(['not', ['inTime' => null]])
            ->andWhere(['not', ['outTime' => null]]);

        if ($filterStart && $filterEnd) {
            $attendanceQuery->andWhere(['between', 'uaDate', $filterStart, $filterEnd]);
        } else {
            // Default: last 24h
            $attendanceQuery->andWhere(['>=', 'uaDate', date('Y-m-d', strtotime('-1 day'))]);
        }

        $attendances = $attendanceQuery->all();

        // 🎟 Tickets (≥6h attendance)
        $tickets = 0;
        foreach ($attendances as $attendance) {
            $in  = strtotime($attendance->inTime);
            $out = strtotime($attendance->outTime);
            if ($in && $out && (($out - $in) / 3600) >= 6) {
                $tickets++;
            }
        }

        // 🥇 Gold from student_test_log
        $gold = 0;
        $testDate = Yii::$app->db->createCommand("
            SELECT completed_at 
            FROM student_test_log 
            WHERE userId = :uid 
            ORDER BY completed_at DESC 
            LIMIT 1
        ")->bindValue(':uid', $user->id)->queryScalar();

        if ($testDate) {
            $completed = new \DateTime($testDate);
            if ($completed >= $schoolStart && $completed <= $schoolEnd) {
                $gold = 1;
            }
        }

        $dashboardData[] = [
            'username' => $user->username,
            'name'     => trim($user->name_first . ' ' . $user->name_last),
            'tickets'  => $tickets,
            'gold'     => $gold,
            'total'    => $tickets + $gold,
        ];
    }

    // 🔹 Sort Top 5
    if ($filterTop) {
        usort($dashboardData, fn($a, $b) => $b['total'] <=> $a['total']);
        $dashboardData = array_slice($dashboardData, 0, 5);
    }

    // 🔹 Render
    return $this->render('index', [
        'title'         => Yii::t('app', 'Dochádzka - administrácia'),
        'list'          => (new UserAttendance())->getListForAdmin(),
        'groups'        => AuthItem::find()->asArray()->all(),
        'modal_users'   => $this->getUserList(),
        'rosterHtml'    => $rosterHtml,
        'dashboardData' => $dashboardData,
        'filterName'    => $filterName,
        'filterStart'   => $filterStart,
        'filterEnd'     => $filterEnd,
        'filterTop'     => $filterTop,
    ]);
    }



    private function checkStudentTest(int $userId): bool
    {
    // Gold valid between 1 September this academic year and 31 July next year
    $year = (date('n') >= 9) ? date('Y') : date('Y') - 1;
    $start = "$year-09-01";
    $end   = (date('Y', strtotime($start)) + 1) . "-07-31";

    $exists = (new \yii\db\Query())
        ->from('student_test_log')
        ->where(['userId' => $userId])
        ->andWhere(['between', 'completed_at', $start, $end])
        ->exists();

    // Also support old .done file fallback
    $legacyPath = Yii::getAlias('@webroot/studenttests/' . $userId . '.done');
    if (file_exists($legacyPath)) {
        return true;
    }

    return $exists;
    }



    private function getUserList(): array
    {
        $sql = "
            SELECT 
                u.id,
                CONCAT(
                    COALESCE(NULLIF(CONCAT(u.name_first,' ',u.name_last),' '), u.username, CONCAT('*** USER #',u.id,' ***')),
                    ' (', aa.item_name, ')'
                ) AS meno
            FROM auth_assignment aa
            JOIN user u ON u.id = aa.user_id
            WHERE u.status = " . User::STATUS_ACTIVE;

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /* ===================== Monitor (AJAX) ===================== */

    public function actionMonitor()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $group  = trim((string)Yii::$app->request->post('group', ''));
        $userId = (int)Yii::$app->request->post('userid', 0);
        $sdate  = (string)Yii::$app->request->post('sdate', '');
        $edate  = (string)Yii::$app->request->post('edate', '');

        $hasRange = ($sdate !== '' && $edate !== '');

        $startDt = new \yii\db\Expression("STR_TO_DATE(CONCAT(ua.uaDate,' ', COALESCE(ua.inTime,'00:00:00')), '%Y-%m-%d %H:%i:%s')");
        $endDt   = new \yii\db\Expression("STR_TO_DATE(CONCAT(ua.uaDate,' ', COALESCE(ua.outTime, TIME(NOW()))), '%Y-%m-%d %H:%i:%s')");

        $where = ['and', 'ua.inTime IS NOT NULL'];
        if ($hasRange) {
            $where[] = ['between', 'ua.uaDate', $sdate, $edate];
        } else {
            $where[] = ['<=', $startDt, new \yii\db\Expression('NOW()')];
            $where[] = ['>=', $endDt,   new \yii\db\Expression('DATE_SUB(NOW(), INTERVAL 24 HOUR)')];
        }
        if ($userId > 0) $where[] = ['ua.userId' => $userId];

        $query = (new \yii\db\Query())
            ->select([
                'ua.id',
                'ua.userId',
                'ua.uaDate',
                'ua.inTime',
                'ua.outTime',
                'ua.note',
                "TRIM(COALESCE(NULLIF(CONCAT(u.name_first,' ',u.name_last),' '), u.username, CONCAT('ID ',u.id))) AS user_name",
                'aa.item_name AS role_name',
            ])
            ->from('userAttendance ua')
            ->innerJoin('user u', 'u.id = ua.userId')
            ->leftJoin('auth_assignment aa', 'aa.user_id = ua.userId')
            ->where($where);

        if ($group !== '') {
            $query->andWhere(['aa.item_name' => $group]);
        }

        $query->orderBy([
            new \yii\db\Expression("STR_TO_DATE(CONCAT(ua.uaDate,' ', COALESCE(ua.inTime,'00:00:00')), '%Y-%m-%d %H:%i:%s') DESC"),
            'ua.id' => SORT_DESC,
        ]);

        $rows = $query->all();

        foreach ($rows as &$r) {
            [$start, $end] = $this->parseSelfiesFromNote((string)($r['note'] ?? ''));
            $r['start_selfie'] = $this->selfieViewerUrl($start);
            $r['end_selfie']   = $this->selfieViewerUrl($end);
            $r['status_html']  = $r['outTime']
                ? '<span class="badge badge-soft-success">'.Yii::t('app','Ukončené').'</span>'
                : '<span class="badge badge-soft-warning">'.Yii::t('app','V práci').'</span>';
        }
        unset($r);

        $tbody = $this->renderPartial('monitor_tbody', ['rows' => $rows]);

        return ['status' => 'ok', 'tbody' => $tbody];
    }

    private function parseSelfiesFromNote(?string $note): array
    {
        $start = null; $end = null;
        if ($note) {
            if (preg_match('~\\[START_SELFIE\\]\\s*(\\S+)~', $note, $m)) $start = $m[1];
            if (preg_match('~\\[END_SELFIE\\]\\s*(\\S+)~',   $note, $m)) $end   = $m[1];
        }
        return [$start, $end];
    }

    /* ===================== Roster (Grid) ===================== */

    public function actionRoster(): string
    {
        $embed = (bool)Yii::$app->request->get('embed', false);
        if ($embed) $this->layout = false;

        $req = Yii::$app->request;
        $q   = trim((string)$req->get('q', ''));
        $uid = (int)$req->get('uid', 0);

        $where  = '1=1';
        $params = [];

        if ($q !== '') {
            $where .= ' AND ('
                . 'CONCAT(COALESCE(u.name_first, \'\'), " ", COALESCE(u.name_last, \'\')) LIKE :q '
                . 'OR u.username LIKE :q '
                . 'OR u.email LIKE :q'
                . ')';
            $params[':q'] = "%{$q}%";
        }
        if ($uid > 0) {
            $where .= ' AND ua.userId = :uid';
            $params[':uid'] = $uid;
        }

        // IMPORTANT: alias columns to match _rosterGrid.php
        $sql = <<<SQL
SELECT
    ua.id                                   AS id,
    u.username                              AS username,
    TRIM(COALESCE(NULLIF(CONCAT(u.name_first,' ',u.name_last),' '), u.username, CONCAT('ID ',u.id))) AS full_name,
    ua.uaDate                               AS date,
    ua.inTime                               AS start_time,
    ua.outTime                              AS end_time,
    SEC_TO_TIME(
        CASE
            WHEN ua.inTime IS NOT NULL AND ua.outTime IS NOT NULL
            THEN GREATEST(
                0,
                TIME_TO_SEC(
                    TIMEDIFF(CONCAT(ua.uaDate,' ',ua.outTime), CONCAT(ua.uaDate,' ',ua.inTime))
                )
            )
            ELSE 0
        END
    )                                        AS worked_hms,
    u.phone                                  AS phone,
    u.email                                  AS email,
    u.birthdate                              AS date_of_birth
FROM userAttendance ua
JOIN user u ON u.id = ua.userId
WHERE {$where}
ORDER BY ua.uaDate DESC, ua.id DESC
SQL;

        $countSql = <<<SQL
SELECT COUNT(*)
FROM userAttendance ua
JOIN user u ON u.id = ua.userId
WHERE {$where}
SQL;

        $totalCount = (int)Yii::$app->db->createCommand($countSql, $params)->queryScalar();

        $dataProvider = new SqlDataProvider([
            'sql'        => $sql,
            'params'     => $params,
            'totalCount' => $totalCount,
            'pagination' => ['pageSize' => 100],
        ]);

        return $this->render('roster', [
            'dataProvider' => $dataProvider,
            'q'            => $q,
            'uid'          => $uid,
            'embed'        => $embed,
        ]);
    }

    public function actionRosterCsv($q = '', $uid = 0)
    {
        $where  = '1=1';
        $params = [];
        $q   = trim((string)$q);
        $uid = (int)$uid;

        if ($q !== '') {
            $where .= ' AND ('
                . 'CONCAT(COALESCE(u.name_first, \'\'), " ", COALESCE(u.name_last, \'\')) LIKE :q '
                . 'OR u.username LIKE :q '
                . 'OR u.email LIKE :q'
                . ')';
            $params[':q'] = "%{$q}%";
        }
        if ($uid > 0) {
            $where .= ' AND ua.userId = :uid';
            $params[':uid'] = $uid;
        }

        $sql = <<<SQL
SELECT
    TRIM(COALESCE(NULLIF(CONCAT(u.name_first,' ',u.name_last),' '), u.username, CONCAT('ID ',u.id))) AS User,
    u.email        AS Email,
    ua.uaDate      AS Date,
    ua.inTime      AS FirstIn,
    ua.outTime     AS LastOut,
    SEC_TO_TIME(
        CASE
            WHEN ua.inTime IS NOT NULL AND ua.outTime IS NOT NULL
            THEN GREATEST(
                0,
                TIME_TO_SEC(
                    TIMEDIFF(CONCAT(ua.uaDate,' ',ua.outTime), CONCAT(ua.uaDate,' ',ua.inTime))
                )
            )
            ELSE 0
        END
    )              AS Worked
FROM userAttendance ua
JOIN user u ON u.id = ua.userId
WHERE {$where}
ORDER BY ua.uaDate DESC, ua.id DESC
SQL;

        $rows = Yii::$app->db->createCommand($sql, $params)->queryAll();

        Yii::$app->response->format = Response::FORMAT_RAW;
        $h = Yii::$app->response->headers;
        $h->set('Content-Type', 'text/csv; charset=UTF-8');
        $h->set('Content-Disposition', 'attachment; filename="roster_all_time.csv' . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['User','Email','Date','First In','Last Out','Worked']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['User'],$r['Email'],$r['Date'],$r['FirstIn'],$r['LastOut'],$r['Worked']]);
        }
        fclose($out);
        return;
    }

    /* ===================== Edit / Files / Lists ===================== */

    public function actionEdit(int $rid)
    {
        $item = UserAttendance::find()->where(['id' => $rid])->asArray()->one();
        if (!$item) {
            throw new NotFoundHttpException('Attendance record not found.');
        }

        $item['diffTime'] = TimeHelper::secToTime($item['diffTime']);

        // prefer user.name_first/name_last; fall back to agent if needed
        $u = User::findOne((int)$item['userId']);
        $first = $u->name_first ?? '';
        $last  = $u->name_last ?? '';
        if ($first === '' && $last === '') {
            $agent = Agent::findOne(['user_id' => $item['userId']]);
            $first = $agent->name_first ?? '';
            $last  = $agent->name_last ?? '';
        }
        $item['meno'] = trim($first . ' ' . $last);

        $item['user_group'] = (new AuthAssignment())->getGroupsByUserId($item['userId']);

        return $this->render('edit', [
            'title' => Yii::t('app', 'Dochádzka - editácia'),
            'item'  => $item,
            'files' => UserFile::find()
                ->andWhere(['user_id' => $item['userId']])
                ->andWhere(['like', 'created_at', $item['uaDate']])
                ->asArray()
                ->all(),
        ]);
    }

    public function actionSelfie(string $f)
    {
        $f = ltrim($f, '/');
        $abs = \Yii::getAlias('@webroot') . '/' . $f;
        if (!is_file($abs)) {
            throw new NotFoundHttpException('File not found.');
        }
        return \Yii::$app->response->sendFile($abs, basename($abs), ['inline' => true]);
    }

    public function actionListUsers()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $group     = Yii::$app->request->post('group');
        $startDate = Yii::$app->request->post('sdate');
        $endDate   = Yii::$app->request->post('edate');
        $list = (new UserAttendance())->getListForAdminByOptions($group, $startDate, $endDate);
        return ['status' => 'ok', 'tbody' => $this->renderPartial('tablebody', ['list' => $list])];
    }

    public function actionListGroupUsers()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $group = Yii::$app->request->post('group');
        return ['status' => 'ok', 'students' => AuthAssignmentRepository::getByRole($group)];
    }

    public function actionUpdateAttendance()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $item = UserAttendance::findOne(['id' => $data['uaid']]);
        if (!$item) {
            return ['status' => 'error','message' => Yii::t('app', 'Dochádzka nebola nájdená pre dátum ' . $data['uadate'])];
        }
        try {
            $item->uaDate      = $data['uadate'];
            $item->note        = $this->sanitizeString($data['uanote']);
            $item->uaType      = (int)$data['uatype'];
            $item->inTime      = $data['intime'];
            $item->outTime     = $data['outtime'];
            $item->inIP        = $data['inip'];
            $item->outIP       = $data['outip'];
            $item->inOrigTime  = $data['inorigtime'];
            $item->outOrigTime = $data['outorigtime'];
            $item->save(false);

            $this->recalcDiffTime((int)$item->id);

            return ['status' => 'ok'];
        } catch (\Throwable $e) {
            return ['status' => 'error','message' => $e->getMessage()];
        }
    }

    public function actionSaveAttendance()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        $existing = UserAttendance::findOne(['uaDate' => $data['uadate'], 'userId' => $data['uid']]);
        if ($existing) {
            return ['status' => 'error','message' => Yii::t('app', "Záznam na dátum {$data['uadate']} už existuje")];
        }
        try {
            $item = new UserAttendance();
            $item->userId = $data['uid'];
            $item->uaDate = $data['uadate'];
            $item->uaType = $data['uatype'];
            $item->inTime = $data['intime'];
            $item->outTime = $data['outtime'];
            $item->note   = $this->sanitizeString($data['uanote']);
            $item->uaAction = 1;
            $item->save(false);

            $this->recalcDiffTime((int)$item->id);

            $list = (new UserAttendance())->getListForAdmin();
            return ['status' => 'ok','tbody' => $this->renderPartial('tablebody', ['list' => $list])];
        } catch (\Throwable $e) {
            return ['status' => 'error','message' => $e->getMessage()];
        }
    }

    private function sanitizeString(?string $str = null): string
    {
        return is_null($str) ? '' : Html::encode(trim($str));
    }

    public function actionRemovePicture()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        $file = UserFile::findOne(['id' => $data['imageid']]);
        if (!$file) {
            return ['status' => 'error','message' => Yii::t('app', 'Súbor nebol nájdený')];
        }

        $file->delete();
        $files = UserFile::find()
            ->andWhere(['user_id' => $file->user_id])
            ->andWhere(['like', 'created_at', (new DateTimeImmutable($file->created_at))->format('Y-m-d')])
            ->asArray()
            ->all();

        if (count($files) === 0) {
            return ['status' => 'ok','divbody' => $this->renderPartial('nofile')];
        }

        $divbody = '';
        foreach ($files as $f) {
            $divbody .= $this->renderPartial('userfiles', ['fileinfo' => $f]);
        }
        return ['status' => 'ok','divbody' => $divbody];
    }

    /* ===================== Optional cards helpers ===================== */

    private function buildRosterData(string $sdate, string $edate, ?string $group): array
    {
        $sql = "
            SELECT u.id, u.username, u.email, a.name_first, a.name_last,
                   a.phone AS phone, a.mobile AS mobile, a.photo AS agent_photo,
                   MAX(aa.item_name) AS role_name
            FROM user u
            LEFT JOIN agent a ON a.user_id = u.id
            LEFT JOIN auth_assignment aa ON aa.user_id = u.id
            WHERE u.status = :active
        ";
        $params = [':active' => User::STATUS_ACTIVE];
        if (!empty($group)) {
            $sql .= " AND aa.item_name = :grp";
            $params[':grp'] = $group;
        }
        $sql .= " GROUP BY u.id, u.username, u.email, a.name_first, a.name_last, a.phone, a.mobile, a.photo";
        $users = Yii::$app->db->createCommand($sql, $params)->queryAll();

        $totals = Yii::$app->db->createCommand("
            SELECT userId, SUM(COALESCE(diffTime, TIME_TO_SEC(TIMEDIFF(outTime,inTime)))) AS secs
            FROM userAttendance
            WHERE uaDate BETWEEN :s AND :e
            GROUP BY userId
        ", [':s'=>$sdate, ':e'=>$edate])->queryAll();

        $secMap = [];
        foreach ($totals as $t) $secMap[(int)$t['userId']] = (int)$t['secs'];

        $rows = [];
        foreach ($users as $u) {
            $id = (int)$u['id'];
            $secs = (int)($secMap[$id] ?? 0);
            $rows[] = [
                'id' => $id,
                'name' => $this->displayNameFrom($u),
                'email' => (string)($u['email'] ?? ''),
                'phone' => (string)($u['mobile'] ?? $u['phone'] ?? ''),
                'group' => (string)($u['role_name'] ?? ''),
                'avatar' => $this->avatarUrlFor($u),
                'total_secs' => $secs,
                'total_hms'  => $this->secsToHms($secs),
            ];
        }
        return ['users'=>$rows, 'sdate'=>$sdate, 'edate'=>$edate];
    }

    public function actionRosterCards(): string
    {
        $start = new \DateTime('first day of this month 00:00:00');
        $end   = new \DateTime('last day of this month 23:59:59');
        $initial = $this->buildRosterData($start->format('Y-m-d'), $end->format('Y-m-d'), null);

        return $this->render('roster', [
            'title'   => Yii::t('app','Zamestnanci – prehľad'),
            'groups'  => AuthItem::find()->asArray()->all(),
            'initial' => $initial,
        ]);
    }

    public function actionRosterCardsData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $sdate = Yii::$app->request->post('sdate');
        $edate = Yii::$app->request->post('edate');

        if (!$sdate || !$edate) {
            $today = date('Y-m-d');
            $sdate = $edate = $today;
        }

        $rows = Yii::$app->db->createCommand("
            SELECT 
                u.id,
                COALESCE(CONCAT(a.name_first,' ',a.name_last), u.username, CONCAT('ID ',u.id)) AS name,
                u.email,
                '' AS phone,
                COALESCE(SUM(ua.diffTime),0) AS total_seconds
            FROM user u
            LEFT JOIN agent a ON a.user_id = u.id
            LEFT JOIN userAttendance ua 
                ON ua.userId = u.id
               AND ua.uaDate BETWEEN :s AND :e
            WHERE u.status = :active
            GROUP BY u.id, name, u.email
            ORDER BY name ASC
        ", [
            ':s' => $sdate,
            ':e' => $edate,
            ':active' => User::STATUS_ACTIVE
        ])->queryAll();

        foreach ($rows as &$r) {
            $secs = (int)$r['total_seconds'];
            $r['total_hms'] = gmdate("H:i:s", $secs);
        }

        return ['status' => 'ok','users'  => $rows];
    }

    private function displayNameFrom(array $u): string
    {
        $first = trim((string)($u['name_first'] ?? ''));
        $last  = trim((string)($u['name_last'] ?? ''));
        if ($first !== '' || $last !== '') return trim($first.' '.$last);
        if (!empty($u['username'])) return (string)$u['username'];
        if (!empty($u['email']))    return strstr((string)$u['email'], '@', true);
        return 'User #'.((int)$u['id']);
    }

    private function avatarUrlFor(array $u): string
    {
        $raw = (string)($u['agent_photo'] ?? '');
        if ($raw !== '') {
            $p = preg_replace('~^@web/?~i', '/', trim($raw));
            $p = preg_replace('~^/web/backend/web~i', '', $p);
            if (!preg_match('~^/uploads/~i', $p)) { $p = '/uploads/' . ltrim($p,'/'); }
            $abs = Yii::getAlias('@webroot') . '/' . ltrim($p,'/');
            if (is_file($abs)) return Url::to('@web/' . ltrim($p,'/'), true);
        }
        $email = strtolower(trim((string)($u['email'] ?? '')));
        $hash  = md5($email);
        return "https://www.gravatar.com/avatar/{$hash}?s=120&d=identicon";
    }

    private function secsToHms(int $t): string
    {
        if ($t < 0) $t = 0;
        $h = floor($t/3600); $m = floor(($t%3600)/60); $s = $t%60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
}
