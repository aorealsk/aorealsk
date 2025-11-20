<?php

/**
 * @var $langs array
 * @var $group
 */

use yii\helpers\Url;

$this->title = 'Pridať kategóriu';
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <diw class="col-12">
            <a href="<?= Url::to(['/promo/stock-groups']) ?>"
               class="btn btn-danger text-white">Späť
            </a>
        </diw>
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

                        <?php foreach ($langs as $lang) : ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Názov - <?= $lang ?></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="Group[<?= $lang ?>][title]"
                                        >
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php foreach ($langs as $lang) : ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Popis - <?= $lang ?></label>
                                        <textarea
                                            class="form-control"
                                            name="Group[<?= $lang ?>][description]"
                                            rows="10"></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success text-white">
                                    <i class="fas fa-save"></i> Uložiť
                                </button>
                                <a href="<?= Url::to(['/promo/stock-groups']) ?>" class="btn btn-danger text-white">
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

