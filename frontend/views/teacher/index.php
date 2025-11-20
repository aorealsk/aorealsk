<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;

$this->title = 'Teacher Registration';
?>
<main class="site-applicant">
  <input type="hidden" id="client_id" value="0">

  <!-- bannerek + breadcrumbs mintánál -->
  <div class="page-banner d-block position-relative raleway">
    <canvas style="background-image:url('/images/contact-us-banner-1.jpg');" width="1600" height="400"></canvas>
    <div class="page-border container-default d-block position-absolute mx-auto">
      <div class="page_title_line_left d-inline-block position-absolute background-gold-before background-gold-after"></div>
      <div class="page_title_line_right d-inline-block position-absolute background-gold-before background-gold-after"></div>
    </div>
    <div class="page-title container-default d-block position-absolute mx-auto">
      <div class="container-fluid">
        <div class="titlewrapper">
          <h1 class="entry-title">
            <strong><?= Html::encode($this->title) ?></strong>
          </h1>
        </div>
      </div>
    </div>
    <div class="breadcrumbs-container">
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-xs-12">
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ?? []]); ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="dual-container">
      <form method="post" role="form" id="teacher_form">
        <input type="hidden" name="Reg[type]" value="teacher">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

        <h3 style="margin-bottom:0">Základné údaje / Alapadatok</h3>
        <h5 class="head-note">* kötelező</h5>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12 red-star">Meno / Keresztnév</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control rq ef-1" name="Reg[teacher][first_name]" data-eid="1">
            <p class="error-msg" id="ep-1"></p>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12 red-star">Priezvisko / Vezetéknév</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control rq ef-2" name="Reg[teacher][last_name]" data-eid="2">
            <p class="error-msg" id="ep-2"></p>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Pohlavie / Nem</label>
          <div class="col-sm-9 col-12">
            <select class="form-control" name="Reg[teacher][gender]">
              <option value=""></option>
              <option value="m">mužské / férfi</option>
              <option value="f">ženské / nő</option>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-12 col-form-label red-star">Dátum narod. / Szül. dátum</label>
          <div class="col-sm-3 col-12">
            <select name="Reg[teacher][birthdate][day]" class="form-control rq ef-3" data-eid="3">
              <option value="">Deň / Nap</option>
              <?php for ($i=1; $i<32; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select><p class="error-msg" id="ep-3"></p>
          </div>
          <div class="col-sm-3 col-12">
            <select name="Reg[teacher][birthdate][month]" class="form-control rq ef-4" data-eid="4">
              <option value="">Mesiac / Hónap</option>
              <?php for ($i=1; $i<13; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select><p class="error-msg" id="ep-4"></p>
          </div>
          <div class="col-sm-3 col-12">
            <select name="Reg[teacher][birthdate][year]" class="form-control rq ef-5" data-eid="5">
              <option value="">Rok / Év</option>
              <?php $thisYear = date('Y'); for ($i=$thisYear-18; $i>1940; $i--): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
              <?php endfor; ?>
            </select><p class="error-msg" id="ep-5"></p>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Výška / Magasság (cm)</label>
          <div class="col-sm-9 col-12">
            <input type="number" step="0.1" class="form-control" name="Reg[teacher][height]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Hmotnosť / Súly (kg)</label>
          <div class="col-sm-9 col-12">
            <input type="number" step="0.1" class="form-control" name="Reg[teacher][weight]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Veľkosť obuvy / Cipőméret</label>
          <div class="col-sm-9 col-12">
            <select class="form-control" name="Reg[teacher][foot_size]">
              <option value=""></option>
              <?php for ($i=36; $i<=48; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Košelu veľkosť (EU) / Ing méret</label>
          <div class="col-sm-9 col-12">
            <select class="form-control" name="Reg[teacher][shirt_size]">
              <option value=""></option>
              <?php for ($i=44; $i<=60; $i+=2): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Obvod pásu / Derékbőség</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][waist]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Nohavice hossza / Nadrág hossza</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][trouser_length]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">IBAN</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][iban]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-12 col-form-label">Anyanyelv / Materinský jazyk</label>
          <div class="col-sm-9 col-12">
            <select name="Reg[teacher][primary_language]" class="form-control">
              <option value=""></option>
              <option value="magyar">magyar</option>
              <option value="slovenský">slovenský</option>
              <option value="other">other</option>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-12 col-form-label">Nyelvek (vesszővel) / Jazyky (čiarkou)</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][languages]" placeholder="pl.: magyar, angol, német">
          </div>
        </div>

        <h3 style="margin-bottom:0;">Kontaktné údaje / Elérhetőségek</h3>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Ulica / Utca</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][contact_street]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Číslo / Házszám</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][contact_building_nr]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Mesto / Város</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][contact_town]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">PSČ / Irányítószám</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][contact_town_id]">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12">Krajina / Ország</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][contact_country]" value="Slovensko">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12 red-star">Email</label>
          <div class="col-sm-9 col-12">
            <input type="email" class="form-control rq ef-7" name="Reg[teacher][email]" data-eid="7">
            <p class="error-msg" id="ep-7"></p>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label col-12 red-star">Telefón</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control rq ef-8" name="Reg[teacher][phone]" data-eid="8">
            <p class="error-msg" id="ep-8"></p>
          </div>
        </div>

        <h3 style="margin-top:40px;margin-bottom:0;">Škola a odbor / Iskola és szak</h3>
        <h5 class="head-note">Minden mező kötelező</h5>

        <div class="form-group row">
          <label class="col-form-label col-sm-3 col-12 red-star">Moja škola / Iskolám</label>
          <div class="col-sm-9 col-12">
            <select name="Reg[teacher][school]" class="form-control rq ef-9" id="t001" data-eid="9">
              <option value=""></option>
              <?php foreach ($schools as $school): ?>
                <option value="<?= $school->id ?>"><?= $school->partner_name ?></option>
              <?php endforeach; ?>
            </select>
            <p class="error-msg" id="ep-9"></p>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-form-label col-sm-3 col-12 red-star">Môj odbor / Szakom</label>
          <div class="col-sm-9 col-12">
            <select name="Reg[teacher][study_field]" id="stf01" class="form-control rq ef-10" data-eid="10">
              <option value=""></option>
            </select>
            <p class="error-msg" id="ep-10"></p>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-form-label col-sm-3 col-12">Osztályfőnök / Třídny učiteľ</label>
          <div class="col-sm-9 col-12">
            <input type="text" class="form-control" name="Reg[teacher][leader_of_class]" placeholder="(pl. 2.B)">
          </div>
        </div>

        <div style="margin-top:40px" class="g-recaptcha" data-sitekey="6Lfs91EqAAAAACqBbzxjOxQ3nZItbdBSWh_fAarO"></div>

        <div class="row" style="margin-top: 30px">
          <div class="col-sm-12" style="text-align:center">
            <button type="submit" class="btn-sm">Registrovať / Regisztráció</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</main>

