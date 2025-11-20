<?php
/**
 * @var $rows
 */
foreach ($rows as $row) {
    ?>
    <tr>
        <td><?= $row->id ?></td>
        <td><?= $row->code ?></td>
        <td><?= $row->desc ?></td>
        <td><?= $row->templ_type ?></td>
        <td><?php
            $content = substr($row->content,0, 100);
            echo htmlentities($content);
            if (strlen($content) > 0) {
                echo '...';
            }
            ?></td>
        <td><?= $row->map_id ?></td>
        <td>0</td>
        <td>
            <a href="#" title="Delete" style="color:black" class="del-item" data-id="<?= $row->id ?>">
                <i class="fas fa-trash-alt"></i>
            </a>
            <a href="#" title="Edit" style="color:black">
                <i class="fas fa-pencil-alt"></i>
            </a>
        </td>
    </tr>
    <?php
}
?>
