<?php
/**
 * @var $items
 */

use yii\helpers\Url;
use yii\helpers\ArrayHelper;

?>
<?php foreach ($items as $item): ?>
<tr>
    <td>
        <?php
        $data = ArrayHelper::toArray($item->groups,['common\models\promo\StockItemGroupLang'=>['lang','title']]);
        foreach ($data as $row){
            if ($row['lang'] != 'sk') {
                continue;
            }
            echo "{$row['title']}";
        }
        ?>
    </td>
    <td><?php
        $data = ArrayHelper::toArray($item->titles,['common\models\promo\StockItemLang'=>['lang','title']]);
        foreach ($data as $row){
            if ($row['lang'] != 'sk') {
                continue;
            }
            echo "{$row['title']}";
        }
    ?></td>
    <td><?= $item->alcohol ?? 0 ?></td>
    <td><?= $item->cost ?? 0 ?></td>
    <td><?= $item->bottle_size ?? 0 ?></td>
    <td><?= $item->bottle_per_carton ?? 0 ?></td>
    <td><?= $item->carton ?? 0 ?></td>
    <td><?= $item->amount ?? 0 ?></td>
    <td><?= $item->bottle_cnt ?? 0 ?></td>
    <td><?= $item->investment ?? 0 ?></td>
    <td><?= $item->price_04 ?? 0 ?></td>
    <td><?= $item->price_04_bottle ?? 0 ?></td>
    <td><?= $item->price_075_bottle ?? 0 ?></td>
    <td><?= $item->price_1 ?? 0 ?></td>
    <td><?= $item->price_5 ?? 0 ?></td>
    <td><?= $item->price_10 ?? 0 ?></td>
    <td><?= $item->price_1_bottle ?? 0 ?></td>
    <td><?= $item->price_bottle ?? 0 ?></td>
    <td><a href="<?= Url::to(['/promo/stock-edit','id'=>$item->id]) ?>" title="<?= Yii::t('app','Detaily'); ?>">
            <i class="fas fa-edit" style="color: black"></i>
        </a></td>
</tr>

<?php endforeach; ?>