<?php
$csrf = "'" . Yii::$app->request->csrfParam . "':'" . Yii::$app->request->getCsrfToken() . "'";
$css = <<<CSS
:root { --mid-size-text: 1.5rem; --mid-star: 18pt; }
.dual-container { margin: 20px auto; width: 70%; }
.reg-button { border:2px solid black; border-radius:3px; }
.head-note { color: red; font-size: var(--mid-size-text); margin-bottom: 20px; }
label.red-star:after { color:red; font-size:var(--mid-star); content:" *"; margin:0; }
.error-msg { font-size:0.8em; color:red; }
.error-border { border-color:red; }
CSS;
$this->registerCss($css);

$js = <<<JS
// szak lista betöltése az iskolaválasztás után (meglévő endpointtal)
$(document).on('change', '#t001', function () {
  $.ajax({
    url: '/dual/get-study-fields',
    type: 'POST',
    dataType: 'json',
    data: { school: $(this).val(), {$csrf} },
    success: function (data) {
      var x = $('#stf01').empty();
      x.append($('<option></option>',{value:'', text:''}));
      if (data && data.list) {
        for (var i=0; i<data.list.length; i++) {
          // itt value az ID, label a név
          x.append($('<option></option>', { value: data.list[i].value, text: data.list[i].label }));
        }
      }
    }
  })
});

// kötelező mezők gyors kliensoldali ellenőrzése
$(document).on('blur change', '.rq', function() {
  var v = $(this).val().trim();
  if (v.length > 0) {
    var i = $(this).data('eid');
    $(this).removeClass('error-border');
    $('#ep-' + i).html('');
  }
});

$(document).on('submit', '#teacher_form', function () {
  var emptyVar = 0;
  $('.rq').each(function () {
    var s = $(this).val().trim();
    if (s.length === 0) {
      var x = $(this).data('eid');
      ++emptyVar;
      $('#ep-' + x).html('Povinné! / Kötelező!');
      $('.ef-' + x).addClass('error-border');
    }
  });

  if (emptyVar > 0) return false;

  var v = grecaptcha.getResponse();
  if (v.length === 0) return false;

  return true;
});
JS;
$this->registerJs($js);
