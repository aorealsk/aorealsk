<?php

use yii\helpers\Url;
use backend\assets\RealAsset;

$this->title = 'Zmeniť referal kód';

$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/js/issue.js?v=0.1', ['depends' => RealAsset::class]);
?>
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-12 align-self-center">
                <h4 class="text-themecolor"><?= $this->title ?></h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <a href="<?= Url::to(['/promo/referrals']) ?>"
                   class="btn btn-danger text-white">Späť
                </a>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" role="form">
                            <input
                                type="hidden"
                                name="<?= Yii::$app->request->csrfParam ?>"
                                value="<?= Yii::$app->request->csrfToken ?>"
                            >
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Kód:</label>
                                        <input type="text" class="form-control" name="code" value="<?= $code->code?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Prefix:</label>
                                        <input type="text" class="form-control cd-gen">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Sufix: </label>
                                        <input type="text" class="form-control cd-gen">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Posledné poradové číslo: </label>
                                        <input type="text" class="form-control cd-gen">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-dark btn-block" id="gen1">
                                        <i class="fas fa-recycle"></i> Vygenerovať
                                    </button>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Platný od:</label>
                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            name="available_from"
                                            value="<?= date('Y-m-d\TH:i:s', strtotime($code->available_from)) ?>"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Platný do:</label>
                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            name="available_to"
                                            value="<?= date('Y-m-d\TH:i:s', strtotime($code->available_to)) ?>"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-success text-white sv-code">
                                        <i class="fas fa-save"></i> Uložiť
                                    </button>
                                    <a href="<?= Url::to(['/promo/referrals']) ?>" class="btn btn-danger text-white">
                                        Späť
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
    $('#gen1').on('click', function(e) {
        const prefix = $('.cd-gen').eq(0).val();
        const sufix = $('.cd-gen').eq(1).val();
        const last = $('.cd-gen').eq(2).val();
        
        $.ajax({
            url: '/backoffice/promo/generate-referral-code',
            dataType: 'json',
            data: { prefix: prefix, sufix: sufix, last: last },
            type: 'POST'
        }).done(function(r) {
            if (r.result.code) {
                $('input[name="code"]').val(r.result.code);
                $('.cd-gen').eq(2).val(r.result.last);
            }
        });
        
        $('input[name="code"]').val(code);
    });

    $('.sv-code').on('click', function(e) {
            e.preventDefault();
            const form = $('form');
            const data = form.serialize();
            
            $.ajax({
                url: '/backoffice/promo/save-referral',
                dataType: 'json',
                data: data,
                type: 'POST'
            }).done(function(r) {
                showMyToast(r, 'Kód bol úspešne uložený!'); 
            });
        });
JS;

$this->registerJS($js);