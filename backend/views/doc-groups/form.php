<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var \common\models\DocGroup $model */
/** @var array $allUsers */
/** @var \common\models\User[] $members */
$this->title = $model->isNewRecord ? 'Új csoport' : 'Csoport szerkesztése: '.$model->name;
?>
<div class="container-fluid">
  <h3><?= Html::encode($this->title) ?></h3>

  <?php $f = ActiveForm::begin(); ?>
    <?= $f->field($model,'name')->textInput(['maxlength'=>true]) ?>
    <?= $f->field($model,'description')->textarea(['rows'=>3]) ?>
    <div class="form-group">
      <button class="btn btn-success">Mentés</button>
      <?= Html::a('Vissza', ['index'], ['class'=>'btn btn-secondary']) ?>
    </div>
  <?php ActiveForm::end(); ?>

  <?php if (!$model->isNewRecord): ?>
    <hr>
    <h4>Tagok</h4>
    <form method="post">
      <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
      <div class="form-group">
        <label>Felhasználók hozzáadása</label>
        <select name="user_ids[]" class="form-control" multiple size="8">
          <?php foreach ($allUsers as $u): ?>
            <option value="<?= (int)$u['id'] ?>"><?= Html::encode($u['username'].' ('.$u['email'].')') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn-primary">Hozzáadás</button>
    </form>

    <table class="table table-sm table-bordered mt-3">
      <thead><tr><th>Felhasználó</th><th>Email</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($members as $u): ?>
        <tr>
          <td><?= Html::encode($u->username) ?></td>
          <td><?= Html::encode($u->email) ?></td>
          <td>
            <?= Html::a('Eltávolít', ['update','id'=>$model->id,'remove_user'=>$u->id], [
              'class'=>'btn btn-danger btn-sm',
              'data-confirm'=>'Biztos?',
            ]) ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
