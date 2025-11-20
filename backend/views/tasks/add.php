<?php
use backend\assets\RealAsset;
use common\models\tasks\TasksPriority;
use yii\helpers\Html;
use yii\helpers\Json;

$this->title = Yii::t('app','Pridať úlohu');
$this->registerCSSFile('@web/css/tasks.css?v=0.9',['depends'=>RealAsset::class]);

// TinyMCE
$this->registerJSFile('@web/assets/node_modules/tinymce/tinymce.min.js',['depends'=>RealAsset::class]);

$emptyTaskTitle = Yii::t('app','Názov úlohy nemože byť prázdny');
$emptyProject   = Yii::t('app','Zvoľte si projekt');

// HTML5 datetime-local érték konverzió
$dueDateValue = '';
if (isset($postData['dueDate']) && $postData['dueDate'] !== '') {
    $ts = strtotime($postData['dueDate']);
    if ($ts !== false) {
        $dueDateValue = date('Y-m-d\TH:i', $ts);
    }
}

// Checkpoint / Issue link / Worklog placeholder szövegek JS-hez
$cpNamePlaceholderJs   = Json::encode(Yii::t('app','Názov checkpointu'));
$cpDescPlaceholderJs   = Json::encode(Yii::t('app','Popis checkpointu'));

$ilTypePlaceholderJs   = Json::encode(Yii::t('app','Typ odkazu (napr. relates to)'));
$ilKeyPlaceholderJs    = Json::encode(Yii::t('app','ID úlohy / ticketu'));

