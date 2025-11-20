<?php
/**
 * @var $orderKey
 * @var $firstName
 */
use yii\helpers\Html;
$activationLink = Yii::$app->urlManager->createAbsoluteUrl("promo/order/hu/{$orderKey}");
?>
<div class="account-activate">
    <p>Kedves <?= Html::encode($firstName) ?>,</p>

    <p>köszönjük, hogy aktiválta a Farsangi Bál <?= (new DateTime('now'))->format('Y') ?> digitális itallapjához a hozzáférését.</p>

    <p>Megrendeléseit az alábbi <?= Html::a('linkre',$activationLink) ?> kattintva adhatja le.</p>

    <p>Ha bármilyen kérdése lenne akkor írjon az promo@aoreal.sk email címre.</p>

    <p>Jó szórakozást kívánunk!</p>

    <p>A szervezők</p>
</div>