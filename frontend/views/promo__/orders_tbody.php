<?php
/**
 * @var $items
 */
?>
<?php foreach ($items as $item): ?>
<tr>
    <td><?= $item['title'] ?></td>
    <td><?= $item['mj'] ?></td>
    <td class="text-end"><?= $item['mnozstvo'] ?></td>
    <td class="text-end"><?= $item['jedcena'] ?></td>
    <td class="text-end"><?= $item['cena'] ?></td>
</tr>
<?php endforeach; ?>
