<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
$this->title = 'Új iskola';
?>
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-12"><h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4></div>
  </div>

  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-body">
          <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
          <?php endif; ?>

          <?php $f = ActiveForm::begin(['method' => 'post']); ?>

            <div class="form-group">
              <label>Intézmény neve (kötelező)</label>
              <input type="text" class="form-control" name="description" required>
            </div>

            <div class="form-group">
              <label>Utca, házszám</label>
              <input type="text" class="form-control" name="address">
            </div>

            <div class="form-row d-flex gap-2">
              <div class="form-group" style="flex:1">
                <label>Irányítószám</label>
                <input type="text" class="form-control" name="zip">
              </div>
              <div class="form-group" style="flex:2">
                <label>Város</label>
                <input type="text" class="form-control" name="town">
              </div>
            </div>

            <hr>

            <div class="form-row d-flex gap-2">
              <div class="form-group" style="flex:1">
                <label>Kapcsolattartó keresztnév</label>
                <input type="text" class="form-control" name="contactPersonFirstName">
              </div>
              <div class="form-group" style="flex:1">
                <label>Kapcsolattartó vezetéknév</label>
                <input type="text" class="form-control" name="contactPersonLastName">
              </div>
            </div>

            <div class="form-row d-flex gap-2">
              <div class="form-group" style="flex:1">
                <label>E-mail</label>
                <input type="email" class="form-control" name="email">
              </div>
              <div class="form-group" style="flex:1">
                <label>Telefon</label>
                <input type="text" class="form-control" name="phone">
              </div>
            </div>

            <div class="d-flex gap-2 mt-3">
              <button type="submit" class="btn btn-success">Mentés</button>
              <?= Html::a('Mégse', ['documents/auto-generate'], ['class'=>'btn btn-light']) ?>
            </div>

          <?php ActiveForm::end(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
