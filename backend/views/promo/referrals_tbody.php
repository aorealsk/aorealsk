<?php

use yii\helpers\Url;
use common\models\fbcharity\Guest;

/**
 * @var $referrals
 */

foreach ($referrals as $code) : ?>
    <tr>
        <td><?= $code->id ?></td>
        <td><?= $code->code ?></td>
        <td>
            <b>Štart:</b> <?= $code->available_from ?><br>
            <b>Koniec:</b> <?= $code->available_to ?>
        </td>
        <td><?= $code->assigned_to ?></td>
        <td>
            <a
                    href="<?= Url::to(['/promo/edit-referral', 'id' => $code->id]) ?>"
                    title="Edit">
                <i class="fas fa-edit" style="color: black"></i>
            </a>
        </td>
    </tr>


<?php endforeach; ?>
