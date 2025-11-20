<?php
/**
 * @var $customer
 * @var $orders
 * @var $guest
 */

?>

<h2>Kedves <?= $customer ?>!</h2>

<p>Köszönjük, hogy jelenlétével egy jó ügyet támogatott. </p>

<p>Az alábbiakban megtalálja a rendezvényen leadott megrendelései összefoglalóját: </p>

<table style="width: 60%; margin-bottom: 20px; border:1px">
    <thead>
        <tr style="text-align: center">
            <th>#</th>
            <th>Dátum</th>
            <th>Termék neve</th>
            <th>Egység</th>
            <th>Menny.</th>
            <th>E.Ár.</th>
            <th>Ár</th>
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
                <td><?= $item->detail->stockDetail->getTitle('hu') ?></td>
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
            <td colspan="6" style="text-align: right">Összesen</td>
            <td style="text-align: right"><?= number_format($total, 2) ?> &euro;</td>
        </tr>
    </tfoot>
</table>

<p>Megrendelései után virtuális számláján <b><?= $guest->balance ?></b> kredit maradt. </p>

<p>A következő linkre kattintva eldöntheti, hogy mit szeretne tenni a számláján fennmaradó összeggel.</p>

<p style="margin-top: 40px; margin-bottom: 40px;">
    <a
            href="https://www.aoreal.sk/promo/refund-money/<?= $guest->id ?>"
            style="background-color: #0d4982; color: white; padding: 10px; text-decoration: none; border-radius: 5px;"
    >
        Tovább a döntéshez
    </a>
</p>

<p>További szép napot kívánunk!</p>

<p>Üdvözlettel,</p>

<p>FB Charity csapata</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefonszám: +421 948 009 989
</p>


