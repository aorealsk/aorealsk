<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use app\assets\RegistrationAsset;

$this->title = 'Dotazník k založeniu s.r.o.';

RegistrationAsset::register($this);
$this->registerCSSFile('@web/css/customer/registration.css?v=0.1', ['depends' => RegistrationAsset::class]);
$this->registerCSSFile("https://fonts.googleapis.com/icon?family=Material+Icons", ['depends' => RegistrationAsset::class]);
$this->registerCSSFile('@web/css/questionnaire.css?v=1.06', ['depends' => AppAsset::class]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js', ['depends' => AppAsset::class]);
$this->registerCSSFile("https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css", ['depends' => RegistrationAsset::class]);
$this->registerJSFile('https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js', ['depends' => AppAsset::class]);
$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css', ['depends' => AppAsset::class]);

?>
<main class="site-questionnaire">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url('/images/header-bg2.jpg');" width="1600" height="400"></canvas>
        <div class="page-border container-default d-block position-absolute mx-auto">
            <div class="page_title_line_left d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
            <div class="page_title_line_right d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
        </div>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true" data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                        <strong><?= Html::encode($this->title) ?></strong>
                    </h1>
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

    <div id="questionnaire">
        <div class="questionnaire-container wizard-content">
            <form id="dotaznik-form" class="needs-validation tab-wizard wizard-circle wizard" enctype="multipart/form-data" action="<?= Url::to(['questionnaire/form-submit']) ?>" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <h1><?= Yii::t('app', 'Dotazník k založeniu s.r.o'); ?></h1>
                <section>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <h5>
                                Všetko čo je označené
                                <span class="required">*</span>
                                je povinné vyplniť.
                            </h5>
                        </div>
                    </div>
                    <div class="form-group row source">
                        <div class="col-xs-12">
                            <label class="form-control-label">
                                <?= Yii::t('app', 'Ako ste sa o nás dozvedeli?'); ?>
                            </label>
                            <span class="required">*</span>
                            <select name='ClientRequest[source]' id='source' class="form-control dropdown">
                                <option value="" selected><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="search-engine">vyhľadávač (napr. google)</option>
                                <option value="relative">od známeho</option>
                                <option value="customer">už som využil Vaše služby</option>
                                <option value="social-media">zo sociálnych sietí</option>
                                <option value="other">iné</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row source-other" hidden>
                        <div class="col-xs-12 source-input">
                            <label class="form-control-label">
                                <?= Yii::t('app', 'Uveďte bližšie, ako ste sa o nás dozvedeli'); ?>
                            </label>
                            <span class="required">*</span>
                        </div>
                    </div>
                    <div class="form-group row reason-for-chosing">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Prečo ste sa rozhodli využiť služby ALPHA-OMEGA REAL & CONSULTING s.r.o.?'); ?></label>
                            <span class="required">*</span>
                            <select name='ClientRequest[reason_for_chosing]' id="reason" class="form-control dropdown">
                                <option value="" selected><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="price">dobrá cena</option>
                                <option value="questionnare">prehľadný dotazník</option>
                                <option value="webpage-info">informácie na webstránke</option>
                                <option value="webpage-design">design webstránky</option>
                                <option value="references">veľa referencií</option>
                                <option value="advertisment">reklama</option>
                                <option value="other">iné</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row reason-for-chosing-other" hidden>
                        <div class="col-xs-12 reason-input">
                            <label class="form-control-label">
                                <?= Yii::t('app', 'Uveďte bližšie, prečo ste sa rozhodli využiť služby ALPHA-OMEGA REAL & CONSULTING s.r.o.'); ?>
                            </label>
                            <span class="required">*</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <h6>
                                <?= Yii::t('app', 'Aby Ste mohli figurovať v s.r.o., 
                                nemôžete mať dlhy na nasledovných zoznamoch. Prosím Vás 
                                skontrolujte to aj Vy, a keby Ste tam figurovali dajte nám vedieť!')
                                ?>
                            </h6>
                            <ul>
                                <li>
                                    <a target="_blank" rel="noopener noreferrer" href="https://www.socpoist.sk/">
                                        Sociálna poisťovňa
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" rel="noopener noreferrer" href="https://obcan.justice.sk/poverenia/rozsirene-vyhladavanie">
                                        Exekúcie
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o založenie s.r.o. s DPH registráciou?'); ?></label>
                            <span class="required">*</span>
                            <select name='ClientRequest[dph_registration]' id="dph" class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="0">Nie/nemám záujem</option>
                                <option value="1">Áno/ mám záujem-zabezpečuje spoločnosť ALPHA-OMEGA REAL & CONSULTING s.r.o. (
                                    550€ + 50€* jednorázovo - pri 1. daňovom priznaní sa Vám 50€ vráti)
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o ready-made s.r.o.?'); ?></label>
                            <span class="required">*</span>
                            <select name='ClientRequest[ready_made_sro]' id="ready-made" class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="no">Nie/chcem založiť novú s.r.o.</option>
                                <option value="yes with assigned DIC">
                                    Áno/mám záujem kúpiť už existujúcu firmu s prideleným DIČ a možnosťou ihneď fakturovať (499€)
                                    - Po podpise dokumentov môžete faktúrovať a podpisovať zmluvy
                                </option>
                                <option value="yes with bank account">
                                    Áno/mám záujem kúpiť už existujúcu firmu aj s bankovým účtom, ktorý môžem ihneď používať
                                    za 549€ (Inak je možné založiť účet až po zápise v obchodnom registri.)
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o virtuálne sídlo?'); ?></label>
                            <select name='ClientRequest[virtual_studio]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="no">Nie/mám vlastné sídlo</option>
                                <option value="Černyševského 10 (BA5)">Áno / Černyševského 10 (BA5) 90€/rok + 13€ jednorázovo za preberanie pošty</option>
                                <option value="Jilemnického 5498/37(DS)">Áno / Jilemnického 5498/37(DS) 90€/rok + 13€ jednorázovo za preberanie pošty</option>
                                <option value="Nám. Á. Vámbéryho 5249/13A (DS)">Áno / Nám. Á. Vámbéryho 5249/13A (DS) 90€/rok + 13€ jednorázovo za preberanie pošty</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte zabezpečené účtovníctvo?'); ?></label>
                            <select name="ClientRequest[accounting]" class="form-control dropdown">
                                <option value="no-price-offer">Účtovníctvo mám zabezpečené - nemám záujem o cenovú ponuku
                                    od ALPHA-OMEGA REAL & CONSULTING s.r.o.
                                </option>
                                <option value="yes with price offer">
                                    Účtovníctvo mám zabezpečené - mám záujem o cenovú ponuku
                                    od ALPHA-OMEGA REAL & CONSULTING s.r.o.
                                </option>
                                <option value="searching for accountant">
                                    Účtovníka si momentálne hľadám
                                </option>
                                <option value="no accountant">
                                    Účtovníka zatiaľ nemám
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o založenie bankového účtu?'); ?></label>
                            <select name='ClientRequest[bank]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="fio-banka">
                                    Áno-Fio Banka
                                </option>
                                <option value="mBank">
                                    Áno-mBank
                                </option>
                                <option value="bks-bank">
                                    Áno-BKS Ban
                                </option>
                                <option value="unicredit-bank">
                                    Áno-UniCredit Bank
                                </option>
                                <option value="slovenska-sporitelna">
                                    Áno-Slovenská Sporiteľňa
                                </option>
                                <option value="vub-banka">
                                    Áno-VÚB BANKA
                                </option>
                                <option value="prima-banka">
                                    Áno-Prima Banka
                                </option>
                                <option value="tatra-banka">
                                    Áno-Tatra Banka
                                </option>
                                <option value="365">
                                    Áno-365
                                </option>
                                <option value="Oberbank">
                                    Áno-Oberbank
                                </option>
                                <option value="privat-banka">
                                    Áno-Priat Banka
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o financovanie Vašej firmy cez banku?'); ?></label>
                            <select name='ClientRequest[finance_banking]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="fio-banka">
                                    Áno-Fio Banka
                                </option>
                                <option value="mBank">
                                    Áno-mBank
                                </option>
                                <option value="bks-bank">
                                    Áno-BKS Ban
                                </option>
                                <option value="unicredit-bank">
                                    Áno-UniCredit Bank
                                </option>
                                <option value="slovenska-sporitelna">
                                    Áno-Slovenská Sporiteľňa
                                </option>
                                <option value="vub-banka">
                                    Áno-VÚB BANKA
                                </option>
                                <option value="prima-banka">
                                    Áno-Prima Banka
                                </option>
                                <option value="tatra-banka">
                                    Áno-Tatra Banka
                                </option>
                                <option value="365">
                                    Áno-365
                                </option>
                                <option value="Oberbank">
                                    Áno-Oberbank
                                </option>
                                <option value="privat-banka">
                                    Áno-Priat Banka
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o firemné mobilné čisla?'); ?></label>
                            <select name='ClientRequest[mobile_phone]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="O2">
                                    Áno-O2
                                </option>
                                <option value="telekom">
                                    Áno-telekom
                                </option>
                                <option value="orange">
                                    Áno-Orange
                                </option>
                                <option value="4">
                                    Áno-4
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o firemný internet?'); ?></label>
                            <select name='ClientRequest[internet]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="O2">
                                    Áno-O2
                                </option>
                                <option value="telekom">
                                    Áno-telekom
                                </option>
                                <option value="orange">
                                    Áno-Orage
                                </option>
                                <option value="upc">
                                    Áno-UPC
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Bude mať Vaša firma zamestnancov? '); ?></label>
                            <select name='ClientRequest[employees]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="0">
                                    Nie nebude mať žiadneho zamestnanca
                                </option>
                                <option value="1">
                                    Áno bude mať zamestnancov
                                </option>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"></label>
                            <select name='ClientRequest[num_employees]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <?php foreach (range(0, 9) as $numOfEmployees) {
                                ?>
                                    <option value=<?= $numOfEmployees ?>><?= $numOfEmployees ?></option>
                                <?php } ?>
                                <option value="10-49">10-49</option>
                                <option value="49-249">49-249</option>
                                <option value="250+">250</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o stravné lístky?'); ?></label>
                            <select name='ClientRequest[meal_voucher]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="doxx">
                                    Áno-DOXX
                                </option>
                                <option value="dejeuner">
                                    Áno-Up Déjeuner
                                </option>
                                <option value="edenred">
                                    Áno-Edenred
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o marketingový balík?'); ?></label>
                            <select name='ClientRequest[marketing_package]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="logo">
                                    Áno-Logo
                                </option>
                                <option value="peciatka">
                                    Áno-Pečiatka
                                </option>
                                <option value="firemna-identita">
                                    Áno-Firemná identita
                                </option>
                                <option value="hlavickovy-papier">
                                    Áno-Hlavičkový papier
                                </option>
                                <option value="vizitka">
                                    Áno-Vizitka
                                </option>
                                <option value="reklamne-produkty">
                                    Áno-Reklamné produkty
                                </option>
                                <option value="mail">
                                    Áno-Mail
                                </option>
                                <option value="socialne-siete">
                                    Áno-Sociálne siete/FB/Insta/Linkedin
                                </option>
                                <option value="web-stranka">
                                    Áno-webová stránka
                                </option>
                                <option value="web-shop">
                                    Áno-Webshop
                                </option>
                                <option value="hosting">
                                    Áno-Hosting
                                </option>
                                <option value="callcenter">
                                    Áno-Callcenter
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Máte záujem o zariadení Vašej kancelárie ?'); ?></label>
                            <select name='ClientRequest[office]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="nabytok">
                                    Áno-Nábytok
                                </option>
                                <option value="technika">
                                    Áno-Technika
                                </option>
                                <option value="nabytok+technika">
                                    Áno-Nábytok + Technika
                                </option>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"></label>
                            <select name='ClientRequest[office_req]' class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="recomendation">
                                    Žiadam aby Ste mi odporučali najlepšie riešenie
                                </option>
                                <option value="planovanie">Plánovanie</option>
                                <option value="realizacia">Realizácia</option>
                                <option value="planovanie+realizacia">Plánovanie + Realizácia</option>
                            </select>
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Firemné údaje') ?></h1>
                <section id="company">
                    <div class="form-group row" style="margin-top: 2rem;">
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Obchodné meno spoločnosti'); ?></label>
                            <span class="required">*</span>
                            <input type="text" name="ClientCompany[name]" id="company-name">
                        </div>
                        <div class="col-xs-6 mt-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Dodatok obchodného mena'); ?></label>
                            <span class="required">*</span>
                            <select name='ClientCompany[appendix]' id="company-dodatok" class="form-control dropdown">
                                <option value=""><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                <option value="s.r.o">s.r.o</option>
                                <option value="spol. s.r.o">spol. s.r.o</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12" style="margin-top: 30px;">
                            <h5><?= Yii::t('app', 'Údaje o majiteľovi nehnutelnosti') ?></h5>
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Meno'); ?></label>
                            <span class="required">*</span>
                            <input type="text" name="Owner[first_name]">
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Priezvisko'); ?></label>
                            <span class="required">*</span>
                            <input type="text" name="Owner[last_name]">
                        </div>
                        <div class="col-xs-6" style="margin-top: 5rem;">
                            <label class="form-control-label"><?= Yii::t('app', 'Titul pred menom') ?></label>
                            <select name='Owner[adegree_before]' class="form-control dropdown">
                                <option value="">Vyberte...</option>
                                <?php foreach ($degrees as $degree) : ?>
                                    <option value="<?= $degree->short_name ?>"> <?= $degree->short_name ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-xs-6" style="margin-top: 5rem;">
                            <label class="form-control-label"><?= Yii::t('app', 'Titul za menom') ?></label>
                            <select name='Owner[adegree_after]' class="form-control dropdown">
                                <option value="">Vyberte...</option>
                                <?php foreach ($degrees as $degree) : ?>
                                    <option value="<?= $degree->short_name ?>"> <?= $degree->short_name ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-xs-12" style="margin-top:5rem;">
                            <label class="form-control-label"><?= Yii::t('app', 'Dátum narodenia') ?></label>
                            <span class="required">*</span>
                            <input type="date" name="Owner[birth_date]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <h3><?= Yii::t('app', 'Adresa trvalého bydliska') ?></h3>
                        </div>
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Ulica a číslo') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="Owner[address]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'PSČ') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="Owner[zip]" id="spolocnik-zip" class="zip-input">
                        </div>
                        <div class="col-xs-6" style="position: relative;">
                            <label class="form-control-label"><?= Yii::t('app', 'Obec') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="Owner[town]" id="town-input" class="town-input" autocomplete="off">
                            <ul class="hidden" id="town-list">
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <h5><?= Yii::t('app', 'Adresa sídla') ?></h5>
                        </div>
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Ulica a číslo') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="ClientCompany[address]" id="company-address">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'PSČ') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="ClientCompany[zip]" id="company-zip" class="zip-input">
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Obec') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="ClientCompany[town]" id="company-town" class="town-input" autocomplete="off">
                            <ul class="hidden" id="town-list">
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-8">
                            <label class="form-control-label"><?= Yii::t('app', 'Katastrálne územie') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="ClientCompany[cadastral_area]" id="company-kat-uzemie">
                        </div>
                        <div class="col-xs-3" style="padding-top: 4rem; margin-left:5rem;">
                            <a target="_blank" rel="noopener noreferrer" href="https://zbgis.skgeodesy.sk/mkzbgis/sk/kataster" class="btn">
                                Katastrálna mapa
                            </a>
                        </div>
                    </div>
                    <div class="form-group row">

                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Číslo listu vlastníctva') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="lv_number" id="company-kat-uzemie">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <h5><?= Yii::t('app', 'Ostatné údaje') ?></h5>
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Základné ímanie') ?></label>
                            <span class="required">*</span>
                            <input type="number" placeholder="min. 5000€" name="ClientCompany[starting_capital]" id="company-capital" class="num-to-convert">
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Slovom') ?></label>
                            <span class="required">*</span>
                            <input type="text" placeholder="Napr. päťtisíc" name="ClientCompany[starting_capital_text]" id="capital-text" class="convert-to">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Obec/mesto podpisu dokumentov') ?></label>
                            <span class="required">*</span>
                            <input type="text" placeholder="Napr. Bratislava" name="ClientCompany[town_of_signature]" id="company-town-signature">
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Konatelia') ?></h1>
                <section>
                    <div style="display:flex; align-items:center; justify-content:center;">
                        <p><a href="#konatelia-modal" class="btn-sm" data-modal>Nahrajte dokument</a></p>
                    </div>


                    <div class="konatelia" data-order="1">
                        <div class="form-group row mt-10">
                            <div class="col-xs-12">
                                <h5><?= Yii::t('app', 'Osobné údaje') ?></h5>
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Meno') ?></label>
                                <span class="required">*</span>
                                <input type="text" name='Konatelia[1][first_name]' id="konatelia-meno" class="input-first-name">

                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Priezvisko') ?></label>
                                <span class="required">*</span>
                                <input type="text" name='Konatelia[1][last_name]' id="konatelia-priezvisko" class="input-last-name">

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Titul pred menom') ?></label>
                                <select name='Konatelia[1][adegree_before]' class="form-control dropdown">
                                    <option value="">Vyberte...</option>
                                    <?php foreach ($degrees as $degree) : ?>
                                        <option value="<?= $degree->short_name ?>"> <?= $degree->short_name ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Titul za menom') ?></label>
                                <select name='Konatelia[1][adegree_after]' class="form-control dropdown">
                                    <option value="">Vyberte...</option>
                                    <?php foreach ($degrees as $degree) : ?>
                                        <option value="<?= $degree->short_name ?>"> <?= $degree->short_name ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Dátum narodenia') ?></label>
                                <input type="date" name='Konatelia[1][birth_date]' id="konatelia-birth" class="input-birth">

                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Rodné číslo') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][ssn]" id="konatelia-ssn" class="input-ssn">

                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <h5><?= Yii::t('app', 'Adresa trvalého bydliska') ?></h5>
                            </div>
                            <div class="col-xs-12">
                                <label class="form-control-label"><?= Yii::t('app', 'Ulica a číslo') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][address]" id="konatelia-adresa">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'PSČ') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][zip]" id="konatelia-zip" class="zip-input">
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Obec') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][town]" id="konatelia-town" class="town-input" autocomplete="off">
                                <ul class="hidden" id="town-list">
                                </ul>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <h5><?= Yii::t('app', 'Ostatné údaje') ?></h5>
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Mesto narodenia') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][birth_town]" id="konatelia-birth-town" class="input-birth-town">

                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Okres narodenia') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][birth_region]" id="konatelia-birth-region">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Meno Otca') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][father_first_name]" id="konatelia-father-meno">
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Priezvisko Otca') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][father_last_name]" id="konatelia-father-priezvisko">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Meno Matky') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][mother_first_name]" id="konatelia-mother-meno">
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Priezvisko Matky') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][mother_last_name]" id="konatelia-mother-priezvisko">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <label class="form-control-label"><?= Yii::t('app', 'Priezvisko Matky za slobodna') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="Konatelia[1][mother_maiden_name]" id="konatelia-maiden">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <label class="form-control-label"><?= Yii::t('app', 'Vzťah k SR') ?></label>
                                <span class="required">*</span>
                                <select name='Konatelia[1][connection_to_sr]' id="konatelia-connection" class="form-control dropdown">
                                    <option value="" selected><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                    <option value="citizen SR">Občan SR</option>
                                    <option value="citizen EU">Občan EÚ</option>
                                    <option value="temporary stay less than 6 months">Prechodný pobyt v SR < ako 6 mesiacov</option>
                                    <option value="temporary stay more than 6 months">Prechodný pobyt v SR > ako 6 mesiacov</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <label class="form-control-label"><?= Yii::t('app', 'Konanie konateľov') ?></label>
                                <span class="required">*</span>
                                <select name='Konatelia[1][executive_actions]' id="konatelia-actions" class="form-control dropdown">
                                    <option value="" selected><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                    <option value="konaju a podpisuju spolocne">Konatelia v mene spoločnosti konajú a za spoločnosť podpisujú spoločne.</option>
                                    <option value="kona a podpisuje samostatne">Konateľ v mene spoločnosti koná a za spoločnosť podpisuje samostatne.</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="section-footer">
                        <button type="button" class="btn-sm" id="add-konatel">Pridať konateľa</button>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Spoločníci') ?></h1>
                <section>
                    <div style="display:flex; align-items:center; justify-content:center; margin-bottom: 3rem;">
                        <p><a href="#spolocnici-modal" class="btn-sm" data-modal>Nahrajte dokument</a></p>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <h5>
                                <?= Yii::t('app', 'Zaškrtnite ak spoločník je totožný s konateľom') ?>
                            </h5>
                        </div>
                        <div class="col-xs-12">
                            <label class="form-control-label">
                                <input type="checkbox" id="spolocnik-konatel">
                                <?= Yii::t('app', 'Spoločník je totožný s konateľom') ?>
                            </label>
                        </div>
                    </div>

                    <div class="spolocnici" data-order="1">
                        <div class="form-group row mt-10">
                            <div class="col-xs-12">
                                <h5><?= Yii::t('app', 'Osobné údaje') ?></h5>
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Meno') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="ClientPersonalInfo[1][first_name]" id="spolocnik-meno" class="input-first-name">

                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Priezvisko') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="ClientPersonalInfo[1][last_name]" id="spolocnik-priezvisko" class="input-last-name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Titul pred menom') ?></label>
                                <select name="ClientPersonalInfo[1][adegree_before]" class="form-control dropdown">
                                    <option value="">Vyberte...</option>
                                    <?php foreach ($degrees as $degree) : ?>
                                        <option value="<?= $degree->short_name ?>"> <?= $degree->short_name ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Titul za menom') ?></label>
                                <select name="ClientPersonalInfo[1][adegree_after]" class="form-control dropdown">
                                    <option value="">Vyberte...</option>
                                    <?php foreach ($degrees as $degree) : ?>
                                        <option value="<?= $degree->short_name ?>"> <?= $degree->short_name ?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Dátum narodenia') ?></label>
                                <span class="required">*</span>
                                <input type="date" name="ClientPersonalInfo[1][birth_date]" id="spolocnik-birth" class="input-birth">
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Rodné číslo') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="ClientPersonalInfo[1][ssn]" id="spolocnik-ssn" class="input-ssn">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <h5><?= Yii::t('app', 'Adresa trvalého bydliska') ?></h5>
                            </div>
                            <div class="col-xs-12">
                                <label class="form-control-label"><?= Yii::t('app', 'Ulica a číslo') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="ClientPersonalInfo[1][address]" id="spolocnik-adresa">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'PSČ') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="ClientPersonalInfo[1][zip]" id="spolocnik-zip" class="zip-input">
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Obec') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="ClientPersonalInfo[1][town]" id="spolocnik-town" class="town-input" autocomplete="off">
                                <ul class="hidden" id="town-list">
                                </ul>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <h5><?= Yii::t('app', 'Výška vkladov') ?></h5>
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Výška vkladu') ?></label>
                                <span class="required">*</span>
                                <input type="number" name="ClientPersonalInfo[1][deposit_amount]" id="spolocnik-deposit-amount" class="num-to-convert">
                            </div>
                            <div class="col-xs-6">
                                <label class="form-control-label"><?= Yii::t('app', 'Slovom') ?></label>
                                <span class="required">*</span>
                                <input type="text" name="ClientPersonalInfo[1][deposit_amount_text]" id="spolocnik-deposit-amount-text" class="convert-to">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">

                                <label class="form-control-label"><?= Yii::t('app', 'Percentačný podiel') ?></label>
                                <span class="required">*</span>
                                <input type="number" placeholder="Napr. 100" name="ClientPersonalInfo[1][percentage_share]" id="spolocnik-percentage">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <label class="form-control-label"><?= Yii::t('app', 'Konečný užívateľ výhod') ?></label>
                                <span class="required">*</span>
                                <select name='ClientPersonalInfo[1][kuv]' class="form-control dropdown" id="spolocnik-kuv">
                                    <option value="" selected><?= Yii::t('app', 'Prosím Vás vyberte jednu z možností') ?></option>
                                    <option value="kuv je spolocnik">KÚV je spoločník + iná osoba (neuvedená v dotazníku)</option>
                                    <option value="kuv je ina osoba">KÚV je iná osoba ako spoločník (neuvedená v dotazníku)</option>
                                    <option value="spolocnici spol su kuv">Iba spoločníci spoločnosti sú jedinými KÚV</option>
                                    <option value="spolocnik je kuv">Spoločník je zároveň jediným KÚV</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="section-footer">
                        <button type="button" class="btn-sm" id="add-partner">Pridať spoločníka</button>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Správca vkladu') ?></h1>
                <section class="spravca">
                    <div style="display:flex; align-items:center; justify-content:center; margin-bottom: 3rem;">
                        <p><a href="#spravca-modal" class="btn-sm" data-modal>Nahrajte dokument</a></p>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-12">
                            <h5>
                                <?= Yii::t('app', 'Zaškrtnite ak správca vkladu je totožný s konateľom') ?>
                            </h5>
                        </div>
                        <div class="col-xs-12">
                            <label class="form-control-label">
                                <input type="checkbox" id="spravca-konatel">
                                <?= Yii::t('app', 'Správca vkladu je totožný s konateľom') ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row mt-10">
                        <div class="col-xs-12">
                            <h5><?= Yii::t('app', 'Osobné údaje') ?></h5>
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Meno') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="SpravcaVkladu[meno]" id="spravca-meno">
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Priezvisko') ?></label>
                            <span class="required">*</span>
                            <input type="text" name="SpravcaVkladu[priezvisko]" id="spravca-priezvisko">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Titul pred menom') ?></label>
                            <select name="SpravcaVkladu[adegree_before]" class="form-control dropdown">
                                <option value="">Vyberte...</option>
                                <?php foreach ($degrees as $degree) : ?>
                                    <option value="<?= $degree->short_name ?>"> <?= $degree->short_name ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label class="form-control-label"><?= Yii::t('app', 'Titul za menom') ?></label>
                            <select name="SpravcaVkladu[adegree_after]" class="form-control dropdown">
                                <option value="">Vyberte...</option>
                                <?php foreach ($degrees as $degree) : ?>
                                    <option value="<?= $degree->short_name ?>"> <?= $degree->short_name ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Predmenty podnikania') ?></h1>
                <section>
                    <div>
                        <div class="form-group row">
                            <div class="col-xs-12">
                                <ul class="accordion">
                                    <li class="predmet-podnikania" data-order="1">
                                        <button class="accordion-control">Neviazané živnosti</button>
                                        <div class="accordion-panel">
                                            <div class="form-group row">
                                                <div class="col-xs-4">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Číslo') ?></label>
                                                    <input type="text" name="ClientBusinessSubject[1][number]" data-input-order="1">
                                                </div>
                                                <div class="col-xs-8">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Predmet podnikania') ?></label>
                                                    <input type="text" name="ClientBusinessSubject[1][subject]" data-input-order="1">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-xs-4">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Číslo') ?></label>
                                                    <input type="text" name="ClientBusinessSubject[2][number]" data-input-order="2">
                                                </div>
                                                <div class="col-xs-8">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Predmet podnikania') ?></label>
                                                    <input type="text" name="ClientBusinessSubject[2][subject]" data-input-order="2">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-xs-4">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Číslo') ?></label>
                                                    <input type="text" name="ClientBusinessSubject[3][number]" data-input-order="3">
                                                </div>
                                                <div class="col-xs-8">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Predmet podnikania') ?></label>
                                                    <input type="text" name="ClientBusinessSubject[3][subject]" data-input-order="3">
                                                </div>
                                            </div>
                                            <div class="section-footer">
                                                <button type="button" class="btn-sm" id="add-predmet-podnikania">Pridať</button>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="zivnosti" data-order="1">
                                        <button class="accordion-control" id="viazane-zivnosti">Viazané/remeslné živnosti </button>
                                        <div class="accordion-panel">
                                            <div class="form-group row">
                                                <div class="col-xs-4">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Kód') ?></label>
                                                    <input type="text" name="ClientZivnost[1][number]" data-input-order="1">
                                                </div>
                                                <div class="col-xs-8">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Predmet podnikania') ?></label>
                                                    <input type="text" name="ClientZivnost[1][subject]" data-input-order="1">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-xs-4">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Kód') ?></label>
                                                    <input type="text" name="ClientZivnost[2][number]" data-input-order="2">
                                                </div>
                                                <div class="col-xs-8">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Predmet podnikania') ?></label>
                                                    <input type="text" name="ClientZivnost[2][subject]" data-input-order="2">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-xs-4">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Kód') ?></label>
                                                    <input type="text" name="ClientZivnost[3][number]" data-input-order="3">
                                                </div>
                                                <div class="col-xs-8">
                                                    <label class="form-control-label"><?= Yii::t('app', 'Predmet podnikania') ?></label>
                                                    <input type="text" name="ClientZivnost[3][subject]" data-input-order="3">
                                                </div>
                                            </div>
                                            <div class="section-footer">
                                                <button type="button" class="btn-sm" id="add-zivnosti">Pridať</button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div id="zodpovedny-zastupca" hidden>
                            <h3><?= Yii::t('app', 'Zodpovedný zástupca') ?></h3>

                            <div class="form-group row mt-5">
                                <div class="col-md-12 col-xs-12">
                                    <select class="form-control dropdown" id="cdoc-type" name="ZastupcaDocId">
                                        <?php
                                        foreach ($cust_docs as $item) {
                                        ?>
                                            <option value="<?= $item['id'] ?>"><?= $item['internal_text'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <div class="form-group row mt-10">
                                        <label class="col-md-3 col-xs-4 col-form-label">Predná strana</label>
                                        <div class="col-md-9 col-xs-8">
                                            <input type="file" name="ZastupcaFiles[predna-strana]" id="op-predna" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 col-xs-4 col-form-label">Zadná strana</label>
                                        <div class="col-md-9 col-xs-8">
                                            <input type="file" name="ZastupcaFiles[zadna-strana]" id="op-zadna" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mt-10">
                                <div class="col-xs-12">
                                    <h5><?= Yii::t('app', 'Osobné údaje') ?></h5>
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Meno') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Priezvisko') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Titul pred menom') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Titul za menom') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Dátum narodenia') ?></label>
                                    <span class="required">*</span>
                                    <input type="date">
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Rodné číslo') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-12">
                                    <h5><?= Yii::t('app', 'Adresa trvalého bydliska') ?></h5>
                                </div>
                                <div class="col-xs-12">
                                    <label class="form-control-label"><?= Yii::t('app', 'Ulica a číslo') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Obec') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'PSČ') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-12">
                                    <h5><?= Yii::t('app', 'Ostatné údaje') ?></h5>
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Mesto narodenia') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Okres narodenia') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Meno Otca') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Priezvisko Otca') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Meno Matky') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-control-label"><?= Yii::t('app', 'Priezvisko Matky') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-12">
                                    <label class="form-control-label"><?= Yii::t('app', 'Priezvisko Matky za slobodna') ?></label>
                                    <span class="required">*</span>
                                    <input type="text">
                                </div>
                            </div>
                        </div>
                    </div>

            </form>
        </div>
    </div>
