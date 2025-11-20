<?php
/**
 * @var $promotions
 */

use yii\helpers\Url;

?>
<?php foreach ($promotions as $promotion) : ?>
<tr>
    <td><?= $promotion->id ?></td>
    <td><?= $promotion->name ?></td>
    <td>
        <b>Štart:</b> <?= $promotion->start_date ?><br>
        <b>Koniec:</b> <?= $promotion->finish_date ?>
    </td>
    <td><?= $promotion->place ?></td>
    <td>
        <a href="<?= Url::to(['/promo/detail', 'id' => $promotion->id]) ?>" title="Detaily">
            <i class="fas fa-edit" style="color: black"></i>
        </a>
    </td>
</tr>
<?php endforeach; ?>
