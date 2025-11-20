<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use frontend\models\Aoreal;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/**
 * @var $properties array
 * @var $model
 */

$this->params['breadcrumbs'][] = $this->title;

?>
<main class="site-properties">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url(/images/header-bg1.jpg);" width="1600" height="400"></canvas>
        <div class="page-border container-default d-block position-absolute mx-auto">
            <div class="page_title_line_left d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible"
                 data-aios-reveal="true"
                 data-aios-animation="fadeIn"
                 data-aios-animation-delay="0.2s"
                 data-aios-animation-reset="false"
                 data-aios-animation-offset="0" style="animation-delay: 0.2s;">
            </div>
            <div class="page_title_line_right d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible"
                 data-aios-reveal="true"
                 data-aios-animation="fadeIn"
                 data-aios-animation-delay="0.2s"
                 data-aios-animation-reset="false"
                 data-aios-animation-offset="0"
                 style="animation-delay: 0.2s;">
            </div>
        </div>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <h1 class="entry-title animated fadeInDown visible"
                        data-aios-reveal="true"
                        data-aios-animation="fadeInDown"
                        data-aios-animation-delay="0.3s"
                        data-aios-animation-reset="false"
                        data-aios-animation-offset="0"
                        style="animation-delay: 0.3s;">
                        <strong><?= Html::encode($this->title) ?></strong>
                    </h1>
                </div>
            </div>
        </div>
        <?= Alert::widget(); ?>
        <div class="breadcrumbs-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <?=
                        Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ?? [],
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="section-wrapper">
        <div class="container">
            <?php if ($properties && sizeof($properties)) : ?>
                <div id="properties-grid" class="row">
                <?php
                foreach ($properties as $property) {
                    $propTypeClass = $property['prop_type_id'] == 2 ?
                        $propTypeClass = 'for-rent' :  $propTypeClass = 'for-sale';

                    $propertyUrl = $property['rewrite_url'];
                    $property_price = Aoreal::displayPrice($property['price']);
                    // Plocha
                    if ($property['features']) {
                        if ($property['features']['plocha_celkova']) {
                            $plocha_title = 'Celková plocha';
                            $plocha_value = $property['features']['plocha_celkova'];
                        } elseif ($property['features']['plocha_zastavana']) {
                            $plocha_title = 'Zastavaná plocha';
                            $plocha_value = null;
                        } elseif ($property['features']['plocha_uzitkova']) {
                            $plocha_title = 'Úžitková plocha';
                            $plocha_value = $property['features']['plocha_uzitkova'];
                        } elseif ($property['features']['plocha_zahrada']) {
                            $plocha_title = 'Plocha záhrady';
                            $plocha_value = $property['features']['plocha_zahrada'];
                        } else {
                            $plocha_title = null;
                            $plocha_value = null;
                        }
                    } else {
                        $plocha_title = null;
                        $plocha_value = null;
                    }

                    if ($property['features'] && $property['features']['plocha_celkova']) {
                        $plocha_title = '';
                    }
                    ?>
                    <div class="col-md-3 col-sm-4 col-xs-12 property-details <?= $propTypeClass ?>">
                        <a
                           href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/nehnutelnost']) . '/' . $propertyUrl ?>"
                           class="property-image-wrapper">
                            <img src="<?= Aoreal::displayImage($property['cover'])?>"
                                 alt="<?= $property['title'] ?>">
                            <?= $property_price ? '<span class="property-price">' . $property_price . '</span>' : '' ?>
                            <span class="dark-overlay"></span>
                            <span class="item-link"></span>
                        </a>
                        <div class="property-descr">
                            <h3><?= $property['street'] ?? '' ?>
                                <br>
                                <?= $property['psc'] . ' ' . $property['mesto'] ?>
                                <span><?= $property['prop_type_name'] ?>
                                </span>
                            </h3>
                            <p><?= $property['title'] ?></p>
                            <hr>
                            <ul class="property-features">
                                <?php if ($plocha_title) : ?>
                                    <li class="property-space" title="<?= $plocha_title ?>"><?= $plocha_value ?>
                                        m<sup>2</sup>
                                    </li>
                                <?php endif; ?>
                                <li class="property-bathrooms">
                                    <?php $property['features'] ? $property['features']['pocet_kupelna'] : '' ?>
                                </li>
                                <li class="property-bedrooms">
                                    <?= $property['features'] ? $property['features']['pocet_kuchyna'] : '' ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php
                }
                ?>
                </div>
            <?php else : ?>
                <div style="margin: auto 50px">
                    <p class="text-center" style="margin-bottom: 40px;">
                        Je nám ľúto, ale nenašli sme nehnuteľnosť podľa Vami zadaných kritérií. <br>Pod spracovaním máme
                        ďalšie stovky ponúk. Zanechajte nám odkaz a my Vám radi pomôžeme s výberom nehnuteľnosti z našej
                        ponuky.
                    </p>
                    <h2>Odkaz pre realitných agentov</h2>
                    <?php $form = ActiveForm::begin(['id' => 'contact-form', 'layout' => 'default']); ?>
                    <div class="row contactus-form">
                        <div class="col-md-6 col-12">
                            <?= $form
                                ->field($model, 'name')
                                ->textInput(['autofocus' => true, 'size' => 40])
                                ->label('Meno') ?>
                        </div>
                        <div class="col-md-6 col-12">
                            <?= $form->field($model, 'surname')->label('Priezvisko') ?>
                        </div>
                    </div>
                    <div class="row contactus-form">
                        <div class="col-md-6 col-12">
                            <?= $form->field($model, 'phone')->label(Aoreal::trans('Telefónne číslo')) ?>
                        </div>
                        <div class="col-md-6 col-12">
                            <?= $form->field($model, 'email')->label(Aoreal::trans('E-mail')) ?>
                        </div>
                    </div>
                    <div class="row mt-3 contactus-form">
                        <?= $form->field($model, 'note')->textarea(['rows' => 6])->label('Správa') ?>
                    </div>
                    <?= $form
                        ->field($model, 'verifyCode')
                        ->label('Overovací kód')
                        ->widget(Captcha::class, [
                        'template' => '
                            <div class="row contactus-form">
                                <div class="col-lg-12">{image}</div>
                                <div class="col-lg-12">{input}</div>
                            </div>',
                    ]) ?>
                    <div class="form-group mt-9">
                        <?= Html::submitButton('Odoslať', [
                                'class' => 'btn btn-primary d-block mx-auto text-uppercase',
                                'name' => 'contact-button'
                            ]) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
$css = <<<CSS
.contactus-form input[type=text], 
.contactus-form input[type=tel], 
.contactus-form input[type=email], 
.contactus-form textarea {
    border:none;
    border-bottom: solid 2px #133045;
    background-image:none;
    background-color:transparent;
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
    display: block;
    width: 100%;
    padding-top: 10px;
    padding-bottom: 5px;
    font-size: 16px;
    text-transform: uppercase;
    outline: none !important;
    color: #666666;
    border-radius: 0;
}
.contactus-form input[type=text]:focus, 
.contactus-form input[type=tel]:focus, 
.contactus-form input[type=email]:focus, 
.contactus-form textarea:focus {
    border: none;
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
    border-bottom: solid 2px #133045;
}
.contactus-form textarea {
		height: 60px;
		padding-bottom: 5px;
		resize: none;
	}
CSS;
$this->registerCss($css);