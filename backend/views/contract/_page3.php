<?php
/**
 * Page 3 – Evidencia dochádzky žiaka na praktické vyučovanie
 *
 * @var common\models\User $user
 * @var common\models\User $supervisor
 * @var string $company
 * @var array  $gridAllMonths   // preferred: [ ['label'=>'September','days'=>30,'values'=>[1..31=>'','A3','8',...]], ...]
 * @var string $gridMonthLabel  // legacy single-month label
 * @var array  $gridValues      // legacy single-month values 1..31 => "A2" | "6" | ""
 * @var int    $gridDaysInMonth // legacy single-month days
 * @var string $studyPlanTypeName
 * @var string $shiftDate
 */

$fullName  = trim(($user->name_last ?? '') . ' ' . ($user->name_first ?? ''));
$birth     = $user->birthdate ?? '';
$address   = trim(($user->street ?? '') . ' ' . ($user->street_no ?? '') . ', ' . ($user->zip ?? '') . ' ' . ($user->city ?? ''));

// Supervisor name
$instructor = '';
if (!empty($supervisor)) {
    $supName = trim(($supervisor->name_first ?? '') . ' ' . ($supervisor->name_last ?? ''));
    if ($supName === '') $supName = $supervisor->username ?? $supervisor->email ?? '';
    $instructor = $supName;
}

// Classroom lookup
$classroom = $user->userclassroom ?? $user->userclassroom1 ?? $user->classroom ?? '';

// Build months data to render
$monthsData = [];
if (!empty($gridAllMonths) && is_array($gridAllMonths)) {
    $order = ['September','Október','November','December','Január','Február','Marec','Apríl','Máj','Jún'];
    $map = [];
    foreach ($gridAllMonths as $row) {
        $lbl = $row['label'] ?? '';
        if ($lbl !== '') $map[$lbl] = [
            'days'   => (int)($row['days'] ?? 31),
            'values' => (array)($row['values'] ?? []),
        ];
    }
    foreach ($order as $lbl) {
        if (isset($map[$lbl])) {
            $monthsData[] = ['label'=>$lbl,'days'=>$map[$lbl]['days'],'values'=>$map[$lbl]['values']];
        } else {
            $monthsData[] = ['label'=>$lbl,'days'=>31,'values'=>array_fill(1,31,'')];
        }
    }
} else {
    // Legacy: only the selected month
    $monthsData[] = [
        'label'  => $gridMonthLabel ?? '',
        'days'   => (int)($gridDaysInMonth ?? 31),
        'values' => is_array($gridValues ?? null) ? $gridValues : array_fill(1,31,''),
    ];
}

// --- Resolve signature image (sign_szabo.png) ---
$signImg = null;
$searchPaths = [
    Yii::getAlias('@webroot/images/sign_szabo.png'),
    Yii::getAlias('@backend/web/images/sign_szabo.png'),
    Yii::getAlias('@frontend/web/images/sign_szabo.png'),
];
foreach ($searchPaths as $p) {
    if (is_string($p) && file_exists($p)) { $signImg = $p; break; }
}
?>
<style>
  .p3-wrap { font-size: 11pt; }
  .p3-title { font-weight: 600; margin: 4px 0 8px; }
  .p3-box { width: 100%; border: 1px solid #000; border-collapse: collapse; }
  .p3-box th, .p3-box td { border: 1px solid #000; padding: 6px 8px; vertical-align: middle; }
  .p3-box th { text-align: center; font-weight: 700; }
  .p3-label { width: 20%; font-weight: 600; }
  .p3-grid { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-top: 6px; }
  .p3-grid th, .p3-grid td { border: 1px solid #000; padding: 3px 5px; text-align: center; vertical-align: middle; }
  .p3-grid thead th { font-weight: 600; }
  .p3-month { text-align: left; padding-left: 6px; font-weight: 600; width: 14%; }
  .p3-day { width: 2.5%; }
  .p3-inactive { background: #f2f2f2; }
  .p3-note { font-size: 10pt; margin-top: 8px; }
  .p3-sigline { display: inline-block; width: 260px; border-bottom: 1px solid #000; height: 16px; }

  /* Signature at bottom */
  .p3-sign-row { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; }
  .p3-sign-img { max-width: 95px; height: auto; display: inline-block; }
</style>

<div class="p3-wrap">
  <div class="p3-title">4.&nbsp; Evidencia dochádzky žiaka na praktické vyučovanie</div>

  <table class="p3-box">
    <tr>
      <th colspan="6">Evidencia praktického vyučovania v organizácii - školský rok 2025/2026</th>
    </tr>
    <tr>
      <td class="p3-label">Priezvisko žiaka:</td><td><?= htmlspecialchars($user->name_last ?? '') ?></td>
      <td class="p3-label">Meno žiaka:</td><td><?= htmlspecialchars($user->name_first ?? '') ?></td>
      <td class="p3-label">Dátum narodenia žiaka:</td><td><?= htmlspecialchars($birth) ?></td>
    </tr>
    <tr>
      <td class="p3-label">Trieda:</td><td><?= htmlspecialchars($classroom) ?></td>
      <td class="p3-label">Bydlisko žiaka:</td><td><?= htmlspecialchars($address) ?></td>
      <td class="p3-label">Odbor štúdia:</td>
      <td><?= htmlspecialchars($studyPlanTypeName ?? '') ?></td>
    </tr>
    <tr>
      <td class="p3-label">Organizácia:</td>
      <td colspan="2"><?= nl2br(htmlspecialchars($company ?? '')) ?></td>
      <td class="p3-label">Inštruktor odborného výcviku:</td>
      <td colspan="2"><?= htmlspecialchars($instructor) ?></td>
    </tr>
  </table>

  <table class="p3-grid">
    <thead>
      <tr>
        <th class="p3-month">Mesiac:</th>
        <th style="text-align:left;">Dni:</th>
        <?php for ($d=1; $d<=31; $d++): ?>
          <th class="p3-day"><?= $d ?></th>
        <?php endfor; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($monthsData as $row): ?>
        <?php
          $label   = $row['label'] ?? '';
          $days    = (int)($row['days'] ?? 31);
          $values  = (array)($row['values'] ?? []);
        ?>
        <tr>
          <td class="p3-month"><?= htmlspecialchars($label) ?></td>
          <td></td>
          <?php for ($d=1; $d<=31; $d++):
            $cell = $values[$d] ?? '';
            $inactive = ($d > $days) ? ' p3-inactive' : '';
          ?>
            <td class="p3-day<?= $inactive ?>"><?= htmlspecialchars($cell) ?></td>
          <?php endfor; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="p3-note">
    <p><strong>Pozn.:</strong> Do príslušného dňa sa uvedie počet hodín praktického vyučovania. Ak sa žiak nezúčastní PV, uvedie sa A a počet hodín absencie (napr. A4). Ospravedlnenie absencie sa uvádza na samostatnom liste.</p>
  </div>

  <div style="margin-top: 10px;">
    <div><strong>Dátum:</strong> <?= htmlspecialchars($shiftDate ?? '___________________________') ?></div>

    <!-- Signature row with image on the right -->
    <div style="margin-top:8px;">
      <div class="p3-sign-row">
        <strong>Podpis inštruktora:</strong>
        <?php if (!empty($signImg)): ?>
          <img class="p3-sign-img" src="<?= htmlspecialchars($signImg) ?>" alt="Podpis inštruktora">
        <?php endif; ?>
      </div>
      <span class="p3-sigline"></span>
    </div>
  </div>
</div>
