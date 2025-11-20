<?php
/**
 * @var $lang
 * @var $groups
 * @var $pricelist
 */

use yii\helpers\Url;
$this->title = $_GET['l'] == 'sk' ? 'Objednávka' : 'Megrendelés';
?>

<div class="row">
    <div class="col-md-3 col-sm-2">
    </div>
    <div class="col-md-6 col-sm-8">
        <form method="post" role="form" action="<?= Url::to(['/promo/finish-order']) ?>" id="frm01">
            <input type="hidden" name="Order" id="Order">
            <input type="hidden" name="orderKey" value="<?= $_GET['k']?>" id="odk">
            <input type="hidden" name="l" value="<?= $_GET['l'] ?>">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <div class="row mt-3">
                <div class="col-6">
                    <button type="button" id="order-history" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderHistoryModal">
                        <i class="bi bi-card-list"></i>&nbsp;
                        <?php
                        if ($lang == 'hu') {
                            echo "Rendelési előzmények";
                        } elseif ($lang == 'sk') {
                            echo "História objednávok";
                        }
                        ?>
                    </button>
                </div>
                <div class="col-6">
                    <button class="btn btn-success float-end" type="submit">
                        <?= $lang == 'hu' ? 'Megrendelem' : 'Objednám' ?>&nbsp;
                        <span class="badge text-bg-secondary" id="tot-cena">0,00 &euro;</span>
                    </button>
                </div>
            </div>
        </form>
        <div class="row mt-3">
            <div class="col">

                <?php foreach ($groups as $group): ?>
                    <?php
                    if (!array_key_exists($group->id,$pricelist)) {
                        continue;
                    }
                    ?>
                    <h4 class="mb-4 mt-4"><?= $group->getTitle($lang) ?></h4>
                    <?php foreach ($pricelist[$group->id] as $listItem): ?>
                        <article
                                class="it it0"
                                data-title="<?= $listItem->itemDetails->getTitle($lang) ?>"
                                data-id="<?= $listItem->id ?>"
                                data-combo="<?= $listItem->combo ?>"
                        >
                            <?php
                            $cart=[
                                '1dl_' . $listItem->price_1,
                                '0,04dl_' . $listItem->price_04,
                                '0,75dl_' . $listItem->price_075,
                                '0,5l_' . $listItem->price_5,
                                '1l_' .  $listItem->price_10,
                                ($lang == 'sk' ? 'fľaša' : 'üveg').'_'.$listItem->price_bottle
                            ];
                            ?>
                            <input type="hidden" id="c-<?= $listItem->id?>" value="<?= implode('|',$cart) ?>">
                            <input type="hidden" id="c0-<?= $listItem->id?>" class="dt0">
                            <div class="tit01">
                                <b class="nazov"><?= $listItem->itemDetails->getTitle($lang) ?></b>
                                <p style="margin:0; font-size: 0.8rem;" class="detaily-<?= $listItem->id ?>">
                                    <?php
                                    if ($listItem->itemDetails->alcohol>0) {
                                        echo "<span style='font-size: 0.8rem; color:#c0c0c0;'>Alkohol: {$listItem->itemDetails->alcohol}%</span>";
                                        echo "<br>";
                                    }
                                    echo $listItem->itemDetails->getDescription($lang);
                                    ?>
                                </p>
                                <p class="mt-2 cena">
                                    <?php
                                    $cena = [];
                                    if ($listItem->itemDetails->alcohol>0) {
                                        $cena[] = $listItem->price_04;
                                    } elseif (!$listItem->itemDetails->isBottleOrBundleSell()) {
                                        $cena[] = $listItem->price_1;
                                    }
                                    if ($listItem->itemDetails->isBundleSell()) {
                                        $cena[] = $listItem->price_bottle;
                                    } else {
                                        $cena[] = $listItem->price_bottle;
                                    }
                                    ?>
                                    <?= implode(' - ', $cena) ?> &euro;
                                </p>
                            </div>
                            <div class="tit02 obrazok">
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-2">
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="orderModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="qid">
                <p id="orderModalDescription" class="d-none"></p>
                <div class="row d-none" id="comb0">
                    <div class="col">
                        <div class="form-group">
                            <select id="comb1" class="form-select"></select>
                        </div>
                    </div>
                </div>
                <div class="row" id="comb2">
                    <div class="col-3">
                        <div class="form-group">
                            <select id="qty" class="form-select"></select>
                        </div>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control" id="mul" value="1" >
                    </div>
                    <div class="col-6 pt-1" id="cena" style="font-size: 16pt; text-align: right">
                        0,00 &euro;
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="add2cart"><?= $lang == 'sk' ? 'Pridať do objednávky' : 'Hozzáadás'?></button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="orderHistoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="orderHistoryModalLabel">
                    <?php
                    $title = $lang == 'sk' ? 'História objednávok' : 'Rendelési előzmények';
                    echo $title;
                    ?>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <?php
                    $title = $lang == 'sk' ? 'Zavrieť' : 'Bezárás';
                    echo $title;
                    ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam ."':'". Yii::$app->request->getCsrfToken() ."'";
