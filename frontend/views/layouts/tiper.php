<?php

/**
 * @var $content
 */
use yii\helpers\Html;

$this->beginPage() ?>

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <meta http-equiv='cache-control' content='no-cache'>
        <meta http-equiv='expires' content='0'>
        <meta http-equiv='pragma' content='no-cache'>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->registerCsrfMetaTags() ?>
        <?php $this->head(); ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD"
            crossorigin="anonymous">
    </head>
    <body>
    <?php $this->beginBody(); ?>

    <div class="container">
        <?= $content ?>
    </div>
    <script
        src="https://code.jquery.com/jquery-3.6.3.min.js"
        crossorigin="anonymous"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        crossorigin="anonymous"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"
        crossorigin="anonymous"></script>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>