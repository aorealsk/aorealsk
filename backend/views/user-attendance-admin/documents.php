<?php

/**
 * @var $privileges
 * @var $offices
 */

use backend\assets\RealAsset;
use common\helpers\DateHelper;

$this->registerJSFile('@web/js/bootstrap-multiselect.js', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/js/bootstrap-multiselect.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/js/bootstrap-multiselect.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/css/bootstrap-multiselect.css', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/css/bootstrap-multiselect.min.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);

$this->title = 'Dokumenty';
?>
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-12 align-self-center">
                <h4 class="text-themecolor"><?= $this->title ?></h4>
            </div>
        </div>

        <div class="card rounded-5 card-shadow w-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <form method="POST" role="form" id="form">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                   value="<?= Yii::$app->request->csrfToken ?>">
                            <h4 class="mb-4"><?= Yii::t('app', 'Základné údaje') ?></h4>
                            <div class="row form-group">
                                <div class="col-12">
                                    <label class="form-label"><?= Yii::t('app', 'Skupina') ?></label>
                                    <select name="group_name" class="form-control form-select" id="privileges" required>
                                        <option value=""><?= Yii::t('app', 'Vyberte...') ?></option>
                                        <?php
                                        foreach ($privileges as $privilage) {
                                            ?>
                                            <option value='<?= $privilage->group_name ?>'><?= $privilage->group_name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-12">
                                    <label class="form-label"><?= Yii::t('app', 'Užívatelia') ?></label>
                                    <select name="user" id='users' class="form-control form-select" multiple="multiple"
                                            required>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-12">
                                    <label class="form-label"><?= Yii::t('app', 'Mesiac') ?></label>
                                    <select name="month" class="form-control form-select" id="month" required>
                                        <option value=""><?= Yii::t('app', 'Vyberte...') ?></option>
                                        <?php
                                        foreach (range(1, 12) as $i) {
                                            ?>
                                            <option value="<?= $i ?>"><?= DateHelper::getMonthText($i) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <h4 class="mb-4"><?= Yii::t('app', 'Doplňujúce údaje') ?></h4>
                            <div class="row form-group">
                                <div class="col-12">
                                    <label class="form-label"><?= Yii::t('app', 'Zamestnávateľ') ?></label>
                                    <select name="employer" class="form-control form-select" id="employer" required>
                                        <option value=""><?= Yii::t('app', 'Vyberte...') ?></option>
                                        <?php
                                        foreach ($offices as $office) {
                                            ?>
                                            <option value="<?= $office->id ?>"
                                                    data-address="<?= $office->getFullAddress() ?>"><?= $office->name ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-12">
                                    <label class="form-label"><?= Yii::t('app', 'Hlavný inštruktor') ?></label>
                                    <select id="instructor" class="form-control form-select required">
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label"><?= Yii::t('app', 'Dátum podpisu'); ?></label>
                                    <input type="date" name="doc_date" id="doc-date" class="form-control">
                                </div>
                            </div>
                            <div class="col mt-3">
                                <button type="submit" id="submit"
                                        class="btn btn-info text-white"><?= Yii::t('app', 'Generovať') ?></button>
                                <a href="/backoffice/user-attendance-admin" class="btn btn-danger text-white"><i
                                            class="fas fa-arrow-alt-circle-left"></i>&nbsp;Späť</a>
                            </div>
                        </form>
                    </div>
                    <div class="col-8">
                        <h4 class="mb-3">
                            <?= Yii::t('app', 'Vygenerované dokumenty') ?>
                        </h4>
                        <div class="download-form">
                            <ul class="pdf-container" id="pdfs">
                                <ul>
                        </div>
                        <div class="download-all-form">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card rounded-5 card-shadow w-100">
            <form method="post" id="view-pdf-card">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                       value="<?= Yii::$app->request->csrfToken ?>">
            </form>
        </div>
    </div>
<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";

$css = <<<CSS
    .rounded-5 {
        border-radius: .5em!important;
    }
    .card-shadow {
        box-shadow: lightgrey 3px 3px;
    }
    .pdf{
        color:red;
    }
    .pdf-container{
        list-style-type: none;
        padding: 0 0 0 1rem !important;
    }
    .btn-svg{
        padding: 0 !important;
    }
    .dropdown-item.active, .dropdown-item:active {
        background-color:rgb(13,113,253);
    }
    .form-check-input:checked {
        background-color:rgb(13,113,253);
    }
    .text-left {
        text-align: left;
    }
    .fillit {
        background-color: #fdffe3;
    }
CSS;
$this->registerCSS($css);

$js = <<<JS

$('#users').multiselect({
    inheritClass: true,
    buttonWidth: '100%',
    buttonTextAlignment: 'left',
    widthSynchronizationMode: 'always'
});

$('#privileges').on('change', function() {
        var selectedOption = $(this).find(":selected").val();
        $.ajax({
            url: "/backoffice/user-attendance-admin/change-group-name",
            dataType: "json",
            data: { 
                data: selectedOption
                },
            type: "post",
            error: function(e)
            {
                console.log(e)
            },
        })
        .done(function(res){ 
            $('#users')
                .empty()
                .append(res)
                .multiselect('destroy')
                .multiselect({
                    inheritClass: true,
                    buttonWidth: '100%',
                    buttonTextAlignment: 'left',
                    widthSynchronizationMode: 'always'
                });
        });     
    });

$('#employer').change(function () {
    let x = $(this).val();
    $.ajax({
        url:"/backoffice/user-attendance-admin/get-employees",
        dataType:"json",
        data: { eid: x,{$csrf}},
        type: "post"
    }).done(function(res){
        if (res.status === 'ok') {
            $('#instructor').empty();
            $.each(res.users, function (i,v) {
               $('#instructor').append('<option value="'+v.value+'" data-id="'+v.id+'">'+v.value+'</option>');
            });
        } else {
            alert('Something went wrong!!!');
        }
    });
});

$(document).on('change', '#doc-date', function (e){
    $(document).find('input[name="date"]').val(e.target.value)
});

$(document).on('change', '#instructor', function (e){
   $(document).find('input[name="instructor"]').val(e.target.value); 
});

$(document).on('change', '#employer', function (e){
   $(document).find('input[name="employer_name"]').val(e.target.options[e.target.selectedIndex].text);
   $(document).find('input[name="employer_address"]').val(e.target.options[e.target.selectedIndex].getAttribute('data-address'));
});

$('#form').on('submit', function(e) {
    e.preventDefault();
    let form = $('#form');
    let privilege = form.find('#privileges');
    let user = form.find('#users');
    let month = form.find('#month');
    $.ajax({
        url: "/backoffice/user-attendance-admin/form-submit",
        dataType: "json",
        data: { 
            group_name: privilege.val(),
            user: user.val(),
            month: month.val()
            },
        type: "post",
        error: function(e)
        {
            console.log(e)
        }
    })
    .done(function(res){
        if (res.status == 'error') {
            alert(res.message);
        } 
        else {  
            $('.download-all-form').empty().append(res.downloadAllForm);
            $('#pdfs').empty().append(res.pdfs);
        }
    });     
});

$(document).on('click', '.view-pdf', function () {
    let templateId = $(this).data('template');
    let month = $(this).data('month');
    let userId = $(this).data('user');
    $.ajax({
        url: "/backoffice/user-attendance-admin/view-file",
        'dataType': 'json',
        'data' : {
            templateId: templateId,
            userId: userId,
            month: month,
        }
    }).done(function (res) {
        $('#view-pdf-card').empty().append('<div class="card-body" id="document-form">' + '<input type="hidden" name="template_id" value="'+templateId+'">' +'<input type="hidden" name="user_id" value="'+userId+'">'+ '<input type="hidden" name="month" value="'+month+'">'+ res.body + '</div>')
        $('#document-form').append('<button type="submit" class="btn btn-success text-white">Uložiť zmeny</button>')

        let docDate = $(document).find('input[id="doc-date"]').val();
        $(document).find('span[id="input.date"]').replaceWith('<input type="date" class="form-control ml-2 fillit" name="date" value="'+docDate+'">');
        
        let employer=$(document).find('select[id="employer"]').find(':selected');
        $(document).find('span[id="employer_name"]').replaceWith('<input name="employer_name" class="form-control fillit" value="'+employer.text()+'">');
        $(document).find('span[id="employer_address"]').replaceWith('<input name="employer_address" class="form-control fillit" value="'+employer.data('address')+'">');
        
        $(document).find('span[id="znamka"]').replaceWith('<input name="znamka" class="form-control fillit">')
        $(document).find('span[id="poznamky"]').replaceWith('<textarea name="poznamky" class="form-control fillit"></textarea>')
        
        let mainInstructor = $(document).find('select[id="instructor"]').val();
        $(document).find('span[id="instructor"]').replaceWith('<input type="text" name="instructor" class="form-control fillit" value="'+mainInstructor+'">');
   
        for(let i = 1; i <= 5; i++) {
            $(document).find('span[id="OdborneVedomosti['+i+']"]').replaceWith('<input type="radio" name="OdborneVedomosti" value="' +i+'">');
            $(document).find('span[id="PraktickeVedomosti['+i+']"]').replaceWith('<input type="radio" name="PraktickeVedomosti" value="' +i+'">');
            $(document).find('span[id="Samostatnost['+i+']"]').replaceWith('<input type="radio" name="Samostatnost" value="' +i+'">');
            $(document).find('span[id="KvalitaPrace['+i+']"]').replaceWith('<input type="radio" name="KvalitaPrace" value="' +i+'">');
            $(document).find('span[id="Povinosti['+i+']"]').replaceWith('<input type="radio" name="Povinosti" value="' +i+'">');
            $(document).find('span[id="Pokyny['+i+']"]').replaceWith('<input type="radio" name="Pokyny" value="' +i+'">');
            $(document).find('span[id="BOZP['+i+']"]').replaceWith('<input type="radio" name="BOZP" value="' +i+'">');
       }
        $(document).find('span[id="OdborneVedomostiInput"]').replaceWith('<input type="text" class="form-control fillit" name="OdborneVedomostiInput">');
        $(document).find('span[id="PraktickeVedomostiInput"]').replaceWith('<input type="text" class="form-control fillit" name="PraktickeVedomostiInput">');
        $(document).find('span[id="SamostatnostInput"]').replaceWith('<input type="text" class="form-control fillit" name="SamostatnostInput">');
        $(document).find('span[id="KvalitaPraceInput"]').replaceWith('<input type="text" class="form-control fillit" name="KvalitaPraceInput">');
        $(document).find('span[id="PovinostiInput"]').replaceWith('<input type="text" class="form-control fillit" name="PovinostiInput">');
        $(document).find('span[id="PokynyInput"]').replaceWith('<input type="text" class="form-control fillit" name="PokynyInput">');
        $(document).find('span[id="BOZPInput"]').replaceWith('<input type="text" class="form-control fillit" name="BOZPInput">');
        
        for (let i=0; i < 4; i++) {
            $(document).find('span[id="datum_'+i+'"]').replaceWith('<input type="date" class="form-control fillit" name="datum_'+i+'">');
            $(document).find('span[id="od_'+i+'"]').replaceWith('<input type="text" class="form-control fillit" name="od_'+i+'">');
            $(document).find('span[id="do_'+i+'"]').replaceWith('<input type="text" class="form-control fillit" name="do_'+i+'">');
            $(document).find('span[id="hodiny_'+i+'"]').replaceWith('<input type="text" class="form-control fillit" name="hodiny_'+i+'">');
            $(document).find('span[id="cinnost_'+i+'"]').replaceWith('<textarea class="form-control fillit" name="cinnost_'+i+'"></textarea>');
        }
        $(document).find('span[id="hodiny"]').replaceWith('<input type="text" class="form-control fillit" name="hodiny">');
    });
});

$('#view-pdf-card').on('submit', function (e) {
    e.preventDefault();
    let dateValue =  $(document).find('input[type="date"]').val();
    let templateId =  $(this).find('input[name="template_id"]').val();
    let userId =  $(this).find('input[name="user_id"]').val();
    let month =  $(this).find('input[name="month"]').val();
    let odborneVedomostiZnamka = $(document).find('input[name="OdborneVedomosti"]:checked').val();
    let odborneVedomostiInput = $(document).find('input[name="OdborneVedomostiInput"]').val();
    let praktickeVedomostiZnamka = $(document).find('input[name="PraktickeVedomosti"]:checked').val();
    let praktickeVedomostiInput = $(document).find('input[name="PraktickeVedomostiInput"]').val();
    let kvalitaPrace = $(document).find('input[name="KvalitaPrace"]:checked').val();
    let kvalitaPraceInput = $(document).find('input[name="KvalitaPraceInput"]').val();
    let povinosti = $(document).find('input[name="Povinosti"]:checked').val();
    let povinostiInput = $(document).find('input[name="PovinostiInput"]').val();
    let pokyny = $(document).find('input[name="Pokyny"]:checked').val();
    let pokynyInput = $(document).find('input[name="PokynyInput"]').val();
    let bozp = $(document).find('input[name="BOZP"]:checked').val();
    let bozpInput = $(document).find('input[name="BOZPInput"]').val();
    let samostatnost = $(document).find('input[name="Samostatnost"]:checked').val();
    let samostatnostInput = $(document).find('input[name="SamostatnostInput"]').val();
    let instructor = $(document).find('input[name="instructor"]').val();
    let znamka = $(document).find('input[name="znamka"]').val();
    let poznamky = $(document).find('textarea[name="poznamky"]').val();
    let employerName = $(document).find('input[name="employer_name"]').val();
    let employerAddress = $(document).find('input[name="employer_address"]').val();
    let datum0 = $(document).find('input[name="datum_0"]').val();
    let datum1 = $(document).find('input[name="datum_1"]').val();
    let datum2 = $(document).find('input[name="datum_2"]').val();
    let datum3 = $(document).find('input[name="datum_3"]').val();
    let od0 = $(document).find('input[name="od_0"]').val();
    let od1 = $(document).find('input[name="od_1"]').val();
    let od2 = $(document).find('input[name="od_2"]').val();
    let od3 = $(document).find('input[name="od_3"]').val();
    let do0 = $(document).find('input[name="do_0"]').val();
    let do1 = $(document).find('input[name="do_1"]').val();
    let do2 = $(document).find('input[name="do_2"]').val();
    let do3 = $(document).find('input[name="do_3"]').val();
    let cinnost0 = $(document).find('textarea[name="cinnost_0"]').val();
    let cinnost1 = $(document).find('textarea[name="cinnost_1"]').val();
    let cinnost2 = $(document).find('textarea[name="cinnost_2"]').val();
    let cinnost3 = $(document).find('textarea[name="cinnost_3"]').val();
    let hodiny = $(document).find('input[name="hodiny"]').val();
    let hodiny0 = $(document).find('input[name="hodiny_0"]').val();
    let hodiny1 = $(document).find('input[name="hodiny_1"]').val();
    let hodiny2 = $(document).find('input[name="hodiny_2"]').val();
    let hodiny3 = $(document).find('input[name="hodiny_3"]').val();
    
    $.ajax({
        url: "/backoffice/user-attendance-admin/handle-document-submit",
        dataType: "json",
        data: {
            date: dateValue,
            templateId: templateId,
            odborneVedomostiZnamka: odborneVedomostiZnamka ? odborneVedomostiZnamka : '',
            odborneVedomostiInput: odborneVedomostiInput ? odborneVedomostiInput : '',
            praktickeVedomostiZnamka: praktickeVedomostiZnamka ? praktickeVedomostiZnamka : '',
            praktickeVedomostiInput: praktickeVedomostiInput ? praktickeVedomostiInput : '',
            kvalitaPrace: kvalitaPrace ? kvalitaPrace : '',
            kvalitaPraceInput: kvalitaPraceInput ? kvalitaPraceInput : '',
            povinosti: povinosti ? povinosti : '',
            povinostiInput: povinostiInput ? povinostiInput : '',
            pokyny: pokyny ? pokyny : '',
            pokynyInput: pokynyInput ? pokynyInput : '',
            bozp: bozp ? bozp : '',
            bozpInput: bozpInput ? bozpInput : '',
            samostatnost: samostatnost ? samostatnost : '',
            samostatnostInput: samostatnostInput ? samostatnostInput : '',
            instructor: instructor ? instructor : '',
            znamka: znamka ? znamka : '',
            poznamky: poznamky ? poznamky : '',
            userId: userId,
            month: month,
            employerName: employerName ? employerName : '',
            employerAddress: employerAddress ? employerAddress : '',
            datum0: datum0 ? datum0 : '',
            datum1: datum1 ? datum1 : '',
            datum2: datum2 ? datum2 : '',
            datum3: datum3 ? datum3 : '',
            do0: do0 ? do0 : '',
            do1: do1 ? do1 : '',
            do2: do2 ? do2 : '',
            do3: do3 ? do3 : '',
            od0: od0 ? od0 : '',
            od1: od1 ? od1 : '',
            od2: od2 ? od2 : '',
            od3: od3 ? od3 : '',
            cinnost0: cinnost0 ? cinnost0 :'',
            cinnost1: cinnost1 ? cinnost1 :'',
            cinnost2: cinnost2 ? cinnost2 :'',
            cinnost3: cinnost3 ? cinnost3 :'',
            hodiny: hodiny ? hodiny : '',
            hodiny0: hodiny0 ? hodiny0 : '',
            hodiny1: hodiny1 ? hodiny1 : '',
            hodiny2: hodiny2 ? hodiny2 : '',
            hodiny3: hodiny3 ? hodiny3 : ''
        },
        type:"POST",
        error: function(e)
        {
            console.log(e)
        },
        success: function (r){
             $.toast({
                //heading: 'Welcome to Material Pro admin',
                text: 'Dokument bol úspešne uložený',
                position: 'top-right',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 2500, 
                stack: 6
            });
        }
    });
});

JS;
$this->registerJS($js);
