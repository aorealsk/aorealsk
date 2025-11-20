<?php
use yii\helpers\Html;
/** @var array $companies */
$this->title = 'Cégek';
?>
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-12"><h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4></div>
  </div>

  <div class="card"><div class="card-body">
    <div class="mb-3">
      <?= Html::a('Új cég', ['my-company/create'], ['class'=>'btn btn-primary']) ?>
      <?= Html::a('Vissza a generáláshoz', ['documents/auto-generate'], ['class'=>'btn btn-secondary']) ?>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Név</th><th>Cím</th><th>Város</th><th>ZIP</th>
            <th>ICO</th><th>DIČ</th><th>IČ DPH</th>
            <th>CEO</th><th>Meghatalmazott</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($companies as $c): ?>
            <tr>
              <td><?= Html::encode($c['company_name']) ?></td>
              <td><?= Html::encode($c['address']) ?></td>
              <td><?= Html::encode($c['town']) ?></td>
              <td><?= Html::encode($c['zip']) ?></td>
              <td><?= Html::encode($c['ICO']) ?></td>
              <td><?= Html::encode($c['DIC']) ?></td>
              <td><?= Html::encode($c['DICDPH']) ?></td>
              <td><?= Html::encode($c['CEO']) ?></td>
              <td><?= Html::encode($c['DELEGATE']) ?></td>
              <td><?= Html::a('Szerkesztés', ['my-company/edit', 'id'=>$c['id']], ['class'=>'btn btn-sm btn-outline-primary']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div></div>
</div>
