<?php

use yii\helpers\Url;

?>
<div class="row p-5">
    <div class="col-md-2 col-m-1"></div>
    <div class="col-md-8 col-sm-10">
        <h1 class="text-center mb-5">Üdv, <?= $username?>!</h1>
        <div class="d-grid gap-5 col-5 mx-auto">
            <a href="<?= Url::to(['/promo/guests']) ?>" class="btn btn-primary p-3 bg-gradient" type="button">
                Vendéglista
            <a href="<?= Url::to(['/promo/logout']) ?>" class="btn btn-danger p-3 bg-gradient" type="button">
                Kilépés
            </a>
        </div>

    </div>
    <div class="col-md-2 col-sm-1"></div>
</div>




