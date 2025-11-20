<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Új partner';
?>
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-12"><h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4></div>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="card"><div class="card-body">

        <?php if (Yii::$app->session->hasFlash('error')): ?>
          <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
        <?php endif; ?>

        <?php $f = ActiveForm::begin(['method' => 'post']); ?>

          <div class="form-group">
            <label>Partner típusa (opcionális)</label>
            <input class="form-control" name="partner_type" placeholder="pl. cég / SZČO">
          </div>

          <div class="form-group">
            <label>Partner neve (kötelező)</label>
            <input class="form-control" name="partner_name" required>
          </div>

          <div class="form-group">
            <label>Cím</label>
            <input class="form-control" name="address" placeholder="Utca, házszám">
          </div>

          <div class="form-row d-flex gap-2">
            <div class="form-group" style="flex:1">
              <label>Irányítószám</label>
              <input class="form-control" name="zip">
            </div>
            <div class="form-group" style="flex:2">
              <label>Város</label>
              <input class="form-control" name="town">
            </div>
          </div>

          <hr>

          <div class="form-row d-flex gap-2">
            <div class="form-group" style="flex:1">
              <label>IČO</label>
              <input class="form-control" name="ICO">
            </div>
            <div class="form-group" style="flex:1">
              <label>DIČ</label>
              <input class="form-control" name="DIC">
            </div>
            <div class="form-group" style="flex:1">
              <label>IČ DPH</label>
              <input class="form-control" name="DICDPH">
            </div>
          </div>

          <div class="form-group">
            <label>Ügyvezető (CEO)</label>
            <input class="form-control" name="CEO">
          </div>

          <div class="form-group">
            <label>Meghatalmazott (DELEGATE)</label>
            <input class="form-control" name="DELEGATE">
          </div>

          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-success" type="submit">Mentés</button>
            <?= Html::a('Mégse', ['documents/auto-generate'], ['class'=>'btn btn-light']) ?>
          </div>

        <?php ActiveForm::end(); ?>

      </div></div>
    </div>
  </div>
</div>
