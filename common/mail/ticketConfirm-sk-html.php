<h2>Vážený/á <?= $customer ?>!</h2>

<p> Ďakujeme Vám za účasť!</p>

<p>Posielame Vám rekapituláciu Vašej objednávky a Vami zvolený PIN kód na neskoršie prihlásenie do Vášu účtu.</p>
<p>Prosíme Vás aby ste Vami zvolený PIN dôkladne uložili. V prípade straty neváhajte nás kontaktovať.</p>

<table style="width: 60%; margin-bottom: 20px;">
    <tr>
        <td>
            <b>Číslo objednávky:</b>
        </td>
        <td><?= $order->code ?></td>
    </tr>
    <tr>
        <td>
            <b>Číslo vstupenky:</b>
        </td>
        <td><?= $guest->badge_code ?></td>
    </tr>
    <tr>
        <td>
            <b>Email:</b>
        </td>
        <td><?= $guest->email ?></td>
    </tr>
    <tr>
        <td>
            <b>Telefón:</b>
        </td>
        <td><?= $guest->phone ?></td>
    </tr>
    <tr>
        <td>
            <b>PIN:</b>
        </td>
        <td><?= $guest->pin ?></td>
    </tr>
</table>

<p>Prajeme Vám pekný a úspešný deň!</p>

<p>S pozdravom,</p>

<p>tím FB Charity</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefón: +421 948 009 989
</p>
