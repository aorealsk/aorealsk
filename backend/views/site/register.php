<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RegistrationForm */ // adjust if needed

$this->title = 'Regisztráció';
?>

<style>
/* ====== Aesthetic polish (scoped to .auth-wrap) ====== */
.auth-bg {
    min-height: 100vh;
    background:
        linear-gradient(135deg, rgba(42, 64, 232, .12), rgba(0, 0, 0, .12)),
        url('../assets/images/background/login-register.jpg') center/cover no-repeat fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px 16px;
}
.auth-wrap {
    max-width: 880px;
    width: 100%;
}
.card-glass {
    background: rgba(255,255,255,.92);
    border-radius: 18px;
    box-shadow: 0 20px 45px rgba(0,0,0,.15);
    overflow: hidden;
    backdrop-filter: blur(12px);
}
.card-head {
    padding: 28px 28px 12px;
    border-bottom: 1px solid rgba(0,0,0,.06);
}
.card-head h1 {
    margin: 0;
    font-weight: 700;
    letter-spacing: .3px;
}
.card-sub {
    color: #6c757d;
    margin-top: 6px;
}
.card-body {
    padding: 24px 28px 28px;
}
.help-block {
    margin-top: 6px;
    font-size: 12px;
}
.form-control {
    height: 44px;
    font-size: 15px;
}
.input-group-addon, .input-group-text {
    min-width: 44px;
    text-align: center;
}
.btn-xl {
    height: 48px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 10px;
}
.hr-text {
    position: relative;
    text-align: center;
    margin: 24px 0;
}
.hr-text:before {
    content: '';
    position: absolute;
    left: 0; right: 0; top: 50%;
    border-top: 1px solid rgba(0,0,0,.08);
}
.hr-text span {
    position: relative;
    padding: 0 10px;
    background: rgba(255,255,255,.92);
    color: #6c757d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .08em;
}
.badge-soft {
    background: rgba(0,0,0,.06);
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    color: #555;
}
.pw-meter {
    height: 6px;
    border-radius: 6px;
    background: #e9ecef;
    overflow: hidden;
    margin-top: 6px;
}
.pw-meter > div {
    height: 100%;
    width: 0%;
    transition: width .25s ease, background-color .25s ease;
}
.show-toggle {
    cursor: pointer;
    user-select: none;
}
@media (max-width: 767px) {
    .card-body { padding: 20px; }
}
</style>

