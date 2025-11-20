<?php
/** @var yii\web\View $this */
/** @var yii\data\SqlDataProvider $dataProvider */
/** @var string $q */
/** @var int $uid */
/** @var bool|null $embed */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$embed = isset($embed) ? (bool)$embed : false;

if (!$embed) {
    $this->title = Yii::t('app', 'Dochádzka – všetky záznamy');
}

// Small tweak for embeds so backgrounds don’t stack weirdly
if ($embed) {
    $this->registerCss('body{background:transparent}');
}
?>

<?php if (!$embed): ?>
  <div class="page-header mb-3">
    <h1 class="h3"><?= Html::encode($this->title) ?></h1>
  </div>
<?php endif; ?>

<div class="<?= $embed ? '' : 'card' ?>">
  <?php if (!$embed): ?><div class="card-body"><?php endif; ?>

    <?php $form = ActiveForm::begin([
        'method'  => 'get',
        'action'  => Url::to(['user-attendance-admin/roster']),
        'options' => ['class' => $embed ? 'p-3 pt-3 pb-0' : 'mb-3'],
    ]); ?>
      <div class="row g-2">
        <div class="col-md-6">
          <input name="q" value="<?= Html::encode($q) ?>" class="form-control" placeholder="<?= Yii::t('app','Hľadať: meno, používateľ, email') ?>">
        </div>
        <div class="col-md-3">
          <input name="uid" value="<?= (int)$uid ?>" class="form-control" placeholder="User ID">
        </div>
        <div class="col-md-3">
          <button class="btn btn-primary w-100"><?= Yii::t('app','Filtrovať') ?></button>
        </div>
      </div>
    <?php ActiveForm::end(); ?>

    <?= $this->render('_rosterGrid', ['dataProvider' => $dataProvider]) ?>

  <?php if (!$embed): ?></div><?php endif; ?>
</div>
