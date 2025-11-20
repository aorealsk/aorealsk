<?php
/**
 * @var $list
 */
?>
<?php foreach ($list as $row) :
    ?>
    <tr>
        <td><?= $row->stockDetail->getTitle() ?></td>
        <td><?= $row->amount ?></td>
        <td>
            <?php if ($row->price_1) : ?>
                <div>
                    <input
                            type="text"
                            class="form-control w-25 d-inline mb-2 vo"
                            data-psi="<?= $row->id ?>"
                            data-up="<?= $row->price_1 ?>"
                    >
                    <span class="px-2"><?= $row->price_1 ?> &euro; / 1dl</span>
                </div>
            <?php endif; ?>
            <?php if ($row->price_04) : ?>
                <div>
                    <input
                            type="text"
                            class="form-control w-25 d-inline mb-2 vo"
                            data-psi="<?= $row->id ?>"
                            data-up="<?= $row->price_04 ?>"
                    >
                    <span class="px-2"><?= $row->price_04 ?> &euro; / 4dl</span>
                </div>
            <?php endif; ?>
            <?php if ($row->price_5) : ?>
                <div>
                    <input
                            type="text"
                            class="form-control w-25 d-inline mb-2 vo"
                            data-psi="<?= $row->id ?>"
                            data-up="<?= $row->price_5 ?>"
                    >
                    <span class="px-2"><?= $row->price_5 ?> &euro; / 0.5l</span>
                </div>
            <?php endif; ?>
            <?php if ($row->price_075) : ?>
                <div>
                    <input
                            type="text"
                            class="form-control w-25 d-inline mb-2 vo"
                            data-psi="<?= $row->id ?>"
                            data-up="<?= $row->price_10 ?>"
                    >
                    <span class="px-2"><?= $row->price_075 ?> &euro; / 0.75l</span>
                </div>
            <?php endif; ?>
            <?php if ($row->price_10) : ?>
                <div>
                    <input
                            type="text"
                            class="form-control w-25 d-inline mb-2 vo"
                            data-psi="<?= $row->id ?>"
                            data-up="<?= $row->price_10 ?>"
                    >
                    <span class="px-2"><?= $row->price_10 ?? 0 ?> &euro; / 1l</span>
                </div>
            <?php endif; ?>
            <?php if ($row->price_bottle) : ?>
                <div>
                    <input
                            type="text"
                            class="form-control w-25 d-inline mb-2 vo"
                            data-psi="<?= $row->id ?>"
                            data-up="<?= $row->price_bottle ?>"
                    >
                    <span class="px-2"><?= $row->price_bottle ?? 0 ?> &euro; / fl.</span>
                </div>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>

