<?php
/**
 * Page 1: Mesačné hlásenie o absencii žiaka na praktickom vyučovaní
 *
 * Expected vars:
 * @var common\models\User      $user
 * @var common\models\User      $supervisor
 * @var common\models\Partner   $partner
 * @var string                  $company
 * @var string                  $shiftDate
 * @var string                  $studyMonth
 * @var common\models\User[]    $batchUsers
 */

use common\models\CalendarEvent;

// two-digit ordinal
$fmtOrd = function(int $i){ return str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '.'; };

// Month "YYYY-MM" -> "Mesiac RRRR"
$monthNice = '';
if (preg_match('/^(\d{4})-(\d{2})$/', $studyMonth ?? '', $m)) {
    $year = (int)$m[1]; $mon = (int)$m[2];
    $sk = [1=>'Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'];
    $monthNice = ($sk[$mon] ?? $m[2]).' '.$year;
} else {
    $monthNice = $studyMonth ?? '';
}

// Supervisor display
$supervisorDisplay = '';
if (!empty($supervisor)) {
    $supLast  = trim($supervisor->name_last ?? '');
    $supFirst = trim($supervisor->name_first ?? '');
    $name = trim($supLast . ' ' . $supFirst);
    if ($name === '') $name = $supervisor->username ?? $supervisor->email ?? '';
    $supervisorDisplay = $name;
}

// Excused hours per student (ceil)
$excusedTypes = ['doctor','sick','other'];
$excusedByUser = [];
if (preg_match('/^\d{4}-\d{2}$/', $studyMonth ?? '')) {
    $monthStart = $studyMonth . '-01 00:00:00';
    $monthEnd   = date('Y-m-t 23:59:59', strtotime($monthStart));
    foreach (($batchUsers ?? []) as $stu) {
        $rows = CalendarEvent::find()
            ->where(['user_id' => $stu->id])
            ->andWhere(['between', 'start', $monthStart, $monthEnd])
            ->andWhere(['type' => $excusedTypes])
            ->all();
        $sum = 0.0;
        foreach ($rows as $ev) {
            $s = strtotime($ev->start); $e = strtotime($ev->end);
            if ($s && $e && $e > $s) $sum += ($e - $s) / 3600.0;
        }
        $excusedByUser[$stu->id] = (int)ceil(max(0,$sum));
    }
}

// -------- signature image resolver --------
$signImg = null;
$try = [
    Yii::getAlias('@webroot/images/sign_szabo.png'),
    Yii::getAlias('@backend/web/images/sign_szabo.png'),
    Yii::getAlias('@frontend/web/images/sign_szabo.png'),
];
foreach ($try as $p) {
    if (is_string($p) && file_exists($p)) { $signImg = $p; break; }
}
?>
<style>
  .abs-page { font-size: 11pt; }
  .abs-title { font-weight: 600; margin: 4px 0 8px; }
  .abs-box { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 10px; }
  .abs-box td { border: 1px solid #000; padding: 8px 10px; vertical-align: top; height: 72px; }
  .abs-box .left { width: 65%; }
  .abs-box .right { width: 35%; }
  .abs-label { font-weight: 600; display: block; margin-bottom: 6px; }
  .abs-small { margin-top: 6px; }

  .abs-table { width: 100%; border: 1px solid #000; border-collapse: collapse; }
  .abs-table th, .abs-table td { border: 1px solid #000; padding: 6px 8px; }
  .abs-table th { text-align: center; font-weight: 600; } /* centered headers */
  .abs-col-ord { width: 8%; }
  .abs-col-name { width: 42%; }
  .abs-col-ospr, .abs-col-neospr, .abs-col-nedor, .abs-col-spolu { width: 12%; }

  .abs-row-ord { white-space: nowrap; text-align:center; }
  .abs-date { margin-top: 20px; }

  .sig-labels { margin-top: 24px; font-size: 10pt; display: flex; justify-content: space-between; gap: 14px; }
  .sig-col { width: 49%; }

  /* New: place the signature image to the RIGHT of the label, smaller */
  .sig-row { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; }
  .sig-caption { white-space:nowrap; }
  .sig-img { max-width:110px; height:auto; margin-left:8px; }
  .sig-line { display: inline-block; border-bottom: 1px solid #000; width: 100%; height: 18px; }
</style>

<div class="abs-page">
  <div class="abs-title">2.&nbsp; Mesačné hlásenie o absencii žiaka na praktickom vyučovaní</div>

  <table class="abs-box">
    <tr>
      <td class="left">
        <strong><span class="abs-label">Zamestnávateľ:</span></strong>
        <div><?= htmlspecialchars($company ?? '') ?></div>

        <div class="abs-small">
          <span class="abs-label" style="display:inline;"><strong>Hlavný inštruktor/Inštruktor/MOV:&nbsp;</strong></span>
          <span><?= htmlspecialchars($supervisorDisplay) ?></span>
        </div>
      </td>
      <td class="right">
        <strong><span class="abs-label">Mesiac:</span></strong>
        <div><?= htmlspecialchars($monthNice) ?></div>
      </td>
    </tr>
  </table>

  <table class="abs-table">
    <thead>
      <tr>
        <th class="abs-col-ord">Por. č.</th>
        <th class="abs-col-name">Priezvisko a meno žiaka</th>
        <th class="abs-col-ospr">Ospravedlnená</th>
        <th class="abs-col-neospr">Neospravedlnená</th>
        <th class="abs-col-nedor">Nedoriešená</th>
        <th class="abs-col-spolu">Spolu</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; foreach (($batchUsers ?? []) as $stu): ?>
        <?php $full = trim(($stu->name_last ?? '') . ' ' . ($stu->name_first ?? '')); ?>
        <?php $ospr = (int)($excusedByUser[$stu->id] ?? 0); ?>
        <tr>
          <td class="abs-row-ord"><?= $fmtOrd($i++) ?></td>
          <td><?= htmlspecialchars($full) ?></td>
          <td style="text-align:center;"><?= $ospr ?></td>
          <td style="text-align:center;">0</td>
          <td style="text-align:center;">0</td>
          <td style="text-align:center;"><?= $ospr ?></td>
        </tr>
      <?php endforeach; ?>

      <?php if (empty($batchUsers)): ?>
      <tr>
        <td class="abs-row-ord"><?= $fmtOrd(1) ?></td>
        <td>&nbsp;</td><td style="text-align:center;">0</td><td style="text-align:center;">0</td><td style="text-align:center;">0</td><td style="text-align:center;">0</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php
    $dateNice = '';
    if (!empty($shiftDate)) { $t = strtotime($shiftDate); if ($t) $dateNice = date('d.m.Y', $t); }
  ?>
  <div class="abs-date" style="font-size: 9pt;"><strong>Dátum:</strong> <?= htmlspecialchars($dateNice) ?></div>

  <div class="sig-labels">
    <div class="sig-col">
      <div class="sig-row">
        <div class="sig-caption">Podpis Hl. inštruktora/Inštruktora/MOV:</div>
        <?php if ($signImg): ?>
          <img class="sig-img" src="<?= htmlspecialchars($signImg) ?>" alt="Podpis hl. inštruktora">
        <?php endif; ?>
      </div>
      <span class="sig-line"></span>
    </div>

    <div class="sig-col" style="text-align:left;">
      <span class="sig-line"></span><br>
      Podpis majstra OV školy:
    </div>
  </div>
</div>
