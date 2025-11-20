<?php

use yii\helpers\Url;

/**
 * @var $guest_registered
 * @var $guests
 * @var $guest_confirmed
 */

$this->registerJsFile('https://cdn.datatables.net/2.0.5/js/dataTables.min.js');
$this->registerJsFile('https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.min.js');
$this->registerCssFile('https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap5.min.css');
$this->registerCssFile('https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.min.css');
$this->registerJsFile('https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js');

?>
<div class="row p-3">
    <div class="col-md-2 col-m-1"></div>
    <div class="col-md-8 col-sm-10">
        <h1 class="text-center mb-5">Vendéglista</h1>


        <div class="mb-5">
            <label class="form-label">Keresés</label>
            <input type="text" class="form-control" id="n01">
        </div>

        <div class="row mb-5">
            <div class="col-sm-12 col-md-6">
                <div class="mini-stat clearfix bg-twitter rounded">
                    <div class="mini-stat-info">
                        <span id="t1"><?= $guest_registered ?></span>
                        Előregisztrált vendégek száma
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="mini-stat clearfix bg-bitbucket rounded">
                    <div class="mini-stat-info">
                        <span id="t2"><?= $guest_confirmed ?></span>
                        Aktív vendégek száma
                    </div>
                </div>
            </div>
        </div>

        <table id="tbguests" class="table table-responsive table-bordered mb-5 table-striped">
            <thead>
                <tr>
                    <th>Megr. sz.</th>
                    <th>Név</th>
                    <th>Ülőhely</th>
                    <th>Szül. dátum</th>
                    <th>Kontakt</th>
                    <th>Státusz</th>
                </tr>
            </thead>
            <tbody>
            <?php
            /**
             * @var $guests array
             */
            ?>
            <?= $this->render('guests_tbody', ['guests' => $guests]) ?>
            </tbody>
        </table>

        <a href="<?= Url::to(['/promo/home']) ?>"
           class="btn btn-primary d-flex justify-content-center col-6 mx-auto mb-5">
            Vissza a főoldalra
        </a>
    </div>
</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$js = <<<JS
$('#n01').on('keyup', function() {
    var search = $(this).val();
    $.ajax({
        url: '/promo/search-guests',
        type: 'POST',
        data: {qname: search, {$csrf}},
        success: function(data) {
            if (data.status === 'error') {
                $('#tbguests tbody').html('<tr><td colspan="6" class="text-center">Nincs találat</td></tr>');
            } else {
                $('#tbguests tbody').html(data.tbody); 
            }
        }
    });
});
$   (document).on('change',
 '.x01',
    function() {
    var guestId = $(this).data('xid');
    var status = $(this).prop('checked') ? 'confirmed' : 'pending';
    $.ajax({
        url: '/promo/change-status',
        type: 'POST',
        data: {guest_id: guestId, status: status, {$csrf}}, 
        success: function(data) {
            let x = $('#r' + guestId);
            if (x.hasClass('green-row')) {
                x.removeClass('green-row');
                $('#t2').text(parseInt($('#t2').text()) - 1);
                $('#t1').text(parseInt($('#t1').text()) + 1);
            } else {
                x.addClass('green-row');
                $('#t2').text(parseInt($('#t2').text()) + 1);
                $('#t1').text(parseInt($('#t1').text()) - 1);
            }
        }
    });
});
JS;
$this->registerJs($js);

$css = <<<CSS

.green-row {
    background-color: #d4edda;
}

.rounded {
  border-radius: 10px !important;
}

.mini-stat {
  padding: 15px;
  margin-bottom: 20px;
}

.mini-stat-icon {
  width: 60px;
  height: 60px;
  display: inline-block;
  line-height: 60px;
  text-align: center;
  font-size: 30px;
  background: none repeat scroll 0% 0% #EEE;
  border-radius: 100%;
  float: left;
  margin-right: 10px;
  color: #FFF;
}

.mini-stat-info {
  font-size: 12px;
  padding-top: 2px;
}

span, p {
  color: white;
}

.mini-stat-info span {
  display: block;
  font-size: 30px;
  font-weight: 600;
  margin-bottom: 5px;
  margin-top: 7px;
}

.bg-twitter {
  background-color: #00a0d1 !important;
  border: 1px solid #00a0d1;
  color: white;
}

.fg-twitter {
  color: #00a0d1 !important;
}

.bg-bitbucket {
  background-color: #205081 !important;
  border: 1px solid #205081;
  color: white;
}

.fg-bitbucket {
  color: #205081 !important;
}

CSS;
$this->registerCss($css);





