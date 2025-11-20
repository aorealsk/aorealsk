<div class="col-lg-2 col-md-6">
    <div class="card">
        <div class="el-card-item">
            <div class="el-card-avatar el-overlay-1">
                <?php
                /**
                 * @var $fileinfo
                 */
                $file = "/backoffice/users/{$fileinfo['user_id']}/{$fileinfo['file']}";
                ?>
                <img src="<?= $file ?>" alt="user" style="border-radius: 5px"/>
                <div class="el-overlay">
                    <ul class="el-info">
                        <li>
                            <a class="btn default btn-outline image-popup-vertical-fit" href="<?= $file ?>">
                                <i class="icon-magnifier"></i>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)"
                               class="btn default btn-outline"
                               onclick="removeImage(<?= $fileinfo['id'] ?>,'<?= $fileinfo['file'] ?>')"
                            >
                                <i class="icon-trash"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
