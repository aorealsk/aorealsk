<?php

use common\models\fbcharity\PromoOrder;

/**
 * @var $orders
 */

foreach ($orders as $order) : ?>
    <li data-orderid="<?= $order->id ?>">
        #<?= $order->id ?> - <?= $order->created_at ?>
        <?php if ($order->status == PromoOrder::NEW) : ?>
        <span class="badge text-bg-success float-end">Nová</span>
        <?php endif; ?>
        <?php if ($order->status == PromoOrder::PROCESSING) : ?>
            <span class="badge text-bg-warning float-end">Spracovaná</span>
            <span class="text-muted small-text">[<?= $order->personal->user_name ?>]</span>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
