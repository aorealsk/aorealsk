<?php
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../includes/header.php';

$questions = getQuestions();
$answers = $_SESSION['answers'] ?? [];

// evaluateAnswers teraz vráti 'details'
$results = evaluateAnswers($questions, $answers);

/* SAVE OPEN ANSWERS */
$openAnswers = [];
foreach ($questions as $q) {
    if ($q['type'] === 'text_input') {
        $id = $q['id'];
        $openAnswers[$id] = $answers[$id] ?? "";
    }
}

$savePath = __DIR__ . "/../results/open_answers/";
if (!is_dir($savePath)) mkdir($savePath, 0777, true);
file_put_contents($savePath . "open_answers_" . date("Ymd_His") . ".json", json_encode($openAnswers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

/* SAVE SCORE */
$scoreData = [
        "student_name" => $_SESSION['student_name'] ?? "",
        "correct" => $results['correct'],
        "total" => $results['total'],
        "percent" => $results['percent'],
        "timestamp" => date("Y-m-d H:i:s")
];

$scorePath = __DIR__ . "/../results/scores/";
if (!is_dir($scorePath)) mkdir($scorePath, 0777, true);
file_put_contents($scorePath . "score_" . date("Ymd_His") . ".json", json_encode($scoreData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// map questions by id for quick lookup
$qmap = [];
foreach ($questions as $q) $qmap[$q['id']] = $q;

$lang = $_SESSION['lang'] ?? 'sk';
?>

<div class="result-container">
    <h2>Výsledky testu</h2>
    <p>Študent: <strong><?= htmlspecialchars($_SESSION['student_name'] ?? '---') ?></strong></p>
    <p>Správne odpovede: <strong><?= $results['correct'] ?></strong></p>
    <p>Celkový počet hodnotených otázok: <strong><?= $results['total'] ?></strong></p>
    <p>Úspešnosť: <strong><?= $results['percent'] ?>%</strong></p>

    <div style="margin-top:18px;display:flex;gap:12px;justify-content:center">
        <a href="../index.php" class="btn-secondary">Späť na úvod</a>
        <a href="../pages/start.php" class="btn-primary">Začni nový test</a>
    </div>

    <a href="certificate.php?percent=<?= $results['percent'] ?>"
       class="btn-primary"
       style="margin-top:20px;display:block;">
        Stiahnuť certifikát (PDF)
    </a>

</div>

<?php
// show only incorrect evaluated questions
$incorrect = array_filter($results['details'], function($d){
    return isset($d['is_correct']) && $d['is_correct'] === false;
});

// if none incorrect -> show nice message
if (empty($incorrect)): ?>
    <div style="max-width:900px;margin:24px auto;padding:18px;background:rgba(16,185,129,0.08);border-radius:12px;border:1px solid rgba(16,185,129,0.12);text-align:center;color:#a7f3d0">
        Všetky hodnotené otázky sú správne. Výborne!
    </div>
<?php else: ?>
    <div style="max-width:900px;margin:24px auto;">
        <h3 style="color:#38bdf8;margin-bottom:12px">Nesprávne otázky (prehľad)</h3>

        <?php foreach ($incorrect as $d):
            $qid = $d['id'];
            if (!isset($qmap[$qid])) continue;
            $q = $qmap[$qid];
            $qtext = $q['text'][$lang] ?? $q['text']['sk'] ?? '';
            ?>
            <div class="question-card" style="border-left:4px solid rgba(220,38,38,0.15);padding:18px 20px;margin-bottom:14px;">
                <h4 style="margin-bottom:8px;color:#f97316"><?= htmlspecialchars($qtext) ?></h4>

                <?php
                // render user's answer and correct answer based on type
                echo "<div style='display:flex;gap:12px;flex-direction:column'>";

                // helper to get option text by key
                $getOptionText = function($question, $key) use ($lang) {
                    if (empty($question['options'])) return $key;
                    foreach ($question['options'] as $opt) {
                        if ($opt['key'] == $key) return $opt['text'][$lang] ?? $opt['text']['sk'] ?? $key;
                    }
                    return $key;
                };

                if (in_array($q['type'], ['single_choice','image_label'])) {
                    $userKey = $d['user'] ?? null;
                    $userText = $userKey !== null ? $getOptionText($q, $userKey) : '<em>Bez odpovede</em>';

                    // correct may be array of keys (take first for single)
                    $correctKeys = $d['correct'] ?? [];
                    $correctTexts = [];
                    if (is_array($correctKeys)) {
                        foreach ($correctKeys as $ck) $correctTexts[] = $getOptionText($q, $ck);
                    }

                    echo "<div class='answer-wrong'><strong>Tvoja odpoveď:</strong> " . htmlspecialchars(is_array($userText) ? implode(', ', $userText) : $userText) . "</div>";
                    echo "<div class='answer-correct'><strong>Správna odpoveď:</strong> " . htmlspecialchars(implode(', ', $correctTexts)) . "</div>";
                }
                else if ($q['type'] === 'multiple_choice') {
                    $userArr = $d['user'] ?? [];
                    $userTexts = [];
                    foreach ($userArr as $uk) $userTexts[] = $getOptionText($q, $uk);

                    $correctArr = $d['correct'] ?? [];
                    $correctTexts = [];
                    foreach ($correctArr as $ck) $correctTexts[] = $getOptionText($q, $ck);

                    echo "<div class='answer-wrong'><strong>Tvoje odpovede:</strong> " . htmlspecialchars(implode(', ', $userTexts) ?: '<em>Bez odpovede</em>') . "</div>";
                    echo "<div class='answer-correct'><strong>Správne odpovede:</strong> " . htmlspecialchars(implode(', ', $correctTexts)) . "</div>";
                }
                else if ($q['type'] === 'sort_list') {
                    // show user's order (in current lang) and correct order (also in current lang)
                    $userOrder = $d['user'] ?? [];
                    $userHtml = implode(' → ', array_map('htmlspecialchars', $userOrder));

                    // build correct order in current language using mapping from en
                    $correctEN = $d['correct'] ?? [];
                    $correctLang = [];
                    if (!empty($correctEN) && isset($q['materials']['en']) && isset($q['materials'][$lang])) {
                        foreach ($correctEN as $enItem) {
                            // find index in EN list
                            $idx = array_search($enItem, $q['materials']['en']);
                            if ($idx !== false && isset($q['materials'][$lang][$idx])) {
                                $correctLang[] = $q['materials'][$lang][$idx];
                            } else {
                                $correctLang[] = $enItem;
                            }
                        }
                    }

                    echo "<div class='answer-wrong'><strong>Tvoje zoradenie:</strong> " . ($userHtml ?: '<em>Bez odpovede</em>') . "</div>";
                    echo "<div class='answer-correct'><strong>Správne zoradenie:</strong> " . htmlspecialchars(implode(' → ', $correctLang)) . "</div>";
                }
                else {
                    // fallback: show raw user/correct
                    echo "<div class='answer-wrong'><strong>Tvoja odpoveď (raw):</strong> " . htmlspecialchars(json_encode($d['user'], JSON_UNESCAPED_UNICODE)) . "</div>";
                    echo "<div class='answer-correct'><strong>Správna odpoveď (raw):</strong> " . htmlspecialchars(json_encode($d['correct'], JSON_UNESCAPED_UNICODE)) . "</div>";
                }

                echo "</div>";
                ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
// clear answers & timer
unset($_SESSION['answers']);
unset($_SESSION['quiz_start']);

include __DIR__ . '/../includes/footer.php';
?>
