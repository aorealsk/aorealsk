<?php

use yii\helpers\Url;
use backend\assets\RealAsset;

$this->title = Yii::t('app', 'School');

$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/dist/css/pages/other-pages.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/dist/css/pages/tab-page.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);

?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-10 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="card rounded-5 card-shadow w-100">
            <div class="card-body">
                <form method="post">
                    <div class="vtabs customvtab w-100">
                        <ul class="nav nav-tabs tabs-vertical" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#userlist" role="tab">
                                    <span class="hidden-sm-up"><i class="mdi mdi-account"></i></span>
                                    <span class="hidden-xs-down"><?= Yii::t('app', 'Osnovy') ?></span>
                                </a>
                            </li>
                        </ul>
                </form>
                <div class="tab-content">
                    <div class="row border-bottom pb-4">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Skupina</th>
                                            <th>Dátum</th>
                                            <th>Od</th>
                                            <th>Do</th>
                                            <th>Celkové hodiny</th>
                                            <th>Pracovná činnosť</th>
                                            <th>Vytvorené</th>
                                            <th>Akcie</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($osnovy as $osnova) { ?>
                                            <tr>
                                                <td>
                                                    <?= $osnova->id ?>
                                                </td>
                                                <td>
                                                    <?= $osnova->group_name ?>
                                                </td>
                                                <td>
                                                    <?= $osnova->date ?>
                                                </td>
                                                <td>
                                                    <?= $osnova->od ?>
                                                </td>
                                                <td>
                                                    <?= $osnova->do ?>
                                                </td>
                                                <td>
                                                    <?= $osnova->total_hours ?>
                                                </td>
                                                <td>
                                                    <?= $osnova->body ?>
                                                </td>
                                                <td>
                                                    <?= $osnova->created_at ?>
                                                </td>
                                                <td>
                                                    <a href="<?= Url::to(['edit', 'id' => $osnova['id']]) ?>" title="<?= Yii::t('app', 'Upraviť'); ?>" style="color: black"><i class="fas fa-pencil-alt"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <form method="POST" class="border-bottom pb-4 mt-4" id="upload" enctype="multipart/form-data">
                        <div class="tab-pane active" id="userlist" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Nahranie súboru') ?></label>
                                    <input type="file" id="upload-csv" name="csv" class="form-control">
                                </div>
                            </div>
                            <button class="btn btn-secondary" type="submit">
                                <i class="fas fa-upload"></i>
                                Nahrať
                            </button>
                        </div>
                    </form>
                    <form method="POST" class="mt-3" id="add-osnova">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label class="control-label"><?= Yii::t('app', 'Skupina') ?></label>
                                <select name="Group[group_name]" id="groups" class="form-control dropdown">
                                    <option value="0">Vyberte...</option>
                                    <?php foreach ($groups as $group) {
                                    ?>
                                        <option value="<?= $group->name ?>"><?= $group->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div id="row-0">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Dátum') ?></label>
                                    <input type="date" id="date0" name="0[date]" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Od') ?></label>
                                    <input type="time" id="od0" name="0[od]" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Do') ?></label>
                                    <input type="time" id="do0" name="0[do]" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Pracovná činnosť') ?></label>
                                    <textarea class="form-control" id="body0" name="0[body]" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Dátum') ?></label>
                                    <input type="date" id="date1" name="1[date]" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Od') ?></label>
                                    <input type="time" id="od1" name="1[od]" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Do') ?></label>
                                    <input type="time" id="do1" name="1[do]" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Pracovná činnosť') ?></label>
                                    <textarea class="form-control" id="body1" name="1[body]" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Dátum') ?></label>
                                    <input type="date" id="date2" name="2[date]" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Od') ?></label>
                                    <input type="time" id="od2" name="2[od]" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Do') ?></label>
                                    <input type="time" id="do2" name="2[do]" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Pracovná činnosť') ?></label>
                                    <textarea class="form-control" id="body2" name="2[body]" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Dátum') ?></label>
                                    <input type="date" id="date3" name="3[date]" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Od') ?></label>
                                    <input type="time" id="od3" name="3[od]" class="form-control">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Do') ?></label>
                                    <input type="time" id="do3" name="3[do]" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="control-label"><?= Yii::t('app', 'Pracovná činnosť') ?></label>
                                    <textarea class="form-control" id="body3" name="3[body]" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="btn-row">
                            <div class="col-md-6 form-group">
                                <button class="btn btn-info mr-2" type="submit">
                                    Uložiť
                                </button>
                                <button class="btn btn-success" id="add-item" type="submit">
                                    Pridať riadok
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<?php
$js = <<<JS
    $(function() {
        $('.datatable').DataTable({
            order: []
        });
    });

    let counter = parseInt($('#item').length,10) -1;
    let increment = 0;
    $('#add-item').on('click',function(e){
    e.preventDefault();
    let el = $('#row-0');
    counter ++;
    console.log(counter)
    increment = increment + 4;
    el.clone().attr('id','row-' + (counter + 1 )).insertAfter('#row-' + counter);
    console.log(increment)
    for(let i = 0; i < 4; i++)
    {
        $('#row-'+(counter + 1)).find('#date' + (0 + i)).attr('name', (increment + i) +'[date]');
        $('#row-'+(counter + 1)).find('#date' + (0 + i)).attr('id', 'date' + (increment + i));
        $('#row-'+(counter + 1)).find('#od' + (0 + i)).attr('name',(increment + i) +'[od]');
        $('#row-'+(counter + 1)).find('#od' + (0 + i)).attr('id', 'od' +(increment + i));
        $('#row-'+(counter + 1)).find('#do' + (0 + i)).attr('name',(increment + i) +'[do]');
        $('#row-'+(counter + 1)).find('#do' + (0 + i)).attr('id', 'do' +(increment + i));
        $('#row-'+(counter + 1)).find('#body' + (0 + i)).attr('name',(increment + i) +'[body]');
        $('#row-'+(counter + 1)).find('#body' + (0 + i)).attr('id', 'body' +(increment + i));
    }
});

    updateDate = function (fieldToUpdate, currentDate)
    {
        let currentTime = $('#' + currentDate);
        let currentYear = currentTime.val().split('-')[0]
        let currentMonth = currentTime.val().split('-')[1]
        let currentDay = currentTime.val().split('-')[2]
        
        let sucet = parseInt(currentDay) + 7

        if(currentMonth == 2 )
        {
            if(sucet > 29)
            {
                sucet = sucet - 28 ;      
                currentMonth = parseInt(currentMonth) + 1;
                currentMonth = "" + 0 + currentMonth;
            }
            setDateValue(sucet, currentMonth, currentYear, fieldToUpdate)
        } else if((currentMonth == 4 || currentMonth == 6 || currentMonth == 9 || currentMonth == 11) )
        {
            if(sucet > 31) 
            {
                sucet = sucet - 30 ;
                currentMonth = parseInt(currentMonth) + 1;
                currentMonth = "" + 0 + currentMonth;
            }
            setDateValue(sucet, currentMonth, currentYear, fieldToUpdate)
        }
        else{
            if(sucet >= 32)
            {
                sucet = sucet - 31 ;
                currentMonth = parseInt(currentMonth) + 1;  
                currentMonth = "" + 0 + currentMonth;
            }
            setDateValue(sucet, currentMonth, currentYear, fieldToUpdate)
        }
    }
    setDateValue = function(updatedDay, currentMonth, currentYear, fieldToUpdate)
        {
            if(updatedDay < 10)
            {
                updatedDay = "" + 0 + updatedDay
            }
            
            let timeToUpdate = $('#' + fieldToUpdate);
            timeToUpdate.val(currentYear + '-' + currentMonth + '-' + updatedDay)
        }
  $('#date0').on('change', function () {
    updateDate('date1', 'date0')
    updateDate('date2', 'date1')
    updateDate('date3', 'date2')
  })

  $('#od0').on('change', function () {
        $('')
        let timeFieldOd = $('#od0');
        $('#od1').val(timeFieldOd.val())
        $('#od2').val(timeFieldOd.val())
        $('#od3').val(timeFieldOd.val())
  })

  $('#do0').on('change', function () {
        let timeFielDo = $('#do0');
        $('#do1').val(timeFielDo.val())
        $('#do2').val(timeFielDo.val())
        $('#do3').val(timeFielDo.val())
  }) 

$('#upload').on('submit', function(e){
    e.preventDefault();
    $.ajax({
        url: "/backoffice/school/upload-file",
        dataType: "json",
        data: new FormData(this),
        type: "post",
        processData: false,
        contentType: false,
        success: function(data)
        {
            console.log(data.rows)
            let firstDateInput = $('#date0');
            let secondDateInput = $('#date1');
            let thirdDateInput = $('#date2');
            let fourthDateInput = $('#date3');
            
            firstDateInput.val(data.rows[0][0])
            secondDateInput.val(data.rows[1][0])
            thirdDateInput.val(data.rows[2][0])
            fourthDateInput.val(data.rows[3][0])

            let firstOdInput = $('#od0');
            let secondOdInput = $('#od1');
            let thirdOdInput = $('#od2');
            let fourthOdInput = $('#od3');
            
            firstOdInput.val(data.rows[0][1])
            secondOdInput.val(data.rows[1][1])
            thirdOdInput.val(data.rows[2][1])
            fourthOdInput.val(data.rows[3][1])

            let firstDoInput = $('#do0');
            let secondDoInput = $('#do1');
            let thirdDoInput = $('#do2');
            let fourthDoInput = $('#do3');
            
            firstDoInput.val(data.rows[0][2])
            secondDoInput.val(data.rows[1][2])
            thirdDoInput.val(data.rows[2][2])
            fourthDoInput.val(data.rows[3][2])

            let firstBodyInput = $('#body0');
            let secondBodyInput = $('#body1');
            let thirdBodyInput = $('#body2');
            let fourthBodyInput = $('#body3');
            
            firstBodyInput.val(data.rows[0][3])
            secondBodyInput.val(data.rows[1][3])
            thirdBodyInput.val(data.rows[2][3])
            fourthBodyInput.val(data.rows[3][3])
        },
        error: function(e)
        {
            console.log(e)
        },
    })
})

JS;
$this->registerJS($js);
$css = <<<CSS
    .rounded-5 {
        border-radius: .5em!important;
    }
    .card-shadow {
        box-shadow: lightgrey 3px 3px;
    }
CSS;
$this->registerCSS($css);
