<?php

/**
 * @var $order_id string
 * @var $degrees array
 * @var $cust_docs array
 */

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;

$qrUrl  = Yii::getAlias('@web') . '/frontend/views/financial-request/asset/finreq_qrcode.png';
$this->title = Yii::t('finreq', 'Rezervácia termínu');
$this->registerCSSFile('@web/css/customer/registration.css?v=0.1', ['depends' => AppAsset::class]);
$this->registerCSSFile(
    "https://fonts.googleapis.com/icon?family=Material+Icons",
    ['depends' => AppAsset::class]
);
$this->registerJSFile("https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js");
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            //initialView: 'dayGridMonth',
            initialView: 'timeGridWeek',
            locale: 'sk',
            buttonText: {
                today: 'Dnes',
                month: 'Mesiac',
                week: 'Týždeň',
                day: 'Deň',
                list: 'Zoznam'
            },
            headerToolbar: {
                left: 'prev today',
                center: 'title',
                right: 'next'
            }
        });
        calendar.render();
        calendar.on('dateClick', function(info) {
            alert('clicked on ' + info.dateStr);
        });
    });
</script>

<main class="site-fin-quest" style="padding-bottom: 2rem;">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url('/images/header-bg2.jpg');" width="1600" height="400"></canvas>
        <div class="page-border container-default d-block position-absolute mx-auto">
            <div class="page_title_line_left d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible"
                 data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s"
                 data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
            <div class="page_title_line_right d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible"
                 data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s"
                 data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
        </div>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <h1 class="entry-title animated fadeInDown visible"
                        data-aios-reveal="true" data-aios-animation="fadeInDown"
                        data-aios-animation-delay="0.3s" data-aios-animation-reset="false"
                        data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                        <strong><?= Html::encode($this->title) ?></strong>
                    </h1>
                </div>
                <?php
                    // QR code under the title (embedded from local /asset/ folder next to this view)
                    $qrPath = __DIR__ . '/asset/finreq_qrcode.png';
                        if (file_exists($qrPath)) {
                        $qrData = base64_encode(file_get_contents($qrPath));
                        echo Html::img('data:image/png;base64,' . $qrData, [
                            'alt'   => 'Financial Request QR code',
                            'class' => 'd-block mx-auto my-3',
                            'style' => 'max-width:180px;height:auto;'
                            ]);
                        } else {
                            echo '<!-- QR code not found: ' . Html::encode($qrPath) . ' -->';
                        }
                    ?>
            </div>
        </div>
    <div class="breadcrumbs-container">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <?= Breadcrumbs::widget([
                 'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]); ?>
          </div>
        </div>
      </div>
    </div>

        <form action="<?= Url::to(['/finantial-request/save-form']) ?>" method="post" id="hpt-frm">
            <input
                type="hidden"
                name="<?= Yii::$app->request->csrfParam ?>"
                value="<?= Yii::$app->request->getCsrfToken() ?>">
            <input type="hidden" name="F[hpt]">
            <div class="req-container">
                <section id="app_req">
                    <h3><?= Yii::t('finreq', 'Služby') ?></h3>
                    <p><?= Yii::t('finreq', 'Chcem...'); ?></p>

                    <!-- QR ribbon at the very top-center of the content (not in the banner) -->
                        <div class="qr-ribbon" role="complementary" aria-label="Finančný dotazník – QR kód">
                            <figure class="qr-card">
                                <img src="<?= Html::encode($qrUrl) ?>" alt="Finančný dotazník – QR kód">
                                    <figcaption><small>aoreal.sk/financny-dotaznik</small></figcaption>
                            </figure>
                        </div>

                    <!-- (optional) small spacer if you need extra air above the form -->
                        <div class="qr-spacer" aria-hidden="true"></div>


                    <ul class="req-list mb-3">
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0001">&nbsp;
                            <?= Yii::t('finreq', 'si dať poradiť') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0020">&nbsp;
                            <?= Yii::t('finreq', 'kúpiť nehnuteľnosť') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0020">&nbsp;
                            <?= Yii::t('finreq', 'predať nehnuteľnosť') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0021">&nbsp;
                            <?= Yii::t('finreq', 'prenajať nehnuteľnosť') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0022">&nbsp;
                            <?= Yii::t('finreq', 'prenajímať nehnuteľnosť') ?>
                        </li>
                        <li id="t2">
                            <input type="checkbox" class="ri" name="F[serv][]" value="sx0002">&nbsp;
                            <?= Yii::t('finreq', 'požiadať o hypotéku') ?>
                        </li>
                        <li id="t3">
                            <input type="checkbox" class="ri" name="F[serv][]" value="sx0003">&nbsp;
                            <?= Yii::t('finreq', 'refinancovať hypotéku') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0004">&nbsp;
                            <?= Yii::t('finreq', 'požiadať o spotrebný úver') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0005">&nbsp;
                            <?= Yii::t('finreq', 'financovať firmu') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0006">&nbsp;
                            <?= Yii::t('finreq', 'lízing') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0007">&nbsp;
                            <?= Yii::t('finreq', 'konsolidovať svoje staré úvery') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0008">&nbsp;
                            <?= Yii::t('finreq', 'bankový účet') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0009">&nbsp;
                            <?= Yii::t('finreq', 'kreditnú kartu') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0010">&nbsp;
                            <?= Yii::t('finreq', 'pravideľne investovať') ?>
                        </li>
                        <li>
                            <input type="checkbox" name="F[serv][]" value="sx0011">&nbsp;
                            <?= Yii::t('finreq', 'jednorázovo investovať') ?>
                        </li>
                    </ul>
                </section>

                <section>
                    <h3><?= Yii::t('finreq', 'Kedy Vás môžme kontaktovať?') ?></h3>
                    <div id='calendar' style="margin-top: 20px;"></div>
                </section>

                <section id="client-contact">
                    <h3><?= Yii::t('finreq', 'Kontaktné údaje') ?></h3>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">
                            <?= Yii::t('finreq', 'Meno') ?>:
                        </label>
                        <div class="col-md-9 col-xs-10">
                            <input type="text" class="form-control" name="f[name_first]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">
                            <?= Yii::t('finreq', 'Priezvisko') ?>:
                        </label>
                        <div class="col-md-9 col-xs-10">
                            <input type="text" class="form-control" name="F[name_last]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">
                            <?= Yii::t('finreq', 'Email') ?>:
                        </label>
                        <div class="col-md-9 col-xs-10">
                            <input type="email" class="form-control" value="@" name="F[email]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">
                            <?= Yii::t('finreq', 'Mobil') ?>:
                        </label>
                        <div class="col-md-3 col-xs-4">
                            <select class="form-control dropdown" name="F[mobile][code]">
                                <?php
                                /** @var $staty */
                                foreach ($staty as $stat) {
                                    ?>
                                    <option value="00<?= $stat->predvolba ?>">
                                        <?= $stat->iso_kod ?> (+<?= $stat->predvolba ?>)
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <input type="tel" class="form-control" name="F[mobile][number]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">
                            <?= Yii::t('app', 'Pevná linka') ?>:
                        </label>
                        <div class="col-md-3 col-xs-4">
                            <select class="form-control dropdown" name="F[landline][code]">
                                <?php
                                /** @var $staty */
                                foreach ($staty as $stat) {
                                    ?>
                                    <option value="00<?= $stat->predvolba ?>">
                                        <?= $stat->iso_kod ?> (+<?= $stat->predvolba ?>)
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <input type="tel" class="form-control" name="F[landline][number]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">
                            <?= Yii::t('finreq', 'Fax') ?>:</label>
                        <div class="col-md-3 col-xs-4">
                            <select class="form-control dropdown" name="F[fax][code]">
                                <?php
                                /** @var $staty */
                                foreach ($staty as $stat) {
                                    ?>
                                    <option value="00<?= $stat->predvolba ?>">
                                        <?= $stat->iso_kod ?> (+<?= $stat->predvolba ?>)
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <input type="tel" class="form-control" name="F[fax][number]">
                        </div>
                    </div>
                </section>

                <section id="src-from">
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select name="F[src]" class="form-control dropdown xi">
                                <option value=""><?= Yii::t('app', 'Odkiaľ ste o nás dozvedeli?') ?></option>
                                <option value="facebook"><?= Yii::t('app', 'Facebook') ?></option>
                                <option value="twitter"><?= Yii::t('app', 'Twitter') ?></option>
                                <option value="linkedin"><?= Yii::t('app', 'Linkedin') ?></option>
                                <option value="refcode"><?= Yii::t('app', 'Od priateľa/známeho') ?></option>
                                <option value="nodef"><?= Yii::t('app', 'Iné') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="other-src" style="display: none">
                        <div class="col-md-12 col-xs-12">
                            <input type="text"
                                   placeholder="<?= Yii::t('finreq', 'Sem napíšte odkiaľ ste sa o nás dozvedeli') ?>"
                                   class="form-control"
                                   name="F[src_other]"
                            >
                        </div>
                    </div>
                    <div class="form-group row" id="referal-code" style="display: none">
                        <div class="col-md-12 col-xs-12">
                            <input type="text"
                                   placeholder="<?= Yii::t('finreq', 'Referal kód') ?>"
                                   class="form-control"
                                   name="F[ref_code]"
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-xs-6">
                            <select class="form-control dropdown" id="call-type" name="F[call_type]">
                                <option value=""><?= Yii::t('app', 'Preferovaný spôsob kontaktu') ?></option>
                                <option value="cont-vid"><?= Yii::t('app', 'Video hovor') ?></option>
                                <option value="cont-phone"><?= Yii::t('app', 'Audio hovor') ?></option>
                                <option value="written"><?= Yii::t('app', 'Písomne') ?></option>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <select class="form-control dropdown" id="call-source" name="F[call_src]">
                            </select>
                        </div>
                    </div>
                </section>



                <section id="gdpr_newsletter">
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12 mt-3">
                            <label for="cl-news" class="form-label">
                                <input type="checkbox" id="cl-news" checked>
                                &nbsp;
                                <?= Yii::t('app', 'Mám záujem o zasielanie noviniek e-mailom') ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-label">
                                <input type="checkbox" id="cl-consent">
                                &nbsp;
                                <?= Yii::t('app', 'Súhlasím s použitím osobných údajov na marketingové účely') ?>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- QR ribbon at the very top-center of the content (not in the banner) -->
                <div class="qr-ribbon" role="complementary" aria-label="Finančný dotazník – QR kód">
                    <figure class="qr-card">
                        <img src="<?= Html::encode($qrUrl) ?>" alt="Finančný dotazník – QR kód">
                            <figcaption><small>aoreal.sk/financny-dotaznik</small></figcaption>
                    </figure>
                </div>

                <!-- (optional) small spacer if you need extra air above the form -->
                <div class="qr-spacer" aria-hidden="true"></div>

                <section>
                    <p class="consent-note mt-1 text-justify">
                        Stlačením „Odoslať“ potvrdzujem, že som sa oboznámil(a) so Všeobecnými
                        obchodnými podmienkami spoločnosti, prečítal(a) som ich a porozumel(a) ich
                        obsahu a v celom rozsahu s nimi súhlasím.
                    </p>
                    <p class="consent-note text-justify">
                        Poskytnutím svojich osobných údajov a/alebo údajov Tretej osoby Poskytovateľovi
                        spoločnosti
                        <span class="aoreal">ALPHA-OMEGA REAL & CONSULTING s. r. o.</span>, <strong>so sídlom
                            Černyševského 10, 851 01 Bratislava - mestská časť Petržalka</strong>,
                        IČO: 51 81 7594, zapísaná v Obchodnom registri Okresného súdu Bratislava I, oddiel:
                        Sro,
                        vložka č. 129875/B (Poskytovateľ),
                        týmto ako Užívateľ/Zákazník a/alebo ako zákonný zástupca
                        Tretej osoby oprávnený na ich poskytnutie, vyjadrujem slobodný, vážny a dobrovoľný
                        súhlas
                        s ich spracúvaním Poskytovateľom a to v súlade a za podmienok stanovených Zásadami
                        spracúvania
                        osobných údajov Poskytovateľa.
                    </p>
                    <p class="consent-note text-justify">
                        Zároveň týmto vyhlasujem, že všetky mnou poskytnuté osobné údaje sú pravdivé, úplné a
                        správne.
                    </p>
                    <p class="consent-note text-justify">
                        Tento súhlas so spracovaním osobných údajov spracúvaných na základe súhlasu je možné
                        kedykoľvek odvolať elektronicky zaslaním e-mailu na
                        <a
                           href="mailto:gdpr@aoreal.sk?subject=Odvolanie súhlasu na spracovanie osobných údajov"
                        >gdpr@aoreal.sk</a>.
                    </p>
                </section>

                <footer style="display: flex; justify-content: center">
                    <button class="btn-sm btn-default" id="_refinback" type="submit">
                        <?= Yii::t('finreq', 'Odoslať') ?>
                    </button>
                </footer>

            </div>
        </form>
    </div>
</main>

<?php
$css = <<<CSS

p {
    margin: 0;
    padding: 0;
}
#req {
    width: 100%;
    height: auto;
    position: relative;
    clear: both;
}
#req .req-container {
    width: 70%;
    margin: 40px auto;
    display: grid; 
    row-gap: 30px;
}
#req input[type=text],
#req input[type=tel],
#req input[type=email],
#req input[type=date],
#req input[type=number],
#req select,
#req textarea,
#req input[type=datetime-local]{
    border: 0;
    border-bottom: solid 2px #133045;
    background-image: none;
    background-color: transparent;
    box-shadow: none;
    display: block;
    width: 100%;
    padding-top: 10px;
    padding-bottom: 5px;
    outline: none !important;
    color: #666666;
    border-radius: 0;
}
ul.req-list {
    list-style: none;
    padding: 0;
    margin: 10 0 0 0;  
}
ul.req-list li {
    padding-bottom: 10px;
}
.btn-sm {
    width: 50%;
    margin-bottom: 0;
    white-space: nowrap;
    touch-action: manipulation;
    cursor: pointer;
    background-image: none;
    padding: 5px;
    border-radius: 3px;
    user-select: none;

    display: inline-block;
    position: relative;
    font-family: 'Raleway', sans-serif;
    font-weight: 500;
    color: #032241;
    text-align: center;
    text-decoration: none !important;
    text-transform: uppercase;
    vertical-align: top;
    border: solid 2px #032241;
    transition: all 0.2s linear;
    z-index: 1;
}
.btn-sm:after {
    content: '';
    display: inline-block;
    position: absolute;
    background-color: #c49444;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    transition: all 0.2s linear;
    z-index: -1;
}
.btn-sm:hover:after {
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.date-row {
    display: flex; 
    flex-direction: row; 
    margin-top: 20px; 
    margin-bottom: 20px; 
    column-gap: 5px;
}
.fc-toolbar-title {
    font-size: 1.2em !important;
    font-weight: 700;
    color: #133045;
}
.fc-button {
    font-size: 0.85em !important;
    padding: 0.3em 0.35em !important;
    background: rgba(105, 64, 2,0.95) !important;
}
/* Top-center QR ribbon (content area, not in the banner) */
.qr-ribbon{
  position: sticky;
  top: 12px;
  z-index: 60;
  display: flex;
  justify-content: center;
  margin: 12px auto 24px;
  width: 100%;
}
.qr-card{
  background: rgba(255,255,255,.98);
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,.15);
  padding: 16px 16px 10px;
  text-align: center;
  width: min(380px, 44vw);
}
.qr-card img{ width:100%; height:auto; display:block; }
.qr-card figcaption{ margin-top:6px; font-size:12px; color:#666; }
@media (min-width:1200px){ .qr-ribbon{ top:8px; } }
@media (max-width:992px){ .qr-ribbon{ display:none; } }
.qr-spacer{ height:130px; }
CSS;
$this->registerCss($css);

$js = <<<JS

    $(document).on('submit', '#hpt-frm', function() {
       let hpt = $('input[name="F[hpt]"]').val();
         if (hpt !== '') {
              return false;
         }
         return false;
    });

    $(document).on('change', '.xi', function() {
        let x = $(this).val();
        if (x === 'refcode') {
            $('#referal-code').show();
            $('#other-src').hide();
        } else if (x === 'nodef') {
            $('#other-src').show();
            $('#referal-code').hide();
        } else {
            $('#referal-code').hide();
            $('#other-src').hide();
        }
    })

    $(document).on('change', '.ri', function() {
        let id = $(this).data('itemid');    
        if ($(this).is(':checked') && id === 2) {
            $('#t' + (id + 1)).hide();
        } else if ($(this).is(':checked') && id === 3) {
            $('#t' + (id - 1)).hide();
        } else {
            $('#t2').show();
            $('#t3').show();
        }
    });

    validateEmail = function(mail) {
     if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))
      {
        return true;
      }
        alert("You have entered an invalid email address!")
        return false;
    }
    
    $(document).on('change', '#call-type', function () {
        var callTypes = {
            "cont-vid":{
                "skype":"Skype",
                "viber":"Viber",
                "whatsapp":"Whatsapp",
                "fb-mess":"Facebook Messanger",
                "zoom": "Zoom",
                "teams": "Microsoft Teams"
            },
            "cont-phone": {
                "phone": "Telefon",
                "viber": "Viber",
                "whatsapp":"Whatsapp",
                "fb-mess":"Facebook Messanger",
                "skype":"Skype"
            },
            "written": {
                "skype":"Skype",
                "viber":"Viber",
                "whatsapp":"Whatsapp",
                "fb-mess":"Facebook Messanger",
                "sms": "SMS",
                "email": "E-mail"
            }
        };
        var t = $(this).val();
        $('#call-source').empty();
        $.each(callTypes[t],function (k,v){
            $('#call-source').append($('<option>', {
                value: k,
                text : v
            }));
        });

    });
JS;
$this->registerJS($js);