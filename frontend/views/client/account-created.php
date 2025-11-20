<?php
use yii\helpers\Html;
use common\widgets\Alert;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app','Zákaznícka zóna - úspešná registrácia');
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
        <?= Alert::widget(); ?>
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
            <h2 class="raleway"><?= Yii::t('app','Aktivácia účtu') ?></h2>
            <div class="wpcf7" id="wpcf7-f2130-o1" lang="en-US" dir="ltr">
                <div class="screen-reader-response"></div>
                <p>
                    <?= Yii::t('app','Registrácia Vášho účtu prebehla úspešne. Na Vašu mailovú adresu sme poslali aktivačný link. Aby ste sa mohli prihlásiť, aktivujte si svôj účet pomocou aktivačného linku.') ?>
                </p>
            </div>
        </div>
        <div class="clear"></div>
    </section>
</main>
