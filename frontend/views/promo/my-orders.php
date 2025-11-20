<?php

use yii\helpers\Url;

/**
 * @var $orders
 */

?>
<div class="row p-5">
    <div class="col-md-2 col-m-1"></div>
    <div class="col-md-8 col-sm-10">
        <h1 class="text-center mb-5">Moje objednávky</h1>
        <a href="<?= Url::to(['/promo/home']) ?>"
           class="btn btn-primary d-flex justify-content-center col-6 mx-auto mb-5">
            Späť na úvod
        </a>

        <ul class="mt-3 objednavky">
            <?php foreach ($orders as $order) : ?>
                <li>
                    <a href="<?= \yii\helpers\Url::to(['/promo/finish-order/' . $order->id]) ?>"
                       class="w-100 d-block">
                        #<?= $order->id ?> - <?= $order->created_at ?>
                    </a>

                </li>
            <?php endforeach; ?>
        </ul>

    </div>
    <div class="col-md-2 col-sm-1"></div>
</div>
