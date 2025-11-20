<?php

/**
 * @var $orders
 * @var $guest
 * @var $fdata
 */



?>

<h2>Kedves <?= $guest->getFullName() ?>!</h2>

<p style="margin-top: 2rem">Köszönjük, hogy jelenlétével egy jó ügyet támogatott. Az alábbiakban megtalálja a
    visszatérítési kérelmének részletét: </p>

<table style="margin-bottom: 3rem; margin-top: 3rem; width: 80%">
    <tbody>
    <?php
    $c_amt = 0;
    $c_style = 'text-decoration: line-through;';
    if (isset($fdata['c_part']) && $fdata['c_part'] == 'on') {
        $c_amt = $fdata['c_part_amt'];
    }
    if (isset($fdata['c_full']) && $fdata['c_full'] == 'on') {
        $c_style = '';
    }
    ?>
    <tr>
        <td rowspan="2"><b>Felajánlom jótékony célra a tombola kedvezményezettjének</b></td>
        <td style="text-align: center;">
            <span style="<?= $c_style ?>">az egész összeget</span>
        </td>
    </tr>
    <tr>
        <td style="text-align: right;"><?= number_format($c_amt, 2) ?> &euro;</td>
    </tr>
    <!-- XXXX  -->
    <?php
    $t_amt = 0;
    $t_style = 'text-decoration: line-through;';
    if (isset($fdata['t_part']) && $fdata['t_part'] == 'on') {
        $t_amt = $fdata['t_part_amt'];
    }
    if (isset($fdata['t_full']) && $fdata['t_full'] == 'on') {
        $t_style = '';
    }
    ?>
    <tr>
        <td rowspan="2"><b>Felajánlom a pincéreknek borravaló gyanánt</b></td>
        <td style="text-align: center;"><span style="<?= $t_style ?>">az egész összeget</span></td>

    </tr>
    <tr>
        <td style="text-align: right;"><?= number_format($t_amt, 2) ?> &euro;</td>
    </tr>
    <!-- XXXX  -->
    <?php
    $b_amt = 0;
    $b_style = 'text-decoration: line-through;';
    if (isset($fdata['b_part']) && $fdata['b_part'] == 'on') {
        $b_amt = $fdata['b_part_amt'];
    }
    if (isset($fdata['b_full']) && $fdata['b_full'] == 'on') {
        $b_style = '';
    }
    ?>
    <tr>
        <td rowspan="2"><b>Küldjék vissza a számlámra</b></td>
        <td style="text-align: center;">
            <span style="<?= $b_style ?>">az egész összeget</span>
        </td>
    </tr>
    <tr>
        <td style="text-align: right;"><?= number_format($b_amt, 2) ?> &euro;</td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td>
            <b>IBAN:</b> <?= $fdata['iban'] ?? '' ?>
        </td>
        <td>
            <i style="font-size: small;">1 kredit = 1 &euro;</i>
        </td>
    </tr>
    </tfoot>
</table>

<p>Kérelmének feldolgozása után virtuális számláján <b>0,00</b> kredit marad. </p>

<p>További szép napot kívánunk!</p>

<p>Üdvözlettel,</p>

<p>FB Charity csapata</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefonszám: +421 948 009 989
</p>
<?php
$css = <<<CSS
        table, th, td {
            border: 1px solid black;
            }
        td {
            padding: 5px;
        } 
CSS;
$this->registerCss($css);
