
<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    echo '<div class="alert alert-' . $key . '" id="'.$key.'-message">' . $message . '</div>';
}
?>

<main class="form-signin w-100 m-auto">

    <form method="post" role="form">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
        <h1 class="h3 mb-3 fw-normal">Vstup pre personál</h1>
        <div class="form-floating">
            <input type="text" class="form-control" id="floatingInput" placeholder="OB0000x" name="Login[username]">
            <label for="floatingInput">Užívateľské meno</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="Login[pin]">
            <label for="floatingPassword">PIN</label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Vstúpiť</button>
    </form>
</main>

<?php
$js = <<<JS
    $('#floatingInput').on('keyup',function() {
        $('#danger-message').fadeOut();
        $('#success-message').fadeOut();
    });
JS;
$this->registerJS($js);
