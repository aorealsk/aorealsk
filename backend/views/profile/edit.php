<?php
/** @var $this yii\web\View */
/** @var $model backend\models\forms\UserProfileForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\StudyPlanType;

$this->title = 'Môj profil';

/** ---- Študijné plány ---- */
$types = StudyPlanType::find()
    ->orderBy(['name' => SORT_ASC])
    ->all();

$planTypes = ArrayHelper::map($types, 'id', 'name');

/** ---- Placeholder styling: tmavožlté a kurzívou ---- */
$css = <<<CSS
.form-control::placeholder {
    color: #d4a017;
    font-style: italic;
    opacity: 1;
}
CSS;
$this->registerCss($css);

/** @var \common\models\User $currentUser */
$currentUser = Yii::$app->user->identity;
?>
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-12 align-self-center">
      <h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
      <?php endif; ?>

      <?php
      // --- Dropdown options ---
      $shirtOptions = array_combine(['XS','S','M','L','XL','XXL'], ['XS','S','M','L','XL','XXL']);

      $pantsOptions = [];
      for ($w = 28; $w <= 38; $w++) {
          for ($inseam = 30; $inseam <= 32; $inseam++) {
              $key = "{$w}/{$inseam}";
              $pantsOptions[$key] = $key;
          }
      }

      $shoeOptions = [];
      for ($s = 35; $s <= 48; $s++) {
          $shoeOptions[(string)$s] = (string)$s;
      }
      ?>

      <?php $form = ActiveForm::begin(['id'=>'profile-form']); ?>
      <div class="card rounded-5 card-shadow mb-4">
        <div class="card-body">
          <h5 class="mb-3">Osobné údaje</h5>
          <div class="row">
            <div class="col-md-4">
              <?= $form->field($model,'username')
                       ->label('Prihlasovacie meno')
                       ->textInput(['maxlength'=>255,'placeholder'=>'napr. jnovak']) ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model,'email')
                       ->label('E-mail')
                       ->textInput(['maxlength'=>255,'placeholder'=>'napr. jan.novak@example.sk']) ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model,'password')
                       ->label('Nové heslo')
                       ->passwordInput(['placeholder'=>'Vyplňte iba ak chcete zmeniť heslo'])
                       ->hint('Vyplňte iba ak chcete zmeniť heslo.') ?>
            </div>

            <div class="col-md-4">
              <?= $form->field($model,'name_first')
                       ->label('Meno')
                       ->textInput(['maxlength'=>255,'placeholder'=>'napr. Ján']) ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model,'name_last')
                       ->label('Priezvisko')
                       ->textInput(['maxlength'=>255,'placeholder'=>'napr. Novák']) ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model,'birthdate')
                       ->label('Dátum narodenia')
                       ->input('date') ?>
            </div>

            <!-- TRIEDA + ŠTUDIJNÝ PLÁN cez form model ($model) -->
            <div class="col-md-4">
  <?= $form->field($model, 'userclassroom')
           ->label('Trieda')
           ->textInput([
               'maxlength'=>50,
               'placeholder'=>'napr. 9.A / 1-2: skupina',
               'pattern'     => '^[0-9A-Za-zÁÄČĎÉÍĹĽŇÓÔŔŠŤÚÝŽáäčďéíĺľňóôŕšťúýž ._\/:-]+$',
               'title'       => 'Povolené sú písmená, čísla a znaky . _ / - :',
           ]) ?>
            </div>
            <div class="col-md-4">
  <?= $form->field($model, 'study_plan_type_id')
           ->label('Študijný plán')
           ->dropDownList($planTypes, ['prompt' => '– bez študijného plánu –'])
           ->hint('Použije sa pri generovaní dokumentov (vyučovací plán).') ?>
            </div>

            <!-- OBLEČENIE -->
            <div class="col-md-4">
              <?= $form->field($model,'shirt_size')
                  ->label('Veľkosť trička')
                  ->dropDownList($shirtOptions, ['prompt' => '– vyberte veľkosť trička –']) ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model,'pants_size')
                  ->label('Veľkosť nohavíc')
                  ->dropDownList($pantsOptions, ['prompt' => '– vyberte veľkosť nohavíc –']) ?>
            </div>
            <div class="col-md-4">
              <?= $form->field($model,'shoe_size')
                  ->label('Veľkosť obuvi (EU)')
                  ->dropDownList($shoeOptions, ['prompt' => '– vyberte veľkosť obuvi –']) ?>
            </div>

            <div class="col-md-6">
              <?= $form->field($model,'street')
                       ->label('Ulica')
                       ->textInput(['maxlength'=>255,'placeholder'=>'napr. Hlavná']) ?>
            </div>
            <div class="col-md-2">
              <?= $form->field($model,'street_no')
                       ->label('Číslo domu')
                       ->textInput(['maxlength'=>50,'placeholder'=>'napr. 12/A']) ?>
            </div>
            <div class="col-md-2">
              <?= $form->field($model,'zip')
                       ->label('PSČ')
                       ->textInput(['maxlength'=>50,'placeholder'=>'napr. 811 01']) ?>
            </div>
            <div class="col-md-2">
              <?= $form->field($model,'city')
                       ->label('Mesto')
                       ->textInput(['maxlength'=>255,'placeholder'=>'napr. Bratislava']) ?>
            </div>

            <div class="col-md-4">
              <?= $form->field($model,'phone')
                       ->label('Telefón')
                       ->textInput(['maxlength'=>32,'placeholder'=>'+421 901 234 567']) ?>
            </div>
            <div class="col-md-8">
              <?= $form->field($model,'iban')
                       ->label('IBAN')
                       ->textInput(['maxlength'=>40,'placeholder'=>'napr. SK68 1100 0000 0029 1234 5678'])
                       ->hint('Príklad: SK68 1100 0000 0029 1234 5678') ?>
            </div>
          </div>
        </div>
      </div>

      <div class="card rounded-5 card-shadow mb-4">
        <div class="card-body">
          <h5 class="mb-3">
            Zákonný zástupca
            <small class="text-muted d-block">
              Ak máte menej ako 18 rokov, je potrebný aspoň 1 zástupca.
            </small>
          </h5>

          <?php for ($i = 0; $i < 2; $i++): ?>
            <div class="border rounded p-3 mb-3">
              <h6 class="mb-3">Zástupca #<?= $i + 1 ?></h6>
              <div class="row">
                <div class="col-md-4">
                  <?= $form->field($model, "guardians[$i][name]")
                    ->textInput(['maxlength' => 255,'placeholder'=>'napr. Mária Nováková'])
                    ->label('Meno a priezvisko') ?>
                </div>
                <div class="col-md-4">
                  <?= $form->field($model, "guardians[$i][phone]")
                    ->textInput(['maxlength' => 255,'placeholder'=>'+421…'])
                    ->label('Telefón') ?>
                </div>
                <div class="col-md-4">
                  <?= $form->field($model, "guardians[$i][email]")
                    ->textInput(['maxlength' => 255,'placeholder'=>'napr. rodic@example.sk'])
                    ->label('E-mail') ?>
                </div>
                <div class="col-md-5">
                  <?= $form->field($model, "guardians[$i][street]")
                    ->textInput(['maxlength' => 255,'placeholder'=>'napr. Hlavná'])
                    ->label('Ulica') ?>
                </div>
                <div class="col-md-2">
                  <?= $form->field($model, "guardians[$i][street_no]")
                    ->textInput(['maxlength' => 255,'placeholder'=>'napr. 12/A'])
                    ->label('Číslo') ?>
                </div>
                <div class="col-md-2">
                  <?= $form->field($model, "guardians[$i][zip]")
                    ->textInput(['maxlength' => 255,'placeholder'=>'napr. 811 01'])
                    ->label('PSČ') ?>
                </div>
                <div class="col-md-3">
                  <?= $form->field($model, "guardians[$i][city]")
                    ->textInput(['maxlength' => 255,'placeholder'=>'napr. Bratislava'])
                    ->label('Mesto') ?>
                </div>
              </div>
            </div>
          <?php endfor; ?>

          <div class="alert alert-warning mb-0" id="minor-hint" style="display:none;">
            Pod 18 rokov je potrebný aspoň 1 zástupca (vyplňte meno a telefón).
          </div>
        </div>
      </div>

      <div class="text-end">
        <?= Html::submitButton('Uložiť', ['class' => 'btn btn-primary text-white']) ?>
      </div>

      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

<?php
// minor hint toggler
$this->registerJs(<<<JS
function toggleMinorHint(){
  var el = document.getElementById('userprofileform-birthdate');
  if(!el){ return; }
  var v = el.value || '';
  var isMinor = false;
  if (v) {
    var d = new Date(v + 'T00:00:00');
    if (!isNaN(d)) {
      var today = new Date();
      var age = today.getFullYear() - d.getFullYear();
      var m = today.getMonth() - d.getMonth();
      if (m < 0 || (m === 0 && today.getDate() < d.getDate())) age--;
      isMinor = age < 18;
    }
  }
  var hint = document.getElementById('minor-hint');
  if (hint) hint.style.display = isMinor ? '' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleMinorHint);
document.getElementById('userprofileform-birthdate')?.addEventListener('change', toggleMinorHint);
JS);
