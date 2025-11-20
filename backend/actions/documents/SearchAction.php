<?php

namespace backend\actions\documents;

use Yii;
use yii\base\Action;
use yii\web\Response;

class SearchAction extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
            $searchValue = Yii::$app->request->post('searchValue');
            $searchResults = Yii::$app->db->createCommand(
                "SELECT id, name FROM template 
                WHERE 
                MATCH(content, name) AGAINST('${searchValue}')
                "
            )->queryAll();

           return $searchResults;
    }
}
