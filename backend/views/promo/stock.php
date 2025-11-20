<?php
/**
 * @var $promotions
 * @var $this
 * @var $items
 */
use backend\assets\RealAsset;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Sklad');
$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/stock-groups']) ?>" class="btn btn-success text-white">
                Kategórie
            </a>
            <a href="<?= Url::to(['/promo/stock-new']) ?>" class="btn btn-info text-white">
                Pridať položku
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm dattable">
                            <thead>
                                <tr>
                                    <th>Kat.</th>
                                    <th>Názov</th>
                                    <th>%</th>
                                    <th>cen. /fl</th>
                                    <th>L /fl</th>
                                    <th>ks /kart.</th>
                                    <th>Kart.</th>
                                    <th>L /spolu</th>
                                    <th>Fľaša</th>
                                    <th>Invest.</th>
                                    <th>0.4dl /cen.</th>
                                    <th>0.4dl /fl</th>
                                    <th>0.75dl /fl</th>
                                    <th>1dl /cen.</th>
                                    <th>0.5l fl. pred. cen.</th>
                                    <th>1l fl. pred. cen.</th>
                                    <th>Cen. za fl.</th>
                                    <th>Spol. za pr. cen.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?= $this->render('stock_tbody', [
                                    'items' => $items
                            ]) ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
$js = <<<JS
    $(function() {
        $('.dattable').DataTable({
            order: []
        });
    });
JS;
$this->registerJS($js);

