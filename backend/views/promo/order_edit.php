<?php

// a jegyrendeles szerkesztesnek a resze
/**
 * @var $order
 */
use yii\helpers\Url;
use backend\assets\RealAsset;
use common\models\fbcharity\OrderStatus;

$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile(
    '@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css',
    ['depends' => RealAsset::class]
);
$this->registerJSFile('@web/assets/node_modules/toast-master/js/jquery.toast.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/toast-master/css/jquery.toast.css', ['depends' => RealAsset::class]);
$this->registerJSFile('@web/js/issue.js?v=0.1', ['depends' => RealAsset::class]);

$this->title = 'Editácia objednávky';
?>
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/detail', 'id' => $order->id]) ?>"
               class="btn btn-danger text-white">Späť
            </a>
        </div>
    </div>

    <form method="post" role="form">
        <input
            type="hidden"
            name="<?= Yii::$app->request->csrfParam ?>"
            value="<?= Yii::$app->request->csrfToken ?>"
        >

        <div class="row mt-4">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="margin-bottom: 20px">Detaily objednávky</h5>
                        <div class="row m-b-30">
                            <div class="col-12">
                                <button
                                        type="button"
                                        class="btn btn-sm btn-info text-white sendm"
                                        data-oid="<?= $order->id ?>"
                                >
                                    <i class="mdi mdi-email"></i> Poslať lístky
                                </button>
                                <div class="spinner-border" role="status" id="sp01" style="display: none">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select class="form-select oe" data-oid="<?= $order->id ?>">
                                        <?php foreach (OrderStatus::getStatuses() ?? [] as $id => $status) : ?>
                                            <option value="<?= $id ?>"
                                                <?= $id == $order->status ? 'selected' : '' ?>
                                            ><?= $status ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Kód</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="code"
                                        value="<?= $order->code ?>"
                                    >
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Spolu (&euro;)</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="total"
                                        value="<?= $order->total ?>"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Vytvorené</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="created_at"
                                        value="<?= $order->created_at ?>"
                                    >
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Daň (&euro;)</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="total_tax"
                                        value="<?= $order->total_tax ?>"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Poznámky</label>
                                    <textarea
                                        class="form-control"
                                        name="note"
                                        rows="5"
                                    ><?= $order->note ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="margin-bottom: 20px">Detaily zákazníka</h5>
                        <?php
                        $customer = $order->customer;
                        ?>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Meno</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="name_first"
                                        value="<?= $customer->name_first ?>"
                                    >
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Priezvisko</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="name_last"
                                        value="<?= $customer->name_last ?>"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input
                                         type="email"
                                         class="form-control"
                                         name="email"
                                         value="<?= $customer->email ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Telefón</label>
                                    <input
                                        type="tel"
                                        class="form-control"
                                        name="phone"
                                        value="<?= $customer->phone ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Dátum narodenia</label>
                                    <input
                                        type="date"
                                        class="form-control"
                                        name="birth_date"
                                        value="<?= $customer->birth_date ?>">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Facebook</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="facebook"
                                        value="<?= $customer->facebook ?>">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="margin-bottom: 20px">Položky</h5>
                        <table class="table table-bordered table-striped table-sm dattable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produkt</th>
                                    <th>Množstvo</th>
                                    <th>JC</th>
                                    <th>Cena</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($order->items as $item) : ?>
                                <tr>
                                    <td><?= $item->id ?></td>
                                    <td></td>
                                    <td><?= $item->amount ?></td>
                                    <td><?= $item->price ?></td>
                                    <td><?= $item->total ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2 mb-3">
            <div class="col-12">
                <button type="submit" class="btn btn-success text-white">
                    <i class="fas fa-save"></i> Uložiť
                </button>
                <a href="<?= Url::to(['/promo/detail', 'id' => $order->id]) ?>" class="btn btn-danger text-white">
                    Späť
                </a>
            </div>
        </div>

    </form>

</div>
<?php
$js = <<<JS
    $(function() {
        $('.dattable').DataTable({
            order: []
        });
    });
    
    $(document).on('change', '.oe', function() {
        let oid = $(this).data('oid');
        let status = $(this).val();
        $.ajax({
            url: '/backoffice/promo/order-status',
            method: 'post',
            data: {
                oid: oid,
                status: status
            },
            success: function(data) {
                if (data.status === 'ok') {
                    $.toast({
                        heading: 'Info',
                        text: 'Status bol úspešne zmenený!',
                        showHideTransition: 'slide',
                        icon: 'success',
                        position: 'top-right'
                    });
                } else {
                    $.toast({
                        heading: 'Chyba',
                        text: 'Status sa nepodarilo zmeniť! ' + data.message,
                        showHideTransition: 'fade',
                        position: 'top-right',
                        icon: 'error'
                    });
                }
            }
        });
    });
    
    $(document).on('click', '.sendm', function() {
        let oid = $(this).data('oid');
        $('.sendm').hide();
        $('#sp01').show();
        $.ajax({
            url: '/backoffice/promo/send-mail',
            method: 'post',
            data: {
                oid: oid
            },
            success: function(data) {
                if (data.status === 'ok') {
                    $.toast({
                        heading: 'Info',
                        text: 'Email bol úspešne odoslaný!',
                        showHideTransition: 'slide',
                        position: 'top-right',
                        icon: 'info'
                    });
                } else {
                    $.toast({
                        heading: 'Chyba',
                        text: 'Email sa nepodarilo odoslať! '  + data.message,
                        showHideTransition: 'fade',
                        position: 'top-right',
                        icon: 'error'
                    });
                }
                $('.sendm').show();
                $('#sp01').hide();
            }
        });
    });

JS;
$this->registerJS($js);