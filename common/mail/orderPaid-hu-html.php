<h2>Kedves <?= $customer ?>!</h2>

<p>Köszönjük megrendelését!</p>

<p>Küldjük megrendelésének az összefoglalóját</p>

<table style="width: 60%; margin-bottom: 20px">
    <thead>
        <tr>
            <th>#</th>
            <th>Termék</th>
            <th>ME</th>
            <th>Mennyiség</th>
            <th>E. Ár</th>
            <th>Összesen</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order->items as $item) : ?>
        <tr>
            <td><?= $item->id ?></td>
            <td><?= $item->detail->stockDetail->getTitle('hu') ?></td>
            <td><?= $item->unit ?></td>
            <td><?= $item->amount ?></td>
            <td><?= $item->unit_price ?></td>
            <td><?= $item->price ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4"></td>
            <td>Összesen:</td>
            <td><?= $order->total ?></td>
        </tr>
    </tfoot>
</table>


<p>További jó szórakozást kívánunk!</p>

<p>Üdvözlettel,</p>

<p>FB Charity csapata</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefonszám: +421 948 009 989
</p>

