<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $model common\models\forms\ContractBatchForm|backend\models\forms\ContractBatchForm */
/** @var $users common\models\User[]|backend\models\User[] */
/** @var $partners common\models\Partner[]|backend\models\Partner[] */
/** @var $companies common\models\MyCompanies[]|backend\models\MyCompanies[]|null */
/** @var $hasPreview bool */
/** @var $contracts array */
/** @var $rendered array */

$this->title = 'Contractor';

$usersMap = ArrayHelper::map(
    $users,
    'id',
    fn($u) => trim("{$u->name_last} {$u->name_first} {$u->username}")
);
$partnersMap = ArrayHelper::map($partners, 'id', 'partner_name');

/** Companies → dropdown choices (binds to companyName) */
$companyChoices = [];
if (!empty($companies)) {
    foreach ($companies as $c) {
        $name = $c->company_name ?? '';
        $addrParts = array_filter([
            $c->address ?? '',
            trim(($c->zip ?? '') . ' ' . ($c->town ?? ''))
        ]);
        $label = trim($name . ' — ' . implode(', ', $addrParts), ' —');
        if ($label !== '') $companyChoices[$label] = $label;
    }
}

/** -------- History: scan archive dir for *.zip / *.pdf -------- */
$archiveDir = Yii::getAlias('@runtime/contracts_archive');
FileHelper::createDirectory($archiveDir); // idempotent
$historyFiles = [];
foreach (glob($archiveDir.'/*.{zip,pdf}', GLOB_BRACE) ?: [] as $p) {
    $historyFiles[] = [
        'path'  => $p,
        'name'  => basename($p),
        'size'  => filesize($p),
        'mtime' => filemtime($p),
        // safe token (filename only) for download route
        'token' => rtrim(strtr(base64_encode(basename($p)), '+/', '-_'), '='),
    ];
}
// newest first
usort($historyFiles, fn($a,$b) => $b['mtime'] <=> $a['mtime']);
?>
<h1 class="page-title"><?= Html::encode($this->title) ?></h1>

