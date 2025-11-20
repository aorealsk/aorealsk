<?php
use backend\assets\RealAsset;
use yii\helpers\Url;

$this->title = Yii::t('app','Premenné pre dokumentov');
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/js/issue.js?v=0.1', ['depends' => RealAsset::class]);
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card rounded-5 card-shadow">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-dark text-white" id="add-col">
                                <i class="fas fa-plus-circle"></i>
                                <?= Yii::t('app','Pridať stĺpec'); ?>
                            </button>
                            <button type="button" class="btn btn-primary text-white m-l-5" id="add-row">
                                <i class="fas fa-plus-circle"></i>
                                <?= Yii::t('app','Pridať riadok'); ?>
                            </button>
                            <a href="<?= Url::to(['manager']) ?>" class="btn btn-success text-white">
                                <i class="fas fa-tasks"></i>
                                <?= Yii::t('app','Prepnúť na manažéra'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="tscroll">
                        <table id="t2022">
                            <?= $this->render('tablebody',['rows'=>$rows,'cols'=>$cols,'fullmap'=>$fullmap]); ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add row to the template vars rows table -->

<div
        class="modal fade"
        id="AddRowModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="AddRowModalLabel"
        aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddRowModalLabel"><?= Yii::t('app','Nový riadok'); ?></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="row">
                        <div class="col-12">
                            <label class="control-label"><?= Yii::t('app','Názov'); ?></label>
                            <input type="text" class="form-control" id="d01">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('app','Zatvoriť'); ?></button>
                <button type="button" class="btn btn-primary text-white" id="rowsave"><?= Yii::t('app','Uložiť'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- end of modal dialog -->

<!-- Add column to the template vars cols table -->

<div
        class="modal fade"
        id="AddColModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="AddColModalLabel"
        aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AddColModalLabel"><?= Yii::t('app','Nový stĺpec'); ?></h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form role="form" id="f0x0a">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="control-label"><?= Yii::t('app','Popis'); ?></label>
                            <input type="text" class="form-control i1" data-item="title" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="control-label"><?= Yii::t('app','Prefix'); ?></label>
                            <input type="text" class="form-control i1" data-item="prefix" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="control-label"><?= Yii::t('app','Postfix'); ?></label>
                            <input type="text" class="form-control i1" data-item="postfix">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Yii::t('app','Zatvoriť'); ?></button>
                <button type="button" class="btn btn-primary text-white" id="colsave"><?= Yii::t('app','Uložiť'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- end of modal dialog -->

<?php

$csrf = "'" . Yii::$app->request->csrfParam ."':'". Yii::$app->request->getCsrfToken() ."'";

$css = <<<CSS

.tscroll {
  width: 100%;
  overflow: scroll;
  max-height: 500px;
}

.tscroll table td:first-child, table th:first-child, table tr:first-child {
  position: sticky;
  left: 0;
  background: #eee;
}

.tscroll table td, th, tr {
    padding: 10px 20px;
}
.mapitem {
    cursor: pointer;
}
.rounded-5 {
    border-radius: .5em!important;
}
.card-shadow {
    box-shadow: lightgrey 3px 3px;
}
CSS;
$this->registerCSS($css);

$js = <<<JS
    $('.mapitem').click(function(){
       let xcord = $(this).data('xcord');
       let ycord = $(this).data('ycord');
       let stat = $(this).is(':checked') ? 1 : 0;
       $.ajax({
            url: "/backoffice/template-vars/update-item",
            dataType: "json",
            data: {x:xcord, y:ycord, s:stat},
            type: "POST"
        }).done(function (res){
            showMyToast(res, res.message);
        });
    });

    $('#add-col').click(function(){
        $('#AddColModal').modal('show');
    });
    
    $('#add-row').click(function(){
        $('#AddRowModal').modal('show');
    });
    
    $('.cols').blur(function(){
        
    });
    
    $('.rows').blur(function(){
        
    });

    $('#rowsave').click(function (){
        let sd = $('#d01').val();
        $.ajax({
            url: "/backoffice/template-vars/add-row",
            dataType: "json",
            data: {d: sd},
            type: "POST"
        }).done(function (res){
            showMyToast(res, res.message);
            $('#t2022').DataTable().destroy();
            $('#t2022').find('tbody').empty().append(res.tablebody);
            $('#t2022').DataTable().draw();
        });
    });
    
    $('#colsave').click(function (){
       let data = '';
       
       $.each($('.i1'),function(){
           // TODO: check if element is required
           // TODO: impelement some error message to show what wasn't filled
           data += $(this).data('item') + ":" + $(this).val() + "|";    
       });
       
       $.ajax({
            url: "/backoffice/template-vars/add-column",
            dataType: "json",
            data: {d: data},
            type: "POST"
       }).done(function (res){
            showMyToast(res, res.message);
            $('#t2022').DataTable().destroy();
            $('#t2022').find('tbody').empty().append(res.tablebody);
            $('#t2022').DataTable().draw();
       });
    });
    
JS;

$this->registerJS($js);


