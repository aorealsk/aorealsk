<?php
/** @var yii\web\View $this */
/** @var yii\data\SqlDataProvider|yii\data\ActiveDataProvider $dataProvider */

use yii\grid\GridView;

$this->registerCss('
#roster-card .grid-view .table { margin-bottom: 0; }
#roster-card .grid-view .summary { margin: .5rem 0; font-size: .9rem; color: #666; }
#roster-card .grid-view .pagination { margin: .5rem 0 0 0; }
');

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class' => 'table table-sm table-striped mb-0'],
    'layout'       => "{items}\n<div class=\"d-flex justify-content-between align-items-center px-3 py-2\"><div class=\"summary\"></div>{pager}</div>",
    'columns'      => [
        // Adjust attribute names to match your query aliases (see controller note below)
        ['attribute' => 'id',            'label' => 'ID', 'contentOptions'=>['style'=>'width:80px']],
        ['attribute' => 'username',      'label' => 'Username'],
        ['attribute' => 'full_name',     'label' => Yii::t('app','Meno')],
        ['attribute' => 'date',          'label' => Yii::t('app','Dátum'),          'format' => ['date', 'php:Y-m-d']],
        ['attribute' => 'start_time',    'label' => Yii::t('app','Začiatok')],
        ['attribute' => 'end_time',      'label' => Yii::t('app','Koniec')],
        ['attribute' => 'worked_hms',    'label' => Yii::t('app','Odpracované')],
        ['attribute' => 'phone',         'label' => Yii::t('app','Telefón')],
        ['attribute' => 'email',         'label' => 'E-mail', 'format' => 'email'],
        ['attribute' => 'date_of_birth', 'label' => Yii::t('app','Dátum narodenia'), 'format' => ['date', 'php:Y-m-d']],
    ],
    'pager' => [
        'firstPageLabel' => 'Prvá',
        'lastPageLabel'  => 'Posledná',
        'maxButtonCount' => 7,
        'options'        => ['class' => 'pagination mb-0'],
    ],
    'summary' => 'Zobrazené {begin}–{end} z {totalCount} záznamov',
]);
