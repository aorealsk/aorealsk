<?php
use backend\assets\RealAsset;
use yii\helpers\Url;
use yii\helpers\Html;


/**
 * @var string $title
 * @var $groups
 * @var $list
 * @var $modal_users
 * @var string|null $rosterHtml
 */

$this->title = $title;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/moment/moment.js',['depends'=>RealAsset::class]);

$this->registerJSFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.full.min.js',['depends'=>RealAsset::class]);
$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css',['depends'=>RealAsset::class]);
$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css',['depends'=>RealAsset::class]);

$this->registerJSFile('@web/assets/node_modules/bootstrap-daterangepicker/daterangepicker.js',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/bootstrap-daterangepicker/daterangepicker.css',['depends'=>RealAsset::class]);

/* ---------- Scoped fixes ONLY for the embedded roster block ---------- */
$this->registerCss(<<<CSS
#roster-card { position: relative; z-index: 1; }
#roster-card .table-responsive { overflow-x: auto; }

/* Hide page shells if they appear inside the embed */
#roster-card .app-header,
#roster-card .navbar,
#roster-card header,
#roster-card .topbar,
#roster-card .main-header,
#roster-card .left-sidebar,
#roster-card aside,
#roster-card nav { display: none !important; }

/* Neutralise wrappers brought by another layout */
#roster-card .page-wrapper,
#roster-card .container-fluid,
#roster-card .page-titles,
#roster-card .footer { padding:0 !important; margin:0 !important; background:transparent !important; border:0 !important; }

/* Disable any fixed-header clones inside the embed */
#roster-card .fixedHeader-floating,
#roster-card .fixedHeader-locked,
#roster-card .dtfh-floatingparent { display: none !important; }
CSS);
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-success text-white" type="button" id="addatt">
                <i class="fas fa-plus-circle"></i> <?= Yii::t('app','Pridať dochádzku') ?>
            </button>

            <a class="btn btn-outline-primary"
               href="<?= Url::to(['/user-attendance-admin/roster']) ?>">
                <i class="fas fa-users"></i>
                <?= Yii::t('app','Zamestnanci – všetky záznamy') ?>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="row m-t-15">
        <div class="col-12">
            <div class="card rounded-5 card-shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <label class="control-label"><?= Yii::t('app','Skupiny') ?></label>
                            <select id="groupSelect" class="form-select">
                                <option value=""><?= Yii::t('app','Zvoľte si skupinu'); ?></option>
                                <?php foreach($groups as $group){ ?>
                                    <option value="<?= $group['name'] ?>"><?= "{$group['name']} - {$group['description']}" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="control-label"><?= Yii::t('app', 'Študent') ?></label>
                            <select id="studentSelect" class="form-select"></select>
                        </div>
                        <div class="col-3">
                            <label class="control-label"><?= Yii::t('app','Obdobie') ?></label>
                            <div class='input-group'>
                                <input type='text' class="form-control daterange" id="dateSelect"/>
                                <span class="input-group-text">
                                    <span class="ti-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-3 d-flex align-items-end justify-content-end">
                            <button id="monitorRefresh" class="btn btn-outline-secondary">
                                <?= Yii::t('app','Obnoviť monitor') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitor table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-5 card-shadow">
                <div class="card-body">
                    <h5 class="card-title mb-3"><?= Yii::t('app','Monitor: Prihlásení do zmeny') ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm dattable" id="monitor01">
                            <thead>
                            <tr>
                                <th><?= Yii::t('app','Meno'); ?></th>
                                <th><?= Yii::t('app','Dátum'); ?></th>
                                <th><?= Yii::t('app','Začiatok'); ?></th>
                                <th><?= Yii::t('app','Koniec'); ?></th>
                                <th><?= Yii::t('app','Selfie'); ?></th>
                                <th><?= Yii::t('app','Stav'); ?></th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        <?= Yii::t('app','Zobrazuje záznamy s príchodom (Začiatok) v zvolenom období.'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php
// 🟢 Count today's completions
$todayCompletions = (new \yii\db\Query())
    ->from('student_test_log')
    ->where(['between', 'completed_at', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
    ->count();

// 🟢 Academic year range (for display)
$currentYear = (date('n') >= 9) ? date('Y') : date('Y') - 1;
$yearStart = $currentYear;
$yearEnd = $currentYear + 1;
?>

<h2>🎯 Reward Dashboard</h2>

<div class="alert alert-info mb-4">
    <strong>📅 Akademicky Rok:</strong> <?= $yearStart ?>/<?= $yearEnd ?><br>
    ✅ <strong><?= $todayCompletions ?></strong> Absolvovane testy dnes.
</div>

<!-- Filter Form -->
<form method="get" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
    <div>
        <label>Name:</label>
        <input type="text" name="name" value="<?= Html::encode($filterName) ?>" class="form-control" placeholder="Search by name">
    </div>
    <div>
        <label>Start date:</label>
        <input type="date" name="start" value="<?= Html::encode($filterStart) ?>" class="form-control">
    </div>
    <div>
        <label>End date:</label>
        <input type="date" name="end" value="<?= Html::encode($filterEnd) ?>" class="form-control">
    </div>
    <div class="form-check ms-3">
        <input type="checkbox" name="top5" id="top5" class="form-check-input" <?= $filterTop ? 'checked' : '' ?>>
        <label for="top5" class="form-check-label">Show Top 5</label>
    </div>
    <button type="submit" class="btn btn-primary ms-2">Filter</button>
    <a href="<?= Url::to(['user-attendance-admin/index']) ?>" class="btn btn-secondary ms-2">Reset</a>
</form>

<div class="table-responsive mb-4">
    <table class="table table-bordered table-striped align-middle shadow-sm">
        <thead class="table-light">
            <tr class="text-center">
                <th>#</th>
                <th>👤 Username</th>
                <th>📛 Meno</th>
                <th>🎟️ Stravné Lístky (aspon 6 hodin)</th>
                <th>🥇 Gold (Test)</th>
                <th>🏆 Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dashboardData)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No data found for the selected filters.</td></tr>
            <?php else: ?>
                <?php foreach ($dashboardData as $i => $data): ?>
                    <tr class="text-center">
                        <td><?= $i + 1 ?></td>
                        <td><?= Html::encode($data['username']) ?></td>
                        <td><?= Html::encode($data['name']) ?></td>
                        <td><span class="text-primary fw-bold"><?= (int)$data['tickets'] ?></span></td>
                        <td>
                            <?php if ($data['gold']): ?>
                                <span class="text-success fw-bold">✅ 1 Gold</span>
                                <small class="text-muted d-block">(Valid until 31.07.<?= $yearEnd ?>)</small>
                            <?php else: ?>
                                <span class="text-danger fw-bold">❌ Not completed</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= $data['total'] ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>



    <?php if (!empty($rosterHtml)): ?>
        <?php
        // --- Strip any layout wrappers coming from the roster page (server-side, safe for PHP 7.4) ---
        $rosterInner = $rosterHtml;
        $rosterInner = preg_replace('#<header\b[^>]*>.*?</header>#is', '', $rosterInner);
        $rosterInner = preg_replace('#<nav\b[^>]*>.*?</nav>#is', '', $rosterInner);
        $rosterInner = preg_replace('#<aside\b[^>]*>.*?</aside>#is', '', $rosterInner);
        $rosterInner = preg_replace('#<div\b[^>]*class="[^"]*left-sidebar[^"]*"[^>]*>.*?</div>#is', '', $rosterInner);
        // unwrap page-wrapper if the roster view includes it
        $rosterInner = preg_replace('#<div\b[^>]*class="[^"]*page-wrapper[^"]*"[^>]*>(.*)</div>#is', '$1', $rosterInner);
        ?>
        <div id="roster-card" class="row mt-4">
            <div class="col-12">
                <div class="card rounded-5 card-shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?= Yii::t('app', 'Dochádzka – všetky záznamy'); ?></h5>
                    </div>
                    <div class="card-body">
                        <?= $rosterInner ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Add attendance modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?= Yii::t('app','Nová dochádzka'); ?></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="control-label"><?= Yii::t('app','Meno'); ?></label>
                            <select id="uaid" class="form-control js-item">
                                <option value=""><?= Yii::t('app','Zvoľte meno'); ?></option>
                                <?php foreach($modal_users as $item){ echo "<option value='{$item['id']}'>{$item['meno']}</option>"; } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="control-label"><?= Yii::t('app','Dátum'); ?></label>
                            <input type="date" id="uadate" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="control-label"><?= Yii::t('app','Typ'); ?></label>
                            <select id="uatype" class="form-select">
                                <option value=""><?= Yii::t('app','Zvoľte typ dochádzky'); ?></option>
                                <?php for($i=1; $i <= 5; $i++) { ?>
                                    <option value="<?= $i ?>"><?= \common\models\users\UserAttendance::workType($i) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="control-label"><?= Yii::t('app','Čas príchodu'); ?></label>
                            <input type="time" id="intime" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="control-label"><?= Yii::t('app','Čas odchodu'); ?></label>
                            <input type="time" id="outtime" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="control-label"><?= Yii::t('app','Poznámka'); ?></label>
                            <textarea id="uanote" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('app','Zatvoriť'); ?></button>
                <button type="button" class="btn btn-primary text-white" id="uasave"><?= Yii::t('app','Uložiť'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php
$baseUrlJs = json_encode(Yii::$app->request->baseUrl);
$csrfKey   = Yii::$app->request->csrfParam;
$csrfVal   = Yii::$app->request->getCsrfToken();

$js  = "const BACKEND_BASE = {$baseUrlJs};\n";
$js .= "const CSRF_KEY = " . json_encode($csrfKey) . ";\n";
$js .= "const CSRF_VAL = " . json_encode($csrfVal) . ";\n";

$js .= <<<'JS'
/* ===== Admin attendance JS ===== */

// ---- helpers ----
function addCsrf(obj){ obj[CSRF_KEY] = CSRF_VAL; return obj; }
function readFilters(){
  const g = $('#groupSelect').val();
  const d = ($('#dateSelect').val() || '').split(' - ');
  let sd = '', ed = '';
  if (d.length === 2 && d[0] && d[1]) { sd = d[0].replaceAll('.', '-'); ed = d[1].replaceAll('.', '-'); }
  const uid = $('#studentSelect').val() || null;
  return { g, sd, ed, uid };
}
function buildPayload(g, sd, ed, uid){
  const p = { group: g || '', userid: uid || '' };
  if (sd && ed) { p.sdate = sd; p.edate = ed; }
  return addCsrf(p);
}

// ---- worked-time header/cells ----
function ensureWorktimeHeader(){
  const $ths = $('#monitor01 thead th');
  const exists = $ths.filter(function(){ return ($(this).text()||'').toLowerCase().indexOf('odpracovan')!==-1; }).length>0;
  if (exists) return;
  const $selfieTh = $ths.eq(4);
  ($selfieTh.length ? $('<th>Odpracované</th>').insertBefore($selfieTh) : $('<th>Odpracované</th>').appendTo('#monitor01 thead tr'));
}
function parseHMS(str){
  const m = String(str||'').match(/\b(\d{1,2}):(\d{2})(?::(\d{2}))?\b/); if(!m) return null;
  return (+m[1])*3600 + (+m[2])*60 + (m[3]?+m[3]:0);
}
function secsToHMS(t){
  t=Math.max(0,Math.round(t||0)); const h=Math.floor(t/3600), m=Math.floor((t%3600)/60), s=t%60;
  const pad=n=>String(n).padStart(2,'0'); return `${pad(h)}:${pad(m)}:${pad(s)}`;
}
function fillWorkedCells(){
  $('#monitor01 tbody tr').each(function(){
    const $tds = $(this).children('td');
    $tds.filter('.worktime-cell').remove();
    const start = parseHMS($tds.eq(2).text());
    const end   = parseHMS($tds.eq(3).text());
    const text = (start!=null && end!=null && end>=start) ? secsToHMS(end-start) : '';
    const $selfieTd = $tds.eq(4);
    ($selfieTd.length ? $('<td class="worktime-cell"></td>').text(text).insertBefore($selfieTd)
                      : $('<td class="worktime-cell"></td>').text(text).appendTo($(this)));
  });
}

// ---- selfie thumbnails ----
function injectMonitorThumbnails(){
  const selfieIdx = $('#monitor01 thead th').toArray()
    .findIndex(th => ($(th).text()||'').toLowerCase().includes('selfie'));

  $('#monitor01 tbody tr').each(function(){
    const $row = $(this);
    const $tds = $row.children('td');

    const hrefs = $row.find('a.selfie-link')
      .map(function(){ return $(this).attr('href') || ''; })
      .get()
      .filter(Boolean);

    const start = hrefs[0] || null;
    const end   = hrefs[1] || null;

    const $selfieCell = selfieIdx >= 0 ? $tds.eq(selfieIdx) : $tds.last();
    $selfieCell.empty();

    if (start) {
      $selfieCell.append(
        $('<a target="_blank" rel="noopener"></a>')
          .attr('href', start)
          .append($('<img class="selfie-thumb" alt="start">').attr('src', start))
      );
    }
    if (end) {
      $selfieCell.append(
        $('<a target="_blank" rel="noopener"></a>')
          .attr('href', end)
          .append($('<img class="selfie-thumb" alt="end">').attr('src', end))
      );
    }

    $tds.each(function(i){
      if (i === selfieIdx) return;
      if ($(this).find('a.selfie-link').length) $(this).remove();
    });
  });
}

// ---- loaders ----
function reloadMonitor(g, sd, ed, uid){
  $.ajax({
    url: BACKEND_BASE + '/user-attendance-admin/monitor',
    type: 'post', dataType: 'json',
    data: buildPayload(g, sd, ed, uid||null)
  }).done(function(res){
    if (res.status !== 'ok') { alert(res.message || 'Monitor load failed'); return; }
    if ($.fn.DataTable.isDataTable('#monitor01')) $('#monitor01').DataTable().destroy();
    $('#monitor01 tbody').html(res.tbody || '');
    ensureWorktimeHeader();
    fillWorkedCells();
    injectMonitorThumbnails();
    $('#monitor01').DataTable({ order: [] });
  });
}
function reloadMainTable(g, sd, ed, uid){
  $.ajax({
    url: BACKEND_BASE + '/user-attendance-admin/list-users',
    type: 'post', dataType: 'json',
    data: buildPayload(g, sd, ed, uid||null)
  }).done(function(res){
    if (res.status === 'error') { alert(res.message || 'Chyba'); return; }
    if ($.fn.DataTable.isDataTable('#ip01')) $('#ip01').DataTable().destroy();
    $('#ip01 tbody').html(res.tbody || '');
    $('#ip01').DataTable({ order: [] });
  });
}
function triggerMonitorReload(){
  const { g, sd, ed, uid } = readFilters();
  reloadMonitor(g, sd, ed, uid);
}

// ---- UI init & handlers ----
$('.js-item').select2({ theme: 'bootstrap', dropdownParent: $('#exampleModal .modal-body') });
$('#addatt').on('click', () => $('#exampleModal').modal('show'));

$('#uasave').on('click', function(){
  const body = addCsrf({
    uid: $('#uaid').val(),
    uadate: $('#uadate').val(),
    uatype: $('#uatype').val(),
    intime: $('#intime').val(),
    outtime: $('#outtime').val(),
    uanote: $('#uanote').val()
  });
  $.ajax({
    url: BACKEND_BASE + '/user-attendance-admin/save-attendance',
    type: 'post', dataType: 'json', data: body
  }).done(function(res){
    if (res.status === 'error') { alert(res.message || 'Chyba'); return; }
    if ($.fn.DataTable.isDataTable('#ip01')) $('#ip01').DataTable().destroy();
    $('#ip01 tbody').html(res.tbody || '');
    $('#ip01').DataTable({ order: [] });
    $('#exampleModal').modal('hide');
    $('#uaid,#uadate,#uatype,#intime,#outtime,#uanote').val('');
    $('.js-item').val('').trigger('change');
    triggerMonitorReload();
  });
});

$('.dattable').DataTable({ order: [] });

$('.daterange').daterangepicker({ autoUpdateInput: false, locale: { cancelLabel: 'Clear' } });
$('.daterange')
  .on('apply.daterangepicker', function(ev, picker){
    $(this).val(picker.startDate.format('YYYY.MM.DD') + ' - ' + picker.endDate.format('YYYY.MM.DD'));
    const { g } = readFilters();
    reloadMainTable(g, picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
    reloadMonitor(g, picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
  })
  .on('cancel.daterangepicker', function(){
    $(this).val('');
    triggerMonitorReload();
  });

$('#studentSelect').on('change', function(){
  const { g, sd, ed, uid } = readFilters();
  reloadMainTable(g, sd, ed, uid);
  triggerMonitorReload();
});

$('#groupSelect').on('change', function(){
  const g = $(this).val();
  $.ajax({
    url: BACKEND_BASE + '/user-attendance-admin/list-group-users',
    type: 'post', dataType: 'json',
    data: addCsrf({ group: g })
  }).done(function(res){
    if (res.status === 'error') { alert(res.message || 'Chyba'); return; }
    $('#studentSelect').empty();
    $('<option>', { value:'', selected:true }).prependTo('#studentSelect');
    $.each(res.students || [], function(_, item){
      $('#studentSelect').append($('<option></option>').attr('value', item.user_id).text(item.full_name));
    });
  });
  const { sd, ed } = readFilters();
  reloadMainTable(g, sd, ed);
  triggerMonitorReload();
});

// manual + auto refresh
$('#monitorRefresh').on('click', triggerMonitorReload);
setInterval(triggerMonitorReload, 60000);

// initial load
$(function(){ triggerMonitorReload(); });

// belt & suspenders: remove any header clones inside embed
$(function () {
  $('#roster-card .app-header, #roster-card .navbar, #roster-card header, #roster-card .topbar, #roster-card .main-header, #roster-card .left-sidebar').remove();
});
JS;

$this->registerJS($js);

$css = <<<'CSS'
.rounded-5 { border-radius: .5em!important; }
.card-shadow { box-shadow: lightgrey 3px 3px; }
.badge-soft-success { background: #e9f9ef; color: #166534; border-radius: .5rem; padding: .25rem .5rem; }
.badge-soft-warning { background: #fff7ed; color: #9a3412; border-radius: .5rem; padding: .25rem .5rem; }
.monitor-selfie { text-align: left; vertical-align: middle; }
.selfie-thumb { height: 48px; width: auto; border-radius: 6px; box-shadow: 0 0 0 1px rgba(0,0,0,.05); margin-right: 6px; }
.worktime-cell { white-space: nowrap; font-variant-numeric: tabular-nums; }
CSS;
$this->registerCSS($css);
