<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var array $company */
$this->title = 'Cég szerkesztése: ' . ($company['company_name'] ?? '');
?>
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-12"><h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4></div>
  </div>

  <div class="card"><div class="card-body">
    <?php $f = ActiveForm::begin(['method'=>'post']); ?>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group"><label>Név</label><input class="form-control" name="company_name" value="<?= Html::encode($company['company_name']) ?>" required></div>
        <div class="form-group"><label>Cím</label><input class="form-control" name="address" value="<?= Html::encode($company['address']) ?>"></div>
        <div class="form-group"><label>Város</label><input class="form-control" name="town" value="<?= Html::encode($company['town']) ?>"></div>
        <div class="form-group"><label>ZIP</label><input class="form-control" name="zip" value="<?= Html::encode($company['zip']) ?>"></div>
        <div class="form-group"><label>ICO</label><input class="form-control" name="ICO" value="<?= Html::encode($company['ICO']) ?>"></div>
        <div class="form-group"><label>DIČ</label><input class="form-control" name="DIC" value="<?= Html::encode($company['DIC']) ?>"></div>
        <div class="form-group"><label>IČ DPH</label><input class="form-control" name="DICDPH" value="<?= Html::encode($company['DICDPH']) ?>"></div>
      </div>
      <div class="col-md-6">
        <div class="form-group"><label>CEO</label><input class="form-control" name="CEO" value="<?= Html::encode($company['CEO']) ?>"></div>
        <div class="form-group"><label>Meghatalmazott</label><input class="form-control" name="DELEGATE" value="<?= Html::encode($company['DELEGATE']) ?>"></div>
        <div class="form-group"><label>E-mail</label><input class="form-control" name="email" value="<?= Html::encode($company['email']) ?>"></div>
        <div class="form-group"><label>Telefon</label><input class="form-control" name="phone" value="<?= Html::encode($company['phone']) ?>"></div>
        <div class="form-group"><label>IBAN</label><input class="form-control" name="iban" value="<?= Html::encode($company['iban']) ?>"></div>
        <div class="form-group"><label>Bank neve</label><input class="form-control" name="bank_name" value="<?= Html::encode($company['bank_name']) ?>"></div>
      </div>
    </div>

    <button class="btn btn-success">Mentés</button>
    <?= Html::a('Mégse', ['documents/auto-generate'], ['class'=>'btn btn-secondary']) ?>

    <?php ActiveForm::end(); ?>
  </div></div>
</div>
