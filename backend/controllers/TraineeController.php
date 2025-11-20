<?php

namespace backend\controllers;

use common\models\Agent;
use common\models\auth\AuthAssignment;
use common\models\trainee\TraineeGroup;
use Exception;
use Yii;
use yii\web\Response;

class TraineeController extends \yii\web\Controller
{
    public function actions(): array
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction' ],
            'settings' => ['class' => 'backend\actions\trainee\SettingsAction'],
            'reports' => ['class' => 'backend\actions\trainee\ReportsAction'],
        ];
    }

    public function actionSaveGroup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        parse_str(Yii::$app->request->post('xdata'), $data);

        [$startYear, $endYear] = explode('/', $data['Grupa']['year']);
        [$startWork, $endWork] = explode('-', $data['Grupa']['worktime']);

        $traineeGroup = TraineeGroup::find()
            ->andWhere(['=','school_id',$data['Grupa']['schoolid']])
            ->andWhere(['=','group',$data['Grupa']['group']])
            ->andWhere(['=','grade',$data['Grupa']['grade']])
            ->andWhere(['=','year_start',$startYear])
            ->one();
        if (!$traineeGroup) {
            $traineeGroup = new TraineeGroup();
        }
        $traineeGroup->school_id = $data['Grupa']['schoolid'];
        $traineeGroup->group = $data['Grupa']['group'];
        $traineeGroup->grade = $data['Grupa']['grade'];
        $traineeGroup->year_start = $startYear;
        $traineeGroup->year_end = $endYear;
        $traineeGroup->hour_start = $startWork;
        $traineeGroup->hour_end = $endWork;
        $traineeGroup->month_start = 9;
        $traineeGroup->month_end = 6;

        $tr = Yii::$app->db->beginTransaction();
        try {
            $traineeGroup->save();
            $tr->commit();
        } catch (Exception $ex) {
            $tr->rollBack();
            return ['status' => 'error', 'message' => $ex->getTraceAsString()];
        }

        return [
            'status' => 'ok',
            'tbody' => $this->renderPartial('tbody_trainee_group', [
                'traineeGroups' => TraineeGroup::find()->all(),
            ])
        ];
    }

    public function actionGetUsers()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $group = Yii::$app->request->post('group');
        $users = [];

        $usersInGroup = AuthAssignment::find()->andWhere(['=','item_name',$group])->all();
        if (!$usersInGroup) {
            return ['status' => 'error','message' => 'The group is empty!'];
        }
        array_walk($usersInGroup, function ($value, $key) use (&$users) {
            $agent = Agent::findOne(['user_id' => $value->user_id]);
            if ($agent) {
                $users[] = ['id' => $value->user_id, 'name' => $agent->getFullName()];
            }
        });

        return [
            'status' => 'ok',
            'user_list' => $this->renderPartial('user_items', ['users' => $users]),
        ];
    }

    public function actionCreateReports(): void
    {
        $trainees = Yii::$app->request->post('Trainee');
    }

    public function actionGetEmployees()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $employerId = (int)Yii::$app->request->post('eid');

        if ($employerId > 0) {
            $users = Agent::find()->andWhere(['=','office_id',$employerId])->all();
            $result = [];
            foreach ($users as $user) {
                $result[] = ['id' => $user->id, 'value' => $user->getFullName()];
            }
            return [
                'status' => 'ok',
                'users' => $result
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Something went wrong!',
        ];
    }
}
