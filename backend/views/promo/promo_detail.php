<?php

/**
 * @var $promo
 * @var $personal
 * @var $guests
 * @var $orders
 */


use backend\assets\RealAsset;
use yii\helpers\Url;
use common\models\fbcharity\OrderStatus;

$this->title = 'Detaily';
$this->registerJSFile('@web/assets/node_modules/datatables/datatables.min.js', ['depends' => RealAsset::class]);
$this->registerCSSFile('@web/assets/node_modules/datatables/media/css/dataTables.bootstrap4.css', ['depends' => RealAsset::class]);

?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/promo-price-list?promo_id=' . $promo->id]) ?>"
               class="btn btn-success text-white">Cenník</a>
            <a href="<?= Url::to(['/promo/promo-closure?promo_id=' . $promo->id]) ?>"
               class="btn btn-info text-white">Uzávierka</a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Všeobecné</h4>
                    <form method="post" role="form" class="mt-4">
                        <input
                                type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                                value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Názov</label>
                                    <input type="text"
                                           class="form-control"
                                           value="<?= $promo->name?>"
                                           name="name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Miesto konania</label>
                                    <input type="text"
                                           class="form-control"
                                           value="<?= $promo->place ?? ''?>"
                                           name="place">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Dátum a čas začatia</label>
                                    <input type="datetime-local"
                                           class="form-control"
                                           value="<?= $promo->start_date ?>" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Dátum a čas konca</label>
                                    <input type="datetime-local"
                                           class="form-control"
                                           value="<?= $promo->finish_date?>" name="finish_date">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success text-white">
                                        <i class="fas fa-save"></i> Uložiť
                                    </button>
                                    <a href="<?= Url::to(['/promo/index']) ?>" class="btn btn-danger text-white">
                                        Späť
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Personál</h4>

                    <div class="row">
                        <div class="col-12">
                            <a href="<?= Url::to(['/promo/add-personal?promo_id=' . $promo->id]) ?>"
                               class="btn btn-sm btn-secondary">
                                Pridať personál
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-striped table-sm dattable">
                            <thead>
                            <tr>
                                <th>Meno</th>
                                <th>Prístupové údaje</th>
                                <th>Kontakt</th>
                                <th>Mzda</th>
                                <th>Pozícia</th>
                                <th>Náplň práce</th>
                                <th>Jazyky</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($personal as $person) :
                                ?>
                                <tr>
                                    <td><?= $person->getFullName() ?></td>
                                    <td>
                                        <b>Username:</b> <?= $person->user_name ?><br>
                                        <b>PIN:</b> <?= $person->pin ?>
                                    </td>
                                    <td>
                                        <b>Email:</b> <?= $person->email ?><br>
                                        <b>Telefón:</b> <?= $person->phone ?>
                                    </td>
                                    <td><?= $person->wage ?></td>
                                    <td><?= $person->place->place_name ?? '' ?></td>
                                    <td><?= $person->work_position ?></td>
                                    <td><?= $person->lang ?></td>
                                    <td>
                                        <a href="<?= Url::to(['/promo/edit-personal?pid=' . $person->id . '&pro=' . $promo->id]) ?>"
                                           style="color: #000;" class="m-l-10">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Objednávky vstupeniek</h4>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-striped table-sm dattable" id="r34">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Číslo</th>
                                <th>Meno zákazníka</th>
                                <th>Cena</th>
                                <th>Vytvorené</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?= $this->render('orders_tbody', [
                                'orders' => $orders
                            ]) ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Zoznam hostí</h4>
                    <div class="row">
                        <div class="col-12">
                            <a href="<?= Url::to(['/promo/guest-orders', 'pid' => $promo->id]) ?>"
                               class="btn btn-sm btn-secondary">
                               Objednávky hostí
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-striped table-sm dattable">
                            <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>Meno</th>
                                <th>Kontakt</th>
                                <th>Dátum narodenia/Vek</th>
                                <th>Jazyk</th>
                                <th>Promokód</th>
                                <th>Stav účtu (kredit)</th>
                                <th>Miesto</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($guests as $guest) :
                                ?>
                                <tr>
                                    <td></td>
                                    <td><?= $guest->id ?></td>
                                    <td><?= $guest->getFullName() ?></td>
                                    <td>
                                        <?php
                                        $contact = [];
                                        if (!empty($guest->email)) {
                                            $contact[] = $guest->email;
                                        }
                                        if (!empty($guest->phone)) {
                                            $contact[] = $guest->phone;
                                        }
                                        ?>
                                        <?= implode('<br>', $contact); ?>
                                    </td>
                                    <td>
                                        <?= $guest->birth_date ?>
                                        <?php
                                        if (!empty($guest->age)) {
                                            echo ' / ' . $guest->age . 'r';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $guest->lang ?></td>
                                    <td><?= $guest->promo_code ?></td>
                                    <td><?= $guest->balance ?></td>
                                    <td>
                                        Rad: <?= $guest->seat_row ?><br>
                                        Sedadlo: <?= $guest->seat_col ?>
                                    </td>
                                    <td>
                                        <a href="<?= Url::to(['/promo/guest-detail?gid=' . $guest->id . '&pid=' . $promo->id]) ?>"
                                           style="color: #000;" class="m-l-10">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($guest->balance > 0) : ?>
                                            <a href="javascript:void(0)"
                                               class="m-l-10 text-dark refund"
                                               data-oid="<?= $guest->id ?>"
                                               title="refund"
                                            >
                                                <i class="fas fa-undo"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<<JS
    $(function() {
        $('.dattable').DataTable({
            order: []
        });
    });
    
    const slugify = str =>
      str
        .toLowerCase()
        .trim()
        .normalize('NFKD').replace(/[^\w\s.-_\/]/g, '')
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    
    $('#title').on('keyup', function (){
       $('#slug').val(slugify($(this).val())); 
    });
    
    $('.del-ord').on('click', function (){
        const oid = $(this).data('oid');
        if (confirm('Naozaj chcete odstrániť túto objednávku?')) {
            $.ajax({
                url: '/backoffice/promo/delete-order',
                method: 'post',
                data: {
                    oid: oid,
                    _csrf: $('input[name="_csrf"]').val()
                },
                success: function (res) {
                    if (res.status === 'ok') {
                        $('#r34 tbody').empty().append(res.tbody);
                    } else {
                        alert(res.message);
                    }
                }
            });
        }
    }); 
    
    $('.reop-ord').on('click', function (){
        const oid = $(this).data('oid');
        if (confirm('Naozaj chcete aktivovať túto objednávku?')) {
            $.ajax({
                url: '/backoffice/promo/reopen-order',
                method: 'post',
                data: {
                    oid: oid,
                    _csrf: $('input[name="_csrf"]').val()
                },
                success: function (res) {
                    if (res.status === 'ok') {
                        $('#r34 tbody').empty().append(res.tbody);
                    } else {
                        alert(res.message);
                    }
                }
            });
        }
    });
    
    $(document).on('click', '.refund', function (){
        const gid = $(this).data('oid');
 
        if (confirm('Naozaj chcete vrátiť peniaze?')) {
           
            $.ajax({
                url: '/backoffice/promo/refund',
                method: 'post',
                data: {
                    gid: gid,
                    _csrf: $('input[name="_csrf"]').val()
                },
                success: function (res) {
                    if (res.status === 'ok') {
                        //  location.reload();
                    } else {
                        alert(res.message);
                    }
                }
            });
        }
    });
    
JS;
$this->registerJS($js);
