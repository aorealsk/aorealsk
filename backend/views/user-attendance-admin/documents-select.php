<?php
if (!empty($users)) {
    foreach ($users as $user) {
?> <option name="<?= $user['username'] ?>" value='<?= $user['id'] ?>'><?= $user['username'] ?></option>
    <?php } ?>
<?php  } ?>