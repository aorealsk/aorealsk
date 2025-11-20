<?php
namespace backend\controllers;

use common\models\FinancialInstitution;
use common\models\OfficeAccounts;
use common\repositories\AccountsRepository;
use common\repositories\OfficeRepository;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;

class AccountsController extends Controller
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }

        return $this->render('index', [
            'offices' => OfficeRepository::getAllActiveAsArray(),
        ]);
    }

    public function actionAdd()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Acc');
        }

        return $this->render('add', [
            'banks' => FinancialInstitution::find()->where('institution_type="bank" and status=1')->all(),
        ]);
    }

    public function actionEdit(int $id)
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }

        $account = OfficeAccounts::findOne($id);

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Acc');
            $result = AccountsRepository::save($data, $account);
            if ($result) {
                return $this->redirect(Url::to(['/accounts/index']));
            }
        }

        return $this->render('edit', [
            'account' => $account,
            'banks' => FinancialInstitution::find()->where('institution_type="bank" and status=1')->all(),
        ]);
    }
}