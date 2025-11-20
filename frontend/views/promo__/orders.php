<?php
/**
 * @var $orders
 * @var $promoId
 */
use yii\helpers\Url;


?>

<div class="row mt-3">
    <div class="col text-end">
        <p><i class="bi bi-person"></i> Vítajte, <b><?= Yii::$app->user->getIdentity()->detail->getFullName() ?></b>.</p>
    </div>
</div>
<div class="row">
    <div class="col text-end">
        <?php if (Yii::$app->user->getIdentity()->location === 'vip'): ?>
        <button class="btn btn-primary" type="button" id="viporder"><i class="bi bi-stars"></i> Zadať VIP objednávku</button>
        <?php endif; ?>
        <button class="btn btn-warning" type="button" id="sklad">Sklad</button>
        <a href="<?= Url::to(['/promo/logout'])?>" class="btn btn-danger">Odhlásiť sa</a>
    </div>
</div>

<div class="row mt-5">
    <div class="col-4">
        <h5>Objednávky</h5>
        <ul class="mt-3 objednavky">
            <?= $this->render('order_list',['orders'=>$orders]) ?>
        </ul>
    </div>
    <div class="col-8">
        <p>
            <span id="ordtitle"></span> <span class="badge" id="ostatus"></span>
        </p>
        <div id="ordpanel">
            <input type="hidden" id="oid">
            <table class="table table-sm" id="t01">
                <thead>
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
            <button class="btn btn-danger d-none" type="button" id="pay">Zaplatiť</button>
        </p>
    </div>
</div>

<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="stockModalLabel">Sklad</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="vipOrderModal" tabindex="-1" aria-labelledby="vipOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="vipOrderModalLabel">VIP Objednávka</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" role="form">
                    <p class="mb-2" style="font-size: 0.8rem;color: red;">Objednané množstvo treba zadať v počte fliaš.</p>
                    <table class="table table-sm" id="t1900">
                        <thead>
                            <tr>
                                <th>Názov</th>
                                <th>Dostupné mn.</th>
                                <th>Objednané mn.</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavrieť</button>
                <button type="button" class="btn btn-primary" id="putorder">Objednať</button>
            </div>
        </div>
    </div>
</div>


<?php
$loadOrderUrl = Url::to(['/promo/load-orders?promo_id='.$promoId]);
$loadOrderDetail = Url::to(['/promo/load-order']);
$orderStatusChange = Url::to(['/promo/order-status-change']);
$getSkladUrl = Url::to(['/promo/stock-status']);
$csrf = "'" . Yii::$app->request->csrfParam ."':'". Yii::$app->request->getCsrfToken() ."'";
$userName = Yii::$app->user->getIdentity()->username;
$promoOrderUrl = Url::to(['/promo/vip-order-list']);
$finishVipOrder = Url::to(['/promo/finish-vip-order']);

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
             $('#proc').removeClass('d-none');
             $('#oid').val(res.oid);
             if ('{$userName}' !== res.locked) {
                 $('#process-btn').hide();
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
               $('#pay').removeClass('d-none');
          }
       });
    });
    
    $(document).on('click','#pay',function(){
        var oid = $('#oid').val(); 
       $.ajax({
           url: "{$orderStatusChange}",
           dataType: "json",
           data: { oid: oid, stat:'paid', {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'error') {
             console.log(res.message);
          } else {
               $('#ostatus').removeClass(res.old_class).addClass(res.status_color).html(res.status_text);
               $('#proc').addClass('d-none');
               $('#pay').addClass('d-none');
          }
       });
    });
    
    $(document).on('click','#sklad', function(){
        $.ajax({
           url: "{$getSkladUrl}",
           dataType: "json",
           data: { {$csrf} },
           type: "post"
        })
        .done(function(res){
            if (res.status == 'error') {
                console.log(res.message);
            } else {
                $('#stockModal .modal-dialog .modal-content .modal-body').html(res.table);
                $('#stockModal').modal('show');
            }
        });     
    });
    
    $(document).on('click', '#viporder', function(){
         $.ajax({
           url: "{$promoOrderUrl}",
           dataType: "json",
           data: { pid:{$promoId}, {$csrf} },
           type: "post"
        })
        .done(function(res){
            if (res.status == 'error') {
                console.log(res.message);
            } else {
                $('#t1900 tbody').empty().append(res.table);
                $('#vipOrderModal').modal('show');
            }
        });
        
    });
    
    $(document).on('click', '#putorder', function(){
        let c='';
        $('.vo').each(function(k,v){
            let d = $(v).data('promo-stock-item');
            let e = $(v).val();
            let up = $(v).data('unit-price');
            if (e>0) {
                c += d + '_' + e + '_' + up +'|';
            }
        });
         $.ajax({
           url: "{$finishVipOrder}",
           dataType: "json",
           data: { pid:{$promoId}, o: c, {$csrf} },
           type: "post"
        })
        .done(function(res){
            if (res.status == 'error') {
                console.log(res.message);
            } else {
                $('#vipOrderModal').modal('hide');
            }
        });
    });
    
JS;
$this->registerJS($js);