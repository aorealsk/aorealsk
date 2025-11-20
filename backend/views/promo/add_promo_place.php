<?php

/**
 * @var $langs
 * @var $promotions
 */

use yii\helpers\Url;

$this->title = Yii::t('app', 'Nová pozícia');
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/places']) ?>"
               class="btn btn-danger text-white"> Späť
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
                                    <label class="form-label">Akcia</label>
                                    <select name="PromoPlace[promo_id]" class="form-select">
                                        <option value="">Zvoľte si akciu</option>
                                        <?php foreach ($promotions as $promotion) : ?>
                                            <option value="<?= $promotion->id?>"><?= $promotion->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Názov</label>
                                    <input type="text" class="form-control" name="PromoPlace[place_name]">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Platný od:</label>
                                    <input type="datetime-local" class="form-control" name="PromoPlace[start_date]">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Platný do:</label>
                                    <input type="datetime-local" class="form-control" name="PromoPlace[finish_date]">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success text-white">
                                   <i class="fas fa-save"></i> Uložiť
                                </button>
                                <a href="<?= Url::to(['/promo/places']) ?>" class="btn btn-danger text-white">
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
