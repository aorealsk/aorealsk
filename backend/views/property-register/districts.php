<?php
/**
 * @var $districts
 */
?>

<?php foreach ($districts as $district) : ?>
    <option value="<?= $district->id ?>_<?= $district->kod ?>"><?= $district->name ?></option>
<?php endforeach; ?><?php
