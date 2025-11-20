<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

$this->registerCSSFile('@web/css/customer/registration.css?v=0.1',['depends'=>\frontend\assets\AppAsset::class]);
$this->registerCSSFile("https://fonts.googleapis.com/icon?family=Material+Icons",['depends'=>\frontend\assets\AppAsset::class]);


$this->title = Yii::t('app','Zákaznícka zóna - môj profil');
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var $model
 * @var $client
 */
?>
<main class="site-my-profile">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url('/images/contact-us-banner-1.jpg');" width="1600" height="400"></canvas>
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
                            'links' => $this->params['breadcrumbs'] ?? [],
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="registration">
        <div class="registration-container wizard-content">
            <?= Alert::widget(); ?>
            <form
                    id="my-profile-form"
                    method="post"
            >
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" name="action" value="profile">
                <h2><?= Yii::t('app','Osobné údaje') ?></h2>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Meno') ?></label>
                    <div class="col-sm-9">
                        <input
                                type="text"
                                name="Profile[first_name]"
                                class="form-control"
                                value="<?= $client->personalInfo->first_name ?>"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Priezvisko') ?></label>
                    <div class="col-sm-9">
                        <input
                                type="text"
                                name="Profile[last_name]"
                                class="form-control"
                                value="<?= $client->personalInfo->last_name ?>"
                        >
                    </div>
                </div>
                <h2><?= Yii::t('app','Kontaktné údaje') ?></h2>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Email'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="email"
                                name="Profile[email]"
                                class="form-control"
                                value="<?= $client->email ?>"
                                disabled
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Telefón'); ?></label>
                    <div class="col-sm-3">
                        <select name="Profile[phone][countryCode]" class="form-control dropdown">
                            <?php
                            /**
                             * @var $countries
                             */
                            foreach($countries as $id=>$item) {
                                $selected = "";
                                if (
                                    !is_null($client->clientContact) &&
                                    $client->clientContact->mobile_area_code == $id
                                ) {
                                    $selected=" selected";
                                }
                                echo "<option value='{$id}'{$selected}>{$item}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <input
                                type="tel"
                                name="Profile[phone][number]"
                                class="form-control"
                                value="<?= !is_null($client->clientContact) ? $client->clientContact->mobile : "" ?>"
                        >
                    </div>
                </div>
                <h2><?= Yii::t('app','Sociálne siete') ?></h2>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Facebook'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="text"
                                name="Profile[social][facebook]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Linkedin'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="text"
                                name="Profile[social][linkedin]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Twitter'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="text"
                                name="Profile[social][twitter]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Youtube'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="text"
                                name="Profile[social][youtube]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Instagram'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="text"
                                name="Profile[social][instagram]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','TikTok'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="text"
                                name="Profile[social][tiktok]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group mt-10">
                    <?= Html::submitButton(Yii::t('app','Uložiť'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </form>
            <form
                    id="my-profile-form"
                    method="post"
                    class="mt-10"
            >
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" name="action" value="pass">
                <h2><?= Yii::t('app','Zmena hesla') ?></h2>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Staré heslo'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="password"
                                name="Profile[password][old]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Nové heslo'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="password"
                                name="Profile[password][new]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label"><?= Yii::t('app','Potvrdenie hesla'); ?></label>
                    <div class="col-sm-9">
                        <input
                                type="password"
                                name="Profile[password][new_repeat]"
                                class="form-control"
                        >
                    </div>
                </div>
                <div class="form-group mt-10">
                    <?= Html::submitButton(Yii::t('app','Zmeniť'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </form>
        </div>
        <div class="clear"></div>
    </div>

</main>

<?php
$css = <<<CSS
input[type="password"] {
    border: 0;
    border-bottom: solid 2px #133045;
    background-image: none;
    background-color: transparent;
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
    display: block;
    width: 100%;
    padding-top: 10px;
    padding-bottom: 5px;
    outline: none !important;
    color: #666666;
    border-radius: 0px;
}
CSS;

$this->registerCSS($css);