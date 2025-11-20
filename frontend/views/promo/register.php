<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app','Registrácia');
$this->params['breadcrumbs'][] = $this->title;
?>

<main class="site-login">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url(/images/contact-us-banner-1.jpg);" width="1600" height="400"></canvas>
        <div class="page-border container-default d-block position-absolute mx-auto">
            <div class="page_title_line_left d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
            <div class="page_title_line_right d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
        </div>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true" data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                        <strong><?= Html::encode($this->title) ?></strong>				</h1>
                </div>
            </div>
        </div>

        <div class="breadcrumbs-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <?=
                        Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section id="contact-section-one">
        <div class="contactus-form" style="margin-top: 0px; width: 90%; margin-bottom: 50px;">
            <div role="form" class="wpcf7" id="wpcf7-f2130-o1" lang="en-US" dir="ltr">
                <div class="screen-reader-response"></div>
                <div style="margin: auto; width: 70%;">
                    <div id="lang01" style="margin-top: 40px;">
                        <p data-lang="hu" class="lang-sel">Magyar</p>
                        <p data-lang="sk" class="lang-sel">Slovenský</p>
                    </div>

                    <form method="post" action="<?= Url::to(['/promo/finish-register']) ?>" style="margin-top:40px" id="frm0102">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                        <input type="hidden" name="Register[valid_lang]" id="validlang">
                        <input type="hidden" name="Register[slug]" value="<?= $_GET['slug'] ?>">
                        <div class="alert alert-danger" role="alert" id="al01" style="display: none">
                        </div>
                        <div id="frm-sk" style="display: none;">
                            <p>
                                Vítame Vás na registračnej stránke digitálneho nápojového listu Fašiangového bálu. Pre lepší užívateľský zážitok prosíme, aby ste
                                nasledujúci formulár vyplnili čo najpodrobnejšie.
                                <br><br>
                                Prajeme Vám príjemnú zábavu!
                            </p>
                            <div class="form-group row" style="margin-top:30px">
                                <div class="col-sm-6">
                                    <label class="form-label">Meno:<span class="redstar">*</span></label>
                                    <input type="text" name="Register[sk][name_first]" class="form-control req">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Priezvisko:<span class="redstar">*</span></label>
                                    <input type="text" name="Register[sk][name_last]" class="form-control req">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label class="form-label">Email:<span class="redstar">*</span></label>
                                    <input type="text" name="Register[sk][email]" class="form-control req" placeholder="meno@mail.com">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Mobil:</label>
                                    <input type="text" name="Register[sk][phone]" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label class="form-label">Dátum narodenia:</label>
                                    <input type="text" name="Register[sk][birth_date]" class="form-control" placeholder="Pr. 31.12.1999">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Číslo lístka:<span class="redstar">*</span></label>
                                    <input type="text" name="Register[sk][ticket_num]" class="form-control req" >
                                </div>
                            </div>
                            <div class="form-group row" style="margin-top: 30px;">
                                <div class="col-sm-12">
                                    <input type="checkbox" name="Register[sk][data_protection]" class="req">
                                    Súhlasím so správou, spracovaním a uchovaním mojich osobných údajov.<span class="redstar">*</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <input type="checkbox" name="Register[sk][newsletter]" checked>
                                    Tímto dávam súhlas na odber newsletterov od spoločnosti ALPHA-OMEGA REAL & CONSULTING s.r.o.
                                </div>
                            </div>
                            <div class="form-group row" style="margin-top: 30px;">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary" id="runsk01">Registrujem sa</button>
                                </div>
                            </div>
                        </div>
                        <div id="frm-hu" style="display: none;">
                            <p>
                                Üdvözöljük a Farsangi Bál digitális itallap regisztrációs oldalán. A magasabb felhasznalói élmény eléréséhez kérjük minél
                                részletesebben töltse ki a regisztrációt.<br><br>
                                Jó szórakozást kívánunk!
                            </p>
                            <div class="form-group row" style="margin-top: 30px;">
                                <div class="col-sm-6">
                                    <label class="form-label">Vezetéknév:<span class="redstar">*</span></label>
                                    <input type="text" name="Register[hu][name_last]" class="form-control req1">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Keresztnév:<span class="redstar">*</span></label>
                                    <input type="text" name="Register[hu][name_first]" class="form-control req1">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label class="form-label">Email:<span class="redstar">*</span></label>
                                    <input type="text" name="Register[hu][email]" class="form-control req1" placeholder="meno@mail.com">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Mobil:</label>
                                    <input type="text" name="Register[hu][phone]" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label class="form-label">Született:</label>
                                    <input type="text" name="Register[hu][birth_date]" class="form-control" placeholder="Pl. 1999.12.31">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Belépőjegy sorszáma:<span class="redstar">*</span></label>
                                    <input type="text" name="Register[hu][ticket_num]" class="form-control req1">
                                </div>
                            </div>
                            <div class="form-group row" style="margin-top: 30px;">
                                <div class="col-sm-12">
                                    <input type="checkbox" name="Register[hu][data_protection]" class="req1">
                                    Hozzájárulok személyes adataim kezeléséhez, feldolgozásához és tárolásához.<span class="redstar">*</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <input type="checkbox" name="Register[hu][newsletter]" checked>
                                    Feliratkozom az ALPHA-OMEGA REAL & CONSULTING s.r.o. hírlevelére
                                </div>
                            </div>
                            <div class="form-group row" style="margin-top: 30px;">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary" id="runhu01">Regisztrálok</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="clear"></div>
    </section>
</main>

<?php
$css = <<<CSS
    p.lang-sel {
        border: 1px solid #c49444;
        padding: 20px;
        cursor: pointer;
        border-radius: 10px;
        text-align: center;
        color: #c49444;
        transition: all 0.5s linear;
    }
    p.lang-sel:hover {
        color: white;
        background-color: #c49444;
    }
    span.redstar{
        color: red;
        font-size: 1.2em;
    }
CSS;
$this->registerCss($css);

$js = <<<JS
    $('.lang-sel').on('click', function (){
        let l = $(this).data('lang');
        $('#lang01').slideUp();
        $('#frm-'+l).slideDown();
    });
    $('#frm0102').on('submit', function(e) {
        var res = true;
        if ($('#frm-hu').is(':visible')) {
            $('.req1').each(function(index, value){
                if ($(value).is('input:text')) {
                    res = $(value).val() != '';    
                }
                if ($(value).is('input:checkbox')) {
                    res = $(value).is(':checked');
                }
            });
            if (!res) {
                $('#al01').html('Kérem töltse ki az összes <b>piros csillaggal</b> jelölt mezőt.').show();
            }
            $('#validlang').val('hu');
        } 
        if ($('#frm-sk').is(':visible')) {
            $('.req').each(function(index, value){
                if ($(value).is('input:text')) {
                    res = $(value).val() != '';    
                }
                if ($(value).is('input:checkbox')) {
                    res = $(value).is(':checked');
                }
            });
            console.log(res);
            if (!res) {
               $('#al01').html('Prosím vyplňte všetky políčka označené <b>červenou hviezdou</b>.').show();
            }
            $('#validlang').val('sk');
        }
        
        return res;    
    });
    
    $('.req').on('keyup', function (){
       if ($('#al01').is(':visible')) {
           $('#al01').fadeOut();
       } 
    });
    
    $('.req1').on('keyup', function (){
       if ($('#al01').is(':visible')) {
           $('#al01').fadeOut();
       } 
    });
    
JS;
$this->registerJs($js);