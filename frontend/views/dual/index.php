<?php

use yii\widgets\Breadcrumbs;
use yii\helpers\Html;

$this->title = 'Dual prax';

/**
 * @var $fields
 * @var $partners
 */
?>
    <main class="site-applicant">
        <input type="hidden" id="client_id" value="0">
        <div class="page-banner d-block position-relative raleway">
            <canvas style="background-image:url('/images/contact-us-banner-1.jpg');" width="1600" height="400"></canvas>
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
                        <h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true"
                            data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s"
                            data-aios-animation-reset="false"
                            data-aios-animation-offset="0" style="animation-delay: 0.3s;">
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
        </div>
        <div class="container-fluid">
            <div class="dual-container">

                <form method="post" role="form" id="t009">
                    <input type="hidden" name="Reg[type]" id="c0" value="dual_student">
                    <input
                        type="hidden"
                        name="<?= Yii::$app->request->csrfParam; ?>"
                        value="<?= Yii::$app->request->getCsrfToken() ?>"
                    >

                    <div class="form-group row" style="margin-bottom: 30px;">
                        <!--<div class="col-md-4 col-12">
                            <button
                                type="button"
                                class="form-control reg-button reg-button-toggled"
                                id="r0"
                            >
                                <?= Yii::t('dual', 'Registrácia partnera') ?>
                            </button>
                        </div>
                        <div class="col-md-4 col-12">
                            <button type="button" class="form-control reg-button" id="r1">
                                <?= Yii::t('dual', 'Registrácia školy') ?>
                            </button>
                        </div>-->
                        <div class="col-md-4 col-12">
                            <button type="button" class="form-control reg-button reg-button-toggled" id="r2">
                                Registrácia študenta / Diák regisztráció
                            </button>
                        </div>
                    </div>

                    <div id="r0_pok" class="dis-none">

                        <h3 style="margin-bottom: 0">
                            Základné údaje / Alapadatok
                        </h3>
                        <h5 class="head-note">Všetky polia sú povinné!</h5>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Názov partnera</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][name]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Adresa</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][address]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Mesto</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][town]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">PSČ</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][zip]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">IČO</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][comp_reg]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">DIČ</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][tax_reg]">
                            </div>
                        </div>

                        <h3 style="margin-top: 40px; margin-bottom: 0">Kontaktná osoba</h3>
                        <h5 class="head-note">Všetky polia sú povinné!</h5>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Meno</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][contact_first_name]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Priezvisko</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][contact_last_name]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Email</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][contact_email]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Telefón</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][contact_phone]">
                            </div>
                        </div>

                        <h3 style="margin-top: 40px; margin-bottom: 0">Študijné odbory</h3>
                        <h5 class="head-note">Vyberte aspoň jednu položku!</h5>

                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Odbor</th>
                                    <th>Max. poč. študentov</th>
                                    <th>Poč. inštruktorov</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($fields as $field) : ?>
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="Reg[partner][field][<?= $field['id'] ?>][code]"
                                            value="<?= $field['code'] ?>"
                                        >
                                    </td>
                                    <td><?= $field['code'] ?> - <?= $field['name'] ?></td>
                                    <td>
                                        <input
                                            type="number"
                                            name="Reg[partner][field][<?= $field['id']?>][max_stud]"
                                            class="form-control"
                                            min="0"
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="Reg[partner][field][<?= $field['id']?>][max_inst]"
                                            class="form-control"
                                            min="0"
                                        >
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div id="r1_pok" class="dis-none">

                        <h3 style="margin-bottom: 0">
                            <?= Yii::t('dual', 'Základné údaje') ?>
                        </h3>
                        <h5 class="head-note">Všetky polia sú povinné!</h5>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 col-12">Názov školy</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][name]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Adresa</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][address]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Mesto</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][town]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">PSČ</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][zip]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">IČO</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][comp_reg]">
                            </div>
                        </div>

                        <h3 style="margin-top: 40px; margin-bottom: 0">Kontaktná osoba</h3>
                        <h5 class="head-note">Všetky polia sú povinné!</h5>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Meno</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][contact_first_name]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Priezvisko</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][contact_last_name]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Email</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][contact_email]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Telefón</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[school][contact_phone]">
                            </div>
                        </div>

                        <h3 style="margin-top: 40px; margin-bottom: 0">Študijné odbory poskytnuté školou</h3>
                        <h5 class="head-note">Vyberte aspoň jednu položku!</h5>

                        <table class="table table-borderless table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Odbor</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($fields as $field) : ?>
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="Reg[school][field][code][]"
                                            value="<?= $field['code'] ?>">
                                    </td>
                                    <td><?= $field['code'] ?> - <?= $field['name'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>

                    <div id="r2_pok" style="margin-bottom: 20px;">
                        <h3 style="margin-bottom: 0">
                            Základné údaje / Alapadatok
                        </h3>
                        <h5 class="head-note">
                            Hviezdičkou označené polia sú povinné! /
                            A csillaggal megjelölt mező kitöltése kötelező!
                        </h5>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Meno / Keresztnév
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-1"
                                       name="Reg[student][first_name]"
                                       data-eid="1"
                                >
                                <p class="error-msg" id="ep-1"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Priezvisko / Vezetéknév
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-2"
                                       name="Reg[student][last_name]"
                                       data-eid="2"
                                >
                                <p class="error-msg" id="ep-2"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Pohlavie / Nem</label>
                            <div class="col-sm-9 col-12">
                                <select class="form-control" name="Reg[student][gender]">
                                    <option value=""></option>
                                    <option value="m">mužské / férfi</option>
                                    <option value="f">ženské / nő</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-12 col-form-label red-star">
                                Dátum narod. / Szül. dátum
                            </label>
                            <div class="col-sm-3 col-12">
                                <select
                                        name="Reg[student][birthdate][day]"
                                        class="form-control rq ef-3"
                                        data-eid="3"
                                >
                                    <option value="">Deň / Nap</option>
                                    <?php for ($i = 1; $i < 32; $i++) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <p class="error-msg" id="ep-3"></p>
                            </div>
                            <div class="col-sm-3 col-12">
                                <select
                                        name="Reg[student][birthdate][month]"
                                        class="form-control rq ef-4"
                                        data-eid="4"
                                    >
                                    <option value="">Mesiac / Hónap</option>
                                    <?php for ($i = 1; $i < 13; $i++) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <p class="error-msg" id="ep-4"></p>
                            </div>
                            <div class="col-sm-3 col-12">
                                <select
                                        name="Reg[student][birthdate][year]"
                                        class="form-control rq ef-5"
                                        data-eid="5"
                                >
                                    <?php
                                    $thisYear = date('Y');
                                    ?>
                                    <option value="">Rok / Év</option>
                                    <?php for ($i = $thisYear - 10; $i > 1999; $i--) : ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>s
                                </select>
                                <p class="error-msg" id="ep-5"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Výška / Magasság (cm)</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[student][height]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Hmotnosť / Súly (kg)</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[student][height]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Veľkosť obuvy / Cipőméret</label>
                            <div class="col-sm-9 col-12">
                                <select class="form-control" name="Reg[student][foot_size]">
                                    <option value=""></option>
                                    <?php for ($i = 36; $i < 45; $i += 0.5) : ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <?php
                        $tShirtSize = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
                        ?>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Veľkosť trička / Póló mérete</label>
                            <div class="col-sm-9 col-12">
                                <select class="form-control" name="Reg[student][tshirt]">
                                    <option value=""></option>
                                    <?php foreach ($tShirtSize as $size) : ?>
                                        <option value="<?= $size ?>"><?= $size ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Obvod pásu / Derékbőség</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[student][waist]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Dĺžka nohavíc / Nadrág hossza</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[student][length]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">IBAN</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[student][iban]">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-3 col-12 col-form-label">
                                Materinský jazyk / Anyanyelv
                            </label>
                            <div class="col-sm-9 col-12">
                                <select name="Reg[student][jazyk]" class="form-control">
                                    <option value=""></option>
                                    <option value="2">magyar</option>
                                    <option value="1">slovenský</option>
                                </select>
                            </div>
                        </div>

                        <h3 style="margin-bottom: 0;">Kontaktné údaje / Elérhetőségek</h3>
                        <h5 class="head-note">
                            Hviezdičkou označené polia sú povinné! /
                            A csillaggal megjelölt mező kitöltése kötelező!
                        </h5>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">
                                Adresa / Cím
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[student][address]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">
                                Mesto / Város
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[student][town]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">
                                PSČ / Irányítószám
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[student][zip]">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">Email</label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-7"
                                       name="Reg[student][email]"
                                       data-eid="7"
                                >
                                <p class="error-msg" id="ep-7"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Telefón
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-8"
                                       name="Reg[student][phone]"
                                       data-eid="8"
                                >
                                <p class="error-msg" id="ep-8"></p>
                            </div>
                        </div>

                        <h3 style="margin-top: 40px; margin-bottom: 0">Škola a odbor / Iskola és szak</h3>
                        <h5 class="head-note">
                            Všetky polia sú povinné! / Minden mező kitöltése kötelező!
                        </h5>
                        
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3 col-12 red-star">
                                Moja škola / Iskolám
                            </label>
                            <div class="col-sm-9 col-12">
                                <select name="Reg[student][school]"
                                        class="form-control rq ef-9"
                                        id="t001"
                                        data-eid="9"
                                >
                                    <option value=""></option>
                                    <?php /** @var $schools */
                                    foreach ($schools as $school) : ?>
                                        <option value="<?= $school->id ?>"><?= $school->partner_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="error-msg" id="ep-9"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3 col-12 red-star">
                                Môj odbor / Szakom
                            </label>
                            <div class="col-sm-9 col-12">
                                <select name="Reg[student][study_field]"
                                        id="stf01"
                                        class="form-control rq ef-10"
                                        data-eid="10"
                                >
                                    <option value=""></option>
                                </select>
                                <p class="error-msg" id="ep-10"></p>
                            </div>
                        </div>

                        <h3 style="margin-bottom: 0; margin-top: 40px">Zákonný zástupcovia / Szülők</h3>
                        <h5 class="head-note" style="margin-bottom: 0;">
                            V prípade plnej rodiny treba uviesť oboch rodičov! /
                            Teljes család esetén mindkét szülőt meg kell említeni!
                        </h5>
                        <h5 class="head-note">
                            Všetky polia sú povinné! / Minden mező kitöltése kötelező!
                        </h5>

                        <h6 class="parents">Matka / Anya</h6>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">Meno / Keresztnév</label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-11"
                                       name="Reg[student][mother][first_name]"
                                       data-eid="11"
                                >
                                <p class="error-msg" id="ep-11"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">Priezvisko / Vezetéknév</label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-12"
                                       name="Reg[student][mother][last_name]"
                                       data-eid="12"
                                >
                                <p class="error-msg" id="ep-12"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">Adresa / Cím</label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       name="Reg[student][mother][address]"
                                       class="form-control rq ef-13"
                                       data-eid="13"
                                >
                                <p class="error-msg" id="ep-13"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">Mesto / Város</label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       name="Reg[student][mother][town]"
                                       class="form-control rq ef-14"
                                       data-eid="14"
                                >
                                <p class="error-msg" id="ep-14"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">PSČ / Irányítószám</label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       name="Reg[student][mother][zip]"
                                       class="form-control rq ef-15"
                                       data-eid="15"
                                >
                                <p class="error-msg" id="ep-15"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">Email</label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-16"
                                       name="Reg[student][mother][email]"
                                       data-eid="16"
                                >
                                <p class="error-msg" id="ep-16"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">Telefón</label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-17"
                                       name="Reg[student][mother][phone]"
                                       data-eid="17"
                                >
                                <p class="error-msg" id="ep-17"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-12 col-form-label red-star">
                                Dátum narod. / Szül. dátum
                            </label>
                            <div class="col-sm-3 col-12">
                                <select name="Reg[student][mother][birthdate][day]"
                                        class="form-control rq ef-18"
                                        data-eid="18"
                                >
                                    <option value="">Deň / Nap</option>
                                    <?php for ($i = 1; $i < 32; $i++) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <p class="error-msg" id="ep-18"></p>
                            </div>
                            <div class="col-sm-3 col-12">
                                <select name="Reg[student][mother][birthdate][month]"
                                        class="form-control rq ef-19"
                                        data-eid="19"
                                >
                                    <option value="">Mesiac / Hónap</option>
                                    <?php for ($i = 1; $i < 13; $i++) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <p class="error-msg" id="ep-19"></p>
                            </div>
                            <div class="col-sm-3 col-12">
                                <select name="Reg[student][mother][birthdate][year]"
                                        class="form-control rq ef-20"
                                        data-eid="20"
                                >
                                    <?php
                                    $thisYear = date('Y');
                                    ?>
                                    <option value="">Rok / Év</option>
                                    <?php for ($i = $thisYear; $i > 1940; $i--) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>s
                                </select>
                                <p class="error-msg" id="ep-20"></p>
                            </div>
                        </div>

                        <h6 class="parents">Otec / Apa</h6>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Meno / Keresztnév
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-22"
                                       name="Reg[student][father][first_name]"
                                       data-eid="22"
                                >
                                <p class="error-msg" id="ep-22"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Priezvisko / Vezetéknév
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-23"
                                       name="Reg[student][father][last_name]"
                                       data-eid="23"
                                >
                                <p class="error-msg" id="ep-23"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Adresa / Cím
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       name="Reg[student][father][address]"
                                       class="form-control rq ef-24"
                                       data-eid="24"
                                >
                                <p class="error-msg" id="ep-24"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Mesto / Város
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       name="Reg[student][father][town]"
                                       class="form-control rq ef-25"
                                       data-eid="25"
                                >
                                <p class="error-msg" id="ep-25"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                PSČ / Irányítószám
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       name="Reg[student][father][zip]"
                                       class="form-control rq ef-26"
                                       data-eid="26"
                                >
                                <p class="error-msg" id="ep-26"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Email
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-27"
                                       name="Reg[student][father][email]"
                                       data-eid="27"
                                >
                                <p class="error-msg" id="ep-27"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12 red-star">
                                Telefón
                            </label>
                            <div class="col-sm-9 col-12">
                                <input type="text"
                                       class="form-control rq ef-28"
                                       name="Reg[student][father][phone]"
                                       data-eid="28"
                                >
                                <p class="error-msg" id="ep-28"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-12 col-form-label red-star">
                                Dátum narod. / Szül. dátum
                            </label>
                            <div class="col-sm-3 col-12">
                                <select name="Reg[student][father][birthdate][day]"
                                        class="form-control rq ef-29"
                                        data-eid="29"
                                >
                                    <option value="">Deň / Nap</option>
                                    <?php for ($i = 1; $i < 32; $i++) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <p class="error-msg" id="ep-29"></p>
                            </div>
                            <div class="col-sm-3 col-12">
                                <select name="Reg[student][father][birthdate][month]"
                                        class="form-control rq ef-30"
                                        data-eid="30"
                                >
                                    <option value="">Mesiac</option>
                                    <?php for ($i = 1; $i < 13; $i++) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <p class="error-msg" id="ep-30"></p>
                            </div>
                            <div class="col-sm-3 col-12">
                                <select name="Reg[student][father][birthdate][year]"
                                        class="form-control rq ef-31"
                                        data-eid="31"
                                >
                                    <?php
                                    $thisYear = date('Y');
                                    ?>
                                    <option value="">Rok</option>
                                    <?php for ($i = $thisYear; $i > 1940; $i--) : ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>s
                                </select>
                                <p class="error-msg" id="ep-31"></p>
                            </div>
                        </div>

                        <h3 style="margin-bottom: 0; margin-top: 40px">
                            Partneri / Partnerek
                        </h3>
                        <h5 class="head-note">
                            Zvoľte si poradie partnerov! / Rakd sorrendbe a partnereket!
                        </h5>

                        <table class="table table-striped" role="table" id="t09">
                            <thead>
                                <tr>
                                    <th style="width: 20%">Poradie / Sorrend</th>
                                    <th>Názov / Név</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($partners as $partner) : ?>
                            <tr>
                                <td>
                                    <select
                                            class="form-control pa"
                                            name="Reg[student][partner][order]">
                                            data-pid="<?= $partner['id'] ?>"
                                        <option value=""></option>
                                        <?php for ($i = 0; $i < count($partners); ++$i) : ?>
                                            <option value="<?= $i + 1 ?>"><?= $i + 1 ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                                <td><?= $partner['partner_name'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>

                    <div style="margin-top:80px;"
                         class="g-recaptcha"
                         data-sitekey="6Lfs91EqAAAAACqBbzxjOxQ3nZItbdBSWh_fAarO"></div>

                    <div class="row" style="margin-top: 40px">
                        <div class="col-sm-12" style="text-align: center">
                            <button type="submit" class="btn-sm">Registrovať sa / Regisztrálok</button>
                        </div>
                    </div>

                </form>



            </div>
        </div>
    </main>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$css = <<<CSS
:root {
    --mid-size-text: 1.5rem;
    --mid-star: 18pt;
}
.dual-container {
    margin: 20px auto;
    width: 70%;
}
.reg-button {
    border: 2px solid black;
    border-radius: 3px;
}
.reg-button:hover {
    background-color: #c49144;
    color: white;
    font-weight: bold;
}
.reg-button-toggled {
    background-color: #c49144;
    color: white;
    font-weight: bold;
}
.dis-none {
    display: none;
}
.head-note {
    color: red;
    font-size: var(--mid-size-text);
    margin-bottom: 20px;
}
h6.parents {
    font-weight: bold;
    margin-bottom: 20px;
}
label.red-star:after {
    color: red;
    font-size: var(--mid-star);
    content: " *";
    margin: 0;
}
.error-msg {
    font-size: 0.8em; 
    color: red;
}
.error-border {
    border-color: red;
}
CSS;

$this->registerCss($css);

$js = <<<JS
    $(document).on('change', '#stf01', function() {
        $.ajax({
            url: '/dual/get-partners',
            type: 'POST',
            data: { sfield: $(this).val(), {$csrf} },
            success: function (data) {
                
            }
        })
    });

    $(document).on('change', '#t001', function () {
        $.ajax({
            url: '/dual/get-study-fields',
            type: 'POST',
            data: { school: $(this).val(), {$csrf} },
            success: function (data) {
                let x = $('#stf01').empty();
                for (i=0; i<data.list.length; i++) {
                    x.append($('<option></option>',{value:data.list[i].value, text:data.list[i].label}));    
                }
            }
        })
    });
        
    $(document).on('click', '#r0', function () {
        $('#r0_pok').show();
        $('#r1_pok').hide();
        $('#r2_pok').hide();
        $('#r0').addClass('reg-button-toggled');
        $('#r1').removeClass('reg-button-toggled');
        $('#r2').removeClass('reg-button-toggled');
        $('#c0').val('dual_partner');
    });
    $(document).on('click', '#r1', function () {
        $('#r0_pok').hide();
        $('#r1_pok').show();
        $('#r2_pok').hide();
        $('#r0').removeClass('reg-button-toggled');
        $('#r1').addClass('reg-button-toggled');
        $('#r2').removeClass('reg-button-toggled');
        $('#c0').val('dual_skola');
    });
    $(document).on('click', '#r2', function () {
        $('#r0_pok').hide();
        $('#r1_pok').hide();
        $('#r2_pok').show();
        $('#r0').removeClass('reg-button-toggled');
        $('#r1').removeClass('reg-button-toggled');
        $('#r2').addClass('reg-button-toggled');
        $('#c0').val('dual_student');
    });
    
    $(document).on('blur', '.rq', function() {
        let v = $(this).val().trim();
        if (v.length > 0) {
            let i = $(this).data('eid');
            $(this).removeClass('error-border');
            $('#ep-' + i).html('');
        } 
    });
    
    $(document).on('submit', '#t009', function () {
        var emptyVar = 0;
        $('.rq').each(function () {
            let s = $(this).val().trim();
            if (s.length === 0) {
                let x = $(this).data('eid');
                ++emptyVar;
                $('#ep-' + x).html('Povinné! / Kötelező!');
                $('.ef-' + x).addClass('error-border');
            }
        });
        
        if (emptyVar > 0) {
            return false;
        }
        
        let v = grecaptcha.getResponse();
        if (v.length === 0) {
            return false;    
        }
        return true; 
    });
JS;
$this->registerJs($js);