</main>

<div id="konatelia-modal" class="modal">
    <div class="form-group row" style="margin-top: 3rem;">
        <div class="col-md-12 col-xs-12">
            <select class="form-control dropdown" id="cdoc-type" name="KonateliaDocId">
                <?php
                foreach ($cust_docs as $item) {
                ?>
                    <option value="<?= $item['id'] ?>"><?= $item['internal_text'] ?></option>
                <?php
                }
                ?>
            </select>
            <div class="form-group row mt-10">
                <label class="col-md-3 col-xs-4 col-form-label">Predná strana</label>
                <div class="col-md-9 col-xs-8">
                    <input type="file" name="KonateliaFiles[predna-strana]" id="op-predna" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-xs-4 col-form-label">Zadná strana</label>
                <div class="col-md-9 col-xs-8">
                    <input type="file" name="KonateliaFiles[zadna-strana]" id="op-zadna" required>
                </div>
            </div>
        </div>
    </div>
    <div id="loading">
    </div>
    <p id="error" style="display: none; color:red; margin-bottom: 2rem; font-size: 15px;">
        Nahratie dokumentu sa nepodarilo skúste znova
    </p>
    <div class="section-footer">
        <button type="button" class="btn-sm" id="submit-doc-konatelia">Nahrajte dokument</button>
    </div>
