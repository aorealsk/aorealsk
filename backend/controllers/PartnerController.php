<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;

class PartnerController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $partners = (new \yii\db\Query())
            ->from('partners') // <-- plural table
            ->orderBy(['partner_name' => SORT_ASC])
            ->all();

        return $this->render('index', ['partners' => $partners]);
    }

    public function actionCreate()
    {
        $db = Yii::$app->db;

        if (Yii::$app->request->isPost) {
            $p = Yii::$app->request->post();

            $row = [
                'partner_type' => $p['partner_type'] ?? null,     // optional
                'partner_name' => trim($p['partner_name'] ?? ''),
                'address'      => trim($p['address'] ?? ''),
                'town'         => trim($p['town'] ?? ''),
                'zip'          => trim($p['zip'] ?? ''),
                // ONLY the new fields:
                'ICO'          => trim($p['ICO'] ?? ''),
                'DIC'          => trim($p['DIC'] ?? ''),
                'DICDPH'       => trim($p['DICDPH'] ?? ''),
                'CEO'          => trim($p['CEO'] ?? ''),
                'DELEGATE'     => trim($p['DELEGATE'] ?? ''),
            ];

            if ($row['partner_name'] === '') {
                Yii::$app->session->setFlash('error', 'Add meg a partner nevét.');
            } else {
                $db->createCommand()->insert('partners', $row)->execute(); // <-- plural table
                Yii::$app->session->setFlash('success', 'Partner létrehozva.');
                return $this->redirect(['documents/auto-generate']);
            }
        }

        return $this->render('create');
    }
}
