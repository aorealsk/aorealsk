<?php
use backend\assets\RealAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app','Editácia úlohy');

$this->registerCSSFile('@web/css/issue.css?v=0.5',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/js/issue.js?v=0.1',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/html5-editor/bootstrap-wysihtml5.css',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/tinymce/tinymce.min.js',['depends'=>RealAsset::class]);

// ✅ Fix badge colors + Issue Links visibility + selection color
$this->registerCss("
    /* Ticket number badge at top (ITPHP-18) */
    .badge-secondary {
        background-color: #6c757d !important; /* bootstrap default grey */
        color: #ffffff !important;           /* white text */
    }

    /* Issue Links styling */
    #issue-links .issue-link-item,
    #issue-links .issue-link-item a {
        color: #343a40 !important;      /* dark grey text */
        font-size: 13px;
    }
    #issue-links .issue-link-type.badge {
        background-color: #e9ecef !important; /* light grey badge */
        color: #343a40 !important;
    }

    /* Make selected text clearly visible */
    ::selection {
        background: #007bff;
        color: #ffffff;
    }
    ::-moz-selection {
        background: #007bff;
        color: #ffffff;
    }
");

?>

<main class="bg-white p-20">
    <div class="row m-b-20">
        <div class="col-md-12">
            <ol class="breadcrumb font-14 m-0 p-0">
                <li class="breadcrumb-item">
                    <a href="/backoffice/tasks"><?= Yii::t('app','Úlohy') ?></a>
                </li>
                <li class="breadcrumb-item active">
                    <?= Html::encode($task->ticketNumber . ' ' . $task->title) ?>
                </li>
            </ol>
        </div>
    </div>

    <div class="row m-b-20">
        <div class="col-md-9">
            <!-- small ticket card header -->
            <div class="d-flex align-items-center m-b-10">
                <span class="badge badge-secondary m-r-10">
                    <?= Html::encode($task->ticketNumber) ?>
                </span>
                <h4 class="card-title m-b-0 editable">
                    <?= Html::encode($task->title) ?>
                </h4>
            </div>
            <button type="button" class="btn btn-sm btn-dark m-b-15" id="title-save">
                <i class="mdi mdi-content-save"></i> Save
            </button>
            <button type="button" class="btn btn-sm btn-secondary m-b-15" id="title-cancel">
                <i class="mdi mdi-close"></i> Cancel
            </button>

            <form method="post"
            action="<?= Url::to(['/tasks/delete', 'id' => $task->id]) ?>"
            class="d-inline"
            onsubmit="return confirm('Naozaj chceš odstrániť tento ticket?');">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                <button type="submit" class="btn btn-sm btn-danger m-b-15">
                    <i class="mdi mdi-delete"></i> <?= Yii::t('app','Delete') ?>
                </button>
            </form>

            <section class="m-b-40">
                <a class="btn btn-sm btn-secondary" href="#attachments">
                    <i class="mdi mdi-attachment"></i> <?= Yii::t('app','Attachment')?>
                </a>
                <a class="btn btn-sm btn-secondary" href="#issue-links">
                    <i class="mdi mdi-link-variant"></i> <?= Yii::t('app','Link')?>
                </a>
                <button class="btn btn-sm btn-secondary" type="button" id="log-time">
                    <i class="mdi mdi-clock"></i> Log Time
                </button>
            </section>

            <h5>Description</h5>
            <section class="m-b-40">
                <form method="post">
                    <textarea id="issue-desc" cols="30" rows="10" class="form-control"><?php
                        // summary HTML goes raw into TinyMCE
                        echo $task->summary;
                    ?></textarea>
                    <button class="btn btn-sm btn-dark m-t-20" type="button" id="cnt-save">
                        <i class="mdi mdi-content-save"></i> Save
                    </button>
                </form>
            </section>

            <!-- ✅ CHECKPOINTS SECTION -->
            <h5><?= Yii::t('app','Checkpoints') ?></h5>
            <section class="m-b-40">
                <ul class="list-unstyled" id="checkpoints">
                    <?php
                    // Normalize checkpoints:
                    //  - "title - desc" -> split
                    //  - standalone "- desc" rows attach to previous
                    $displayCheckpoints = [];
                    $rawCps = $task->checkpoints;
                    foreach ($rawCps as $cp) {
                        $label = trim((string)$cp->label);

                        // row like "- description" (description-only)
                        if (strpos($label, '- ') === 0 && !empty($displayCheckpoints)) {
                            $displayCheckpoints[count($displayCheckpoints)-1]['desc'] = trim(substr($label, 2));
                            continue;
                        }

                        $parts = explode(' - ', $label, 2);
                        $title = trim($parts[0] ?? '');
                        $desc  = trim($parts[1] ?? '');

                        $displayCheckpoints[] = [
                            'id'     => (int)$cp->id,
                            'isDone' => !empty($cp->isDone),
                            'title'  => $title,
                            'desc'   => $desc,
                        ];
                    }
                    ?>

                    <?php if (!empty($displayCheckpoints)): ?>
                        <?php foreach ($displayCheckpoints as $cp): ?>
                            <li class="checkpoint-item m-b-5" data-id="<?= $cp['id'] ?>">
                                <label class="mb-0">
                                    <input type="checkbox"
                                           class="checkpoint-toggle"
                                           data-id="<?= $cp['id'] ?>"
                                           <?= $cp['isDone'] ? 'checked' : '' ?>>
                                    <span class="checkpoint-text <?= $cp['isDone'] ? 'checkpoint-done' : '' ?>">
                                        <span class="cp-title"><?= Html::encode($cp['title']) ?></span>
                                        <?php if ($cp['desc'] !== ''): ?>
                                            <span class="cp-sep"> - </span>
                                            <span class="cp-desc text-muted"><?= Html::encode($cp['desc']) ?></span>
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-muted">
                            <?= Yii::t('app','No checkpoints yet.') ?>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="input-group m-t-10">
                    <input type="text"
                           id="new-checkpoint-title"
                           class="form-control"
                           placeholder="<?= Yii::t('app','New checkpoint...') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="button" id="add-checkpoint">
                            <i class="mdi mdi-plus"></i> <?= Yii::t('app','Add') ?>
                        </button>
                    </div>
                </div>
            </section>
            <!-- ✅ END CHECKPOINTS SECTION -->

            <!-- ✅ ISSUE LINKS -->
            <h5>Issue Links</h5>
            <ul class="m-b-15 list-unstyled" id="issue-links">
                <?php if ($task->issueLinks): ?>
                    <?php foreach ($task->issueLinks as $link): ?>
                        <li class="m-b-5 issue-link-item">
                            <span class="badge issue-link-type badge-light">
                                <?= Html::encode($link->type) ?>
                            </span>
                            <span class="issue-link-key">
                                <?= Html::encode($link->issueKey) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-muted">
                        <?= Yii::t('app','No issue links yet.') ?>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- ✅ ATTACHMENTS -->
            <h5>Attachments</h5>
            <ul class="m-b-15 list-unstyled" id="attachments">
                <?php if ($task->attachments): ?>
                    <?php foreach ($task->attachments as $att): ?>
                        <li class="m-b-5">
                            <i class="mdi mdi-paperclip"></i>
                            <a href="/<?= Html::encode($att->filePath) ?>" target="_blank">
                                <?= Html::encode($att->fileName) ?>
                            </a>
                            <span class="small text-muted">
                                <?= $att->uploadedBy ? ' – '.Html::encode($att->uploadedBy) : '' ?>
                                <?= $att->uploadedAt ? ' ('.Html::encode($att->uploadedAt).')' : '' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="text-muted">
                        <?= Yii::t('app','No attachments yet.') ?>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- ✅ WORKLOG -->
            <h5><?= Yii::t('app','Worklog'); ?></h5>
            <div class="m-b-15" id="worklogs">
                <?php if (empty($worklogs)): ?>
                    <p class="m-t-20 w-100 text-center">
                        <?= Yii::t('app','No Work Logs are available...') ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($worklogs as $log): ?>
                        <?php
                            $date = $log['loggedDateTo'] ?? $log['loggedDate'] ?? $log['date'] ?? '';
                            $who  = $log['loggedBy'] ?? $log['user'] ?? '';
                            $time = $log['workedHours'] ?? $log['time'] ?? '';
                            $note = $log['note'] ?? $log['description'] ?? '';
                        ?>
                        <div class="border rounded p-10 m-b-10 worklog-item">
                            <div class="small text-muted m-b-5">
                                <?= Html::encode($date) ?>
                                <?= $who ? ' • '.Html::encode($who) : '' ?>
                                <?= $time ? ' • '.Html::encode($time) : '' ?>
                            </div>
                            <div><?= nl2br(Html::encode($note)) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <h5>Activities</h5>

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-comments-tab" data-toggle="tab"
                       href="#nav-comments" role="tab" aria-controls="nav-comments" aria-selected="true">
                        Comments
                    </a>
                    <a class="nav-item nav-link" id="nav-history-tab" data-toggle="tab"
                       href="#nav-history" role="tab" aria-controls="nav-history" aria-selected="false">
                        History
                    </a>
                </div>
            </nav>

            <div class="tab-content m-b-40" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-comments" role="tabpanel"
                     aria-labelledby="nav-comments-tab">
                    <?php if (empty($comments)): ?>
                        <p class="m-t-20 w-100 text-center">
                            No Comments are available...
                        </p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="m-b-10 border-bottom pb-10">
                                <?= $comment['summary'] ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="nav-history" role="tabpanel"
                     aria-labelledby="nav-history-tab">
                    <?php if (empty($history)): ?>
                        <p class="m-t-20 w-100 text-center">
                            No History is available...
                        </p>
                    <?php else: ?>
                        <table class="table table-sm m-t-20 table-borderless w-100 xt123">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($history as $item): ?>
                                <tr>
                                    <td><?= Html::encode($item['createdAt']) ?></td>
                                    <td><?= Html::encode($item['updatedBy']) ?></td>
                                    <td><?= Html::encode($item['field']) ?></td>
                                    <td><?= Html::encode($item['oldValue']) ?></td>
                                    <td><?= Html::encode($item['newValue']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <div id="addcomment" class="w-100 m-b-30">
                <form method="post" id="comment-form">
                    <textarea id="mymce" cols="30" rows="10" class="form-control"></textarea>
                    <button type="button" class="btn btn-sm btn-dark m-t-10" id="savecomment">
                        Save
                    </button>
                </form>
            </div>
            <button class="btn btn-sm btn-secondary" type="button" id="add-comment">
                <i class="mdi mdi-comment-outline"></i> Add comment
            </button>
        </div>

        <!-- RIGHT COLUMN – ticket card info -->
        <div class="col-md-3">
            <h5><?= Yii::t('app', 'Stage') ?></h5>
            <section class="m-b-15">
                <select class="form-select" id="s1">
                    <?php foreach ($stages as $stage=>$title): ?>
                        <option value="<?= Html::encode($stage) ?>"
                            <?= $stage === $task->stage ? 'selected' : '' ?>>
                            <?= Html::encode($title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </section>
            
            <h5>Priority</h5>
            <section class="m-b-15">
                <select id="s2" class="form-select">
                    <?php foreach ($priorities as $priority=>$title): ?>
                        <option value="<?= Html::encode($priority) ?>"
                            <?= $priority === $task->priority ? 'selected' : '' ?>>
                            <?= Html::encode($title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </section>

            <h5>Assignee</h5>
            <section class="m-b-15">
                <select class="form-select" id="s3">
                    <?php foreach ($users as $user): ?>
                        <option value="<?= Html::encode($user['username']) ?>"
                            <?= $user['username'] === $task->assignee ? 'selected' : '' ?>>
                            <?= Html::encode($user['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </section>

            <h5>Reporter</h5>
            <p class="m-b-15">
                <?= Html::encode($task->reporter) ?>
            </p>

            <h5>Created</h5>
            <p class="m-b-15"><?= Html::encode($task->createdAt) ?></p>

            <h5>Due Date</h5>
            <p class="m-b-15">
                <?php if (is_null($task->dueDate)): ?>
                    <?php $hideP = " style='display: none'"; ?>
                    <input type="datetime-local" id="dueDateSelector" class="form-control">
                <?php else: ?>
                    <?php $hideP = ''; ?>
                <?php endif; ?>
            <p id="p1"<?= $hideP ?>>
                <?= Html::encode($task->dueDate) ?>
            </p>
            </p>
        </div>
    </div>
</main>

<!-- worklog modal window -->
<div class="modal fade" id="worklogModal" tabindex="-1" role="dialog"
     aria-labelledby="worklogModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="worklogModalLabel">
                    <?= Yii::t('app','Work Log') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="worklog-form">
                    <div id="errors"></div>
                    <div class="form-group m-b-10">
                        <label class="form-control-label">
                            <?= Yii::t('app','Description') ?>
                        </label>
                        <textarea class="form-control lt" style="height: 150px"
                                  data-item="note"></textarea>
                    </div>
                    <div class="form-check m-b-10">
                        <input type="checkbox" class="form-check-input"
                               id="lt-period" value="check">
                        <label class="form-check-label" for="lt-period">
                            <?= Yii::t('app','Period') ?>
                        </label>
                    </div>
                    <div class="form-group m-b-10" id="lt-from">
                        <label class="form-control-label">
                            <?= Yii::t('app','From') ?>
                        </label>
                        <input type="datetime-local" class="form-control lt"
                               data-item="loggedDateFrom">
                    </div>
                    <div class="form-group m-b-10">
                        <label class="form-control-label">
                            <?= Yii::t('app','Date') ?><sup style="color: red">*</sup>
                        </label>
                        <input type="datetime-local" class="form-control lt"
                               required aria-required="true" data-label="Date"
                               data-item="loggedDateTo">
                    </div>
                    <div class="form-group m-b-10">
                        <label class="form-control-label">
                            <?= Yii::t('app','Worked Hours') ?><sup style="color: red">*</sup>
                        </label>
                        <input type="text" class="form-control lt"
                               required aria-required="true" data-label="Worked Hours"
                               data-item="workedHours">
                        <div class="small text-muted">
                            Ex. 1h 35m, 15m,...etc.
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?= Yii::t('app','Close') ?></button>
                <button type="button" class="btn btn-primary"
                        id="logtime-save"><?= Yii::t('app','Save') ?></button>
            </div>
        </div>
    </div>
</div>
<!-- end of worklog modal window -->

<?php
$csrf = "'" . Yii::$app->request->csrfParam ."':'". Yii::$app->request->getCsrfToken() ."'";
$js = <<<JS
    $('#add-comment').click(function(){
       $('#addcomment').show();
       $(this).hide();
    });
    
    $('#worklogModal').on('hidden.bs.modal', function () {
        $('#lt-period').prop('checked',false);
        $('.lt').val('');
    });

    $('#logtime-save').click(function(){
        let hasError = false;
        let er = []; 
        let x = [];
        
        $('.lt').each(function(_,v){
           if ($(v).attr('required') !== undefined && $(v).val() === '') {
               hasError = true;
               let y = $(v).data('label');
               er.push(
                  '<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                  '<strong>'+y+'</strong> cannot be empty!!! <button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                  '<span aria-hidden="true">&times;</span></button></div>'  
               );
           } else {
               x.push({
                    item: $(v).data('item'), value:$(v).val()
               })
           }
        });
        if (hasError) {
            $('#errors').empty();
            $.each(er,function(_,v){
                $('#errors').append(v);
            });
            return false;
        } else {
            $.ajax({
               url: "/backoffice/tasks/log-time",
               dataType: "json",
               data: { timedata: x, ticketid: {$_GET['id']}, {$csrf} },
               type: "post"
           })
           .done(function(res){
              showMyToast(res, 'Time was written');
              $('#worklogModal').modal('hide');
           });
        }
    });

    $('#lt-period').on('click',function(){
        $('#lt-from').toggle();
    });

    $('#log-time').click(function(){
        $('#worklogModal').modal('show');
    });

    $('#s1').change(function(){
        let x = $(this).val();
        $.ajax({
           url: "/backoffice/tasks/update-stage",
           dataType: "json",
           data: { stage: x, ticketid: {$_GET['id']}, {$csrf} },
           type: "post"
       })
       .done(function(res){
          showMyToast(res, 'Stage update done');
       });
    });

    $('#s2').change(function(){
        let x = $(this).val();
        $.ajax({
           url: "/backoffice/tasks/update-priority",
           dataType: "json",
           data: { priority: x, ticketid: {$_GET['id']}, {$csrf} },
           type: "post"
       })
       .done(function(res){
         showMyToast(res, 'Priority update done');
       });
    });
    
    $('#s3').change(function(){
        let x = $(this).val();
        $.ajax({
           url: "/backoffice/tasks/update-assignee",
           dataType: "json",
           data: { assignee: x, ticketid: {$_GET['id']}, {$csrf} },
           type: "post"
       })
       .done(function(res){
          showMyToast(res, 'Assignee update done');
       });
    });

    $('#dueDateSelector').change(function(){
       let x = $(this).val();
       $.ajax({
           url: "/backoffice/tasks/update-due-date",
           dataType: "json",
           data: { duedate: x, ticketid: {$_GET['id']}, {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'ok') {
              $('#dueDateSelector').hide();
              $('#p1').html(res.newDueDate).show();
          }
          showMyToast(res, 'Due Date update done');            
       });
    });

    $('#issue-desc').keypress(function(){
       $('#cnt-save').show(); 
    });

    $('#cnt-save').click(function(){
        let x = tinymce.get('issue-desc').getContent();
        $.ajax({
           url: "/backoffice/tasks/update-description",
           dataType: "json",
           data: { descr: x, ticketid: {$_GET['id']}, {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'ok') {
              $('#cnt-save').hide();
          } 
          showMyToast(res, 'Description update done');            
       });
    });

    $('.card-title').click(function(){
       $(this).attr('contenteditable',true)
              .addClass('contedit')
              .removeClass('editable m-b-15'); 
       $('#title-save').show();
       $('#title-cancel').show();
    });

    $('#title-cancel').click(function(){
        $('.card-title').attr('contenteditable',false).removeClass('contedit');
        $('#title-save').hide();
        $(this).hide();
    });

    $('#title-save').click(function(){
        let x = $('.card-title').text();
        $.ajax({
           url: "/backoffice/tasks/update-title",
           dataType: "json",
           data: { title: x, ticketid: {$_GET['id']}, {$csrf} },
           type: "post"
       })
       .done(function(res){
          if (res.status == 'ok') {
              $('.card-title').attr('contenteditable',false).removeClass('contedit');
          } 
          showMyToast(res, 'Description update done');
          $('#title-save').hide();
          $('#title-cancel').hide();
       });
    });
    
    if ($("#mymce").length > 0) {
        tinymce.init({
            selector: "textarea#mymce",
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

    tinymce.init({
        selector: "textarea#issue-desc",
        theme: "modern",
        height: 300,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons"
    });

    $('#savecomment').click(function(){
        let x = tinymce.get('mymce').getContent();
        $.ajax({
           url: "/backoffice/tasks/save-comment",
           dataType: "json",
           data: { comment: x, ticketid: {$_GET['id']}, {$csrf} },
           type: "post"
       })
       .done(function(res){
          showMyToast(res, 'Description update done');
       });
    });

    // ✅ CHECKPOINTS JS

    // Add new checkpoint (inline, only title)
    $('#add-checkpoint').on('click', function () {
        var title = $('#new-checkpoint-title').val().trim();
        if (!title) {
            return;
        }

        $.ajax({
            url: "/backoffice/tasks/add-checkpoint",
            dataType: "json",
            type: "post",
            data: { title: title, taskId: {$_GET['id']}, {$csrf} }
        }).done(function (resp) {
            if (resp.success) {
                $('#checkpoints .text-muted').remove();

                $('#checkpoints').append(
                    '<li class="checkpoint-item m-b-5" data-id="' + resp.id + '">' +
                        '<label class="mb-0">' +
                            '<input type="checkbox" class="checkpoint-toggle" data-id="' + resp.id + '">' +
                            '<span class="checkpoint-text">' +
                                '<span class="cp-title">' + resp.label + '</span>' +
                            '</span>' +
                        '</label>' +
                    '</li>'
                );
                $('#new-checkpoint-title').val('');
            }
        });
    });

    // Toggle checkpoint done / undone
    $(document).on('change', '.checkpoint-toggle', function () {
        var \$cb = $(this);
        var done = \$cb.is(':checked') ? 1 : 0;
        var id   = \$cb.data('id');

        $.ajax({
            url: "/backoffice/tasks/toggle-checkpoint",
            dataType: "json",
            type: "post",
            data: { id: id, done: done, {$csrf} }
        }).done(function () {
            var \$text = \$cb.closest('label').find('.checkpoint-text').first();
            if (done) {
                \$text.addClass('checkpoint-done');
            } else {
                \$text.removeClass('checkpoint-done');
            }
        });
    });

JS;

$this->registerJS($js);
