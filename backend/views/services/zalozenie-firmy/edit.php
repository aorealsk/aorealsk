<?php

$this->title = 'Editacia'
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-10 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <form method="post" class="form">
        <input id="form-token" type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>" />
        <div class="card">
            <div class="card-header bg-info text-white" style="padding:10px; font-size: 0.98em">Údaje o firme</div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">
                            <?= Yii::t('app', 'Meno firmy') ?>
                        </label>
                        <input type="text" name="Company[name]" class="form-control" value=<?= $company->name ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="col-form-label">
                            <?= Yii::t('app', 'Dodatok meno') ?>
                        </label>
                        <div class="col-10">
                            <input type="text" name="Company[appendix]" class="form-control" value=<?= $company->appendix ?>>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">
                            <?= Yii::t('app', 'Adresa firmy') ?>
                        </label>
                        <input type="text" name="Company[address]" class="form-control" value=<?= $company->address ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="col-2 col-form-label">
                            <?= Yii::t('app', 'Mesto') ?>
                        </label>
                        <div class="col-10">
                            <input type="text" name="Company[town]" class="form-control" value=<?= $company->town ?>>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">
                            <?= Yii::t('app', 'PSČ') ?>
                        </label>
                        <input type="text" name="Company[zip]" class="form-control" value=<?= $company->zip ?>>
                    </div>
                    <div class="col-md-6">
                        <label class="col-form-label">
                            <?= Yii::t('app', 'Mesto podpisu') ?>
                        </label>
                        <div class="col-10">
                            <input type="text" name="Company[town_of_signature]" class="form-control" value=<?= $company->town_of_signature ?>>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php foreach ($spolocnici as $i => $spolocnik) {
        ?>
            <div class="card">
                <input type="hidden" name=<?= "Spolocnik[" . $i . "][id]" ?> value=<?= $spolocnik->id ?>>
                <div class="card-header bg-info text-white" style="padding:10px; font-size: 0.98em">Spoločník</div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label">
                                <?= Yii::t('app', 'Meno spolocnika') ?>
                            </label>
                            <input type="text" name=<?= "Spolocnik[" . $i . "][first_name]" ?> class="form-control" value=<?= $spolocnik->first_name ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">
                                <?= Yii::t('app', 'Priezvisko spolocnika') ?>
                            </label>
                            <div class="col-10">
                                <input type="text" name=<?= "Spolocnik[" . $i . "][last_name]" ?> class="form-control" value=<?= $spolocnik->last_name ?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="control-label">
                                <?= Yii::t('app', 'Rodné číslo') ?>
                            </label>
                            <input type="text" name=<?= "Spolocnik[" . $i . "][ssn]" ?> class="form-control" value=<?= $spolocnik->ssn ?>>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label">
                                <?= Yii::t('app', 'Adresa') ?>
                            </label>
                            <input type="text" name=<?= "Spolocnik[" . $i . "][address]" ?> class="form-control" value=<?= $spolocnik->address ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">
                                <?= Yii::t('app', 'Mesto') ?>
                            </label>
                            <div class="col-10">
                                <input type="text" name=<?= "Spolocnik[" . $i . "][town]" ?> class="form-control" value=<?= $spolocnik->town ?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label">
                                <?= Yii::t('app', 'PSČ') ?>
                            </label>
                            <input type="text" name=<?= "Spolocnik[" . $i . "][zip]" ?> class="form-control" value=<?= $spolocnik->zip ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="col-2 col-form-label">
                                <?= Yii::t('app', 'Vklad') ?>
                            </label>
                            <div class="col-10">
                                <input type="text" name=<?= "Spolocnik[" . $i . "][deposit_amount]" ?> class="form-control" value=<?= $spolocnik->deposit_amount ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
        foreach ($konatelia as $i => $konatel) { ?>
            <div class="card">
                <input type="hidden" name=<?= "Konatel[" . $i . "][id]" ?> value=<?= $konatel->id ?>>
                <div class="card-header bg-info text-white" style="padding:10px; font-size: 0.98em">Konateľ</div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label">
                                <?= Yii::t('app', 'Meno konatela') ?>
                            </label>
                            <input type="text" name=<?= "Konatel[" . $i . "][first_name]" ?> class="form-control" value=<?= $konatel->first_name ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">
                                <?= Yii::t('app', 'Priezvisko konatela') ?>
                            </label>
                            <div class="col-10">
                                <input type="text" name=<?= "Konatel[" . $i . "][last_name]" ?> class="form-control" value=<?= $konatel->last_name ?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="control-label">
                                <?= Yii::t('app', 'Rodné číslo') ?>
                            </label>
                            <input type="text" name=<?= "Konatel[" . $i . "][ssn]" ?> class="form-control" value=<?= $konatel->ssn ?>>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label">
                                <?= Yii::t('app', 'Adresa') ?>
                            </label>
                            <input type="text" name=<?= "Konatel[" . $i . "][address]" ?> class="form-control" value=<?= $konatel->address ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="col-form-label">
                                <?= Yii::t('app', 'Mesto') ?>
                            </label>
                            <div class="col-10">
                                <input type="text" name=<?= "Konatel[" . $i . "][town]" ?> class="form-control" value=<?= $konatel->town ?>>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label">
                                <?= Yii::t('app', 'PSČ') ?>
                            </label>
                            <input type="text" name=<?= "Konatel[" . $i . "][zip]" ?> class="form-control" value=<?= $konatel->zip ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="col-2 col-form-label">
                                <?= Yii::t('app', 'Vklad') ?>
                            </label>
                            <div class="col-10">
                                <input type="text" name=<?= "Konatel[" . $i . "][deposit_amount]" ?> class="form-control" value=<?= $konatel->deposit_amount ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row m-b-20">
            <div class="col-lg-12">
                <input type="submit" class="btn btn-success" value="Uložiť faktúru">
            </div>
        </div>
    </form>
</div>