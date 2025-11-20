<?php
use yii\helpers\Html;

$this->title = 'Hibás adatok / Chybné údaje';

$css = <<<CSS
.error-page-wrapper {
    max-width: 900px;
    margin: 100px auto 80px;   /* lejjebb húzzuk, alul is adunk helyet */
    padding: 0 15px;
}

.error-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 24px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}

.error-card h1 {
    font-size: 1.6rem;
    margin-bottom: 16px;
    line-height: 1.3;
}

.error-card p {
    margin-bottom: 8px;
    line-height: 1.5;
}

.error-back-btn {
    margin-top: 24px;
}

/* Gomb fix: ne csússzon ki a szöveg, legyen normál magasságú */
.error-back-btn .btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 4px;
    font-size: 0.95rem;
    white-space: normal;       /* engedjük a sortörést */
    text-align: center;
    line-height: 1.3;
}

/* Mobilon legyen kényelmesen olvasható, középre húzva */
@media (max-width: 767.98px) {
    .error-page-wrapper {
        margin: 60px auto 60px;
    }
    .error-card {
        padding: 24px 18px;
    }
    .error-card h1 {
        font-size: 1.4rem;
    }
    .error-back-btn .btn {
        width: 100%;
    }
}

@media (min-width: 768px) {
    .error-card {
        padding: 32px 28px;
    }
    .error-card h1 {
        font-size: 2rem;
    }
}
CSS;

$this->registerCss($css);
?>

<main class="site-applicant">
    <div class="container-fluid">
        <div class="error-page-wrapper">
            <div class="error-card">
                <h1>Hibás vagy hiányos adatok / Chybné alebo neúplné údaje</h1>

                <p>
                    A regisztráció feldolgozása közben hiba történt, valószínűleg
                    néhány mező hiányzik vagy hibásan lett kitöltve.
                </p>
                <p>
                    Kérjük, lépjen vissza az előző oldalra, ellenőrizze az összes
                    mezőt, és töltse ki újra a jelentkezési űrlapot.
                </p>

                <p style="margin-top: 12px;">
                    Pri spracovaní registrácie sa vyskytla chyba, pravdepodobne
                    niektoré polia chýbajú alebo sú nesprávne vyplnené.
                </p>
                <p>
                    Prosíme, vráťte sa na predchádzajúcu stránku, skontrolujte
                    všetky polia a odošlite prihlášku znova.
                </p>

                <div class="error-back-btn">
                    <a href="javascript:history.back()" class="btn btn-primary">
                        ← Vissza az űrlaphoz / Späť na formulár
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
