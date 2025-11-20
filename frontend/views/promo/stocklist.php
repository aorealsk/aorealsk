<?php
/**
 * @var $list
 */
?>
<table class="table table-sm">
    <thead>
    <tr>
        <th>Názov</th>
        <th>Množstvo</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach($list as $row): ?>
            <tr>
                <td><?= $row->itemDetails->getTitle() ?></td>
                <td><?= $row->amount ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
