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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <style>
        body {
            font-family: Poppins, sans-serif;
        }
        .cena {
            color: rgb(0,157,224);
        }
        .it {
            display: flex;
            flex-wrap: wrap;
        }
        .it0 {
            padding-top: 20px;
            padding-bottom: 20px;
            padding-left: 10px;
            padding-right: 20px;
            border-top: 1px solid #dddddd;
        }
        .it0:hover {
            border: 1px solid #ddd;
            cursor: pointer;
            border-radius: 3px;
        }
        .tit01 {
            width: 70%;
        }
        .tit02 {
            width: 30%;
        }
        ul.objednavky,
        ul.oh-main{
            list-style-type: none;
            padding:0;
        }

        ul.oh-main > li {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: 1fr;
            grid-column-gap: 5px grid-row-gap: 0px;
            padding: 5px 10px;
        }

        ul.objednavky > li {
            padding:10px;
            border: 1px solid #c0c0c0;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        ul.objednavky > li:hover {
            background-color: #c0c0c0;
            color: #fff;
            cursor:pointer;
        }

        ul.oh-main > li:hover {
            background-color: #f0f0f0;
            cursor:pointer;
        }

    </style>
</head>
<body>
<?php $this->beginBody(); ?>

<div class="container">
<?= $content ?>
</div>
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>