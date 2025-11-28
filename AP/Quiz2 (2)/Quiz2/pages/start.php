<?php
session_start();
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['student_name'] ?? '');
    if ($name === '') {
        $error = "Prosím zadajte meno.";
    } else {
        $_SESSION['student_name'] = $name;
        header("Location: quiz_page.php?page=1");
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="intro" style="max-width:600px;margin:auto;">
    <h2>Začiatok testu</h2>
    <p>Zadajte svoje meno. Bude uvedené na výsledkoch aj na certifikáte.</p>

    <?php if (!empty($error)): ?>
        <div style="color:#f87171;margin-bottom:10px;"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="student_name" placeholder="Vaše meno"
               style="width:100%;padding:12px;border-radius:8px;border:1px solid #475569;margin-bottom:20px;font-size:1rem;">
        <button type="submit" class="btn-primary" style="width:100%;">Začať test</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
