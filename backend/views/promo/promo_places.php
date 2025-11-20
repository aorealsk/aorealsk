<?php

use backend\assets\RealAsset;
use yii\helpers\Url;

/**
 * @var $places
 */

$this->title = Yii::t('app', 'Pozície');
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
            <a href="<?= Url::to(['/promo/add-promo-place']) ?>" class="btn btn-success text-white">
                Pridať pozíciu
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm dattable" id="t-01">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Akcia</th>
                                <th>Názov</th>
                                <th>Dátum</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?=
                                $this->render('promo_places_tbody', ['places' => $places])
                            ?>
                            </tbody>
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

    $('a.del').on('click', function(e) {
       const xid = $(this).data('xid');
       if (confirm('Naozaj chcete zmazat poziciu?')) {
           $.ajax({
                url: "/backoffice/promo/delete-place",
                dataType: "json",
                data: { pid: xid, {$csrf} },
                type: "POST"
           }).done(function(r){
                if (r.status === 'error') {
                    console.log(r.message);
                } else {
                    $('#t-01').find('tbody').empty().append(r.tbody); 
                }
           });    
       }
    });
JS;
$this->registerJS($js);
