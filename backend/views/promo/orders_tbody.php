<?php

use common\models\fbcharity\OrderStatus;
use yii\helpers\Url;
// promo detail oldalon a jegyrendelesek resze
/**
 * @var $orders
 */

?>
<?php foreach ($orders as $order) :
    ?>
    <tr style="<?= OrderStatus::getCssOptions($order->status) ?>">
        <td><?= $order->id ?></td>
        <td><?= $order->code ?></td>
        <td><?= $order->customer->getFullName() ?></td>
        <td>€ <?= $order->total ?></td>
        <td><?= $order->created_at ?></td>
        <td>
            <?= OrderStatus::getLabel($order->status) ?>
        </td>
        <td>
            <?php if ($order->status != OrderStatus::DELETED) : ?>
            <a
                href="javascript:void(0)"
                class="m-l-10 del-ord"
                style="color: #000"
                data-oid="<?= $order->id ?>"
            >
                <i class="fas fa-trash-alt"></i>
            </a>
            <?php endif; ?>

            <a href="<?= Url::to(['/promo/order-edit?oid=' . $order->id]) ?>"
               style="color: #000;" class="m-l-10">
                <i class="fas fa-edit"></i>
            </a>
        </td>
    </tr>
    <?php
endforeach;
?>
