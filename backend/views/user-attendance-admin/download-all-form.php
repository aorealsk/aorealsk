<?php
/**
 * @var $group
 * @var $templateIds
 * @var $users
 * @var $month
 */
use yii\helpers\Url;
?>

<form method="POST" action=<?= Url::to('/backoffice/user-attendance-admin/download-all') ?> id="download-all">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
    <input type="hidden" name="group_name" value="<?= $group ?>">
    <?php
    foreach($templateIds as $id) {
    ?>
        <input type="hidden" name="template_id[]" value="<?= $id['template_id'] ?>">
    <?php
    }
    ?>
    <?php
    foreach($users as $user) {
        ?>
        <input type="hidden" name="user[]" value="<?= $user->id ?>">
        <?php
    }
    ?>
    <button type="submit" class="btn btn-success text-white">
        Stiahnuť všetky dokumenty
    </button>
    <input type="hidden" name="month" value="<?= $month ?>">
</form>