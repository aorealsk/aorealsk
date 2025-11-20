<?php

/**
 * @var $langs
 * @var $personal
 */

use yii\helpers\Url;

$this->title = Yii::t('app', 'Editácia personálu');
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/personal']) ?>"
               class="btn btn-danger text-white">Späť
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" role="form">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><?= Yii::t('app','Meno'); ?></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="Personal[name_first]"
                                        value="<?= $personal->name_first ?>"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><?= Yii::t('app','Prievisko'); ?></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="Personal[name_last]"
                                        value="<?= $personal->name_last ?>"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><?= Yii::t('app','Telefón'); ?></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="Personal[phone]"
                                        value="<?= $personal->phone ?>"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><?= Yii::t('app','Email'); ?></label>
                                    <input type="text" class="form-control" name="Personal[email]" value="<?= $personal->email ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><?= Yii::t('app','Mzda'); ?></label>
                                    <input type="text" class="form-control" name="Personal[wage]" value="<?= $personal->wage ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><?= Yii::t('app','Ovládané jazyky'); ?></label>
                                    <?php
                                        $aLangs = explode(',',$personal->lang);
                                    ?>
                                    <?php foreach ($langs as $lang): ?>
                                        <?php
                                            $checked = in_array($lang, $aLangs) ? ' checked': '';
                                        ?>
                                        <p><input type="checkbox" name="Personal[lang][]" value="<?= $lang ?>"<?=$checked?>> <?= strtoupper($lang) ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success text-white">
                                    <i class="fas fa-save"></i> <?= Yii::t('app','Uložiť'); ?>
                                </button>
                                <a href="<?= Url::to(['/promo/personal']) ?>" class="btn btn-danger text-white">
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
