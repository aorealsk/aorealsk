<?php
/**
 * @var $activationKey
 * @var $firstName
 * @var $lang
 */
use yii\helpers\Html;
$activationLink = Yii::$app->urlManager->createAbsoluteUrl("promo/activate/hu/{$activationKey}");
?>
<div class="account-activate">
    <p>Kedves <?= Html::encode($firstName) ?>,</p>

    <p>köszönjük, hogy regisztrált a Farsangi Bál <?= (new DateTime('now'))->format('Y') ?> digitális itallapjára.</p>

    <p>Az alábbi <?= Html::a('linkre',$activationLink) ?> kattintva tudja aktiválni
        hozzáférését itallapunkhoz.</p>

    <p>Ha bármilyen kérdése lenne akkor írjon az promo@aoreal.sk email címre.</p>

    <p>Jó szórakozást kívánunk!</p>

    <p>A szervezők</p>
</div>