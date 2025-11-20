<?php

/**
 * @var $orders
 * @var $promoId
 */
use yii\helpers\Url;

?>

<div class="row mt-3">
    <div class="col text-end">
        <p><i class="bi bi-person"></i> Vítajte, <b><?= Yii::$app->user->getIdentity()->getFullName() ?></b>.</p>
    </div>
</div>
<div class="row">
    <div class="col text-end">
        <a href="<?= Url::to(['/promo/home']) ?>" class="btn btn-primary">Späť na úvod</a>
        <!--<button class="btn btn-success" type="button" id="sklad">Sklad</button>-->
        <a href="<?= Url::to(['/promo/logout'])?>" class="btn btn-danger">Odhlásiť sa</a>
    </div>
</div>

<div class="row mt-5">
    <div class="col-4">
        <h5>Objednávky</h5>
        <ul class="mt-3 objednavky">
            <?= $this->render('order_list', ['orders' => $orders]) ?>
        </ul>
    </div>
    <div class="col-8">
        <p>
            <span id="ordtitle"></span> <span class="badge" id="ostatus"></span>
        </p>
        <div id="ordpanel">
            <input type="hidden" id="oid">
            <table class="table table-sm table-striped table-responsive" id="t01">
                <thead class="bg-primary text-white">
                    <tr>
                        <th style="width:55%">Položka</th>
                        <th>MJ</th>
                        <th>Množstvo</th>
                        <th>Jednotková cena (&euro;)</th>
                        <th>Cena (&euro;)</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
        </div>
        <p id="process-btn" class="d-block">
            <button class="btn btn-warning d-none" type="button" id="proc">Spracovať</button>
            <button class="btn btn-primary d-none" type="button" id="compl">Skompletizovať</button>
        </p>
    </div>
</div>

<?php
$loadOrderUrl = Url::to(['/promo/load-orders?promo_id=' . $promoId]);
$loadOrderDetail = Url::to(['/promo/load-order']);
$orderStatusChange = Url::to(['/promo/order-status-change']);
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$userName = Yii::$app->user->getIdentity()->user_name;


$js = <<<JS
   
    const runTimer = () => {
      timer = window.setInterval(
        () => {
            $('.objednavky').load('{$loadOrderUrl}').fadeIn('slow');
        }, 10000);
    }
    
    runTimer();

    $(document).on('click','.objednavky>li', function(){
       var oid = $(this).data('orderid'); 
       $.ajax({
           url: "{$loadOrderDetail}",
           dataType: "json",
           data: { oid: oid, {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'error') {
             console.log(res.message);
          } else {
             $('#ordtitle').html(res.title);
             $('#ostatus').addClass(res.status_color).html(res.status_text);
             $('#t01 tbody').empty().append(res.items_tbody);
             $('#t01 tfoot').empty().append(res.items_tfoot);
             $('#oid').val(res.oid);
             if ('{$userName}' !== res.locked) {
                 $('#process-btn').hide();
             } else {
                 $('#process-btn').show();
                if (res.ostatus == 'new') {
                    $('#proc').removeClass('d-none');
                } else if (res.ostatus == 'processing') {
                    $('#proc').addClass('d-none');
                    $('#compl').removeClass('d-none');
                }
             }
          }
       });
    });
    
    $(document).on('click','#proc',function(){
        var oid = $('#oid').val(); 
       $.ajax({
           url: "{$orderStatusChange}",
           dataType: "json",
           data: { oid: oid, stat:'processing', {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'error') {
             console.log(res.message);
          } else {
               $('#ostatus').removeClass(res.old_class).addClass(res.status_color).html(res.status_text);
               $('#proc').addClass('d-none');
               $('#compl').removeClass('d-none');
          }
       });
    });
    
    $(document).on('click','#compl',function(){
        var oid = $('#oid').val(); 
       $.ajax({
           url: "{$orderStatusChange}",
           dataType: "json",
           data: { oid: oid, stat:'completed', {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'error') {
             console.log(res.message);
          } else {
               $('#ostatus').removeClass(res.old_class).addClass(res.status_color).html(res.status_text);
               $('#proc').addClass('d-none');
               $('#compl').addClass('d-none');
          }
       });
    });
    
JS;
$this->registerJS($js);