$orderHistoryUrl = Url::to(['/promo/order-history']);
$comboTemplateUrl = Url::to(['/promo/combo-template']);
$emptyCart = $_GET['l'] == 'sk' ? 'Váš košík je prázdny!' : 'Az Ön kosara üres!';
$js = <<<JS
var total2pay = 0;

const formatter = new Intl.NumberFormat('sk-SK', {
    style:'currency',
    currency: 'EUR'    
});

$('.it0').click(function(){
    let id=$(this).data('id');
    let x = $('#c-'+id).val();
    let combo = $(this).data('combo');
    $('#qty').empty().append('<option value=""></option>');
    $('#qid').val(id);
    x = x.split('|');
    $(x).each(function (k,v){
           let y = v.split('_');
           if (y[1] >0) {
                $('#qty').append('<option value="'+y[1]+'">'+y[0]+'</option>');
           }
    });
    $('#orderModalLabel').html($(this).data('title')); 
    let d = ($('.detaily-'+id).html()).trim();
    if (d.length != 0) {
        $('#orderModalDescription').removeClass('d-none').html(d);
    }
    if (combo == 1) {
        $.ajax({
           url: "{$comboTemplateUrl}",
           dataType: "json",
           data: { iid: id, l:'{$_GET['l']}', {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'error') {
             console.log(res.message);
          } else {
              $('#comb1').empty().append(res.items);
              $('#comb0').removeClass('d-none');
              $('#comb2').addClass('mt-3');
          }
       });
    }
    $('#orderModal').modal('show');
});

$('#qty').change(function (){
   let v = $(this).val();
   let m = $('#mul').val();
   $('#cena').html(formatter.format(v*m));
});

$('#mul').keyup(function(){
    let v = $('#qty').val();
    let m = $(this).val();
    $('#cena').html(formatter.format(v*m));
});

$('#add2cart').click(function (){
    let qid = $('#qid').val();
    let q = $('#qty').val();
    let m = $('#mul').val();
    let s = $('#qty option:selected').text();
    let c = ($('#comb1').val() === undefined || $('#comb1').val() == null) ? '' : '_' + ($('#comb1').val());
    let c0 = $('#c0-'+qid).val();
    
    c0 = c0 + qid+'_'+s+'_'+m+'_'+q+c+'|';
    $('#c0-'+qid).val(c0);
    total2pay += q * m;
    $('#tot-cena').html(formatter.format(total2pay));
    
    $('#qty').empty().append('<option value></option>');
    $('#mul').val(1);
    $('#comb1').empty().append('<option value></option>');
    $('#cena').html(formatter.format(0));
    $('#orderModal').modal('hide');
});

$('#frm01').submit(function (){
   var order=''; 
   $('.dt0').each(function (k,v){
        let x = $(v).val();
        if (x !== '') {
            order += x;
        }
   });
   if (order === undefined || order.length ===0 || order === '') {
       alert('{$emptyCart}');
       return false;
   } 
   $('#Order').val(order);
   return true;
});

$('#order-history').click(function(){
    let odk = $('#odk').val();
    $.ajax({
           url: "{$orderHistoryUrl}",
           dataType: "json",
           data: { odk: odk, {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'error') {
             console.log(res.message);
          } else {
              $('#orderHistoryModal div.modal-body').empty().append(res.items);
          }
       });
});
JS;
$this->registerJS($js);
