<?php

use Yii;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use frontend\assets\OpenDaysAsset;
use yii\helpers\Url;

$this->title = 'Nyílt nap / Deň otvorených dverí';

/**
 * On POST: save clothing data to `student` table
 * and send an email with all submitted data.
 */
if (Yii::$app->request->isPost) {
    $reg = Yii::$app->request->post('Reg', []);

    // Map form fields -> student table columns
    $height   = $reg['height_range'] ?? null;  // e.g. "176-188"
    $footSize = $reg['shoe_size']    ?? null;  // 35–48
    $tshirt   = $reg['shirt_size']   ?? null;  // S, M, L, ...
    $waist    = $reg['pants']        ?? null;  // trousers size
    $length   = $reg['jacket']       ?? null;  // coat size

    // Insert into student table (adjust table/column names if needed)
    try {
        Yii::$app->db->createCommand()->insert('student', [
            'height'   => $height,
            'footSize' => $footSize,
            'tshirt'   => $tshirt,
            'waist'    => $waist,
            'length'   => $length,
        ])->execute();
    } catch (\Throwable $e) {
        Yii::error('Student insert failed: ' . $e->getMessage(), __METHOD__);
        // We don't break the flow, email can still be sent.
    }

    // Build simple plain-text email with all submitted data
    $body  = "Új open day regisztráció / Nová registrácia na deň otvorených dverí\n\n";
    foreach ($reg as $key => $value) {
        if (is_array($value)) {
            $valueStr = print_r($value, true);
        } else {
            $valueStr = $value;
        }
        $body .= $key . ': ' . $valueStr . "\n";
    }

    // Send email to admin (adjust address in params.php if needed)
    try {
        Yii::$app->mailer->compose()
            ->setTo(Yii::$app->params['adminEmail'])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setSubject('Open Days – új regisztráció / nová registrácia')
            ->setTextBody($body)
            ->send();
    } catch (\Throwable $e) {
        Yii::error('Open Days mail send failed: ' . $e->getMessage(), __METHOD__);
    }

    Yii::$app->session->setFlash(
        'success',
        'Köszönjük a regisztrációt! / Ďakujeme za registráciu!'
    );
}

/**
 * @var $fields
 * @var $partners
 * @var $towns
 */
