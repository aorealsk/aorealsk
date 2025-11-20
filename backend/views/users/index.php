<?php

use backend\assets\RealAsset;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * @var $userlist
 * @var $usergroups
 * @var $documents
 * @var $privileges
 * @var $groupmatrix
 */

$this->title = 'Užívatelia';

$this->registerJSFile('@web/assets/node_modules/switchery/dist/switchery.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/switchery/dist/switchery.min.css', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/dist/css/pages/other-pages.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/dist/css/pages/tab-page.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);

$confirmRemoval = 'Naozaj chcete zmazať?';

// helper: compute age (or null)
$computeAge = static function (?string $birthdate): ?int {
    if (!$birthdate) return null;
    $ts = strtotime($birthdate);
    if (!$ts) return null;
    $today = new DateTimeImmutable('today');
    $dob   = (new DateTimeImmutable())->setTimestamp($ts);
    return (int)$dob->diff($today)->y;
};
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center"><h4 class="text-themecolor"><?= Html::encode($this->title) ?></h4></div>
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
                        <a class="dropdown-item" href="/backoffice/users/add">Užívateľa</a>
                        <a class="dropdown-item" href="/backoffice/users/add-group">Skupinu</a>
                        <a class="dropdown-item" href="/backoffice/users/add-privilege">Funckiu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card rounded-5 card-shadow">
            <div class="card-body">
                <form method="post">
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#userlist" role="tab">
                                <span class="hidden-sm-up"><i class="mdi mdi-account"></i></span>
                                <span class="hidden-xs-down">Užívatelia</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#usergroups" role="tab">
                                <span class="hidden-sm-up"><i class="mdi mdi-account-multiple"></i></span>
                                <span class="hidden-xs-down">Skupiny</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#features" role="tab">
                                <span class="hidden-sm-up"><i class="ti-package"></i></span>
                                <span class="hidden-xs-down">Funkcie</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- USERS TAB -->
                        <div class="tab-pane active" id="userlist" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-sm dattable">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Meno</th>
                                                <th>Vek / ZZ</th>
                                                <th>Telefon</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Akcia</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($userlist as $user) :
                                                $fullName = trim(($user['name_first'] ?? '') . ' ' . ($user['name_last'] ?? ''));
                                                $age = $computeAge($user['birthdate'] ?? null);
                                                $isMinor = $age !== null && $age < 18;

                                                // guardians count (quick + safe)
                                                $gcount = 0;
                                                try {
                                                    $gcount = (int)Yii::$app->db->createCommand(
                                                        'SELECT COUNT(*) FROM user_guardian WHERE user_id = :id'
                                                    )->bindValue(':id', (int)$user['id'])->queryScalar();
                                                } catch (\Throwable $e) { /* ignore */ }

                                                $phone = trim((string)($user['phone'] ?? ''));
                                                $email = trim((string)($user['email'] ?? ''));
                                                ?>
                                                <tr<?= ((int)$user['status'] === 0) ? " class='text-muted'" : "" ?>>
                                                    <td><?= (int)$user['id'] ?></td>
                                                    <td><?= Html::encode($user['username']) ?></td>
                                                    <td><?= $fullName !== '' ? Html::encode($fullName) : '<span class="text-muted">—</span>' ?></td>
                                                    <td>
                                                        <?php if ($age === null): ?>
                                                            <span class="text-muted">—</span>
                                                        <?php else: ?>
                                                            <?php if ($isMinor): ?>
                                                                <span class="badge badge-warning">maloletý • <?= (int)$age ?></span>
                                                                <span class="badge badge-info" title="Počet zástupcov"><?= (int)$gcount ?></span>
                                                            <?php else: ?>
                                                                <span class="text-muted"><?= (int)$age ?></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($phone !== ''): ?>
                                                            <a href="tel:<?= Html::encode(preg_replace('/\s+/', '', $phone)) ?>">
                                                                <?= Html::encode($phone) ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">—</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($email !== ''): ?>
                                                            <a href="mailto:<?= Html::encode($email) ?>"><?= Html::encode($email) ?></a>
                                                        <?php else: ?>
                                                            <span class="text-muted">—</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <input
                                                            type="checkbox"
                                                            class="js-switch"
                                                            data-color="#26c6da"
                                                            data-secondary-color="#f62d51"
                                                            data-userid="<?= (int)$user['id'] ?>"
                                                            <?= ((int)$user['status'] === 10) ? ' checked' : '' ?>
                                                        >
                                                    </td>
                                                    <td>
                                                        <?php if ((int)$user['status'] !== 0) : ?>
                                                            <a
                                                                href="<?= Url::to(['users/edit', 'id' => (int)$user['id']]) ?>"
                                                                title="Edit"
                                                                style="color: black">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a
                                                                href="<?= Url::to() ?>"
                                                                title="Restore"
                                                                style="color: black">
                                                                <i class="mdi mdi-backup-restore"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                        <a class="dropdown-item" href="/backoffice/users/add">Užívateľa</a>
                        <a class="dropdown-item" href="/backoffice/users/add-group">Skupinu</a>
                        <a class="dropdown-item" href="/backoffice/users/add-privilege">Funckiu</a>
                    </div>
                </div>

                        <!-- GROUPS TAB -->
                        <div class="tab-pane" id="usergroups" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-sm dattable">
                                            <thead>
                                            <tr>
                                                <th>Názov</th>
                                                <th class="w-50">Popis</th>
                                                <th>Akcie</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($usergroups as $group) : ?>
                                                <tr>
                                                    <td><?= Html::encode($group['name']) ?></td>
                                                    <td class="w-50"><?= Html::encode($group['description']) ?></td>
                                                    <td>
                                                        <a
                                                            href="<?= Url::to(['users/edit-group', 'name' => $group['name']]) ?>"
                                                            title="Edit"
                                                            style="color: black">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FEATURES TAB -->
                        <div class="tab-pane" id="features" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12" style="overflow: auto">
                                    <form method="post" role="form" id="priv-form">
                                        <input
                                            type="hidden"
                                            name="<?= Yii::$app->request->csrfParam ?>"
                                            value="<?= Yii::$app->request->csrfToken ?>">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                            <tr>
                                                <th>Funkcia</th>
                                                <?php foreach ($usergroups as $group) {
                                                    echo "<th>".Html::encode($group['name'])."</th>";
                                                } ?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($privileges as $item) : ?>
                                                <tr>
                                                    <td>
                                                        <?= Html::encode($item['name']) ?>
                                                        <a href="<?= Url::to(['edit-privilege','id'=>$item['id']]) ?>">
                                                            <i class="fas fa-pencil-alt m-l-10" style="color: black"></i>
                                                        </a>
                                                    </td>
                                                    <?php foreach ($usergroups as $group) :
                                                        $checked = in_array($item['id'], $groupmatrix[$group['name']]) ? " checked" : ""; ?>
                                                        <td>
                                                            <input
                                                                type="checkbox"
                                                                data-priv="<?= (int)$item['id']?>"
                                                                data-usr="0"
                                                                data-group="<?= Html::encode($group['name']) ?>"
                                                                class="priv" <?= $checked ?>>
                                                        </td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> <!-- /.tab-content -->
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$js = <<<JS
$(function() {
    $('.dattable').DataTable({ order: [] });
});
$('.js-switch').each(function () {
    new Switchery($(this)[0], $(this).data());
});
$('.js-switch').change(function(){
    let c = $(this).is(':checked') ? 10 : 0;
    let i = $(this).data('userid');
    $.ajax({
        url: "/backoffice/users/ajax-change-status",
        dataType: "json",
        data: { iuser: i, istatus: c, {$csrf} },
        type: "post"
    }).done(function(res){
        if (res.status == 'error') {
            console.log(res.message);
        } else {
            $.toast({
                text: res.message,
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 2500,
                stack: 6
            });
        }
    });
});
$(".priv").on('click',function(){
    var g = $(this).data('group'),
        p = $(this).data('priv'),
        u = $(this).data('usr'),
        c = $(this).is(':checked') ? 1 : 0;
    $.ajax({
        url: '/backoffice/users/ajax-change-privilege',
        dataType: 'json',
        method: 'post',
        data: { group: g, priv: p, user: u, status: c },
        success: function(r){
            var icon = (r.status === 'ok') ? 'success' : 'error';
            $.toast({
                text: r.message,
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: icon,
                hideAfter: 2500,
                stack: 6
            });
        }
    });
});
JS;
$this->registerJS($js);

$css = <<<CSS
.vtabs { width: 100%; }
.tabs-vertical { width: 150px !important; }
.rounded-5 { border-radius: .5em!important; }
.card-shadow { box-shadow: lightgrey 3px 3px; }
.badge-warning { background-color:#ffc107; color:#212529; }
.badge-info { background-color:#17a2b8; }
CSS;
$this->registerCSS($css);
