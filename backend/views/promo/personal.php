<?php
/**
 * @var $personal
 */
use yii\helpers\Url;
use backend\assets\RealAsset;

$this->title = Yii::t('app', 'Personál');
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
            <a href="<?= Url::to(['/promo/personal-new']) ?>" class="btn btn-success text-white">
                <?= Yii::t('app','Pridať personál'); ?>
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
                                    <th><?= Yii::t('app','Meno'); ?></th>
                                    <th><?= Yii::t('app','Jazyk'); ?></th>
                                    <th><?= Yii::t('app','Mzda'); ?></th>
                                    <th><?= Yii::t('app','Telefón'); ?></th>
                                    <th><?= Yii::t('app','Email'); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?= $this->render('personal_tbody',[
                                    'personal' => $personal
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

