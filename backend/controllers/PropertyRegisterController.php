<?php

namespace backend\controllers;

use common\models\Kraj;
use common\models\Okres;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class PropertyRegisterController extends Controller
{
    public function actionIndex()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }
        return $this->render('index', [
            'data' => $this->getData(),
            'regions' => Kraj::find()->where('status=1')->orderBy('id')->all(),
            'districts' => Okres::find()->where('status=1')->orderBy('id')->all(),
        ]);
    }

    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post('filter');
        $result = ['status' => 'ok'];
        $filters = [];

        if (!empty($data['meno'])) {
            $filters[] = "(name_first LIKE '%{$data['meno']}%' OR name_last LIKE '%{$data['meno']}%')";
        }
        if (!empty($data['priezvisko'])) {
            $filters[] = "(name_last LIKE '%{$data['priezvisko']}%' OR name_first LIKE '%{$data['priezvisko']}%')";
        }

        $sql = "";

        $list = Yii::$app->db->createCommand($sql)->queryAll();

        $result['tbody'] = $this->renderPartial('_tbody', ['tbody' => $list]);

        return $result;
    }

    public function actionGetDistricts()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['status' => 'ok'];
        $data = Yii::$app->request->post('kraj');

        $regions = Okres::find()
            ->select(['id', 'kod', 'name'])
            ->where('status=1 and region_id=' . $data)
            ->orderBy('id')
            ->all();
        $result['regions'] = $regions;

        return $result;
    }

    private function getData()
    {
        $sql = "select
    pl.municipality_id,
    pl.municipality_name,
    pl.district_id,
    pl.district_name,
    pl.cadastral_area_id,
    pl.cadastral_area_name,
    pl.list_id,
    CONCAT(po.name_first, ' ', po.name_last) AS owner_name,
    po.email,
    po.phone,
    po.ownership,
    concat(po.addr_street, ' ',po.addr_num) AS addr
from 
    property_list pl
join 
    property_owners po on po.property_list_id = pl.id";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }
}
