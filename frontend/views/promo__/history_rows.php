<?php
/**
 * @var $rows
 */
?>
<ul style="list-style-type: none; padding: 0px;" class="oh-main">
    <?php foreach ($rows as $row): ?>
    <li style="">
        <div style="grid-area: 1 / 1 / 2 / 2;"><?= $row->created_at ?></div>&nbsp;
        <div style="grid-area: 1 / 2 / 2 / 3;" class="float-end"><?= $row->price ?> &euro;</div>
    </li>
    <?php endforeach; ?>
</ul>
