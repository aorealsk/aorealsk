<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use app\assets\RegistrationAsset;
use common\models\settings\Settings;

$this->title = Yii::t('app', "Finančný dotazník");

RegistrationAsset::register($this);
$this->registerCSSFile('@web/css/customer/registration.css?v=0.1', ['depends' => RegistrationAsset::class]);
$this->registerCSSFile('https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css', ['depends' => AppAsset::class]);
$this->registerCSSFile('@web/css/req.css?v=1.06', ['depends' => AppAsset::class]);
$this->registerJSFile('@web/js/app-request.js?v=0.7', ['depends' => AppAsset::class]);
$this->registerJSFile('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/i18n/jquery-ui-i18n.min.js', ['depends' => AppAsset::class]);
$this->registerJSFile('@web/js/tambr/ui/datepicker_langs.js');
$this->registerCSSFile('@web/css/nouislider.min.css?v=0.5', ['depends' => AppAsset::class]);
$this->registerJSFile('@web/js/nouislider.min.js?v=0.5', ['depends' => AppAsset::class]);
?>

<main class="site-applicant">
    <input type="hidden" id="client_id" value="0">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url('/images/contact-us-banner-1.jpg');" width="1600" height="400"></canvas>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true"
                        data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s"
                        data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                        <strong><?= Html::encode($this->title) ?></strong>
                    </h1>
                </div>
            </div>
        </div>
        <div class="breadcrumbs-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <?=  Breadcrumbs::widget([ 'links' => $this->params['breadcrumbs'] ?? [] ]); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
                    // QR code under the title (embedded from local /asset/ folder next to this view)
                    $qrPath = __DIR__ . '/asset/finreq_qrcode.png';
                        if (file_exists($qrPath)) {
                        $qrData = base64_encode(file_get_contents($qrPath));
                        echo Html::img('data:image/png;base64,' . $qrData, [
                            'alt'   => 'Financial Request QR code',
                            'class' => 'qr-center',
                            ]);
                        } else {
                            echo '<!-- QR code not found: ' . Html::encode($qrPath) . ' -->';
                        }
                    ?>
    </div>
    <div id="req" class="container-fluid">
        <div class="req-container wizard-content">
            <form id="finance-form"
                  class="needs-validation tab-wizard wizard-circle wizard"
                  enctype="multipart/form-data" action="<?= Url::to(['students/save-form']) ?>" method="post">
                <input
                        type="hidden"
                        name="<?= Yii::$app->request->csrfParam ?>"
                        value="<?= Yii::$app->request->getCsrfToken() ?>">
                <h1><?= Yii::t('app', 'Úvod'); ?></h1>
                <section id="uvod">
                    <div class="ao-time-line">
                    </div>

                    <p id="p-1"><?= Yii::t('app', 'Chcem...'); ?></p>
                    <?php
                    $reqs = Settings::getFinancialQuestionaryRequests();
                    ?>
                    <ul id="client-req" style="list-style: none;">
                        <?php foreach ($reqs as $item) : ?>
                            <li>
                                <input type="checkbox" class="creq mr-2" data-item="<?= $item['field_name'] ?>">
                                <?= Yii::t('app', $item['field_value']) ?></li>
                        <?php endforeach;?>
                    </ul>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown contact" id="application-source" data-item="app_src">
                                <option value=""><?= Yii::t('app', 'Odkiaľ ste o nás dozvedeli?') ?></option>
                                <option value="facebook"><?= Yii::t('app', 'Facebook') ?></option>
                                <option value="twitter"><?= Yii::t('app', 'Twitter') ?></option>
                                <option value="linkedin"><?= Yii::t('app', 'Linkedin') ?></option>
                                <option value="refcode"><?= Yii::t('app', 'Od priateľa/známeho') ?></option>
                                <option value="nodef"><?= Yii::t('app', 'Iné') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="other-src" style="display:none">
                        <div class="col-md-12 col-xs-12">
                            <input type="text"
                                   placeholder="<?= Yii::t('app', 'Sem napíšte odkiaľ ste sa o nás dozvedeli') ?>"
                                   class="form-control contact" data-item="other_src">
                        </div>
                    </div>
                    <div class="form-group row" id="referal-code" style="display: none">
                        <div class="col-md-12 col-xs-12">
                            <input type="text"
                                   placeholder="<?= Yii::t('app', 'Referal kód') ?>"
                                   class="form-control contact" data-item="referal_code">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-xs-6">
                            <select class="form-control dropdown contact" id="call-type" data-item="call_type">
                                <option value=""><?= Yii::t('app', 'Preferovaný spôsob kontaktu') ?></option>
                                <option value="cont-vid"><?= Yii::t('app', 'Video hovor') ?></option>
                                <option value="cont-phone"><?= Yii::t('app', 'Telefon') ?></option>
                                <option value="written"><?= Yii::t('app', 'Písomne') ?></option>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <select class="form-control dropdown contact" id="call-source" data-item="call_src">
                            </select>
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Kontaktné údaje'); ?></h1>
                <section id="client-contact">
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">Email:</label>
                        <div class="col-md-9 col-xs-10">
                            <input type="email" class="form-control contact"
                                   value="@" data-item="client_email" id="ema-1">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">Mobil:</label>
                        <div class="col-md-3 col-xs-4">
                            <select class="form-control dropdown contact" data-item="client_mobile_area">
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
                            <input type="tel" class="form-control contact"
                                   placeholder="9XXXXXXXX" data-item="client_mobile">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">
                            <?= Yii::t('app', 'Pevná linka') ?>:
                        </label>
                        <div class="col-md-3 col-xs-4">
                            <select class="form-control dropdown contact" data-item="client_landline_area">
                                <?php
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
                            <input type="tel" class="form-control contact" data-item="client_landline">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-xs-2 col-form-label">Fax:</label>
                        <div class="col-md-3 col-xs-4">
                            <select class="form-control dropdown contact" data-item="client_fax_area">
                                <?php
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
                            <input type="tel" class="form-control contact" data-item="client_fax">
                        </div>
                    </div>
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
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Osobné údaje') ?></h1>
                <section id="client-basic-data">
                    <div class="form-group row">
                        <div class="col-md-6 col-xs-6">
                            <select class="form-control dropdown client-data" data-item="adegree_before">
                                <option value=""><?= Yii::t('app', 'Titul pred menom') ?></option>
                                <?php
                                /** @var $titul_pred */
                                foreach ($titul_pred as $item) {
                                    echo "<option value='{$item['short_name']}'>{$item['short_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <select class="form-control dropdown client-data" data-item="adegree_after">
                                <option value=""><?= Yii::t('app', 'Titul za menom') ?></option>
                                <?php
                                /** @var $titul_za */
                                foreach ($titul_za as $item) {
                                    echo "<option value='{$item['short_name']}'>{$item['short_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control client-data c-name"
                                   placeholder="<?= Yii::t('app', 'Meno') ?>" data-item="first_name">
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control client-data c-givenname"
                                   placeholder="<?= Yii::t('app', 'Priezvisko') ?>"
                                   data-item="last_name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control client-data c-maidenname"
                                   placeholder="<?= Yii::t('app', 'Rodné priezvisko') ?>" data-item="maiden_name">
                        </div>
                        <div class="col-md-6 col-xs-6">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown client-address c-country" data-item="perm_country">
                                <option value=""><?= Yii::t('app', 'Zvoľte si krajinu...') ?></option>
                                <?php
                                foreach ($staty as $stat) {
                                    ?>
                                    <option value="<?= $stat->id ?>"><?= Yii::t('app', $stat->name) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <h4><?= Yii::t('app', 'Adresa') ?></h4>
                    <div class="form-group row">
                        <div class="col-md-5 col-xs-3">
                            <input type="text" class="form-control client-address" id="addr-zip"
                                   placeholder="<?= Yii::t('app', 'PSČ') ?>" data-item="perm_zip">
                        </div>
                        <div class="col-md-7 col-xs-9">
                            <input type="text" id="add-town" placeholder="<?= Yii::t('app', 'Mesto') ?>"
                                   class="form-control client-address" data-item="perm_town">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-9 col-xs-8">
                            <input type="text" class="form-control client-address c-permaddress"
                                   placeholder="<?= Yii::t('app', 'Ulica a číslo') ?>"
                                   data-item="perm_street">
                        </div>
                        <div class="col-md-3 col-xs-4">
                            <input type="text" class="form-control doc-cal client-address"
                                   placeholder="<?= Yii::t('app', 'Od roku') ?>" data-item="perm_from">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-4 col-xs-5 col-form-label">
                            <?= Yii::t('app', 'Korešpondenčná adresa') ?>:</label>
                        <div class="col-md-8 col-xs-7">
                            <span class="radio-holder">
                                <input type="radio" name="D[]" id="perm-addr1">
                                <?= Yii::t('app', 'Zhodná s trvalým bydliskom') ?>
                            </span>
                            <span class="radio-holder">
                                <input type="radio" name="D[]" id="perm-addr2">
                                <?= Yii::t('app', 'Iná ako trvalé bydlisko') ?>
                            </span>
                        </div>
                    </div>
                    <div class="form-group row client-other-addr">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown client-address" data-item="temp_country">
                                <option value=""><?= Yii::t('app', 'Zvoľte si krajinu...') ?></option>
                                <?php
                                foreach ($staty as $stat) {
                                    ?>
                                    <option value="<?= $stat->id ?>"><?= Yii::t('app', $stat->name) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row client-other-addr">
                        <div class="col-md-5 col-xs-4">
                            <input type="text" class="form-control client-address" id="addr-other-zip"
                                   placeholder="<?= Yii::t('app', 'PSČ') ?>" data-item="temp_zip">
                        </div>
                        <div class="col-md-7 col-xs-8">
                            <input type="text" class="form-control client-address"
                                   placeholder="<?= Yii::t('app', 'Mesto') ?>" data-item="temp_town">
                        </div>
                    </div>
                    <div class="form-group row client-other-addr">
                        <div class="col-md-9 col-xs-8">
                            <input type="text" class="form-control client-address"
                                   placeholder="<?= Yii::t('app', 'Ulica a číslo') ?>" data-item="temp_street">
                        </div>
                        <div class="col-md-3 col-xs-4">
                            <input type="text" class="form-control doc-cal client-address"
                                   placeholder="<?= Yii::t('app', 'Od roku') ?>" data-item="temp_from">
                        </div>
                    </div>
                    <h4> <?= Yii::t('app', 'Ostatné údaje') ?></h4>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown other-personal o-country" data-item="citizenship">
                                <option value=""><?= Yii::t('app', 'Štátne občianstvo...') ?></option>
                                <?php
                                foreach ($staty as $stat) {
                                    ?>
                                    <option value="<?= $stat->id ?>"><?= Yii::t('app', $stat->name) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown other-personal" data-item="gender" id="c-gend">
                                <option value=""><?= Yii::t('app', 'Zvolte si pohlavie') ?></option>
                                <option value="m"><?= Yii::t('app', 'Muž') ?></option>
                                <option value="f"><?= Yii::t('app', 'Žena') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control other-personal"
                                   placeholder="<?= Yii::t('app', 'Rodné číslo') ?>" data-item="ssn" id="c-ssn">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control doc-cal other-personal"
                                   placeholder="<?= Yii::t('app', 'Dátum narodenia') ?>"
                                   data-item="birth_date" id="c-birth">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control other-personal"
                                   placeholder="<?= Yii::t('app', 'Miesto narodenia') ?>"
                                   id="born-place" data-item="birth_place">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control other-personal" data-item="education">
                                <option value=""><?= Yii::t('app', 'Vyberte vzdelanie') ?></option>
                                <?php
                                /** @var $educations */
                                foreach ($educations as $edu) : ?>
                                    <option value="<?= $edu['id'] ?>">
                                        <?= Yii::t('app', $edu['internal_text']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Údaje o identifikač. preukaze') ?></h1>
                <section id="client-docs">
                    <div class="doc" data-order="1">
                        <h5><?= Yii::t('app', 'Doklad č.') ?>1</h5>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown client-docs" data-item="doc_type" data-order="1">
                                    <option value=""><?= Yii::t('app', 'Typ dokladu') ?></option>
                                    <?php foreach ($cust_docs as $item) : ?>
                                        <option value="<?= $item['id'] ?>">
                                            <?= Yii::t('app', $item['internal_text']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control client-docs"
                                       placeholder="<?= Yii::t('app', 'Číslo dokladu') ?>"
                                       data-item="doc_number" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown client-docs"
                                        data-item="doc_country" data-order="1">
                                    <option value="">
                                        <?= Yii::t('app', 'Štát vydania dokladu') ?>
                                    </option>
                                    <?php
                                    foreach ($staty as $stat) {
                                        ?>
                                        <option value="<?= $stat->id ?>"><?= Yii::t('app', $stat->name) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control client-docs"
                                       placeholder="<?= Yii::t('app', 'Doklad vydal') ?>"
                                       data-item="doc_issuer" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6 col-xs-6">
                                <input type="text" class="form-control doc-cal client-docs"
                                       placeholder="<?= Yii::t('app', 'Dátum vydania') ?>"
                                       data-item="issue_date" data-order="1">
                            </div>
                            <div class="col-md-6 col-xs-6">
                                <input type="text" class="form-control doc-cal client-docs"
                                       placeholder="<?= Yii::t('app', 'Dátum platnosti') ?>"
                                       data-item="validity_date" data-order="1">
                            </div>
                        </div>
                    </div>
                    <div class="section-footer">
                        <button type="button" class="btn-sm" id="add-document">
                            <?= Yii::t('app', 'Pridať dokument') ?>
                        </button>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Rodinné údaje') ?></h1>
                <section id="family-data">
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control fam-data" data-item="marital_status">
                                <option value=""><?= Yii::t('app', 'Rodinný stav') ?></option>
                                <?php
                                /** @var $marital_status */
                                foreach ($marital_status as $item) {
                                    ?>
                                    <option value="<?= $item['id'] ?>">
                                        <?= Yii::t('app', $item['internal_text']) ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control fam-data" data-item="way_of_living">
                                <option value=""><?= Yii::t('app', 'Spôsob bývania') ?></option>
                                <?php
                                /** @var $living */
                                foreach ($living as $item) : ?>
                                    <option value="<?= $item['id'] ?>">
                                        <?= Yii::t('app', $item['internal_text']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown fam-data" data-item="residence_type">
                                <option value="">
                                    <?= Yii::t('app', 'Druh nehnuteľnosti trvalého bydliska') ?>
                                </option>
                                <?php
                                /** @var $property_type */
                                foreach ($property_type as $item) {
                                    ?>
                                    <option value="<?= $item['id'] ?>"><?= Yii::t('app', $item['nazov']) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control doc-cal fam-data"
                                   placeholder="<?= Yii::t('app', 'Od ktorého roku bývate na tejto adrese?') ?>"
                                   data-item="living_from">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown fam-data" data-item="shared_household">
                                <option value=""><?= Yii::t('app', 'Bývanie v spoločnej domácnosti') ?></option>
                                <option value="1"><?= Yii::t('app', 'Áno') ?></option>
                                <option value="0"><?= Yii::t('app', 'Nie') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown fam-data" data-item="bsm">
                                <option value="">
                                    <?= Yii::t('app', 'Bezpodielové spoluvlastníctvo manželov BSM') ?>
                                </option>
                                <option value="0"><?= Yii::t('app', 'Nie') ?></option>
                                <option value="1"><?= Yii::t('app', 'Áno') ?></option>
                                <option value="2"><?= Yii::t('app', 'Zúžené BSM') ?></option>
                                <option value="3"><?= Yii::t('app', 'Zrušené BSM') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-6 col-xs-7 col-form-label">
                            <?= Yii::t('app', 'Počet dospelých v rodine') ?>:
                        </label>
                        <div class="col-md-6 col-xs-5">
                            <input type="number" class="form-control fam-data"
                                   min="0" value="0" data-item="cnt_adults_in_family">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-6 col-xs-7 col-form-label">
                            <?= Yii::t('app', 'Počet nezaopatrených detí') ?>:
                        </label>
                        <div class="col-md-6 col-xs-5">
                            <input type="number" class="form-control fam-data"
                                   min="0" value="0" data-item="cnt_nourished_child">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-6 col-xs-7 col-form-label">
                            <?= Yii::t('app', 'Počet plnoletých osôb v domácnosti') ?>:
                        </label>
                        <div class="col-md-6 col-xs-5">
                            <input type="number" class="form-control fam-data"
                                   min="0" value="0" data-item="cnt_adults_in_household">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown fam-data" id="nourishing" data-item="maint_obligation">
                                <option value="">
                                    <?= Yii::t('app', 'Vyživovacia povinnosť iných osôb') ?>
                                </option>
                                <option value="1"><?= Yii::t('app', 'Áno') ?></option>
                                <option value="0"><?= Yii::t('app', 'Nie') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row vyzivne">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control fam-data"
                                   placeholder="<?= Yii::t('app', 'Výživné určené súdom') ?>"
                                   data-item="alimony_by_court">
                        </div>
                    </div>
                    <div class="form-group row vyzivne">
                        <label class="col-md-6 col-xs-7 col-form-label">
                            <?= Yii::t('app', 'Počet dospelých s výživným') ?>:
                        </label>
                        <div class="col-md-6 col-xs-5">
                            <input type="number" class="form-control fam-data"
                                   min="0" value="0" data-item="cnt_nourished_adult">
                        </div>
                    </div>
                    <div class="form-group row vyzivne">
                        <label class="col-md-6 col-xs-7 col-form-label">
                            <?= Yii::t('app', 'Počet detí s výživným') ?>:
                        </label>
                        <div class="col-md-6 col-xs-5">
                            <input type="number" class="form-control fam-data"
                                   min="0" value="0" data-item="cnt_nourished_child">
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Aké sú Vaše zdoje príjmu?') ?></h1>
                <section id="income-src">
                    <p>
                        <?= Yii::t('app', 'Tu si zvoľte zdroje Vášho mesačné/ročného príjmu. Napr. ak ste zamestnaný/á na trvalý pracovný pomer, zaškrtnite <strong>"Zamestnanie"</strong> a zadajte, koľko máte takýchto zamestananí.') ?>
                    </p>
                    <p>
                        <?= Yii::t('app', 'Ak ste napr. súčasne aj majiteľom nejakej s.r.o. a ste aj zamestnaný, zaškrtnite aj <strong>"Zamestnanie"</strong> aj <strong>"som majiteľom s.r.o/a.s."</strong>.') ?>
                    </p>
                    <div class="form-group row mb-5 mt-10 pl-5 pr-5">
                        <div class="col-md-1 col-xs-2">
                            <input type="checkbox" id="perm-work">
                        </div>
                        <div class="col-md-9 col-xs-7">
                            <label class="col-form-label"><?= Yii::t('app', 'Zamestnanie') ?></label>
                        </div>
                        <div class="col-md-2 col-xs-3">
                            <input type="number" id="perm-work-cnt" class="form-control" min="0" value="0">
                        </div>
                    </div>
                    <div class="form-group row mb-5 pl-5 pr-5">
                        <div class="col-md-1 col-xs-2 ">
                            <input type="checkbox" id="self-emp">
                        </div>
                        <div class="col-md-9 col-xs-7">
                            <label class="col-form-label"><?= Yii::t('app', 'Samostane zárobkovo činná osoba - SZČO') ?></label>
                        </div>
                        <div class="col-md-2 col-xs-3"></div>
                    </div>
                    <div class="form-group row pl-5 pr-5">
                        <div class="col-md-1 col-xs-1">
                            <input type="checkbox" id="bus-owner">
                        </div>
                        <div class="col-md-9 col-xs-6">
                            <label class="col-form-label"><?= Yii::t('app', 'som majiteľom s.r.o/a.s') ?></label>
                        </div>
                        <div class="col-md-2 col-xs-3">
                            <input type="number" id="bus-owner-cnt" class="form-control" min="0" value="0">
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Zamestanie') ?></h1>
                <section id="client-jobs">
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select id="soc-sec-sickleave" class="form-control social-data" data-item="sick_leave">
                                <option value="">
                                    <?= Yii::t('app', 'Kedy ste boli naposledy na PN?') ?>
                                </option>
                                <option value="1">
                                    <?= Yii::t('app', 'Bol som (pred viac ako 6. mesiacmi)') ?>
                                </option>
                                <option value="2">
                                    <?= Yii::t('app', 'Bol som (pred menej ako 6. mesiacmi)') ?>
                                </option>
                                <option value="3">
                                    <?= Yii::t('app', 'Aktuálne trvá...') ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row sickleave-row">
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control doc-cal social-data"
                                   placeholder="<?= Yii::t('app', 'Od') ?>" data-item="sick_leave_from">
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control doc-cal sickleave social-data"
                                   placeholder="<?= Yii::t('app', 'Do') ?>" data-item="sick_leave_to">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control social-data" id="maternity" data-item="maternity">
                                <option value=""><?= Yii::t('app', 'Boli ste na materskej?') ?></option>
                                <option value="1"><?= Yii::t('app', 'Bol/a som...') ?></option>
                                <option value="2"><?= Yii::t('app', 'Budem...') ?></option>
                                <option value="3"><?= Yii::t('app', 'Aktuálne trvá...') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row maternity-row">
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control doc-cal social-data"
                                   placeholder="<?= Yii::t('app', 'Od') ?>" data-item="maternity_from">
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control doc-cal maternity-leave social-data"
                                   placeholder="<?= Yii::t('app', 'Do') ?>" data-item="maternity_to">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown social-data" id="pension" data-item="pension">
                                <option value=""><?= Yii::t('app', 'Ste na dôchodku?') ?></option>
                                <option value="1"><?= Yii::t('app', 'Budem...') ?></option>
                                <option value="2"><?= Yii::t('app', 'Aktuálne som...') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row pension-row">
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control doc-cal social-data"
                                   placeholder="<?= Yii::t('app', 'Od') ?>" data-item="pension_from">
                        </div>
                        <div class="col-md-6 col-xs-6">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <select class="form-control dropdown social-data" id="inv-pension"
                                    data-item="invalidity_pension">
                                <option value=""><?= Yii::t('app', 'Ste na invalidnom dôchodku?') ?></option>
                                <option value="1"><?= Yii::t('app', 'Budem...') ?></option>
                                <option value="2"><?= Yii::t('app', 'Aktuálne som...') ?></option>
                                <option value="3"><?= Yii::t('app', 'Bol/a som...') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row inv-pension-row">
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control doc-cal social-data"
                                   placeholder="<?= Yii::t('app', 'Od') ?>" data-item="invalidity_pension_from">
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <input type="text" class="form-control doc-cal inv-pension-to social-data"
                                   placeholder="<?= Yii::t('app', 'Do') ?>" data-item="invalidity_pension_to">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <!-- eddig ledolgozott evek szama -->
                            <input type="text" class="form-control social-data"
                                   placeholder="<?= Yii::t('app', 'Celková doba zamestnania (RR/MM)') ?>"
                                   data-item="total_employment"
                            >
                        </div>
                    </div>
                    <div class="employer" data-order="1">
                        <h5 class="mb-4"><?= Yii::t('app', 'Zamestnávateľ č.') ?>1</h5>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control jobs" data-order="1" data-item="profession">
                                    <option value=""><?= Yii::t('app', 'Profesia') ?></option>
                                    <?php
                                    /** @var $professions */
                                    foreach ($professions as $item) {
                                        ?>
                                        <option value="<?= $item['id'] ?>"><?= $item['title'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control jobs" data-order="1" data-item="employment_type">
                                    <option value=""><?= Yii::t('app', 'Druh zamestnania') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-xs-5 col-form-label">
                                <?= Yii::t('app', 'Spôsob poberania príjmu:') ?>
                            </label>
                            <div class="col-md-8 col-xs-7">
                                <span class="radio-holder">
                                    <input type="radio" name="payroll" id="pay-cash"
                                           data-order="1" data-item="payroll_payout" class="jobs"
                                           data-default-value="cash"> <?= Yii::t('app', 'V hotovosti') ?>
                                </span>
                                <span class="radio-holder">
                                    <input type="radio" name="payroll" id="pay-acc"
                                           data-order="1" data-item="payroll_payout"
                                           data-default-value="account" class="jobs">
                                    <?= Yii::t('app', 'Na účet') ?>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row payroll">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs"
                                       placeholder="IBAN" data-order="1" data-item="payroll_iban">
                            </div>
                        </div>
                        <div class="form-group row payroll">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown jobs" data-order="1" data-item="payroll_bank">
                                    <option value=""><?= Yii::t('app', 'Názov banky') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-xs-5 col-form-label">
                                <?= Yii::t('app', 'Doba aktuálneho pracovného pomeru:') ?>
                            </label>
                            <div class="col-md-8 col-xs-7">
                                <span class="radio-holder">
                                    <input type="radio" class="jobs" name="workterm"
                                           data-order="1" data-item="work_term"
                                           data-default-value="permanent">
                                    <?= Yii::t('app', 'Na dobu neurčitú') ?>
                                </span>
                                <span class="radio-holder">
                                    <input type="radio" class="jobs" name="workterm"
                                           data-order="1" data-item="work_term"
                                           data-default-value="fixed">
                                    <?= Yii::t('app', 'Na dobu určitú') ?>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6 col-xs-6">
                                <input type="text" class="form-control doc-cal jobs"
                                       placeholder="<?= Yii::t('app', 'Od') ?>" data-order="1" data-item="work_from">
                            </div>
                            <div class="col-md-6 col-xs-6">
                                <input type="text" class="form-control doc-cal jobs"
                                       placeholder="<?= Yii::t('app', 'Do') ?>" data-order="1" data-item="work_to">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'Doba zamestnania v terajšom odbore (RR/MM)')?>"
                                       data-order="1" data-item="worktime_in_profession">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'Názov zamestnávateľa') ?>"
                                       data-order="1" data-item="employer_name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown jobs" data-order="1" data-item="country">
                                    <option value=""><?= Yii::t('app', 'Zvoľte si krajinu...') ?></option>
                                    <?php
                                    foreach ($staty as $stat) {
                                        ?>
                                        <option value="<?= $stat->id ?>"><?= Yii::t('app', $stat->name) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-5 col-xs-4">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'PSČ') ?>"
                                       data-order="1" data-item="zip">
                            </div>
                            <div class="col-md-7 col-xs-8">
                                <input type="text" placeholder="<?= Yii::t('app', 'Mesto') ?>"
                                       class="form-control jobs" data-order="1" data-item="town">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'Ulica, číslo') ?>"
                                       data-order="1" data-item="address">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6 col-xs-6">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'IČO') ?>"
                                       data-order="1" data-item="company_id">
                            </div>
                            <div class="col-md-6 col-xs-6">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'DIČ') ?>" data-order="1"
                                       data-item="tax_id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown jobs" data-order="1" data-item="legal_form">
                                    <option value=""><?= Yii::t('app', 'Právna forma') ?></option>
                                    <?php
                                    /** @var $legal_form */
                                    foreach ($legal_form as $item) {
                                        ?>
                                        <option value="<?= $item['id'] ?>">
                                            <?= Yii::t('app', $item['internal_text']) ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs"
                                       placeholder="IBAN" data-order="1" data-item="iban">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown jobs" data-order="1" data-item="bank_name">
                                    <option value=""><?= Yii::t('app', 'Názov banky') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'Ovládaný/vlastnený subjektom') ?>"
                                       data-order="1"
                                       data-item="owned_controlled_by">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown jobs" data-order="1" data-item="industry">
                                    <option value=""><?= Yii::t('app', 'Odvetvie') ?></option>
                                    <?php
                                    /** @var $industry */
                                    foreach ($industry as $item) {
                                        ?>
                                        <option value="<?= $item['id'] ?>">
                                            <?= Yii::t('app', $item['internal_text']) ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'Doba existencie') ?>"
                                       data-order="1" data-item="time_of_existence">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-xs-2 col-form-label">Mobil:</label>
                            <div class="col-md-3 col-xs-4">
                                <select class="form-control dropdown jobs" data-order="1" data-item="mobile_area">
                                    <?php
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
                                <input type="text" class="form-control jobs"
                                       placeholder="Napr. 9xx xxx xxx" data-order="1" data-item="mobile">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-xs-2 col-form-label">
                                <?= Yii::t('app', 'Pevná linka:') ?>
                            </label>
                            <div class="col-md-3 col-xs-4">
                                <select class="form-control dropdown jobs" data-order="1" data-item="landline-area">
                                    <?php
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
                                <input type="text" class="form-control jobs" data-order="1" data-item="landline">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-xs-2 col-form-label">Fax:</label>
                            <div class="col-md-3 col-xs-4">
                                <select class="form-control dropdown jobs" data-order="1" data-item="fax_area">
                                    <?php
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
                                <input type="text" class="form-control jobs" data-order="1" data-item="fax">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-xs-2 col-form-label">E-mail:</label>
                            <div class="col-md-9 col-xs-10">
                                <input type="email" class="form-control jobs"
                                       value="@" data-order="1" data-item="email">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'Kontaktná osoba') ?>"
                                       data-order="1" data-item="contact_person">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control jobs" value="https://"
                                       data-order="1" data-item="web">
                            </div>
                        </div>
                        <h6><?= Yii::t('app', 'Čistý mesačný príjem zo závislej činnosti za posledných 12 mesiacov') ?></h6>
                        <?php
                        for ($i = 0; $i < 12; $i++) {
                            ?>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">
                                    <?= $i + 1 ?>. <?= Yii::t('app', 'mesiac:') ?>
                                </label>
                                <div class="col-md-8">
                                    <input type="number" class="form-control jobs calc-avg-wage"
                                           data-order="1" data-item="netwage_<?= $i + 1 ?>" min="0" value="0">
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <h6><?= Yii::t('app', 'Priemerný čistý mesačný príjem zo závislej činnosti za posledných') ?></h6>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">
                                <?= Yii::t('app', '4 mesiacov') ?>
                            </label>
                            <div class="col-md-9">
                                <input type="number" class="form-control jobs" id="avg-4m-wage"
                                       data-order="1" data-item="avg_4month_netwage" min="0" value="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">
                                <?= Yii::t('app', '6 mesiacov') ?>
                            </label>
                            <div class="col-md-9">
                                <input type="number" class="form-control jobs" id="avg-6m-wage"
                                       data-order="1" data-item="avg_6month_netwage" min="0" value="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">
                                <?= Yii::t('app', '12 mesiacov') ?></label>
                            <div class="col-md-9">
                                <input type="number" class="form-control jobs" id="avg-12m-wage"
                                       data-order="1" data-item="avg_12month_netwage" min="0" value="0">
                            </div>
                        </div>
                        <h6><?= Yii::t('app', 'Priemerný hrubý mesačný príjem zo závislej činnosti za posledných') ?></h6>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">
                                <?= Yii::t('app', '12 mesiacov') ?></label>
                            <div class="col-md-9">
                                <input type="number" class="form-control jobs" data-order="1"
                                       data-item="avg_12month_grosswage" min="0" value="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="number" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'Súhrnná suma vedľajších mesačných príjmov') ?>"
                                       data-order="1"
                                       data-item="sum_of_extra_income" min="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="number" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', '13. a/alebo 14. plat') ?>"
                                       data-order="1" data-item="extra_wage" min="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6 col-xs-6">
                                <select class="form-control dropdown jobs" data-order="1" data-item="bonus_freq">
                                    <option value=""><?= Yii::t('app', 'Frekvencia bonusov') ?></option>
                                    <?php
                                    /** @var $bonus_freq */
                                    foreach ($bonus_freq as $item) {
                                        ?>
                                        <option value="<?= $item['id'] ?>"><?= $item['internal_text'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 col-xs-6">
                                <input type="number" class="form-control jobs"
                                       placeholder="<?= Yii::t('app', 'Suma bonusov') ?>"
                                       data-order="1" data-item="sum_of_bonuses" min="0">
                            </div>
                            <div class="col-md-6 col-xs-6"></div>
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Podnikanie') ?></h1>
                <section id="client-business">
                    <div class="business" data-order="1">
                        <h5 class="mb-4"><?= Yii::t('app', 'Podnikanie č.') ?>1</h5>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control biz"
                                       placeholder="Názov" data-item="name" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown biz" data-item="country" data-order="1">
                                    <option value=""><?= Yii::t('app', 'Zvoľte si krajinu...') ?></option>
                                    <?php
                                    foreach ($staty as $stat) {
                                        ?>
                                        <option value="<?= $stat->id ?>"><?= Yii::t('app', $stat->name) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-5">
                                <input type="text" class="form-control biz"
                                       placeholder="<?= Yii::t('app', 'PSČ') ?>"
                                       data-item="zip" data-order="1">
                            </div>
                            <div class="col-md-7">
                                <input type="text" placeholder="<?= Yii::t('app', 'Mesto') ?>"
                                       class="form-control biz" data-item="town" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control biz"
                                       placeholder="<?= Yii::t('app', 'Ulica, číslo') ?>"
                                       data-item="address" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6 col-xs-6">
                                <input type="text" class="form-control biz"
                                       placeholder="<?= Yii::t('app', 'IČO') ?>"
                                       data-item="company_id" data-order="1">
                            </div>
                            <div class="col-md-6 col-xs-6">
                                <input type="text" class="form-control biz"
                                       placeholder="<?= Yii::t('app', 'DIČ') ?>"
                                       data-item="tax_id" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control dropdown biz" data-item="legal_form" data-order="1">
                                    <option value=""><?= Yii::t('app', 'Právna forma') ?></option>
                                    <?php
                                    foreach ($legal_form as $item) {
                                        ?>
                                        <option value="<?= $item['id'] ?>">
                                            <?= Yii::t('app', $item['internal_text']) ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control biz"
                                       placeholder="IBAN" data-item="iban" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control biz" data-item="bank_name" data-order="1">
                                    <option value=""><?= Yii::t('app', 'Názov banky') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control biz"
                                       placeholder="<?= Yii::t('app', 'Ovládaný/vlastnený subjektom') ?>"
                                       data-order="1"
                                       data-item="owned_controlled_by">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control biz"
                                       placeholder="<?= Yii::t('app', 'Dĺžka podnikania – v mesiacoch') ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <select class="form-control biz">
                                    <option value=""><?= Yii::t('app', 'Odvetvie') ?></option>
                                    <?php
                                    foreach ($industry as $item) {
                                        ?>
                                        <option value="<?= $item['id'] ?>"><?= Yii::t('app', $item['internal_text']) ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Mobil:</label>
                            <div class="col-md-3">
                                <select class="form-control dropdown biz" data-item="mobile_area_code" data-order="1">
                                    <?php
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
                                <input type="text" class="form-control biz" data-item="mobile" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">
                                <?= Yii::t('app', 'Pevná linka:') ?></label>
                            <div class="col-md-3">
                                <select class="form-control dropdown biz" data-item="landline_area_code" data-order="1">
                                    <?php
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
                                <input type="text" class="form-control biz" data-item="landline" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Fax:</label>
                            <div class="col-md-3">
                                <select class="form-control dropdown biz" data-item="fax_area_code" data-order="1">
                                    <?php
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
                                <input type="text" class="form-control biz" data-item="fax" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">E-mail:</label>
                            <div class="col-md-9">
                                <input type="email" class="form-control biz" value="@" data-item="email" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control biz"
                                       placeholder="<?= Yii::t('app', 'Kontaktná osoba') ?>"
                                       data-item="contact_person" data-order="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 col-xs-12">
                                <input type="text" class="form-control biz"
                                       value="https://" data-item="web" data-order="1">
                            </div>
                        </div>
                    </div>
                    <h6><?= Yii::t('app', 'Príjmy z podnikania') ?></h6>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control biz"
                                   placeholder="<?= Yii::t('app', 'Celkové ročné príjmy') ?>"
                                   data-order="1" data-item="total_yearly_income">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control biz"
                                   placeholder="<?= Yii::t('app', 'Základ dane (príjmy – výdavky)') ?>"
                                   data-item="tax_base" data-order="1">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control biz"
                                   placeholder="<?= Yii::t('app', 'Daň') ?>" data-item="tax" data-order="1">
                        </div>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Ostatné príjmy a výdavky') ?></h1>
                <section id="client-cashflow">
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'IBAN bežného účtu z ktorého plánujete splácať úver') ?>"
                                   data-item="iban_for_loan_repay">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" id="bank-12" class="form-control o-income"
                                   data-item="bank_for_loan_repay"
                                   placeholder="<?= Yii::t('app', 'Názov banky') ?>"
                            >
                        </div>
                    </div>
                    <h5><?= Yii::t('app', 'Sociálne dávky dlhodobého charakteru') ?></h5>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Starobný dôchodok') ?>"
                                   data-item="old_age_pension">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Invalidný dôchodok') ?>"
                                   data-item="disability_pension">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Výsluhový dôchodok') ?>"
                                   data-item="retirement_pension">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Vdovský dôchodok') ?>"
                                   data-item="widow_pension">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Rodičovský príspevok/prídavok na dieťa') ?>"
                                   data-item="parental_allowance">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Výživné') ?>"
                                   data-item="nutritious">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Sirotský dôchodok') ?>"
                                   data-item="orphan_pension">
                        </div>
                    </div>
                    <h5><?= Yii::t('app', 'Ostatné mesačné príjmy') ?></h5>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Prenájom') ?>" data-item="lease">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Diéty') ?>"
                                   data-item="diets">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Dividendy') ?>"
                                   data-item="dividends">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Ostatné mesačné príjmy') ?>"
                                   data-item="other_monthly_income">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-income"
                                   placeholder="<?= Yii::t('app', 'Renty') ?>"
                                   data-item="rent">
                        </div>
                    </div>
                    <h5><?= Yii::t('app', 'Výdavky') ?></h5>
                    <div class="form-group row">
                        <div class="col-md-10 col-xs-10">
                            <select class="form-control dropdown" id="product-list">
                                <option value=""><?= Yii::t('app', 'Zvoľte si produkt') ?></option>
                                <option value="LOANS">
                                    <?= Yii::t('app', 'Výška mesačných splátok skôr poskytnutých úverov') ?>
                                </option>
                                <option value="CREDITLIMIT">
                                    <?= Yii::t('app', 'Výška limitu povoleného prečerpania na účte') ?>
                                </option>
                                <option value="CREDITCARD">
                                    <?= Yii::t('app', 'Výška limitu kreditnej karty') ?>
                                </option>
                                <option value="LEASING">
                                    <?= Yii::t('app', 'Výška mesačných splátok leasingu') ?>
                                </option>
                                <option value="INSTALLMENT">
                                    <?= Yii::t('app', 'Výška mesačných splátok tovaru na splátky') ?>
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 col-xs-2">
                            <button type="button" class="btn-sm" id="add-product">
                                <?= Yii::t('app', 'Pridať') ?>
                            </button>
                        </div>
                    </div>
                    <!-- template -->
                    <div class="card template" style="display: none">
                        <input type="hidden" value="" data-order="1" data-item="exp_type" class="pp-list">
                        <div class="card-block">
                            <div class="form-group row">
                                <div class="col-md-6 col-xs-6">
                                    <select class="form-control dropdown pp-list" data-order="1" data-item="owner">
                                        <option value="">
                                            <?= Yii::t('app', 'Inštitúcia') ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <input type="text" class="form-control pp-list"
                                           placeholder="<?= Yii::t('app', 'Výška splátky') ?>"
                                           data-order="1" data-item="payment">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 col-xs-6">
                                    <input type="text" class="form-control pp-list"
                                           placeholder="<?= Yii::t('app', 'Pôvodné čerpanie/Povolený limit') ?>"
                                           data-order="1" data-item="amount">
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <input type="text" class="form-control pp-list"
                                           placeholder="<?= Yii::t('app', 'Zostatok záväzkov') ?>"
                                           data-order="1" data-item="balance">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end of template -->
                    <div class="prev-prods-list">
                    </div>
                    <h6><?= Yii::t('app', 'Ostatné výdavky') ?></h6>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-exp"
                              placeholder="<?= Yii::t('app', 'Výška iných pravidelných mesačných výdavkov v € (mimo výdavkov na bývanie)') ?>"
                              data-item="regular_mothly_expenses">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-exp"
                                   placeholder="<?= Yii::t('app', 'Mesačná platba výživného') ?>"
                                   data-item="mothly_nutritious">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-exp"
                                   placeholder="<?= Yii::t('app', 'Celková suma exekúcií') ?>"
                                   data-item="total_sum_of_exec">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <input type="text" class="form-control o-exp"
                                   placeholder="<?= Yii::t('app', 'Náklady na domácnosť') ?>"
                                   data-item="household_costs">
                        </div>
                    </div>
                    <div class="section-footer">
                        <button type="button" id="_fs01" class="btn-sm"><?= Yii::t('app', 'Uložiť') ?></button>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Hypotéka') ?></h1>
                <section id="mortgage-calculator">
                    <?php
                    $limits = Settings::getFinancialQuestionaryCalcLimits();
                    ?>
                    <!-- pozadovana vyska hypoteky -->
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-control-label">
                                <?= Yii::t('app', 'Požadovaná výška hypotéky') ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 col-xs-2"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Od') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="mortgage-amount-min" min="0"
                                   value="<?= $limits['mortgage_amount_start1'] ?>">
                        </div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Do') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="mortgage-amount-max" min="0"
                                   value="<?= $limits['mortgage_amount_start2'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mt-5">
                        <div class="col-md-12 col-xs-12">
                            <div id="mortage-calc-amount"></div>
                        </div>
                    </div>
                    <!-- pozadovane obdobie -->
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-control-label">
                                <?= Yii::t('app', 'Na požadované obdobie') ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 col-xs-2"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Od') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number"
                                   id="mortgage-season-min" min="0" value="<?= $limits['mortgage_season_start1'] ?>">
                        </div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Do') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number"
                                   id="mortgage-season-max" min="0" value="<?= $limits['mortgage_season_start2'] ?>">
                        </div>
                    </div>
                    <div class="form-group row mt-5">
                        <div class="col-md-12 col-xs-12">
                            <div id="mortage-calc-season"></div>
                        </div>
                    </div>
                    <!-- fixacia -->
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'S fixáciou') ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 col-xs-2"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Od') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="mortgage-fixation-min" min="0"
                                   value="<?= $limits['mortgage_fixation_start1'] ?>"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Do') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="mortgage-fixation-max" min="0"
                                   value="<?= $limits['mortgage_fixation_start1'] ?>"></div>
                    </div>
                    <div class="form-group row mt-5">
                        <div class="col-md-12 col-xs-12">
                            <div id="mortage-calc-fixation"></div>
                        </div>
                    </div>
                    <div class="section-footer">
                        <button type="button" id="_fs02" class="btn-sm">
                            <?= Yii::t('app', 'Uložiť') ?>
                        </button>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Spotrebný úver') ?></h1>
                <section id="loan-calculator">
                    <!-- loan amount -->
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-control-label">
                                <?= Yii::t('app', 'Požadovaná výška úveru') ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 col-xs-2"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Od') ?></label></div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="loan-amount-min" min="0" value="60000"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Do') ?></label></div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="loan-amount-max" min="0" value="150000"></div>
                    </div>
                    <div class="form-group row mt-5">
                        <div class="col-md-12 col-xs-12">
                            <div id="loan-calc-amount"></div>
                        </div>
                    </div>
                    <!-- loan season -->
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Na obdobie') ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 col-xs-2"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Od') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="loan-season-min" min="0" value="60000"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Do') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="loan-season-max" min="0" value="150000"></div>
                    </div>
                    <div class="form-group row mt-5">
                        <div class="col-md-12 col-xs-12">
                            <div id="loan-calc-season"></div>
                        </div>
                    </div>
                    <div class="section-footer">
                        <button type="button" id="_fs03" class="btn-sm"><?= Yii::t('app', 'Uložiť') ?></button>
                    </div>
                </section>
                <h1><?= Yii::t('app', 'Refinancovanie hypotéky') ?></h1>
                <section id="refinance-calculator">
                    <!-- refinance amount -->
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-control-label">
                                <?= Yii::t('app', 'Požadovaná výška hypotéky') ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 col-xs-2"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Od') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="refinance-amount-min" min="0" value="60000"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Do') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="refinance-amount-max" min="0" value="150000"></div>
                    </div>
                    <div class="form-group row mt-5">
                        <div class="col-md-12 col-xs-12">
                            <div id="refinance-calc-amount"></div>
                        </div>
                    </div>
                    <!-- refinance season -->
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'Na požadované obdobie') ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 col-xs-2"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Od') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="refinance-season-min" min="0" value="1"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Do') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="refinance-season-max" min="0" value="30"></div>
                    </div>
                    <div class="form-group row mt-5">
                        <div class="col-md-12 col-xs-12">
                            <div id="refinance-calc-season"></div>
                        </div>
                    </div>
                    <!-- refinance fixation -->
                    <div class="form-group row">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-control-label"><?= Yii::t('app', 'S fixáciou') ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 col-xs-2"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Od') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="refinance-fixation-min" min="0" value="3"></div>
                        <div class="col-md-1 col-xs-1">
                            <label class="form-control-label"><?= Yii::t('app', 'Do') ?></label>
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <input type="number" id="refinance-fixation-max" min="0" value="10"></div>
                    </div>
                    <div class="form-group row mt-5">
                        <div class="col-md-12 col-xs-12">
                            <div id="refinance-calc-fixation"></div>
                        </div>
                    </div>
                    <div class="section-footer">
                        <button class="btn-sm btn-default" id="_refinback" type="button">
                            <?= Yii::t('app', 'Späť') ?>
                        </button>
                        <button type="button" id="_fs04" class="btn-sm">
                            <?= Yii::t('app', 'Uložiť') ?>
                        </button>
                    </div>
                </section>
                <div class="clear"></div>
            </form>
        </div>
</main>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
/** @var $finantial_institutions */
$banky = json_encode($finantial_institutions);
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
       onFinished: function () {
           $('#finance-form').submit();
       }
    });

    // mortgage calc amount
    var hypoCalcAmount = document.getElementById('mortage-calc-amount');
    noUiSlider.create(hypoCalcAmount, {
        connect: true,
        behaviour: 'tap',
        start: [{$limits['mortgage_amount_start1']}, 150000],
        range: {
            'min': [7000,100],
            'max': [300000]
        }
    });
    
    var nodesAmount = [
        document.getElementById('mortgage-amount-min'), // 0
        document.getElementById('mortgage-amount-max')  // 1
    ];
    hypoCalcAmount.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        nodesAmount[handle].value = values[handle];
    });
    // mortgage calc season
    var hypoCalcSeason = document.getElementById('mortage-calc-season');
    noUiSlider.create(hypoCalcSeason, {
        connect: true,
        behaviour: 'tap',
        start: [1, 30],
        range: {
            'min': [1,1],
            'max': [40]
        }
    });
    var nodesSeason = [
        document.getElementById('mortgage-season-min'),
        document.getElementById('mortgage-season-max') 
    ];
    hypoCalcSeason.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        nodesSeason[handle].value = values[handle];
    });
    // mortgage calc fixation
    var hypoCalcFixation = document.getElementById('mortage-calc-fixation');
    noUiSlider.create(hypoCalcFixation, {
        connect: true,
        behaviour: 'tap',
        start: [3,10],
        snap:true,
        range: {
            'min': 1,
            '15%': 3,
            '30%': 5,
            '55%': 10,
            'max': 15
        }
    });
    var nodesFixation = [
        document.getElementById('mortgage-fixation-min'), // 0
        document.getElementById('mortgage-fixation-max')  // 1
    ];
    hypoCalcFixation.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        nodesFixation[handle].value = values[handle];
    });
    // Refinance
    var refinanceCalcAmount = document.getElementById('refinance-calc-amount');
    noUiSlider.create(refinanceCalcAmount, {
        connect: true,
        behaviour: 'tap',
        start: [60000, 150000],
        range: {
            'min': [7000,100],
            'max': [300000]
        }
    });
    var nodesRefinanceAmount = [
        document.getElementById('refinance-amount-min'), // 0
        document.getElementById('refinance-amount-max')  // 1
    ];
    refinanceCalcAmount.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        nodesRefinanceAmount[handle].value = values[handle];
    });
    var refinanceCalcSeason = document.getElementById('refinance-calc-season');
    noUiSlider.create(refinanceCalcSeason, {
        connect: true,
        behaviour: 'tap',
        start: [1, 30],
        range: {
            'min': [1,1],
            'max': [40]
        }
    });
    var nodesRefinanceSeason = [
        document.getElementById('refinance-season-min'), // 0
        document.getElementById('refinance-season-max')  // 1
    ];
    refinanceCalcSeason.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        nodesRefinanceSeason[handle].value = values[handle];
    });
    var refinanceCalcFixation = document.getElementById('refinance-calc-fixation');
    noUiSlider.create(refinanceCalcFixation, {
        connect: true,
        behaviour: 'tap',
        start: [3,10],
        snap:true,
        range: {
            'min': 1,
            '15%': 3,
            '30%': 5,
            '55%': 10,
            'max': 15
        }
    });
    var nodesRefinanceFixation = [
        document.getElementById('refinance-fixation-min'), // 0
        document.getElementById('refinance-fixation-max')  // 1
    ];
    refinanceCalcFixation.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        nodesRefinanceFixation[handle].value = values[handle];
    });
    // Loan amount
    var loanCalcAmount = document.getElementById('loan-calc-amount');
    noUiSlider.create(loanCalcAmount, {
        connect: true,
        behaviour: 'tap',
        start: [300, 35000],
        range: {
            'min': [500,100],
            'max': [35000]
        }
    });
    var nodesLoanAmount = [
        document.getElementById('loan-amount-min'), // 0
        document.getElementById('loan-amount-max')  // 1
    ];
    loanCalcAmount.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        nodesLoanAmount[handle].value = values[handle];
    });
    var loanCalcSeason = document.getElementById('loan-calc-season');
    noUiSlider.create(loanCalcSeason, {
        connect: true,
        behaviour: 'tap',
        start: [300, 35000],
        range: {
            'min': [500,100],
            'max': [35000]
        }
    });
    var nodesLoanSeason = [
        document.getElementById('loan-season-min'), // 0
        document.getElementById('loan-season-max')  // 1
    ];
    loanCalcSeason.noUiSlider.on('update', function (values, handle, unencoded, isTap, positions) {
        nodesLoanSeason[handle].value = values[handle];
    });
    
    $(document).ready(function(){
        if (!window.sessionStorage.getItem('fin_inst')) {
            window.sessionStorage.setItem('fin_inst','{$banky}');
        }
    });

    $('#add-product').on('click',function(){
        var product = $('#product-list').val();
        var template = $('div.template');
        var main = $('div.prev-prods-list');
        var lastOne = $(main).find('div.card:last');
        var templateClone = template.clone(true);
        var lastOrderId = lastOne.length + 1;
        var toUpdate = $(templateClone).find('.pp-list');
        var institutions = $(templateClone).find('select');
         $(templateClone).find('input[type=hidden]').val(product);
        $.each(toUpdate, function(k,v){
            $(v).attr('data-order', lastOrderId);
        });
        var fin_inst = JSON.parse(window.sessionStorage.getItem('fin_inst'));
        $.each(fin_inst,function(k,v){
            $(institutions).append($('<option>',{value: v.id, text: v.name}));
        });
        $(templateClone).removeClass('template');
        templateClone.attr('data-order',lastOrderId);
        templateClone.css('display','block');
        main.append(templateClone); 
    });

    $.datepicker.setDefaults($.datepicker.regional['sk']);
    $('.doc-cal').datepicker({
            dateFormat: "dd.mm.yy",
            showOtherMonths: true,
            selectOtherMonths: true,
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            yearRange: '1920:2100',
            minDate: new Date(1920,1-1,1),
            autoclose: false
        }
    );
    
    $('#save-client-cashflow').on('click',function(){
        var other_income = getDataItems('.o-income');
        var expenses = getDataItemsWithOrder('.pp-list');
        var other_expenses = getDataItems('.o-exp');
         $.ajax({
           url: "/app-request/ajax-save-client-cashflow",
           dataType: "json",
           data: { 
               other_income: other_income,
               expenses: expenses,
               other_expenses: other_expenses,
               client_id: $('#client_id').val(), 
               {$csrf} 
           },
           type: "post"
       })
       .done(function(res){
          if (res.status === 'error') {
             alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              $('#client-cashflow').hide();
              var _x = JSON.parse(window.sessionStorage.getItem('product-request'));
              if (_x['mortgage'] === '1') {
                  $('#mortgage-calculator').show();
              }
              if (_x['refin_mortgage'] === '1') {
                  $('#refinance-calculator').show();
              } 
              if (_x['loan'] === '1') {
                  $('#loan-calculator').show();
              }
          }
       });
    });
    
    $('#save-client-business').on('click',function(){
        var bizData = getDataItemsWithOrder('.biz');
        $.ajax({
           url: "/app-request/ajax-save-client-businesses",
           dataType: "json",
           data: { 
               bizdata: bizData,
               client_id: $('#client_id').val(), 
               {$csrf} 
           },
           type: "post"
       })
       .done(function(res){
          if (res.status === 'error') {
             alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              // hide and show divs
              $('#client-business').hide();
              $('#client-cashflow').show();
          }
       });
    });
    
    $('#save-client-jobs').on('click',function SaveClientJobsAndSocialData(){
        var socialData = getDataItems('.social-data');
        var jobs = getDataItemsWithOrderAndTypeCheck('.jobs');
        $.ajax({
           url: "/app-request/ajax-save-client-jobs",
           dataType: "json",
           data: { 
               socialdata: socialData,
               jobs: jobs, 
               client_id: $('#client_id').val(), 
               {$csrf} 
           },
           type: "post"
       })
       .done(function(res){
          if (res.status === 'error') {
             alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              // hide and show divs
              var win = window.sessionStorage;
              if (
                  win.getItem('self-emp') === '1' || 
                  win.getItem('bus-owner') === '1'
              ) {
                  $('#income-src').hide();
                  $('#client-jobs').hide();
                  var toRepeat = parseInt(win.getItem('bus-owner-cnt'));
                  toRepeat += win.getItem('self-emp') === '1' ? 1 : 0;
                  if (toRepeat > 1) {
                      for(var i=0; i < toRepeat-1; i++) {
                            var lastOne = $('.business:last');
                            var lastClone = lastOne.clone(true);
                            var lastOneOrder = i + 2;
                            var h5 = $(lastClone).find("h5");
                            var h5Title = h5.html().split('.');
                            h5.html(h5Title[0] + '.' + lastOneOrder);
                            var toUpdate = $(lastClone).find(".biz");
                            $.each(toUpdate,function(k,v){
                                $(v).attr('data-order',lastOneOrder);
                            });
                            lastClone.attr('data-order',lastOneOrder);
                            lastOne.after(lastClone);
                      }
                  }
                  $('#client-business').show();
              } else {
                  $('#client-jobs').hide();
                  $('#client-business').hide();
                  $('#income-src').hide();
                  $('#client-cashflow').show();
              }
          }
       });
    });
    
    $('#save-income-src').on('click',function(){
        window.sessionStorage.setItem('perm-work', $('#perm-work').is(':checked') ? '1': '0');
        window.sessionStorage.setItem('perm-work-cnt',$('#perm-work-cnt').val());
        window.sessionStorage.setItem('self-emp',$('#self-emp').is(':checked') ? '1' : '0');
        window.sessionStorage.setItem('bus-owner',$('#bus-owner').is(':checked') ? '1':'0');
        window.sessionStorage.setItem('bus-owner-cnt',$('#bus-owner-cnt').val());
        if (window.sessionStorage.getItem('perm-work') === '1') {
            $('#income-src').hide();
            var toRepeat = parseInt(window.sessionStorage.getItem('perm-work-cnt'));
            if ( toRepeat>1) {
                for(var i=0; i < toRepeat-1; i++) {
                    var lastOne = $('.employer:last');
                    var lastClone = lastOne.clone(true);
                    var lastOneOrder = i + 2;
                    var h5 = $(lastClone).find("h5");
                    var h5Title = h5.html().split('.');
                    h5.html(h5Title[0] + '.' + lastOneOrder);
                    var toUpdate = $(lastClone).find(".jobs");
                    $.each(toUpdate,function(k,v){
                        $(v).attr('data-order',lastOneOrder);
                    });
                    lastClone.attr('data-order',lastOneOrder);
                    lastOne.after(lastClone);
                }
            }
            
            $('#client-jobs').show();
            
        }
    });
    
    $('#client-save-family-data').on('click',function(){
        var dat = getDataItems('.fam-data');
        $.ajax({
           url: "/app-request/ajax-save-family-data",
           dataType: "json",
           data: { 
               client_data: dat, 
               client_id: $('#client_id').val(), 
               {$csrf} 
           },
           type: "post"
       })
       .done(function(res){
          if (res.status === 'error') {
             alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              // hide and show divs
              $('#family-data').hide();
              $('#income-src').show();
          }
       });
    });
    
    $('#save-client-docs').on('click',function (){
        var dat =  getDataItemsWithOrder('.client-docs');
        $.ajax({
           url: "/app-request/ajax-save-client-docs",
           dataType: "json",
           data: { 
               client_data: dat, 
               client_id: $('#client_id').val(), 
               {$csrf} 
           },
           type: "post"
       })
       .done(function(res){
          if (res.status === 'error') {
             alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              // hide and show divs
              $('#client-docs').hide();
              $('#family-data').show();
          }
       });
    });
    
    $('#save-contact').on('click',function (){
        var dat = getDataItems('.contact');
        var _x = new Map();
        var req = new Map();
        $.each($('.creq'),function(k,v){
            var ke = $(v).data('item');
            var va = $(v).is(':checked') ? 1 : 0;
            req.set(ke,va );
            _x[ke] = va;
        });
        window.sessionStorage.setItem('product-request', JSON.stringify(_x));
        $.ajax({
           url: "/app-request/ajax-save-client",
           dataType: "json",
           data: { 
               client_data: dat, 
               client_id: $('#client_id').val(), 
               reqs: Object.fromEntries(req), 
               clnews: $('#cl-news').is(':checked') ? 1 : 0,
               clconsent: $('#cl-consent').is(':checked') ? 1 : 0,
               {$csrf} 
           },
           type: "post"
       })
       .done(function(res){
          if (res.status === 'error') {
              alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              // hide and show divs
              $('#p-1').hide();
              $('#client-req').hide();
              $('#referal').hide();
              $('#client-contact').hide();
              $('#client-papers').show();
          }
       });
    });
  
    $('#save-client-data').on('click',function (){
        var dat = getDataItems('.client-data');
        $.ajax({
           url: "/app-request/ajax-save-client-data",
           dataType: "json",
           data: { client_data: dat, client_id: $('#client_id').val(), {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status === 'error') {
             alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              // hide and show divs
              $('#client-basic-data').hide();
              $('#client-address').show();
          }
       });
    });
   
    $('#save-client-address').on('click',function (){
        var dat = getDataItems('.client-address');
        $.ajax({
           url: "/app-request/ajax-save-client-address",
           dataType: "json",
           data: { client_data: dat, client_id: $('#client_id').val(), {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status ==='error') {
             alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              // hide and show divs
              $('#client-address').hide();
              $('#client-personal-data').show();
          }
       });
    });
   
    $('#save-other-personal-data').on('click',function (){
        var dat = getDataItems('.other-personal');
        $.ajax({
           url: "/app-request/ajax-save-client-other-personal-data",
           dataType: "json",
           data: { client_data: dat, client_id: $('#client_id').val(), {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status === 'error') {
              alert(res.message);
          } else {
              $('#client_id').val(res.client_id);
              // hide and show divs
              $('#client-personal-data').hide();
              $('#client-docs').show();
          }
       });
    });
    
    $(document).on('change', '#application-source', function () {
        let v = $(this).val();
        if( v === 'nodef' && v !== 'refcode') {
            $('#other-src').show();
            $('#referal-code').hide();
        } else if( v === 'refcode' && v !== 'nodef') {
            $('#referal-code').show();
            $('#other-src').hide();
        } else {
            $('#referal-code').hide();
            $('#other-src').hide();
        }
    });
    
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
                "phone": "Telefón",
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
    
    $(document).on('change', '#soc-sec-sickleave', function () {
        if ($(this).val() === 1) {
            $('.sickleave-row').show();
            $('.sickleave').css('visibility','visible');
        } else if ($(this).val() === 2) {
            $('.sickleave-row').show();
            $('.sickleave').css('visibility','hidden');
        } else {
            $('.sickleave-row').hide();
            $('.sickleave').css('visibility','hidden');
        }
    });
    
    $(document).on('click', '#perm-addr1', function () {
        $('.client-other-addr').each(function () {
            $(this).hide();
        });
    });
    
    $(document).on('click', '#perm-addr2', function () {
        $('.client-other-addr').each(function () {
            $(this).show();
        });
    });
    
    function slovakSSN(ssn,gender)
    {
        var bdate = '';
        var ssncontrol = '';
        var by = '', bm = '', bd = '';
    
        if (ssn.indexOf('/') !== -1) {
            bdate = (ssn.split('/'))[0];
            ssncontrol = (ssn.split('/'))[1];
        } else {
            bdate = ssn.substring(0,6);
            ssncontrol = ssn.substring(6,ssn.length);
        }
        by = parseInt(bdate.substring(0,2),10);
        bm = parseInt(bdate.substring(2,2),10);
        bd = bdate.substring(4,2);
    
        if (gender === 'f') {
            bm -= 50;
        }
    
        if (by < 54 && ssncontrol.length === 3) {
            by += 1900;
        } else if (by < 54 && ssncontrol.length === 4) {
            by += 2000;
        } else {
            by += 1900;
        }
    
        // fix bigger year than the actual year
        var y = (new Date()).getFullYear();
        if (y < by) {
            by -= 100;
        }
    
        return bd + '.' + bm.toString().padStart(2,'0') + '.' + by.toString();
    }

    $('#c-ssn').on('blur', function calcBirthDay()
    {
        var gend = $('#c-gend').val();
        var ssn = $(this).val();
        var country = $('#addr-country').val();
        var bDate = '';
    
        if (country === 1) {
            // slovakia code
            bDate = slovakSSN(ssn, gend);
            $('#c-birth').val(bDate);
        }
    });
    
    $('#add-document').on('click',function () {
        var lastOne = $('div.doc:last');
        var lastClone = lastOne.clone(true);
        var lastOneOrder = parseInt(lastOne.data('order')) + 1;
    
        var h5 = $(lastClone).find("h5");
        var h5Title = h5.html().split('.');
        h5.html(h5Title[0] + '.' + lastOneOrder);
    
        var toUpdate = $(lastClone).find(".client-docs");
        $.each(toUpdate,function (k,v) {
            $(v).attr('data-order',lastOneOrder);
        });
    
        lastClone.attr('data-order',lastOneOrder);
        lastOne.after(lastClone);
    });
    
    $('#perm-work').on('change',function () {
        let x = $(this).is(':checked') ? 'visible' : 'hidden';
        $('#perm-work-cnt').css('visibility', x);
    });
    
    $('#bus-owner').on('change',function () {
        let x = $(this).is(':checked') ? 'visible' : 'hidden';
        $('#bus-owner-cnt').css('visibility', x);
    });
    
    function getDataItemsWithOrder(elm)
    {
        var dat =  [];
        $.each($(elm), function (k,v) {
            var ke = $(v).data('item');
            var va = $(v).val();
            var or = $(v).data('order');
            dat.push({item:ke,val:va,order:or});
        });
        return dat;
    }
    
    function getDataItems(elm)
    {
        var dat = [];
        $.each($(elm), function (k,v) {
            var ke = $(v).data('item');
            var va = $(v).val();
            dat.push({item:ke,val:va});
        });
        return dat;
    }
    
    function getDataItemsWithOrderAndTypeCheck(elm)
    {
        var dat =  [];
        $.each($(elm), function (k,v) {
            var va;
            if ($(v).is(':radio')) {
                if ($(v).is(':checked')) {
                    va = $(v).data('default-value');
                } else {
                    return true;
                }
            } else {
                va = $(v).val();
            }
            var ke = $(v).data('item');
            var or = $(v).data('order');
    
            dat.push({item:ke,val:va,order:or});
        });
        return dat;
    }
    
    $('#maternity').on('change',function () {
        if ($(this).val() === 1) {
            $('.maternity-row').show();
            $('.maternity-leave').css('visibility','visible');
        } else if ($(this).val() === 2 || $(this).val() === 3) {
            $('.maternity-row').show();
            $('.maternity-leave').css('visibility','hidden');
        } else {
            $('.maternity-row').hide();
            $('.maternity-leave').css('visibility','hidden');
        }
    });
    $('#inv-pension').on('change',function () {
        if ($(this).val() === 1) {
            $('.inv-pension-row').show();
            $('.inv-pension-to').css('visibility','hidden');
        } else if ($(this).val() === 2 || $(this).val() === 3) {
            $('.inv-pension-row').show();
            $('.inv-pension-to').css('visibility','hidden');
        } else {
            $('.inv-pension-row').hide();
            $('.inv-pension-to').css('visibility','hidden');
        }
    });
    $('#pension').on('change',function () {
        if ($(this).val() === 1 || ($(this).val() === 2)) {
            $('.pension-row').show();
        } else {
            $('.pension-row').hide();
        }
    });
    
    $('#cl-famdata-back').on('click',function () {
        $('#family-data').hide();
        $('#client-docs').show();
    });
    
    $('#nourishing').on('change',function () {
        if ($(this).val() === 'y') {
            $('.vyzivne').show();
        } else {
            $('.vyzivne').hide();
        }
    });

    $('#pay-cash').on('click',function () {
        $('.payroll').each(function () {
            $(this).hide();
        });
    });
    $('#pay-acc').on('click',function () {
        $('.payroll').each(function () {
            $(this).show();
        });
    });

    function cloneLoansField(e,f)
    {
        var lastOne = $('div.' + e + ':last');
        var lastClone = lastOne.clone(true);
        var lastOneOrder = parseInt(lastOne.data('order')) + 1;
    
        var toUpdate = $(lastClone).find('.' + f);
        $.each(toUpdate,function (k,v) {
            $(v).attr('data-order',lastOneOrder);
        });
    
        lastClone.attr('data-order',lastOneOrder);
        lastOne.after(lastClone);
    }
    
    $('.add-loan').on('click',function () {
        cloneLoansField('loan-1','p-loan-1');
    });
    
    $('.add-limit').on('click',function () {
        cloneLoansField('limit-1','p-limit-1');
    });
    
    $('#cl-bas-data-back').on('click',function () {
        $('#client-papers').show();
        $('#client-basic-data').hide();
    });

    $('#cl-pap-next').on('click',function () {
        $('#client-papers').hide();
        $('#client-basic-data').show();
    });
    
    $('#cl-pap-back').on('click',function () {
        $('#p-1').show();
        $('#client-req').show();
        $('#referal').show();
        $('#client-contact').show();
        $('#client-papers').hide();
    });

    $('#cl-addr-back').on('click',function () {
        $('#client-address').hide();
        $('#client-basic-data').show();
    });
    
    $('#cl-persdat-back').on('click',function () {
        $('#client-personal-data').hide();
        $('#client-address').show();
    });
    $('#cust-docs-back').on('click',function () {
        $('#client-docs').hide();
        $('#client-personal-data').show();
    });

    function calcAvg(x, a)
    {
        return Math.round(x * 100 / a) / 100;
    }
    
    $('.calc-avg-wage').on('blur', function CalculateAvgWage()
    {
        var orderid = $(this).data('order');
        var wages = $('.calc-avg-wage'), sum = 0;
        for (var i = 0; i < wages.length; i++) {
            if ($(wages[i]).data('order') !== orderid) {
                continue;
            }
            sum += parseFloat($(wages[i]).val());
            if ( i === 3 ) {
                $('#avg-4m-wage').val(calcAvg(sum, 4));
            }
            if ( i === 5) {
                $('#avg-6m-wage').val(calcAvg(sum, 6));
            }
        }
        $('#avg-12m-wage').val(calcAvg(sum, 12));
    });
    
JS;

$this->registerJS($js);
