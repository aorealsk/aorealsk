<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
$this->title = Yii::t('app','Spätná väzba');
?>

<main class="site-applicant">
    <input type="hidden" id="client_id" value="0">
    <div class="page-banner d-block position-relative raleway">
        <canvas style="background-image:url('/images/contact-us-banner-1.jpg');" width="1600" height="400"></canvas>
        <div class="page-border container-default d-block position-absolute mx-auto">
            <div class="page_title_line_left d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
            <div class="page_title_line_right d-inline-block position-absolute background-gold-before background-gold-after animated fadeIn visible" data-aios-reveal="true" data-aios-animation="fadeIn" data-aios-animation-delay="0.2s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.2s;"></div>
        </div>
        <div class="page-title container-default d-block position-absolute mx-auto">
            <div class="container-fluid">
                <div class="titlewrapper">
                    <!--<h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true" data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s" data-aios-animation-reset="false" data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                        <strong><?= Html::encode($this->title) ?></strong>
                    </h1>-->
                </div>
            </div>
        </div>
        <div class="breadcrumbs-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-xs-12">
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
    <div id="resp" class="container-fluid">
        <div class="resp-container">
            <form id="frm01" role="form" method="post" action="/spatna-vazba">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <h3 style="margin-bottom:30px" id="h0"><?= Yii::t('app','Spätná väzba'); ?></h3>
                <div class="form-group row">
                    <div class="col-md-12 col-xs-12">
                        <label class="control-label">
                            <span id="f01"><?= Yii::t('app','Jazyk'); ?></span>
                            <span style="color:red">*</span>
                        </label>
                        <select class="form-control dropdown" id="f02" name="Resp[lang]">
                            <option value="sk">Slovenský</option>
                            <option value="hu">Magyar</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12 col-xs-12">
                        <label class="control-label"><?= Yii::t('app','Email'); ?></label>
                        <input type="email" class="form-control" placeholder="your.email@domain.com" name="Resp[email]">
                    </div>
                </div>
                <div class="form-group row" style="margin-top: 40px;">
                    <div class="col-md-12 col-xs-12">
                        <label class="form-label">
                            <input type="checkbox" name="Resp[newsl]">
                            &nbsp;
                            <span id="nl">Mám záujem o zasielanie noviniek e-mailom</span>
                        </label>
                    </div>
                </div>
                <div class="form-group row" style="margin-top: 30px; margin-bottom: 30px;">
                    <div class="col-md-12 col-xs-12">
                        <button type="submit" class="btn" id="sub01"><?= Yii::t('app','Prejsť na formulár'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php
$css = <<<CSS
#resp {
    width: 100%;
    height: auto;
    position: relative;
    clear: both;
}

#resp .resp-container {
    width: 40%;
    margin: 20px auto;
    position: relative;
}
#resp input[type=text],
#resp input[type=tel],
#resp input[type=email],
#resp input[type=date],
#resp input[type=number],
#resp select,
#resp textarea {
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

$this->registerCss($css);

$js = <<<JS

$('#f02').on('change', function (){
    let v = $(this).val();
    if (v === 'sk') {
        $('#h0').html('SPÄTNÁ VÄZBA');
        $('#f01').html('Jazyk');
        $('#nl').html('&nbsp;Mám záujem o zasielanie noviniek e-mailom');
        $('#sub01').html('Prejsť na formulár');
    } 
    if (v === 'hu') {
        $('#h0').html('VISSZAJELZÉS');
        $('#f01').html('Nyelv');
        $('#nl').html('&nbsp;Szeretnék feliratkozni az Önök hírleveleire');
        $('#sub01').html('Ugrás az űrlapra');
    }
});

JS;

$this->registerJs($js);
