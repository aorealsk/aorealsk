<?php
/**
 * @var $lang
 */
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = $lang === 'hu' ? 'Sikertelen hitelesítés' : 'Neúspešná validácia';
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $model
 */
?>
<main class="site-signup">
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
    <section id="contact-section-one">
        <div class="contactus-form" style="margin-top: 0px; width: 50%; margin-bottom: 50px;">
            <div class="wpcf7" id="wpcf7-f2130-o1" lang="en-US" dir="ltr">
                <div class="screen-reader-response"></div>
                <p>
                    <?php if ($lang === 'sk'): ?>
                        Validácia Vášho prístupu na digitálny nápojový lístok bol neúspešný. Prosím kontaktujte nás na
                        <a href="mailto:promo@aoreal.sk">promo@aoreal.sk</a> alebo na tel. č. +421 948 00 99 89.
                    <?php endif; ?>
                    <?php if ($lang === 'hu'): ?>
                        Az Ön hozzáférésének hitelesítése sikeretelen volt. Kérjük vegye fel a kapcsolatot velünk a
                        <a href="mailto:promo@aoreal.sk">promo@aoreal.sk</a> e-mail címen vagy hívjon minket a
                        +421 948 00 99 89 telefonszámon.
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="clear"></div>
    </section>
</main>
