<?php

use yii\helpers\Url;
use common\models\fbcharity\Guest;

/**
 * @var $codes
 */

foreach ($codes as $code) : ?>
    <tr>
        <td><?= $code->id ?></td>
        <td><?= $code->code ?></td>
        <td>
            <b>Štart:</b> <?= $code->available_from ?><br>
            <b>Koniec:</b> <?= $code->available_to ?>
        </td>
        <td><?= $code->assigned_to ?></td>
        <td>
            <?= $code->used_at ?? "nepoužité" ?>
            <?php
            if ($code->used_at) {
                $customer = Guest::findByPromoCode($code->code);
                if ($customer) {
                    echo "<br><b>Zákazník:</b> " . $customer->getFullName();
                }
            }
            ?>
        </td>
        <td>
            <a
                    href="<?= Url::to(['/promo/codes-edit', 'id' => $code->id]) ?>"
                    title="Edit">
                <i class="fas fa-edit" style="color: black"></i>
            </a>
        </td>
    </tr>


<?php endforeach; ?>
