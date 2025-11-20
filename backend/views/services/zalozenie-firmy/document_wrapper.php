<?php
/**
 * @var $template_content
 */
?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <?= $template_content ?>
        </div>
    </div>
    <div class="row m-t-20">
        <div class="col-12">
            <button type="button" class="btn btn-success text-white" id="save-template">
                <?= Yii::t('app','Uložiť zmeny'); ?>
            </button>
        </div>
    </div>
</div>
