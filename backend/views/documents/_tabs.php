<?php
use yii\helpers\Url;

$active = Yii::$app->controller->action->id;
$cls = fn($id) => 'nav-link' . ($active === $id ? ' active' : '');
?>

<ul class="nav nav-tabs customtab m-b-20" role="tablist">
  <!-- 📄 Main list tab -->
  <li class="nav-item">
    <a class="<?= $cls('index') ?>" href="<?= Url::to(['/documents/index']) ?>">Zoznam</a>
  </li>

  <!-- 🆕 Add document -->
  <li class="nav-item">
    <a class="<?= $cls('add-document') ?>" href="<?= Url::to(['/documents/add-document']) ?>">Pridať dokument</a>
  </li>

  <!-- ✏️ Edit -->
  <li class="nav-item">
    <a class="<?= $cls('edit') ?>" href="<?= Url::to(['/documents/edit']) ?>">Upraviť</a>
  </li>


<!-- 🟩 Universal generation (REAL PAGE, not tab) -->
    <li class="nav-item">
        <a class="<?= $cls('contractor') ?>" href="<?= Url::to(['/autoshift/contractor']) ?>">
        Univerzálna generácia
        </a>
    </li>


</ul>
