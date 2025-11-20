<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Új cég';
?>
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-12"><h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4></div>
  </div>

  <div class="card"><div class="card-body">
    <?php $f = ActiveForm::begin(['method'=>'post']); ?>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group"><label>Név</label><input class="form-control" name="company_name" required></div>
        <div class="form-group"><label>Cím</label><input class="form-control" name="address"></div>
        <div class="form-group"><label>Város</label><input class="form-control" name="town"></div>
        <div class="form-group"><label>ZIP</label><input class="form-control" name="zip"></div>
        <div class="form-group"><label>ICO</label><input class="form-control" name="ICO"></div>
        <div class="form-group"><label>DIČ</label><input class="form-control" name="DIC"></div>
        <div class="form-group"><label>IČ DPH</label><input class="form-control" name="DICDPH"></div>
      </div>
      <div class="col-md-6">
        <div class="form-group"><label>CEO</label><input class="form-control" name="CEO"></div>
        <div class="form-group"><label>Meghatalmazott</label><input class="form-control" name="DELEGATE"></div>
        <div class="form-group"><label>E-mail</label><input class="form-control" name="email"></div>
        <div class="form-group"><label>Telefon</label><input class="form-control" name="phone"></div>
        <div class="form-group"><label>IBAN</label><input class="form-control" name="iban"></div>
        <div class="form-group"><label>Bank neve</label><input class="form-control" name="bank_name"></div>
      </div>
    </div>

    <button class="btn btn-success">Mentés</button>
    <?= Html::a('Mégse', ['documents/auto-generate'], ['class'=>'btn btn-secondary']) ?>

    <?php ActiveForm::end(); ?>
  </div></div>
</div>
