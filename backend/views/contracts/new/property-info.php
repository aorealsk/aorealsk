<?php
/**
 * @var $nehnutelnost
 * @var $cislo_byt
 * @var $byt
 * @var $lv_data
 */
use backend\assets\RealAsset;
use yii\helpers\Url;
use common\models\Mesto;

$this->title="Nová zákazka";

$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css',['depends'=>RealAsset::class]);
$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css',['depends'=>RealAsset::class]);
$this->registerJSFile('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.full.min.js',['depends'=>RealAsset::class]);
$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.1/jquery-confirm.min.css',['depends'=>RealAsset::class]);
$this->registerJSFile('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.1/jquery-confirm.min.js',['depends'=>RealAsset::class]);

?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="<?=Url::to(['contracts/save-property-info'])?>" method="post">
                <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->csrfToken?>"/>
                <input type="hidden" name="Data[zmluva_id]" value="<?=$_GET['id']?>">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="mb-0 text-white">Identifikačné údaje</h4>
                    </div>
                    <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="d0">Súpisné č.</label>
                        <?php
                        $supis = '';
                        if (!is_null($nehnutelnost->supis_cis)) {
                            $supis = $nehnutelnost->supis_cis;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->supis_cis)) {
                            $supis = $byt['SUPIS_CISLO'];
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[supis_cis]" value="<?= $supis ?>" id="d0">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="d1">Evidovaná(é) na LV č.</label>
                        <?php
                        $list_vlast = '';
                        if (!is_null($nehnutelnost->list_vlast)) {
                            $list_vlast = $nehnutelnost->list_vlast;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->list_vlast)) {
                            $list_vlast = $lv_data['LV'];
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[list_vlast]" value="<?= $list_vlast ?>" id="d1">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="d2">Orient.č.</label>
                        <?php
                        $vchod = '';
                        if (!is_null($nehnutelnost->orient_cisl)) {
                            $vchod = $nehnutelnost->orient_cisl;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->orient_cisl) && isset($byt['VCHOD'])) {
                            $vchod = $byt['VCHOD'];
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[orient_cisl]" value="<?= $vchod ?>" id="d2">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="d3">Č. bytu (nebyt. priestoru)</label>
                        <?php
                        $cislo_bytu = '';
                        if (!is_null($nehnutelnost->cislo_byt)) {
                            $cislo_bytu = $nehnutelnost->cislo_byt;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->cislo_byt) && isset($byt['CISLO_BYT'])) {
                            $cislo_bytu = $byt['CISLO_BYT'];
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[cislo_byt]" value="<?= $cislo_bytu ?>" id="d3">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="d4">Parc.č.</label>
                        <?php
                        $parc_cislo = '';
                        if (!is_null($nehnutelnost->parc_cislo)) {
                            $parc_cislo = $nehnutelnost->parc_cislo;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->parc_cislo) && isset($lv_data['PARCELA_CISLO'])) {
                            $parc_cislo = $lv_data['PARCELA_CISLO'];
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[parc_cislo]" value="<?= $parc_cislo ?>" id="d4">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="obec">Obec</label>
                        <?php
                        $obec = '';
                        if (!is_null($nehnutelnost->mesto)) {
                            $obec = $nehnutelnost->mesto;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->mesto) && isset($lv_data['OBEC_TEXT'])) {
                            $obec = $lv_data['OBEC_TEXT'];
                        }
                        ?>
                        <select class="js-data-example-ajax form-select" name="Data[KU_obec_nazov]" id="obec">
                            <?php
                                if ($obec !== '') {
                            ?>
                                    <option value="<?= $obec ?>"><?= $obec ?></option>
                            <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="obec_kod">Kód obce</label>
                        <?php
                        $obec_cislo = '';
                        if (!is_null($nehnutelnost->obec_kod)) {
                            $obec_cislo = $nehnutelnost->obec_kod;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->obec_kod)) {
                            $obec_cislo = $lv_data['OBEC_CISLO'];
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[KU_obec_kod]" id="obec_kod" value="<?= $obec_cislo ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="d5">Ulica</label>
                        <?php
                        $ulica = '';
                        if (!is_null($nehnutelnost->ulica)) {
                            $ulica = $nehnutelnost->ulica;
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[KU_ulica]" value="<?= $ulica ?>" id="d5">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="psc">PSČ</label>
                        <?php
                        $psc = '';
                        if (!is_null($nehnutelnost->psc)) {
                            $psc = $nehnutelnost->psc;
                        } else if($cislo_byt != -1 && is_null($nehnutelnost->psc) && $lv_data['OBEC_TEXT']) {
                            $psc = Mesto::getPsc($lv_data['OBEC_TEXT']);
                        }
                        ?>
                        <select class="js-data-psc form-control" name="Data[psc]" id="psc">
                            <?php
                            if ($psc != '') {
                            ?>
                                <option value="<?= $psc?>"><?= $psc ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="KU-uzemie">Katastrálne územie</label>
                        <?php
                        $ku_text = '';
                        if (!is_null($nehnutelnost->kat_uzemie)) {
                            $ku_text = $nehnutelnost->kat_uzemie;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->kat_uzemie) && isset($lv_data['KU_TEXT'])) {
                            $ku_text = $lv_data['KU_TEXT'];
                        }
                        ?>
                        <input type="text" class="form-control" id="KU-uzemie" name="Data[KU_uzemie_nazov]" value="<?= $ku_text ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="KU-uzemie-kod">Kód kat. územia</label>
                        <?php
                        $ku_kod = '';
                        if (!is_null($nehnutelnost->kat_uzemie_kod)) {
                            $ku_kod = $nehnutelnost->kat_uzemie_kod;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->kat_uzemie_kod) && isset($lv_data['KU_KOD'])) {
                            $ku_kod = $lv_data['KU_KOD'];
                        }
                        ?>
                        <input type="text" class="form-control" id="KU-uzemie-kod" name="Data[KU_uzemie_kod]" value="<?= $ku_kod ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="KU-okres">Okres</label>
                        <?php
                        $okres = '';
                        if (!is_null($nehnutelnost->okres)) {
                            $okres = $nehnutelnost->okres;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->okres) && isset($lv_data['OKRES_TEXT'])) {
                            $okres = $lv_data['OKRES_TEXT'];
                        }
                        ?>
                        <input type="text" class="form-control" id="KU-okres" name="Data[KU_okres_nazov]" value="<?= $okres ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="KU-okres-kod">Kód okresu</label>
                        <?php
                        $kod_okres = '';
                        if (!is_null($nehnutelnost->okres_kod)) {
                            $kod_okres = $nehnutelnost->okres_kod;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->okres_kod) && isset($lv_data['OKRES_CISLO'])) {
                            $kod_okres = $lv_data['OKRES_CISLO'];
                        }
                        ?>
                        <input type="text" class="form-control" id="KU-okres-kod" name="Data[KU_okres_kod]" value="<?= $kod_okres ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="KU-krajina">Krajina</label>
                        <?php
                        $krajina = '';
                        if (!is_null($nehnutelnost->stat)) {
                            $krajina = $nehnutelnost->stat;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->stat) && isset($lv_data['OBEC_TEXT'])) {
                            $krajina = Mesto::getKrajina($lv_data['OBEC_TEXT']);
                        }
                        ?>
                        <input type="text" class="form-control" id="KU-krajina" name="Data[krajina]" value="<?= $krajina ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="KU-kraj">Kraj</label>
                        <?php
                        $kraj = '';
                        if (!is_null($nehnutelnost->kraj)) {
                            $kraj = $nehnutelnost->kraj;
                        } else if ($cislo_byt != -1 && is_null($nehnutelnost->kraj) && isset($lv_data['OBEC_TEXT'])) {
                            $kraj = Mesto::getKraj($lv_data['OBEC_TEXT']);
                        }
                        ?>
                        <input type="text" class="form-control" id="KU-kraj" name="Data[kraj]" value="<?= $kraj ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="gps-lat">GPS - lat</label>
                        <?php
                        $gps_lat = '';
                        if (!is_null($nehnutelnost->gps_lat)) {
                            $gps_lat = $nehnutelnost->gps_lat;
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[gps_lat]" id="gps-lat" value="<?= $gps_lat ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label" for="gps-long">GPS - long</label>
                        <?php
                        $gps_long = '';
                        if (!is_null($nehnutelnost->gps_long)) {
                            $gps_long = $nehnutelnost->gps_long;
                        }
                        ?>
                        <input type="text" class="form-control" name="Data[gps_long]" id="gps-long" value="<?= $gps_long ?>">
                    </div>
                </div>
            </div>
                </div>

                <div class="card">
                    <div class="form-actions">
                        <div class="card-body">
                            <button type="submit" class="btn btn-success text-white"> <i class="fa fa-arrow-circle-right"></i>&nbsp;Pokračovať</button>
                            <button type="button" class="btn btn-dark text-white" onclick="redirectTo('<?= Url::to(['/contracts'])?>')" ><i class="fa fa-times-circle"></i>&nbsp;Zrušiť</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<?php

$url = Url::to(['/contracts/ajax-get-obec']);
$urlDetail = Url::to(['/contracts/ajax-get-obec-detail']);
$urlPSC = Url::to(['/contracts/ajax-get-psc']);

$js = <<< JS
    redirectTo = function(url)
    {
        $.confirm({
            title: 'Skončiť',
            content: 'Naozaj chcete skončiť?',
            buttons: {
                ano: function () {
                    window.location.replace(url);
                },
                nie: function () {
                }
            }
        });
    }
    
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position){
            $('#gps-lat').val(position.coords.latitude);
            $('#gps-long').val(position.coords.longitude);
        });
    }
    
    $('#psc').select2({
        theme: "bootstrap",
        minimumInputLength: 3,
        tags:true,
        ajax : {
            url: '{$urlPSC}',
            dataType: 'json',
            delay: 250,
            data: function(params){
                return {
                    q: params.term
                }
            },
            processResults: function(data) {
                return {
                    results: $.map( data.items, function(val,ind){ return {id: ind, text: val};})   
                };
            }
        }
    });

    $('.js-data-example-ajax').select2({
        theme: "bootstrap",
        ajax: {
            url: '{$url}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: $.map( data.items, function(val,ind){ return {id: ind, text: val};})   
                };
            }
        },
        minimumInputLength: 3
    });
    
    $('.js-data-example-ajax').on('select2:select',function(){
        var town = $('.js-data-example-ajax').val();
        $.ajax({
            url: '{$urlDetail}',
            dataType: 'json',
            data: {tid: town},
            success: function(data){
               var details = data.details;
               if(details.hasOwnProperty('id')) {
                   $('#obec').empty().append($('<option></option>',{value:details.nazov_obce, text:details.nazov_obce}));
                   $('#obec_kod').val(details.kod_obce);
                   $('#psc').empty().append($('<option></option>',{id:details.psc,text:details.psc}));
                   $('#KU-uzemie').val(details.KU_nazov);
                   $('#KU-uzemie-kod').val(details.KU_kod);
                   $('#KU-okres').val(details.okres);
                   $('#KU-okres-kod').val(details.okres_kod);
                   $('#KU-krajina').val(details.krajina);
                   $('#KU-kraj').val(details.kraj);
               }
            }
        });
    });
    
    $('#psc').on('select2:select',function(){
         var town = $('#psc').val();
        $.ajax({
            url: '{$urlDetail}',
            dataType: 'json',
            data: {tid: town},
            success: function(data){
               var details = data.details; 
               if(details.hasOwnProperty('id')) {
                   $('#obec').empty().append($('<option></option>',{value:details.nazov_obce, text:details.nazov_obce}));
                   $('#obec_kod').val(details.kod_obce);
                   $('#psc').empty().append($('<option></option>',{value:details.psc,text:details.psc}));
                   $('#KU-uzemie').val(details.KU_nazov);
                   $('#KU-uzemie-kod').val(details.KU_kod);
                   $('#KU-okres').val(details.okres);
                   $('#KU-okres-kod').val(details.okres_kod);
                   $('#KU-krajina').val(details.krajina);
                   $('#KU-kraj').val(details.kraj);
               }
            }
        });
    });
JS;

$this->registerJS($js);

