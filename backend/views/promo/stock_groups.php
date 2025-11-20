<?php

use backend\assets\RealAsset;
use yii\helpers\Url;

/**
 * @var $groups
 */

$this->title = 'Kategórie';
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
        <diw class="col-12">
            <a href="<?= Url::to(['/promo/stock']) ?>"
               class="btn btn-danger text-white">Späť
            </a>
            <a href="<?= Url::to(['/promo/add-stock-group']) ?>"
               class="btn btn-success text-white">Pridať
            </a>
        </diw>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm dattable" id="ff-01">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Názov</th>
                                <th>Popis</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?= $this->render('stock_groups_tbody', [
                                    'groups' => $groups
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

$css = <<<CSS
    .mybadge-success {
        padding: 5px; 
        border-radius: 5px; 
        color:#fff;
        background-color:#2b542c;
        font-size: 12px;
    }
    .mybadge-danger {
        color:#fff;
        padding: 5px;
        border-radius: 5px;
        background-color: #981D2D;
        font-size: 12px;
    }   
CSS;
$this->registerCSS($css);

$js = <<<JS
    $(function() {
        $('.dattable').DataTable({
            order: []
        });
    });
    
    $(document).on('click', '.del-grp', function() {
        var gid = $(this).data('gid');
        
        if (confirm('Naozaj chcete vymazať túto kategóriu?')) {
            $.ajax({
                url: '/backoffice/promo/delete-stock-group',
                type: 'POST',
                data: {
                    gid: gid
                },
                success: function(data) {
                    if (data.status === 'ok') {
                        $('#ff-01 tbody').empty().append(data.tbody);
                    } else {
                        alert('Nepodarilo sa vymazať kategóriu! ' + data.message);
                    }
                }
            });
        }
    });
    
    $(document).on('click', '.reop-grp', function() {
        var gid = $(this).data('gid');
        
        if (confirm('Naozaj chcete aktivovať túto kategóriu?')) {
            $.ajax({
                url: '/backoffice/promo/reopen-stock-group',
                type: 'POST',
                data: {
                    gid: gid
                },
                success: function(data) {
                    if (data.status === 'ok') {
                        $('#ff-01 tbody').empty().append(data.tbody);
                    } else {
                        alert('Nepodarilo sa aktivovať kategóriu! ' + data.message);
                    }
                }
            });
        }
    });
JS;
$this->registerJS($js);
