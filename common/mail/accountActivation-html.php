<?php
use yii\helpers\Html;

/**
 * @var Client $client
 */

$activationLink = Yii::$app->urlManager->createAbsoluteUrl(["client/activate-account/{$client->auth_key}/{$client->id}"]);
?>
<div class="account-activate">
    <p><?= Yii::t('app','Dobrý deň'); ?> <?= Html::encode($client->name_first) ?> <?= Html::encode($client->name_last)?>,</p>

    <p><?= Yii::t('app','pre aktiváciu Vášho účtu kliknite na nasledujúci link'); ?>:</p>

    <p><?= Html::a(Html::encode($activationLink), $activationLink) ?></p>
</div>
