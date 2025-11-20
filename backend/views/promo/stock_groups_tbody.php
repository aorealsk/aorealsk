<?php

use yii\helpers\Url;

/**
 * @var $groups
 */

?>
<?php foreach ($groups as $group) : ?>
    <tr>
        <td style="width: 5%"><?= $group->id ?></td>
        <td style="width: 40%">
            <?php
            foreach ($group->langs as $lang) : ?>
                <b> <?= $lang->lang ?>: </b> <?= $lang->title ?><br>
            <?php endforeach; ?>
        </td>
        <td>
            <?php
            foreach ($group->langs as $lang) : ?>
                <b> <?= $lang->lang ?>: </b> <?= $lang->description ?? 'bez popisu' ?>
                <br><br>
            <?php endforeach; ?>
        </td>
        <td style="vertical-align: middle; width: 8%">
            <?php if (is_null($group->deleted_at)) : ?>
                <span class="mybadge-success">Aktívna</span>
            <?php else : ?>
                <span class="mybadge-danger">Zmazané</span>
            <?php endif; ?>
        </td>
        <td style="width: 5%; text-align: center">

            <a
                href="<?= Url::to(['/promo/stock-group-edit', 'id' => $group->id]) ?>"
                style="color: black;"
                title="Upraviť"
            >
                <i class="fas fa-edit"></i>
            </a>
            <?php if (is_null($group->deleted_at)) : ?>
                <a
                        href="javascript:void(0);"
                        data-gid="<?= $group->id ?>"
                        style="color: black"
                        title="Vymazať"
                        class="del-grp"
                >
                    <i class="fas fa-trash-alt"></i>
                </a>
            <?php else : ?>
                <a
                        href="javascript:void(0);"
                        data-gid="<?= $group->id ?>"
                        style="color: black"
                        title="Aktivovať"
                        class="reop-grp"
                >
                    <i class="fas fa-recycle"></i>
                </a>

            <?php endif; ?>

        </td>
    </tr>
<?php endforeach; ?>
