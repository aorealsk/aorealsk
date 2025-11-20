<?php

use yii\helpers\Url;
use backend\assets\RealAsset;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);

$this->title = 'Založenie firmy';


?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-10 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
        <div class="col-md-2 align-self-center text-right" style="text-align: right;">
            <a href="/backoffice/services/dotaznik" class="btn btn-info text-white">
                Vytvoriť firmu
                <i class="fas fa-plus-circle m-l-5"></i>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm dattable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?= Yii::t('app', 'Názov firmy'); ?></th>
                                    <th><?= Yii::t('app', 'Adresa'); ?></th>
                                    <th><?= Yii::t('app', 'Spolocnik/Spolocnici'); ?></th>
                                    <th><?= Yii::t('app', 'Konatel/Konatelia'); ?></th>
                                    <th>Akcia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                /**
                                 * @var $clients
                                 */
                                foreach ($clients as $client) {
                                ?>
                                    <tr>
                                        <td>
                                            <?= $client->id ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!is_null($client->companyInfo)) {
                                                echo $client->companyInfo->name . ' ' . $client->companyInfo->appendix;
                                            } else {
                                                echo '';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!is_null($client->companyInfo)) {
                                                echo $client->companyInfo->address . '<br>' . $client->companyInfo->town . ' ' . $client->companyInfo->zip;
                                            } else {
                                                echo '';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!is_null($client->getSpolocnici())) {
                                                foreach($client->getSpolocnici() as $spolocnik) {
                                                    echo $spolocnik->getFullName();
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!is_null($client->getKonatelia())) {
                                                foreach($client->getKonatelia() as $konatel) {
                                                    echo $konatel->getFullName();
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?= Url::to(['documents', 'id' => $client->id]) ?>" title="Print" style="color: black;margin-right: 5px;">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="<?= Url::to(['edit', 'id' => $client->id]) ?>"><i class="fas fa-pencil-alt m-l-10" style="color: black"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
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
