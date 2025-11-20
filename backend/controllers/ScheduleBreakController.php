<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use common\models\ScheduleBreak;

class ScheduleBreakController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['post'],
                    'export-csv' => ['get'], // download is a GET
                ],
            ],
        ];
    }

    // POST /schedule-break/create (AJAX)
    public function actionCreate()
    {
        $m = new ScheduleBreak();
        $m->load(Yii::$app->request->post(), '');

        if ($m->validate() && $m->save(false)) {
            return $this->asJson([
                'ok'  => true,
                'row' => [
                    'id'    => (int)$m->id,
                    'title' => $m->title,
                    'from'  => $m->from_time,
                    'to'    => $m->to_time,
                    'break' => $m->break_min,
                ],
            ]);
        }
        return $this->asJson(['ok' => false, 'errors' => $m->getErrors()]);
    }

    // POST /schedule-break/update (AJAX)
    public function actionUpdate()
    {
        $id = (int)Yii::$app->request->post('id');
        if (!$id) return $this->asJson(['ok' => false, 'error' => 'Missing id']);

        $m = $this->findModel($id);
        $m->load(Yii::$app->request->post(), '');

        if ($m->validate() && $m->save(false)) {
            return $this->asJson([
                'ok'  => true,
                'row' => [
                    'id'    => (int)$m->id,
                    'title' => $m->title,
                    'from'  => $m->from_time,
                    'to'    => $m->to_time,
                    'break' => $m->break_min,
                ],
            ]);
        }
        return $this->asJson(['ok' => false, 'errors' => $m->getErrors()]);
    }

    // POST /schedule-break/delete (AJAX)
    public function actionDelete()
    {
        $id = (int)Yii::$app->request->post('id');
        if (!$id) return $this->asJson(['ok' => false, 'error' => 'Missing id']);

        $this->findModel($id)->delete();
        return $this->asJson(['ok' => true]);
    }

    // GET /schedule-break/export-csv?sep=;   (sep can be ',' or ';')
    public function actionExportCsv()
    {
        $delimiter = Yii::$app->request->get('sep') === ',' ? ',' : ';'; // default ';' for EU Excel
        $rows = ScheduleBreak::find()->orderBy(['from_time' => SORT_ASC])->all();

        // Build CSV in memory (UTF-8 BOM + RFC-friendly rows)
        $fp = fopen('php://temp', 'r+');
        // UTF-8 BOM so Excel displays diacritics correctly
        fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($fp, ['Názov', 'Od', 'Do', 'Prestávka (min)'], $delimiter);

        foreach ($rows as $r) {
            fputcsv($fp, [
                $r->title,
                $r->from_time,
                $r->to_time,
                $r->break_min !== null ? (int)$r->break_min : '',
            ], $delimiter);
        }

        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);

        $filename = 'schedule_' . date('Ymd_His') . '.csv';

        // Send as a real file download (Yii helper)
        return Yii::$app->response->sendContentAsFile(
            $csv,
            $filename,
            [
                'mimeType' => 'text/csv; charset=UTF-8',
                // RFC 4180 uses CRLF; Excel handles both, but set if you want strict CRLF:
                // 'inline' => false
            ]
        );
    }

    protected function findModel(int $id): ScheduleBreak
    {
        if (($m = ScheduleBreak::findOne($id)) !== null) return $m;
        throw new NotFoundHttpException('Schedule row not found.');
    }
}
