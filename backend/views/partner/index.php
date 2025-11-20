<?php
use yii\helpers\Html;

/** @var array $partners */
$this->title = 'Partnerek';
?>
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
      <h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4>
      <?= Html::a('Új partner', ['partner/create'], ['class'=>'btn btn-primary']) ?>
    </div>
  </div>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Név</th>
            <th>Cím</th>
            <th>IČO</th>
            <th>DIČ</th>
            <th>IČ DPH</th>
            <th>CEO</th>
            <th>DELEGATE</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($partners)): ?>
            <tr><td colspan="8" class="text-muted">Még nincs partner.</td></tr>
          <?php else: ?>
            <?php foreach ($partners as $p): ?>
              <tr>
                <td><?= (int)$p['id'] ?></td>
                <td><?= Html::encode($p['partner_name']) ?></td>
                <td>
                  <?= Html::encode(trim(($p['address'] ?? '') .
                        (empty($p['zip']) && empty($p['town']) ? '' : ', ' .
                         trim(($p['zip'] ?? '').' '.($p['town'] ?? ''))))) ?>
                </td>
                <td><?= Html::encode($p['ICO'] ?? '') ?></td>
                <td><?= Html::encode($p['DIC'] ?? '') ?></td>
                <td><?= Html::encode($p['DICDPH'] ?? '') ?></td>
                <td><?= Html::encode($p['CEO'] ?? '') ?></td>
                <td><?= Html::encode($p['DELEGATE'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="mt-3">
        <?= Html::a('Vissza a generáláshoz', ['documents/auto-generate'], ['class'=>'btn btn-light']) ?>
      </div>
    </div>
  </div>
</div>
