<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$this->title = 'Učitelia';
$this->params['breadcrumbs'][] = $this->title;

/** @var $schools array */
$schoolFilter = ArrayHelper::map($schools, 'id', 'partner_name');
?>
<div class="teacher-index">
  <h1><?= Html::encode($this->title) ?></h1>

  <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'filterModel'  => $searchModel,
      'columns' => [
          ['class' => 'yii\grid\SerialColumn'],

          [
              'attribute' => 'LastName',
              'label' => 'Vezetéknév',
          ],
          [
              'attribute' => 'FirstName',
              'label' => 'Keresztnév',
          ],
          'EmailAddress:email',
          'PhoneNumber',
          [
              'attribute' => 'SchoolID',
              'label' => 'Škola',
              'value' => function($model){ return $model->school ? $model->school->partner_name : null; },
              'filter' => $schoolFilter,
          ],
          [
              'attribute' => 'PrimaryStudy',
              'label' => 'Odbor',
          ],
          [
              'attribute' => 'BirthDate',
              'format' => ['date','php:Y-m-d'],
              'label' => 'Dátum nar.',
              'filter' => false,
          ],

          // opcionális műveletek (ha később lesz megtekintés/szerkesztés)
          // ['class' => 'yii\grid\ActionColumn'],
      ],
  ]); ?>
</div>
