<?php

namespace frontend\controllers;

use common\models\users\Developers;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;

class DevTestController extends Controller
{
    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {

            $data = Yii::$app->request->post('Quiz');
            $tr = Yii::$app->db->beginTransaction();
            try{
                $resp = [];
                foreach(range(1,28) as $id) {
                    $resp[$id] = $data["q{$id}"] ?? '';
                }

                $dev = new Developers();
                $dev->name_first = $data['name_first'];
                $dev->name_last = $data['name_last'];
                $dev->email = $data['email'];
                $dev->test_result = json_encode($resp);
                $dev->skills = json_encode($data['skills']);
                $dev->save();

                $tr->commit();

                return $this->redirect(Url::to(['dev-test/thank-you']));
            } catch (\Exception $e) {
                $tr->rollBack();
                echo $e->getMessage();exit;
            }
        }
        return $this->render('index', [
            'data' => $data ?? []
        ]);
    }

    public function actionThankYou()
    {
        return $this->render('thank-you');
    }
}