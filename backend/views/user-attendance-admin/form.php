<?php
/**
 * @var $group
 * @var $users
 * @var $templateIds
 * @var $month
 * @var $templates
 */
?>

<?php
// templates first
// users inside templates first

foreach($templateIds as $id => $item) {
    $isPerUser =(int)((\common\models\PrivilegesTemplates::findOne(['template_id'=>$item['template_id']]))->per_user) === 1;

    if (!$isPerUser) {
        $userList = [];
        array_walk($users, function($val, $key) use (&$userList){
            $userList[] = $val->id;
        });
?>
    <li>
        <form method="POST" class="d-flex align-items-center">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
            <input type="hidden" name="group_name" value="<?= $group ?>">
            <input type="hidden" name="user" value="<?= implode(',',$userList) ?>">
            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="template_id" value="<?= $item['template_id'] ?>">
            <input type="checkbox" checked/>
            <p class="mt-3 ml-5">
                <?= $templates[$id] ?> - <?= \common\helpers\DateHelper::getMonthText($month) ?>
            </p>
            <button type="submit" class="btn btn-svg ml-1"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" /><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" /></svg></button>
            <button type="button" data-month="<?= $month ?>"  data-user="<?= implode(',',$userList) ?>" data-template="<?= $item['template_id'] ?>" class="btn btn-svg ml-2 view-pdf">
                <i class="fas fa-eye"></i>
            </button>
        </form>
    </li>
<?php
    } else {
        foreach($users as $user) {
?>
    <li>
        <form method="POST" id="download" class="d-flex align-items-center">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
            <input type="hidden" name="group_name" value="<?= $group ?>">
            <input type="hidden" name="user" value="<?= $user->id ?>">
            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="template_id" value="<?= $item['template_id'] ?>">
            <input type="checkbox" checked/>
            <p class="mt-3 ml-5">
                <?= $templates[$id] ?> - <?= $user->username ?>
            </p>
            <button type="submit" class="btn btn-svg ml-1"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" /><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" /></svg></button>
            <button type="button" data-month="<?= $month ?>"  data-user="<?= $user->id ?>" data-template="<?= $item['template_id'] ?>" class="btn btn-svg ml-2 view-pdf">
                <i class="fas fa-eye"></i>
            </button>
        </form>
    </li>
<?php
        }
    }
}
?>
</form>