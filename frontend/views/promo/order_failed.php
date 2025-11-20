<?php
/**
 * @var $lang
 * @var $key
 *
 */
use yii\helpers\Url;
?>
<div class="row">
    <div class="col-md-3 col-sm-2">
    </div>

    <div class="col-md-6 col-sm-8 text-center">
        <?php
        $message = "Vašu objednávku sme nevedeli spracovať! Prosím zopakujte Vašu objednávku!";
        if ($lang === 'hu') {
            $message = "Az Ön megrendelését nem tudtuk feldolgozni! Kérjük ismétele meg a rendelést!";
        }
        ?>
        <h4 class="mt-5"><?= $message ?></h4>
        <p class="mt-5">
            <?php
            $btnTitle = "Chcem znovu objednať";
            if ($lang === 'hu') {
                $btnTitle = "Újra rendelni akarok";
            }
            ?>
            <a href="<?= Url::to(["/promo/order/{$lang}/{$key}"]) ?>" class="btn btn-primary">
                <?= $btnTitle ?>
            </a>
        </p>
    </div>

    <div class="col-md-3 col-sm-2">
    </div>

</div>
