<?php

/**
 * @var $offices
 * @var $clientId
 * @var $templates
 */

use backend\assets\RealAsset;
use yii\helpers\Url;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);
$this->registerJSFile("https://code.jquery.com/ui/1.13.2/jquery-ui.js", ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);
$this->title = 'Dokumenty'
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-10 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h4><?= Yii::t('app','Dokumenty na stiahnutie'); ?></h4>
            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm dattable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th></th>
                                <th><?= Yii::t('app', 'Názov dokumentu'); ?></th>
                                <th>Akcia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($templates as $id => $template) {
                            ?>
                                <tr>
                                    <form action=<?= Url::to('download-file') ?> method="post" id="docs">
                                        <input id="form-token" type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>" />
                                        <input type="hidden" name="templateId" value=<?= $id ?>>
                                        <input type="hidden" name="clientId" value=<?= $clientId ?>>
                                        <td align="center">
                                            <input type="checkbox" name="" id="" checked>
                                        </td>
                                        <td></td>
                                        <td>
                                            <?= $template ?>
                                        </td>
                                        <td>
                                            <button type="submit" id="download-doc" class="btn btn-svg"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                                                </svg>
                                            </button>
                                            <input id="form-token" type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>" />
                                            <button type="button" id="view-file" class="btn" data-client=<?= $clientId ?> data-template=<?= $id ?>>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn view-temp" data-client="<?= $clientId ?>" data-templateid="<?= $id ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <h4 class="card-title mb-3"><?= Yii::t('app', 'Doplňujúce informácie ku dokumentom') ?></h4>
            <div class="form-group row">
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Miesto podpisu') ?></label>
                    <input type="text" class="form-control ml-2" name="parent-town" placeholder="(V) Bratislave, Senci, Poprade ...">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Dátum podpisu') ?></label>
                    <input type="date" class="form-control ml-2" name="parent-month">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Dátum založenia firmy') ?></label>
                    <input type="date" class="form-control ml-2" name="incorporation_date">
                </div>
            </div>
            <h4 class="card-title mb-3 mt-5"><?= Yii::t('app', 'Poskytovateľ') ?></h4>
            <div class="form-group row">
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Poskytovateľ') ?></label>
                    <select name="office" class="form-select form-control">
                        <option value="">Vyberte</option>
                        <?php foreach ($offices as $office) {
                            ?>
                            <option value=<?= $office->id ?> data-address="<?= $office->address ?>" data-town="<?= $office->town ?>" data-zip="<?= $office->zip ?>" data-ico="<?= $office->ico ?>" data-vlozka="<?= $office->registered ?>" data-email="<?= $office->email ?>">
                                <?= $office->name ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Zástupca poskytovateľa') ?></label>
                    <input type="text" class="form-control ml-2" name="provider_deputy">
                </div>

            </div>
            <h4 class="card-title mb-3 mt-5"><?= Yii::t('app','Údaje o advokátovy'); ?></h4>
            <div class="form-group row" style="margin-top: 25px;">
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Meno advokáta') ?></label>
                    <input type="text" class="form-control lawyer_name" name="lawyer_name">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Residencia advokáta') ?></label>
                    <input type="text" class="form-control" name="lawyer_residence">
                </div>
            </div>
            <div class="form-group row" style="margin-top: 25px;">
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Registračné číslo adovkáta') ?></label>
                    <input type="text" class="form-control" name="lawyer_registration_number">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'IČO advokáta') ?></label>
                    <input type="text" class="form-control" name="lawyer_ico">
                </div>
            </div>
            <div class="form-group row" style="margin-top: 25px;">
                <div class="col-md-3">
                    <label class="form-label"><?= Yii::t('app', 'Email adovkáta') ?></label>
                    <input type="email" class="form-control" name="lawyer_email">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-12">
                    <form action=<?= Url::to('download-all') ?> method="post">
                        <input id="form-token" type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>" />
                        <input type="hidden" name="clientId" value=<?= $clientId ?>>
                        <?php
                        foreach ($templates as $id => $template) {
                            echo "<input type='hidden' name='templateId[]' value=$id>";
                        }
                        ?>
                        <button type="submit" class="btn btn-info text-white">
                            Stiahnuť všetky dokumenty
                        </button>
                        <a href="/backoffice/services" class="btn btn-danger test"><i class="fas fa-arrow-alt-circle-left"></i>&nbsp;Späť</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card" id="show-view-file">
    </div>
</div>

<?php
$csrf = "'" . Yii::$app->request->csrfParam ."':'". Yii::$app->request->getCsrfToken() ."'";
$js = <<<JS
    $(function() {
        $('.dattable').DataTable({
            order: []
        });
    });

    $('.view-temp').click(function(){
        let cid = $(this).data('client');
        let tid = $(this).data('templateid');
        $.ajax({
            url: '/backoffice/services/view-template',
            dataType: 'json',
            data:{tid:tid,cid:cid,{$csrf}},
            type: 'post'
        }).done(function(res){
            $('#show-view-file').empty().append(res.view_content);
        });
    });
    
    $(document).on('click','#save-template',function(){
        let x = new Array();

        $.each($('.doc-item'),function(i,v){
            let itm = $(v).data('item');
            let val = $(v).val();
            x.push({it:itm, val: val});
        }); 
        $.ajax({
            url: '/backoffice/services/save-template',
            dataType: 'json',
            type: 'post',
            data: {
                it: x,
                {$csrf}
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
            },
            error: function(r){
                $.toast({
                    //heading: 'Welcome to Material Pro admin',
                    text: 'Uloženie dokumentu zlyhalo',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 2500, 
                    stack: 6
                });
            }
        });
    });

    $(document).find('button[id="view-file"]').on('click', function () {
       let clientId = $(this).data('client')
        let templateId = $(this).data('template')
        $.ajax({
            url: "/backoffice/services/view-file",
            dataType: "json",
            data: {templateId: templateId, clientId: clientId}
        }).done(function (res) {
            $('#show-view-file').empty().append('<div class="card-body">'+ res.content+'<button type="button" style="margin-top: 1rem;" id="save-changes" class="btn btn-success text-white">Uložiť zmeny</button></div>')
            $(document).find('span[id="input.town"]').
            replaceWith('<div class="col-sm-2 d-flex align-items-center">V<input type="text" class="form-control ml-2" name="town"></div>')
            $(document).find('span[id="input.date"]').
            replaceWith('<div class="col-sm-2 d-flex align-items-center">dňa<input type="date" class="form-control ml-2" name="date"></div>')
            $(document).find('span[id="select.client_personal_info_full_name"]').
            replaceWith('<div class="row" style="margin-left: 0 !important;"> <div> Meno a priezvisko</div> <div class="col-md-6"> <select name="customer_full_name" class="form-select form-control client-info"><option>Vyberte...</option></select></div></div>')
            
            $(document).find('p[id="splnomocnenec_meno"]').
            replaceWith('<div class="row text"> <div> Meno a priezvisko</div> <div class="col-md-6"> <input name="splnomocnenec" class="form-control"></div></div>')
             
            $(document).find('p[id="selected_ssn"]').
            replaceWith('<div class="row text" style="margin-top:10px;"> <div> Rodné číslo:</div> <div class="col-md-6"> <input name="splnomocnenec_ssn" class="form-control"></div></div>')

            $(document).find('p[id="selected_birth"]').
            replaceWith('<div class="row text" style="margin-top:10px;"> <div> Narodený:</div> <div class="col-md-6"> <input name="splnomocnenec_birth" type="date" class="form-control"></div></div>')

            $(document).find('p[id="selected_full_address"]').
            replaceWith('<div class="row text" style="margin-top:10px;"> <div> Adresa:</div> <div class="col-md-6"> <input name="splnomocnenec_address" class="form-control"></div></div>')

            $(document).find('p[id="selected_address"]').
            replaceWith('<div class="row text" style="margin-top:10px;"> <div> Adresa:</div> <div class="col-md-6"> <input name="splnomocnenec_doc_address" class="form-control"></div></div>')

            $(document).find('p[id="selected_meno"]').
            replaceWith('<div class="row text" style="margin-top:10px;"> <div> Meno a priezvisko:</div> <div class="col-md-6"> <input name="splnomocnenec_doc_name" class="form-control"></div></div>')

            $(document).find('p[id="selected_zip"]').
            replaceWith('<div class="row text" style="margin-top:10px;"> <div>PSČ:</div> <div class="col-md-6"> <input name="splnomocnenec_doc_zip" class="form-control"></div></div>')

            $(document).find('p[id="selected_town"]').
            replaceWith('<div class="row text" style="margin-top:10px;"> <div>Mesto:</div> <div class="col-md-6"> <input name="splnomocnenec_doc_town" class="form-control"></div></div>')

            let menoAdvokata = $(document).find('input[name="lawyer_name"]').val();

            $(document).find('p[id="lawyer_fullname"]').
            replaceWith('<p id="lawyer_fullname">Meno a priezvisko: ' + menoAdvokata  + '</p>')

            $(document).find('span[id="lawyer_fullname"]').
            replaceWith('<span id="lawyer_fullname">' + menoAdvokata  + '</span>')

            $(document).find('span[id="lawyer_name"]').
            replaceWith('<span id="lawyer_name">' + menoAdvokata  + '</span>')

            let adresaAdvokata = $(document).find('input[name="lawyer_residence"]').val();

            $(document).find('p[id="lawyer_residence"]').
            replaceWith('<p id="lawyer_residence">Sídlo: ' + adresaAdvokata  + '</p>')

            $(document).find('span[id="lawyer_residence"]').
            replaceWith('<span id="lawyer_residence">' + adresaAdvokata  + '</span>')
            

            let registracneCislo = $(document).find('input[name="lawyer_registration_number"]').val();

            $(document).find('p[id="lawyer_registration_number"]').
            replaceWith('<p id="lawyer_registration_number">zapísaný v zozname advokátov vedený Slovenskou advokátskou komorou, registračné číslo: ' + registracneCislo  + '</p>')

            $(document).find('span[id="lawyer_registration_number"]').
            replaceWith('<span id="lawyer_registration_number">' + registracneCislo  + '</span>')

            let icoPravnika = $(document).find('input[name="lawyer_ico"]').val();

            $(document).find('p[id="lawyer_ico"]').
            replaceWith('<p id="lawyer_ico">IČO: ' + icoPravnika   + '</p>')

            $(document).find('span[id="lawyer_ico"]').
            replaceWith('<span id="lawyer_ico">' + icoPravnika  + '</span>')

            let email = $(document).find('input[name="lawyer_email"]').val();

            $(document).find('span[id="lawyer_email"]').
            replaceWith('<span id="lawyer_email">' + email  + '</span>')
           
            let select = $(document).find('select[name="customer_full_name"]');
            $.each(res.clientInfo, function (idx, client){
                select.append('<option value="'+client.id+ '">' +client.first_name + ' ' + client.last_name+ '</option>')
            })

            let parentTownValue = $(document).find('input[name="parent-town"]').val();
            $(document).find('input[name="town"]').val(parentTownValue);

            let parentDateValue = $(document).find('input[name="parent-month"]').val();
            $(document).find('input[name="date"]').val(parentDateValue);

            let companyFoundedAt = $(document).find('input[name="incorporation_date"]').val()
            $(document).find('span[id="input.incorporation.date"]').replaceWith('<span id="input.incorporation.date">' + $.datepicker.formatDate('dd.mm.yy', new Date(companyFoundedAt)) + '</span>');
            
            let serviceProvider = $(document).find('select[name="office"]').find(':selected');
            $(document).find('span[id="service_provider_name"]').replaceWith('<span id="service_provider_name">' + serviceProvider.text() + '</span>');
            $(document).find('span[id="service_provider_address"]').replaceWith('<span id="service_provider_address"> ' + serviceProvider.data('address')+ '</span>');
            $(document).find('span[id="service_provider_town"]').replaceWith('<span id="service_provider_town"> ' + serviceProvider.data('town')+ '</span>');
            $(document).find('span[id="service_provider_zip"]').replaceWith('<span id="service_provider_zip"> ' + serviceProvider.data('zip')+ '</span>');
            $(document).find('span[id="service_provider_ico"]').replaceWith('<span id="service_provider_ico"> ' + serviceProvider.data('ico')+ '</span>');
            $(document).find('span[id="service_provider_registered"]').replaceWith('<span id="service_provider_registered"> ' + serviceProvider.data('vlozka')+ '</span>');
            $(document).find('span[id="service_provider_email"]').replaceWith('<span id="service_provider_email"> ' + serviceProvider.data('email')+ '</span>');
            
            let serviceProviderDeputy = $(document).find('input[name="provider_deputy"]').val();
            $(document).find('span[id="service_provider_deputy"]').replaceWith('<span id="service_provider_deputy">'+serviceProviderDeputy+'</span>')
            
            let selectedOfficeId = $(document).find('select[name="office"]').find(':selected').val();
                 $.ajax({
                    url: "/backoffice/services/fetch-service-provider-bank-info",
                    dataType: "json",
                    method: "post",
                    data: { officeId: selectedOfficeId  }
                    }).done(function (res){
                        $(document).find('span[id="office_bank_account"]').text(res.account.iban)
                        $(document).find('span[id="office_bank_account_name"]').text(res.bank.name)
                })

            $(document).find('span[id="odplata_cena"]').replaceWith('<input type="text" id="sluzba_cena" class="form-control" style="width:10% !important;" value="' + res.sluzba.cena_za_jednotku + '">')
            $(document).find('span[id="odplata_dph"]').replaceWith('<input type="text" id="sluzba_dph" class="form-control" style="width:10% !important;" value="12">')

            $(document).find('button#save-changes').on('click', function () {
                let townValue = $(document).find('input[name="town"]').val();
                let dateValue = $(document).find('input[name="date"]').val();
                let incorporationDateValue = $(document).find('input[name="incorporation_date"]').val();
                let selectedClientId = $(document).find('select[name="customer_full_name"]').find(':selected').val();
                let splnomocnenecName = $(document).find('input[name="splnomocnenec"]').val();
                let splnomocnenecSsn = $(document).find('input[name="splnomocnenec_ssn"]').val();
                let splnomocnenecAddress = $(document).find('input[name="splnomocnenec_address"]').val();
                let splnomocnenecBirth = $(document).find('input[name="splnomocnenec_birth"]').val();
                let splnomocnenecDocName = $(document).find('input[name="splnomocnenec_doc_name"]').val();
                let splnomocnenecDocAddress = $(document).find('input[name="splnomocnenec_doc_address"]').val();
                let splnomocnenecDocZip = $(document).find('input[name="splnomocnenec_doc_zip"]').val();
                let splnomocnenecDocTown = $(document).find('input[name="splnomocnenec_doc_town"]').val();
                let lawyerName = $(document).find('input[name="lawyer_name"]').val();
                let lawyerEmail = $(document).find('input[name="lawyer_email"]').val();
                let lawyerResidence = $(document).find('input[name="lawyer_residence"]').val();
                let lawyerIco = $(document).find('input[name="lawyer_ico"]').val();
                let lawyerRegistrationNumber = $(document).find('input[name="lawyer_registration_number"]').val();
                let officeIban = $(document).find('span[id="office_bank_account"]').text();
                let bankName = $(document).find('span[id="office_bank_account_name"]').text();
                let odplataCena = $(document).find('input[id="sluzba_cena"]').val();
                let odplataDph = $(document).find('input[id="sluzba_dph"]').val();
                let providerDeputy = $(document).find('input[name="provider_deputy"]').val();
                $.ajax({
                    url: "/backoffice/services/save-changes",
                    dataType: "json",
                    method: "post",
                    data: {
                        'templateId': templateId, 'clientId': clientId, 
                        'town': townValue ? townValue : '', 'date': dateValue ? dateValue : '', 
                        'selectedClientId': selectedClientId ? selectedClientId : '', 
                        'incorporationDateValue': incorporationDateValue ? incorporationDateValue : '',
                        'splnomocnenecName': splnomocnenecName ? splnomocnenecName : '',
                        'lawyerName': lawyerName ? lawyerName : '', 
                        'lawyerResidence': lawyerResidence ? lawyerResidence : '', 
                        'lawyerIco': lawyerIco ? lawyerIco : '', 
                        'lawyerRegistrationNumber': lawyerRegistrationNumber ? lawyerRegistrationNumber : '', 
                        'lawyerEmail': lawyerEmail ? lawyerEmail : '', 
                        'serviceProvider': serviceProvider.val() ? serviceProvider.val() : '',
                        'splnomocnenecSsn': splnomocnenecSsn ? splnomocnenecSsn : '',
                        'splnomocnenecAddress': splnomocnenecAddress ? splnomocnenecAddress : '',
                        'splnomocnenecBirth': splnomocnenecBirth ? splnomocnenecBirth : '',
                        'splnomocnenecDocName': splnomocnenecDocName ? splnomocnenecDocName : '',
                        'splnomocnenecDocAddress': splnomocnenecDocAddress ? splnomocnenecDocAddress : '',
                        'splnomocnenecDocZip': splnomocnenecDocZip ? splnomocnenecDocZip : '',
                        'splnomocnenecDocTown': splnomocnenecDocTown ? splnomocnenecDocTown : '',
                        'officeIban' : officeIban ? officeIban : '',
                        'bankName' : bankName ? bankName : '',
                        'odplataCena' : odplataCena ? odplataCena : '',
                        'odplataDph' : odplataDph ? odplataDph : '',
                        'providerDeputy': providerDeputy ? providerDeputy : ''
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
                    },
                    error: function(r){
                        $.toast({
                            //heading: 'Welcome to Material Pro admin',
                            text: 'Uloženie dokumentu zlyhalo',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 2500, 
                            stack: 6
                        });
                    }
                })
             })
        })
    })
            $(document).on('change', '.client-info', function(){ 
                let id = $(this).find(':selected').val()
                $.ajax({
                    url: "/backoffice/services/fetch-client-personal-info",
                    dataType: "json",
                    data: {clientId: id}
                }).done(function (res) {
                    $(document).find('span[id="selected.client_personal_info_address"]').replaceWith('<span id="selected.client_personal_info_address">Bytom: '+ res.address + ', '+ res.zip + ', '+ res.town + ' </span>')
                    $(document).find('span[id="selected.client_personal_info_birth"]').replaceWith('<span id="selected.client_personal_info_birth"> Narodený(á): '+ $.datepicker.formatDate('dd.mm.yy', new Date(res.birth_date)) + '</span>')
                    $(document).find('span[id="selected_client_name"]').replaceWith('<span id="selected_client_name">'+ res.first_name + ' ' + res.last_name + '</span>')
                })
            });

        $(document).on('change', '.lawyer_name', function (e) {
            $(document).find('span[id="lawyer_name"]').
            replaceWith('<span id="lawyer_name">'+ e.target.value +'</span>')

            $(document).find('p[id="lawyer_fullname"]').
            replaceWith('<p id="lawyer_fullname">Meno a priezvisko: ' + e.target.value  + '</p>')

            $(document).find('span[id="lawyer_fullname"]').
            replaceWith('<span id="lawyer_fullname">' + e.target.value  + '</span>')
        })

        $(document).on('change', 'input[name="lawyer_residence"]', function (e) {
            $(document).find('span[id="lawyer_residence"]').
            replaceWith('<span id="lawyer_residence">'+ e.target.value +'</span>')

            $(document).find('p[id="lawyer_residence"]').
            replaceWith('<p id="lawyer_residence">' + e.target.value  + '</p>')
        })

        $(document).on('change','input[name="provider_deputy"]', function (e){
            $(document).find('span[id="service_provider_deputy"]').
            replaceWith('<span id="service_provider_deputy">'+ e.target.value +'</span>');
        });

        $(document).on('change', 'input[name="lawyer_ico"]', function (e) {
            $(document).find('span[id="lawyer_ico"]').
            replaceWith('<span id="lawyer_ico">'+ e.target.value +'</span>')

            $(document).find('p[id="lawyer_ico"]').
            replaceWith('<p id="lawyer_ico">' + e.target.value  + '</p>')
        })

        $(document).on('change', 'input[name="lawyer_registration_number"]', function (e) {
            $(document).find('span[id="lawyer_registration_number"]').
            replaceWith('<span id="lawyer_registration_number">'+ e.target.value +'</span>')

            $(document).find('p[id="lawyer_registration_number"]').
            replaceWith('<p id="lawyer_registration_number">' + e.target.value  + '</p>')
        })       

        $(document).on('change', 'input[name="lawyer_email"]', function (e) {
            $(document).find('span[id="lawyer_email"]').
            replaceWith('<span id="lawyer_email">'+ e.target.value +'</span>')
        })

        $(document).on('change', 'input[name="parent-town"]', function (e){
            $(document).find('input[name="town"]').val(e.target.value)
        })

        $(document).on('change', 'input[name="parent-month"]', function (e){
            $(document).find('input[name="date"]').val(e.target.value)
        })

        $(document).on('change', 'input[name="incorporation_date"]', function (e){
            $(document).find('span[id="input.incorporation.date"]').text($.datepicker.formatDate('dd.mm.yy', new Date(e.target.value)))
        })

        $(document).on('change', 'select[name="office"]', function (e){
            $(document).find('span[id="service_provider_name"]').text($(this).find(':selected').text())
        })

        $(document).on('change', 'input[name="splnomocnenec"]', function (e) {
            $(document).find('input[name="splnomocnenec_doc_name"]').val(e.target.value)
            $(document).find('span[id="splnomocnenec_podpis"]').text(e.target.value)
         })

        $(document).on('change', 'input[name="splnomocnenec_address"]', function (e) {
            $(document).find('input[name="splnomocnenec_doc_address"]').val(e.target.value)
         })

           $(document).on('change', '.user-info', function(){ 
                let id = $(this).find(':selected').val()
                $.ajax({
                    url: "/backoffice/services/fetch-client-personal-info",
                    dataType: "json",
                    data: {clientId: id}
                }).done(function (res) {
                    $(document).find('span[id="selected_meno"]').replaceWith('<span id="selected_meno"> '+ res.first_name + ' ' + res.last_name  +' </span>')
                    $(document).find('p[id="selected_meno"]').replaceWith('<p id="selected_meno" class="text">Meno a priezvisko: '+ res.first_name + ' ' + res.last_name  +' </p>')
                    $(document).find('p[id="selected_address"]').replaceWith('<p id="selected_address" class="text">Adresa: '+ res.address  +' </p>')
                    $(document).find('p[id="selected_zip"]').replaceWith('<p id="selected_zip" class="text">PSČ: '+ res.zip +' </p>')
                    $(document).find('p[id="selected_town"]').replaceWith('<p id="selected_town" class="text">Town: '+ res.town +' </p>')
                    $(document).find('p[id="selected_ssn"]').replaceWith('<p id="selected_ssn" class="text">Rodné číslo: '+ res.ssn +' </p>')
                    $(document).find('p[id="selected_birth"]').replaceWith('<p id="selected_birth" class="text">Narodený: '+ $.datepicker.formatDate('dd.mm.yy', new Date(res.birth_date)) +' </p>')
                    $(document).find('p[id="selected_full_address"]').replaceWith('<p id="selected_full_address" class="text">Adresa: '+ res.address + ', ' + res.zip + ', '+ res.town +'</p>')
                })
            });
        $(document).on('change', 'select[name="office"]', function (e){
            let selectedOfficeId = $(this).find(':selected').val();
            $.ajax({
                    url: "/backoffice/services/fetch-service-provider-bank-info",
                    dataType: "json",
                    method: "post",
                    data: { officeId: selectedOfficeId  }
            }).done(function (res){
                $(document).find('span[id="office_bank_account"]').text(res.account.iban)
                $(document).find('span[id="office_bank_account_name"]').text(res.bank.name)
            })
        }) 

JS;
$this->registerJS($js);
