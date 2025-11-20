<?php

/**
 * @var $promotions
 * @var $this
 * @var $promo
 */


use backend\assets\RealAsset;

$this->title = Yii::t('app', 'Akcie');
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

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm dattable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Názov</th>
                                    <th>Dátum</th>
                                    <th>Miesto konania</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?= $this->render('promo_tbody', [
                                    'promotions' => $promotions
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
