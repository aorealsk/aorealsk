<?php

namespace backend\controllers;

use Yii;
use common\models\tasks\TasksProject;
use yii\web\Controller;
use yii\helpers\Url;

class TaskManagerController extends Controller
{
    public function actions()
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

        $sql = "SELECT
                    tc.*, lower(j.`name`) AS `name`
                FROM 
                    tasksColumn tc 
                JOIN
                    jazyk j ON j.id=tc.langId
                WHERE 
                    tc.`status`=1";

        return $this->render('index',[
            'projects'  => TasksProject::find()->asArray()->all(),
            'columns'   => Yii::$app->db->createCommand($sql)->queryAll()
        ]);
    }

    public function actionAddProject()
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }

        if (Yii::$app->request->isPost) {
            $projectData = Yii::$app->request->post('TasksProject');
            $project = new TasksProject();
            $project->name = $projectData['name'];
            $project->code = $projectData['code'];
            $project->color = $projectData['color'];
            $project->status = $projectData['status'];
            $project->save();
            return $this->redirect(['index']);
        }
        return $this->render('add/project', [
            'title' => Yii::t('app', 'Pridať projekt')
        ]);
    }

    public function actionEditProject(int $id)
    {
        if (is_null(Yii::$app->user->identity)) {
            return $this->redirect(Url::to(['/site/login']));
        }

        $project = TasksProject::findOne(['id' => $id]);

        if (Yii::$app->request->isPost) {
            $projectData = Yii::$app->request->post('TasksProject');
            $project->name = $projectData['name'];
            $project->code = $projectData['code'];
            $project->color = $projectData['color'];
            $project->status = $projectData['status'];
            $project->save();
            return $this->redirect(['index']);
        }
        
        return $this->render('edit/project',[
            'project'   =>  $project,
            'title'     =>  Yii::t('app', 'Upraviť projekt')
        ]);
    }
}