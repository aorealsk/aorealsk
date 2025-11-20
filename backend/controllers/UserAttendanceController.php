<?php

namespace backend\controllers;

use common\models\users\UserAttendanceType;
use common\models\users\UserFile;
use common\models\users\UserAttendance;
use DateTime;
use Yii;
use yii\db\Exception;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class UserAttendanceController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Require login for all actions in this controller (user-facing pages).
     */
    public function beforeAction($action)
    {
        if (is_null(Yii::$app->user->identity)) {
            $this->redirect(Url::to(['/site/login']));
            return false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Edit one record.
     */
    public function actionEdit(int $rid, int $uid)
    {
        $record = UserAttendance::findOne(['id' => $rid]);
        if (!$record) {
            Yii::$app->session->setFlash('error', 'Neexistujúci záznam!');
            $this->redirect(Url::to(['/user-attendance','uid' => $uid]));
        }
        return $this->render('edit', [
            'record' => $record,
            'uid' => $uid,
        ]);
    }

    /**
     * User index (self attendance list).
     * @throws Exception
     */
    public function actionIndex(int $uid)
    {
        $attendance = new UserAttendance();
        $today = (new DateTime('now'))->format('Y-m-d');

        return $this->render('index', [
            "attendance" => $attendance->getListByUserId($uid) ?? [] ,
            "userId" => $uid ?? Yii::$app->user->identity->getId(),
            "pageTitle" =>  empty($uid) ? 'Dochádzka' : 'Moja dochádzka',
            'isPresent' => Yii::$app->user->identity->isPresent($today),
            'hasAbsence' => Yii::$app->user->identity->hasAbsence($today),
        ]);
    }

    /**
 * Sanitize a free-text note coming from the client.
 * - trims, normalizes newlines
 * - removes HTML/JS
 * - removes control chars (except \n and \t)
 * - limits length to avoid abuse
 */
    private function sanitizeString(?string $value, int $maxLen = 2000): string
    {
    $s = trim((string)$value);

    // Normalize CRLF/CR -> LF
    $s = preg_replace("/\r\n?/", "\n", $s);

    // Strip any HTML tags
    $s = strip_tags($s);

    // Remove control characters except tab/newline
    $s = preg_replace('/[^\P{C}\t\n]/u', '', $s);

    // Collapse long runs of spaces/tabs
    $s = preg_replace('/[ \t]{2,}/', ' ', $s);

    // Enforce a reasonable length
    if (mb_strlen($s) > $maxLen) {
        $s = mb_substr($s, 0, $maxLen);
    }

    return $s;
    }


    /**
     * Save a text comment to today's row.
     * @throws Exception
     */
    public function actionSaveComment(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = (int)Yii::$app->request->post('userId');
        $noteRaw = (string)Yii::$app->request->post('note', '');
        $note = $this->sanitizeString($noteRaw);
        $date = (new DateTime('now'))->format('Y-m-d');

        $row = UserAttendance::findOne(['userId' => $userId, 'uaDate' => $date]);
        if ($row instanceof UserAttendance) {
            $row->note = trim(($row->note ?? '') . "\n" . $note);
            $row->save(false);
        }

        $rows = (new UserAttendance())->getListByUserId($userId);

        return [
            'ok' => true,
            'status' => 'ok',
            'table_response' => $this->renderPartial('tablebody', ['rows' => $rows]),
        ];
    }

    /* =======================
     * Selfie Start / End + Uploads
     * ======================= */

    /**
     * START of shift (with selfie).
     */
    public function actionStart(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $uid   = $this->requireUserIdFromRequest();
            $image = (string)Yii::$app->request->post('image', '');
            if ($image === '') {
                return ['ok' => false, 'error' => 'Missing selfie image.'];
            }

            $dateObj = new DateTime('now');
            $date    = $dateObj->format('Y-m-d');

            $existing = UserAttendance::find()
                ->andWhere(['uaDate' => $date, 'userId' => $uid])
                ->one();
            if ($existing) {
                return ['ok' => false, 'error' => "Na dátum {$date} už existuje záznam."];
            }

            $url = $this->saveSelfieDataUrl($image, $uid, 'start');

            $rec = new UserAttendance();
            $rec->userId   = $uid;
            $rec->uaDate   = $date;
            $rec->inTime   = $dateObj->format('H:i:s');
            $rec->inIP     = Yii::$app->request->getUserIP();
            $rec->uaType   = UserAttendanceType::REGULAR_WORKTIME;
            $rec->uaAction = 1;

            $note = (string)$this->sanitizeString(Yii::$app->request->post('note', ''));
            $rec->note = trim(($rec->note ?? '') . ($note ? "\n{$note}" : '') . "\n[START_SELFIE] {$url}\n");

            $rec->save(false);

            $rowsHtml = $this->renderPartial('tablebody', [
                'rows' => $rec->getListByUserId($uid)
            ]);

            return ['ok' => true, 'rows' => $rowsHtml, 'start_selfie' => $url];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * END of shift (with selfie).
     */
    public function actionEnd(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $uid   = $this->requireUserIdFromRequest();
            $image = (string)Yii::$app->request->post('image', '');
            if ($image === '') {
                return ['ok' => false, 'error' => 'Missing selfie image.'];
            }

            $dateObj = new DateTime('now');
            $date    = $dateObj->format('Y-m-d');

            $rec = UserAttendance::find()
                ->andWhere(['uaDate' => $date, 'userId' => $uid])
                ->one();

            if (!$rec) {
                $rec = new UserAttendance();
                $rec->userId = $uid;
                $rec->uaType = UserAttendanceType::REGULAR_WORKTIME;
                $rec->uaDate = $date;
            }

            $url = $this->saveSelfieDataUrl($image, $uid, 'end');

            $rec->outTime = $dateObj->format('H:i:s');
            $rec->outIP   = Yii::$app->request->getUserIP();
            $rec->note    = trim(($rec->note ?? '') . "\n[END_SELFIE] {$url}\n");
            $rec->save(false);

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

    /**
     * Upload photos & documents DURING a shift.
     */
    public function actionUpload(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $uid = $this->requireUserIdFromRequest();
            if (empty($_FILES['files']) || !is_array($_FILES['files']['tmp_name'])) {
                return ['ok' => false, 'error' => 'No files[] received'];
            }

            [$baseWeb, /*$selfiesRel*/, $docsRel] = $this->userPathsFromIdentity();

            $out = [];
            foreach ($_FILES['files']['tmp_name'] as $i => $tmp) {
                if (!isset($_FILES['files']['name'][$i])) { $out[] = ['ok'=>false,'error'=>'name_missing']; continue; }
                if (!is_uploaded_file($tmp)) { $out[] = ['ok'=>false,'error'=>'upload_tmp_missing']; continue; }

                $name  = $_FILES['files']['name'][$i] ?? ('file_' . $i);
                $ext   = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $safe  = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $name);
                $stamp = (new \DateTime('now'))->format('Ymd_His') . "_{$i}";
                $rel   = "{$docsRel}/{$stamp}" . ($ext ? ".{$ext}" : '');
                $abs   = $baseWeb . '/' . $rel;

                if (!@move_uploaded_file($tmp, $abs)) { $out[]=['ok'=>false,'error'=>'move_failed']; continue; }
                @chmod($abs, 0644);

                $f = new UserFile();
                $f->user_id = $uid;
                $f->file    = $rel;
                $f->save(false);

                $out[] = ['ok'=>true, 'file'=>Url::to('@web/' . $rel, true)];
            }

            return ['ok' => true, 'files' => $out];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Older shifts loader.
     */
    public function actionListOlder(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $uid   = $this->requireUserIdFromRequest();
            $today = (new \DateTime('now'))->format('Y-m-d');

            $limit  = (int)(Yii::$app->request->post('limit', Yii::$app->request->get('limit', 30)));
            $offset = (int)(Yii::$app->request->post('offset', Yii::$app->request->get('offset', 0)));

            $query = UserAttendance::find()
                ->where(['userId' => $uid])
                ->andWhere(['<', 'uaDate', $today])
                ->orderBy(['uaDate' => SORT_DESC, 'id' => SORT_DESC]);

            $rows = $query->limit($limit)->offset($offset)->all();

            $html = $this->renderPartial('tablebody', [
                'rows' => $rows,
                'uid'  => $uid,
            ]);

            return [
                'ok'        => true,
                'html'      => $html,
                'nextOffset'=> $offset + count($rows),
                'hasMore'   => count($rows) === $limit,
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Today table JSON (with selfies parsed from note).
     */
    public function actionListToday(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $uid  = $this->requireUserIdFromRequest();
            $date = (new DateTime('now'))->format('Y-m-d');

            /** @var UserAttendance|null $rec */
            $rec = UserAttendance::find()
                ->andWhere(['uaDate' => $date, 'userId' => $uid])
                ->one();

            $shifts = [];
            if ($rec) {
                [$startSelfie, $endSelfie] = $this->parseSelfiesFromNote($rec->note ?? '');

                $startedAt = $rec->inTime  ? ($rec->uaDate . ' ' . $rec->inTime)  : null;
                $endedAt   = $rec->outTime ? ($rec->uaDate . ' ' . $rec->outTime) : null;

                $durationSec = null;
                $durationTxt = '';
                if ($startedAt && $endedAt) {
                    $dt1 = new \DateTime($startedAt);
                    $dt2 = new \DateTime($endedAt);
                    $interval = $dt1->diff($dt2);
                    $durationSec = ($interval->days * 86400) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
                    $hours = (int)floor($durationSec / 3600);
                    $mins  = (int)floor(($durationSec % 3600) / 60);
                    $durationTxt = sprintf('%02d:%02d', $hours, $mins);
                }

                $shifts[] = [
                    'id'               => $rec->id,
                    'started_at'       => $startedAt,
                    'ended_at'         => $endedAt,
                    'start_photo'      => $startSelfie,
                    'end_photo'        => $endSelfie,
                    'note'             => $rec->note,
                    'duration_seconds' => $durationSec,
                    'duration_human'   => $durationTxt,
                ];
            }

            $dummy = new UserAttendance();
            return [
                'ok'               => true,
                'shifts'           => $shifts,
                'day_total_time'   => $dummy->getDailyWorkedHoursByUserId($uid, true),
                'month_total_time' => $dummy->getMonthlyWorkedHoursByUserId($uid, true),
                'year_total_time'  => $dummy->getYearlyWorkedHoursByUserId($uid, true),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Arrival (start without selfie; legacy)
     * @throws Exception
     */
    public function actionArrival(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $dateObj = new DateTime('now');
        $date = $dateObj->format('Y-m-d');
        $userId = (int)Yii::$app->request->post('userId');

        $visit = UserAttendance::find()
            ->andWhere(['uaDate' => $date, 'userId' => $userId])
            ->one();

        if ($visit) {
            return [
                'ok' => false,
                'status' => 'error',
                'message' => "Na dátum $date už existuje záznam. Kontaktujte svojho nadriadeného!",
            ];
        }

        $note = $this->sanitizeString((string)Yii::$app->request->post('note', ''));

        $arrivalTime = new UserAttendance();
        $arrivalTime->userId  = $userId;
        $arrivalTime->uaDate  = $date;
        $arrivalTime->inTime  = $dateObj->format('H:i:s');
        $arrivalTime->inIP    = Yii::$app->request->getUserIP();
        $arrivalTime->uaType  = UserAttendanceType::REGULAR_WORKTIME;
        $arrivalTime->note    = $note;
        $arrivalTime->uaAction= 1;
        $arrivalTime->save(false);

        $tableRows = $this->renderPartial('tablebody', [
            "rows" => $arrivalTime->getListByUserId($userId)
        ]);

        return [
            'ok' => true,
            'status' => 'ok',
            'rows' => $tableRows
        ];
    }

    public function actionVacation()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = (int)Yii::$app->request->post('userId');
        $date = new DateTime('now');

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
            $visit->uaType = UserAttendance::VACATION;
            $visit->uaDate = $date->format('Y-m-d');
            $visit->inTime = '08:00:00';
            $visit->outTime = '16:30:00';
            $visit->outIP = Yii::$app->request->getUserIP();

            $visit->save(false);
            $tr->commit();
        } catch (\Throwable $ex) {
            if ($tr->isActive) $tr->rollBack();
            return [
                'ok' => false,
                'status' => 'error',
                'message' => $ex->getMessage(),
            ];
        }

        $tableRows = $this->renderPartial('tablebody', [
            'rows' => $visit->getListByUserId($userId),
            'uid' => $userId,
        ]);

        return [
            'ok' => true,
            'status' => 'ok',
            'rows' => $tableRows,
        ];
    }

    /**
     * @throws Exception
     */
    public function actionDoctorVisit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = (int)Yii::$app->request->post('userId');
        $date = new DateTime('now');

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
            $visit->uaType = UserAttendance::DOCTOR_VISIT;
            $visit->uaDate = $date->format('Y-m-d');
            $visit->inTime = '08:00:00';
            $visit->outTime = '16:30:00';
            $visit->outIP = Yii::$app->request->getUserIP();

            $visit->save(false);
            $tr->commit();
        } catch (\Throwable $ex) {
            if ($tr->isActive) $tr->rollBack();
            return [
                'ok' => false,
                'status' => 'error',
                'message' => $ex->getMessage(),
            ];
        }

        $tableRows = $this->renderPartial('tablebody', [
            'rows' => $visit->getListByUserId($userId),
            'uid' => $userId,
        ]);

        return [
            'ok' => true,
            'status' => 'ok',
            'rows' => $tableRows,
        ];
    }

    /**
     * @throws Exception
     */
    public function actionDeparture(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = (int)Yii::$app->request->post('userId');
        $note   = $this->sanitizeString((string)Yii::$app->request->post('note', ''));
        $now    = new DateTime('now');
        $date   = $now->format('Y-m-d');

        $departure = UserAttendance::find()
            ->andWhere(['uaDate' => $date, 'userId' => $userId])
            ->one();

        if (!$departure) {
            $departure = new UserAttendance();
            $departure->userId = $userId;
            $departure->uaType = UserAttendanceType::REGULAR_WORKTIME;
            $departure->uaDate = $date;
        }

        $departure->outTime = $now->format('H:i:s');
        $departure->note = trim(($departure->note ?? '') . ($note ? "\n{$note}" : ''));
        $departure->outIP = Yii::$app->request->getUserIP();
        $departure->save(false);

        $tableRows = $this->renderPartial('tablebody', [
            "rows" => $departure->getListByUserId($userId)
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

    public function actionUpdateNote()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'ok' => true,
            'status' => 'ok',
            'message' => 'Poznámky bolu uložené'
        ];
    }

    /* =======================
     * Helpers
     * ======================= */

    /**
     * Resolve current user id from POST or GET 'uid' or the logged-in identity.
     */
    private function requireUserIdFromRequest(): int
    {
        $uid = (int)Yii::$app->request->post('uid', Yii::$app->request->get('uid', 0));
        if (!$uid && !Yii::$app->user->isGuest) {
            $uid = (int)Yii::$app->user->id;
        }
        if (!$uid) {
            throw new \yii\web\UnauthorizedHttpException('Not authenticated / missing uid');
        }
        return $uid;
    }

    /**
     * Slug from CURRENT LOGGED-IN user's username (fallback to email local-part, then 'user').
     */
    private function currentUsernameSlug(): string
    {
        $id = Yii::$app->user->identity ?? null;
        if (!$id) {
            throw new \yii\web\UnauthorizedHttpException('Not authenticated');
        }

        $value = null;

        if (isset($id->username) && trim((string)$id->username) !== '') {
            $value = (string)$id->username;
        }

        if ($value === null && isset($id->email) && trim((string)$id->email) !== '') {
            $email = (string)$id->email;
            $value = strpos($email, '@') !== false ? strstr($email, '@', true) : $email;
        }

        if ($value === null) $value = 'user';

        $slug = @iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$value);
        if ($slug === false) $slug = $value;
        $slug = preg_replace('~[^a-zA-Z0-9_.-]+~','_', $slug);
        $slug = trim($slug, '_.-');
        if ($slug === '') $slug = 'user';

        return strtolower($slug);
    }

    /**
     * Build/ensure folders for CURRENT LOGGED-IN user.
     * Returns: [$baseWeb, $selfiesRel, $docsRel]
     */
    private function userPathsFromIdentity(): array
    {
        $slug = $this->currentUsernameSlug();
        $baseWeb    = Yii::getAlias('@webroot');
        $baseRel    = "uploads/users/{$slug}";
        $selfiesRel = "{$baseRel}/selfies";
        $docsRel    = "{$baseRel}/documents";

        @mkdir($baseWeb . '/' . $selfiesRel, 0775, true);
        @mkdir($baseWeb . '/' . $docsRel,    0775, true);

        return [$baseWeb, $selfiesRel, $docsRel];
    }

    /**
     * Save a dataURL selfie and return WEB path.
     */
    private function saveSelfieDataUrl(string $dataUrl, int $uid, string $prefix): string
    {
        if (!preg_match('/^data:image\\/(png|jpeg|jpg|webp);base64,/', $dataUrl, $m)) {
            throw new \RuntimeException('Invalid selfie image data.');
        }
        $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
        $raw = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));

        [$baseWeb, $selfiesRel,] = $this->userPathsFromIdentity();

        $name = $prefix . '_' . date('Ymd_His') . '.' . $ext;
        $abs  = $baseWeb . '/' . $selfiesRel . '/' . $name;

        if (file_put_contents($abs, $raw) === false) {
            throw new \RuntimeException('Failed to write selfie file.');
        }
        @chmod($abs, 0644);

        return '/' . $selfiesRel . '/' . $name;
    }

    /**
     * Extract start/end selfie URLs from the note field.
     */
    private function parseSelfiesFromNote(?string $note): array
    {
        $start = null; $end = null;
        if ($note) {
            if (preg_match('~\\[START_SELFIE\\]\\s*(\\S+)~', $note, $m)) $start = $m[1];
            if (preg_match('~\\[END_SELFIE\\]\\s*(\\S+)~',   $note, $m)) $end   = $m[1];
        }
        return [$start, $end];
    }
}
