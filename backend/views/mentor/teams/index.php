<?php
use yii\helpers\Html;

/** @var $profile \common\models\MentorProfile */
/** @var $teams \common\models\Team[] */
$this->title = 'Moje tímy';
?>
<div class="container">
  <h1><?= Html::encode($this->title) ?></h1>

  <p>
    <strong>Rola:</strong> <?= Html::encode($profile->role) ?>,
    <strong>Organizácia:</strong> <?= Html::encode($profile->org_name) ?>
  </p>

  <p><?= Html::a('Vytvoriť tím', ['team-create'], ['class'=>'btn btn-primary']) ?></p>

  <table class="table table-bordered table-sm">
    <thead>
      <tr>
        <th>Názov tímu</th>
        <th>Počet študentov</th>
        <th>Akcia</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($teams as $t): ?>
      <tr>
        <td><?= Html::encode($t->name) ?></td>
        <td><?= (int)$t->getStudents()->count() ?></td>
        <td><?= Html::a('Upraviť', ['team-update', 'id'=>$t->id], ['class'=>'btn btn-sm btn-secondary']) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($teams)): ?>
      <tr><td colspan="3" class="text-muted">Zatiaľ nemáte žiadne tímy.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
