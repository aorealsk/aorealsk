<h2>Kedves <?= $customer ?>!</h2>

<p>Köszönjük részvételét!</p>

<p>Küldjük megrendelésének az összefoglalóját és az Ön által kiválasztott PIN-kódot,
    amellyel később bejelentkezhet a fiókjába.</p>
<p>Kérjük, gondosan mentse el PIN kódját. Elvesztése esetén ne habozzon kapcsolatba lépni velünk.</p>

<table style="width: 60%; margin-bottom: 20px;">
    <tr>
        <td>
            <b>Megr.szám:<</b>
        </td>
        <td><?= $order->code ?></td>
    </tr>
    <tr>
        <td>
            <b>Belépőjegy száma:</b>
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
            <b>Telefonszám:</b>
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

<p>További szép napot kívánunk!</p>

<p>Üdvözlettel,</p>

<p>FB Charity csapata</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefonszám: +421 948 009 989
</p>

