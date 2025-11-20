<?php
/**
 * @var $record
 * @var int $uid
 */
use yii\helpers\Url;
use common\models\users\UserAttendance;
use backend\assets\RealAsset;

$this->registerCSSFile('@web/assets/node_modules/Magnific-Popup-master/dist/magnific-popup.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup-init.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/css/user-card.css', ['depends' => RealAsset::class]);

$this->title = Yii::t('app','Zmena dochádzky');
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['index','uid'=>Yii::$app->request->get('uid')]) ?>" class="btn btn-danger text-white">
                <i class="fas fa-arrow-alt-circle-left"></i>&nbsp;<?php echo Yii::t('app','Späť') ?>
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card rounded-5 card-shadow">
                <div class="card-body">
                <form method="post" role="form" id="frm001">
                    <h4 class="card-title">Základné údaje</h4>
                    <div class="form-group row mt-3">
                        <label class="col-2 col-form-label">Dátum</label>
                        <div class="col-10">
                            <input type="date" disabled value="<?= $record->uaDate ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Príchod</label>
                        <div class="col-10">
                            <input type="time" disabled value="<?= $record->inTime ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Odchod</label>
                        <div class="col-10">
                            <input type="time" disabled value="<?= $record->outTime ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Typ dochádzky</label>
                        <div class="col-10">
                            <input type="text" disabled value="<?= UserAttendance::workType($record->uaType) ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Poznámky</label>
                        <div class="col-10">
                            <textarea name="Record[note]" cols="30" rows="10" class="form-control"><?= $record->note ?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-success text-white" type="button" id="note01"><i class="ti-save"></i>&nbsp;&nbsp;Uložiť</button>
                            <a href="<?= Url::to(['index','uid'=>Yii::$app->request->get('uid')]) ?>" class="btn btn-danger text-white">
                                <i class="fas fa-arrow-alt-circle-left"></i>&nbsp;<?php echo Yii::t('app','Späť') ?>
                            </a>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card rounded-5 card-shadow">
                <div class="card-body">
                    <form method="post" role="form" enctype="multipart/form-data" id="frm-photo">
                        <h4 class="card-title">Nahrať fotku</h4>
                        <input type="hidden" id="photo_userId" value="">
                        <div class="form-group mt-4">
                            <input type="file" class="form-control fileupload" accept="image/*;capture=camera" id="p0">
                        </div>
                        <div class="form-group">
                            <button type="button" id="take-photo" class="btn btn-secondary">
                                <i class="ti-upload"></i>&nbsp;&nbsp;Nahrať
                            </button>
                            <p id="p0-msg" class="mt-3"></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card rounded-5 card-shadow">
                <div class="card-body">
                    <h4 class="card-title">Nahraté fotky</h4>
                    <div class="row el-element-overlay m-t-25" id="el1">
                        <?php
                        /**
                         * @var array $files
                         */
                        if (empty($files) || 0 === count($files)) {
                            echo $this->render('nofile');
                        } else {
                            foreach($files as $file) {
                                echo $this->render('userfiles',['fileinfo'=>$file]);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$uploadImgMessage = 'Nahrávam obrázok...';
$uploadedImgMessage = 'Obrázok bol nahratý úspešne!';

$js = <<<JS
$('#note01').on('click',function(){
    $.ajax({
            url: "/backoffice/user-attendance/update-note",
            dataType: "json",
            data: { userId:{$uid}, {$csrf} },
            type: "POST"
    }).done(function(res){
        alert(res.message);
    });
});

$('#take-photo').click(function(){
    var formData = new FormData();
    formData.append('uid', $('#photo_userId').val());
    formData.append('photo',$('input[type=file]')[0].files[0]);
    $('#p0-msg').text('$uploadImgMessage');
    $.ajax({
            url: "/backoffice/user-attendance/save-photos",
            dataType: "json",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false
    }).done(function(res){
        if (res.status == 'error') {
                console.log(res.message);
                alert(res.message);
            } else {    
                $('#p0').val(null);
                $('#p0-msg').text('$uploadedImgMessage');
            }
    });
});
JS;

$this->registerJs($js);

$css = <<<CSS
.rounded-5 {
    border-radius: .5em!important;
}
.card-shadow {
    box-shadow: lightgrey 3px 3px;
}
CSS;
$this->registerCSS($css);