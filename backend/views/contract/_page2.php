<?php
/**
 * Page 2 – Mesačný výkaz praktického vyučovania žiaka
 *
 * @var common\models\User    $user
 * @var common\models\Partner $partner
 * @var string $company
 * @var string $studyMonth
 * @var array  $events
 * @var float  $hoursTotal
 */

// Month pretty: "YYYY-MM" -> "Mesiac RRRR"
$monthNice = '';
if (preg_match('/^(\d{4})-(\d{2})$/', $studyMonth ?? '', $m)) {
    $year = (int)$m[1];
    $mon  = (int)$m[2];
    $skMonths = [1=>'Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'];
    $monthNice = ($skMonths[$mon] ?? $m[2]) . ' ' . $year;
}

// Split company to name + address if you used “Name — Address”
$companyNameOnly = $company;
$companyAddress  = '';
if (is_string($company)) {
    if (mb_strpos($company, '—') !== false) {
        [$companyNameOnly, $companyAddress] = array_map('trim', explode('—', $company, 2));
    } elseif (mb_strpos($company, ' - ') !== false) {
        [$companyNameOnly, $companyAddress] = array_map('trim', explode(' - ', $company, 2));
    }
}

// Partner bits
$partnerName = $partner->partner_name ?? '';
$partnerAddr = trim(implode(', ', array_filter([
    $partner->address ?? '',
    trim(($partner->zip ?? '').' '.($partner->town ?? ''))
])));

// rows & pad
$VISIBLE_ROWS = 14;
$rows = $events ?? [];
$pad  = max(0, $VISIBLE_ROWS - count($rows));

$normalizeType = function(array $row): string {
    $candidates = ['type','event_type','category','kind'];
    foreach ($candidates as $k) {
        if (isset($row[$k])) {
            $raw = $row[$k];
            if (is_string($raw)) {
                $t = trim(mb_strtolower($raw));
                $map = [
                    'vacation'=>'holiday','leave'=>'holiday','dayoff'=>'holiday','voľno'=>'holiday','volno'=>'holiday',
                    'prázdniny'=>'holiday','prazdniny'=>'holiday','dovolenka'=>'holiday',
                    'pn'=>'sick','ill'=>'sick','illness'=>'sick','choroba'=>'sick','chorý'=>'sick','chory'=>'sick',
                    'lekár'=>'doctor','lekar'=>'doctor','dr'=>'doctor','dr.'=>'doctor',
                    'work'=>'work','workday'=>'work','smena'=>'shift'
                ];
                return $map[$t] ?? $t;
            }
            if (is_int($raw) || ctype_digit((string)$raw)) {
                $numMap = [0=>'work',1=>'doctor',2=>'sick',3=>'holiday',4=>'other',5=>'shift'];
                return $numMap[(int)$raw] ?? '';
            }
        }
    }
    $t = mb_strtolower((string)($row['title'] ?? ''));
    $patterns = [
        'doctor' => '/\b(dr\.?|doctor|dokt(?:or|orka)|lek[aá]r|ambulanci|poliklinik|ordin[aá]ci|vy[šs]etren|kontrol|zub[aá]r|stomatolog|orvos|rendel|vizsg[aá]lat)\b/u',
        'sick'   => '/\b(sick|pn|chor[ýy]|choroba|nemoc|mar[oó]d|praceneschopn|pr[aá]c[eo]neschopn|beteg|t[aá]pp[eé]nz)\b/u',
        'holiday'=> '/\b(holiday|dovolenka|sviatok|vo[ľl]no|pr[aá]zdnin[ay])\b/u',
        'shift'  => '/\b(shift|smena)\b/u',
        'other'  => '/\b(other|iné|ostatn[ée])\b/u',
    ];
    foreach ($patterns as $label => $re) {
        if (preg_match($re, $t)) return $label;
    }
    return '';
};

$EXCLUDE = ['other','holiday','sick','doctor','shift'];
$workedTotal = 0.0;
foreach ($rows as $r) {
    $h = (float)($r['hours'] ?? 0);
    if ($h <= 0) continue;
    if (in_array($normalizeType($r), $EXCLUDE, true)) continue;
    $workedTotal += $h;
}

