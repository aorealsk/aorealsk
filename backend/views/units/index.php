<?php

use yii\helpers\Url;
use backend\assets\RealAsset;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile(
    '@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',
    ['depends' => RealAsset::class]
);
$this->title = Yii::t('app', 'Zoznam merných jednotiek');

/**
 * @var $units
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
            <div class="row">
                <div class="col-md-12">
                    <a href="<?= Url::to(['units/add']) ?>" class="btn btn-danger text-white">
                        <?= Yii::t('app', 'Pridať'); ?>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm dattable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th><?= Yii::t('app', 'Názov') ?></th>
                                <th><?= Yii::t('app', 'Jazyk') ?></th>
                                <th><?= Yii::t('app', 'Popis') ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <?php foreach ($units as $unit) : ?>
                            <tr>
                                <td><?= $unit['id'] ?></td>
                                <td><?= $unit['unit_name'] ?></td>
                                <td><?= $unit['code3'] ?></td>
                                <td><?= $unit['unit_desc'] ?></td>
                                <td>
                                    <a href="<?= Url::to(['units/edit','id' => $unit['id']]) ?>"
                                       title="Edit" style="color: black"><i class="fas fa-pencil-alt"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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