OpenDaysAsset::register($this);
$this->registerCSSFile(
    'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css',
    ['depends' => OpenDaysAsset::class]
);
$this->registerCSSFile(
    'https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css',
    ['depends' => OpenDaysAsset::class]
);
$this->registerJSFile(
    'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.full.min.js',
    ['depends' => OpenDaysAsset::class]
);
?>
    <main class="site-applicant">
        <input type="hidden" id="client_id" value="0">
        <div class="page-banner d-block position-relative raleway">
            <canvas style="background-image:url('/images/contact-us-banner-1.jpg');" width="1600" height="400"></canvas>
            <div class="page-title container-default d-block position-absolute mx-auto">
                <div class="container-fluid">
                    <div class="titlewrapper">
                        <h1 class="entry-title animated fadeInDown visible" data-aios-reveal="true"
                            data-aios-animation="fadeInDown" data-aios-animation-delay="0.3s"
                            data-aios-animation-reset="false"
                            data-aios-animation-offset="0" style="animation-delay: 0.3s;">
                            <strong><?= Html::encode($this->title) ?></strong>
                        </h1>
                    </div>
                </div>
            </div>
            <div class="breadcrumbs-container">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 col-xs-12">
                            <?=  Breadcrumbs::widget([ 'links' => $this->params['breadcrumbs'] ?? [] ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            // QR code under the title (embedded from local /asset/ folder next to this view)
            $qrPath = __DIR__ . '/asset/openday_qrcode.png';
            if (file_exists($qrPath)) {
                $qrData = base64_encode(file_get_contents($qrPath));
                echo Html::img('data:image/png;base64,' . $qrData, [
                    'alt'   => 'aoreal.sk/open-days',
                    'class' => 'd-block mx-auto my-3',
                    'style' => 'max-width:220px;height:auto;'
                ]);
            } else {
                echo '<!-- QR code not found: ' . Html::encode($qrPath) . ' -->';
            }
            ?>
        </div>
        <div class="container-fluid">

            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success" style="margin-top:20px;">
                    <?= Yii::$app->session->getFlash('success') ?>
                </div>
            <?php endif; ?>

            <div class="dual-container">
                <form method="post" role="form" id="t009">
                    <input
                        type="hidden"
                        name="<?= Yii::$app->request->csrfParam; ?>"
                        value="<?= Yii::$app->request->getCsrfToken() ?>"
                    >
                    <h3 style="margin: 0 0 30px 0;">Általános adatok / Základné údaje</h3>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label col-12">Keresztnév / Meno</label>
                        <div class="col-sm-9 col-12">
                            <input type="text" class="form-control rq ef-1" name="Reg[first_name]" data-eid="1">
                            <p class="error-msg" id="ep-1"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label col-12">Vezetéknév / Priezvisko</label>
                        <div class="col-sm-9 col-12">
                            <input type="text" class="form-control rq ef-2" name="Reg[last_name]" data-eid="2">
                            <p class="error-msg" id="ep-2"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label col-12">Email</label>
                        <div class="col-sm-9 col-12">
                            <input type="text" class="form-control rq ef-3" name="Reg[email]" data-eid="3" id="e-1">
                            <p class="error-msg" id="ep-3"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label col-12">Email újra / Email znovu</label>
                        <div class="col-sm-9 col-12">
                            <input type="text" class="form-control rq ef-3v" name="Reg[email_valid]" data-eid="3v" id="e-2">
                            <p class="error-msg" id="ep-3v"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label col-12">Telefon / Telefón</label>
                        <div class="col-sm-9 col-12">
                            <input type="text" class="form-control rq ef-4" name="Reg[phone]" data-eid="4" id="p-1">
                            <p class="error-msg" id="ep-4"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label col-12">Telefon újra / Telefón znovu</label>
                        <div class="col-sm-9 col-12">
                            <input type="text" class="form-control rq ef-4v" name="Reg[phone_valid]" data-eid="4v" id="p-2">
                            <p class="error-msg" id="ep-4v"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label col-12">Facebook profil</label>
                        <div class="col-sm-9 col-12">
                            <input type="text" class="form-control" name="Reg[facebook]">
                        </div>
                    </div>

                    <h3 style="margin:50px 0 30px 0;">Munkaruha méret / Veľkosť pracovného oblečenia</h3>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Magasság (cm) / Výška (cm)</label>
                        <div class="col-12 col-sm-9">
                            <select class="form-control" name="Reg[height_range]">
                                <option value=""></option>
                                <option value="164-176">164-176</option>
                                <option value="176-188">176-188</option>
                                <option value="188-194">188-194</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Nadrág / Nohavice</label>
                        <div class="col-sm-9 col-12">
                            <select name="Reg[pants]" class="form-control">
                                <option value=""></option>
                                <?php foreach(range(40, 64, 2) as $x) :?>
                                    <option value="<?= $x ?>"><?= $x ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Kabát / Bunda</label>
                        <div class="col-sm-9 col-12">
                            <select name="Reg[jacket]" class="form-control">
                                <option value=""></option>
                                <?php foreach(range(40, 64, 2) as $x) :?>
                                    <option value="<?= $x ?>"><?= $x ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Kesztyű / Rukavice</label>
                        <div class="col-sm-9 col-12">
                            <select class="form-control" name="Reg[gloves]">
                                <option value=""></option>
                                <?php foreach(range(6, 11, 1) as $x) :?>
                                <option value="<?= $x ?>"><?= $x ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Cipő / Obuv</label>
                        <div class="col-12 col-sm-9">
                            <select class="form-control" name="Reg[shoe_size]">
                                <option value=""></option>
                                <?php foreach(range(35, 48, 1) as $x) :?>
                                    <option value="<?= $x ?>"><?= $x ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Trikó / Tričko</label>
                        <div class="col-12 col-sm-9">
                            <select class="form-control" name="Reg[shirt_size]">
                                <option value=""></option>
                                <?php foreach(['S','M','L', 'XL', 'XXL', 'XXXL'] as $x) :?>
                                    <option value="<?= $x ?>"><?= $x ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h3 style="margin: 50px 0 30px 0;">Általános iskola / Základná škola</h3>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label col-12">Város / Mesto</label>
                        <div class="col-sm-9 col-12">
                            <select name="Reg[town]" class="form-control" id="sel2-city"></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Iskolám / Moja škola</label>
                        <div class="col-sm-9 col-12">
                            <select name="Reg[primary_school]" class="form-control" id="sel-school"></select>
                        </div>
                    </div>
                    <h3 style="margin-bottom: 50px;margin-top: 20px;">Középiskola / Stredná škola</h3>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Iskola neve / Názov školy</label>
                        <div class="col-sm-9 col-12">
                            SOŠ stavebná s VJM, Dunajská Streda, Ul. Gyulu Szabóa 1
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Kollégium / Internát</label>
                        <div class="col-sm-9 col-12">
                            <select name="Reg[internat]" class="form-control">
                                <option value=""></option>
                                <option value="yes">Igénylek kollégiumot / Chcem internát</option>
                                <option value="no">Nem igénylek kollégiumot / Nechcem internát</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">Menza / Školská jedáleň</label>
                        <div class="col-sm-9 col-12">
                            <select name="Reg[canteen]" class="form-control">
                                <option value=""></option>
                                <option value="yes">Szeretnék menzára járni / Chcem sa stravovať v školskom jedálni</option>
                                <option value="no">Nem szeretnék menzára járni / Nechcem sa stravovať v školskom jedálni</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-3 col-12">
                            Elérhető szakok / Dostupné študijné odbory
                        </label>
                        <div class="col-sm-9 col-12">

                            <p class="head-note">
                                Rakd sorrendbe a szakokat 1-től 10-ig, ahogy tanulni szeretnéd. Azok, amelyek
                                biztosan nem érdekelnek hagyd 0-án.
                                /
                                Zoraď si študijné odbory podľa tvojej preferencie od 1 do 10. Tie ktoré nechceš
                                študovať označ číslom 0.
                            </p>

                            <h4>3 éves képzés / 3 ročné štúdium</h4>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][16][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    kőműves / murár
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][1][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    asztalos / stolár
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][5][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    festő / maliar
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][3][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    ács / tesár
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][17][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    víz-, gáz- és központifűtés szerelő / inštalatér
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][19][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    virágkötő / viazač- aranžér kvetín
                                </div>
                            </div>
                            <h4 style="margin-top: 30px;">4 éves képzés / 4 ročné štúdium</h4>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][11][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    építészet / staviteľstvo
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][15][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    épületgépész-technikus / mechanik stavebnoinštalačných zariadení
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][20][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    műszaki líceum – építészeti irányzat / technické lýceum  – štúdium ukončené maturitnou skúškou
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-2 col-12">
                                    <select class="form-control" name="Reg[fields][21][ord]">
                                        <?php for ($i=0; $i<11; ++$i): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-sm-10 col-12">
                                    kerttervezés, dekoratőr és virágkötészet / záhradníctvo -  viazačstvo a aranžérstvo – štúdium ukončené maturitnou skúškou
                                </div>
                            </div>

                        </div>
                    </div>
                    <h3 style="margin-bottom: 50px;margin-top: 30px;">Partner a praxhoz / Partner pre dual prax</h3>
                    <div class="form-group row">
                        <div class="col-sm-3 col-12"></div>
                        <div class="col-sm-9 col-12">
                            <input type="radio" name="Reg[partner][choice]" value="aors" id="c2">
                                &nbsp;Az ALPHA-OMEGA REAL SOLUTIONS s.r.o.-nál szeretnék praxolni /
                            Chcem absolvovať prax u firmy ALPHA-OMEGA REAL SOLUTIONS s.r.o.
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 col-12"></div>
                        <div class="col-sm-9 col-12">
                            <input type="radio" name="Reg[partner][choice]" value="other" id="c3">
                                &nbsp;Más cégben szeretnék praxolni / Chcem absolvovať prax v inej firme
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 col-12"></div>
                        <div class="col-sm-9 col-12">
                            <input type="radio" name="Reg[partner][choice]" value="none" id="c1" checked>
                            &nbsp;Még nem döntöttem / Ešte nie som rozhodnutý(á)
                        </div>
                    </div>
                    <section id="company" style="display: none">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Cég neve / Názov firmy</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][name]" data-eid="6" id="t-1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Kapcsolattartó személy / Kontaktna osoba</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][contact]" id="t-2">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Email</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][email]" data-eid="6" id="t-3">
                                <p class="error-msg" id="ep-6"></p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label col-12">Telefon / Telefón</label>
                            <div class="col-sm-9 col-12">
                                <input type="text" class="form-control" name="Reg[partner][phone]" id="t-4">
                            </div>
                        </div>
                    </section>

                    <div class="form-group row">
                        <div class="col-sm-3 col-12"></div>
                        <div class="col-sm-9 col-12">
                            <input type="checkbox" name="Reg[consent]" class="rq">&nbsp;
                            Hozzájárulok személyes adataim marketing célokra való feldolgozásához
                            az ALPHA-OMEGA REAL SOLUTIONS s.r.o.  által.
                            <br>
                            Súhlasím so spracovaním mojich osobných údajov firmou
                            ALPHA-OMEGA REAL SOLUTIONS s.r.o. na marketingové účely.
                        </div>

                    </div>

                    <div class="row" style="margin-top: 40px">
                        <div class="col-sm-12" style="text-align: center">
                            <button type="submit" class="btn-sm">Regisztrálok / Registrovať sa</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$css = <<<CSS
