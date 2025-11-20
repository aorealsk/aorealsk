<?php

use yii\helpers\Url;
use backend\assets\RealAsset;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile(
    '@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',
    ['depends' => RealAsset::class]
);
$this->registerJSFile('@web/assets/node_modules/switchery/dist/switchery.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/switchery/dist/switchery.min.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/js/issue.js?v=0.1', ['depends' => RealAsset::class]);

$this->title = Yii::t('app', 'Zoznam služieb');


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
                    <a href="<?= Url::to(['services/add']) ?>" class="btn btn-danger text-white">
                        <?= Yii::t('app', 'Pridať službu'); ?>
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
                                <th><?= Yii::t('app', 'Názov'); ?></th>
                                <th><?= Yii::t('app', 'Popis'); ?></th>
                                <th><?= Yii::t('app', 'Poznámka'); ?></th>
                                <th><?= Yii::t('app', 'M.j.'); ?></th>
                                <th><?= Yii::t('app', 'Cena za M.J.'); ?></th>
                                <th><?= Yii::t('app', 'Status'); ?></th>
                                <th><?= Yii::t('app', 'Akcie'); ?></th>
                            </tr>
                            </thead>
                            <?php
                            /**
                             * @var $services
                             */
                            foreach ($services as $service) {?>
                            <tr>
                                <td><?= $service->id ?></td>
                                <td><?= $service->nazov ?></td>
                                <td><?= $service->popis ?></td>
                                <td><?= $service->poznamka ?></td>
                                <td><?= $service->merna_jednotka ?></td>
                                <td><?= $service->cena_za_jednotku ?></td>
                                <td><input
                                            type="checkbox"
                                            class="js-switch"
                                            data-color="#26c6da"
                                            data-secondary-color="#f62d51"
                                            data-service="<?= $service->id ?>"
                                        <?= $service->status == 1 ? ' checked' : '' ?>
                                    ></td>
                                <td>
                                    <a href="<?= Url::to(['services/edit','id' => $service->id]) ?>" title="Edit" style="color: black"><i class="fas fa-pencil-alt"></i></a>
                                </td>
                            </tr>

                                <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$js = <<<JS
    $(function() {
        $('.dattable').DataTable({
            order: []
        });
    });
    $('.js-switch').each(function () {
        new Switchery($(this)[0], $(this).data());
    });
    $('.js-switch').change(function(){
        let c = $(this).is(':checked') ? 1 : 0;
        let id = $(this).data('service');
        $.ajax({
           url: "/backoffice/services/change-service-status",
           dataType: "json",
           data: {status:c, service_id: id, {$csrf}},
           type: "post"
       })
       .done(function(res){
          if (res.status == 'error') {
             console.log(res.message);
          } else {
             showMyToast(res, res.message); 
          }
       });
    });
JS;
$this->registerJS($js);
