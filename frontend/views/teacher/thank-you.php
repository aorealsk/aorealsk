<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = 'Prihláška študenta na dual prax';
?>
<main class="site-student">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url('/images/header-bg1.jpg');" width="1600" height="400"></canvas>
        <div class="page-border container-default d-block position-absolute mx-auto">
            <div class="page_title_line_left d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
            <div class="page_title_line_right d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
        </div>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true" data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                        <strong><?= Html::encode($this->title) ?></strong></h1>
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

    <div id="students" class="container-fluid">
        <div class="students-container">
            <section>
                <h1 style="font-weight: bold">Vaša registrácia prebehla úspešne</h1>
                <p style="margin-top: 30px">
                    Počkajte prosím na schválenie Vašej žiadosti. Po odsúhlasení Vám obratom pošleme
                    prihlasovacie údaje do nášho backoffice-u.
                </p>

            </section>

        </div>
    </div>
</main>