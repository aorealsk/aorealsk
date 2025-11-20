<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model \backend\models\users\ChangePasswordForm */
/** @var $user  \common\models\User */

$this->title = 'Jelszó módosítása: ' . Html::encode($user->username ?? ('ID #'.$user->id));
?>
<div class="user-change-password">
  <h1><?= Html::encode($this->title) ?></h1>

  <?php if (Yii::$app->session->hasFlash('error')): ?>
      <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
  <?php endif; ?>

  <?php $form = ActiveForm::begin([
      // RELATÍV route a jelenlegi UsersControllerhez → nem dupláz “backoffice”-t
      'action' => ['change-password', 'uid' => $user->id],
      'method' => 'post',
      'options' => ['id' => 'change-pass-form'],
  ]); ?>

    <?= Html::hiddenInput('uid', $user->id) ?> <!-- biztonsági öv -->

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <?= $form->field($model, 'new_password')->passwordInput([
            'autocomplete' => 'new-password',
            'placeholder'  => 'Új jelszó'
        ]) ?>

    <?= $form->field($model, 'new_password_repeat')->passwordInput([
            'autocomplete' => 'new-password',
            'placeholder'  => 'Új jelszó ismét'
        ]) ?>

    <div class="form-group">
      <?= Html::submitButton('Mentés', ['class'=>'btn btn-primary']) ?>
      <?= Html::a('Mégse', ['edit', 'uid' => $user->id], ['class'=>'btn btn-default']) ?>
    </div>

  <?php ActiveForm::end(); ?>
</div>
