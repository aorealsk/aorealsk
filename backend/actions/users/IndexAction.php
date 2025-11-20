<?php
namespace backend\actions\users;

use common\models\settings\Privileges;
use common\models\users\PrivilegesUsers;
use common\models\users\UserGroups;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use Yii;

class IndexAction extends Action
{
    public function run()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->controller->redirect(Url::to(['/site/login']));
        }
        return $this->controller->render('index', [
            'userlist'  => $this->getUserList(),
            'usergroups' => UserGroups::find()->where('type>0')->asArray()->all(),
            'privileges' => Privileges::find()->asArray()->all(),
            'documents' => $this->getDocuments(),
            'groupmatrix'   => $this->getGroupAccessMatrix()
        ]);
    }

    private function getGroupAccessMatrix()
    {
        $matrix = UserGroups::find()->select(['name'])->asArray()->all();
        $access = [];
        foreach ($matrix as $item) {
            $tmp = PrivilegesUsers::find()
                ->select(['privilegesId'])
                ->andWhere(['=','group',$item['name']])
                ->andWhere(['=','userId',0])
                ->andWhere(['=','status',1])
                ->asArray()->all();
            if (count($tmp) == 0) {
                $access[$item['name']] = [];
            } else {
                foreach ($tmp as $i) {
                    $access[$item['name']][] = $i['privilegesId'];
                }
            }


        }
        return $access;
    }

    private function getUserList()
    {
    // Prefer data stored directly on `user`, fallback to legacy `agent` values
    // Preload guardians_count so the view doesn't need an extra query per row
    return Yii::$app->db->createCommand("
        SELECT
            u.id,
            COALESCE(u.name_first, a.name_first) AS name_first,
            COALESCE(u.name_last,  a.name_last)  AS name_last,
            COALESCE(u.phone,      a.phone)      AS phone,
            u.email,
            u.username,
            u.status,
            u.birthdate,
            (
                SELECT COUNT(*) 
                FROM user_guardian ug 
                WHERE ug.user_id = u.id
            ) AS guardians_count
        FROM user u
        LEFT JOIN agent a ON a.user_id = u.id
        ORDER BY u.id DESC
    ")->queryAll();
    }



    public function getDocuments()
    {
        $sql = "SELECT
                     id, name
                FROM
                    template";
       return Yii::$app->db->createCommand($sql)->queryAll();
        
    }
}