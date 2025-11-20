<?php

use yii\helpers\Html;

/**
 * @var $pro
 */

$this->beginPage();
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <?= Html::csrfMetaTags() ?>
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>
    <?php $this->registerCsrfMetaTags() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title>Registrácia personálu</title>
</head>
<body class="bg-light">
    <div class="container w-50 mx-auto p-5">
        <h3>Registrácia personálu</h3>
        <p class="mb-5 form-text">Všetky polia sú povinné!</p>
        <form method="post" role="form" action="/promo/finish-personal">
            <input
                    type="hidden"
                    name="<?= Yii::$app->request->csrfParam; ?>"
                    value="<?= Yii::$app->request->getCsrfToken() ?>">
            <input type="hidden" name="promo_id" value="<?= $pro ?>">

            <div class="alert alert-success alert-dismissible fade d-none" role="alert" id="mess-01">
                <span></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Meno</label>
                    <input type="text" name="name_first" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Priezvisko</label>
                    <input type="text" name="name_last" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Telefón</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Uzivatelske meno</label>
                    <input type="text" name="user_name" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">PIN</label>
                    <input type="password" name="pin" class="form-control" id="pin1">
                    <span class="text-small d-inline text-danger errpin1"></span>
                </div>
                <div class="col-md-6">
                    <label class="form-label">PIN znovu</label>
                    <input type="password" class="form-control" id="pin2">
                    <span class="text-small d-inline text-danger errpin2"></span>
                </div>
            </div>

            <h5 class="mb-3">Jazyky</h5>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="en" name="lang[]">
                <label class="form-check-label">
                    EN - Anglický
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="hu" name="lang[]">
                <label class="form-check-label">
                    HU - Maďarský
                </label>
            </div>
            <div class="form-check mb-5">
                <input class="form-check-input" type="checkbox" value="sk" name="lang[]">
                <label class="form-check-label">
                    SK- Slovenský
                </label>
            </div>

            <button type="button" class="btn btn-primary bg-gradient rg-btn">Registrácia</button>

        </form>
    </div>

    <script>
        $(document).on('click', '.rg-btn', function(){
           $.ajax({
               url: '/promo/finish-personal',
               type: 'post',
               data: $('form').serialize(),
               success: function(data){
                   if (data.status === 'ok') {
                       $('#mess-01').removeClass('d-none').addClass('show');
                       $('#mess-01 span').html('Registrácia prebehla úspešne');
                       $('form').trigger('reset');
                   } else {
                          $('#mess-01').removeClass('d-none').addClass('show');
                          $('#mess-01 span').html(data.message);

                   }
               }
           });
        });

        $(document).on('blur', '#pin2', function(){
            if($('#pin1').val() !== $('#pin2').val()){
                $('.errpin2').html('PIN sa nezhoduje');
                $('#pin2').val('');
                $('#pin1').val('');
            }
        });

        $(document).on('keydown', '#pin1', function(){
           $('.errpin2').html('');
           $('.errpin1').html('');
        });

        $(document).on('keydown', '#pin2', function(){
            $('.errpin2').html('');
            $('.errpin1').html('');
        });

        $(document).on('blur', '#pin1', function(){
            if ($(this).val().length < 4){
                $('.errpin1').html('PIN musí mať aspoň 4 znaky');
                $(this).val('');
            }
        });

        $(document).on('blur', '#pin2', function(){
            if ($(this).val().length < 4){
                $('.errpin2').html('PIN musí mať aspoň 4 znaky');
                $(this).val('');
            }
        });

    </script>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
