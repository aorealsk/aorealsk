<?php
/**
 * @var $groups
 * @var $langs
 */

use yii\helpers\Url;

$this->title = 'Nová položka';
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <diw class="col-12">
            <a href="<?= Url::to(['/promo/stock']) ?>"
               class="btn btn-danger text-white">Späť
            </a>
        </diw>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <form method="post" role="form" enctype="multipart/form-data">
                <input
                        type="hidden"
                        name="<?= Yii::$app->request->csrfParam ?>"
                        value="<?= Yii::$app->request->csrfToken ?>">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-6">
                                <h4 class="mb-3 card-title">Popis</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Kategória</label>
                                            <select class="form-select" name="Item[group_id]">
                                                <option value="">Vyberte si kategóriu</option>
                                                <?php foreach ($groups as $group) : ?>
                                                    <option
                                                            value="<?= $group['stock_item_group_id'] ?>">
                                                        <?= $group['title'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <?php foreach ($langs as $lang) : ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">Názov - <?= $lang ?></label>
                                                <input type="text" class="form-control" name="Item[<?= $lang ?>][title]">
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
                                                        name="Item[<?= $lang ?>][description]" rows="10"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <h4 class="mb-3 card-title mt-2">Obstarávacia cena a objemy/kusy</h4>

                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Ob. alk. [%]</label>
                                            <input type="text" class="form-control" name="Item[alcohol]">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">&euro; /fl.</label>
                                            <input type="text" class="form-control" name="Item[cost]" id="cost">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Obj. [L/fl.]</label>
                                            <input type="text" class="form-control" name="Item[bottle_size]" id="F">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Počet fl. v kartóne</label>
                                            <input type="text" class="form-control" name="Item[bottle_per_carton]" id="G">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Poč. kart.</label>
                                            <input type="text" class="form-control" name="Item[carton]" id="H">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-label">Spolu [L]</label>
                                            <input type="text" class="form-control" name="Item[amount]" id="SL">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-label">Spolu [fl.]</label>
                                            <input type="text" class="form-control" name="Item[bottle_cnt]" id="SF">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label class="form-label">Investícia</label>
                                            <input type="text" class="form-control" name="Item[investment]" id="Inv">
                                        </div>
                                    </div>
                                </div>

                                <h4 class="mb-3 card-title mt-2">Predajná cena a objemy/kusy</h4>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">0,4dl / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_04]" id="P04">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">0,4dl / fl. / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_04_bottle]" id="P04B">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">0,75dl / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_075]">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">0,75dl/ fl. / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_075_bottle]">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">1dl / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_1]" id="P3">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">1dl / fl. / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_1_bottle]">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">0,5l / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_5]">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">L / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_10]" id="PL">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">fl. / &euro;</label>
                                            <input type="text" class="form-control" name="Item[price_bottle]" id="PB">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1"></div>
                            <div class="col-5">
                                <h4 class="card-title">Obrázky</h4>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <input type="file" name="pics">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success text-white" id="sm-01">
                                    <i class="fas fa-save"></i> Uložiť'
                                </button>
                                <a href="<?= Url::to(['/promo/stock']) ?>" class="btn btn-danger text-white">
                                    Späť
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$js = <<<JS
    totalLiter = function() {
        var F = parseFloat($('#F').val()) || 0;
        var G = parseFloat($('#G').val()) || 0;
        var H = parseFloat($('#H').val()) || 0;
        var SL = F * G * H;
        $('#SL').val(SL);
    }
    
    totalFlase = function () {
        var G = parseFloat($('#G').val()) || 0;
        var H = parseFloat($('#H').val()) || 0;
        var SF = G * H;
        $('#SF').val(SF);
    }
    
    investicia = function () {
        var cost = parseFloat($('#cost').val()) || 0;
        var G = parseFloat($('#G').val()) || 0;
        var H = parseFloat($('#H').val()) || 0;
        var Inv = Math.round(cost * G * H * 100) / 100;
        $('#Inv').val(Inv); 
    }
    
    $(document).on('change', '#F, #G, #H', function() {
        totalLiter();
        investicia();
    });
    
    $(document).on('change', '#G, #H', function() {
        totalFlase();
        investicia();
    });
    
    $(document).on('change', '#P04, #P04B', function() {
        var P04 = parseFloat($('#P04').val()) || 0;
        var P04B = parseFloat($('#P04B').val()) || 0;
        var PB = Math.round(P04 * P04B * 100) / 100;
        
        $('#PB').val(PB);
    });

    $(document).on('change', '#P3', function() {
        var P3 = parseFloat($('#P3').val()) || 0;
        var F = parseFloat($('#F').val()) || 0;
        var PB = Math.round(P3 * F * 10 * 100) / 100;
        var PL = Math.round(P3 * 10 * 100) / 100;
        $('#PB').val(PB);
        $('#PL').val(PL);
    });
    
    
JS;
$this->registerJs($js);

