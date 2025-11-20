<?php
/** @var string $frontendTestUrl */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Kötelező teszt';

// --- Identify current user ---
$user = Yii::$app->user->identity ?? null;
if (!$user) {
    echo '<div class="container mt-5 text-center"><h3 class="text-danger">❌ Nem található bejelentkezett felhasználó.</h3></div>';
    return;
}

// --- Check student_test_log for this user ---
$testDate = Yii::$app->db->createCommand("
    SELECT completed_at 
    FROM student_test_log 
    WHERE userId = :uid 
    ORDER BY completed_at DESC 
    LIMIT 1
")->bindValue(':uid', $user->id)->queryScalar();

// --- Determine if still valid in this school year ---
$isCompleted = false;
if ($testDate) {
    $completed = new DateTime($testDate);
    $now = new DateTime();
    $year = (int)$now->format('Y');

    // School year: September 1 → July 31 next year
    $schoolStart = new DateTime(($now->format('m') >= 9 ? $year : $year - 1) . '-09-01');
    $schoolEnd = (clone $schoolStart)->modify('+11 months')->modify('+30 days'); // ~July 31

    if ($completed >= $schoolStart && $completed <= $schoolEnd) {
        $isCompleted = true;
    }
}

// --- If completed, redirect to shift dashboard ---
if ($isCompleted) {
    $redirectUrl = Url::to(['/user-attendance', 'uid' => $user->id], true);
    echo '<meta http-equiv="refresh" content="0;url=' . Html::encode($redirectUrl) . '">';
    echo '<div class="container mt-5 text-center"><h4 class="text-success">✅ Teszt teljesítve. Átirányítás a műszak oldalra...</h4></div>';
    return;
}
?>

<div class="container mt-5 text-center">
    <h2 class="mb-3">🎓 Kötelező teszt</h2>
    <p class="lead mb-4">
        Mielőtt belép a rendszerbe, kérem, töltse ki az alábbi tesztet.
    </p>
    <?= Html::a(
        'Megnyitás: Építési technikák és szerszámhasználat teszt',
        $frontendTestUrl,
        ['class' => 'btn btn-lg btn-primary', 'target' => '_blank']
    ) ?>
    <p class="mt-4 text-muted">
        A teszt kitöltése után zárja be az oldalt, és térjen vissza ide.<br>
        A rendszer automatikusan felismeri, ha a teszt sikeresen teljesült.
    </p>
    <div class="mt-5">
        <?= Html::a('Ellenőrzés', ['site/testnotice'], ['class' => 'btn btn-outline-success']) ?>
    </div>
</div>
