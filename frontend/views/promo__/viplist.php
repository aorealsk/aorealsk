<?php
/**
 * @var $list
 */
?>
<?php foreach($list as $row): ?>
    <tr>
        <td><?= $row->itemDetails->getTitle() ?></td>
        <td>
            <?= $row->amount ?> l
            <br>
            <?= number_format($row->amount / $row->itemDetails->bottle_size,2) ?> fl.
        </td>
        <td><input
                    type="text"
                    class="form-control vo"
                    data-promo-stock-item="<?= $row->id ?>"
                    data-unit-price="<?= $row->itemDetails->price_bottle?>"
                    value="0"
            >
        </td>
    </tr>
<?php endforeach; ?>

