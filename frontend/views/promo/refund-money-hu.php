<?php

use yii\helpers\Html;

/**
 * @var $pro
 * @var $content
 */

$this->beginPage();
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <?= Html::csrfMetaTags() ?>
    <?php $this->registerCsrfMetaTags() ?>
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <title></title>
    <style>
        body{
            height: 50vh;
        }
        .container{
            height: 100%;
        }
    </style>
</head>
<body class="bg-light">
<?php $this->beginBody(); ?>
<div class="container mx-auto p-4 w-50">

    <h3 class="mt-5">Kedves <?= $guest->getFullName() ?>,</h3>

    <div class="alert alert-success alert-dismissible fade show mt-5" role="alert" id="s-01" style="display: none">
        Kérelme sikeresen elküldve!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <p class="mt-5">virtuális számláján
        <span id="bal"><b><?= number_format($guest->balance, 2) ?></b></span>
        kredit/&euro; maradt. Mit szeretne tenni a számláján fennmaradó összeggel?
    </p>
    <form method="post" role="form">

        <div class="row mt-5">
            <div class="col-8">Felajánlom jótékony célra a tombola kedvezményezettjének</div>
            <div class="col-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="c-full" name="c_full">
                    <label class="form-check-label">a teljes összeget</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="c-partial" name="c_part">
                    <label class="form-check-label">egy részét</label>
                    <input
                            type="text"
                            id="charity-amount"
                            class="w-25 form-control d-inline"
                            name="c_part_amt"
                            disabled
                    >
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-8">Felajánlom a pincéreknek borravaló gyanánt</div>
            <div class="col-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="t-full" name="t_full">
                    <label class="form-check-label">a teljes összeget</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="t-partial" name="t_part">
                    <label class="form-check-label">egy részét</label>
                    <input
                            type="text"
                            id="tip-amount"
                            class="w-25 form-control d-inline"
                            name="t_part_amt"
                            disabled
                    >
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-8">
                Küldjék vissza a számlámra
                <br>
                <br>
                <b>IBAN:</b> <input type="text" id="iban" class="w-75 form-control d-inline" name="iban">
            </div>
            <div class="col-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="b-full" name="b_full">
                    <label class="form-check-label">a teljes összeget</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="b-partial" name="b_part">
                    <label class="form-check-label">egy részét</label>
                    <input
                            type="text"
                            id="back-amount"
                            class="w-25 form-control d-inline"
                            name="b_part_amt"
                            disabled
                    >
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <button type="button" class="btn btn-primary" id="send-frm">Kérelem elküldése</button>
            </div>
        </div>

    </form>

</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
?>

