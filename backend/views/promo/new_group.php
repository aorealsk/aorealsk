<?php
/**
 * @var $langs
 */

use yii\helpers\Url;

$this->title = Yii::t('app', 'Nová kategória');
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/stock']) ?>" class="btn btn-danger text-white"><?= Yii::t('app','Späť'); ?></a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" role="form">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <?php foreach ($langs as $lang): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"><?= Yii::t('app','Názov'); ?> - <?= $lang ?></label>
                                        <input type="text" class="form-control" name="Group[<?= $lang ?>][title]">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach ($langs as $lang): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"><?= Yii::t('app','Popis'); ?> - <?= $lang ?></label>
                                        <textarea class="form-control" name="Group[<?= $lang ?>][description]" rows="10"></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success text-white">
                                    <i class="fas fa-save"></i> <?= Yii::t('app','Uložiť'); ?>
                                </button>
                                <a href="<?= Url::to(['/promo/stock']) ?>" class="btn btn-danger text-white">
                                    <?= Yii::t('app','Späť'); ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