</div>

<div id="spolocnici-modal" class="modal">
    <div class="form-group row" style="margin-top: 3rem;">
        <div class="col-md-12 col-xs-12">
            <select class="form-control dropdown" id="cdoc-type" name="SpolocniciDocId">
                <?php
                foreach ($cust_docs as $item) {
                ?>
                    <option value="<?= $item['id'] ?>"><?= $item['internal_text'] ?></option>
                <?php
                }
                ?>
            </select>
            <div class="form-group row mt-10">
                <label class="col-md-3 col-xs-4 col-form-label">Predná strana</label>
                <div class="col-md-9 col-xs-8">
                    <input type="file" name="SpolocnikFiles[predna-strana]" id="op-predna" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-xs-4 col-form-label">Zadná strana</label>
                <div class="col-md-9 col-xs-8">
                    <input type="file" name="SpolocnikFiles[zadna-strana]" id="op-zadna" required>
                </div>
            </div>
        </div>
    </div>
    <div id="loading">
    </div>
    <p id="error" style="display: none; color:red; margin-bottom: 2rem; font-size: 15px;">
        Nahratie dokumentu sa nepodarilo skúste znova
    </p>
    <div class="section-footer">
        <button type="button" class="btn-sm" id="submit-doc-spolocnici">Nahrajte dokument</button>
    </div>
