<?php
/**
 * @var $this \yii\web\View
 * @var $order \common\models\Order
 * @var $customer string
 * @var $dueDate DateTime
 * @var $lang string
 * @var $referralCode string
 * @var $referAFriendCard string
 */
?>
<h2>Kedves <?= $customer ?>!</h2>

<p>Köszönjük rendelését!</p>

<p>Az alábbiakban megtalálja a jegyét.</p>
<p>
    Ezt a jegyet elég lesz belépéskor felmutatni az okostelefonján, vagy ha nem rendelkezik ilyen készülékkel,
    nyomtassa ki és hozza magával a rendezvényre.</p>

<h3>Mire kell a QR kód?</h3>
<p>
A QR kód segítségével tudjuk kedves vendégeinknek biztosítani egyedi profiljaikat rendszerünkben, ahol
    mindenki a  mobiltelefonján keresztül a saját profilján az ülőhelyére rendelhet a bárból, vagy qrkódja segítségével
    ( virtuális jegy/karszalag) fizethet a bárban vagy az asztalánál.
</p>

<h3>Miért jó ez Önnek?</h3>

<ol>
    <li>Nem kell feleslegesen sorban állni – a székéhez kapja a megrendelését a bárból</li>
    <li>Nem kell várni a pénz váltására</li>
    <li>Hölgyeknek nem kell táskájukat állandóan maguknál tartani, bárhol tudnak fizetni karszalagjukkal</li>
    <li>Bármikor ellenőrizheti fogyasztását, akár  másnap reggel otthon is</li>
</ol>

<h3>Belépés</h3>

<ol>
    <li>Belépéskor megkapja a virtuális jegyéhez a karszalagját, amely szintén egy egyedi qrkóddal lesz ellátva,
        aminek a segítségével egyszerűen és biztonságosan rendelhet, majd fizethet a rendezvényen a bárban.</li>
    <li>Leadja a ruhatárban a kabátját</li>
    <li>Hoszteszeink a helyére kísérik Önt</li>
</ol>

<p>A egyenlegét hoszteszeinknél lehet majd feltölteni.<br>
A jövőben bankkártyás feltöltésre is lehetősége lesz a helyszínen és akár otthonról is, ha az Ön segítségével
    sikerül tökéletesítenünk rendszerünket.
</p>

<h3>Feltöltés</h3>

<ol>
    <li>A feltöltésnél Ön jelzi, hogy mennyi pénzt szeretne virtuális számlájára helyezni,</li>
    <li>Hoszteszünk feltölti az Ön egyedi számláját a kredittel</li>
    <li> Miután megbizonyosodtak róla, hogy a kreditje az Ön számlájára került, kifizeti a hosztesznek az összeget.</li>
</ol>

<p>Minden egyes tranzakcióról értesítést fog kapni üzenetben az Ön által megadott email címre
    és az egyedi profiljába is.</p>

<h3>Visszautalás</h3>

<p>
    Nem kell attól tartania, hogy megmaradt kreditjei elvesznek.
    A rendezvény után, eldöntheti, hogy, mit szeretne kezdeni kreditjeivel virtuális számláján:
</p>

<ol>
    <li>Felajánlja jótékony célra ( a tombola kedvezményezettjének és mi átutaljuk az Ő IBAN-jára)</li>
    <li>Felajánlja a pincéreknek borravaló gyanánt</li>
    <li>Visszautaljuk az Ön által megadott IBAN-ra</li>
</ol>

<p>A rendezvényt követő első héten belül minden pénzügyi tranzakciót elvégzünk.</p>
<p>
Ajánlja barátainak és ismerőseinek rendezvényünket, és mi megjutalmazzuk Önt és barátját is 5€ értékű kredittel,
    amit jóváírunk a saját profilján és beváltható rendezvényünkön a bárban.
Nincs más dolga, mint elküldeni ezt az egyedi PROMO kódot
</p>

<p class="w-60 p-10 text-center" style="margin: auto; font-size: 20pt;">
    <a href="https://fbcharity.aoreal.sk/hu/<?= $referralCode ?>" target="_blank">
        <img src="https://www.aoreal.sk/media/output/cards/<?= $referAFriendCard  ?>" alt="">
    </a>
</p>

<p>barátainak és ismerőseinek, és minden egyes jegyért cserébe, amit az Ön kódján keresztül vásároltak meg, jóváírásra
    kerülnek kreditjei a rendszerünkben. az ismerőseinek és minden egyes jegyért cserébe, amit az Ön kódján keresztül
    vásároltak meg, jóváírásra kerülnek kreditjei a rendszerünkben.</p>
<p>TIPP- kód küldésére- sms, Viber, WhatsApp, Messanger, Facebook, Instagramm, LinkedIn, email.</p>

<p>További szép napot kívánunk!</p>

<p>Üdvözlettel,</p>

<p>FB Charity csapata</p>

<p>
    Mail: fbcharity@aoreal.sk<br>
    Telefonszám: +421 948 009 989
</p>

<h3>RENDELÉSI ADATAID</h3>

<table class="w-60 mb-2">
    <tr>
        <td>
            <b>Megr.szám:</b>
        </td>
        <td>
            <?= $order->code ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>Megr. kelte:</b>
        </td>
        <td>
            <?= (new DateTimeImmutable($order->created_at))->format("Y.m.d") ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>Fizetési határidő:</b>
        </td>
        <td>
            <?= $dueDate->format('Y.m.d') ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>Számlaszám:</b>
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
        <td>Megjegyzés:</td>
        <td>Fizetésnél kérjük tüntesse fel a teljes nevét és a megrendelésének a számát is.</td></tr>
</table>
<table class="w-60 mb-2">
    <thead>
    <tr>
        <th>Terméknév</th>
        <th>Menny.</th>
        <th class="num">Ár</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($order->items as $item) : ?>
    <tr>
        <td>'<?=  $item->getProductLang('hu')->one()->name ?>' belépőjegy</td>
        <td><?= $item->amount ?></td>
        <td class="num"><?= $item->price ?> €</td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="2" style="border-top: 1px solid gray;"><b>Végösszeg:</b></td>
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
        .mb-2 {
            margin-bottom: 2rem;
        }
CSS;
$this->registerCSS($css);

