<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var $model \common\models\MentorProfile */
$this->title = 'Môj profil (učiteľ / partner)';
?>
<div class="container">
  <h1><?= Html::encode($this->title) ?></h1>

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($model, 'role')->dropDownList([
      'teacher'          => 'Učiteľ',
      'supervisor'       => 'Supervízor',
      'business_partner' => 'Firemný partner',
  ], ['prompt'=>'— Vyberte rolu —']) ?>

  <?= $form->field($model, 'org_name')->textInput(['maxlength'=>true, 'placeholder'=>'Názov školy / firmy']) ?>
  <?= $form->field($model, 'phone')->textInput(['maxlength'=>true, 'placeholder'=>'+421...']) ?>

  <div class="form-group">
    <?= Html::submitButton('Uložiť profil', ['class'=>'btn btn-primary']) ?>
    <?= Html::a('Moje tímy', ['teams'], ['class'=>'btn btn-link']) ?>
    <?= Html::a('Pre Učiteľov', ['mentor/download-mentor-doc'], ['class' => 'btn btn-link']) ?>
    <?= Html::a('Pre Partnerov - JAVÍTANI KELL', ['mentor/download-partners'], ['class' => 'btn btn-link']) ?>
  </div>

  <?php ActiveForm::end(); ?>
</div>
