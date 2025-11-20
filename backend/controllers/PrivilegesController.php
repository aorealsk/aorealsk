<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use app\models\Privilege;
use app\models\PrivilegesTemplate;
use app\models\PrivilegesUser;

class PrivilegesController extends Controller
{
    public function actionIndex()
    {
        // all active privileges (rows)
        $privileges = Privilege::find()
            ->where(['status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        // all active groups (columns)
        $groups = PrivilegesTemplate::find()
            ->where(['status' => 1])
            ->orderBy(['group_name' => SORT_ASC])
            ->all();

        if (Yii::$app->request->isPost) {
            $matrix = Yii::$app->request->post('Matrix', []);

            // clear current group-level permissions
            PrivilegesUser::deleteAll(['userId' => null]);

            // recreate from POST
            foreach ($matrix as $groupName => $privilegeIds) {
                foreach (array_keys($privilegeIds) as $privId) {
                    $pu = new PrivilegesUser();
                    $pu->group        = $groupName;         // col name
                    $pu->userId       = null;               // group-level
                    $pu->privilegesid = (int)$privId;       // row id
                    $pu->status       = 1;
                    $pu->save(false);
                }
            }

            Yii::$app->session->setFlash('success', 'Práva boli uložené.');
            return $this->redirect(['index']);
        }

        // build a quick lookup: [group_name][privilege_id] => true
        $existing = [];
        $rows = PrivilegesUser::find()
            ->where(['userId' => null, 'status' => 1])
            ->all();

        foreach ($rows as $row) {
            $existing[$row->group][$row->privilegesid] = true;
        }

        return $this->render('index', [
            'privileges' => $privileges,
            'groups'     => $groups,
            'existing'   => $existing,
        ]);
    }
}
