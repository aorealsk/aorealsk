<?php
/**
 * @var $this
 * @var $offices
 * @var $trainees
 * @var $groups
 * @var $year
 */

use backend\assets\RealAsset;
use common\helpers\DateHelper;

$this->title = Yii::t('app','Reporty');
$this->registerJSFile('@web/js/bootstrap-multiselect.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/css/bootstrap-multiselect.min.css', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/dist/css/pages/tab-page.css?v=1.0',['depends' => RealAsset::class]);
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="get" role="form">

                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label"><?= Yii::t('app','Skupina'); ?></label>
                            <select name="grp" class="form-select" id="grp">
                                <option value=""><?= Yii::t('app','Vyberte...'); ?></option>
                                <?php foreach($groups as $group): ?>
                                    <option value="<?= $group->name ?>"><?= $group->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label"><?= Yii::t('app','Mesiac'); ?></label>
                            <select name="mon" id="" class="form-select">
                                <option value=""><?= Yii::t('app', 'Vyberte...') ?></option>
                                <?php foreach (range(1, 12) as $i): ?>
                                    <option value="<?= $i ?>"><?= DateHelper::getMonthText($i) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label"><?= Yii::t('app','Zamestnávateľ'); ?></label>
                            <select name="offi" id="office" class="form-select">
                                <option value=""><?= Yii::t('app','Zvoľte si zamestnávateľa'); ?></option>
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?= $office->id ?>"><?= $office->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label"><?= Yii::t('app','Užívatelia'); ?></label>
                            <select name="usr[]" class="form-select" multiple="multiple" id="users"></select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label"><?= Yii::t('app','Dátum podpisu'); ?></label>
                            <input type="date" name="sigdt" class="form-control" value="<?= $sigdt = $_GET['sigdt'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label class="form-label"><?= Yii::t('app','Hlavný inštruktor'); ?></label>
                            <select name="inst" class="form-select"></select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-secondary" type="submit"><?= Yii::t('app','Otvoriť'); ?></button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <?php if ($trainees): ?>
    <div class="card">
        <div class="card-body">
            <form role="form" method="post" action="/backoffice/trainee/create-reports">
                <div class="row mt-3">
                    <div class="col-12">
                        <ul class="nav nav-pills m-b-30">
                            <?php foreach($trainees['users'] as $id => $item): ?>
                            <li class="nav-item">
                                <?php
                                $active = $id === 0 ? ' active' : '';
                                ?>
                                <a href="#navpills-<?= $id+1 ?>" class="nav-link<?= $active ?>" data-toggle="tab" aria-expanded="false">
                                    <?= $item['name'] ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content br-n pn">

                            <?php foreach($trainees['users'] as $id => $item): ?>
                            <?php
                                $active = $id === 0 ? ' active' : '';
                            ?>
                            <div id="navpills-<?= $id+1 ?>" class="tab-pane<?= $active ?>">
                                <input type="hidden" name="Docs[<?= $id ?>][user][id]" value="<?= $item['id'] ?>">
                                <h5><?= Yii::t('app','Mesačný výkaz praktického vyučovania žiaka'); ?></h5>
                                <div class="table-responsive m-t-15">
                                    <table class="table table-sm table-stripe table-bordered xtab">
                                        <thead>
                                            <tr>
                                                <th><?= Yii::t('app','Dátum'); ?></th>
                                                <th><?= Yii::t('app','Od'); ?></th>
                                                <th><?= Yii::t('app','Do'); ?></th>
                                                <th><?= Yii::t('app','Spolu hodín'); ?></th>
                                                <th><?= Yii::t('app','Vykonávaná pracovná činnosť'); ?></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="x-0" class="xrow" data-id="0">
                                                <td><input type="date" name="Docs[<?= $id ?>][timesheet][0][date]" class="form-control"></td>
                                                <td><input type="text" name="Docs[<?= $id ?>][timesheet][0][from]" class="form-control"></td>
                                                <td><input type="text" name="Docs[<?= $id ?>][timesheet][0][to]" class="form-control"></td>
                                                <td><input type="text" name="Docs[<?= $id ?>][timesheet][0][total]" class="form-control"></td>
                                                <td><textarea name="Docs[<?= $id ?>][timesheet][0][activity]" cols="30" rows="3" class="form-control"></textarea></td>
                                                <td style="vertical-align: middle; text-align: center">
                                                    <button class="btn btn-success text-white xbut" type="button" data-id="0">+</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <h5 class="m-t-15"><?= Yii::t('app','Ročná dochádzka'); ?></h5>
                                <div class="table-responsive m-t-15">
                                    <table class="table table-sm table-stripe table-bordered xtab">
                                        <thead>
                                            <tr>
                                                <th rowspan="2"><?= Yii::t('app','Typ práce'); ?></th>
                                                <th colspan="4"><?= $year ?></th>
                                                <th colspan="6"><?= $year + 1 ?></th>
                                                <th rowspan="2"><?= Yii::t('app','Spolu'); ?></th>
                                            </tr>
                                            <tr class="text-center">
                                                <?php foreach (range(9,12) as $x): ?>
                                                    <th><?= DateHelper::getMonthText($x) ?></th>
                                                <?php endforeach; ?>
                                                <?php foreach (range(1,6) as $x): ?>
                                                    <th><?= DateHelper::getMonthText($x) ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?= Yii::t('app','Odpracované'); ?></td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">110</td>
                                            </tr>
                                            <tr>
                                                <td><?= Yii::t('app','Ospravedlnená'); ?></td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">110</td>
                                            </tr>
                                            <tr>
                                                <td>Neospravedlnená</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">110</td>
                                            </tr>
                                            <tr>
                                                <td>Nedoriešená</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">1</td>
                                                <td class="text-center">110</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <h5 class="m-t-15"><?= Yii::t('app','Hodnotiaci list žiaka na praktickom vyučovaní'); ?></h5>
                                <div class="table-responsive m-t-15">
                                    <table class="table table-sm table-stripe table-bordered xtab">
                                        <thead>
                                            <tr>
                                                <th><?= Yii::t('app','Oblasť hodnotenia'); ?></th>
                                                <th>1</th>
                                                <th>2</th>
                                                <th>3</th>
                                                <th>4</th>
                                                <th>5</th>
                                                <th><?= Yii::t('app','Poznámky k činnostiam'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $hodnotenie = [
                                                Yii::t('app','Odborné vedomosti žiaka k vykonávanej práci'),
                                                Yii::t('app','Hodnotenie praktických zručností žiaka pri práci'),
                                                Yii::t('app','Samostatnosť žiaka pri práci'),
                                                Yii::t('app','Kvalita vykonávanej práce žiaka'),
                                                Yii::t('app','Prístup a správanie žiaka k povinnostiam'),
                                                Yii::t('app','Prístup žiaka k pokynom inštruktora a vedúcich zamestnancov organizácie'),
                                                Yii::t('app','Dodržiavanie BOZP a PO žiakom'),
                                            ];
                                            foreach ($hodnotenie as $item):?>
                                            <tr>
                                                <td class="vmid"><?= $item ?></td>
                                                <td class="vmid text-center"><input type="radio" name=""></td>
                                                <td class="vmid text-center"><input type="radio" name=""></td>
                                                <td class="vmid text-center"><input type="radio" name=""></td>
                                                <td class="vmid text-center"><input type="radio" name=""></td>
                                                <td class="vmid text-center"><input type="radio" name=""></td>
                                                <td class="text-center"><textarea cols="30" rows="2" class="form-control" name=""></textarea></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <button class="btn btn-success text-white"><?= Yii::t('app','Stiahnúť všetky dokumenty'); ?></button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam ."':'". Yii::$app->request->getCsrfToken() ."'";
$css=<<<CSS
.xtab thead {
    background-color: #f0f0f0
}
.xtab thead tr {
    text-align:center;
}
.xtab tbody tr td > input,
.xtab tbody tr td > textarea {
    background-color: #f8efc0;
}
.vmid {
    vertical-align: middle;
}
CSS;
$this->registerCSS($css);

$js=<<<JS
$('#grp').on('change', function (){
   let s = $(this).val();
   $.ajax({
      url: '/backoffice/trainee/get-users',
        dataType: 'json',
        data: {
            group: s,
            {$csrf} 
        },
        type: 'post'
   })
   .done(function (r){
        if(r.status === 'ok') {
           $('#users')
                .empty()
                .append(r.user_list)
                .multiselect('destroy')
                .multiselect({
                    inheritClass: true,
                    buttonWidth: '100%',
                    buttonTextAlignment: 'left',
                    widthSynchronizationMode: 'always'
                });
        } else {
            alert(r.message);
        }  
   });
});

$('#users').multiselect({
    inheritClass: true,
    buttonWidth: '100%',
    buttonTextAlignment: 'left',
    widthSynchronizationMode: 'always'
});
JS;
$this->registerJs($js);