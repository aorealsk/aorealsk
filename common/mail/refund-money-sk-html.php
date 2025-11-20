<?php
/**
 * @var $customer
 * @var $orders
 * @var $guest
 */

?>

<h2>Vážený/á <?= $customer ?>!</h2>

<p>Ďakujeme, že svojou prítomnosťou ste podporili dobrú vec.</p>

<p>Nižšie nájdete súhrn objednávok zadaných na podujatí: </p>

<table style="width: 60%; margin-bottom: 20px; border:1px">
    <thead>
    <tr style="text-align: center">
        <th>#</th>
        <th>Dátum</th>
        <th>Názov</th>
        <th>MJ</th>
        <th>Množ.</th>
        <th>J.C.</th>
        <th>Cena</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total = 0;
    foreach ($orders as $order) :
        $total += $order->total;
        ?>

        <?php foreach ($order->items as $item) : ?>
        <tr>
            <td width="5%" style="text-align: right"><?= $item->id ?></td>
            <td style="text-align: center;"><?= $item->created_at ?></td>
            <td><?= $item->detail->stockDetail->getTitle('sk') ?></td>
            <td style="text-align: center;"><?= $item->unit ?></td>
            <td style="text-align: right;"><?= $item->amount ?></td>
            <td style="text-align: right;"><?= number_format($item->unit_price, 2) ?> &euro;</td>
            <td style="text-align: right;"><?= number_format($item->price, 2) ?> &euro;</td>
        </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="6" style="text-align: right">Spolu</td>
        <td style="text-align: right"><?= number_format($total, 2) ?> &euro;</td>
    </tr>
    </tfoot>
</table>

<p>Po Vašich objednávkach Vám na Vašom virtuálnom účte zostalo <b><?= $guest->balance ?></b> kreditu. </p>

<p>Kliknutím na nasledujúci odkaz sa môžete rozhodnúť, čo chcete urobiť so zostatkom na svojom účte.</p>

<p style="margin-top: 40px; margin-bottom: 40px;">
    <a
        href="https://www.aoreal.sk/promo/refund-money/<?= $guest->id ?>"
        style="background-color: #0d4982; color: white; padding: 10px; text-decoration: none; border-radius: 5px;"
    >
        Prejsť na rozhodnutie
    </a>
</p>


<p>Prajeme Vám pekný deň!</p>

<p>S pozdravom,</p>

<p>Tím FB Charity</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefón: +421 948 009 989
</p>



