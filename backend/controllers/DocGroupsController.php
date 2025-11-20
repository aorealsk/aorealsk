<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

use common\models\DocGroup;
use common\models\DocGroupUser;   // pivot: doc_group_member
use common\models\User;

class DocGroupsController extends Controller
{
    public function actionIndex()
    {
        $dp = new ActiveDataProvider([
            'query'      => DocGroup::find()->orderBy(['name' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', compact('dp'));
    }

    public function actionCreate()
    {
        $model = new DocGroup();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Csoport létrehozva.');
            return $this->redirect(['update', 'id' => $model->id]);
        }
        return $this->render('form', compact('model'));
    }

    public function actionUpdate($id)
    {
        $model = DocGroup::findOne((int)$id);
        if (!$model) {
            throw new NotFoundHttpException();
        }

        // update group fields
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Mentve.');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        // bulk add members
        if (Yii::$app->request->isPost) {
            $ids = (array)Yii::$app->request->post('user_ids', []);
            if (!empty($ids)) {
                foreach ($ids as $uid) {
                    $uid = (int)$uid;
                    if ($uid > 0 && !DocGroupUser::find()
                        ->where(['group_id' => $model->id, 'user_id' => $uid])
                        ->exists()) {
                        (new DocGroupUser(['group_id' => $model->id, 'user_id' => $uid]))->save(false);
                    }
                }
                Yii::$app->session->setFlash('success', 'Tagok hozzáadva.');
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }

        // remove one member (via GET remove_user=ID)
        $removeId = (int)Yii::$app->request->get('remove_user', 0);
        if ($removeId > 0) {
            DocGroupUser::deleteAll(['group_id' => $model->id, 'user_id' => $removeId]);
            Yii::$app->session->setFlash('success', 'Tag eltávolítva.');
            return $this->redirect(['update', 'id' => $model->id]);
        }

        $allUsers = User::find()
            ->select(['id', 'username', 'email'])
            ->orderBy(['username' => SORT_ASC])
            ->asArray()
            ->all();

        $members = $model->users;   // assumes DocGroup::getUsers() relation

        return $this->render('form', compact('model', 'allUsers', 'members'));
    }

    public function actionDelete($id)
    {
        $model = DocGroup::findOne((int)$id);
        if ($model) {
            // clean up pivot rows first (no FK cascade on some hosts)
            DocGroupUser::deleteAll(['group_id' => $model->id]);
            $model->delete();
            Yii::$app->session->setFlash('success', 'Csoport törölve.');
        } else {
            Yii::$app->session->setFlash('error', 'Skupina nenájdená.');
        }
        return $this->redirect(['index']);
    }
}