<div class="auth-bg">
    <div class="auth-wrap">
        <div class="card-glass">
            <div class="card-head">
                <h1><?= Html::encode($this->title) ?></h1>
                <div class="card-sub">A regisztrációhoz kérem töltse ki az adatmezőket.</div>
            </div>

            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-register',
                    'enableClientValidation' => true,
                    'enableAjaxValidation' => false,
                    'options' => ['autocomplete' => 'on'],
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                        'labelOptions' => ['class' => 'control-label'],
                        'errorOptions' => ['class' => 'help-block text-danger'],
                    ],
                ]); ?>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'username', [
                            'template' => "<label class='control-label'>Felhasználónév</label><div class='input-group'>{input}<span class='input-group-addon'><span class='glyphicon glyphicon-user'></span></span></div>\n{error}"
                        ])->textInput([
                            'maxlength' => true,
                            'autocomplete' => 'username',
                            'placeholder' => 'Felhasználónév',
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'email', [
                            'template' => "<label class='control-label'>Email-cím</label><div class='input-group'>{input}<span class='input-group-addon'><span class='glyphicon glyphicon-envelope'></span></span></div>\n{error}"
                        ])->input('email', [
                            'maxlength' => true,
                            'autocomplete' => 'email',
                            'placeholder' => 'emailcím@aoreal.sk',
                        ]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'password', [
                            'template' => "<label class='control-label'>Jelszó</label>
                                           <div class='input-group'>
                                               {input}
                                               <span class='input-group-addon show-toggle' id='pw-toggle' title='Show/Hide'>
                                                   <span class='glyphicon glyphicon-eye-open'></span>
                                               </span>
                                           </div>
                                           <div class='pw-meter'><div id='pw-bar'></div></div>
                                           <div id='pw-hint' class='help-block'></div>
                                           {error}"
                        ])->passwordInput([
                            'maxlength' => true,
                            'autocomplete' => 'new-password',
                            'placeholder' => 'Legalább 6 karakter',
                            'id' => 'pw-input',
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'name_first')->textInput([
                            'maxlength' => true,
                            'autocomplete' => 'given-name',
                            'placeholder' => 'Keresztnév',
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'name_last')->textInput([
                            'maxlength' => true,
                            'autocomplete' => 'family-name',
                            'placeholder' => 'Vezetéknév',
                        ]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'birthdate', [
                            'template' => "<label class='control-label'>Születési adatok</label><div class='input-group'>{input}<span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span></span></div>\n{error}"
                        ])->input('date', [
                            'placeholder' => 'ÉV-HÓNAP-NAP',
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'phone', [
                            'template' => "<label class='control-label'>Telefonszám</label><div class='input-group'>{input}<span class='input-group-addon'><span class='glyphicon glyphicon-earphone'></span></span></div>\n{error}"
                        ])->textInput([
                            'maxlength' => true,
                            'autocomplete' => 'tel',
                            'placeholder' => '+421 908 123 456',
                            'pattern' => '\+?[0-9\s\-\(\)]{7,20}',
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label">Fontos:</label>
                        <div class="badge-soft">Kérjük hiteles adatokat adjon meg, beleértve az elérhetőségeit is!</div>
                    </div>
                </div>

                <div class="hr-text"><span>Köszönjük</span></div>

                <div class="row">
                    <div class="col-sm-6">
                        <?= Html::a('Vissza a bejelentkezéshez', ['/site/login'], [
                            'class' => 'btn btn-default btn-xl btn-block',
                            'data-pjax' => 0,
                        ]) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= Html::submitButton('Fiók létrehozása', [
                            'class' => 'btn btn-info btn-xl btn-block text-white',
                        ]) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
/* ====== Lightweight password strength + toggle (no extra libs) ====== */
$js = <<<JS
(function(){
    var input = document.getElementById('pw-input');
    var bar   = document.getElementById('pw-bar');
    var hint  = document.getElementById('pw-hint');
    var toggle= document.getElementById('pw-toggle');

    if(!input || !bar || !hint || !toggle) return;

    function score(pw){
        if(!pw) return 0;
        var s = 0;
        if(pw.length >= 6) s += 1;
        if(pw.length >= 10) s += 1;
        if(/[a-z]/.test(pw) && /[A-Z]/.test(pw)) s += 1;
        if(/[0-9]/.test(pw)) s += 1;
        if(/[^A-Za-z0-9]/.test(pw)) s += 1;
        return Math.min(s,5);
    }
    function render(){
        var val = input.value;
        var sc = score(val);
        var pct = (sc/5)*100;
        bar.style.width = pct + '%';
        var color = '#e74c3c'; // red
        if(sc >= 2) color = '#f39c12'; // orange
        if(sc >= 3) color = '#f1c40f'; // yellow
        if(sc >= 4) color = '#27ae60'; // green
        if(sc >= 5) color = '#2ecc71'; // bright green
        bar.style.backgroundColor = color;

        var msg = 'Túl gyenge';
        if(sc==2) msg='Gyenge';
        if(sc==3) msg='Jó';
        if(sc==4) msg='Erős';
        if(sc==5) msg='Tökéletes';
        hint.textContent = val.length ? ('Jelszó nehézsége: ' + msg) : '';
    }
    input.addEventListener('input', render);
    render();

    toggle.addEventListener('click', function(){
        var icon = this.querySelector('.glyphicon');
        if(input.type === 'password'){
            input.type = 'text';
            if(icon){ icon.className = 'glyphicon glyphicon-eye-close'; }
        }else{
            input.type = 'password';
            if(icon){ icon.className = 'glyphicon glyphicon-eye-open'; }
        }
    });
})();
JS;
$this->registerJs($js);
?>
