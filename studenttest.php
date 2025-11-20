<?php
// ===================================================================
// studenttest.php — Standalone test page (frontend-side quiz)
// -------------------------------------------------------------------
// Logs completions to student_test_log (userId, completed_at)
// Marks /studenttests/{userId}.done
// Redirects user to /backoffice/user-attendance?uid={userId}
// ===================================================================

error_reporting(E_ALL);
ini_set('display_errors', 0);

// --- Configuration ---
$backendBase = '/backoffice';
$storageDir  = __DIR__ . '/data';
$csvFile     = $storageDir . '/test_results.csv';

// ✅ Simple direct DB credentials (edit these)
$dbHost = 'localhost';
$dbName = 'aoreal';        // your DB name
$dbUser = 'aoreal';   // your DB username
$dbPass = 'Op9YQ@WC3F'; // your DB password

if (!is_dir($storageDir)) @mkdir($storageDir, 0775, true);

// --- Identify user ---
$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
if ($uid <= 0) {
    echo '<h3 style="margin:2em;color:red;">❌ Nincs érvényes felhasználói azonosító (uid).</h3>';
    exit;
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// ===================================================================
// Questions
// ===================================================================
$imgBase = '/media/assets_for_testing';
$questions = [
    [
        'text' => 'Melyik szerszámmal húzod meg a facsavart?',
        'options' => ['A'=>'Kalapács','B'=>'Csavarhúzó','C'=>'Fűrész','D'=>'Ceruza'],
        'correct'=>'B',
        'img_options'=>[
            'A'=>$imgBase.'/kalapacs.png','B'=>$imgBase.'/csavarhuzo.png',
            'C'=>$imgBase.'/furesz.png','D'=>$imgBase.'/ceruza.png'
        ],
        'img_correct'=>'B',
    ],
    [
        'text'=>'Melyik szerszámmal vágod el a lécet?',
        'options'=>['A'=>'Fűrész','B'=>'Kalapács','C'=>'Csavarhúzó','D'=>'Ceruza'],
        'correct'=>'A',
        'img_options'=>[
            'A'=>$imgBase.'/furesz.png','B'=>$imgBase.'/kalapacs.png',
            'C'=>$imgBase.'/csavarhuzo.png','D'=>$imgBase.'/ceruza.png'
        ],
        'img_correct'=>'A',
    ],
    [
        'text'=>'Téglák vágás előtti jelöléséhez mi a legcélszerűbb eszköz?',
        'options'=>['A'=>'Ceruza','B'=>'Kalapács','C'=>'Fűrész','D'=>'Csavarhúzó'],
        'correct'=>'A',
        'img_options'=>[
            'A'=>$imgBase.'/ceruza.png','B'=>$imgBase.'/kalapacs.png',
            'C'=>$imgBase.'/furesz.png','D'=>$imgBase.'/csavarhuzo.png'
        ],
        'img_correct'=>'A',
    ],
    [
        'text'=>'Tartós, erős zajban melyik egyéni védőeszköz a megfelelő?',
        'options'=>['A'=>'Védősisak','B'=>'Fülvédő','C'=>'Kabát','D'=>'Ceruza'],
        'correct'=>'B',
        'img_options'=>[
            'A'=>$imgBase.'/sisak.png','B'=>$imgBase.'/fulvedo.png',
            'C'=>$imgBase.'/kabat.png','D'=>$imgBase.'/ceruza.png'
        ],
        'img_correct'=>'B',
    ],
    [
        'text'=>'Magasban végzett munkánál melyik védőeszköz az alap?',
        'options'=>['A'=>'Védősisak','B'=>'Kabát','C'=>'Fülvédő','D'=>'Kalapács'],
        'correct'=>'A',
        'img_options'=>[
            'A'=>$imgBase.'/sisak.png','B'=>$imgBase.'/kabat.png',
            'C'=>$imgBase.'/fulvedo.png','D'=>$imgBase.'/kalapacs.png'
        ],
        'img_correct'=>'A',
    ],
];

// ===================================================================
// Process form submission
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Save result CSV backup (optional) ---
    if ($fp = @fopen($csvFile, file_exists($csvFile) ? 'a' : 'w')) {
        if (ftell($fp) === 0)
            fputcsv($fp, ['timestamp','user_id','ip','agent']);
        fputcsv($fp, [time(), $uid, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']);
        fclose($fp);
    }

    // --- Save in Database ---
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("
            INSERT INTO student_test_log (userId, completed_at)
            VALUES (:uid, NOW())
            ON DUPLICATE KEY UPDATE completed_at = NOW()
        ");
        $stmt->execute([':uid' => $uid]);
    } catch (Exception $e) {
        error_log('DB write failed: ' . $e->getMessage());
    }

    // --- Mark as done (legacy) ---
    $doneDir = __DIR__ . '/studenttests';
    if (!is_dir($doneDir)) @mkdir($doneDir, 0777, true);
    file_put_contents($doneDir . '/' . $uid . '.done', 'completed ' . date('Y-m-d H:i:s'));

    // ✅ Redirect to attendance
    $redirectUrl = $backendBase . '/user-attendance?uid=' . $uid;
    echo '<meta http-equiv="refresh" content="0;url=' . h($redirectUrl) . '">';
    echo '<div style="margin:2em;font-size:1.3em;">✅ Teszt befejezve. Átirányítás a műszak oldalra...</div>';
    exit;
}
?>
<!doctype html>
<html lang="hu">
<head>
<meta charset="utf-8">
<title>Építési technikák és szerszámhasználat – teszt</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
body{padding:16px}
.img-opts{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px}
.img-opts label{display:block;border:1px solid #ddd;border-radius:8px;padding:8px;text-align:center;cursor:pointer}
.img-opts img{max-width:100%;height:90px;object-fit:contain}
.form-check-image{display:none}
.form-check-image:checked + label{border-color:#0d6efd;box-shadow:0 0 0 0.2rem rgba(13,110,253,.15)}
.badge-sub{vertical-align:middle}
</style>
</head>
<body>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">&larr; Vissza</button>
    <h3 class="mb-0">Építési technikák és szerszámhasználat – teszt</h3>
  </div>

  <form method="post">
    <?php foreach ($questions as $i => $q): ?>
      <div class="card mb-3">
        <div class="card-body">
          <strong><?= ($i+1).'. '.h($q['text']) ?></strong>
          <div class="mt-2">
            <?php foreach ($q['options'] as $key => $label): ?>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="answers[<?= $i ?>]" id="q<?= $i.$key ?>" value="<?= h($key) ?>" required>
                <label class="form-check-label" for="q<?= $i.$key ?>"><b><?= h($key) ?>.</b> <?= h($label) ?></label>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if (!empty($q['img_options'])): ?>
            <div class="mt-3">
              <div class="mb-1"><span class="badge bg-secondary badge-sub">Képes választás:</span></div>
              <div class="img-opts">
                <?php foreach ($q['img_options'] as $key => $src): $imgId="imgq{$i}{$key}"; ?>
                  <input class="form-check-image" type="radio" name="imganswers[<?= $i ?>]" id="<?= $imgId ?>" value="<?= h($key) ?>" required>
                  <label for="<?= $imgId ?>">
                    <div class="small mb-1"><b><?= h($key) ?>.</b></div>
                    <img src="<?= h($src) ?>" alt="">
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <input type="hidden" name="uid" value="<?= (int)$uid ?>">
    <button class="btn btn-primary">Beküldés</button>
  </form>
</div>
</body>
</html>
