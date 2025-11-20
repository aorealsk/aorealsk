<?php

namespace backend\controllers;

use common\models\TemplateVars;
use common\models\TemplateVarsCols;
use common\models\TemplateVarsMap;
use common\models\TemplateVarsRows;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class TemplateVarsController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'index' => [
                'class' => 'backend\actions\templatevars\IndexAction'
            ],
            'manager' => [
                'class' => 'backend\actions\templatevars\ManagerAction'
            ]
        ];
    }

    public function actionAddRow()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post('d');
        if (TemplateVarsRows::exists($data)) {
            return [
                'status' => 'error',
                'message' => Yii::t('app',"Riadok '{$data}' už existuje!")
            ];
        }
        $row = new TemplateVarsRows();
        $row->name = $data;
        $tr = Yii::$app->db->beginTransaction();
        try {
            $row->save();
            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            return [
                'status' => 'error',
                'message' => $e->getTraceAsString()
            ];
        }
        return [
            'status' => 'ok',
            'message' => Yii::t('app','Riadok bol uložený'),
            'tablebody' => $this->getTableBody()
        ];
    }

    public function actionAddColumn()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post('d');
        $data = explode('|',$data);
        unset($data[3]);
        $result=null;
        array_walk($data, function($val, $key) use(&$result){
            [$title, $value] = explode(':',$val);
            $result[$title] = $value;
        });
        if (TemplateVarsCols::exists($result['prefix'],$result['postfix'])) {
            return [
                'status' => 'error',
                'message' => Yii::t('app','Stĺpec už existuje!')
            ];
        }
        $col = new TemplateVarsCols();
        foreach($result as $key=>$value) {
            $col->$key = $value[$key];
        }
        $tr = Yii::$app->db->beginTransaction();
        try{
            $col->save();
            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            return [
                'status' => 'error',
                'message' => $e->getTraceAsString()
            ];
        }

        return [
            'status' => 'ok',
            'message' => Yii::t('app','Stĺpec bol úspešne uložený'),
            'tablebody' => $this->getTableBody()
        ];
    }

    private function getTableBody()
    {
        return $this->renderPartial('template-vars/tablebody',[
            'cols' => TemplateVarsCols::find()->andWhere(['=','status',1])->orderBy('id desc')->asArray()->all(),
            'rows' => TemplateVarsRows::find()->andWhere(['=','status',1])->asArray()->all(),
            'fullmap' => (new TemplateVarsMap())->getFullMap()
        ]);
    }

    public function actionUpdateItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        $item = TemplateVarsMap::find()
            ->andWhere(['=','row_id',$data['y']])
            ->andWhere(['=','col_id',$data['x']])
            ->one();
        if (!$item) {
            $item = new TemplateVarsMap();
        }
        $item->status = $data['s'];
        $tr = Yii::$app->db->beginTransaction();
        try{
            $item->save();
            if ($data['s'] == 1) {
                $this->saveTemplate($item,$data);
            }
            $tr->commit();
        } catch (\Exception $e) {
            $tr->rollBack();
            return ['status'=>'error','message'=>$e->getMessage()];
        }

        return ['status' => 'ok','message'=>Yii::t('app','Premenná bola uložená')];
    }

    /**
     * @param TemplateVarsMap $item
     * @param array $data
     * @return void
     */
    private function saveTemplate(TemplateVarsMap $item, array $data)
    {
        $template = TemplateVars::find()->andWhere(['=','map_id',$item->id])->one();
        if (!$template) {
            $row = TemplateVarsRows::findOne(['id'=>$data['y']+1]);
            $col = TemplateVarsCols::findOne(['id'=>$data['x']+1]);
            $value = [
                $col->prefix,
                $row->name,
                $col->postfix ?? ''
            ];
            $template = new TemplateVars();
            $template->code = implode('.',$value);
            $template->desc = $col->title . ' - ' . $row->name;
            $template->templ_type='var';
            $template->map_id = $item->id;
            $template->save();
        }
    }
}