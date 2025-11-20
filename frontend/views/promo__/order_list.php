<?php
/**
 * @var $orders
 */
?>
<?php foreach ($orders as $order): ?>
    <li data-orderid="<?= $order->id ?>">
        #<?= $order->id ?> - <?= $order->created_at ?>
        <?php if ($order->status == \common\models\promo\PromoGuestOrder::NEW): ?>
        <span class="badge text-bg-success float-end">Nová</span>
        <?php endif; ?>
        <?php if ($order->status == \common\models\promo\PromoGuestOrder::PROCESSING): ?>
            <span class="badge text-bg-warning float-end">Spracovaná</span>
        <?php endif; ?>
    </li>
<?php endforeach; ?><?php
