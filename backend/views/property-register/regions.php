<?php
/**
 * @var $regions
 */
?>

<?php foreach ($regions as $region) : ?>
    <option value="<?= $region->id ?>"><?= $region->name ?></option>
<?php endforeach; ?>