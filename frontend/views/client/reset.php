<?php

use yii\helpers\Html;
use common\widgets\Alert;

$this->title = Yii::t('app', 'Zákaznícka zóna - obnovenie hesla');
$this->params['breadcrumbs'][] = $this->title;

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
    </div>
    <section id="contact-section-one">
        <div class="contactus-form">
            <?= Alert::widget(); ?>
            <h2 class="raleway"><?= Yii::t('app', 'Obnovenie hesla') ?></h2>
            <div class="form-group">
                <form method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                    <div class="form-group row">
                        <label class="control-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group row">
                        <button type="submit" class="btn btn-primary" name="login-button">obnoviť heslo</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="clear"></div>
    </section>
</main>