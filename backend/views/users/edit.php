<?php
/** @var \yii\web\View $this */
/** @var \common\models\User $model */
/** @var bool $isNew */
/** @var \common\models\UserGuardian[]|null $guardians */
/** @var \common\models\UserGuardian|null $g1 */
/** @var \common\models\UserGuardian|null $g2 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\StudyPlanType;

if (!isset($isNew)) {
    $isNew = $model->isNewRecord;
}

$this->title = $isNew ? 'Pridať používateľa' : "Upraviť používateľa – {$model->username}";

$shirtSizes = ['XS'=>'XS','S'=>'S','M'=>'M','L'=>'L','XL'=>'XL','XXL'=>'XXL'];
$pantsSizes = [
    '28/30'=>'28/30','28/32'=>'28/32','30/30'=>'30/30','30/32'=>'30/32','31/32'=>'31/32','32/30'=>'32/30','32/32'=>'32/32',
    '33/32'=>'33/32','34/30'=>'34/30','34/32'=>'34/32','36/32'=>'36/32','38/32'=>'38/32'
];
$shoeSizesEU = array_combine(range(35, 48), range(35, 48));

/** ---- Study plan types for dropdown ---- */
$schema  = Yii::$app->db->schema->getTableSchema('{{%study_plan_types}}');
$hasCode = $schema && isset($schema->columns['code']);
$types   = StudyPlanType::find()
    ->orderBy($hasCode ? ['code'=>SORT_ASC, 'name'=>SORT_ASC] : ['name'=>SORT_ASC])
    ->all();

$planTypes = ArrayHelper::map($types, 'id', function(StudyPlanType $m) use ($hasCode) {
    $code = trim((string)($hasCode ? $m->code : ''));
    $name = trim((string)$m->name);
    if ($code !== '' && $name !== '') return $code . ' – ' . $name;
    if ($name !== '') return $name;
    if ($code !== '') return $code;
    return 'Bez názvu (' . $m->id . ')';
});

/** --------- Guardians helpers --------- */
$gPosted = Yii::$app->request->post('Guardian', []);
$guardianModels = [
    0 => $guardians[0] ?? $g1 ?? null,
    1 => $guardians[1] ?? $g2 ?? null,
];
$gval = function(int $idx, string $field, string $fallback = '') use ($gPosted, $guardianModels): string {
    if (isset($gPosted[$idx][$field])) {
        return Html::encode((string)$gPosted[$idx][$field]);
    }
    $m = $guardianModels[$idx] ?? null;
    return Html::encode($m ? (string)$m->{$field} : $fallback);
};
$wrapDisplay = $model->isMinor ? '' : 'none';
$birthId = Html::getInputId($model, 'birthdate');

/** ---- Placeholder styling: tmavožlté a kurzívou ---- */
$css = <<<CSS
.form-control::placeholder {
    color: #d4a017;      /* tmavožltá */
    font-style: italic;  /* kurzíva */
    opacity: 1;
}
CSS;
$this->registerCss($css);
?>

