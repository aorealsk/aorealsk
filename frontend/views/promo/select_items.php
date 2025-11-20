<?php
/**
 * @var $items
 */
?>
<option value=""></option>
<?php foreach ($items as $item): ?>
    <option value="<?= $item['value']?>"><?= $item['text'] ?></option>
<?php endforeach; ?>
