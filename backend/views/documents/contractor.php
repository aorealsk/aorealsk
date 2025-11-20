<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;

/** @var $this yii\web\View */
/** @var $model app\models\forms\ContractBatchForm */
/** @var $users app\models\User[] */
/** @var $partners app\models\Partner[] */
/** @var $hasPreview bool */
/** @var $contracts array */
/** @var $rendered array */

$this->title = 'Contractor';
$usersMap = ArrayHelper::map($users, 'id', fn($u) => "{$u->name_last} {$u->name_first} ({$u->username})");
$partnersMap = ArrayHelper::map($partners, 'id', 'partner_name');

?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="row">
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"><strong>Control Panel</strong></div>
      <div class="panel-body">
        <?php $form = ActiveForm::begin(['action' => ['autoshift/contractor'], 'method' => 'post', 'id' => 'contract-form']); ?>

        <?= $form->field($model, 'userIds')->checkboxList($usersMap, ['separator' => '<br>'])->label('Select users') ?>

        <?= $form->field($model, 'supervisorId')->dropDownList($usersMap, ['prompt' => 'Select supervisor']) ?>

        <?= $form->field($model, 'companyName')->textInput(['maxlength' => true, 'placeholder' => 'e.g., Your Company s.r.o.']) ?>

        <?= $form->field($model, 'partnerId')->dropDownList($partnersMap, ['prompt' => 'Select partner']) ?>

        <?= $form->field($model, 'shiftDate')->widget(DatePicker::class, [
            'dateFormat' => 'php:Y-m-d',
            'options' => ['class' => 'form-control'],
        ]) ?>

        <?= $form->field($model, 'studyPlanMonth')->textInput(['placeholder' => 'YYYY-MM']) ?>

        <div class="form-group">
          <button class="btn btn-primary" name="action" value="preview">Preview</button>
        </div>

        <?php if ($hasPreview): ?>
          <!-- Hidden container for edited HTML (one textarea per user) -->
          <div id="contracts-hidden"></div>
          <div class="form-group">
            <button class="btn btn-success" id="btn-generate" name="action" value="generate">Generate PDF</button>
          </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <?php if ($hasPreview): ?>
      <div class="panel panel-info">
        <div class="panel-heading"><strong>Editable Preview</strong> (click into any block and edit)</div>
        <div class="panel-body">

          <style>
            .contract-card { border: 1px solid #ddd; border-radius: 6px; padding: 12px; margin-bottom: 16px; }
            .contract-toolbar { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 12px; color: #666; }
            .editable { outline: 1px dashed #bbb; padding: 8px; min-height: 200px; background: #fff; }
            .page-break { page-break-after: always; }
            @media print { .contract-card { page-break-inside: avoid; } }
          </style>

          <?php foreach ($contracts as $payload):
            $user = $payload['user'];
            $uid = $user->id;
            $initialHtml = $rendered[$uid] ?? '<p>Missing content.</p>';
          ?>
            <div class="contract-card" data-user-id="<?= (int)$uid ?>">
              <div class="contract-toolbar">
                <div><strong><?= Html::encode($user->name_last . ' ' . $user->name_first) ?></strong> — <?= Html::encode($user->username) ?></div>
                <div>3 pages (page breaks preserved)</div>
              </div>
              <div class="editable" contenteditable="true" id="contract-edit-<?= (int)$uid ?>">
                <?= $initialHtml ?>
              </div>
            </div>
          <?php endforeach; ?>

          <p class="text-muted">Tip: You can change any wording, add notes, or adjust formatting before generating the PDF.</p>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info">Fill the control panel and click <strong>Preview</strong> to see/edit contracts here.</div>
    <?php endif; ?>
  </div>
</div>

<?php if ($hasPreview): ?>
<?php
$js = <<<JS
(function(){
  function ensureHiddenInputs(){
    const hidden = document.getElementById('contracts-hidden');
    if (!hidden) return;
    hidden.innerHTML = '';
    document.querySelectorAll('.contract-card').forEach(card => {
      const uid = card.getAttribute('data-user-id');
      const editable = card.querySelector('.editable');
      const ta = document.createElement('textarea');
      ta.name = 'contracts['+uid+']';
      ta.style.display = 'none';
      ta.value = editable.innerHTML;
      hidden.appendChild(ta);
    });
  }

  // Before submitting "Generate PDF", sync edited HTML into hidden textareas
  const btnGen = document.getElementById('btn-generate');
  if (btnGen) {
    btnGen.addEventListener('click', function(e){
      ensureHiddenInputs();
    });
  }

  // Optional: auto-sync when leaving an editable block
  document.querySelectorAll('.editable').forEach(ed => {
    ed.addEventListener('blur', function(){
      // no-op; we gather at submit, but you could live-sync if needed
    });
  });
})();
JS;
$this->registerJs($js);
?>
<?php endif; ?>
