<?php
/**
 * @var $promotions
 * @var $this
 * @var $promo
 * @var $referrals
 */
use backend\assets\RealAsset;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Promo kódy');
$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);

?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/add-referral']) ?>" class="btn btn-success text-white">
                Pridať referal kód
            </a>
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
                                <th>Kód</th>
                                <th>Platnosť</th>
                                <th>Priradené</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?= $this->render('referrals_tbody', [
                                'referrals' => $referrals,
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