// -------- signature image resolver --------
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
  .p2-wrap { font-size: 11pt; }
  .p2-title { font-weight: 600; margin: 4px 0 8px; }

  .p2-box { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 10px; }
  .p2-box td { border: 1px solid #000; padding: 6px 10px; vertical-align: top; }
  .p2-lbl { font-weight: 600; }
  .p2-lbl-top { height: 30px; }         /* one-line label row */
  .p2-val-deep { height: 56px; }        /* deeper value row */

  /* Main grid */
  .p2-grid { width: 100%; border: 1px solid #000; border-collapse: collapse; }
  .p2-grid th, .p2-grid td { border: 1px solid #000; padding: 6px 8px; }
  .p2-grid th { font-weight: 600; text-align: center; }

  /* Wider Dátum / Od / Do */
  .p2-c-date  { width: 15%; }
  .p2-c-from,
  .p2-c-to    { width: 10%; }
  .p2-c-hours { width: 10%; text-align: right; }
  .p2-c-work  { width: 36%; }
  .p2-c-sig,
  .p2-c-sig2  { width: 9.5%; }

  .p2-bottom-row td { border-top: 1px solid #000; }
  .p2-total-box { text-align: right; }
  .p2-total-label { line-height: 1.1; }

  .p2-notes { font-size: 10pt; margin-top: 8px; }

  /* signature visuals */
  .p2-sign-img { max-width: 85px; height: auto; display: inline-block; }
  .p2-sign-cell { text-align: center; vertical-align: middle; }
  .p2-sigline { display: inline-block; width: 100%; border-bottom: 1px solid #000; height: 16px; }

  .p2-sign-row { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; }
</style>

<div class="p2-wrap">
  <div class="p2-title"><strong>3.&nbsp; Mesačný výkaz praktického vyučovania žiaka</strong></div>

  <!-- Partner | Mesiac(label) | value -->
  <table class="p2-box">
    <tr>
      <td style="width: 58%;"><span class="p2-lbl"><strong></strong></span> <?= htmlspecialchars($partnerName) ?></td>
      <td style="width: 12%;"><span class="p2-lbl">Mesiac:</span></td>
      <td style="width: 30%;"><?= htmlspecialchars($monthNice) ?></td>
    </tr>
  </table>

  <!-- Addresses block: labels row + values row -->
  <table class="p2-box">
    <tr>
      <td class="p2-lbl p2-lbl-top" style="width: 50%;">Adresa školy:</td>
      <td class="p2-lbl p2-lbl-top" style="width: 50%;">Adresa PPV:</td>
    </tr>
    <tr>
      <td class="p2-val-deep" style="width: 50%;"><?= nl2br(htmlspecialchars($partnerAddr)) ?></td>
      <td class="p2-val-deep" style="width: 50%;"><?= nl2br(htmlspecialchars($companyAddress)) ?></td>
    </tr>
  </table>

  <!-- Employer & Student: label/value + label/value -->
  <table class="p2-box">
    <tr>
      <td class="p2-lbl" style="width: 18%;">Zamestnávateľ:</td>
      <td style="width: 35%;"><?= htmlspecialchars($companyNameOnly ?? '') ?></td>
      <td class="p2-lbl" style="width: 18%;">Meno žiaka:</td>
      <td style="width: 32%;"><?= htmlspecialchars(trim(($user->name_last ?? '').' '.($user->name_first ?? ''))) ?></td>
    </tr>
  </table>

  <hr style="border: none; border-top: 1px solid #000; margin: 10px 0;">

  <!-- Main grid -->
  <table class="p2-grid">
    <thead>
      <tr>
        <th class="p2-c-date">Dátum</th>
        <th class="p2-c-from">Od</th>
        <th class="p2-c-to">Do</th>
        <th class="p2-c-hours">Spolu hodín</th>
        <th class="p2-c-work">Vykonávaná pracovná činnosť</th>
        <th class="p2-c-sig">Podpis žiaka</th>
        <th class="p2-c-sig2">Podpis hl.<br>inštruktora/inštruktora</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['date']) ?></td>
          <td><?= htmlspecialchars($r['from']) ?></td>
          <td><?= htmlspecialchars($r['to']) ?></td>
          <td style="text-align:right;"><?= number_format((float)$r['hours'], 2, ',', ' ') ?></td>
          <td><?= htmlspecialchars($r['title']) ?></td>
          <td></td>
          <td class="p2-sign-cell">
            <?php if ($signImg && !empty($r['date'])): ?>
              <img class="p2-sign-img" src="<?= htmlspecialchars($signImg) ?>" alt="Podpis hl. inštruktora">
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>

      <?php for ($i=0; $i<$pad; $i++): ?>
        <tr>
          <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
      <?php endfor; ?>

      <!-- Totals row -->
      <tr class="p2-bottom-row">
        <td colspan="3" class="p2-total-label">Odpracované hodiny<br>spolu:</td>
        <td class="p2-total-box"><strong><?= number_format($workedTotal, 2, ',', ' ') ?></strong></td>
        <td colspan="2">Klasifikácia žiaka za uvedené obdobie (známka):</td>
        <td></td>
      </tr>
    </tbody>
  </table>

  <div class="p2-notes" style="margin-top: 16px;">
    <div class="p2-sign-row" style="font-size: 9pt;">
      <strong>Podpis hl. inštruktora/inštruktora/majstra odbornej výchovy:</strong>
      <?php if ($signImg): ?>
        <img class="p2-sign-img" src="<?= htmlspecialchars($signImg) ?>" alt="Podpis hl. inštruktora">
      <?php endif; ?>
    </div>
    <div class="p2-sigline"></div>

    <div style="margin-top:10px; font-size: 9pt;"><strong>Podpis vedúceho zmeny/výroby a pod.:</strong></div>
    <div class="p2-sigline"></div>

    <div style="margin-top:10px; font-size: 9pt;">
      <strong>Pozn.:</strong> Mesačný výkaz PV žiaka vyplňa a spracováva žiak (okrem hodnotenia žiaka za mesiac známkou).
      Hl. inštruktor/inštruktor /majster odbornej výchovy a riadiaci pracovník potvrdia správnosť údajov.
    </div>
  </div>
</div>