<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-6">
      <h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4>
    </div>
  </div>

  <div class="card rounded-5 card-shadow">
    <div class="card-body">
      <?php $form = ActiveForm::begin(); ?>

      <div class="row">
        <div class="col-md-6">
          <?= $form->field($model, 'username')
                   ->label('Prihlasovacie meno')
                   ->textInput(['maxlength'=>255, 'placeholder' => 'napr. jnovak']) ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'email')
                   ->label('E-mail')
                   ->textInput([
                       'type' => 'email',
                       'maxlength'=>255,
                       'placeholder' => 'napr. jan.novak@example.sk'
                   ]) ?>
        </div>

        <div class="col-md-6">
          <?= $form->field($model, 'newPassword')
                   ->label('Nové heslo')
                   ->passwordInput(['placeholder' => $isNew ? 'Zadajte heslo' : 'Nechajte prázdne, ak nechcete meniť heslo'])
                   ->hint($isNew ? '' : 'Nechajte pole prázdne, ak nechcete meniť heslo.') ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'newPasswordRepeat')
                   ->label('Nové heslo znova')
                   ->passwordInput(['placeholder' => 'Zopakujte nové heslo']) ?>
        </div>

        <div class="col-md-6">
          <?= $form->field($model, 'name_first')
                   ->label('Meno')
                   ->textInput([
                       'maxlength'=>255,
                       'placeholder'=>'napr. Ján'
                   ]) ?>
        </div>
        <div class="col-md-6">
          <?= $form->field($model, 'name_last')
                   ->label('Priezvisko')
                   ->textInput([
                       'maxlength'=>255,
                       'placeholder'=>'napr. Novák'
                   ]) ?>
        </div>

        <div class="col-md-4">
          <?= $form->field($model, 'birthdate')
                   ->label('Dátum narodenia')
                   ->input('date')
                   ->hint('Formát: RRRR-MM-DD') ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'phone')
                   ->label('Telefón')
                   ->textInput([
                       'maxlength'=>32,
                       'placeholder'=>'+421 901 234 567'
                   ]) ?>
        </div>
        <div class="col-md-4">
          <?= $form->field($model, 'iban')
                   ->label('IBAN')
                   ->textInput([
                       'maxlength'=>40,
                       'placeholder'=>'napr. SK68 1100 0000 0029 1234 5678'
                   ]) ?>
        </div>
      </div>

      <!-- ===== TRIEDA + ŠTUDIJNÝ PLÁN ===== -->
<div class="row mt-2">
  <div class="col-md-6">
    <?= $form->field($model, 'userclassroom')
             ->label('Trieda')
             ->textInput([
                 'maxlength'   => 50,
                 'placeholder' => 'napr. 9.A / 1-2: skupina',
                 'pattern'     => '^[0-9A-Za-zÁÄČĎÉÍĹĽŇÓÔŔŠŤÚÝŽáäčďéíĺľňóôŕšťúýž ._\/:-]+$',
                 'title'       => 'Povolené sú písmená, čísla a znaky . _ / - :'
             ]) ?>
  </div>

  <div class="col-md-6">
    <?= $form->field($model, 'study_plan_type_id')
             ->label('Študijný plán')
             ->dropDownList(
                 $planTypes,
                 ['prompt' => '– bez študijného plánu –']
             )
             ->hint('Použije sa pre blok „Vyučovací plán“ v generátore dokumentov.') ?>
  </div>
