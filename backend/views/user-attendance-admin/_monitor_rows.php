<?php
/** @var array $items */
use yii\helpers\Html;

foreach ($items as $it): ?>
<tr>
    <td><?= Html::encode($it['name']) ?></td>
    <td><?= Html::encode($it['date']) ?></td>
    <td><?= Html::encode($it['start']) ?></td>
    <td><?= Html::encode($it['end']) ?></td>

    <td class="monitor-selfie">
        <?php if (!empty($it['startUrl'])): ?>
            <a href="<?= Html::encode($it['startUrl']) ?>" target="_blank" rel="noopener">
                <img class="selfie-thumb" src="<?= Html::encode($it['startUrl']) ?>" alt="start">
            </a>
        <?php endif; ?>
    </td>

    <td class="monitor-selfie">
        <?php if (!empty($it['endUrl'])): ?>
            <a href="<?= Html::encode($it['endUrl']) ?>" target="_blank" rel="noopener">
                <img class="selfie-thumb" src="<?= Html::encode($it['endUrl']) ?>" alt="end">
            </a>
        <?php endif; ?>
    </td>

    <td><?= $it['statusHtml'] ?></td>
</tr>
<?php endforeach; ?>
