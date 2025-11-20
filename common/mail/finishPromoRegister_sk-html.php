<?php
/**
 * @var $activationKey
 * @var $firstName
 * @var $lang
 */
use yii\helpers\Html;
$activationLink = Yii::$app->urlManager->createAbsoluteUrl("promo/activate/sk/{$activationKey}");
?>
<div class="account-activate">
    <p>Krásny deň <?= Html::encode($firstName) ?>,</p>

    <p>ďakujeme, že ste sa zaregistrovali na digitálny nápojový lístok Fašiangového bálu.</p>

    <p>Váš prístup môžete aktivovať kliknutím na <?= Html::a( 'túto linku',$activationLink) ?>.</p>

    <p>V prípade akýchkoľvek otázok píšte na emailovú adresu promo@aoreal.sk.</p>

    <p>Prajeme Vám príjemné chvíle!</p>

    <p>Organizátori</p>
</div>