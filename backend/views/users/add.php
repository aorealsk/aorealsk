<?php
use backend\assets\RealAsset;
use yii\helpers\Url;

$this->title= Yii::t('app','Pridať užívateľa');

$this->registerCSSFile('@web/assets/dist/css/pages/tab-page.css',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/js/users.js',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/select2/dist/js/select2.full.min.js',['depends'=>RealAsset::class]);
$this->registerJSFile('@web/assets/node_modules/bootstrap-select/bootstrap-select.min.js',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/select2/dist/css/select2.min.css',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/bootstrap-select/bootstrap-select.min.css',['depends'=>RealAsset::class]);
$this->registerCSSFile('@web/assets/dist/css/style.min.css',['depends'=>RealAsset::class]);

$errorUserNameNeeded   = Yii::t('app','Vyplňte užívateľské meno. Minimálna dĺžka je 3 znaky!');
$errorTooShortPassword = Yii::t('app','Heslo je príliž krátke. Minimálna dĺžka je 5 znakov!');
$errorPasswordNotMatch = Yii::t('app','Heslá sa nezhodujú!');
$errorPhone            = Yii::t('app','Neplatné telefónne číslo!');
$errorEmail            = Yii::t('app','Neplatný email!');
$errorIban             = Yii::t('app','Neplatný IBAN formát (napr. SK..)!');
?>
<div class="container-fluid">
  <div class="row page-titles">
    <div class="col-md-12 col-xs-12 align-self-center">
      <h4 class="text-themecolor"><?= $this->title ?></h4>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card rounded-5 card-shadow">
        <div class="card-body">
          <form method="post" role="form" id="user-reg">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="vtabs customvtab">
              <ul class="nav nav-tabs tabs-vertical" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" href="#userdetails" role="tab">
                    <span class="hidden-sm-up"><i class="mdi mdi-account"></i></span>
                    <span class="hidden-xs-down"><?= Yii::t('app','Základné údaje') ?></span>
                    <span class="badge badge-xs badge-danger" id="bdg-details">5</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" href="#accesses" role="tab">
                    <span class="hidden-sm-up"><i class="ti-package"></i></span>
                    <span class="hidden-xs-down"><?= Yii::t('app','Prístupy') ?></span>
                  </a>
                </li>
              </ul>

              <div class="tab-content">
                <div class="tab-pane active" id="userdetails" role="tabpanel">

                  <!-- Username -->
                  <div class="row">
                    <div class="col-md-12 col-xs-12 form-group has-danger">
                      <label class="control-label"><?= Yii::t('app','Používateľské meno') ?></label>
                      <input type="text" class="form-control form-control-danger" name="User[username]" id="inp-username" required>
                      <small class="form-control-feedback"><?= $errorUserNameNeeded ?></small>
                    </div>
                  </div>

                  <!-- Password (use newPassword fields to match model) -->
                  <div class="row">
                    <div class="col-md-6 col-xs-6 form-group has-danger">
                      <label for="pass" class="control-label"><?= Yii::t('app','Heslo') ?></label>
                      <input type="password" name="User[newPassword]" class="form-control form-control-danger" id="pass" required>
                      <small class="form-control-feedback"><?= Yii::t('app','Vyplňte heslo!') ?></small>
                    </div>
                    <div class="col-md-6 col-xs-6 form-group has-danger">
                      <label class="control-label"><?= Yii::t('app','Zopakovat heslo') ?></label>
                      <input type="password" name="User[newPasswordRepeat]" id="pass-test" class="form-control form-control-danger" required>
                      <small class="form-control-feedback"></small>
                    </div>
                  </div>

                  <!-- Name -->
                  <div class="row">
                    <div class="col-md-6 col-xs-6 form-group">
                      <label class="control-label"><?= Yii::t('app','Meno') ?></label>
                      <input type="text" class="form-control" name="User[name_first]">
                    </div>
                    <div class="col-md-6 col-xs-6 form-group">
                      <label class="control-label"><?= Yii::t('app','Priezvisko') ?></label>
                      <input type="text" name="User[name_last]" class="form-control">
                    </div>
                  </div>

                  <!-- Birthdate + Contact -->
                  <div class="row">
                    <div class="col-md-4 col-xs-6 form-group">
                      <label class="control-label"><?= Yii::t('app','Dátum narodenia') ?></label>
                      <input type="date" name="User[birthdate]" class="form-control" id="inp-birthdate" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-md-4 col-xs-6 form-group has-danger">
                      <label class="control-label"><?= Yii::t('app','Telefón') ?></label>
                      <input type="text" name="User[phone]" class="form-control form-control-danger" id="inp-phone" required>
                      <small class="form-control-feedback"><?= Yii::t('app','Vyplňte telefon') ?></small>
                    </div>
                    <div class="col-md-4 col-xs-6 form-group has-danger">
                      <label class="control-label">Email</label>
                      <input type="text" name="User[email]" class="form-control form-control-danger" id="inp-email" required>
                      <small class="form-control-feedback"><?= Yii::t('app','Vyplňte email') ?></small>
                    </div>
                  </div>

                  <!-- Address + IBAN -->
                  <hr class="my-3">
                  <h5 class="text-muted mb-3"><?= Yii::t('app','Adresa a IBAN') ?></h5>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label class="control-label"><?= Yii::t('app','Ulica') ?></label>
                      <input type="text" name="User[street]" class="form-control" id="inp-street">
                    </div>
                    <div class="col-md-3 form-group">
                      <label class="control-label"><?= Yii::t('app','Číslo domu') ?></label>
                      <input type="text" name="User[street_no]" class="form-control" id="inp-streetno">
                    </div>
                    <div class="col-md-3 form-group">
                      <label class="control-label"><?= Yii::t('app','PSČ') ?></label>
                      <input type="text" name="User[zip]" class="form-control" id="inp-zip">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 form-group">
                      <label class="control-label"><?= Yii::t('app','Mesto') ?></label>
                      <input type="text" name="User[city]" class="form-control" id="inp-city">
                    </div>
                    <div class="col-md-6 form-group">
                      <label class="control-label">IBAN</label>
                      <input type="text" name="User[iban]" class="form-control" id="inp-iban" placeholder="SK..">
                      <small class="form-control-feedback d-none" id="iban-feedback"><?= $errorIban ?></small>
                    </div>
                  </div>

                  <!-- Group / Commission / Offices -->
                  <hr class="my-3">
                  <div class="row">
                    <div class="col-md-12 form-group">
                      <label class="control-label"><?= Yii::t('app','Grupa') ?></label>
                      <select name="User[auth_assignment]" id="inp-group" class="form-control">
                        <option value="">Vyberte si...</option>
                        <?php foreach($usergroups as $group): ?>
                          <option value="<?= $group['name']?>"><?= $group['name']?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <div class="row" id="inp-commission">
                    <div class="col-md-12">
                      <label class="control-label m-t-10 m-b-10"><?= Yii::t('app','Provízia') ?></label>
                      <table class="table-sm table-striped table-hover w-100 table-bordered">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><?= Yii::t('app','Tržba od') ?></th>
                            <th><?= Yii::t('app','Tržba do') ?></th>
                            <th><?= Yii::t('app','Percento z predaja') ?></th>
                            <th><?= Yii::t('app','Percento z kúpy') ?></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach($commissions as $item): ?>
                            <tr>
                              <td><input type="radio" name="User[commission]" value="<?= $item['id'] ?>"></td>
                              <td><?= $item['trzba_od'] ?></td>
                              <td><?= $item['trzba_do'] ?></td>
                              <td><?= $item['predavajuci_percento'] ?></td>
                              <td><?= $item['kupujuci_percento'] ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 form-group">
                      <label class="control-label"><?= Yii::t('app','Kancelárie') ?></label>
                      <select name="User[office_id][]" class="select2 select2-multiple" style="width: 100%" multiple="multiple" data-placeholder="Choose">
                        <option value=""><?= Yii::t('app','Vyberte si...') ?></option>
                        <?php foreach($offices as $office): ?>
                          <option value="<?= $office['id']?>"><?= $office['name']?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>

                  <!-- Guardians (auto required if minor) -->
                  <hr class="my-3">
                  <h5 class="text-muted mb-2"><?= Yii::t('app','Zákonní zástupcovia') ?></h5>
                  <p class="text-muted small mb-3">
                    <?= Yii::t('app','Ak je používateľ mladší ako 18 rokov, obaja zástupcovia sú povinní: meno a telefón alebo e-mail.') ?>
                  </p>

                  <div id="guardians-wrap" class="d-none">
                    <!-- Guardian 1 -->
                    <div class="border rounded p-3 mb-3">
                      <h6 class="mb-3"><?= Yii::t('app','Zástupca #1') ?></h6>
                      <div class="row">
                        <div class="col-md-6 form-group">
                          <label class="control-label"><?= Yii::t('app','Meno') ?></label>
                          <input type="text" name="Guardian[0][name]" class="form-control g1-name">
                        </div>
                        <div class="col-md-3 form-group">
                          <label class="control-label"><?= Yii::t('app','Vzťah') ?></label>
                          <input type="text" name="Guardian[0][relation]" class="form-control g1-req">
                        </div>
                        <div class="col-md-3 form-group">
                          <label class="control-label">Email</label>
                          <input type="text" name="Guardian[0][email]" class="form-control g1-email">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3 form-group">
                          <label class="control-label"><?= Yii::t('app','Telefón') ?></label>
                          <input type="text" name="Guardian[0][phone]" class="form-control g1-phone">
                        </div>
                        <div class="col-md-5 form-group">
                          <label class="control-label"><?= Yii::t('app','Ulica') ?></label>
                          <input type="text" name="Guardian[0][street]" class="form-control g1-req">
                        </div>
                        <div class="col-md-2 form-group">
                          <label class="control-label"><?= Yii::t('app','Číslo domu') ?></label>
                          <input type="text" name="Guardian[0][street_no]" class="form-control g1-req">
                        </div>
                        <div class="col-md-2 form-group">
                          <label class="control-label"><?= Yii::t('app','PSČ') ?></label>
                          <input type="text" name="Guardian[0][zip]" class="form-control g1-req">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4 form-group">
                          <label class="control-label"><?= Yii::t('app','Mesto') ?></label>
                          <input type="text" name="Guardian[0][city]" class="form-control g1-req">
                        </div>
                      </div>
                    </div>

                    <!-- Guardian 2 -->
                    <div class="border rounded p-3">
                      <h6 class="mb-3"><?= Yii::t('app','Zástupca #2') ?></h6>
                      <div class="row">
                        <div class="col-md-6 form-group">
                          <label class="control-label"><?= Yii::t('app','Meno') ?></label>
                          <input type="text" name="Guardian[1][name]" class="form-control g2-name">
                        </div>
                        <div class="col-md-3 form-group">
                          <label class="control-label"><?= Yii::t('app','Vzťah') ?></label>
                          <input type="text" name="Guardian[1][relation]" class="form-control g2-req">
                        </div>
                        <div class="col-md-3 form-group">
                          <label class="control-label">Email</label>
                          <input type="text" name="Guardian[1][email]" class="form-control g2-email">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-3 form-group">
                          <label class="control-label"><?= Yii::t('app','Telefón') ?></label>
                          <input type="text" name="Guardian[1][phone]" class="form-control g2-phone">
                        </div>
                        <div class="col-md-5 form-group">
                          <label class="control-label"><?= Yii::t('app','Ulica') ?></label>
                          <input type="text" name="Guardian[1][street]" class="form-control g2-req">
                        </div>
                        <div class="col-md-2 form-group">
                          <label class="control-label"><?= Yii::t('app','Číslo domu') ?></label>
                          <input type="text" name="Guardian[1][street_no]" class="form-control g2-req">
                        </div>
                        <div class="col-md-2 form-group">
                          <label class="control-label"><?= Yii::t('app','PSČ') ?></label>
                          <input type="text" name="Guardian[1][zip]" class="form-control g2-req">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4 form-group">
                          <label class="control-label"><?= Yii::t('app','Mesto') ?></label>
                          <input type="text" name="Guardian[1][city]" class="form-control g2-req">
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- /guardians -->

                </div>

                <div class="tab-pane" id="accesses" role="tabpanel">
                  <!-- későbbre: jogosultságok -->
                </div>
              </div>
            </div>

            <div class="row m-t-30">
              <div class="col-xs-12 col-md-10 offset-2">
                <button type="submit" class="btn btn-success mr-1 text-white">
                  <i class="mdi mdi-content-save m-r-5"></i><?= Yii::t('app','Uložiť') ?>
                </button>
                <a class="btn btn-danger text-white" href="<?= Url::to(['/users']) ?>">
                  <i class="mdi mdi-step-backward m-r-5"></i><?= Yii::t('app','Späť') ?>
                </a>
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$js = <<<JS
  $(".select2").select2();

  function isValidEmail(v){ return /^\\S+@\\S+\\.[\\w\\-]{2,}$/i.test(v); }
  function isValidIban(v){
    if(!v) return true; // optional
    v = v.replace(/\\s+/g,'').toUpperCase();
    if(!/^[A-Z]{2}[0-9A-Z]{13,30}$/.test(v)) return false;
    var rep = v.slice(4) + v.slice(0,4);
    var num = '';
    for (var i=0;i<rep.length;i++){
      var c = rep.charCodeAt(i);
      if(c>=65 && c<=90) num += (c-55); else num += rep[i];
      if(num.length>9){ num = (parseInt(num,10)%97).toString(); }
    }
    return parseInt(num,10)%97 === 1;
  }

  // basic inline validations you had
  $('#inp-username').on('blur',function(){
    if ($(this).val().length>3) { removeErrorMessage($(this),$('#bdg-details')); }
    else { addErrorMessage($('#user-reg'),$(this),$('#bdg-details'),'{$errorUserNameNeeded}'); }
  });
  $('#pass').on('blur',function(){
    if ($(this).val().length < 5) {
      addErrorMessage($('#user-reg'),$(this),$('#bdg-details'),'{$errorTooShortPassword}');
    } else { removeErrorMessage($(this),$('#bdg-details')); }
  });
  $('#pass-test').on('blur',function(){
    var ok = $('#pass').val() === $(this).val();
    if ($(this).val().length < 5) {
      addErrorMessage($('#user-reg'),$(this),$('#bdg-details'),'{$errorTooShortPassword}');
    } else if (ok) {
      removeErrorMessage($(this),$('#bdg-details'));
    } else {
      addErrorMessage($('#user-reg'),$(this),$('#bdg-details'),'{$errorPasswordNotMatch}');
    }
  });
  $('#inp-phone').on('blur',function(){
    if ($(this).val().length < 5) {
      addErrorMessage($('#user-reg'),$(this),$('#bdg-details'),'{$errorPhone}');
    } else { removeErrorMessage($(this),$('#bdg-details')); }
  });
  $('#inp-email').on('blur',function(){
    if (!isValidEmail($(this).val())) {
      addErrorMessage($('#user-reg'),$(this),$('#bdg-details'),'{$errorEmail}');
    } else { removeErrorMessage($(this),$('#bdg-details')); }
  });
  $('#inp-iban').on('blur',function(){
    if (!isValidIban($(this).val())) {
      $('#iban-feedback').removeClass('d-none');
      addErrorMessage($('#user-reg'),$(this),$('#bdg-details'),'{$errorIban}');
    } else {
      $('#iban-feedback').addClass('d-none');
      removeErrorMessage($(this),$('#bdg-details'));
    }
  });

  // group -> commission toggle
  $('#inp-group').on('change',function(){
    if ($(this).val() == 'makler') { $('#inp-commission').show(); }
    else { $('#inp-commission').hide(); }
  });

  // ===== Guardians visibility + requirements when MINOR =====
  function isMinor(dateStr){
    if(!dateStr) return false;
    var d = new Date(dateStr);
    if (isNaN(d.getTime())) return false;
    var n = new Date();
    var age = n.getFullYear() - d.getFullYear();
    var m = n.getMonth() - d.getMonth();
    if (m < 0 || (m === 0 && n.getDate() < d.getDate())) age--;
    return age < 18;
  }
  function toggleGuardians(){
    var minor = isMinor($('#inp-birthdate').val());
    var wrap = $('#guardians-wrap');
    if (minor) wrap.removeClass('d-none'); else wrap.addClass('d-none');
  }
  $('#inp-birthdate').on('change', toggleGuardians);
  $(document).ready(toggleGuardians);

  // Require name + (phone or email) for BOTH guardians if minor
  $('#user-reg').on('submit', function(e){
    var minor = isMinor($('#inp-birthdate').val());
    if (!minor) return true;

    function checkOne(prefix){
      var name  = $.trim($(prefix+'-name').val());
      var phone = $.trim($(prefix+'-phone').val());
      var email = $.trim($(prefix+'-email').val());
      var ok = (name.length > 0) && (phone.length > 0 || isValidEmail(email));
      if (!ok) {
        $(prefix+'-name,'+prefix+'-phone,'+prefix+'-email').addClass('is-invalid');
      } else {
        $(prefix+'-name,'+prefix+'-phone,'+prefix+'-email').removeClass('is-invalid');
      }
      return ok;
    }
    var ok1 = checkOne('.g1');
    var ok2 = checkOne('.g2');
    if (!ok1 || !ok2) {
      e.preventDefault();
      alert('Pre osobu mladšiu ako 18 rokov vyplňte u oboch zástupcov meno a telefón alebo e-mail.');
      return false;
    }
  });
JS;

$this->registerJS($js);

$css = <<<CSS
  .vtabs { width: 100%; }
  .tabs-vertical { width: 200px !important; }
  #inp-commission { display: none; }
  .rounded-5 { border-radius: .5em!important; }
  .card-shadow { box-shadow: lightgrey 3px 3px; }
CSS;
$this->registerCSS($css);