<div class="row contractor-grid">
  <div class="col-md-4">
    <div class="card">
      <div class="card__head">
        <div class="card__title">Dual Generacia</div>
      </div>
      <div class="card__body">
        <?php $form = ActiveForm::begin([
          'action' => ['autoshift/contractor'],
          'method' => 'post',
          'id'     => 'contract-form'
        ]); ?>

        <?= $form->errorSummary($model, ['class' => 'alert alert-danger card__alert']) ?>

        <!-- USERS -->
        <div class="form-group">
          <label class="label">Žiaci (users)</label>
          <input type="text" id="filter-users" class="input" placeholder="Search users…">
          <div id="users-scroll" class="scroll-box">
            <?= $form->field($model, 'userIds', ['template' => "{input}\n{error}"])
              ->checkboxList($usersMap, [
                  'separator'   => '<br>',
                  'itemOptions' => ['class' => 'user-item custom-check'],
                  'unselect'    => null,
              ]); ?>
          </div>
          <small id="users-count" class="muted"></small>
        </div>

        <!-- SUPERVISOR (single select via checkboxes) -->
        <div class="form-group">
          <label class="label">Inštruktor (supervisor)</label>
          <input type="text" id="filter-supervisor" class="input" placeholder="Search supervisor…">
          <div id="supervisor-scroll" class="scroll-box">
            <?php $currentSup = (string)($model->supervisorId ?? '');
            foreach ($usersMap as $id => $label): ?>
              <div class="checkbox custom-check">
                <label>
                  <input type="checkbox"
                         class="supervisor-cb"
                         value="<?= Html::encode($id) ?>"
                         <?= ((string)$id === $currentSup) ? 'checked' : '' ?>>
                  <span><?= Html::encode($label) ?></span>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
          <?= Html::hiddenInput(Html::getInputName($model, 'supervisorId'), $currentSup, ['id' => 'supervisor-id-hidden']) ?>
          <?= Html::error($model, 'supervisorId', ['class' => 'help-block help-block-error']) ?>
        </div>

        <!-- COMPANY -->
        <div class="form-group">
          <label class="label">Organizácia (Company)</label>
          <?= $form->field($model, 'companyName')
               ->dropDownList($companyChoices, ['prompt' => '— Select company —', 'class' => 'select'])
               ->label(false) ?>
        </div>

        <!-- PARTNER -->
        <div class="form-group">
          <label class="label">Partner</label>
          <?= $form->field($model, 'partnerId')
               ->dropDownList($partnersMap, ['prompt' => '— Select partner —', 'class' => 'select'])
               ->label(false) ?>
        </div>

        <!-- DATE + MONTH -->
        <div class="form-row">
          <div class="form-col">
            <label class="label">Shift Date</label>
            <?= $form->field($model, 'shiftDate')->input('date', ['class' => 'input'])->label(false) ?>
          </div>
          <div class="form-col">
            <label class="label">Study Plan Month (YYYY-MM)</label>
            <?= $form->field($model, 'studyPlanMonth')->input('month', ['class' => 'input'])->hint('Format: YYYY-MM')->label(false) ?>
          </div>
        </div>

        <div class="sticky-actions">
          <button type="submit" class="btn btn--primary" name="action" value="preview">Preview</button>
          <?php if ($hasPreview): ?>
            <div id="contracts-hidden"></div>
            <button type="submit" class="btn btn--success" id="btn-generate" name="action" value="generate">Generate PDF</button>
          <?php endif; ?>
        </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>

    <!-- HISTORY card -->
    <div class="card" style="margin-top:14px;">
      <div class="card__head">
        <div class="card__title">History</div>
        <div class="card__sub muted">Previously generated ZIP/PDF</div>
      </div>
      <div class="card__body">
        <input type="text" id="history-filter" class="input" placeholder="Search files…">
        <div id="history-list" class="history-list">
          <?php if (empty($historyFiles)): ?>
            <div class="muted" style="padding:8px 0;">No files yet.</div>
          <?php else: ?>
            <?php foreach ($historyFiles as $f): ?>
              <div class="history-item" data-name="<?= Html::encode(mb_strtolower($f['name'])) ?>">
                <div class="history-meta">
                  <div class="history-name"><?= Html::encode($f['name']) ?></div>
                  <div class="history-sub muted">
                    <?= Yii::$app->formatter->asDatetime($f['mtime']) ?>
                    · <?= Yii::$app->formatter->asShortSize($f['size']) ?>
                  </div>
                </div>
                <div class="history-actions">
                  <a class="btn btn--primary btn--sm"
                     href="<?= Url::to(['autoshift/contract-download', 't' => $f['token']]) ?>">
                    Download
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-8" id="preview-anchor">
    <?php if ($hasPreview): ?>
      <div class="card">
        <div class="card__head">
          <div class="card__title">Editable Preview</div>
          <div class="card__sub muted">Click any block to edit before export</div>
        </div>
        <div class="card__body">
          <style>
            .contract-card { border: 1px solid #e8eaee; border-radius: 10px; padding: 12px; margin-bottom: 16px; box-shadow: 0 1px 2px rgba(0,0,0,.04); background: #fff; }
            .contract-toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom: 8px; font-size: 12px; color: #667085; }
            .editable { outline: 1px dashed #cbd5e1; padding: 10px; min-height: 200px; background: #fff; border-radius: 8px; }
            .page-break { page-break-after: always; }
            @media print { .contract-card { page-break-inside: avoid; } }
          </style>

          <?php foreach ($contracts as $payload):
            $user = $payload['user'];
            $uid  = $user->id;
            $initialHtml = $rendered[$uid] ?? '<p>Missing content.</p>';
          ?>
            <div class="contract-card" data-user-id="<?= (int)$uid ?>">
              <div class="contract-toolbar">
                <div><strong><?= Html::encode($user->name_last . ' ' . $user->name_first) ?></strong> <span class="muted">— <?= Html::encode($user->username) ?></span></div>
                <div class="muted">3 pages · page breaks preserved</div>
              </div>
              <div class="editable" contenteditable="true" id="contract-edit-<?= (int)$uid ?>">
                <?= $initialHtml ?>
              </div>
            </div>
          <?php endforeach; ?>

          <p class="muted">Tip: Adjust wording/formatting here — your edits are captured into the PDF.</p>
        </div>
      </div>
    <?php else: ?>
      <div class="card card--empty">
        <div class="card__body centered">
          <h4 class="muted" style="margin:0 0 6px;">Ready to generate contracts</h4>
          <p class="muted" style="margin:0;">Fill the panel on the left and click <strong>Preview</strong>.</p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
// ---------- Design (CSS) ----------
$this->registerCss(<<<CSS
/* layout */
.page-title{margin:8px 0 14px;font-weight:600;color:#0f172a;}
.contractor-grid{row-gap:16px}

/* card */
.card{background:#fff;border:1px solid #e8eaee;border-radius:12px;box-shadow:0 1px 2px rgba(16,24,40,.04);}
.card__head{padding:14px 16px;border-bottom:1px solid #eef1f6;}
.card__body{padding:16px;}
.card__title{font-weight:600;color:#111827}
.card__sub{font-size:.9rem}
.card--empty{border-style:dashed}

/* form */
.label{display:block;font-size:.92rem;color:#334155;margin:8px 0 6px;font-weight:600}
.input,.select{width:100%;height:36px;border:1px solid #cfd8e3;border-radius:8px;padding:6px 10px;background:#ffffff;}
.input:focus,.select:focus{outline:none;border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,.15)}
.form-group{margin-bottom:12px}
.form-row{display:flex;gap:10px}
.form-row .form-col{flex:1}

/* scroll boxes */
.scroll-box{max-height:260px;overflow:auto;padding:10px;border:1px solid #e6e9f2;border-radius:10px;background:#fafbff}
.scroll-box .checkbox{margin:4px 0}
.scroll-box::-webkit-scrollbar{width:10px}
.scroll-box::-webkit-scrollbar-thumb{background:#cfd8e3;border-radius:8px}

/* custom checkboxes */
.custom-check input[type="checkbox"]{margin-right:8px;transform:scale(1.05)}
.custom-check label{display:flex;align-items:center;gap:6px;color:#111827}

/* helper text + alerts */
.muted{color:#6b7280}
.card__alert{border-radius:8px}

/* actions */
.sticky-actions{position:sticky;bottom:0;background:#f8fafc;padding:10px;border-top:1px solid #eef1f6;border-bottom-left-radius:12px;border-bottom-right-radius:12px;display:flex;gap:8px;justify-content:flex-end;}

/* buttons */
.btn{border:none;border-radius:8px;padding:8px 14px;font-weight:600;letter-spacing:.2px}
.btn--primary{background:#2563eb;color:#fff}
.btn--primary:hover{background:#1d4ed8}
.btn--success{background:#059669;color:#fff}
.btn--success:hover{background:#047857}

/* history */
.history-list{display:flex;flex-direction:column;gap:8px;margin-top:10px;}
.history-item{display:flex;align-items:center;justify-content:space-between;border:1px solid #edf0f7;border-radius:10px;padding:10px;background:#fff;}
.history-name{font-weight:600;color:#111827}
.btn--sm{padding:6px 10px;font-size:.9rem}

/* preview helpers */
.centered{text-align:center}
CSS);

// ---------- Search + selection (fixed) ----------
$this->registerJs(<<<'JS'
(function(){
  // normalize text
  function norm(s){
    s = (s || '').toLowerCase();
    try { s = s.normalize('NFD').replace(/[\u0300-\u036f]/g,''); } catch(e){}
    s = s.replace(/[()]/g,' ');
    return s.replace(/\s+/g,' ').trim();
  }
  function filterCheckboxList(containerSelector, itemSelector, query){
    var container = document.querySelector(containerSelector);
    if (!container) return;
    var q = norm(query);
    var items = container.querySelectorAll(itemSelector);
    for (var i=0;i<items.length;i++){
      var el = items[i];
      var txt = norm(el.textContent||'');
      el.style.display = (!q || txt.indexOf(q)!==-1) ? '' : 'none';
    }
  }

  // USERS
  var usersBox   = document.getElementById('users-scroll');
  var userFilter = document.getElementById('filter-users');
  var usersCount = document.getElementById('users-count');
  function updateUsersCount(){
    if (!usersBox) return;
    var n = usersBox.querySelectorAll('input[type="checkbox"]:checked').length;
    if (usersCount) usersCount.textContent = n ? (n + ' selected') : '';
  }
  if (userFilter){
    userFilter.addEventListener('input', function(){
      filterCheckboxList('#users-scroll', '.user-item', this.value);
    });
  }
  if (usersBox){ usersBox.addEventListener('change', updateUsersCount); }
  filterCheckboxList('#users-scroll', '.user-item', userFilter ? userFilter.value : '');
  updateUsersCount();

  // SUPERVISOR
  var supBox    = document.getElementById('supervisor-scroll');
  var supFilter = document.getElementById('filter-supervisor');
  var hiddenSup = document.getElementById('supervisor-id-hidden');
  if (supFilter){
    supFilter.addEventListener('input', function(){
      filterCheckboxList('#supervisor-scroll', '.checkbox', this.value);
    });
  }
  if (supBox){
    supBox.addEventListener('change', function(e){
      var t = e.target || e.srcElement;
      if (t && t.classList && t.classList.contains('supervisor-cb')){
        var cbs = supBox.querySelectorAll('.supervisor-cb');
        for (var i=0;i<cbs.length;i++){ if (cbs[i]!==t) cbs[i].checked = false; }
        if (hiddenSup) hiddenSup.value = t.checked ? t.value : '';
      }
    });
  }
  filterCheckboxList('#supervisor-scroll', '.checkbox', supFilter ? supFilter.value : '');

  // HISTORY filter
  var hFilter = document.getElementById('history-filter');
  var hList   = document.getElementById('history-list');
  if (hFilter && hList){
    hFilter.addEventListener('input', function(){
      var q = norm(this.value);
      var items = hList.querySelectorAll('.history-item');
      for (var i=0;i<items.length;i++){
        var n = items[i].getAttribute('data-name') || '';
        items[i].style.display = (!q || n.indexOf(q)!==-1) ? '' : 'none';
      }
    });
  }
})();
JS
);
?>

<?php if ($hasPreview) :
$this->registerJs(<<<'JS'
(function(){
  function ensureHiddenInputs(){
    var hidden = document.getElementById('contracts-hidden');
    if (!hidden) return;
    hidden.innerHTML = '';
    var cards = document.querySelectorAll('.contract-card');
    for (var i=0; i<cards.length; i++){
      var card = cards[i];
      var uid = card.getAttribute('data-user-id');
      var editable = card.querySelector('.editable');
      var ta = document.createElement('textarea');
      ta.name = 'contracts['+uid+']';
      ta.style.display = 'none';
      ta.value = editable ? editable.innerHTML : '';
      hidden.appendChild(ta);
    }
  }
  var btnGen = document.getElementById('btn-generate');
  if (btnGen) btnGen.addEventListener('click', ensureHiddenInputs);

  if (location.hash !== '#preview') {
    var anchor = document.getElementById('preview-anchor');
    if (anchor) anchor.scrollIntoView({behavior:'smooth', block:'start'});
  }
})();
JS
);
endif; ?>
