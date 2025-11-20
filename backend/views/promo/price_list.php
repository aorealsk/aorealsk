<?php

/**
 * @var $items
 * @var $pricelist
 */
use yii\helpers\Url;

$this->title = Yii::t('app', 'Cenník');
$emptyPriceList = count($pricelist) === 0;
?>

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-12 align-self-center">
            <h4 class="text-themecolor"><?= $this->title ?></h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <a href="<?= Url::to(['/promo/detail?id=' . $_GET['promo_id']]) ?>"
               class="btn btn-danger text-white">Späť
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form role="form" method="post">
                        <input type="hidden"
                               name="<?= Yii::$app->request->csrfParam ?>"
                               value="<?= Yii::$app->request->csrfToken ?>">
                        <input type="hidden" name="PriceList[promo_id]" value="<?= $_GET['promo_id']?>">
                        <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Názov</th>
                                    <th class="text-center">Mnz. [L]</th>
                                    <th class="text-center">0,4dl [&euro;]</th>
                                    <th class="text-center">0,75dl [&euro;]</th>
                                    <th class="text-center">1dl [&euro;]</th>
                                    <th class="text-center">0,5l [&euro;]</th>
                                    <th class="text-center">1l [&euro;]</th>
                                    <th class="text-center">fl [&euro;]</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $row) : ?>
                                <tr style="background-color: #8d97ad">
                                    <td colspan="10" style="color: white; font-size: 0.9rem">
                                        <?= $row->langs[1]->title; ?>
                                    </td>
                                </tr>
                                <?php foreach ($row->items as $item) : ?>
                                    <tr>
                                        <?php
                                        $checked = ($emptyPriceList) || array_key_exists($item->id, $pricelist) ?
                                            ' checked' :
                                            '';
                                        ?>
                                        <td>
                                            <input
                                                    type="checkbox"
                                                    value="<?= $item->id ?>"
                                                    name="PriceList[items][<?= $item->id ?>][item_id]"<?= $checked?>>
                                        </td>
                                        <td><?= $item->title ?></td>
                                        <td style="width: 8%">
                                            <?php
                                            $amount = $item->amount;
                                            if (!$emptyPriceList && array_key_exists($item->id, $pricelist)) {
                                                $amount = $pricelist[$item->id]['amount'];
                                            }
                                            ?>
                                            <input
                                                    type="text"
                                                    name="PriceList[items][<?= $item->id ?>][amount]"
                                                    class="form-control"
                                                    value="<?= $amount ?>"
                                            >
                                        </td>
                                        <td style="width: 8%">
                                            <?php
                                            $price_04 = $item->price_04;
                                            if (!$emptyPriceList && array_key_exists($item->id, $pricelist)) {
                                                $price_04 = $pricelist[$item->id]['price_04'];
                                            }
                                            ?>
                                            <input
                                                    type="text"
                                                    name="PriceList[items][<?= $item->id ?>][price_04]"
                                                    class="form-control" value="<?= $price_04 ?>"
                                            >
                                        </td>
                                        <td style="width: 8%">
                                            <?php
                                            $price_075 = $item->price_075;
                                            if (!$emptyPriceList && array_key_exists($item->id, $pricelist)) {
                                                $price_075 = $pricelist[$item->id]['price_075'];
                                            }
                                            ?>
                                            <input
                                                    type="text"
                                                    name="PriceList[items][<?= $item->id ?>][price_075]"
                                                    class="form-control" value="<?= $price_075 ?>"
                                            >
                                        </td>
                                        <td style="width: 8%">
                                            <?php
                                            $price_1 = $item->price_1;
                                            if (!$emptyPriceList && array_key_exists($item->id, $pricelist)) {
                                                $price_1 = $pricelist[$item->id]['price_1'];
                                            }
                                            ?>
                                            <input
                                                    type="text"
                                                    name="PriceList[items][<?= $item->id ?>][price_1]"
                                                    class="form-control"
                                                    value="<?= $price_1 ?>"
                                            >
                                        </td>
                                        <td style="width: 8%">
                                            <?php
                                            $price_5 = $item->price_5;
                                            if (!$emptyPriceList && array_key_exists($item->id, $pricelist)) {
                                                $price_5 = $pricelist[$item->id]['price_5'];
                                            }
                                            ?>
                                            <input
                                                    type="text"
                                                    name="PriceList[items][<?= $item->id ?>][price_5]"
                                                    class="form-control"
                                                    value="<?= $price_5 ?>"
                                            >
                                        </td>
                                        <td style="width: 8%">
                                            <?php
                                            $price_10 = $item->price_10;
                                            if (!$emptyPriceList && array_key_exists($item->id, $pricelist)) {
                                                $price_10 = $pricelist[$item->id]['price_10'];
                                            }
                                            ?>
                                            <input
                                                    type="text"
                                                    name="PriceList[items][<?= $item->id ?>][price_10]"
                                                    class="form-control"
                                                    value="<?= $price_10 ?>"
                                            >
                                        </td>
                                        <td style="width: 8%">
                                            <?php
                                            $price_bottle = $item->price_bottle;
                                            if (!$emptyPriceList && array_key_exists($item->id, $pricelist)) {
                                                $price_bottle = $pricelist[$item->id]['price_bottle'];
                                            }
                                            ?>
                                            <input
                                                    type="text"
                                                    name="PriceList[items][<?= $item->id ?>][price_bottle]"
                                                    class="form-control"
                                                    value="<?= $price_bottle ?>"
                                            >
                                        </td>
                                        <td style="width: 10%"></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success text-white">
                                    <i class="fas fa-save"></i> Uložiť
                                </button>
                                <a href="<?= Url::to(['/promo/detail?id=' . $_GET['promo_id']]) ?>"
                                   class="btn btn-danger text-white">
                                    Späť
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
