<tr>
    <th class="sticky-col"></th>
    <?php
    /**
     * @var $cols
     */
    foreach($cols as $item) {
        ?>
        <th>
            <span style="font-size: 10px">Popis:</span>
            <input type="text" value="<?= $item['title'] ?>" class="cols" data-xid="<?= $item['id'] ?>" data-xitem="title">
            <span style="font-size: 10px">Prefix:</span>
            <input type="text" value="<?= $item['prefix'] ?>" class="cols" data-xid="<?= $item['id']?>" data-xitem="prefix">
            <span style="font-size: 10px">Postfix:</span>
            <input type="text" value="<?= $item['postfix'] ?>" class="cols" data-xid="<?= $item['id'] ?>" data-xitem="postfix">
        </th>
        <?php
    }
    ?>
</tr>
<?php
/**
 * @var $rows
 */
foreach($rows as $row) {
    ?>
    <tr>
        <td><input type="text" value="<?= $row['name'] ?>" data-yid="<?= $row['id'] ?>" class="rows"></td>
        <?php
        foreach($cols as $col) {
            $checked = isset($fullmap[$row['id']-1][$col['id']-1]) && $fullmap[$row['id']-1][$col['id']-1] == 1 ? 'checked' : '';
            ?>
            <td align="center">
                <input
                    type="checkbox"
                    title="<?=$col['title']?>"
                    class="mapitem"
                    data-xcord="<?= $col['id'] - 1 ?>"
                    data-ycord="<?= $row['id'] - 1 ?>"
                    <?= $checked ?>
                >
            </td>
            <?php
        }
        ?>
    </tr>
    <?php
}
?>
