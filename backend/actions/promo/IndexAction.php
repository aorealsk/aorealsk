<?php
namespace backend\actions\promo;

use common\models\promo\Promo;
use common\models\fbcharity\Promo as FbCharityPromo;
use yii\helpers\Url;
use Yii;

class IndexAction extends \yii\base\Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        return $this->controller->render('index', [
            'promotions' => FbCharityPromo::find()->all(),
        ]);
    }
}