</div>

<div id="spravca-modal" class="modal">
    <div class="form-group row" style="margin-top: 3rem;">
        <div class="col-md-12 col-xs-12">
            <select class="form-control dropdown" id="cdoc-type" name="SpolocniciDocId">
                <?php
                foreach ($cust_docs as $item) {
                ?>
                    <option value="<?= $item['id'] ?>"><?= $item['internal_text'] ?></option>
                <?php
                }
                ?>
            </select>
            <div class="form-group row mt-10">
                <label class="col-md-3 col-xs-4 col-form-label">Predná strana</label>
                <div class="col-md-9 col-xs-8">
                    <input type="file" name="SpravcaFiles[predna-strana]" id="op-predna" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 col-xs-4 col-form-label">Zadná strana</label>
                <div class="col-md-9 col-xs-8">
                    <input type="file" name="SpravcaFiles[zadna-strana]" id="op-zadna" required>
                </div>
            </div>
        </div>
    </div>
    <div id="loading">
    </div>
    <p id="error" style="display: none; color:red; margin-bottom: 2rem; font-size: 15px;">
        Nahratie dokumentu sa nepodarilo skúste znova
    </p>
    <div class="section-footer">
        <button type="button" class="btn-sm" id="submit-doc-spravca">Nahrajte dokument</button>
    </div>
