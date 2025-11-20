<?php

namespace backend\controllers;

use common\models\Jazyk;
use common\models\sys\SysLog;
use common\models\Units;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;

class UnitsController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    private function getUnits(): array
    {
        return Units::find()
            ->select(['units.id', 'units.unit_name', 'jazyk.code3', 'units.unit_desc'])
            ->join('inner join', 'jazyk', 'jazyk.id=units.unit_lang')
            ->where('units.status = ' . Units::ACTIVE)
            ->asArray()
            ->orderBy('units.id DESC')
            ->all();
    }

    private function getLanguages(): array
    {
        return Jazyk::find()
            ->asArray()
            ->all();
    }

    public function actionIndex()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }
        return $this->render('index', [
            'units' => $this->getUnits(),
        ]);
    }

    public function actionAdd()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Units');
            $tr = Yii::$app->db->beginTransaction();
            try {
                $unit = new Units();
                foreach ($data as $key => $row) {
                    $unit->$key = $row;
                }
                $unit->save();
                $tr->commit();
                return $this->redirect(Url::to(['units/index']));
            } catch (Exception $e) {
                SysLog::WriteError(null, __CLASS__, $e->getTraceAsString(), __LINE__);
                Yii::$app->session->setFlash('error', $e->getTraceAsString());
                $tr->rollBack();
            }
        }
        return $this->render('add', [
            'units' => $this->getUnits(),
            'langs' => $this->getLanguages(),
        ]);
    }

    public function actionEdit()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }

        $id = Yii::$app->request->get('id');
        $unit = Units::findOne($id);

        if (!$unit) {
            return $this->redirect(Url::to(['/units/index']));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Units');
            $tr = Yii::$app->db->beginTransaction();
            try {
                foreach ($data as $key => $row) {
                    $unit->$key = $row;
                }
                $unit->save();
                $tr->commit();
                return $this->redirect(Url::to(['units/index']));
            } catch (Exception $e) {
                SysLog::WriteError(null, __CLASS__, $e->getTraceAsString(), __LINE__);
                Yii::$app->session->setFlash('error', $e->getTraceAsString());
                $tr->rollBack();
            }
        }

        return $this->render('edit', [
            'unit' => $unit,
            'langs' => $this->getLanguages(),
        ]);
    }
}
