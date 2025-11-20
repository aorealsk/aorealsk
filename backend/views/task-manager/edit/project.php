<?php

use yii\helpers\Url;

?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 col-xs-12 align-self-center">
            <h4 class="text-themecolor"><?= $title ?> - <?= $project->name ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['index']) ?>" class="btn btn-danger text-white">
                <i class="fas fa-arrow-alt-circle-left"></i>&nbsp;<?php echo Yii::t('app','Späť') ?>
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" role="form">
                    <input type="hidden" name="<?php echo Yii::$app->request->csrfParam ?>" value="<?php echo Yii::$app->request->getCsrfToken()?>">
                        <div class="form-group row mt-3">
                            <label class="col-2 col-form-label"><?= Yii::t('app','Názov projektu') ?></label>
                            <div class="col-10">
                                <input type="text" class="form-control" name="TasksProject[name]" value="<?= $project->name ?>">
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <label class="col-2 col-form-label"><?= Yii::t('app','Kód projektu') ?></label>
                            <div class="col-10">
                                <input type="text" class="form-control" name="TasksProject[code]" value="<?= $project->code ?>">
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <label class="col-2 col-form-label"><?= Yii::t('app','Farba') ?></label>
                            <div class="col-10">
                                <input type="color" class="form-control" name="TasksProject[color]" value="<?= $project->color ?>">
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <label class="col-2 col-form-label">
                                <?= Yii::t('app','Stav') ?>
                            </label>
                            <?php
                            $status = $project->status;
                            ?>
                            <div class="col-10">
                                <select name="TasksProject[status]" class="form-select">
                                    <option value="1"<?= $status == 1 ? ' selected' : '' ?>><?= Yii::t('app','Aktívny') ?></option>
                                    <option value="0"<?= $status == 0 ? ' selected' : '' ?>><?= Yii::t('app','Neaktívny') ?></option>
                                </select>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-success text-white" type="submit">
                                    <i class="ti-save"></i>&nbsp;&nbsp;<?= Yii::t('app', 'Uložiť') ?></button>
                                <a href="<?= Url::to(['index']) ?>" class="btn btn-danger text-white">
                                    <i class="fas fa-arrow-alt-circle-left"></i>&nbsp;<?php echo Yii::t('app','Späť') ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
