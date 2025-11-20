<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Ďakujeme za využívanie naších služieb');
?>
<main>
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
                        <strong><?= Html::encode($this->title) ?> !</strong>
                    </h1>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content">
        <h1>
            <?= Yii::t('app', 'Ďakujeme že ste využili naše služby') ?> !
        </h1>
        <h3>
            <?= Yii::t('app', 'Na Vašu emailovú adresu Vám bol zaslaný informačný email.') ?>
        </h3>
    </div>
</main>

<?php

$css = <<<CSS
    .page-content { 
        padding: 10rem;
    }
    .page-content > h1 {
        text-transform: uppercase;
        font-weight: 500;
    }
    .page-content > h3 {
        text-transform: none;
        font-weight: 400;
    }
CSS;
$this->registerCss($css);
