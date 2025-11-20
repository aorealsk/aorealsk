<?php
use yii\helpers\Url;
use backend\assets\RealAsset;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);

$this->title = Yii::t('app','Manažér premenných');

?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row m-t-10">
        <div class="col-12">
            <div class="card rounded-5 card-shadow">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <a href="<?= Url::to(['/template-vars'])?>" class="btn btn-danger text-white">
                                <i class="fas fa-arrow-alt-circle-left"></i>
                                &nbsp;<?= Yii::t('app','Späť na konfigurátora'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm dattable" id="att-01">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?= Yii::t('app','Kód') ?></th>
                                    <th><?= Yii::t('app','Popis'); ?></th>
                                    <th><?= Yii::t('app','Typ'); ?></th>
                                    <th><?= Yii::t('app','Obsah'); ?></th>
                                    <th><?= Yii::t('app','MapID'); ?></th>
                                    <th><?= Yii::t('app','Počet použití'); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?= $this->render('managerbody',['rows'=>$rows]) ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php

$css = <<<CSS

.rounded-5 {
    border-radius: .5em!important;
}
.card-shadow {
    box-shadow: lightgrey 3px 3px;
}
CSS;
$this->registerCSS($css);

$js = <<<JS
    $(function() { $('.dattable').DataTable({ order: [] }); });
    $('.del-item').click(function(){
       let i = $(this).data('id');
    });
JS;

$this->registerJS($js);