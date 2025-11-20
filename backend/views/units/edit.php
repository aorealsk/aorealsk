<?php

use yii\helpers\Url;

$this->title = Yii::t('app', 'Nová MJ');
/**
 * @var $units
 * @var $langs
 * @var $unit
 */
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
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                       value="<?= Yii::$app->request->csrfToken ?>">
                <div class="form-group row">
                    <label class="form-label col-sm-2"><?= Yii::t('app', 'Názov'); ?></label>
                    <div class="col-sm-10">
                        <input
                                type="text"
                                class="form-control"
                                name="Units[unit_name]"
                                value="<?= $unit->unit_name ?>"
                        >
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="form-label col-sm-2">
                        <?= Yii::t('app', 'Jazyk') ?>
                    </label>
                    <div class="col-sm-10">
                        <select name="Units[unit_lang]" class="form-select">
                            <option value=""></option>
                            <?php
                            foreach ($langs as $lang) :
                                $selected = $lang['id'] == $unit['unit_lang'] ? ' selected="selected"' : '';
                                if (empty($lang['code3'])) {
                                    continue;
                                }
                                ?>
                            <option value="<?= $lang['id'] ?>"<?= $selected ?>>
                                <?= $lang['code3'] ?> - <?= strtolower($lang['name']) ?>
                            </option>
                            <?php  endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="" class="form-label col-sm-2">
                        <?= Yii::t('app', 'Popis') ?>
                    </label>
                    <div class="col-sm-10">
                        <textarea name="Units[unit_desc]" class="form-control" rows="5"><?= $unit['unit_desc'] ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <button class="btn btn-success text-white" type="submit">
                            <?= Yii::t('app', 'Uložiť'); ?>
                        </button>
                        <a href="<?= Url::to(['units/index']) ?>"
                           class="btn btn-danger text-white"><?= Yii::t('app', 'Späť') ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
