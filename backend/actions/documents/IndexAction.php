<?php
namespace backend\actions\documents;

use common\models\Template;
use backend\models\User;
use common\models\TemplateCategory;
use yii\base\Action;
use yii\helpers\Url;
use Yii;

class IndexAction extends Action
{
    public function run()
    {
        // Redirect to login if not authenticated
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }

        // Fetch all templates (as before)
        $templates = Template::find()->all();

        // ✅ Fetch all users for the left-side selector in the new tab
        //    Adjust the model name if your user model lives elsewhere (common\models\User, etc.)
        $users = User::find()->all();

        // ✅ Pass everything to the index view
        return $this->controller->render('index', [
            'templates' => $templates,
            'users'     => $users,
        ]);
    }
}
