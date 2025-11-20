<?php

/**
 * @var $list
 */

use yii\helpers\Url;

?>
<div class="row p-5">
    <div class="col-md-2 col-m-1"></div>
    <div class="col-md-8 col-sm-10">
        <h1 class="text-center mb-5">Nová objednávka</h1>
        <a href="<?= Url::to(['/promo/home']) ?>"
           class="btn btn-primary d-flex justify-content-center col-6 mx-auto mb-5">
            Späť na úvod
        </a>


        <table class="table table-borderless table-responsive mb-5">
            <input type="hidden" id="r01">
            <tr>
                <td>Vyhladanie:</td>
                <td><input type="text" id="n01" class="form-control"></td>
            </tr>
            <tr>
                <td>Číslo zákazníka:</td>
                <td id="t00"></td>
            </tr>
            <tr>
                <td>Meno zákazníka:</td>
                <td id="n20"></td>
            </tr>
            <tr>
                <td>Kredit:</td>
                <td id="n02"></td>
            </tr>
            <tr>
                <td>Stav objednávky:</td>
                <td id="rr01"></td>
            </tr>
        </table>

        <h5 class="mb-5">Cenník</h5>
        <table class="table table-borderless table-responsive table-striped mb-5">
            <thead class="bg-primary">
                <tr class="text-white">
                    <th></th>
                    <th>Názov</th>
                    <th>Dost. (L)</th>
                    <th>Objednané</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($list as $row) :
                ?>
                <tr>
                    <td style="width: 20%">
                        <?php if ($row->stockDetail->pic) : ?>
                            <img src="<?=  $row->stockDetail->getPicUrl() ?>"
                                 alt="<?= $row->stockDetail->pic->file_name ?>"
                                 class="img-fluid"
                                 width="100"
                                 height="100"
                            >
                        <?php endif; ?>
                    </td>
                    <td style="width: 30%">
                        <?= $row->stockDetail->getTitle() ?>
                    </td>
                    <td style="width: 10%"><?= $row->amount ?></td>
                    <td style="width: 25%">
                        <?php if ($row->price_1) : ?>
                            <div>
                                <input
                                        type="text"
                                        class="form-control w-25 d-inline mb-2 vo"
                                        data-psi="<?= $row->id ?>"
                                        data-up="<?= $row->price_1 ?>"
                                        data-u="1dl"
                                >
                                <span class="px-2"><?= $row->price_1 ?> &euro; / 1dl</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($row->price_04) : ?>
                            <div>
                                <input
                                        type="text"
                                        class="form-control w-25 d-inline mb-2 vo"
                                        data-psi="<?= $row->id ?>"
                                        data-up="<?= $row->price_04 ?>"
                                        data-u="0.4dl"
                                >
                                <span class="px-2"><?= $row->price_04 ?> &euro; / 0.4dl</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($row->price_5) : ?>
                            <div>
                                <input
                                        type="text"
                                        class="form-control w-25 d-inline mb-2 vo"
                                        data-psi="<?= $row->id ?>"
                                        data-up="<?= $row->price_5 ?>"
                                        data-u="0.5l"
                                >
                                <span class="px-2"><?= $row->price_5 ?> &euro; / 0.5l</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($row->price_075) : ?>
                            <div>
                                <input
                                        type="text"
                                        class="form-control w-25 d-inline mb-2 vo"
                                        data-psi="<?= $row->id ?>"
                                        data-up="<?= $row->price_10 ?>"
                                        data-u="0.75l"
                                >
                                <span class="px-2"><?= $row->price_075 ?> &euro; / 0.75l</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($row->price_10) : ?>
                            <div>
                                <input
                                        type="text"
                                        class="form-control w-25 d-inline mb-2 vo"
                                        data-psi="<?= $row->id ?>"
                                        data-up="<?= $row->price_10 ?>"
                                        data-u="1l"
                                >
                                <span class="px-2"><?= $row->price_10 ?? 0 ?> &euro; / 1l</span>
                            </div>
                        <?php endif; ?>
                        <?php if ($row->price_bottle) : ?>
                            <div>
                                <input
                                        type="text"
                                        class="form-control w-25 d-inline mb-2 vo"
                                        data-psi="<?= $row->id ?>"
                                        data-up="<?= $row->price_bottle ?>"
                                        data-u="fl."
                                >
                                <span class="px-2"><?= $row->price_bottle ?? 0 ?> &euro; / fl.</span>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <button class="btn btn-success" type="button" id="proc">Spracovať</button>
                    </td>
                    <td>Spolu</td>
                    <td><span id="total">0.00</span> &euro;</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-md-2 col-sm-1"></div>
</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$js = <<<JS

$('#n01').on('change', function() {
    $.ajax({
        url: '/promo/get-guest',
        method: 'POST',
        data: {
            $csrf,
            gname: $(this).val()
        },
        success: function (data) {
            if (data.status == 'ok') {
                $('#t00').html(data.guest.id);
                $('#n20').html(data.name);
                $('#n02').html(data.guest.balance);
                $('#n10').html(data.guest.status);
                $('#r01').val(data.guest.id);
            } else {
                $('#rr01').html(data.message);
            }
        }
    });
});

$('#proc').on('click', function() {
  let data = [];
  $('.vo').each(function() {
    if ($(this).val() > 0) {
      data.push({
        id: $(this).data('psi'),
        amount: $(this).val(),
        price: $(this).data('up'),
        unit: $(this).data('u')
      });
    }
  });
  if (data.length > 0) {
    $.ajax({
      url: '/promo/process-order',
      method: 'POST',
      data: {
        $csrf,
        qr: $('#t00').val(),
        items: data,
        gid: $('#r01').val(),
        total: $('#total').html()
      },
      success: function (data) {
        if (data.status == 'ok') {
          location.reload();
        } else {
            $('#rr01').html(data.message);
        }
      }
    });
  }
});

$('.vo').each(function() {
    $(this).on('change', function() {
        let total = 0;
        $('.vo').each(function() {
            let amount = $(this).val();
            let price = $(this).data('up');
            if (amount > 0) {
                total += amount * price;
            }
        });
        $('#total').html(total);
    });
});


JS;

$this->registerJS($js);





