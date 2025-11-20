<?php

/**
 * @var $data array
 * @var $regions
 * @var $districts
 */

use backend\assets\RealAsset;

$this->title = 'Register';
$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile(
    '@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',
    ['depends' => RealAsset::class]
);
$this->registerJSFile(
    '@web/assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js',
    ['depends' => RealAsset::class]
);
$this->registerCSSFile(
    '@web/assets/node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css',
    ['depends' => RealAsset::class]
);

?>

    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-12 align-self-center">
                <h4 class="text-themecolor"><?= $this->title ?></h4>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Filtre</h4>
                        <form method="post" role="form" id="sb01">
                            <div class="row mb-3">
                                <div class="col-2">
                                    <label class="control-label">Meno</label>
                                    <input type="text" name="filter[meno]" id="" class="form-control">
                                </div>
                                <div class="col-2">
                                    <label class="control-label">Priezvisko</label>
                                    <input type="text" name="filter[priezvisko]" id="" class="form-control">
                                </div>
                                <div class="col-2">
                                    <label class="control-label">Kraj</label>
                                    <select name="filter[kraj]" id="kraj" class="form-select">
                                        <option value=""></option>
                                        <?= $this->render('regions', ['regions' => $regions]) ?>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label class="control-label">Okres</label>
                                    <select name="filter[okres]" id="okres" class="form-select">
                                        <option value=""></option>
                                        <?= $this->render('districts', ['districts' => $districts]) ?>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label class="control-label">Obec</label>
                                    <input type="text" name="filter[obec]" id="" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label class="control-label">Kat. úrad</label>
                                    <input type="text" name="filter[ku]" id="" class="form-control">
                                </div>
                                <div class="col-2">
                                    <label class="control-label">LV</label>
                                    <input type="text" name="filter[lv]" id="" class="form-control">
                                </div>
                                <div class="col-2">
                                    <label class="control-label">Čís. par.</label>
                                    <input type="text" name="filter[parc]" id="" class="form-control">
                                </div>
                                <div class="col-2">
                                    <label class="control-label">Typ par.</label>
                                    <select class="form-select" name="filter[par_typ]" id="">
                                        <option value=""></option>
                                        <option value="C">C</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label class="control-label">Sup. č.</label>
                                    <input type="text" name="filter[supcis]" id="" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label for="" class="control-lable">Výmera od</label>
                                    <input type="text" name="filter[vymera_od]" id="" class="form-control">
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-lable">Výmera do</label>
                                    <input type="text" name="filter[vymera_do]" id="" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label for="" class="control-label" title="Druh pozemku">Druh p.</label>
                                    <select name="filter[druh_poz]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="2">2 - orná pôda</option>
                                        <option value="3">3 - chmeľnica</option>
                                        <option value="4">4 - vinica</option>
                                        <option value="5">5 - záhrada</option>
                                        <option value="6">6 - ovocný sad</option>
                                        <option value="7">7 - trvalý trávnatý porast</option>
                                        <option value="10">10 - lesný pozemok</option>
                                        <option value="11">11 - vodná plocha</option>
                                        <option value="13">13 - zastavaná plocha a nádvorie</option>
                                        <option value="14">14 - ostatná plocha</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label" title="Spôsobu využitia pozemku">spôsobu využitia p.</label>
                                    <select name="filter[spos_vyuz]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Pozemok využívaný pre rastlinnú výrobu, na ktorom sa pestujú obilniny, okopaniny, krmoviny, technické plodiny, zelenina a iné poľnohospodárske plodiny alebo pozemok dočasne nevyužívaný pre rastlinnú výrobu</option>
                                        <option value="2">2 - Pozemok vysadený chmeľom alebo pozemok vhodný na pestovanie chmeľu, na ktorom bol chmeľ dočasne odstránený</option>
                                        <option value="3">3 - Pozemok, na ktorom sa pestuje vinič alebo pozemok vhodný na pestovanie viniča, na ktorom bol vinič dočasne odstránený</option>
                                        <option value="4">4 - Pozemok prevažne v zastavanom území obce alebo v záhradkárskej osade, na ktorom sa pestuje zelenina, ovocie, okrasná nízka a vysoká zeleň a iné poľnohospodárske plodiny</option>
                                        <option value="5">5 - Pozemok v rámci záhradného centra, na ktorom sa pestuje okrasná nízka a vysoká zeleň alebo pozemok dočasne využívaný na výrobu trávnikových kobercov, vianočných stromčekov a inej okrasnej zelene</option>
                                        <option value="6">6 - Pozemok súvisle vysadený ovocnými stromami, ovocnými krami a ovocnými sadenicami na jednom mieste, jedným alebo viacerými druhmi</option>
                                        <option value="7">7 - Pozemok lúky a pasienku trvalo porastený trávami alebo pozemok dočasne nevyužívaný pre trvalý trávny porast</option>
                                        <option value="8">8 - Na pozemku je postavený skleník, japan, parenisko a iné</option>
                                        <option value="9">9 - Na pozemku je škôlka pre chmeľové sadivo, viničová škôlka, škôlka pre ovocné, alebo okrasné dreviny, lesná škôlka alebo semenný sad a iné</option>
                                        <option value="10">10 - Na pozemku je účelová ochranná poľnohospodárska a ekologická zeleň proti erozívnych opatrení a opatrení na zabezpečenie ekologickej stability územia</option>
                                        <option value="11">11 - Vodný tok (prirodzený – rieka, potok; umelý – kanál, náhon a iné)</option>
                                        <option value="12">12 - Vodná plocha (jazero, umelá vodná nádrž, odkryté podzemné vody – štrkovisko, bagrovisko a iné)</option>
                                        <option value="13">13 - Rybník – umelá vodná nádrž určená na chov rýb vrátane stavieb</option>
                                        <option value="14">14 - Močiar</option>
                                        <option value="15">15 - Pozemok, na ktorom je postavená bytová budova označená súpisným číslom</option>
                                        <option value="16">16 - Pozemok, na ktorom je postavená nebytová budova označená súpisným číslom</option>
                                        <option value="17">17 - Pozemok, na ktorom je postavená budova bez označenia súpisným číslom</option>
                                        <option value="18">18 - Pozemok, na ktorom je dvor</option>
                                        <option value="19">19 - Pozemok, na ktorom je spoločný dvor</option>
                                        <option value="20">20 - Pozemok, na ktorom je postavená inžinierska stavba – železničná, lanová a iná dráha a jej súčasti</option>
                                        <option value="21">21 - Pozemok, na ktorom je postavená inžinierska stavba – diaľnica a rýchlostná komunikácia a jej súčasti</option>
                                        <option value="22">22 - Pozemok, na ktorom je postavená inžinierska stavba – cestná, miestna a účelová komunikácia, lesná cesta, poľná cesta, chodník, nekryté parkovisko a ich súčasti</option>
                                        <option value="23">23 - Pozemok, na ktorom je postavená inžinierska stavba – vzletová, pristávacia a rolovacia dráha letiska a jej súčasti</option>
                                        <option value="24">24 - Pozemok, na ktorom je postavená inžinierska stavba – prístav, plavebný kanál a komora, priehrada a iná ochranná hrádza, závlahová a melioračná sústava a jej súčasti</option>
                                        <option value="25">25 - Pozemok, na ktorom je postavená ostatná inžinierska stavba a jej súčasti</option>
                                        <option value="26">26 - Pozemok, na ktorom je rozostavaná stavba</option>
                                        <option value="27">27 - Pozemok, na ktorom je zrúcanina</option>
                                        <option value="28">28 - Pozemok, na ktorom je postavený vstupný portál do podzemnej stavby alebo pivnice</option>
                                        <option value="29">29 - Pozemok, na ktorom je okrasná záhrada, uličná a sídlisková zeleň, park a iná funkčná zeleň a lesný pozemok na rekreačné a poľovnícke využívanie</option>
                                        <option value="30">30 - Pozemok, na ktorom je ihrisko, štadión, kúpalisko, športová dráha, autokemp, táborisko a iné</option>
                                        <option value="31">31 - Pozemok, na ktorom je botanická a zoologická záhrada, skanzen, amfiteáter, pamätník a iné</option>
                                        <option value="32">32 - Pozemok, na ktorom je cintorín alebo urnový háj</option>
                                        <option value="33">33 - Pozemok, ktorý slúži na ťažbu nerastov a surovín</option>
                                        <option value="34">34 - Pozemok, na ktorom je manipulačná a skladová plocha, objekt a stavba slúžiaca lesnému hospodárstvu</option>
                                        <option value="35">35 - Pozemok, na ktorom je skládka odpadu</option>
                                        <option value="36">36 - Pozemok, ktorý nie je využívaný žiadnym z uvedených spôsobov</option>
                                        <option value="37">37 - Pozemok, na ktorom sú skaly, svahy, rokliny, výmole, vysoké medze s krovím alebo kamením a iné plochy, ktoré neposkytujú trvalý úžitok</option>
                                        <option value="38">38 - Pozemok s lesným porastom, dočasne bez lesného porastu na účely obnovy lesa alebo po vykonaní náhodnej ťažby</option>
                                        <option value="99">99 - Pozemok využívaný podľa druhu pozemku</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label" title="Druh chránenej nehnuteľnosti">Druh chr. neh.</label>
                                    <select name="filter[druh_chra_neh]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="101">101 - Chránená krajinná oblasť</option>
                                        <option value="102">102 - Národný park</option>
                                        <option value="103">103 - Chránený areál</option>
                                        <option value="104">104 - Prírodná rezervácia (národná prírodná rezervácia)</option>
                                        <option value="105">105 - Prírodná pamiatka (národná prírodná pamiatka)</option>
                                        <option value="106">106 - Chránený krajinný prvok</option>
                                        <option value="107">107 - Ochranné pásmo chráneného územia</option>
                                        <option value="108">108 - Chránené vtáčie územie</option>
                                        <option value="109">109 - Chránený strom a jeho ochranné pásmo</option>
                                        <option value="110">110 - Územie európskeho významu</option>
                                        <option value="201">201 - Nehnuteľná kultúrna pamiatka (národná kultúrna pamiatka)</option>
                                        <option value="202">202 - Pamiatková rezervácia</option>
                                        <option value="203">203 - Pamiatková zóna</option>
                                        <option value="204">204 - Ochranné pásmo nehnuteľnej kultúrnej pamiatky, pamiatkovej rezervácie alebo pamiatkovej zóny</option>
                                        <option value="205">205 - Lokalita svetového dedičstva UNESCO</option>
                                        <option value="301">301 - Kúpeľné územie</option>
                                        <option value="302">302 - Prírodný liečivý zdroj alebo prírodný zdroj minerálnej stolovej vody</option>
                                        <option value="303">303 - Ochranné pásmo kúpeľného územia</option>
                                        <option value="304">304 - Ochranné pásmo prírodného liečivého zdroja alebo prírodného zdroja minerálnej stolovej vody (I. – III. stupeň)</option>
                                        <option value="401">401 - Chránené ložiskové územie</option>
                                        <option value="501">501 - Chránená vodohospodárska oblasť</option>
                                        <option value="502">502 - Ochranné pásmo vodárenských zdrojov (I. – III. stupeo)</option>
                                        <option value="503">503 - Ochranné pásmo vodnej stavby</option>
                                        <option value="601">601 - Chránená značka geodetického bodu</option>
                                        <option value="602">602 - Ochranné pásmo geodetického bodu</option>
                                        <option value="701">701 - Ochranné pásmo letiska a leteckých pozemných zariadení</option>
                                        <option value="801">801 - Iná ochrana</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label">Právny vzťah</label>
                                    <select name="filter[prav_vztah]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Oprávnená držba k pozemku</option>
                                        <option value="2">2 - Nájom k pozemku</option>
                                        <option value="3">3 - Spoluvlastníctvo k pozemku pod stavbou</option>
                                        <option value="4">4 - Vlastník pozemku je vlastníkom stavby postavenej na tomto pozemku</option>
                                        <option value="5">5 - Vlastník pozemku nie je vlastníkom stavby postavenej na tomto pozemku</option>
                                        <option value="7">7 - Právny vzťah nie je evidovaný v súbore popisných informácií katastra nehnuteľností</option>
                                        <option value="9">9 - Duplicitné alebo viacnásobné vlastníctvo k tej istej nehnuteľnosti alebo k jej časti</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label" title="Umiestnenie pozemku">Umiest. poz.</label>
                                    <select name="filter[umiest_poz]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Pozemok je umiestnený v zastavanom území obce</option>
                                        <option value="2">2 - Pozemok je umiestnený mimo zastavaného územia obce</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-2">
                                    <label for="" class="control-label" title="Kódy spoločnej nehnuteľnosti">
                                        Spol. neh.
                                    </label>
                                    <select name="filter[spol_neh]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Pozemok nie je spoločnou nehnuteľnosťou</option>
                                        <option value="2">2 - Pozemok je spoločnou nehnuteľnosťou</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label" title="Kódy druhu stavby">Druh stav.</label>
                                    <select name="" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Priemyselná budova</option>
                                        <option value="2">2 - Poľnohospodárska budova</option>
                                        <option value="3">3 - Budova železníc a dráh</option>
                                        <option value="4">4 - Budova pre správu a údržbu diaľnic a rýchlostných ciest</option>
                                        <option value="5">5 - Budova letísk</option>
                                        <option value="6">6 - Iná dopravná a telekomunikačná budova (budova prístavu, garáže, kryté parkovisko, budova na rádiové a televízne vysielanie a iné)</option>
                                        <option value="7">7 - Samostatne stojaca garáž</option>
                                        <option value="8">8 - Budova lesného hospodárstva (horáreň, technická prevádzková stavba a iné)</option>
                                        <option value="9">9 - Bytový dom</option>
                                        <option value="10">10 - Rodinný dom</option>
                                        <option value="11">11 - Budova pre školstvo, na vzdelávanie a výskum</option>
                                        <option value="12">12 - Budova zdravotníckeho a sociálneho zariadenia</option>
                                        <option value="13">13 - Budova ubytovacieho zariadenia</option>
                                        <option value="14">14 - Budova obchodu a služieb</option>
                                        <option value="15">15 - Administratívna budova</option>
                                        <option value="16">16 - Budova pre kultúru a na verejnú zábavu (múzeum, knižnica a galéria)</option>
                                        <option value="17">17 - Budova na vykonávanie náboženských aktivít, krematóriá a domy smútku</option>
                                        <option value="18">18 - Budova technickej vybavenosti sídla (výmenníková stanica, budova na rozvod energií, čerpacia a prečerpávacia stanica, úpravňa vody, transformačná stanica a rozvodňa, budova vodojemu alebo čistiarne odpadových vôd a iné)</option>
                                        <option value="19">19 - Budova pre šport a na rekreačné účely</option>
                                        <option value="20">20 - Iná budova</option>
                                        <option value="21">21 - Rozostavaná budova</option>
                                        <option value="22">22 - Polyfunkčná budova</option>
                                        <option value="23">23 - Inžinierska stavba</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label" title="Kódy umiestnenia stavby">
                                        Umiest. stav.
                                    </label>
                                    <select name="filter[umiest_stavb]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Stavba postavená na zemskom povrchu</option>
                                        <option value="2">2 - Podzemná stavba</option>
                                        <option value="3">3 - Nadzemná stavba</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label" title="Kódy druhu priestoru">
                                        Druh priest.
                                    </label>
                                    <select name="filter[druh_priest]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Byt</option>
                                        <option value="2">2 - Nebytový priestor</option>
                                        <option value="3">3 - Rozostavaný byt</option>
                                        <option value="4">4 - Rozostavaný nebytový priestor</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label" title="Kódy druhu nebytového priestoru">
                                        Druh nebyt. priest.
                                    </label>
                                    <select name="filter[druh_nebytp]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Zariadenie obchodu</option>
                                        <option value="2">2 - Garáž</option>
                                        <option value="3">3 - Zariadenie verejnej správy a administratívy</option>
                                        <option value="4">
                                            4 - Zariadenie služieb (výrobné, nevýrobné, opravárenské)
                                        </option>
                                        <option value="5">5 - Zariadenie školské a výchovné</option>
                                        <option value="6">6 - Zariadenie kultúrne a osvetové</option>
                                        <option value="7">7 - Zariadenie stravovacie</option>
                                        <option value="8">8 - Skladový priestor</option>
                                        <option value="9">
                                            9 - Zariadenie zdravotníckej a sociálnej starostlivosti
                                        </option>
                                        <option value="10">10 - Telovýchovné a športové zariadenie</option>
                                        <option value="11">11 - Ateliér</option>
                                        <option value="12">12 - Iný nebytový priestor</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label for="" class="control-label" title="Kódy účastníka právneho vzťahu">
                                            Práv. vťah úč.
                                    </label>
                                    <select name="filter[prav_vztah_ucast]]" id="" class="form-select">
                                        <option value=""></option>
                                        <option value="1">1 - Vlastník</option>
                                        <option value="2">2 - Správca</option>
                                        <option value="3">3 - Nájomca</option>
                                        <option value="4">4 - Iná oprávnená osoba z práva k nehnuteľnosti</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-info text-white">Hľadať</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="table-responsive m-t-20">
                            <table class="table table-bordered table-striped table-sm dattable" id="tab01">
                                <thead>
                                <tr>
                                    <th>Majiteľ</th>
                                    <th>Adresa</th>
                                    <th>Email</th>
                                    <th>Telefón</th>
                                    <th title="Číslo okresu">Č.ok.</th>
                                    <th title="Okres">Okr.</th>
                                    <th title="Číslo obce">Č. ob.</th>
                                    <th title="Obec">Ob.</th>
                                    <th title="Kód katastrálneho územia">K.k.u.</th>
                                    <th title="Katastrálne územie">K.u.</th>
                                    <th title="List vlastníctva">LV</th>
                                    <th>Typ</th>
                                    <th title="Podiel">Pod.</th>
                                    <th title="Titul nadobudnutia">Tit. nad.</th>
                                    <th title="Ťarchy">Ťar.</th>
                                    <th title="Súpisné číslo">Sup. č.</th>
                                    <th title="Číslo parcely">Č.par.</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?= $this->render('_tbody', ['data' => $data]) ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$js = <<<JS
    $(function() {
        $('.dattable').DataTable({
            order: [],
            responsive: true,
            "bFilter": false
        });
    });

    $('#sb01').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: '/backoffice/property-register/search',
            type: 'post',
            data: $(this).serialize(),
            success: function(data) {
                $('.dattable tbody').html(data);
            }
        });
    });
    $(document).on('change', '#kraj', function(e) {
        let kraj = $(this).val();
       $.ajax({
            url: '/backoffice/property-register/get-districts',
            type: 'post',
            data: { kraj: kraj },
            success: function(data) {
                let x = $('#okres').empty();
                for(i=0; i<data.regions.length; i++) {
                    x.append($('<option>',{value:data.regions[i].id + "_" + data.regions[i].kod, text:data.regions[i].name}));
                }
            } 
       });
    });  
JS;
$this->registerJS($js);
