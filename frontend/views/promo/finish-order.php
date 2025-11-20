<?php

use yii\helpers\Url;

/**
 * @var $order
 */

?>
<div class="row p-5">
    <div class="col-md-2 col-m-1"></div>
    <div class="col-md-8 col-sm-10">
        <h1 class="text-center">Objednavka #<?= $order->id ?></h1>
        <h5 class="text-center mb-5 text-muted">Naskenujte QR zákazníka</h5>
        <a href="<?= Url::to(['/promo/home']) ?>"
           class="btn btn-primary d-flex justify-content-center col-6 mx-auto mb-5">
            Späť na úvod
        </a>
        <div class="d-grid gap-5 col-5 mx-auto mb-5">
            <div id="reader" width="600px">
                <input type="file" id="qr-input-file" accept="image/*" capture>
            </div>
        </div>
        <input type="hidden" id="oid" value="<?= $order->id ?>">
        <table class="table table-borderless table-responsive mb-3">
            <tr>
                <td>Meno zákazníka:</td>
                <td id="n01"></td>
            </tr>
            <tr>
                <td>Suma:</td>
                <td><?= $order->total ?> &euro;</td>
            </tr>
            <tr>
                <td>Platba:</td>
                <td id="p05"></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <button class="btn btn-danger" id="pay">Zaplatiť</button>
                    <a href="<?= Url::to(['/promo/my-orders']) ?>" class="btn btn-primary">Späť na moje objednávky</a>
                </td>
            </tr>
        </table>

        <table class="table table-borderless table-responsive mb-3">
            <thead class="bg-primary text-white">
                <tr>
                    <th style="width:55%">Položka</th>
                    <th>MJ</th>
                    <th>Množstvo</th>
                    <th>Jedn. cena (&euro;)</th>
                    <th>Cena (&euro;)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order->items as $item) : ?>
                <tr>
                        <td><?= $item->detail->stockDetail->getTitle() ?></td>
                        <td><?= $item->unit ?></td>
                        <td><?= $item->amount ?></td>
                        <td><?= $item->unit_price ?></td>
                        <td><?= $item->price ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
    <div class="col-md-2 col-sm-1"></div>
</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$js = <<<JS
const html5QrCode = new Html5Qrcode("reader");
// File based scanning
const fileinput = document.getElementById('qr-input-file');
fileinput.addEventListener('change', e => {
  if (e.target.files.length == 0) {
    // No file selected, ignore 
    return;
  }

  const imageFile = e.target.files[0];
  // Scan QR Code
  html5QrCode.scanFile(imageFile, true)
  .then(decodedText => {
    $.ajax({
        url: '/promo/identify-qr',
        type: 'POST',
        data: {qr: decodedText, {$csrf} },
        success: function(data) {
            $('#n01').text(data.guest_name);
            html5QrCode.clear();
        }
    });
  })
  .catch(err => {
    // failure, handle it.
    console.log('Error scanning file.')
  });
});

$('#pay').on('click', function() {
    var orderId = $('#oid').val();
    $.ajax({
        url: '/promo/pay-order',
        type: 'POST',
        data: {order_id: orderId, {$csrf} },
        success: function(data) {
            if (data.status == 'ok') {
                $('#p05').html('Objednávka bola úspešne zaplatená');
            } else {
                $('#p05').html(data.message);
            }
        }
    });
});
JS;

$this->registerJS($js);