<?php
use yii\helpers\Html;
/** @var yii\data\ActiveDataProvider $dp */
$this->title = 'Csoportok';
?>
<div class="container-fluid">
  <h3><?= Html::encode($this->title) ?></h3>
  <p><?= Html::a('Új csoport', ['create'], ['class'=>'btn btn-success']) ?></p>
  <table class="table table-striped">
    <thead><tr><th>Név</th><th>Leírás</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($dp->getModels() as $g): ?>
      <tr>
        <td><?= Html::encode($g->name) ?></td>
        <td><?= Html::encode($g->description) ?></td>
        <td>
          <?= Html::a('Szerkeszt', ['update','id'=>$g->id], ['class'=>'btn btn-sm btn-primary']) ?>
          <?= Html::a('Törlés', ['delete','id'=>$g->id], [
            'class'=>'btn btn-sm btn-danger',
            'data-confirm'=>'Biztos?',
            'data-method'=>'post'
          ]) ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
