<?php
/** @var array $offices **/

use backend\assets\RealAsset;
use common\repositories\AccountsRepository;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Firemné účty');

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile(
    '@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',
    ['depends' => RealAsset::class]
);

?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row m-b-20">
        <div class="col-12">
            <a class="btn btn-info text-white" href="<?= Url::to(['/accounts/add']) ?>">
                <i class="fas fa-plus-circle"></i>&nbsp;<?= Yii::t('app', 'Pridať účet'); ?>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <?php foreach ($offices as $id => $office) : ?>
                        <li class="nav-item">
                            <a
                                href="#company<?= $id ?>"
                                class="nav-link<?= $id==0 ? ' active' : '' ?>"
                                data-toggle="tab"
                                role="tab"
                            >
                                <?= trim($office['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="tab-content">
                <?php foreach ($offices as $id => $office) : ?>
                    <div class="tab-pane<?= $id == 0 ? ' active': '' ?>" id="company<?= $id ?>" role="tabpanel">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm dattable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th><?= Yii::t('app', 'Banka') ?></th>
                                            <th>IBAN</th>
                                            <th><?= Yii::t('app','Mena') ?></th>
                                            <th><?= Yii::t('app', 'Otvorené') ?></th>
                                            <th><?= Yii::t('app', 'Uzavreté') ?></th>
                                            <th><?= Yii::t('app', 'Akcie') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $accounts = AccountsRepository::getAllByCompanyId($office['id']);
                                    ?>
                                    <?php foreach ($accounts as $account) : ?>
                                        <tr>
                                            <td><?= $account['id'] ?></td>
                                            <td><?= $account['bank_name'] ?></td>
                                            <td><?= $account['iban'] ?></td>
                                            <td><?= $account['currency'] ?></td>
                                            <td><?= $account['valid_from'] ?></td>
                                            <td><?= $account['valid_to'] ?></td>
                                            <td>
                                                <a href="<?= Url::to(['accounts/edit','id' => $account['id']]) ?>"
                                                   title="Edit" style="color: black">
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
                <?php endforeach; ?>
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
JS;

$this->registerJs($js);