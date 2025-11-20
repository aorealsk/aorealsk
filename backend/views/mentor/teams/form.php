<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $model common\models\Team */
/* @var $studentMap array */
/* @var $isUpdate bool */

$this->title = $isUpdate ? 'Upraviť tím' : 'Vytvoriť tím';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin([
    'options' => ['id' => 'team-form'],
    'enableClientValidation' => true,
]); ?>

<?= $form->errorSummary($model) ?>

<div id="noStudentAlert" class="alert alert-warning d-none" role="alert">
    Prosím, vyberte aspoň jedného študenta.
</div>

<?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Názov tímu']) ?>
<?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'Popis']) ?>

<?= $form->field($model, 'studentIds')->checkboxList(
    $studentMap,
    [
        'unselect'    => null,                         // don’t send the hidden empty value
        'listOptions' => ['class' => 'row student-grid'], // grid container
        'item'        => function ($index, $label, $name, $checked, $value) use ($model) {
            // render each item as a grid cell with some spacing
            $id   = Html::getInputId($model, 'studentIds') . '-' . $index;
            $opts = ['id' => $id, 'value' => $value, 'class' => 'align-middle'];
            if ($index === 0) $opts['required'] = true; // HTML5 required on first item for native UX

            $checkbox = Html::checkbox($name, $checked, $opts);
            $labelTag = Html::label(' ' . Html::encode($label), $id, ['class' => 'mb-0']);

            // 2 cols on sm, 3 cols on md+, with bottom margin for breathing room
            return Html::tag('div', $checkbox . $labelTag, ['class' => 'col-sm-6 col-md-4 mb-2']);
        },
    ]
)->hint('Vyberte aspoň jedného študenta.'); ?>

<div class="form-group mt-3">
    <?= Html::submitButton($isUpdate ? 'Uložiť' : 'Vytvoriť', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Späť na zoznam', ['teams'], ['class' => 'btn btn-light']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
// light CSS to space label text from the checkbox
$this->registerCss(<<<CSS
.student-grid label { display: inline-block; margin-left: .4rem; }
CSS);

// extra client guard: show warning and prevent submit if none selected
$js = <<<JS
(function(){
  var form = document.getElementById('team-form');
  if (!form) return;
  form.addEventListener('submit', function(e){
    var boxes = form.querySelectorAll('input[name="Team[studentIds][]"]');
    var anyChecked = false;
    for (var i = 0; i < boxes.length; i++) {
      if (boxes[i].checked) { anyChecked = true; break; }
    }
    if (!anyChecked) {
      e.preventDefault();
      var al = document.getElementById('noStudentAlert');
      if (al) {
        al.classList.remove('d-none');
        al.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  });
})();
JS;
$this->registerJs($js);
?>
