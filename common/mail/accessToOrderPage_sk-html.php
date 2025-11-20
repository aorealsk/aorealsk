<?php
/**
 * @var $orderKey
 * @var $firstName
 */
use yii\helpers\Html;
$activationLink = Yii::$app->urlManager->createAbsoluteUrl("promo/order/sk/{$orderKey}");
?>
<div class="account-activate">
    <p>Krásny deň <?= Html::encode($firstName) ?>,</p>

    <p>ďakujeme, že ste si aktivovali prístup k digitálnemu nápojovému lístku Fašiangového bálu.</p>

    <p>Vaše objednávky môžete zrealizovať kliknutím na <?= Html::a( 'túto linku',$activationLink) ?>.</p>

    <p>V prípade akýchkoľvek otázok píšte na emailovú adresu promo@aoreal.sk.</p>

    <p>Prajeme Vám príjemné chvíle!</p>

    <p>Organizátori</p>
</div>