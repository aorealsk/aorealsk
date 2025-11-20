<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\db\Query;

class RewardDashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // only logged-in users (admins)
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $searchName = trim($request->get('search'));
        $startDate  = $request->get('start');
        $endDate    = $request->get('end');
        $sortBy     = $request->get('sort');

        // Default time range: last 24 hours
        if (empty($startDate) && empty($endDate)) {
            $startDate = date('Y-m-d H:i:s', strtotime('-1 day'));
            $endDate   = date('Y-m-d H:i:s');
        } else {
            // Convert date-only input into full timestamps
            if (!empty($startDate)) $startDate .= ' 00:00:00';
            if (!empty($endDate))   $endDate   .= ' 23:59:59';
        }

        // Build query
        $query = (new Query())
            ->from('user')
            ->select([
                'id',
                'username',
                "CONCAT(name_first, ' ', name_last) AS name",
                'ticket AS tickets',
                'gold',
                'updated_at'
            ])
            ->where(['between', 'updated_at', $startDate, $endDate]);

        // Search by name or username
        if ($searchName) {
            $query->andFilterWhere(['or',
                ['like', 'username', $searchName],
                ['like', "CONCAT(name_first,' ',name_last)", $searchName]
            ]);
        }

        // Sorting
        if ($sortBy === 'tickets') {
            $query->orderBy(['tickets' => SORT_DESC])->limit(15);
        } elseif ($sortBy === 'gold') {
            $query->orderBy(['gold' => SORT_DESC, 'tickets' => SORT_DESC])->limit(15);
        } else {
            $query->orderBy(['updated_at' => SORT_DESC]);
        }

        $dashboardData = $query->all();

        return $this->render('index', [
            'dashboardData' => $dashboardData,
            'searchName' => $searchName,
            'startDate' => $request->get('start'),
            'endDate' => $request->get('end'),
            'sortBy' => $sortBy,
        ]);
    }
}
