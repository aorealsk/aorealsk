<?php
/** @var array $rows */
use yii\helpers\Html;

foreach ($rows as $r):
?>
<tr>
    <td><?= Html::encode($r['user_name']) ?></td>
    <td><?= Html::encode($r['uaDate']) ?></td>
    <td><?= Html::encode($r['inTime']  ?: '-') ?></td>
    <td><?= Html::encode($r['outTime'] ?: '-') ?></td>
    <td class="monitor-selfie">
        <?php if (!empty($r['start_selfie'])): ?>
            <a class="selfie-link" target="_blank" rel="noopener" href="<?= Html::encode($r['start_selfie']) ?>" data-url="<?= Html::encode($r['start_selfie']) ?>">📷</a>
        <?php endif; ?>
        <?php if (!empty($r['end_selfie'])): ?>
            <a class="selfie-link" target="_blank" rel="noopener" href="<?= Html::encode($r['end_selfie']) ?>" data-url="<?= Html::encode($r['end_selfie']) ?>">📷</a>
        <?php endif; ?>
    </td>
    <td><?= $r['status_html'] ?></td>
</tr>
<?php endforeach; ?>

<?php if (empty($rows)): ?>
<tr><td colspan="6" class="text-muted text-center"><?= Yii::t('app','No data available in table') ?></td></tr>
<?php endif; ?>
