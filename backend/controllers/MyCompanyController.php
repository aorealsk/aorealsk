<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\db\Query;

class MyCompanyController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false; // keep it simple (match your other controllers)
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $companies = (new Query())
            ->from('myCompanies')
            ->orderBy(['company_name' => SORT_ASC])
            ->all();

        return $this->render('index', ['companies' => $companies]);
    }

    public function actionCreate()
    {
        $db = Yii::$app->db;

        if (Yii::$app->request->isPost) {
            $p = Yii::$app->request->post();

            $row = [
                'company_name' => trim($p['company_name'] ?? ''),
                'address'      => trim($p['address'] ?? ''),
                'zip'          => trim($p['zip'] ?? ''),
                'town'         => trim($p['town'] ?? ''),
                'ICO'          => trim($p['ICO'] ?? ''),
                'DIC'          => trim($p['DIC'] ?? ''),
                'DICDPH'       => trim($p['DICDPH'] ?? ''),
                'CEO'          => trim($p['CEO'] ?? ''),
                'DELEGATE'     => trim($p['DELEGATE'] ?? ''),
                'email'        => trim($p['email'] ?? ''),
                'phone'        => trim($p['phone'] ?? ''),
                'iban'         => trim($p['iban'] ?? ''),
                'bank_name'    => trim($p['bank_name'] ?? ''),
            ];

            if ($row['company_name'] === '') {
                Yii::$app->session->setFlash('error', 'Add meg a cég nevét.');
            } else {
                $db->createCommand()->insert('myCompanies', $row)->execute();
                Yii::$app->session->setFlash('success', 'Cég létrehozva.');
                return $this->redirect(['documents/auto-generate']);
            }
        }

        return $this->render('create');
    }

    public function actionEdit(int $id)
    {
        $db = Yii::$app->db;

        $company = (new Query())
            ->from('myCompanies')
            ->where(['id' => $id])
            ->one();

        if (!$company) {
            throw new \yii\web\NotFoundHttpException('Cég nem található.');
        }

        if (Yii::$app->request->isPost) {
            $p = Yii::$app->request->post();

            $row = [
                'company_name' => trim($p['company_name'] ?? ''),
                'address'      => trim($p['address'] ?? ''),
                'zip'          => trim($p['zip'] ?? ''),
                'town'         => trim($p['town'] ?? ''),
                'ICO'          => trim($p['ICO'] ?? ''),
                'DIC'          => trim($p['DIC'] ?? ''),
                'DICDPH'       => trim($p['DICDPH'] ?? ''),
                'CEO'          => trim($p['CEO'] ?? ''),
                'DELEGATE'     => trim($p['DELEGATE'] ?? ''),
                'email'        => trim($p['email'] ?? ''),
                'phone'        => trim($p['phone'] ?? ''),
                'iban'         => trim($p['iban'] ?? ''),
                'bank_name'    => trim($p['bank_name'] ?? ''),
            ];

            $db->createCommand()->update('myCompanies', $row, ['id' => $id])->execute();
            Yii::$app->session->setFlash('success', 'Cég frissítve.');
            return $this->redirect(['documents/auto-generate']);
        }

        return $this->render('edit', ['company' => $company]);
    }
}
