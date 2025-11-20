<?php

use yii\helpers\Url;

/**
 * @var $places
 */
?>
<?php foreach ($places as $place) : ?>
    <tr>
        <td><?= $place->id ?></td>
        <td><?= $place->promotion->name ?></td>
        <td><?= $place->place_name ?></td>
        <td>
            <b>Štart:</b> <?= $place->start_date ?><br>
            <b>Koniec:</b> <?= $place->finish_date ?>
        </td>
        <td>
            <a
                href="<?= Url::to(['/promo/promo-place-edit', 'id' => $place->id]) ?>"
                title="Edit">
                <i class="fas fa-edit" style="color: black"></i>
            </a>
            <a
                href="javascript:void(0)"
                title="Delete"
                class="del"
                data-xid="<?= $place->id ?>"
            >
                <i class="fas fa-trash-alt" style="color: black"></i>
            </a>
        </td>
    </tr>
<?php endforeach; ?>
