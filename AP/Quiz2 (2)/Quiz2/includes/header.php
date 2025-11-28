<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Munkafüzet – Építészet</title>
    <link rel="stylesheet" href="<?= (strpos($_SERVER['PHP_SELF'], 'pages') !== false) ? '../assets/css/style.css' : 'assets/css/style.css'; ?>">
</head>
<body>
<header>
    <h1>Munkafüzet – Építészet</h1>
    <div class="lang-switch">
        <?php global $languages; foreach ($languages as $code => $name): ?>
            <a href="?lang=<?= $code ?>" class="<?= $_SESSION['lang'] == $code ? 'active' : '' ?>"><?= $name ?></a>
        <?php endforeach; ?>
    </div>
</header>
<div class="theme-toggle">
    <button id="themeBtn">🌙</button>
</div>

<script>
    // načítaj tému z localStorage
    const currentTheme = localStorage.getItem("theme") || "dark";
    document.documentElement.setAttribute("data-theme", currentTheme);
    document.getElementById("themeBtn").textContent =
        currentTheme === "dark" ? "🌙" : "☀️";

    // po kliknutí
    document.getElementById("themeBtn").addEventListener("click", () => {
        let theme = document.documentElement.getAttribute("data-theme");

        if (theme === "dark") {
            document.documentElement.setAttribute("data-theme", "light");
            localStorage.setItem("theme", "light");
            document.getElementById("themeBtn").textContent = "☀️";
        } else {
            document.documentElement.setAttribute("data-theme", "dark");
            localStorage.setItem("theme", "dark");
            document.getElementById("themeBtn").textContent = "🌙";
        }
    });
</script>

<main>
