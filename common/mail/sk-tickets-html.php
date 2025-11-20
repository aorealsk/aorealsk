<?php
/**
 * @var $this \yii\web\View
 * @var $order \common\models\Order
 * @var $customer string
 * @var $dueDate string
 * @var $lang string
 * @var $referralCode string
 * @var $referAFriendCard string
 */
?>
<h2>Vážený/á <?= $customer ?>!</h2>

<p> Ďakujeme Vám za objednávku!</p>

<p>V prílohe Vám posielame Vašu vstupenku.</p>
<p>
    Vstupenku stačí predložič pri vstupe z Vášho mobilného zariadenia, alebo ak nemáte také zariadenie, tak vytlačte
    ju a doneste ju so sebou na akciu.
</p>

<h3>Na čo slúži QR kód?</h3>

<p>
    Pomocou QR kódu Vám dokážeme zabezpečiť jedinečný profil v našom systéme, kde každý môže cez svoj mobilný telefón
    objednať pitie na svoje miesto z baru, alebo pomocou QR kódu (virtuálna vstupenka/náramok) zaplatiť v bare alebo
    pri stolíku.
</p>

<h3>Prečo je to pre Vás výhodné?</h3>

<ol>
    <li>Nemusíte zbytočne stáť v rade – Vašu objednávku dostanete priamo k Vášmu stolu z baru</li>
    <li>Nemusíte čakať na výmenu peňazí</li>
    <li>Dámy nemusia mať svoju tašku neustále pri sebe, môžu platiť kdekoľvek s náramkom</li>
    <li>Hoci kedy môžete skontrolovať svoju spotrebu, aj na druhý deň ráno doma</li>
</ol>

<h3>Vstup</h3>

<ol>
    <li>Pri vsupe dostanete k virtuálnej vstupenke náramok, ktorý bude tiež označený jedinečným QR kódom, ktorým
        jednoducho a bezpečne môžete objednať a zaplatiť v bare na akcii.</li>
    <li>Odovzdáte si Vašu bundu v šatni</li>
    <li>Naše hostesky Vás sprevádzajú na Vaše miesto</li>
</ol>

<p>Svoj zostatok si môžete doplniť u našich hostesiek.<br>
    V budúcnosti bude možnosť doplniť kredit aj bankovou kartou na mieste a dokonca aj z domu, ak sa nám podarí
    dokonalejšie vylepšiť náš systém.
</p>

<h3>Dobíjanie</h3>

<ol>
    <li>Pri dobíjaní si vyberiete, koľko peňazí chcete vložiť na svoj virtuálny účet,</li>
    <li>Naša hosteska Vám dobije Váš jedinečný účet kreditom</li>
    <li>Potom, čo sa presvedčíte, že Váš kredit je na Vašom účte, zaplatíte hosteske sumu.</li>
</ol>

<p>O každej tranzakcii dostanete oznámenie v správe na Váš email a do Vášho jedinečného profilu.</p>

<h3>Vrátenie peňazí</h3>

<p>Nemusíte sa obávať, že zostávajúci kredit Vám prepadne. Po akcii si môžete vybrať, čo chcete robiť so svojim
    nevyužitým kreditom na Vašom virtuálnom účte:</p>

<ol>
    <li>Ponúknete ho na charitatívny účel (pre obdarovaného tomboly a my mu prevedieme na jeho IBAN)</li>
    <li>Ponúknete ho ako prepitné čašníkom</li>
    <li>Vrátime Vám ho na Váš uvedený IBAN</li>
</ol>

<p>Po akcii Vám v priebehu prvého týždňa vybavíme všetky finančné transakcie.</p>
<p>
    Odporučte naše podujatie svojim priateľom a známym, a odmeníme Vás a Vášho priateľa kreditom v hodnote 5 €,
    ktorý pripíšeme na Váš vlastný profil a môžete si ho uplatniť na našej akcii v bare.
    Stačí, ak pošlete tento jedinečný PROMO kód
</p>

<p class="w-60 p-10 text-center" style="margin: auto; font-size: 20pt;">
    <a href="https://fbcharity.aoreal.sk/hu/<?= $referralCode ?>" target="_blank">
        <img src="https://www.aoreal.sk/media/output/cards/<?= $referAFriendCard  ?>" alt="">
    </a>
</p>

<p>svojim priateľom a známym, a za každú jednu vstupenku, ktorú si kúpia cez Váš kód, pripíšeme kredit na Váš
    účet. svojim priateľom a známym, a za každú jednu vstupenku, ktorú si kúpia cez Váš kód, pripíšeme kredit na Váš
    účet.
</p>

<p>TIP- na posielanie kódu - sms, Viber, WhatsApp, Messanger, Facebook, Instagramm, LinkedIn, email.</p>

<p>Prajeme Vám pekný a úspešný deň!</p>

<p>S pozdravom,</p>

<p>tím FB Charity</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefonszám: +421 948 009 989
</p>

<h3>INFORMÁCIE O OBJEDNÁVKE</h3>

<table class="w-60 mb-2">
    <tr>
        <td>
            <b>Číslo objednávky / VS:</b>
        </td>
        <td><?= $order->code ?></td>
    </tr>
    <tr>
        <td>
            <b>Dátum objednávky:</b>
        </td>
        <td><?= $order->created_at ?></td>
    </tr>
    <tr>
        <td>
            <b>Dátum splatnosti:</b>
        </td>
        <td><?= $dueDate->format("d.m.Y") ?></td>
    </tr>
    <tr>
        <td>
            <b>Bankový účet:</b>
        </td>
        <td>SK45 0900 0000 0001 9189 3808</td>
    </tr>
    <tr>
        <td>
            <b>BIC SWIFT:</b>
        </td>
        <td>GIBASKBX</td>
    </tr>
    <tr>
        <td>Poznámka:</td>
        <td>Poprosíme Vás, aby Ste pri platbe uviedli do komentára svoje celé meno a číslo objednávky.</td>
    </tr>
</table>
<table class="w-60 mb-2">
    <thead>
    <tr>
        <th>Názov</th>
        <th>Množstvo.</th>
        <th class="num">Cena</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($order->items as $item) : ?>
        <tr>
            <td>Vstupenka '<?=  $item->getProductLang('sk')->one()->name ?>'</td>
            <td><?= $item->amount ?></td>
            <td class="num"><?= $item->price ?> €</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2" style="border-top: 1px solid gray;"><b>Spolu:</b></td>
        <td class="num" style="border-top: 1px solid gray;"><?= $order->total ?>  €</td>
    </tr>
    </tfoot>
</table>

<?php
$css = <<<CSS
    .num {
            text-align: right;
        }
        .w-40 {
            width: 40%;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
        }

        td {
            padding: 10px;
        }
        table {
            border-spacing: 0px;
        }
        .w-60 {
            width: 60%;
        }
        .p-10 {
            padding: 10px;
        }
        .text-center {
            text-align: center;
        }
        .mt-1 {
            margin-top: 1rem;
        }
        img {
            width: 75%;
        }
CSS;
$this->registerCSS($css);