</div>

<?php
$css = <<<CSS
.section-footer {
    display:flex;
    justify-content: center;
}
.modal {
    z-index: 5 !important
}

.wizard-content .wizard>.steps>ul>li:after, 
.wizard-content .wizard>.steps>ul>li:before{
    z-index: 0 !important
}
.wizard-content .wizard>.steps .step{
    z-index: 1 !important
}
.modal a.close-modal{
    top: 5px !important;
    right: 5px !important
}

.blocker {
    z-index: 10 !important;
}


#town-list {
    position:absolute;
    margin-top:15px;
    list-style: none;
    z-index: 3; 
    height: 300px;
    width: 400px;
    overflow-y:auto;
}

.hidden {
    display: none;
}


@keyframes spinner {
  to {transform: rotate(360deg);}
}
 
.spinner:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  width: 40px;
  height: 40px;
  margin-top: -10px;
  margin-left: -10px;
  border-radius: 50%;
  border-top: 2px solid #07d;
  border-right: 2px solid transparent;
  animation: spinner .6s linear infinite;
}


CSS;
$this->registerCss($css);

$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$csrfName = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->getCsrfToken();
$translation['finish'] = Yii::t('app', 'Nahrať');
$translation['next'] = Yii::t('app', 'Ďalej');
$translation['previous'] = Yii::t('app', 'Späť');
$js = <<<JS
    $(".tab-wizard").steps({
       headerTag: "h1",
       bodyTag: "section",
       transitionEffect: "fade",
       titleTemplate: '<span class="step">#index#</span> #title#',
       labels: {
           finish: "{$translation['finish']}",
           next: "{$translation['next']}",
           previous: "{$translation['previous']}"
       },
       onFinished: function (event, currentIndex) {
           $('#dotaznik-form').submit();
       }
    });

    $('.accordion').on('click', '.accordion-control', function(e){
  e.preventDefault(); //prevent default action of a button 
  $(this) //get the element the user clicked on
    .next('.accordion-panel') //select the next accordion panel
    .not(':animated') //if it is not currently animating
    .slideToggle(); //use slideToggle to show or hide it
})

    $('a[data-modal]').click(function(event) {
        $(this).modal({
            fadeDuration: 200,
            fadeDelay: 0.80
        });
        return false;
    });

    $(document).on('click', '#submit-doc-konatelia', function () {
        let modal = $(this).closest('.modal')
        let container = $(document).find('.konatelia')
        submitDocument(modal, container, 'KonateliaDocId')
    })
    
    $(document).on('click', '#submit-doc-spolocnici', function () {
        let modal = $(this).closest('.modal')
        let container = $(document).find('.spolocnici')
        submitDocument(modal, container, 'SpolocniciDocId')
    })

    $(document).on('click', '#submit-doc-spravca', function () {
        let modal = $(this).closest('.modal')
        let container = $(document).find('.spravca')
        submitDocument(modal, container, 'SpravcaDocId')
    })

    function submitDocument(modal, container, selector)
    {
        let docType = modal.find('#cdoc-type :selected').val()
        let prednaStrana = modal.find('#op-predna')
        let zadnaStrana = modal.find('#op-zadna')
        let formData = new FormData();
		  
		  if(prednaStrana[0].files.length === 0 || zadnaStrana[0].files.length === 0){
				modal.find('#error').css('display', 'block')
				return;
		  }

        formData.append('prednaStrana', prednaStrana[0].files[0])
        formData.append('zadnaStrana', zadnaStrana[0].files[0])
        formData.append('docType', docType)
         $.ajax({
            url: '/questionnaire/submit-doc',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                modal.find('#loading').addClass('spinner')
                modal.find('#error').css('display', 'none')
            },
            complete: function(){
                modal.find('#loading').removeClass('spinner')
            },
            success: function (res) {
                modal.find('#error').css('display', 'none')
                $.modal.close()
                $.toast({
                    text: 'Dokument bol úspešne nahratý',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 2500, 
                    stack: 6
                });

                container.find('.input-first-name').val(res.name_first)
                container.find('.input-last-name').val(res.name_last)
                container.find('.input-ssn').val(res.ssn)
                container.find('.input-birth').val(res.birth_date)
                container.find('.input-birth-town').val(res.birth_place)

                let prednaStranaClone = prednaStrana.clone()
                let zadnaStranaClone = zadnaStrana.clone()

                $(document).find('#dotaznik-form').append('<input type="hidden" value="' + docType +'" name="' +selector+'">')
                $(document).find('#dotaznik-form').append(prednaStranaClone.css('display', 'none'))
                $(document).find('#dotaznik-form').append(zadnaStranaClone.css('display', 'none'))
            },
            error: function () {
                modal.find('#error').css('display', 'block')
            }
        }) 
    }

    $('#viazane-zivnosti').on('click', function () {
        if($('#zodpovedny-zastupca').is(":hidden"))
        {
            $('#zodpovedny-zastupca').removeAttr('hidden');
        }
        else {
            $('#zodpovedny-zastupca').attr('hidden', true);
        }
    })

    let nextButton = $('a:contains("Ďalej")');
    let prevButton = $('a:contains("Späť")');

    nextButton.on('click', function () {
        turnOffBtn();
    })

    prevButton.on('click', function () {
        turnOnBtn();
    })
    
    function turnOffBtn()
    {
        nextButton.attr('href', '#').css({
            "background-color": 'grey',
            'cursor':'default'
        })
    }

    function turnOnBtn(){
            nextButton.attr('href', '#next').css({
                'background-color': 'rgb(196, 148, 68)',
                'cursor': 'pointer'
            })
    }

    function checkCompanyInputs()
    {
        let name = $('#company-name').val();
        let dodatok = $('#company-dodatok').find(':selected').val()
        let address = $('#company-address').val()
        let town = $('#company-town').val()
        let zip = $('#company-zip').val()
        let capital = $('#company-capital').val()
        let text = $('#capital-text').val()
        let townSignature = $('#company-town-signature').val()
        if(name != '' && dodatok != '' && address != '' && town != '' && zip != '' && capital != '' && text != '' && townSignature != '' ) 
        {
            turnOnBtn();
        } else { turnOffBtn() } 
    }

    $('#company-name').keyup(function () {
        checkCompanyInputs()
    })

    $('#company-dodatok').on('change', function () {
        checkCompanyInputs()
    })

    $('#company-address').keyup(function () {
        checkCompanyInputs();
    })

    $('#company-town').keyup(function () {
        checkCompanyInputs();
    })
    
    $('#company-zip').keyup(function () {
        checkCompanyInputs();
    })
    
    $('#company-capital').keyup(function () {
        checkCompanyInputs();
    })

    $('#capital-text').keyup(function () {
        checkCompanyInputs();
    })

    $('#company-town-signature').keyup(function () {
        checkCompanyInputs();
    })

    function checkSpravcaInputs(){
        let name = $('#spravca-meno').val();
        let priezvisko = $('#spravca-priezvisko').val();
        if(name != '' && priezvisko != '')
        {
            turnOnBtn();
        } else {turnOffBtn()}
    }

    $('#spravca-meno').keyup(function() {
        checkSpravcaInputs();
    })

    $('#spravca-priezvisko').keyup(function() {
        checkSpravcaInputs();
    })

    function checkSpolociciInputs() {
        let name = $("#spolocnik-meno").val()
        let priezvisko = $("#spolocnik-priezvisko").val()
        let birth = $("#spolocnik-birth").val()
        let ssn = $("#spolocnik-ssn").val()
        let adresa = $("#spolocnik-adresa").val()
        let town = $("#spolocnik-town").val()
        let zip = $("#spolocnik-zip").val()
        let deposit = $("#spolocnik-deposit-amount").val()
        let depositText = $("#spolocnik-deposit-amount-text").val()
        let percentage = $("#spolocnik-percentage").val()
        let kuv = $('#spolocnik-kuv').find(':selected').val()
        if(name != '' && priezvisko != '' && birth != '' && ssn != '' &&
         adresa != '' && town != '' && zip != '' && deposit != '' && depositText != ''
         && percentage != '' && kuv != '')
        {
            turnOnBtn()
        } else {turnOffBtn()}
    }
    $('#spolocnik-meno').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-priezvisko').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-birth').on('change', function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-ssn').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-adresa').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-town').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-zip').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-deposit-amount').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-deposit-amount-text').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-percentage').keyup(function () {
        checkSpolociciInputs();
    })
    $('#spolocnik-kuv').on('change', function () {
        checkSpolociciInputs();
    })

    function checkKonateliaInputs() {
        let name = $("#konatelia-meno").val()
        let priezvisko = $("#konatelia-priezvisko").val()
        let birth = $("#konatelia-birth").val()
        let ssn = $("#konatelia-ssn").val()
        let adresa = $("#konatelia-adresa").val()
        let town = $("#konatelia-town").val()
        let zip = $("#konatelia-zip").val()
        let birthTown = $("#konatelia-birth-town").val()
        let birthRegion = $("#konatelia-birth-region").val()
        let fatherMeno = $("#konatelia-father-meno").val()
        let fatherPriezvisko = $("#konatelia-father-priezvisko").val()
        let motherMeno = $("#konatelia-mother-meno").val()
        let motherPriezvisko = $("#konatelia-mother-priezvisko").val()
        let maiden = $("#konatelia-maiden").val()
        let connection = $('#konatelia-connection').find(':selected').val()
        let actions = $('#konatelia-actions').find(':selected').val()
        if(name != '' && priezvisko != '' && birth != '' && ssn != '' &&
            adresa != '' && town != '' && zip != '' && birthTown != '' &&
            fatherMeno != '' && fatherPriezvisko != '' && motherMeno != '' && 
            motherPriezvisko != '' && maiden != '' && connection != '' && actions != '')  
        {
            turnOnBtn()
        } else {turnOffBtn()}
    }
    $('#konatelia-meno').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-priezvisko').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-birth').on('change', function () {
        checkKonateliaInputs();
    })
    $('#konatelia-ssn').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-adresa').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-town').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-zip').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-birth-town').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-birth-region').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-father-meno').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-father-priezvisko').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-mother-meno').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-mother-priezvisko').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-maiden').keyup(function () {
        checkKonateliaInputs();
    })
    $('#konatelia-connection').on('change', function () {
        checkKonateliaInputs();
    })
    $('#konatelia-actions').on('change', function () {
        checkKonateliaInputs();
    })

    let firstOptionReason = $('#reason option:selected').val();
    let firstOptionSource = $('#source option:selected').val();
    let firstOptionDph = $('#dph option:selected').val();
    let firstOptionReadyMade = $('#ready-made option:selected').val();

    if(firstOptionSource === '' && firstOptionReason === '' && firstOptionDph === '' && firstOptionReadyMade === '')
    {
        turnOffBtn();
    }

    $('#source').on('change', function ()  {
        checkInputs()
        
        let optionValue = $('#source').find(':selected').val()
        if(optionValue == 'other')
        {
            $(document).find('div.source-other').removeAttr('hidden')
            $(document).find('div.source-input').append('<input type="text" name="ClientRequest[source]" id="cr-source-input">')
        }
        else
        {
            $(document).find('div.source-other').attr('hidden', true)
            $(document).find('input#cr-source-input').remove();
        }
    })

    $('#reason').on('change', function ()  {
        checkInputs();
        let optionValue = $('#reason').find(':selected').val()
        if(optionValue == 'other')
        {
            $(document).find('div.reason-for-chosing-other').removeAttr('hidden')
            $(document).find('div.reason-input').append('<input type="text" name="ClientRequest[reason_for_chosing]" id="cr-reason">')
        }
        else
        {
            $(document).find('div.reason-for-chosing-other').attr('hidden', true)
            $(document).find('input#cr-reason').remove();
        }
    })

    function checkInputs()
    {
        var optionValueSource = $('#source').find(':selected').val()
        var optionValueReason = $('#reason').find(':selected').val()
        var optionValueDph = $('#dph').find(':selected').val()
        var optionValueReadyMade = $('#ready-made').find(':selected').val()
        if(optionValueSource === '' || optionValueReason === '' || optionValueDph === '' || optionValueReadyMade === '')
        {
            turnOffBtn();
        }
        else if(optionValueSource != '' && optionValueReason != '' && optionValueDph != '' && optionValueReadyMade != '') {
            turnOnBtn();
        }
    }

    $('#dph').on('change', function ()  {
        checkInputs();
    })

    $('#ready-made').on('change', function ()  {
        checkInputs();
    })

    $('#add-predmet-podnikania').on('click', function () {
       let lastEl = $('li.predmet-podnikania:last')
       let clone = lastEl.clone()
       let order = parseInt(lastEl.data('order')) + 1 
       
       let firstInput = lastEl.find('input:first');
       let inputOrder = parseInt(firstInput.data('input-order'))
       
       clone.find('input[name="ClientBusinessSubject['+inputOrder+'][number]"]').attr('name', "ClientBusinessSubject["+ (inputOrder + 3) +'][number]').attr('data-input-order', (inputOrder + 3)).val('')
       clone.find('input[name="ClientBusinessSubject['+inputOrder+'][subject]"]').attr('name', "ClientBusinessSubject["+ (inputOrder + 3) +'][subject]').attr('data-input-order', (inputOrder + 3)).val('')
       clone.find('input[name="ClientBusinessSubject['+(inputOrder + 1)+'][number]"]').attr('name', "ClientBusinessSubject["+ (inputOrder + 4) +'][number]').attr('data-input-order', (inputOrder + 4)).val('')
       clone.find('input[name="ClientBusinessSubject['+(inputOrder + 1)+'][subject]"]').attr('name', "ClientBusinessSubject["+ (inputOrder + 4) +'][subject]').attr('data-input-order', (inputOrder + 4)).val('')
       clone.find('input[name="ClientBusinessSubject['+(inputOrder + 2)+'][number]"]').attr('name', "ClientBusinessSubject["+ (inputOrder + 5) +'][number]').attr('data-input-order', (inputOrder + 5)).val('')
       clone.find('input[name="ClientBusinessSubject['+(inputOrder + 2)+'][subject]"]').attr('name', "ClientBusinessSubject["+ (inputOrder + 5) +'][subject]').attr('data-input-order', (inputOrder + 5)).val('')

       clone.attr('data-order', order)
       clone.find('.section-footer').remove();
       lastEl.after(clone)
    })

    $('#add-zivnosti').on('click', function () {
       let lastEl = $('li.zivnosti:last')
       let clone = lastEl.clone()
       let order = parseInt(lastEl.data('order')) + 1 

       let firstInput = lastEl.find('input:first');
       let inputOrder = parseInt(firstInput.data('input-order'))
       
       clone.find('input[name="ClientZivnost['+inputOrder+'][number]"]').attr('name', "ClientZivnost["+ (inputOrder + 3) +'][number]').attr('data-input-order', (inputOrder + 3)).val('')
       clone.find('input[name="ClientZivnost['+inputOrder+'][subject]"]').attr('name', "ClientZivnost["+ (inputOrder + 3) +'][subject]').attr('data-input-order', (inputOrder + 3)).val('')
       clone.find('input[name="ClientZivnost['+(inputOrder + 1)+'][number]"]').attr('name', "ClientZivnost["+ (inputOrder + 4) +'][number]').attr('data-input-order', (inputOrder + 4)).val('')
       clone.find('input[name="ClientZivnost['+(inputOrder + 1)+'][subject]"]').attr('name', "ClientZivnost["+ (inputOrder + 4) +'][subject]').attr('data-input-order', (inputOrder + 4)).val('')
       clone.find('input[name="ClientZivnost['+(inputOrder + 2)+'][number]"]').attr('name', "ClientZivnost["+ (inputOrder + 5) +'][number]').attr('data-input-order', (inputOrder + 5)).val('')
       clone.find('input[name="ClientZivnost['+(inputOrder + 2)+'][subject]"]').attr('name', "ClientZivnost["+ (inputOrder + 5) +'][subject]').attr('data-input-order', (inputOrder + 5)).val('')

       clone.attr('data-order', order)
       clone.find('.section-footer').remove();
       lastEl.after(clone)
    })

    $('#add-konatel').on('click', function () {
       let lastEl = $('div.konatelia:last')
       let clone = lastEl.clone()
       let order = parseInt(lastEl.data('order')) + 1 

       clone.find('input[name="Konatelia['+(order - 1)+'][first_name]"]').attr('name', "Konatelia["+ order +'][first_name]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][last_name]"]').attr('name', "Konatelia["+ order +'][last_name]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][adegree_before]"]').attr('name', "Konatelia["+ order +'][adegree_before]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][adegree_after]"]').attr('name', "Konatelia["+ order +'][adegree_after]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][birth_date]"]').attr('name', "Konatelia["+ order +'][birth_date]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][ssn]"]').attr('name', "Konatelia["+ order +'][ssn]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][address]"]').attr('name', "Konatelia["+ order +'][address]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][town]"]').attr('name', "Konatelia["+ order +'][town]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][zip]"]').attr('name', "Konatelia["+ order +'][zip]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][birth_town]"]').attr('name', "Konatelia["+ order +'][birth_town]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][birth_region]"]').attr('name', "Konatelia["+ order +'][birth_region]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][father_first_name]"]').attr('name', "Konatelia["+ order +'][father_first_name]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][father_last_name]"]').attr('name', "Konatelia["+ order +'][father_last_name]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][mother_first_name]"]').attr('name', "Konatelia["+ order +'][mother_first_name]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][mother_last_name]"]').attr('name', "Konatelia["+ order +'][mother_last_name]').val('')
       clone.find('input[name="Konatelia['+(order - 1)+'][mother_maiden_name]"]').attr('name', "Konatelia["+ order +'][mother_maiden_name]').val('')
       clone.find('select[name="Konatelia['+(order - 1)+'][connection_to_sr]"]').attr('name', "Konatelia["+ order +'][connection_to_sr]').val('')
       clone.find('select[name="Konatelia['+(order - 1)+'][executive_actions]"]').attr('name', "Konatelia["+ order +'][executive_actions]').val('')

       clone.attr('data-order', order)
       lastEl.after(clone)
    })

    $('#add-partner').on('click', function () {
       let lastEl = $('div.spolocnici:last')
       let clone = lastEl.clone()
       let order = parseInt(lastEl.data('order')) + 1 

       changeFields(order, clone)
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][deposit_amount]"]').attr('name', "ClientPersonalInfo["+ order +'][deposit_amount]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][deposit_amount_text]"]').attr('name', "ClientPersonalInfo["+ order +'][deposit_amount_text]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][percentage_share]"]').attr('name', "ClientPersonalInfo["+ order +'][percentage_share]').val('')
       clone.find('select[name="ClientPersonalInfo['+(order - 1)+'][kuv]"]').attr('name', "ClientPersonalInfo["+ order +'][kuv]').val('')

       clone.attr('data-order', order)
       lastEl.after(clone)
    })

    function changeFields(order, clone)
    {
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][first_name]"]').attr('name', "ClientPersonalInfo["+ order +'][first_name]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][last_name]"]').attr('name', "ClientPersonalInfo["+ order +'][last_name]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][adegree_before]"]').attr('name', "ClientPersonalInfo["+ order +'][adegree_before]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][adegree_after]"]').attr('name', "ClientPersonalInfo["+ order +'][adegree_after]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][birth_date]"]').attr('name', "ClientPersonalInfo["+ order +'][birth_date]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][ssn]"]').attr('name', "ClientPersonalInfo["+ order +'][ssn]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][address]"]').attr('name', "ClientPersonalInfo["+ order +'][address]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][town]"]').attr('name', "ClientPersonalInfo["+ order +'][town]').val('')
       clone.find('input[name="ClientPersonalInfo['+(order - 1)+'][zip]"]').attr('name', "ClientPersonalInfo["+ order +'][zip]').val('')
    }

    $(document).on("click",".town-input",function() {
        let townContainer = $(this).closest('div').find('#town-list')
        let zip = $(this).closest('.row').find('.zip-input').val()
        townContainer.addClass('open');  
        if(townContainer.hasClass('open'))
        {
            townContainer.removeClass('hidden')
            $.ajax({
                url: '/questionnaire/fetch-town',
                dataType: 'json',
                method: 'post',
                data: {zip: zip}
            }).done(function (res) {
                townContainer.empty();
                res.forEach((town) => {
                    townContainer.append('<li> <button type="button" id="nazov-obce" class="btn" style="width:100%;" data-town="' + town.nazov_obce+ '" data-zip="' + town.psc +'">' + town.nazov_obce + '</button> </li>')
                })
            })
        }
    });

    $(document).on("click","#nazov-obce",function() {
        let townInput = $(this).closest('div').find('.town-input');
        let zipInput = $(this).closest('.row').find('.zip-input');
        let townContainer = $(this).closest('div').find('#town-list')
        townContainer.addClass('hidden')
        townContainer.removeClass('open')
        townInput.val($(this).data('town'))
        zipInput.val($(this).data('zip'))
    })

    $(document).on("keydown",".zip-input",function() {
        let townContainer = $(this).closest('.row').find('#town-list')
        let zip = $(this).val()
        townContainer.addClass('open');  
        if(townContainer.hasClass('open')){
            townContainer.removeClass('hidden')
          $.ajax({
                url: '/questionnaire/fetch-town',
                dataType: 'json',
                method: 'post',
                data: {zip: zip}
            }).done(function (res) {
                townContainer.empty();
                res.forEach((town) => {
                    townContainer.append('<li> <button type="button" id="nazov-obce" class="btn" style="width:100%;" data-town="' + town.nazov_obce+ '" data-zip="' + town.psc +'">' + town.nazov_obce + '</button> </li>')
                })
            })
        }
    })

    $(document).on("change",".num-to-convert",function() {
        let num = $(this).val();
        let textContainer = $(this).closest('.row').find('.convert-to')
          $.ajax({
                url: '/questionnaire/convert-num-to-text',
                dataType: 'json',
                method: 'post',
                data: {num: num}
            }).done(function (res) {
                textContainer.val(res)
            })
    })

    $(document).on("change","#spolocnik-konatel",function() {
            let konatelFirstName = $(document).find('input[name="Konatelia[1][first_name]"]').val()
            let spolocnikFirstName = $(document).find('input[name="ClientPersonalInfo[1][first_name]"]')
            let konatelLastName = $(document).find('input[name="Konatelia[1][last_name]"]').val()
            let spolocnikLastName = $(document).find('input[name="ClientPersonalInfo[1][last_name]"]')
            let konatelDegreeBefore = $(document).find('select[name="Konatelia[1][adegree_before]"]').val()
            let spolocnikDegreeBefore = $(document).find('select[name="ClientPersonalInfo[1][adegree_before]"]')
            let konatelDegreeAfter = $(document).find('select[name="Konatelia[1][adegree_after]"]').val()
            let spolocnikDegreeAfter = $(document).find('select[name="ClientPersonalInfo[1][adegree_after]"]')
            let konatelBirthDate = $(document).find('input[name="Konatelia[1][birth_date]"]').val()
            let spolocnikBirthDate = $(document).find('input[name="ClientPersonalInfo[1][birth_date]"]')
            let konatelSsn = $(document).find('input[name="Konatelia[1][ssn]"]').val()
            let spolocnikSsn = $(document).find('input[name="ClientPersonalInfo[1][ssn]"]')
            let konatelAddress = $(document).find('input[name="Konatelia[1][address]"]').val()
            let spolocnikAddress = $(document).find('input[name="ClientPersonalInfo[1][address]"]')
            let konatelZip = $(document).find('input[name="Konatelia[1][zip]"]').val()
            let spolocnikZip = $(document).find('input[name="ClientPersonalInfo[1][zip]"]')
            let konatelTown = $(document).find('input[name="Konatelia[1][town]"]').val()
            let spolocnikTown = $(document).find('input[name="ClientPersonalInfo[1][town]"]')
        if($(this).is(':checked')){
            spolocnikFirstName.val(konatelFirstName)
            spolocnikLastName.val(konatelLastName)
            spolocnikDegreeBefore.val(konatelDegreeBefore)
            spolocnikDegreeAfter.val(konatelDegreeAfter)
            spolocnikBirthDate.val(konatelBirthDate)
            spolocnikSsn.val(konatelSsn)
            spolocnikAddress.val(konatelAddress)
            spolocnikZip.val(konatelZip)
            spolocnikTown.val(konatelTown)
        } else {
            spolocnikFirstName.val('')
            spolocnikLastName.val('')
            spolocnikDegreeBefore.val('')
            spolocnikDegreeAfter.val('')
            spolocnikBirthDate.val('')
            spolocnikSsn.val('')
            spolocnikAddress.val('')
            spolocnikZip.val('')
            spolocnikTown.val('')
        }
    })
    $(document).on("change","#spravca-konatel",function() {
            let konatelFirstName = $(document).find('input[name="Konatelia[1][first_name]"]').val()
            let spravcaFirstName = $(document).find('input[name="SpravcaVkladu[meno]"]')
            let konatelLastName = $(document).find('input[name="Konatelia[1][last_name]"]').val()
            let spravcaLastName = $(document).find('input[name="SpravcaVkladu[priezvisko]"]')
            let konatelDegreeBefore = $(document).find('select[name="Konatelia[1][adegree_before]"]').val()
            let spravcaDegreeBefore = $(document).find('select[name="SpravcaVkladu[adegree_before]"]')
            let konatelDegreeAfter = $(document).find('select[name="Konatelia[1][adegree_after]"]').val()
            let spravcaDegreeAfter = $(document).find('select[name="SpravcaVkladu[adegree_after]"]')
        if($(this).is(':checked')){
            spravcaFirstName.val(konatelFirstName)
            spravcaLastName.val(konatelLastName)
            spravcaDegreeBefore.val(konatelDegreeBefore)
            spravcaDegreeAfter.val(konatelDegreeAfter)
        } else {
            spravcaFirstName.val('')
            spravcaLastName.val('')
            spravcaDegreeBefore.val('')
            spravcaDegreeAfter.val('')
        }

        checkSpravcaInputs();
    })

JS;
$this->registerJS($js);
?>