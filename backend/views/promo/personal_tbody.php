<?php
/**
 * @var $personal
 */
use yii\helpers\Url;
?>
<?php foreach ($personal as $row): ?>
<tr>
    <td><?= $row->getFullName() ?></td>
    <td><?= $row->lang ?></td>
    <td><?= $row->wage ?></td>
    <td><?= $row->phone ?? '' ?></td>
    <td><?= $row->email ?? '' ?></td>
    <td>
        <a href="<?= Url::to(['/promo/personal-edit','id'=>$row->id]) ?>" title="<?= Yii::t('app','Edit'); ?>">
            <i class="fas fa-edit" style="color: black"></i>
        </a>
    </td>
</tr>

<?php endforeach; ?>