<?php
/**
 * @var $lang
 */
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = $lang === 'hu' ? 'Sikeres regisztráció' : 'Úspešná registrácia';
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
            <h2 class="raleway"><?php
                echo $lang === 'hu' ? 'Sikeres regisztráció' : 'Úspešná registrácia';
            ?></h2>
            <div class="wpcf7" id="wpcf7-f2130-o1" lang="en-US" dir="ltr">
                <div class="screen-reader-response"></div>
                <p>
                    <?php if ($lang === 'sk'): ?>
                        Registrácia Vášho prístupu prebehla úspešne. Na Vašu mailovú adresu sme poslali aktivačný link. Aby ste mohli objednať z nápojového listu, prosíme aktivujte
                        si svôj prístup pomocou aktivačného linku.
                    <?php endif; ?>
                    <?php if ($lang === 'hu'): ?>
                        Az Ön hozzáférésének regisztrációja sikeres volt. E-mail címére elküldtük az aktiváló linket, mellyel aktiválhatja
                        a megrendelőlaphoz való hozzáférését.
                    <?php endif; ?>
                </p>
                <br>
                <p>
                    <?php if ($lang === 'sk'): ?>
                       V prípade otázok neváhajte nás kontaktovať na <a href="mailto:promo@aoreal.sk">promo@aoreal.sk</a>.
                    <?php endif; ?>
                    <?php if ($lang === 'hu'): ?>
                        Ha bármilyen kérdése lenne akkor írjon az <a href="mailto:promo@aoreal.sk">promo@aoreal.sk</a> email címre.
                    <?php endif; ?>
                </p>
                <br>
                <p>
                    <?php if ($lang === 'sk'): ?>
                        Prajeme Vám príjemné chvíle!
                    <?php endif; ?>
                    <?php if ($lang === 'hu'): ?>
                        Jó szórakozást kívánunk!
                    <?php endif; ?>

                </p>
            </div>
        </div>
        <div class="clear"></div>
    </section>
</main>
