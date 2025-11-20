<?php
use yii\helpers\Url;
/**
 * @var $service
 */
$this->title = Yii::t('app','Editácia služby #').$service->id;
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-10 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form role="form" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <div class="form-group row">
                    <label class="form-label col-sm-2"><?= Yii::t('app','Názov'); ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="Service[nazov]" value="<?= $service->nazov ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="form-label col-sm-2">
                        <?= Yii::t('app','Popis') ?>
                    </label>
                    <div class="col-sm-10">
                        <textarea name="Service[popis]" class="form-control" rows="5"><?= $service->popis ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="form-label col-sm-2">
                        <?= Yii::t('app','Poznámka') ?>
                    </label>
                    <div class="col-sm-10">
                        <textarea name="Service[poznamka]" class="form-control" rows="5"><?= $service->poznamka ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="form-label col-sm-2"><?= Yii::t('app','Merná jednotka'); ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="Service[merna_jednotka]" value="<?= $service->merna_jednotka ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="form-label col-sm-2"><?= Yii::t('app','Cena za jednotku'); ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="Service[cena_za_jednotku]" value="<?= $service->cena_za_jednotku ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="form-label col-sm-2"><?= Yii::t('app','DPH [%]'); ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="Service[dph]" value="<?= $service->dph ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <button class="btn btn-success text-white" type="submit">
                            <?= Yii::t('app','Uložiť'); ?>
                        </button>
                        <a href="<?= Url::to(['services/settings']) ?>" class="btn btn-danger text-white"><?= Yii::t('app','Späť') ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
