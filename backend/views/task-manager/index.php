<?php
use backend\assets\RealAsset;
use yii\helpers\Url;

$this->title= Yii::t('app','Manažér úloh');
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/dist/css/pages/other-pages.css',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/dist/css/pages/tab-page.css',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js',['depends'=>RealAsset::class]);
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center"><h4 class="text-themecolor"><?= $this->title ?></h4></div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">
                <div class="btn-group">
                    <button
                            type="button"
                            class="btn btn-info dropdown-toggle d-none d-lg-block m-l-5 text-white"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                        <i class="fas fa-plus-circle m-r-5"></i>Pridať
                    </button>
                    <div class="dropdown-menu">
                    <a class="dropdown-item" href="/backoffice/task-manager/add-project"> <?php echo Yii::t('app','projekt') ?>...</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="post">
                <div class="tabs customvtab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#projectlist" role="tab">
                                <span class="hidden-sm-up"><i class="mdi mdi-account"></i></span>
                                <span class="hidden-xs-down"><?= Yii::t('app','Projekty') ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#columns" role="tab">
                                <span class="hidden-sm-up"><i class="mdi mdi-view-column"></i></span>
                                <span class="hidden-xs-down"><?= Yii::t('app','Stĺpce') ?></span>
                            </a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="projectlist" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 form-group" style="overflow: auto">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-sm dattable">
                                            <thead>
                                            <tr>
                                                <th><?= Yii::t('app','Kód projektu') ?></th>
                                                <th><?= Yii::t('app','Názov projektu') ?></th>
                                                <th><?= Yii::t('app','Farba') ?></th>
                                                <th><?= Yii::t('app', 'Status') ?></th>
                                                <th><?= Yii::t('app', 'Akcie') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach($projects as $project) {
                                            ?>
                                                <tr>
                                                    <td><?php echo $project['code'] ?></td>
                                                    <td><?php echo $project['name'] ?></td>
                                                    <td style="background-color: <?php echo $project['color'] ?>"></td>
                                                    <td>
                                                        <?php
                                                        if ($project['status'] == 1) {
                                                            echo Yii::t('app','Aktívny');
                                                        } else {
                                                            echo Yii::t('app','Neaktívny');
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a
                                                                href="<?= Url::to(['task-manager/edit-project','id'=>$project['id']]) ?>"
                                                                title="Edit '<?php echo $project['name'] ?>'"
                                                                style="color: black">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="columns" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 form-group" style="overflow: auto">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-sm dattable">
                                            <thead>
                                                <tr>
                                                    <th><?php echo Yii::t('app','Poradie'); ?></th>
                                                    <th><?php echo Yii::t('app','Názov'); ?></th>
                                                    <th><?php echo Yii::t('app','Jazyk'); ?></th>
                                                    <th><?php echo Yii::t('app','Target'); ?></th>
                                                    <th><?php echo Yii::t('app','Zmazateľný?'); ?></th>
                                                    <th><?php echo Yii::t('app','Vytvorený'); ?></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                foreach($columns as $column) {
                                            ?>
                                                 <tr>
                                                     <td style="width: 5%">
                                                         <a
                                                             href="#"
                                                             style="color: black"
                                                             onclick="moveOrder()"
                                                         >
                                                             <i class="mdi mdi-arrow-up-bold"></i>
                                                         </a>
                                                         <a
                                                             href="#"
                                                             style="color: black"
                                                             onclick="moveOrder()"
                                                         >
                                                             <i class="mdi mdi-arrow-down-bold"></i>
                                                         </a>
                                                     </td>
                                                     <td><?php echo $column['title'] ?></td>
                                                     <td><?php echo $column['name'] ?></td>
                                                     <td><?php echo $column['target'] ?></td>
                                                     <td style="width:5%">
                                                         <?php
                                                            if ($column['removable'] < 99) {
                                                         ?>
                                                         <input type="checkbox" name="" id="">
                                                         <?php
                                                            }
                                                         ?>
                                                     </td>
                                                     <td><?php echo $column['createdAt'] ?></td>
                                                     <td>
                                                         <a
                                                                 href="<?= Url::to(['task-manager/edit-column','id'=>$column['id']]) ?>"
                                                                 title="Edit '<?php echo $column['title'] ?>'"
                                                                 style="color: black">
                                                             <i class="fas fa-pencil-alt"></i>
                                                         </a>
                                                     </td>
                                                 </tr>
                                            <?php
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--<div class="tab-pane" id="features" role="tabpanel">

                        </div>-->
                    </div>
                </div>
            </form>
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

    moveOrder = function(i,o) {
        // i = id
        // o = orientation up = 1 or down = -1
    }
JS;

$this->registerJS($js);

$css = <<<CSS
    .vtabs {
    width: 100%;
    }
    .tabs-vertical {
        width: 150px !important;
    }
    
CSS;
$this->registerCSS($css);

