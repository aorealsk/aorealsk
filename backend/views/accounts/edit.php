<?php

use yii\helpers\Url;

$this->title = Yii::t('app', 'Zmena bankového účtu');
/**
 * @var $banks
 * @var $account
 */
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-8 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post" role="form">
                <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->csrfToken?>"/>
                <div class="form-group row">
                    <label class="col-2 col-form-label">
                        <?= Yii::t('app', 'Názov banky') ?>
                    </label>
                    <div class="col-10">
                        <select class="form-select" name="Acc[bank_id]">
                            <option value=""></option>
                            <?php foreach ($banks as $bank): ?>
                            <?php $selected = $bank['id'] == $account['bank_id'] ? " selected":"" ?>
                            <option value="<?= $bank['id'] ?>"<?= $selected ?>>
                                <?= $bank['name'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label">
                        IBAN
                    </label>
                    <div class="col-10">
                        <input type="text" name="Acc[iban]" class="form-control" value="<?= $account['iban'] ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label">
                        SWIFT
                    </label>
                    <div class="col-10">
                        <input type="text" name="Acc[swift]" class="form-control" value="<?= $account['swift'] ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label">
                        BBAN
                    </label>
                    <div class="col-10">
                        <input type="text" name="Acc[bban]" class="form-control" value="<?= $account['bban'] ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label">
                        <?= Yii::t('app', 'Otvorené') ?>
                    </label>
                    <div class="col-10">
                        <input type="date" name="Acc[valid_from]" class="form-control" value="<?= $account['valid_from'] ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label">
                        <?= Yii::t('app', 'Uzavreté') ?>
                    </label>
                    <div class="col-10">
                        <input type="date" name="Acc[valid_to]" class="form-control" value="<?= $account['valid_to'] ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label">
                        <?= Yii::t('app', 'API URL') ?>
                    </label>
                    <div class="col-10">
                        <input type="text" name="Acc[api_url]" class="form-control" value="<?= $account['api_url'] ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label">
                        <?= Yii::t('app', 'API kľúč') ?>
                    </label>
                    <div class="col-10">
                        <input type="text" name="Acc[api_key]" class="form-control" value="<?= $account['api_key'] ?>">
                    </div>
                </div>

                <div class="row m-b-20 p-t-20">
                    <div class="col-12">
                        <input type="submit" class="btn btn-success text-white" value="Uložiť">
                        <a href="<?= Url::to(['accounts/index']) ?>"
                           class="btn btn-danger text-white">Späť
                        </a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

