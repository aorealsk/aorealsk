<?php
/**
 * @var $data
 */
?>
<?php foreach ($data as $row) : ?>
    <tr>
        <td><?= $row['owner_name'] ?></td>
        <td><?= $row['addr'] ?></td>
        <td><?= $row['email'] ?? '' ?></td>
        <td><?= $row['phone'] ?? '' ?></td>
        <td><?= $row['district_id'] ?? '' ?></td>
        <td><?= $row['district_name'] ?></td>
        <td><?= $row['municipality_id'] ?? '' ?></td>
        <td><?= $row['municipality_name'] ?></td>
        <td><?= $row['cadastral_area_id'] ?? '' ?></td>
        <td><?= $row['cadastral_area_name'] ?? '' ?></td>
        <td><?= $row['list_id'] ?></td>
        <td></td>
        <td><?= $row['ownership'] ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>

        </td>
    </tr>
<?php endforeach; ?>