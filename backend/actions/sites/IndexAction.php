<?php
namespace backend\actions\sites;

use common\models\Calendar;
use common\models\CalendarEvent;
use common\models\Ucel;
use yii\base\Action;
use common\models\CalendarEventType;
use Yii;

class IndexAction extends Action
{

    public function init()
    {
        return parent::init();
    }

    public function run()
    {
        return $this->controller->render('index',[
           'events' => CalendarEvent::find()->where(['=', 'userId', Yii::$app->user->identity->id])->all()
        ]);
    }
}