</div>
<!-- ================================== -->


      <!-- ================= ZÁKONNÍ ZÁSTUPCOVIA ================= -->
      <div id="guardians-wrap" class="mt-4" style="display: <?= $wrapDisplay ?>">
        <h5 class="mb-3">Zákonní zástupcovia (povinné pre osoby mladšie ako 18 rokov)</h5>

        <?php foreach ([0 => 'Zákonný zástupca 1', 1 => 'Zákonný zástupca 2'] as $idx => $label): ?>
          <h6 class="mt-3"><?= Html::encode($label) ?></h6>
          <div class="row">
            <div class="col-md-6">
              <label class="form-label">Meno a priezvisko</label>
              <input class="form-control"
                     name="Guardian[<?= $idx ?>][name]"
                     placeholder="napr. Mária Nováková"
                     value="<?= $gval($idx,'name') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Vzťah k dieťaťu</label>
              <input class="form-control"
                     name="Guardian[<?= $idx ?>][relation]"
                     placeholder="Matka / Otec / Starý rodič / …"
                     value="<?= $gval($idx,'relation') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Telefón</label>
              <input class="form-control"
                     name="Guardian[<?= $idx ?>][phone]"
                     placeholder="+421…"
                     value="<?= $gval($idx,'phone') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">E-mail</label>
              <input class="form-control"
                     type="email"
                     name="Guardian[<?= $idx ?>][email]"
                     placeholder="napr. rodic@example.sk"
                     value="<?= $gval($idx,'email') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Ulica</label>
              <input class="form-control"
                     name="Guardian[<?= $idx ?>][street]"
                     placeholder="napr. Hlavná"
                     value="<?= $gval($idx,'street') ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Číslo domu</label>
              <input class="form-control"
                     name="Guardian[<?= $idx ?>][street_no]"
                     placeholder="napr. 12/A"
                     value="<?= $gval($idx,'street_no') ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">PSČ</label>
              <input class="form-control"
                     name="Guardian[<?= $idx ?>][zip]"
                     placeholder="napr. 811 01"
                     value="<?= $gval($idx,'zip') ?>">
            </div>
            <div class="col-md-2">
              <label class="form-label">Mesto</label>
              <input class="form-control"
                     name="Guardian[<?= $idx ?>][city]"
                     placeholder="napr. Bratislava"
                     value="<?= $gval($idx,'city') ?>">
            </div>
          </div>
        <?php endforeach; ?>

        <p class="text-muted mt-2">
          <small>
            Pre osoby mladšie ako 18 rokov musí mať každý zástupca vyplnené
            <b>meno</b> a <b>telefón alebo e-mail</b>.
          </small>
        </p>
      </div>
      <!-- ===================================================== -->

      <div class="row mt-3">
        <div class="col-md-6">
          <?= $form->field($model, 'street')
                   ->label('Ulica')
                   ->textInput([
                       'maxlength'=>255,
                       'placeholder'=>'napr. Hlavná'
                   ]) ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'street_no')
                   ->label('Číslo domu')
                   ->textInput([
                       'maxlength'=>50,
                       'placeholder'=>'napr. 12/A'
                   ]) ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'zip')
                   ->label('PSČ')
                   ->textInput([
                       'maxlength'=>50,
                       'placeholder'=>'napr. 811 01'
                   ]) ?>
        </div>
        <div class="col-md-2">
          <?= $form->field($model, 'city')
                   ->label('Mesto')
                   ->textInput([
                       'maxlength'=>255,
                       'placeholder'=>'napr. Bratislava'
                   ]) ?>
        </div>

        <div class="col-md-3">
          <?= $form->field($model, 'shirt_size')
                   ->label('Veľkosť trička')
                   ->dropDownList($shirtSizes, ['prompt'=>'– vyberte –']) ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'pants_size')
                   ->label('Veľkosť nohavíc')
                   ->dropDownList($pantsSizes, ['prompt'=>'– vyberte –']) ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'shoe_size')
                   ->label('Veľkosť obuvi (EU)')
                   ->dropDownList($shoeSizesEU, ['prompt'=>'EU – vyberte']) ?>
        </div>
        <div class="col-md-3">
          <?= $form->field($model, 'status')
                   ->label('Stav účtu')
                   ->dropDownList([10 => 'Aktívny', 0 => 'Neaktívny']) ?>
        </div>
      </div>

      <div class="form-group mt-3">
        <?= Html::submitButton('Uložiť', ['class' => 'btn btn-info text-white']) ?>
        <?= Html::a('Späť', ['users/index'], ['class' => 'btn btn-light']) ?>
      </div>

      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

<?php
$js = <<<JS
function ao_isMinor(dateStr){
  if(!dateStr) return false;
  var d = new Date(dateStr);
  if (isNaN(d.getTime())) {
    var s = dateStr.replace(/\\./g,'-').replace(/^(\\d{1,2})\\/(\\d{1,2})\\/(\\d{4})$/,'$3-$1-$2');
    d = new Date(s);
    if (isNaN(d.getTime())) return false;
  }
  var n = new Date();
  var age = n.getFullYear() - d.getFullYear();
  var m = n.getMonth() - d.getMonth();
  if (m < 0 || (m === 0 && n.getDate() < d.getDate())) age--;
  return age < 18;
}
function ao_toggleGuardians(){
  var el = document.getElementById('<?= $birthId ?>');
  var wrap = document.getElementById('guardians-wrap');
  if(!el || !wrap) return;
  wrap.style.display = ao_isMinor(el.value) ? '' : 'none';
}
document.getElementById('<?= $birthId ?>')?.addEventListener('change', ao_toggleGuardians);
document.addEventListener('DOMContentLoaded', ao_toggleGuardians);
JS;
$this->registerJs($js);
