<?php

use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var \common\models\PromoCode $code */

$this->title = 'Editácia promo kódu';
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/codes']) ?>"
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kód:</label>
                                    <input type="text" class="form-control" name="code" value="<?= $code->code?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Priradené:</label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            name="assigned_to"
                                            value="<?= $code->assigned_to?>">
                                </div>
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
                                            value="<?= date('Y-m-d\TH:i:s', strtotime($code->available_from))?>"
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
                                            value="<?= date('Y-m-d\TH:i:s', strtotime($code->available_to))?>"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success text-white">
                                    <i class="fas fa-save"></i> Uložiť
                                </button>
                                <a href="<?= Url::to(['/promo/codes']) ?>" class="btn btn-danger text-white">
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