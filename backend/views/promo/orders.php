<?php

use yii\helpers\Url;
use backend\assets\RealAsset;

/**
 * @var $promoId
 * @var $orders
 */

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);

$this->title = Yii::t('app', 'Objednávky');

?>
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-12 align-self-center">
                <h4 class="text-themecolor"><?= $this->title ?></h4>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <a href="<?= Url::to(['/promo/detail', 'id' => $promoId]) ?>"
                   class="btn btn-danger text-white">Späť
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Objednávky hostí</h4>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-striped table-sm dattable">
                                <thead>
                                <tr>
                                    <th>Meno </th>
                                    <th>Status objednávky</th>
                                    <th>Spolu</th>
                                    <th>Vybavil</th>
                                    <th>Pozícia</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($orders as $row) :
                                    ?>
                                    <tr style="<?= $row->getCssOptions() ?>">
                                        <td><?= $row->guest->getFullName() ?></td>
                                        <td><?= $row->label() ?></td>
                                        <td><?= $row->total ?></td>
                                        <td><?= $row->personal->getFullName() ?></td>
                                        <td><?= $row->personal->workingPlace->place_name ?></td>
                                        <td></td>
                                    </tr>
                                <?php endforeach; ?>
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