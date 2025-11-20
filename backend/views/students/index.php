<?php
use backend\assets\StudentIndexAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\ScheduleBreak;
use common\models\User;
/**
 * @var $this yii\web\View
 * @var $students \common\models\schools\Students[]
 */

$this->title = "Študenti";
StudentIndexAsset::register($this);
?>

<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-4 align-self-center">
      <h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="col-md-8 align-self-center text-right"></div>
  </div>

<!-- Students table -->
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-sm dattable">
        <thead>
        <tr>
          <th style="width:20px;">#</th>
          <th>ID</th>
          <th>Meno/Priezvisko</th>
          <th>Kontakt</th>
          <th>Škola/odbor</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $student): ?>
          <tr class="student-main-row">
            <td>
              <a href="#" class="details-control" data-id="<?= $student->id ?>" title="Podrobnosti">
                <i class="fas fa-plus-square"></i>
              </a>
            </td>
            <td><?= (int)$student->id ?></td>
            <td><?= Html::encode($student->studentName) ?></td>
            <td>
              <p class="m-b-0">
                <i class="mdi mdi-phone"></i>
                <a href="tel:<?= Html::encode($student->formattedPhone) ?>"><?= Html::encode($student->formattedPhone) ?></a>
              </p>
              <p class="m-b-0">
                <i class="mdi mdi-email"></i>
                <a href="mailto:<?= Html::encode($student->email) ?>"><?= Html::encode($student->email) ?></a>
              </p>
            </td>
            <td>
              <?= $student->school ? Html::encode($student->school->description) : '-' ?><br>
              <?= $student->studyField ? Html::encode($student->studyField->code . ' ' . $student->studyField->name) : '-' ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php
// ---------- Alertness Test results (CSV) ----------
$csvCandidates = [
    Yii::getAlias('@webroot') . '/data/test_results.csv',            // e.g. /var/www/site/web/data/...
    dirname(Yii::getAlias('@webroot')) . '/data/test_results.csv',  // parent of this webroot
    Yii::getAlias('@app') . '/../../web/data/test_results.csv',     // backend/app -> ../../web/data
];

$csv = null;
foreach ($csvCandidates as $p) {
    if (is_file($p)) { $csv = $p; break; }
}

$rows = [];
if ($csv && ($fp = fopen($csv, 'r')) !== false) {
    $header = fgetcsv($fp); // skip header
    while (($data = fgetcsv($fp)) !== false) {
        // expected: timestamp,user_id,score,total,ip,user_agent,details_json
        $rows[] = [
            'ts'      => (int)($data[0] ?? 0),
            'user_id' => (int)($data[1] ?? 0),
            'score'   => (int)($data[2] ?? 0),
            'total'   => (int)($data[3] ?? 0),
            'ip'      => (string)($data[4] ?? ''),
            'ua'      => (string)($data[5] ?? ''),
            'details' => (string)($data[6] ?? ''),
        ];
    }
    fclose($fp);
}

// newest first
usort($rows, static function($a, $b){ return $b['ts'] <=> $a['ts']; });

// Build user map (schema-aware: only selects columns that exist)
$userMap = [];
$userIds = array_values(array_unique(array_column($rows, 'user_id')));
if ($userIds) {
    $schema = User::getTableSchema();
    $selectCols = ['id'];
    foreach (['username','first_name','last_name','name','email'] as $c) {
        if ($schema->getColumn($c)) { $selectCols[] = $c; }
    }

    $users = User::find()
        ->select($selectCols)
        ->where(['id' => $userIds])
        ->indexBy('id')
        ->asArray()
        ->all();

    foreach ($users as $u) {
        // Prefer first+last, else single "name", else username, else email
        $display = '';
        if (isset($u['first_name']) || isset($u['last_name'])) {
            $display = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
        }
        if ($display === '' && isset($u['name']))     $display = $u['name'];
        if ($display === '' && isset($u['username'])) $display = $u['username'];
        if ($display === '' && isset($u['email']))    $display = $u['email'];
        if ($display === '')                          $display = 'User #'.$u['id'];

        $userMap[$u['id']] = [
            'name'  => $display,
            'email' => $u['email'] ?? null,
        ];
    }
}
?>

