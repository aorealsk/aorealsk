<?php
/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\users\UserFile;
use yii\helpers\StringHelper;

$this->title = "Dashboard";

$user = Yii::$app->user->identity;
$first = $user->name_first ?? '';
$last  = $user->name_last ?? '';
$displayName = trim($first.' '.$last) ?: ($user->username ?? ('User #'.(int)$user->id));

// =====================================================
// 🎟️ Calculate tickets (each attendance ≥ 6 hours)
// =====================================================
$attendances = \common\models\UserAttendance::find()
    ->where(['userId' => $user->id])
    ->andWhere(['not', ['inTime' => null]])
    ->andWhere(['not', ['outTime' => null]])
    ->all();

$tickets = 0;
foreach ($attendances as $a) {
    $in  = strtotime($a->inTime);
    $out = strtotime($a->outTime);
    if ($in && $out && (($out - $in) / 3600) >= 6) {
        $tickets++;
    }
}

// =====================================================
// 🥇 Calculate golds (from student_test_log, valid school year)
// =====================================================
$now = new \DateTime();
$year = (int)$now->format('Y');
$schoolStart = new \DateTime(($now->format('m') >= 9 ? $year : $year - 1) . '-09-01');
$schoolEnd = (clone $schoolStart)->modify('+11 months')->modify('+30 days'); // → July 31

$gold = 0;
$testDate = Yii::$app->db->createCommand("
    SELECT completed_at 
    FROM student_test_log 
    WHERE userId = :uid 
    ORDER BY completed_at DESC 
    LIMIT 1
")->bindValue(':uid', $user->id)->queryScalar();

if ($testDate) {
    $completed = new \DateTime($testDate);
    if ($completed >= $schoolStart && $completed <= $schoolEnd) {
        $gold = 1;
    }
}

// =====================================================
// --- Dashboard styles and constants ---
// =====================================================
$this->registerCss(<<<CSS
.card.rounded-5 { border-radius: .75rem; }
.card-shadow { box-shadow: 0 4px 16px rgba(0,0,0,.05); }
.kpi { font-weight: 700; font-variant-numeric: tabular-nums; letter-spacing: .3px; }
.small-muted { color: #687076; font-size: .85rem; }
.quick-btn { min-width: 170px; margin: .25rem .25rem .25rem 0; }
.table-compact th, .table-compact td { padding: .45rem .5rem; vertical-align: middle; }
.border-soft { border: 1px solid rgba(0,0,0,.06); }
.badge-soft-success{background:#e9f9ef;color:#166534;border-radius:.5rem;padding:.15rem .5rem;}
.badge-soft-warning{background:#fff7ed;color:#9a3412;border-radius:.5rem;padding:.15rem .5rem;}
CSS);

$uid = (int)Yii::$app->user->id;
?>

<div class="container-fluid">

  <div class="row page-titles">
    <div class="col-md-8 align-self-center">
      <h4 class="text-themecolor mb-0"><?= $this->title ?></h4>
      <div class="small-muted">
        Vitajte, <strong><?= Html::encode($displayName) ?></strong>
      </div>
    </div>
  </div>

  <div class="row">

    <!-- Quick Actions -->
    <div class="col-lg-6">
      <div class="card rounded-5 card-shadow mb-4">
        <div class="card-body">
          <h5 class="mb-3">Rýchle akcie</h5>
          <div class="d-flex flex-wrap">
            <a class="btn btn-primary quick-btn"
               href="<?= Url::to(['/user-attendance', 'uid' => $uid], true) ?>">
              <i class="ti-list me-1"></i> Dochádzka
            </a>
            <a class="btn btn-light border-soft quick-btn" href="<?= Url::to(['/profile/edit']) ?>">
              <i class="ti-bar-chart me-1"></i> Osobné údaje
            </a>
            <a class="btn btn-outline-primary quick-btn"
               href="<?= Url::to(['/user-attendance', 'uid' => $uid], true) ?>">
              <i class="ti-list me-1"></i> Všetky záznamy
            </a>
            <a class="btn btn-outline-secondary quick-btn" href="<?= Url::to(['/calendar/index']) ?>">
              <i class="ti-calendar me-1"></i> Kalendár
            </a>
            <a class="btn btn-outline-secondary quick-btn" href="<?= Url::to(['/tasks/index']) ?>">
              <i class="ti-check-box me-1"></i> Úlohy
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- KPI -->
    <div class="col-lg-6">
      <div class="card rounded-5 card-shadow mb-4">
        <div class="card-body text-center">
          <h5 class="mb-3">Moje dnešné KPI</h5>
          <div class="row">
            <div class="col-6">
              <div class="small-muted">Odpracované dnes</div>
              <div class="kpi display-6" id="kpi-today">00:00</div>
            </div>
            <div class="col-6">
              <div class="small-muted">Status</div>
              <div class="kpi display-6">
                <span id="kpi-status" class="badge-soft-warning">Dnes</span>
              </div>
            </div>
          </div>
          <div class="small-muted mt-2">Čas sa počíta z dnešných záznamov (in/out).</div>
        </div>
      </div>
    </div>

    <!-- 🎟️ Tickets & 🥇 Gold -->
    <div class="col-lg-6">
      <div class="card rounded-5 card-shadow mb-4">
        <div class="card-body text-center">
          <h5 class="mb-3">Moje odmeny</h5>
          <div class="row">
            <div class="col-6">
              <div class="small-muted">Stravné lístky</div>
              <div class="kpi display-6 text-primary">
                <i class="ti-ticket me-1"></i> <?= (int)$tickets ?>
              </div>
            </div>
            <div class="col-6">
              <div class="small-muted">Zlaté body</div>
              <div class="kpi display-6 text-warning">
                <i class="ti-crown me-1"></i> <?= (int)$gold ?>
              </div>
            </div>
          </div>
          <div class="small-muted mt-2">
            <?= $gold ? '✅ Test platný v aktuálnom školskom roku.' : '❌ Test sa musí obnoviť.' ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Today's Attendance (placeholder retained) -->
    <div class="col-12">
      <div class="card rounded-5 card-shadow mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0">Dnešná dochádzka</h5>
            <a class="btn btn-sm btn-outline-secondary"
               href="<?= Url::to(['/user-attendance', 'uid' => $uid], true) ?>">
              Otvoriť dochádzku
            </a>
          </div>
          <div class="table-responsive">
            <table id="today-monitor" class="table table-striped table-compact mb-0">
              <thead>
                <tr>
                  <th>Selfie</th>
                  <th>Meno</th>
                  <th>Dátum</th>
                  <th>Začiatok</th>
                  <th>Koniec</th>
                  <th>Odpracované</th>
                  <th>Stav</th>
                </tr>
              </thead>
              <tbody>
                <tr><td colspan="7" class="text-muted">Načítavam…</td></tr>
              </tbody>
            </table>
          </div>
          <div class="small-muted mt-2">
            Zobrazuje iba záznamy pre dnešný dátum a vaše ID.
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
