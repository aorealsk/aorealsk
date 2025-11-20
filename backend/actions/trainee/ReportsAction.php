<?php
namespace backend\actions\trainee;

use common\models\Agent;
use common\models\auth\AuthItem;
use common\models\Office;
use Yii;
use yii\helpers\Url;
class ReportsAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        $trainees = [];
        $data = Yii::$app->request->get();
        $year = (new \DateTimeImmutable('now'))->format('Y');

        if (Yii::$app->request->isGet && !empty($data)) {
            array_walk($data['usr'], function($value, $key) use(&$trainees){
                $user = Agent::findOne(['user_id'=>$value]);
                $trainees['users'][] = ['id'=>$value, 'name'=>$user->getFullName()];
            });
        }

        return $this->controller->render('reports',[
            'groups' => AuthItem::find()->all(),
            'offices' => Office::find()->all(),
            'trainees' => $trainees,
            'year' => $year,
        ]);
    }
}