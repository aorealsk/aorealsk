<h2>Vážený/á <?= $customer ?>!</h2>

<p>Ďakujeme za Vašu objednávku!</p>

<p>Posielame Vám informáciu o Vašej objednávke.</p>

<table style="width: 60%; margin-bottom: 20px">
    <thead>
    <tr>
        <th>#</th>
        <th>Produkt</th>
        <th>MJ</th>
        <th>Množ.</th>
        <th>J.C.</th>
        <th>Spolu</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($order->items as $item) : ?>
        <tr>
            <td><?= $item->id ?></td>
            <td><?= $item->detail->stockDetail->getTitle() ?></td>
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
        <td>Spolu:</td>
        <td><?= $order->total ?></td>
    </tr>
    </tfoot>
</table>


<p>Prajeme Vám príjemnú zábavu!</p>

<p>S pozdravom,</p>

<p>tím FB Charity</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefón: +421 948 009 989
</p>

