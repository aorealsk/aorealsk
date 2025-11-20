<?php

$this->title = Yii::t('app', 'Editácia');

?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-10 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <form method="POST">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="control-label"><?= Yii::t('app', 'Dátum') ?></label>
                        <input type="date" id="date" name="date" value="<?= $osnova->date ?>" class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="control-label"><?= Yii::t('app', 'Od') ?></label>
                        <input type="time" id="od0" name="od" value="<?= $osnova->od ?>" class="form-control">
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="control-label"><?= Yii::t('app', 'Do') ?></label>
                        <input type="time" id="do0" name="do" value="<?= $osnova->do ?>" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label class="control-label"><?= Yii::t('app', 'Pracovná činnosť') ?></label>
                        <textarea class="form-control" id="body" name="body" cols="30" rows="10"><?= $osnova->body ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <button class="btn btn-info mr-2" type="submit">
                            Uložiť
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>