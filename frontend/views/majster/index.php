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

      <form method="post" role="form" id="majster_form">
        <input type="hidden" name="Reg[type]" value="majster">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

        <h3>Základné údaje / Alapadatok</h3>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label red-star">Meno / Keresztnév</label>
          <div class="col-sm-9">
            <input type="text" class="form-control rq ef-1" name="Reg[majster][first_name]" data-eid="1">
            <p class="error-msg" id="ep-1"></p>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label red-star">Priezvisko / Vezetéknév</label>
          <div class="col-sm-9">
            <input type="text" class="form-control rq ef-2" name="Reg[majster][last_name]" data-eid="2">
            <p class="error-msg" id="ep-2"></p>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Pohlavie / Nem</label>
          <div class="col-sm-9">
            <select class="form-control" name="Reg[majster][gender]">
              <option value=""></option>
              <option value="m">mužské / férfi</option>
              <option value="f">ženské / nő</option>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label red-star">Dátum narod. / Szül. dátum</label>
          <div class="col-sm-3">
            <select name="Reg[majster][birthdate][day]" class="form-control rq ef-3" data-eid="3">
              <option value="">Deň / Nap</option>
              <?php for ($i=1; $i<=31; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select><p class="error-msg" id="ep-3"></p>
          </div>
          <div class="col-sm-3">
            <select name="Reg[majster][birthdate][month]" class="form-control rq ef-4" data-eid="4">
              <option value="">Mesiac / Hónap</option>
              <?php for ($i=1; $i<=12; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select><p class="error-msg" id="ep-4"></p>
          </div>
          <div class="col-sm-3">
            <select name="Reg[majster][birthdate][year]" class="form-control rq ef-5" data-eid="5">
              <option value="">Rok / Év</option>
              <?php $y=date('Y'); for ($i=$y-18; $i>1940; $i--): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select><p class="error-msg" id="ep-5"></p>
          </div>
        </div>

        <!-- Mértékek -->
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Výška / Magasság (cm)</label>
          <div class="col-sm-9"><input type="number" step="0.1" class="form-control" name="Reg[majster][height]"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Hmotnosť / Súly (kg)</label>
          <div class="col-sm-9"><input type="number" step="0.1" class="form-control" name="Reg[majster][weight]"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Veľkosť obuvy / Cipőméret</label>
          <div class="col-sm-9">
            <select class="form-control" name="Reg[majster][foot_size]">
              <option value=""></option>
              <?php for ($i=36; $i<=48; $i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Košeľa (EU) / Ing méret</label>
          <div class="col-sm-9">
            <select class="form-control" name="Reg[majster][shirt_size]">
              <option value=""></option>
              <?php for ($i=44; $i<=60; $i+=2): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Obvod pásu / Derékbőség</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][waist]"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Nohavíc hossza / Nadrág hossza</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][trouser_length]"></div>
        </div>

        <!-- Pénzügy/nyelv -->
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">IBAN</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][iban]"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Anyanyelv / Materinský jazyk</label>
          <div class="col-sm-9">
            <select name="Reg[majster][primary_language]" class="form-control">
              <option value=""></option>
              <option value="magyar">magyar</option>
              <option value="slovenský">slovenský</option>
              <option value="other">other</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Nyelvek (vesszővel) / Jazyky (čiarkou)</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][languages]" placeholder="pl.: magyar, angol, német"></div>
        </div>

        <!-- Elérhetőség -->
        <h3>Kontaktné údaje / Elérhetőségek</h3>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Ulica / Utca</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][contact_street]"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Číslo / Házszám</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][contact_building_nr]"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Mesto / Város</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][contact_town]"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">PSČ / Irányítószám</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][contact_town_id]"></div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Krajina / Ország</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][contact_country]" value="Slovensko"></div>
        </div>

        <!-- Kapcsolat -->
        <div class="form-group row">
          <label class="col-sm-3 col-form-label red-star">Email</label>
          <div class="col-sm-9">
            <input type="email" class="form-control rq ef-7" name="Reg[majster][email]" data-eid="7">
            <p class="error-msg" id="ep-7"></p>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label red-star">Telefón</label>
          <div class="col-sm-9">
            <input type="text" class="form-control rq ef-8" name="Reg[majster][phone]" data-eid="8">
            <p class="error-msg" id="ep-8"></p>
          </div>
        </div>

        <!-- Tanulmányok / szakterület -->
        <h3>Végzettség és szakterület</h3>
        <div class="form-group row">
          <label class="col-sm-3 col-form-label">Utolsó befejezett iskola</label>
          <div class="col-sm-9"><input type="text" class="form-control" name="Reg[majster][last_finished_school]"></div>
        </div>

        <div class="form-group row">
          <label class="col-sm-3 col-form-label red-star">TraineeFor (Odbor)</label>
          <div class="col-sm-9">
            <select name="Reg[majster][trainee_for]" class="form-control rq ef-9" data-eid="9">
              <option value=""></option>
              <?php foreach ($fields as $f): ?>
                <option value="<?= $f['id'] ?>"><?= Html::encode($f['name']) ?> (<?= Html::encode($f['code']) ?>)</option>
              <?php endforeach; ?>
            </select>
            <p class="error-msg" id="ep-9"></p>
          </div>
        </div>

        <div style="margin-top:30px" class="g-recaptcha" data-sitekey="6Lfs91EqAAAAACqBbzxjOxQ3nZItbdBSWh_fAarO"></div>

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
$css = <<<CSS
.dual-container { margin:20px auto; width:70%; }
label.red-star:after { color:red; content:" *"; margin-left:2px; }
.error-msg { font-size:0.8em; color:red; }
.error-border { border-color:red; }
CSS;
$this->registerCss($css);

$js = <<<JS
// minimális kliensoldali kötelező ellenőrzés + reCAPTCHA
$(document).on('blur change', '.rq', function() {
  var v = $(this).val().trim();
  var i = $(this).data('eid');
  if (v.length > 0) { $(this).removeClass('error-border'); $('#ep-'+i).html(''); }
});

$(document).on('submit', '#majster_form', function () {
  var emptyVar = 0;
  $('.rq').each(function () {
    var s = $(this).val().trim();
    var x = $(this).data('eid');
    if (!s.length) { ++emptyVar; $('#ep-'+x).html('Povinné! / Kötelező!'); $('.ef-'+x).addClass('error-border'); }
  });
  if (emptyVar > 0) return false;
  var v = grecaptcha.getResponse();
  if (v.length === 0) return false;
  return true;
});
JS;
$this->registerJs($js);
