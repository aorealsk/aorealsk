<?php

/**
 * @var $order
 * @var $guest
 * @var $promoId
 */
use backend\assets\RealAsset;
use yii\helpers\Url;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile(
    '@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',
    ['depends' => RealAsset::class]
);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/js/issue.js?v=0.1', ['depends' => RealAsset::class]);

$this->title = 'Editácia hosťa';
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/detail', 'id' => $promoId]) ?>"
               class="btn btn-danger text-white">Späť
            </a>
        </div>
    </div>

        <div class="row mt-4">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form method="post" role="form">
                            <input
                                    type="hidden"
                                    name="<?= Yii::$app->request->csrfParam ?>"
                                    value="<?= Yii::$app->request->csrfToken ?>"
                            >
                            <h5 class="card-title" style="margin-bottom: 20px">Detaily klienta</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Meno:</label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                name="name_first"
                                                value="<?= $guest->name_first ?>"
                                        >
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Priezvisko:</label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                name="name_last"
                                                value="<?= $guest->name_last ?>"
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Email:</label>
                                        <input
                                                type="email"
                                                class="form-control"
                                                name="email"
                                                value="<?= $guest->email ?>"
                                        >
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Telefón:</label>
                                        <input
                                                type="tel"
                                                class="form-control"
                                                name="phone"
                                                value="<?= $guest->phone ?>"
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Dátum narodenia:</label>
                                        <input
                                                type="date"
                                                class="form-control"
                                                name="birth_date"
                                                value="<?= $guest->birth_date ?>"
                                        >
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Vek:</label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                name="age"
                                                value="<?= $guest->age ?>"
                                        >
                                    </div>
                                </div>
                            </div>
                            <h5 class="card-title" style="margin-bottom: 20px">Detaily objednávky</h5>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Číslo objednávky:</label>
                                        <input
                                                type="text"
                                                class="form-control disabled"
                                                name="code"
                                                value="<?= $guest->order->code ?>"
                                                disabled
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Promo kód:</label>
                                    <input
                                            type="text"
                                            class="form-control"
                                            name="promo_code"
                                            value="<?= $guest->promo_code ?>"
                                    >
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success text-white" id="ap01">Uložiť</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form method="post" role="form">
                            <input
                                    type="hidden"
                                    name="<?= Yii::$app->request->csrfParam ?>"
                                    value="<?= Yii::$app->request->csrfToken ?>"
                            >
                            <h5 class="card-title">Vrátenie kreditu</h5>
                            <div class="row mt-5">
                                <div class="col-12">
                                    <?php
                                    $data = json_decode($guest->refund, true);
                                    $suma = [0, 0, 0];
                                    if (isset($data['c_part']) && $data['c_part'] == 'on') {
                                        $suma[0] = $data['c_part_amt'];
                                    }
                                    if (isset($data['c_full']) && $data['c_full'] == 'on') {
                                        $suma[0] = $guest->balance;
                                    }
                                    if (isset($data['t_part']) && $data['t_part'] == 'on') {
                                        $suma[1] = $data['t_part_amt'];
                                    }
                                    if (isset($data['t_full']) && $data['t_full'] == 'on') {
                                        $suma[1] = $guest->balance;
                                    }
                                    if (isset($data['b_part']) && $data['b_part'] == 'on') {
                                        $suma[2] = $data['b_part_amt'];
                                    }
                                    if (isset($data['b_full']) && $data['b_full'] == 'on') {
                                        $suma[2] = $guest->balance;
                                    }
                                    ?>
                                    <p>
                                        <b>Zostatok na virtuálnom účte:</b>
                                        <span id="b01"><?= $guest->balance?></span> &euro;
                                    </p>
                                    <table class="table table-responsive table-bordered">
                                        <tr>
                                            <td>na dobročinné účely príjemcovi tomboly</td>
                                            <td><?= number_format($suma[0], 2) ?> &euro;</td>
                                        </tr>
                                        <tr>
                                            <td>čašníkom ako prepitné</td>
                                            <td><?= number_format($suma[1], 2) ?> &euro;</td>
                                        </tr>
                                        <tr>
                                            <td>späť na účet</td>
                                            <td><?= number_format($suma[2], 2) ?> &euro;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <b>IBAN:</b> <?= $data['iban'] ?? '' ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-3 row">
                                <div class="col-12">
                                    <button
                                            type="button"
                                            class="btn btn-success text-white"
                                            id="ap02"
                                            <?= $guest->balance == 0 ? 'disabled' : '' ?>
                                    >Uložiť</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php
$js = <<<JS
    $('#ap01').on('click', function() {
        var data = $('form').serialize();
        $.ajax({
            url: '/backoffice/promo/update-guest?id={$guest->id}',
            type: 'post',
            data: data,
            success: function(response) {
                if (response.status === 'ok') {
                    $.toast({
                    text: 'Údaje boli úspešne uložené',
                    showHideTransition: 'slide',
                    bgColor: '#28a745',
                    textColor: 'white',
                    allowToastClose: false,
                    hideAfter: 3000,
                    stack: 5,
                    textAlign: 'left',
                    position: 'top-right'
                });
                } else {
                    $.toast({
                    text: response.message,
                    showHideTransition: 'slide',
                    bgColor: '#28a745',
                    textColor: 'white',
                    allowToastClose: false,
                    hideAfter: 3000,
                    stack: 5,
                    textAlign: 'left',
                    position: 'top-right'
                });
                }
                
            }
        });
    });
    $('#ap02').on('click', function() {
        var data = $('form').serialize();
        $.ajax({
            url: '/backoffice/promo/update-guest-balance?id={$guest->id}',
            type: 'post',
            data: data,
            success: function(response) {
                if (response.status === 'ok') {
                    $.toast({
                        text: 'Údaje boli úspešne uložené',
                        showHideTransition: 'slide',
                        bgColor: '#28a745',
                        textColor: 'white',
                        allowToastClose: false,
                        hideAfter: 3000,
                        stack: 5,
                        textAlign: 'left',
                        position: 'top-right'
                    });
                    $('#b01').text(response.balance);
                    $('#ap02').prop('disabled', true);
                } else {
                    $.toast({
                        text: response.message,
                        showHideTransition: 'slide',
                        bgColor: '#28a745',
                        textColor: 'white',
                        allowToastClose: false,
                        hideAfter: 3000,
                        stack: 5,
                        textAlign: 'left',
                        position: 'top-right'
                    });
                }
               
            }
        });
    });            
JS;
$this->registerJS($js);