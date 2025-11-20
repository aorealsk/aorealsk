<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use common\models\Osnova;
use common\models\users\UserGroups;

class SchoolController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $group = Yii::$app->request->post('Group');

            foreach ($data as $field) {
                if ($field != $group) {
                    $osnova = new Osnova();
                    foreach ($field as $col => $val) {
                        $osnova->$col = $val;
                        $osnova->group_name = $group['group_name'];
                    }
                    $osnova->save();
                }
            }
        }

        return $this->render('index', [
            'groups' => UserGroups::find()->all(),
            'osnovy' => Osnova::find()->all()
        ]);
    }

    public function actionUploadFile()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (isset($_FILES['csv'])) {
            $data = fopen($_FILES['csv']['tmp_name'], 'r');

            $tmp = fgetcsv($data);
            $cols = explode(';', $tmp[0]);
            $rows = [];
            while ($row = fgetcsv($data)) {
                $rows[] = explode(';', $row[0]);
            }
            $result = [
                'cols' => $cols,
                'rows' => $rows
            ];

            return $result;
        }
    }

    public function actionCreate()
    {
    $db = Yii::$app->db;

    if (Yii::$app->request->isPost) {
        $p = Yii::$app->request->post();

        // collect posted fields (use null coalescing to avoid notices)
        $row = [
            'description'            => trim($p['description']            ?? ''),
            'address'                => trim($p['address']                ?? ''),
            'town'                   => trim($p['town']                   ?? ''),
            'zip'                    => trim($p['zip']                    ?? ''),
            'contactPersonFirstName' => trim($p['contactPersonFirstName'] ?? ''),
            'contactPersonLastName'  => trim($p['contactPersonLastName']  ?? ''),
            'email'                  => trim($p['email']                  ?? ''),
            'phone'                  => trim($p['phone']                  ?? ''),
        ];

        // minimal required: description
        if ($row['description'] === '') {
            Yii::$app->session->setFlash('error', 'Kérlek add meg az iskola nevét.');
        } else {
            $db->createCommand()->insert('school', $row)->execute();
            Yii::$app->session->setFlash('success', 'Iskola létrehozva.');
            // go back to the generator so the dropdown sees the new school
            return $this->redirect(['documents/auto-generate']);
        }
    }

    // GET → render a simple form
    return $this->render('create');
    }


    public function actionEdit(int $id)
    {
        $osnova = Osnova::findOne($id);
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            foreach ($data as $col => $val) {
                $osnova->$col = $val;
            }
            $osnova->save();
        }

        return $this->render('edit', [
            'osnova' => $osnova
        ]);
    }
}
