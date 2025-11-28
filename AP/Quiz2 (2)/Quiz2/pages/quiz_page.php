<?php
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../includes/header.php';

$questions = getQuestions();
$lang = $_SESSION['lang'];

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 12;
$totalPages = ceil(count($questions) / $perPage);

// uloženie odpovedí z predchádzajúcej stránky
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $val) {
        $_SESSION['answers'][$key] = $val;
    }

    // ak je posledná stránka → presmeruj na výsledky
    if ($page >= $totalPages) {
        $results = evaluateAnswers($questions, $_SESSION['answers'] ?? []);
        header("Location: result_page.php?c={$results['correct']}&t={$results['total']}&p={$results['percent']}");
        exit;
    } else {
        header("Location: quiz_page.php?page=" . ($page + 1));
        exit;
    }
}

// vypočítaj, ktoré otázky sa majú zobraziť
$start = ($page - 1) * $perPage;
$end = $start + $perPage;
$currentQuestions = array_slice($questions, $start, $perPage);

?>

<form method="post" class="quiz-form">
    <?php foreach ($currentQuestions as $q): ?>
        <?= renderQuestion($q, $lang) ?>
    <?php endforeach; ?>

    <div class="quiz-buttons">
        <?php if ($page > 1): ?>
            <a href="quiz_page.php?page=<?= $page - 1 ?>" class="btn-secondary">← Späť</a>
        <?php endif; ?>

        <button type="submit" class="btn-primary">
            <?= ($page == $totalPages) ? "Zobraziť výsledky" : "Ďalšia strana →" ?>
        </button>
    </div>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
