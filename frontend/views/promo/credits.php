<?php

use yii\helpers\Url;

?>
<div class="row p-5">
    <div class="col-md-2 col-m-1"></div>
    <div class="col-md-8 col-sm-10">
        <h1 class="text-center mb-5">Ahoj, <?= $username?>!</h1>



        <table class="table table-borderless table-responsive">
            <tr>
                <td></td>
                <td><input type="text" class="form-control" id="q"></td>
            </tr>
            <tr>
                <td>Meno zákazníka:</td>
                <td id="n01"></td>
            </tr>
            <tr>
                <td>Zostatok:</td>
                <td id="n02"></td>
            </tr>
            <tr>
                <td>Nový kredit:</td>
                <td>
                    <input type="hidden" name="guest_id" id="g01">
                    <input type="text" name="" id="ncred" class="form-control">
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button class="btn btn-success" id="addcredit">Pridať</button>
                    <a href="<?= Url::to(['/promo/home']) ?>" class="btn btn-primary">Späť na hlavnú stránku</a>
                </td>
            </tr>
            
        </table>

    </div>
    <div class="col-md-2 col-sm-1"></div>
</div>




<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$js = <<<JS

$('#addcredit').on('click', function() {
    var newCredit = $('#ncred').val();
    var guestId = $('#g01').val();
    $.ajax({
        url: '/promo/add-credit',
        type: 'POST',
        data: {guest_id: guestId, credit: newCredit, {$csrf} },
        success: function(data) {
            $('#n02').text(data.guest_balance);
        }
    });
});
$('#q').on('keyup', function() {
    var query = $(this).val();
    $.ajax({
        url: '/promo/get-guest',
        type: 'POST',
        data: {gname: query, {$csrf} },
        success: function(data) {
            $('#n01').text(data.name);
            $('#n02').text(data.guest.balance);
            $('#g01').val(data.guest.id);
        }
    });
});
JS;

$this->registerJS($js);