$wlTimePlaceholderJs   = Json::encode(Yii::t('app','Čas (napr. 1h 30m)'));
$wlCommentPlaceholderJs= Json::encode(Yii::t('app','Komentár'));
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <?= common\widgets\Alert::widget() ?>

    <div class="card">
        <div class="card-body">
            <form method="post" role="form" id="task-frm" enctype="multipart/form-data">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken()?>">

                <div class="form-group row has-danger">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Projekt') ?><sup style="color:red">*</sup>
                    </label>
                    <div class="col-5">
                        <select id="i1" class="form-control form-control-danger form-select req" name="Task[project]" data-item="project">
                            <option value=""><?= Yii::t('app','Zvoľte projekt') ?></option>
                            <?php foreach($boardProjects as $project): ?>
                                <option value="<?= $project['code'] ?>"><?= $project['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="project-error" class="invalid-feedback">
                            <?= $emptyProject ?>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Priorita') ?><sup style="color:red">*</sup>
                    </label>
                    <div class="col-5">
                        <select class="form-select req" name="Task[priority]">
                            <?php foreach(TasksPriority::getPriorities() as $key => $prio): ?>
                                <option value="<?= $key ?>"><?= $prio ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row <?= isset($postData['title']) && $postData['title'] != '' ? '' : 'has-danger'?>">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Názov') ?><sup style="color:red">*</sup>
                    </label>
                    <div class="col-8">
                        <input
                            id="i2"
                            type="text"
                            class="form-control form-control-danger"
                            name="Task[title]"
                            data-item="title"
                            value="<?= isset($postData['title']) ? $postData['title'] : '' ?>"
                        >
                        <div id="title-error" class="invalid-feedback">
                            <?= $emptyTaskTitle ?>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Priradiť skupine') ?>
                    </label>
                    <div class="col-5">
                        <select class="form-select" name="Task[assigneeGroup]" id="assigneeGroup">
                            <option value="unassigned">unassigned</option>
                            <?php foreach($groups as $group): ?>
                                <option value="<?= $group['name'] ?>"><?= $group['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Priradiť osobe') ?>
                    </label>
                    <div class="col-5">
                        <select class="form-select" name="Task[assignee]" id="assignee">
                            <option value="unassigned">unassigned</option>
                            <?php foreach($users as $user): ?>
                                <option value="<?= $user['username'] ?>"><?= $user['username'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Popis') ?>
                    </label>
                    <div class="col-8">
                        <textarea
                            class="form-control"
                            rows="8"
                            name="Task[summary]"
                            id="summary"><?= isset($postData['summary']) ? $postData['summary'] : '' ?></textarea>
                    </div>
                </div>

                <!-- ✅ CHECKPOINTOK: Név + Leírás -->
                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Checkpoints') ?>
                    </label>
                    <div class="col-8">
                        <ul id="cp-list" class="list-unstyled">
                            <li class="m-b-2 cp-row">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control cp-name"
                                        name="Checkpoints[][name]"
                                        placeholder="<?= Yii::t('app','Názov checkpointu') ?>"
                                    >
                                    <span class="input-group-text">-</span>
                                    <input
                                        type="text"
                                        class="form-control cp-desc"
                                        name="Checkpoints[][description]"
                                        placeholder="<?= Yii::t('app','Popis checkpointu') ?>"
                                    >
                                </div>
                            </li>
                        </ul>
                        <button type="button"
                                class="btn btn-secondary btn-sm m-t-10"
                                id="cp-add-row">
                            <i class="mdi mdi-plus"></i> <?= Yii::t('app','Pridať checkpoint') ?>
                        </button>
                        <div class="small text-muted m-t-5">
                            <?= Yii::t('app','Každý riadok bude uložený ako samostatný checkpoint (názov - popis).') ?>
                        </div>
                    </div>
                </div>
                <!-- ✅ CHECKPOINTOK VÉGE -->

                <!-- ✅ Dátum dodania -->
                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Dátum dodania') ?>
                    </label>
                    <div class="col-5">
                        <input
                            type="datetime-local"
                            id="due-date"
                            class="form-control"
                            name="Task[dueDate]"
                            value="<?= Html::encode($dueDateValue) ?>"
                        >
                    </div>
                </div>

                <!-- ✅ ISSUE LINKS -->
                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Issue links') ?>
                    </label>
                    <div class="col-8">
                        <ul id="il-list" class="list-unstyled">
                            <li class="m-b-2 il-row">
                                <div class="row">
                                    <div class="col-4 m-b-2">
                                        <input
                                            type="text"
                                            class="form-control il-type"
                                            name="IssueLinks[][type]"
                                            placeholder="<?= Yii::t('app','Typ odkazu (napr. relates to)') ?>"
                                        >
                                    </div>
                                    <div class="col-8 m-b-2">
                                        <input
                                            type="text"
                                            class="form-control il-key"
                                            name="IssueLinks[][key]"
                                            placeholder="<?= Yii::t('app','ID úlohy / ticketu') ?>"
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <button type="button"
                                class="btn btn-secondary btn-sm m-t-10"
                                id="il-add-row">
                            <i class="mdi mdi-plus"></i> <?= Yii::t('app','Pridať issue link') ?>
                        </button>
                    </div>
                </div>

                <!-- ✅ ATTACHMENTS -->
                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Prílohy') ?>
                    </label>
                    <div class="col-8">
                        <ul id="att-list" class="list-unstyled">
                            <li class="m-b-2 att-row">
                                <input type="file" class="form-control" name="Attachments[]">
                            </li>
                        </ul>
                        <button type="button"
                                class="btn btn-secondary btn-sm m-t-10"
                                id="att-add-row">
                            <i class="mdi mdi-plus"></i> <?= Yii::t('app','Pridať prílohu') ?>
                        </button>
                        <div class="small text-muted m-t-5">
                            <?= Yii::t('app','Môžete pridať viac súborov.') ?>
                        </div>
                    </div>
                </div>

                <!-- ✅ WORKLOG -->
                <div class="form-group row">
                    <label class="col-2 col-form-label text-right">
                        <?= Yii::t('app','Worklog') ?>
                    </label>
                    <div class="col-8">
                        <ul id="wl-list" class="list-unstyled">
                            <li class="m-b-2 wl-row">
                                <div class="row">
                                    <div class="col-3 m-b-2">
                                        <input
                                            type="text"
                                            class="form-control wl-time"
                                            name="Worklog[][time_spent]"
                                            placeholder="<?= Yii::t('app','Čas (napr. 1h 30m)') ?>"
                                        >
                                    </div>
                                    <div class="col-9 m-b-2">
                                        <input
                                            type="text"
                                            class="form-control wl-comment"
                                            name="Worklog[][comment]"
                                            placeholder="<?= Yii::t('app','Komentár') ?>"
                                        >
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <button type="button"
                                class="btn btn-secondary btn-sm m-t-10"
                                id="wl-add-row">
                            <i class="mdi mdi-plus"></i> <?= Yii::t('app','Pridať worklog') ?>
                        </button>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-10 offset-2">
                        <button class="btn btn-secondary">
                            <i class="mdi mdi-content-save"></i>
                            <?= Yii::t('app','Uložiť'); ?>
                        </button>
                        <?= Html::a(Yii::t('app','Zrušiť'),['/tasks'],['class'=>'btn btn-danger text-white']); ?>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>

<?php
$cantBeSubmitted = Yii::t('app','Nie sú všetky povinné políčka vyplnené!');
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";

$js = <<<JS
$(function () {

    var cpNamePlaceholder    = {$cpNamePlaceholderJs};
    var cpDescPlaceholder    = {$cpDescPlaceholderJs};
    var ilTypePlaceholder    = {$ilTypePlaceholderJs};
    var ilKeyPlaceholder     = {$ilKeyPlaceholderJs};
    var wlTimePlaceholder    = {$wlTimePlaceholderJs};
    var wlCommentPlaceholder = {$wlCommentPlaceholderJs};

    // Assignee group change → AJAX load users
    $(document).on('change','#assigneeGroup',function() {
        let group = $(this).val();
        $.ajax({
            url: 'get-assignee-list',
            type: 'post',
            data: { group: group, {$csrf} },
            success: function(data) {
                let users = data.users || [];
                let html = '<option value="unassigned">unassigned</option>';
                if (users.length !== 0) {
                    for (let i = 0; i < users.length; i++) {
                        html += '<option value="' + users[i].username + '">' + users[i].username + '</option>';
                    }
                }
                $('#assignee').empty().html(html);
            }
        });
    });

    // Kötelező mezők ellenőrzése submit előtt
    $('#task-frm').on('submit', function(){
        let canSubmit = true;
        $.each($('.req'), function(k, v){
            if ($(v).val() === '') {
                canSubmit = false;
            }
        });
        if (!canSubmit) {
            alert('{$cantBeSubmitted}');
            return false;
        }
    });

    // Hibamegjelenítés projekt / cím mezőknél
    window.displayError = function(e) {
        let x = e.data('item');
        if (e.val() !== '') {
            e.removeClass('form-control-danger').parent().parent().removeClass('has-danger');
            $('#' + x + '-error').hide();
            return false;
        }
        e.addClass('form-control-danger').parent().parent().addClass('has-danger');
        $('#' + x + '-error').show();
    };

    $('#i1').change(function(){
        displayError($(this));
    });

    $('#i2').keyup(function(){
        displayError($(this)); 
    });

    // TinyMCE init – csak ha valóban be van töltve
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: "textarea#summary",
            theme: "modern",
            height: 300,
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table contextmenu directionality emoticons template paste textcolor"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"
        });
    }

    // ✅ Új checkpoint sor
    $('#cp-add-row').on('click', function () {
        $('#cp-list').append(
            '<li class="m-b-2 cp-row">' +
                '<div class="input-group">' +
                    '<input type="text" class="form-control cp-name" name="Checkpoints[][name]" placeholder="' + cpNamePlaceholder + '">' +
                    '<span class="input-group-text">-</span>' +
                    '<input type="text" class="form-control cp-desc" name="Checkpoints[][description]" placeholder="' + cpDescPlaceholder + '">' +
                '</div>' +
            '</li>'
        );
    });

    // ✅ Új issue link sor
    $('#il-add-row').on('click', function () {
        $('#il-list').append(
            '<li class="m-b-2 il-row">' +
                '<div class="row">' +
                    '<div class="col-4 m-b-2">' +
                        '<input type="text" class="form-control il-type" name="IssueLinks[][type]" placeholder="' + ilTypePlaceholder + '">' +
                    '</div>' +
                    '<div class="col-8 m-b-2">' +
                        '<input type="text" class="form-control il-key" name="IssueLinks[][key]" placeholder="' + ilKeyPlaceholder + '">' +
                    '</div>' +
                '</div>' +
            '</li>'
        );
    });

    // ✅ Új attachment sor
    $('#att-add-row').on('click', function () {
        $('#att-list').append(
            '<li class="m-b-2 att-row">' +
                '<input type="file" class="form-control" name="Attachments[]">' +
            '</li>'
        );
    });

    // ✅ Új worklog sor
    $('#wl-add-row').on('click', function () {
        $('#wl-list').append(
            '<li class="m-b-2 wl-row">' +
                '<div class="row">' +
                    '<div class="col-3 m-b-2">' +
                        '<input type="text" class="form-control wl-time" name="Worklog[][time_spent]" placeholder="' + wlTimePlaceholder + '">' +
                    '</div>' +
                    '<div class="col-9 m-b-2">' +
                        '<input type="text" class="form-control wl-comment" name="Worklog[][comment]" placeholder="' + wlCommentPlaceholder + '">' +
                    '</div>' +
                '</div>' +
            '</li>'
        );
    });

});
JS;

$this->registerJS($js);