.dual-container {
    margin: 20px auto;
    width: 70%;
}
.error-msg {
    font-size: 0.8em; 
    color: red;
}
.error-border {
    border-color: red;
}
h4 {
    font-weight: bold;
    font-size: 12pt;
    margin-bottom: 20px;
}
.head-note {
    font-size: 0.95em; 
    font-style: italic;
    text-align: justify;  
    color: rgb(128, 0, 0); 
    margin-bottom: 20px;
}
CSS;
$this->registerCss($css);
$urlMesta = Url::to(["/open-days/get-cities"]);
$urlSkoly = Url::to(['/open-days/get-primary-schools']);
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$js = <<<JS
    $('#sel2-city').select2({
        theme: "bootstrap",
        minimumInputLength: 3,
        tags: true,
        ajax: {
            url: '{$urlMesta}',
            method: 'post',
            dataType: 'json',
            delay: 250,
            data: function(params){
                return {
                    q: params.term
                }
            },
            processResults: function(data,params) {
                return {
                    results: $.map( data.items, function(val,ind){ return {id: ind, text: val};})   
                };
            }
            
        }
    });

    $('#sel2-city').on('select2:select', function(e){
       let schoolId = e.params.data.id;
       $.ajax({
            url: '{$urlSkoly}',
            type: 'post',
            data: { sid: schoolId, {$csrf} },
            success: function (data) {
                $('#sel-school').empty();
                $.each(data.items, function(k, v){
                    $('#sel-school').append($('<option>', {value: v.id, text: v.text}));
                });
            }
       });
    });

    $(document).on('blur', '.rq', function() {
        let v = $(this).val() === undefined ? '' : $(this).val().trim();
        if (v.length > 0) {
            let i = $(this).data('eid');
            $(this).removeClass('error-border');
            $('#ep-' + i).html('');
        } 
    });
    
    $(document).on('submit', '#t009', function () {
        var emptyVar = 0;
        $('.rq').each(function () {
            let s = $(this).val().trim();
            if (s.length === 0) {
                let x = $(this).data('eid');
                ++emptyVar;
                $('#ep-' + x).html('Povinné! / Kötelező!');
                $('.ef-' + x).addClass('error-border');
            }
        });
        
        if (emptyVar > 0) {
            return false;
        }
        return true; 
    });
    
    function validateEmail(email) {
      const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
      if (email === undefined) {
          return true;
      }
      return emailRegex.test(email);
    }
    
    function checkEmail()
    {
        let e1 = $('#e-1').val().trim();
        let e2 = $('#e-2').val().trim();
        let e2_id = $('#e-2').data('eid');
        if (e1 !== e2) {
            $('#ep-' + e2_id).html('A két email nem egyezik! / Emaily sa nezhodujú!');
            $('.ef-' + e2_id).addClass('error-border');
        }
    }
    
    $(document).on('blur', '#e-1', function () {
        let e = $(this).val().trim();
        let eid = $(this).data('eid');
        if (validateEmail(e) === false) {
            $('#ep-' + eid).html('Hibás email! / Emaily má zlý formát!');
            $('.ef-' + eid).addClass('error-border');
            return false;
        }
        checkEmail();
    });
    
    $(document).on('blur', '#e-2', function () {
        let e = $(this).val().trim();
        let eid = $(this).data('eid');
        if (validateEmail(e) === false) {
            $('#ep-' + eid).html('Hibás email! / Emaily má zlý formát!');
            $('.ef-' + eid).addClass('error-border');
            return false;
        }
        checkEmail();
    });
    
    $(document).on('change', '#c1', function () {
        $('#t-1').val('');
        $('#t-2').val('');
        $('#t-3').val('');
        $('#t-4').val('');
        $('#company').hide();
    }); 
    $(document).on('change', '#c2', function () {
        $('#t-1').val('ALPHA-OMEGA REAL SOLUTIONS s. r. o.');
        $('#t-2').val('Mgr. Balázs Szabó');
        $('#t-3').val('info@aosolutions.sk');
        $('#t-4').val('+421948009989');
        $('#company').show();
    });
    $(document).on('change', '#c3', function () {
        $('#t-1').val('');
        $('#t-2').val('');
        $('#t-3').val('');
        $('#t-4').val('');
        $('#company').show();
    })
    $('#p-2').on('blur', function () {
        let p1= $('#p-1').val().trim();
        let p2 = $(this).val().trim();
        let eid= $(this).data('eid');
        if (p1 !== p2) {
            $('#ep-' + eid).html('A telefonszámok nem egyeznek! / Telefónne čísla sa nezhodujú!');
            $('.ef-' + eid).addClass('error-border');
        } else {
            
        }
    });
JS;
$this->registerJs($js);
