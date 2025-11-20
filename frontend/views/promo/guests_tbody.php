<?php
/**
 * @var $guests array
 */
foreach ($guests as $guest) : ?>
    <?php
    $checked = '';
    $class = '';
    if ($guest->status === \common\models\fbcharity\Guest::CONFIRMED) {
        $checked = ' checked';
        $class = ' class="green-row"';
    }
    ?>
    <tr id="r<?= $guest->id ?>"<?= $class ?>>
        <td><?= $guest->order->code ?></td>
        <td><?= $guest->getFullName($guest->lang) ?></td>
        <td>
            Sor: <?= $guest->seat_row ?>
            <br>
            Szék: <?= $guest->seat_col ?>
        </td>
        <td><?= (new DateTime($guest->birth_date))->format('Y.m.d') ?></td>
        <td>Email: <?= $guest->email ?></td>
        <td>
            <div class="form-check form-switch">
                <input class="form-check-input x01" type="checkbox" data-xid="<?= $guest['id'] ?>"<?= $checked ?>>
            </div>
        </td>
    </tr>

<?php endforeach; ?>