<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title mb-3">Výsledky testu bdelosti</h5>

    <?php if (!$csv): ?>
      <div class="alert alert-warning mb-0">
        Nenájdený súbor s výsledkami. Hľadal som:
        <code><?= Html::encode(implode(', ', $csvCandidates)) ?></code>
      </div>
    <?php elseif (!$rows): ?>
      <div class="alert alert-info mb-0">Zatiaľ žiadne výsledky.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0">
          <thead>
            <tr>
              <th>Čas</th>
              <th>Študent</th>
              <th>Body</th>
              <th>%</th>
              <th>IP</th>
              <th>Detail</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r):
              $user  = $userMap[$r['user_id']] ?? null;
              $name  = $user['name']  ?? ('User #'.$r['user_id']);
              $email = $user['email'] ?? null;
              $when  = $r['ts'] ? Yii::$app->formatter->asDatetime($r['ts']) : '-';
              $pct   = $r['total'] > 0 ? round(($r['score'] / max(1,$r['total'])) * 100) : 0;
              $cid   = 'd'. $r['ts'] . '_' . $r['user_id'];
          ?>
            <tr>
              <td><?= Html::encode($when) ?></td>
              <td>
                <?= Html::encode($name) ?>
                <?php if ($email): ?>
                  <div class="text-muted small"><?= Html::encode($email) ?></div>
                <?php endif; ?>
              </td>
              <td><strong><?= (int)$r['score'] ?>/<?= (int)$r['total'] ?></strong></td>
              <td><?= (int)$pct ?>%</td>
              <td><code><?= Html::encode($r['ip']) ?></code></td>
              <td>
                <?php if (!empty($r['details'])): ?>
                  <!-- If your backend uses Bootstrap 4, use data-toggle/data-target -->
                  <button class="btn btn-sm btn-outline-secondary"
                          type="button" data-toggle="collapse"
                          data-target="#<?= $cid ?>">
                    Zobraziť
                  </button>
                  <div class="collapse mt-2" id="<?= $cid ?>">
                    <pre class="mb-0 small bg-light p-2" style="white-space: pre-wrap;"><?= Html::encode($r['details']) ?></pre>
                  </div>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

  <?php
  // ===== Mentors / Partners block (teachers, supervisors, business partners) =====
  $mentorsByRole = $mentorsByRole ?? (Yii::$app->view->params['mentorsByRole'] ?? [
      'teacher' => [], 'supervisor' => [], 'business_partner' => []
  ]);
  $labels = [
      'teacher' => 'Učitelia',
      'supervisor' => 'Supervízori',
      'business_partner' => 'Biznis partneri',
  ];
  ?>
  <div class="card mt-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Mentori a partneri</h5>
        <div class="btn-group">
          <a class="btn btn-sm btn-outline-primary" href="<?= Url::to(['/mentor/profile']) ?>">Môj profil</a>
          <a class="btn btn-sm btn-outline-secondary" href="<?= Url::to(['/mentor/teams']) ?>">Moje tímy</a>
        </div>
      </div>

      <div class="row">
        <?php foreach (['teacher','supervisor','business_partner'] as $role): ?>
          <div class="col-md-4 mb-3">
            <h6 class="text-muted mb-2">
              <?= Html::encode($labels[$role]) ?>
              <span class="badge badge-light"><?= count($mentorsByRole[$role]) ?></span>
            </h6>

            <?php if (empty($mentorsByRole[$role])): ?>
              <p class="text-muted mb-0">Žiadne záznamy.</p>
            <?php else: ?>
              <ul class="list-unstyled mb-0">
                <?php foreach ($mentorsByRole[$role] as $m): ?>
                  <?php
                    // Robust display name + email with safe fallbacks
                    $name  = (method_exists($m, 'getDisplayName') && $m->displayName)
                              ? $m->displayName
                              : (($m->user->fullName ?? $m->user->name ?? $m->user->username ?? ('#'.$m->user_id)));
                    $email = (property_exists($m, 'email') && $m->email) ? $m->email : ($m->user->email ?? null);
                  ?>
                  <li class="mb-2">
                    <strong><?= Html::encode($name) ?></strong>
                    <?php if (!empty($m->org_name)): ?>
                      <div class="small text-muted"><?= Html::encode($m->org_name) ?></div>
                    <?php endif; ?>

                    <div class="small">
                      <?php if (!empty($m->phone)): ?>
                        <i class="mdi mdi-phone"></i>
                        <a href="tel:<?= Html::encode($m->phone) ?>"><?= Html::encode($m->phone) ?></a>
                      <?php endif; ?>
                      <?php if ($email): ?>
                        <?php if (!empty($m->phone)): ?>&nbsp;·&nbsp;<?php endif; ?>
                        <i class="mdi mdi-email"></i>
                        <?= Html::mailto(Html::encode($email), $email) ?>
                      <?php endif; ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Schedule breaks (Add/Edit/Delete + Export CSV) -->
  <?php
  $tableExists    = Yii::$app->db->schema->getTableSchema('{{%schedule_break}}', true) !== null;
  $scheduleBreaks = $tableExists ? ScheduleBreak::find()->orderBy(['from_time' => SORT_ASC])->all() : [];
  ?>

  <div class="card mt-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Rozvrh — prestávky medzi hodinami</h5>
        <div class="btn-group">
          <a class="btn btn-sm btn-outline-secondary"
             href="<?= Url::to(['schedule-break/export-csv']) ?>"
             title="Export CSV (Excel-friendly ; separator)">
            Export CSV
          </a>
          <?= Html::a('Download source .xlsx', ['students/download-schedule'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
          <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#scheduleModal" id="btn-open-create">
            + Add new schedule
          </button>
        </div>
      </div>

      <?php if (!$tableExists): ?>
        <div class="alert alert-warning mb-3">
          Tabuľka rozvrhu ešte neexistuje. Spustite migrácie: <code>php yii migrate</code>.
        </div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0" id="schedule-table">
          <caption class="sr-only">Názov, časy a dĺžka prestávok</caption>
          <thead class="thead-light">
          <tr>
            <th style="width:200px;">Názov</th>
            <th style="width:120px;">Od</th>
            <th style="width:120px;">Do</th>
            <th style="width:140px;">Prestávka (min)</th>
            <th style="width:120px;">Akcia</th>
          </tr>
          </thead>
          <tbody id="schedule-tbody">
          <?php foreach ($scheduleBreaks as $row): ?>
            <tr
              data-id="<?= (int)$row->id ?>"
              data-title="<?= Html::encode($row->title) ?>"
              data-from="<?= Html::encode($row->from_time) ?>"
              data-to="<?= Html::encode($row->to_time) ?>"
              data-break="<?= $row->break_min !== null ? (int)$row->break_min : '' ?>"
            >
              <td class="td-title"><?= Html::encode($row->title) ?></td>
              <td class="td-from"><?= Html::encode($row->from_time) ?></td>
              <td class="td-to"><?= Html::encode($row->to_time) ?></td>
              <td class="td-break"><?= $row->break_min !== null ? (int)$row->break_min : '-' ?></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary schedule-edit" title="Upraviť" data-id="<?= (int)$row->id ?>">
                  <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger schedule-del" title="Zmazať" data-id="<?= (int)$row->id ?>">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal (reused for Add & Edit) -->
<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="schedule-form" method="post" action="<?= Url::to(['schedule-break/create']) ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="scheduleModalLabel">Add new schedule</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
          <input type="hidden" id="sched_id" name="id"><!-- present only for edit -->
          <div class="form-group">
            <label for="title">Názov</label>
            <input type="text" class="form-control" id="title" name="title" required placeholder="napr. Ranná prestávka">
          </div>
          <div class="form-group">
            <label for="from_time">Od</label>
            <input type="time" class="form-control" id="from_time" name="from_time" required>
          </div>
          <div class="form-group">
            <label for="to_time">Do</label>
            <input type="time" class="form-control" id="to_time" name="to_time" required>
          </div>
          <div class="form-group">
            <label for="break_min">Prestávka (min)</label>
            <input type="number" class="form-control" id="break_min" name="break_min" min="0" max="300" placeholder="napr. 5">
          </div>
          <p class="text-muted small mb-0">Uloženie prebehne cez AJAX a tabuľka sa hneď aktualizuje.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="schedule-save-btn">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
$createUrl = Url::to(['schedule-break/create']);
$updateUrl = Url::to(['schedule-break/update']);
$delUrl    = Url::to(['schedule-break/delete']);
$csrfParam = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$js = <<<JS
(function(){
  var tbody = document.getElementById('schedule-tbody');
  var form  = document.getElementById('schedule-form');
  var modal = $('#scheduleModal');
  var title = document.getElementById('title');
  var fromI = document.getElementById('from_time');
  var toI   = document.getElementById('to_time');
  var brk   = document.getElementById('break_min');
  var idI   = document.getElementById('sched_id');
  var saveBtn = document.getElementById('schedule-save-btn');
  var label   = document.getElementById('scheduleModalLabel');

  // Open modal in CREATE mode
  document.getElementById('btn-open-create').addEventListener('click', function(){
    form.action = '$createUrl';
    label.textContent = 'Pridať';
    saveBtn.textContent = 'Uložiť';
    idI.value = '';
    form.reset();
  });

  // Delegated: open modal in EDIT mode
  if (tbody) {
    tbody.addEventListener('click', function(e){
      var btn = e.target.closest('.schedule-edit');
      if (!btn) return;

      var tr = btn.closest('tr');
      if (!tr) return;

      // Fill form with row dataset
      idI.value   = tr.getAttribute('data-id') || '';
      title.value = tr.getAttribute('data-title') || '';
      fromI.value = tr.getAttribute('data-from') || '';
      toI.value   = tr.getAttribute('data-to') || '';
      brk.value   = tr.getAttribute('data-break') || '';

      form.action = '$updateUrl';
      label.textContent = 'Edit schedule';
      saveBtn.textContent = 'Update';

      modal.modal('show'); // Bootstrap modal
    });

    // Delegated: DELETE
    tbody.addEventListener('click', function(e){
      var btn = e.target.closest('.schedule-del');
      if (!btn) return;
      var id = btn.getAttribute('data-id');
      if (!id) return;
      if (!confirm('Naozaj chcete zmazať tento záznam?')) return;

      var fd = new FormData();
      fd.append('id', id);
      fd.append('$csrfParam', '$csrfToken');

      fetch('$delUrl', {
        method: 'POST',
        credentials: 'same-origin',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(function(r){ return r.json(); })
      .then(function(data){
        if (!data.ok) { alert('Delete failed'); return; }
        var tr = btn.closest('tr');
        if (tr) tr.remove();
      })
      .catch(function(err){ console.error(err); alert('Network error.'); });
    });
  }

  // Submit (CREATE or UPDATE depending on action)
  form.addEventListener('submit', function(e){
    e.preventDefault();
    var fd = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      credentials: 'same-origin',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
      if (!data.ok) {
        alert('Validation error: ' + JSON.stringify(data.errors || data.error));
        return;
      }
      var row = data.row;
      var existing = tbody.querySelector('tr[data-id="'+row.id+'"]');

      if (existing) {
        // UPDATE in-place
        existing.querySelector('.td-title').textContent = row.title || '';
        existing.querySelector('.td-from').textContent  = row.from || '';
        existing.querySelector('.td-to').textContent    = row.to || '';
        existing.querySelector('.td-break').textContent = (row.break !== null && row.break !== '') ? row.break : '-';

        // refresh dataset for future edits
        existing.setAttribute('data-title', row.title || '');
        existing.setAttribute('data-from',  row.from  || '');
        existing.setAttribute('data-to',    row.to    || '');
        existing.setAttribute('data-break', (row.break !== null && row.break !== '') ? row.break : '');

      } else {
        // CREATE: append new row with both buttons
        var tr = document.createElement('tr');
        tr.setAttribute('data-id', row.id);
        tr.setAttribute('data-title', row.title || '');
        tr.setAttribute('data-from',  row.from  || '');
        tr.setAttribute('data-to',    row.to    || '');
        tr.setAttribute('data-break', (row.break !== null && row.break !== '') ? row.break : '');

        tr.innerHTML =
          '<td class="td-title">' + (row.title ? row.title : '') + '</td>' +
          '<td class="td-from">'  + (row.from  ? row.from  : '') + '</td>' +
          '<td class="td-to">'    + (row.to    ? row.to    : '') + '</td>' +
          '<td class="td-break">' + ((row.break !== null && row.break !== '') ? row.break : '-') + '</td>' +
          '<td class="text-center">' +
            '<button type="button" class="btn btn-sm btn-outline-secondary schedule-edit" title="Upraviť" data-id="' + row.id + '">' +
              '<i class="fas fa-pencil-alt"></i>' +
            '</button> ' +
            '<button type="button" class="btn btn-sm btn-outline-danger schedule-del" title="Zmazať" data-id="' + row.id + '">' +
              '<i class="fas fa-trash"></i>' +
            '</button>' +
          '</td>';
        tbody.appendChild(tr);
      }

      form.reset();
      // reset to CREATE mode after closing
      modal.on('hidden.bs.modal', function(){
        form.action = '$createUrl';
        document.getElementById('scheduleModalLabel').textContent = 'Add new schedule';
        saveBtn.textContent = 'Save';
        idI.value = '';
        modal.off('hidden.bs.modal');
      });
      modal.modal('hide');
    })
    .catch(function(err){ console.error(err); alert('Network error.'); });
  });
})();
JS;

$this->registerJs($js);
