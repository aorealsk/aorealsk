<?php
use yii\helpers\Html;
use common\models\users\UserAttendance;
use yii\helpers\Url;

/**
 * @var array $rows
 * @var int $uid
 */
?>

<?php foreach ($rows as $row):?>
    <tr>
        <td><?= $row['uaDate'] ?></td>
        <td><?= $row['inTime'] ?></td>
        <td><?= $row['outTime'] ?></td>
        <td><?= $row['diffTime'] ?></td>
        <td><?= $row['picCount'] ?></td>
        <td><?= UserAttendance::workType($row['uaType'])  ?> </td>
        <td><?= Html::decode($row['note']) ?></td>
        <td><a href="<?= Url::to(['edit','rid'=>$row['id'],'uid'=>$uid]) ?>" title="Edit" style="color: black">
                <i class="icon-pencil"></i>
            </a>
        </td>
    </tr>
<?php endforeach; ?>