<script>
    let total = orig_total = <?= $guest->balance ?>;

    let recalc_balance = function(){
        let c = $('#charity-amount').val() || 0;
        let t = $('#tip-amount').val() || 0;
        let b = $('#back-amount').val() || 0;
        total = orig_total - c - t - b;
        $('#bal').html('<b>' + total + '</b>');
    };

    $(document).on('keyup', '#charity-amount, #tip-amount, #back-amount', function(){
        recalc_balance();
    });

    $('#send-frm').on('click', function(){
        if ($('#c-partial').is(':checked') && $('#charity-amount').val() == ''){
            alert('Kérem adja meg a felajánlás összegét!');
            return;
        }
        if ($('#t-partial').is(':checked') && $('#tip-amount').val() == ''){
            alert('Kérem adja meg a borravaló összegét!');
            return;
        }
        if ($('#b-partial').is(':checked') && $('#back-amount').val() == ''){
            alert('Kérem adja meg a visszaküldendő összeget!');
            return;
        }
        if ($('#b-partial').is(':checked') && $('#iban').val() == ''){
            alert('Kérem adja meg a bankszámlaszámát!');
            return;
        }
        if (
            !$('#c-full').is(':checked') &&
            !$('#t-full').is(':checked') &&
            !$('#b-full').is(':checked') &&
            !$('#c-partial').is(':checked') &&
            !$('#t-partial').is(':checked') &&
            !$('#b-partial').is(':checked')
        ){
            alert('Kérem válasszon legalább egy lehetőséget!');
            return;
        }
        if ($('#b-full').is(':checked') && $('#iban').val() == ''){
            alert('Kérem adja meg a bankszámlaszámát!');
            return;
        }

        let iban = $('#iban').val();
        if (iban != '' && !isIban(iban)){
            alert('Kérem adjon meg egy érvényes bankszámlaszámot!');
            return;
        }

        const form = document.querySelector('form');
        const formData = new FormData(form);
        const serializedData = {};
        for (let [key, value] of formData.entries()) {
            serializedData[key] = value;
        }
        const jsonData = JSON.stringify(serializedData);
         $.ajax({
              url: '/promo/refund-done',
              type: 'post',
              data: {
                  fdata: jsonData,
                  gid: <?= $guest->id ?>,
                  <?= $csrf ?>
              },
              success: function(data){
                  $('#s-01').show();
                  form.reset();
              }
         });
    });

    function isIban(iban) {
        iban = iban.replace(/\s/g, '').toUpperCase();

        if (iban.length !== 24) {
            return false;
        }

        iban = iban.slice(4) + iban.slice(0, 4);
        iban = iban.replace(/[A-Z]/g, function (match) {
            return match.charCodeAt(0) - 55;
        });
        var remainder = iban.split('').reduce(function (acc, digit) {
            return (acc + digit) % 97;
        }, 0);

        return remainder === 1;
    }

    $(document).ready(function(){
        $('#c-full').on('change', function(){
            if($(this).is(':checked')){
                $('#c-partial').prop('checked', false).prop('disabled', true);
                $('#t-full').prop('checked', false).prop('disabled', true);
                $('#t-partial').prop('checked', false).prop('disabled', true);
                $('#b-full').prop('checked', false).prop('disabled', true);
                $('#b-partial').prop('checked', false).prop('disabled', true);

                $('#charity-amount').prop('disabled', true);
                $('#tip-amount').prop('disabled', true);
                $('#back-amount').prop('disabled', true);
                total = 0;
            } else {
                $('#c-partial').prop('checked', false).prop('disabled', false);
                $('#t-full').prop('checked', false).prop('disabled', false);
                $('#t-partial').prop('checked', false).prop('disabled', false);
                $('#b-full').prop('checked', false).prop('disabled', false);
                $('#b-partial').prop('checked', false).prop('disabled', false);
                $('#charity-amount').prop('disabled', true);
                $('#tip-amount').prop('disabled', true);
                $('#back-amount').prop('disabled', true);
                total = orig_total;
            }
            $('#bal').html('<b>' + total + '</b>');
        });

        $('#t-full').on('change', function(){
            if($(this).is(':checked')){
                $('#c-full').prop('checked', false).prop('disabled', true);
                $('#c-partial').prop('checked', false).prop('disabled', true);
                $('#t-partial').prop('checked', false).prop('disabled', true);
                $('#b-full').prop('checked', false).prop('disabled', true);
                $('#b-partial').prop('checked', false).prop('disabled', true);

                $('#charity-amount').prop('disabled', true);
                $('#tip-amount').prop('disabled', true);
                $('#back-amount').prop('disabled', true);
                total = 0;
            } else {
                $('#c-full').prop('checked', false).prop('disabled', false);
                $('#c-partial').prop('checked', false).prop('disabled', false);
                $('#t-partial').prop('checked', false).prop('disabled', false);
                $('#b-full').prop('checked', false).prop('disabled', false);
                $('#b-partial').prop('checked', false).prop('disabled', false);
                $('#charity-amount').prop('disabled', true);
                $('#tip-amount').prop('disabled', true);
                $('#back-amount').prop('disabled', true);
                total = orig_total;
            }
            $('#bal').html('<b>' + total + '</b>');
        });

        $('#b-full').on('change', function(){
            if($(this).is(':checked')){
                $('#c-full').prop('checked', false).prop('disabled', true);
                $('#c-partial').prop('checked', false).prop('disabled', true);
                $('#t-full').prop('checked', false).prop('disabled', true);
                $('#t-partial').prop('checked', false).prop('disabled', true);
                $('#b-partial').prop('checked', false).prop('disabled', true);

                $('#charity-amount').prop('disabled', true);
                $('#tip-amount').prop('disabled', true);
                $('#back-amount').prop('disabled', true);
                total = 0;
            } else {
                $('#c-full').prop('checked', false).prop('disabled', false);
                $('#c-partial').prop('checked', false).prop('disabled', false);
                $('#t-full').prop('checked', false).prop('disabled', false);
                $('#t-partial').prop('checked', false).prop('disabled', false);
                $('#b-partial').prop('checked', false).prop('disabled', false);
                $('#charity-amount').prop('disabled', true);
                $('#tip-amount').prop('disabled', true);
                $('#back-amount').prop('disabled', true);
                total = orig_total;
            }
            $('#bal').html('<b>' + total + '</b>');
        });

        $('#c-partial').on('change', function(){
            if($(this).is(':checked')){
                $('#charity-amount').prop('disabled', false);
                $('#c-full').prop('checked', false).prop('disabled', true);
                $('#t-full').prop('checked', false).prop('disabled', true);
                $('#b-full').prop('checked', false).prop('disabled', true);
            } else {
                $('#charity-amount').val('').prop('disabled', true);
                $('#c-full').prop('checked', false).prop('disabled', false);
                $('#t-full').prop('checked', false).prop('disabled', false);
                $('#b-full').prop('checked', false).prop('disabled', false);
            }
            recalc_balance();
        });

        $('#t-partial').on('change', function(){
            if($(this).is(':checked')){
                $('#tip-amount').prop('disabled', false);
                $('#c-full').prop('checked', false).prop('disabled', true);
                $('#t-full').prop('checked', false).prop('disabled', true);
                $('#b-full').prop('checked', false).prop('disabled', true);
            } else {
                $('#tip-amount').val('').prop('disabled', true);
                $('#c-full').prop('checked', false).prop('disabled', false);
                $('#t-full').prop('checked', false).prop('disabled', false);
                $('#b-full').prop('checked', false).prop('disabled', false);
            }
            recalc_balance();
        });

        $('#b-partial').on('change', function(){
            if($(this).is(':checked')){
                $('#back-amount').prop('disabled', false);
                $('#c-full').prop('checked', false).prop('disabled', true);
                $('#t-full').prop('checked', false).prop('disabled', true);
                $('#b-full').prop('checked', false).prop('disabled', true);
            } else {
                $('#back-amount').val('').prop('disabled', true);
                $('#c-full').prop('checked', false).prop('disabled', false);
                $('#t-full').prop('checked', false).prop('disabled', false);
                $('#b-full').prop('checked', false).prop('disabled', false);
            }
            recalc_balance();
        });

    });
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
