<?php

use yii\helpers\Url;

?>
    <div class="row p-5">
        <div class="col-md-2 col-m-1"></div>
        <div class="col-md-8 col-sm-10">
            <h1 class="text-center">Ahoj, <?= $username?>!</h1>
            <h3 class="text-center mb-5">Načítajte QR kód</h3>

            <h5 class="text-center mb-5">QR kód lístka</h5>
            <div class="d-grid gap-5 col-5 mx-auto mb-5">
                <div id="reader1" width="600px">
                    <input type="file" id="qr-input-file1" accept="image/*" capture>
                </div>
            </div>

            <table class="table table-borderless table-responsive">
                <tr>
                    <td>Číslo lístka:</td>
                    <td><input id="t00" type="text" class="form-control"></td>
                </tr>

                <tr>
                    <td>Meno zákazníka:</td>
                    <td>
                        <input type="text" id="n01" class="form-control">
                    </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>
                        <input type="email" id="n02" class="form-control">
                    </td>
                </tr>
                <tr>
                    <td>Telefón:</td>
                    <td>
                        <input type="text" id="n03" class="form-control">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">Miesto:</td>
                </tr>
                <tr>
                    <td>Rad:</td>
                    <td>
                        <input type="text" id="n04" class="form-control">
                    </td>
                </tr>
                <tr>
                    <td>Číslo:</td>
                    <td>
                        <input type="text" id="n05" class="form-control">
                    </td>
                </tr>
                <tr>
                    <td>PIN:</td>
                    <td>
                        <input type="password" id="n06" class="form-control">
                    </td>
                </tr>
                <tr>
                    <td>Kredit:</td>
                    <td>
                        <input type="text" id="n12" class="form-control">
                    </td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td id="n10"></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="hidden" id="gid">
                        <button class="btn btn-success" id="confirm-guest">Pridať</button>
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
const html5QrCode1 = new Html5Qrcode("reader1");

// File based scanning
const fileinput1 = document.getElementById('qr-input-file1');

fileinput1.addEventListener('change', e => {
  if (e.target.files.length == 0) {
    // No file selected, ignore 
    return;
  }
  const imageFile = e.target.files[0];
  // Scan QR Code
  html5QrCode1.scanFile(imageFile, true)
  .then(decodedText => {
    $('#t00').val(decodedText);
    $.ajax({
        url: '/promo/identify-qr',
        type: 'POST',
        data: {qr: decodedText, action: 'reg', {$csrf} },
        success: function(data) {
            $('#n01').val(data.guest_name);
            $('#n02').val(data.guest_email);
            $('#n03').val(data.guest_phone);
            $('#n04').val(data.seat_row);
            $('#n05').val(data.seat_num);
            $('#n12').val(data.guest_credit);
            $('#gid').val(data.guest_id);
            html5QrCode1.clear();
        }
    });
  })
  .catch(err => {
    console.log('Error scanning file.')
  });
});


$('#confirm-guest').on('click', function() {
    var ticket = $('#t00').val();
    var name = $('#n01').val();
    var email = $('#n02').val();
    var phone = $('#n03').val();
    var row = $('#n04').val();
    var seat = $('#n05').val();
    var pin = $('#n06').val();
    var credit = $('#n12').val();
    var gid = $('#gid').val();
    $.ajax({
        url: '/promo/confirm-guest',
        type: 'POST',
        data: {
            ticket: ticket, 
            name: name, 
            email: email, 
            phone: phone, 
            row: row, 
            seat: seat, 
            pin: pin, 
            credit: credit, 
            gid: gid,
            {$csrf} 
           },
        success: function(data) {
            if (data.status == 'error') {
                $('#n10').text(data.message);
            } else {
                $('#n10').text('Zákazník bol potvrdený.');
            }
        }
    });
});

JS;

$this->registerJS($js);
