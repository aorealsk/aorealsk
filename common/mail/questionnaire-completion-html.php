<div class="container">
    <div class="img">
        <img src="<?= Yii::$app->request->baseUrl . 'backend/web/images/watermark.png' ?>" alt="logo">
    </div>
    <h1 style="text-align: center; margin-bottom: 10rem;">
        Ďakujeme za využívanie naších služieb.
    </h1>
    <h2>
        Rekapitulácia informacií, ktoré ste uviedli vo formulári:
    </h2>
    <h3>
        Infromácie k založeniu s.r.o
    </h3>
    <p>
        Záujem o založenie s.r.o. s DPH registráciou:
        <?php
        echo $clientReq['dph_registration'] == 0 ? "Nie" :  'Áno';
        ?>
    </p>
    <p>
        Záujem o založenie s.r.o. s DPH registráciou?:
        <?php
        if ($clientReq['ready_made_sro'] == 'no') {
            echo "Nie";
        } else if ($clientReq['ready_made_sro'] == 'yes with assigned DIC') {
            echo 'Áno/mám záujem kúpiť už existujúcu firmu s prideleným DIČ a možnosťou ihneď fakturovať (499€)- Po podpise dokumentov môžete faktúrovať a podpisovať zmluvy';
        } else {
            echo 'Áno/mám záujem kúpiť už existujúcu firmu aj s bankovým účtom, ktorý môžem ihneď používať za 549€ (Inak je možné založiť účet až po zápise v obchodnom registri.';
        }
        ?>
    </p>
    <p>
        Záujem o virtuálne sídlo:
        <?php
        if ($clientReq['virtual_studio'] == 'no') {
            echo "Nie/mám vlastné sídlo";
        } else if ($clientReq['virtual_studio'] == 'Černyševského 10 (BA5)') {
            echo "Áno / Černyševského 10 (BA5) 90€/rok + 13€ jednorázovo za preberanie pošty";
        } else if ($clientReq['virtual_studio'] == 'Nám. Á. Vámbéryho 5249/13A (DS)') {
            echo "Áno / Nám. Á. Vámbéryho 5249/13A (DS) 90€/rok + 13€ jednorázovo za preberanie pošty";
        }
        ?>
    </p>
    <p>
        Zabezpečené účtovníctvo:
        <?php
        if ($clientReq['accounting'] == 'no') {
            echo "Nie/mám vlastné sídlo";
        } else if ($clientReq['accounting'] == 'no-price-offer') {
            echo "Účtovníctvo mám zabezpečené - nemám záujem o cenovú ponuku od ALPHA-OMEGA REAL & CONSULTING s.r.o.";
        } else if ($clientReq['accounting'] == 'yes with price offer') {
            echo "Účtovníctvo mám zabezpečené - mám záujem o cenovú ponuku od ALPHA-OMEGA REAL & CONSULTING s.r.o.";
        } else if ($clientReq['accounting'] == 'searching for accountant') {
            echo "Účtovníka si momentálne hľadám";
        } else if ($clientReq['accounting'] == 'no accountant') {
            echo "Účtovníka zatiaľ nemám";
        }
        ?>
    </p>
    <p>
        Záujem o založenie bankového účtu:
        <?php
        if ($clientReq['bank'] == 'recomendation') {
            echo "Žiadam aby Ste mi odporučali najlepšie riešenie";
        } else if ($clientReq['bank'] == 'fio-banka') {
            echo "Áno-Fio Banka";
        } else if ($clientReq['bank'] == 'mBank') {
            echo "Áno-mBank";
        } else if ($clientReq['bank'] == 'bks-bank') {
            echo "Áno-BKS Bank";
        } else if ($clientReq['bank'] == 'unicredit-bank') {
            echo "Áno-UniCredit Bank";
        } else if ($clientReq['bank'] == 'slovenska-sporitelna') {
            echo "Áno-Slovenská Sporiteľňa";
        } else if ($clientReq['bank'] == 'vub-banka') {
            echo "Áno-VÚB BANKA";
        } else if ($clientReq['bank'] == 'prima-banka') {
            echo " Áno-Prima Banka";
        } else if ($clientReq['bank'] == 'tatra-banka') {
            echo "Áno-Tatra Banka";
        } else if ($clientReq['bank'] == '365') {
            echo "Áno-365";
        } else if ($clientReq['bank'] == 'Oberbank') {
            echo "Áno-Oberbank";
        } else if ($clientReq['bank'] == 'privat-banka') {
            echo " Áno-Priat Banka";
        }
        ?>
    </p>
    <p>
        Záujem o financovanie Vašej firmy cez banku:
        <?php
        if ($clientReq['finance_banking'] == 'recomendation') {
            echo "Žiadam aby Ste mi odporučali najlepšie riešenie";
        } else if ($clientReq['finance_banking'] == 'fio-banka') {
            echo "Áno-Fio Banka";
        } else if ($clientReq['bank'] == 'mBank') {
            echo "Áno-mBank";
        } else if ($clientReq['finance_banking'] == 'bks-bank') {
            echo "Áno-BKS Bank";
        } else if ($clientReq['finance_banking'] == 'unicredit-bank') {
            echo "Áno-UniCredit Bank";
        } else if ($clientReq['finance_banking'] == 'slovenska-sporitelna') {
            echo "Áno-Slovenská Sporiteľňa";
        } else if ($clientReq['finance_banking'] == 'vub-banka') {
            echo "Áno-VÚB BANKA";
        } else if ($clientReq['finance_banking'] == 'prima-banka') {
            echo " Áno-Prima Banka";
        } else if ($clientReq['finance_banking'] == 'tatra-banka') {
            echo "Áno-Tatra Banka";
        } else if ($clientReq['finance_banking'] == '365') {
            echo "Áno-365";
        } else if ($clientReq['finance_banking'] == 'Oberbank') {
            echo "Áno-Oberbank";
        } else if ($clientReq['finance_banking'] == 'privat-banka') {
            echo " Áno-Priat Banka";
        }
        ?>
    </p>
    <p>
        Záujem o firemné mobilné čisla:
        <?php
        if ($clientReq['mobile_phone'] == 'recomendation') {
            echo "Žiadam aby Ste mi odporučali najlepšie riešenie";
        } else if ($clientReq['mobile_phone'] == 'O2') {
            echo "Áno-O2";
        } else if ($clientReq['mobile_phone'] == 'telekom') {
            echo "Áno-Telekom";
        } else if ($clientReq['mobile_phone'] == 'orange') {
            echo "Áno-Orange";
        } else if ($clientReq['mobile_phone'] == '4') {
            echo "Áno-4";
        }
        ?>
    </p>
    <p>
        Záujem o firemný internet:
        <?php
        if ($clientReq['mobile_phone'] == 'recomendation') {
            echo "Žiadam aby Ste mi odporučali najlepšie riešenie";
        } else if ($clientReq['internet'] == 'O2') {
            echo "Áno-O2";
        } else if ($clientReq['internet'] == 'telekom') {
            echo "Áno-Telekom";
        } else if ($clientReq['internet'] == 'orange') {
            echo "Áno-Orange";
        } else if ($clientReq['internet'] == 'upc') {
            echo "Áno-UPC";
        }
        ?>
    </p>
    <p>
        Bude mať Vaša firma zamestnancov:
        <?php
        if ($clientReq['employees'] == 'recomendation') {
            echo "Žiadam aby Ste mi odporučali najlepšie riešenie";
        } else if ($clientReq['employees'] == '0') {
            echo "Nie nebude mať žiadneho zamestnanca";
        } else if ($clientReq['employees'] == '1') {
            echo "Áno bude mať zamestnancov - " . $clientReq['num_employees'];
        }
        ?>
    </p>
    <p>
        Záujem o stravné lístky:
        <?php
        if ($clientReq['meal_voucher'] == 'recomendation') {
            echo "Žiadam aby Ste mi odporučali najlepšie riešenie";
        } else if ($clientReq['meal_voucher'] == 'doxx') {
            echo "Ano-DOXX";
        } else if ($clientReq['meal_voucher'] == 'dejeuner') {
            echo " Áno-Up Déjeuner";
        } else if ($clientReq['meal_voucher'] == 'edenred') {
            echo "Áno-Edenred";
        }
        ?>
    </p>
    <p>
        Záujem o marketingový balík:
        <?php
        if ($clientReq['marketing_package'] == 'recomendation') {
            echo "Žiadam aby Ste mi odporučali najlepšie riešenie";
        } else if ($clientReq['marketing_package'] == 'logo') {
            echo "Áno-Logo";
        } else if ($clientReq['marketing_package'] == 'peciatka') {
            echo "Áno-Pečiatka";
        } else if ($clientReq['marketing_package'] == 'firemna-identita') {
            echo "Áno-Firemná identita";
        } else if ($clientReq['marketing_package'] == 'hlavickovy-papier') {
            echo " Áno-Hlavičkový papier";
        } else if ($clientReq['marketing_package'] == 'vizitka') {
            echo "Áno-Vizitka";
        } else if ($clientReq['marketing_package'] == 'reklamne-produkty') {
            echo "Áno-Reklamné produkty";
        } else if ($clientReq['marketing_package'] == 'mail') {
            echo "Áno-Mail";
        } else if ($clientReq['marketing_package'] == 'socialne-siete') {
            echo "Áno-Sociálne siete/FB/Insta/Linkedin";
        } else if ($clientReq['marketing_package'] == 'web-stranka') {
            echo "Áno-webová stránka";
        } else if ($clientReq['marketing_package'] == 'web-shop') {
            echo "Áno-Webshop";
        } else if ($clientReq['marketing_package'] == 'hosting') {
            echo "Áno-Hosting";
        } else if ($clientReq['marketing_package'] == 'callcenter') {
            echo "Áno-Callcenter";
        }
        ?>
    </p>
    <p>
        Záujem o zariadení Vašej kancelárie:
        <?php
        if ($clientReq['office'] == 'recomendation') {
            echo "Žiadam aby Ste mi odporučali najlepšie riešenie";
        } else if ($clientReq['office'] == 'nabytok') {
            echo "Áno-Nábytok";
        } else if ($clientReq['office'] == 'technika') {
            echo " Áno-Technika";
        } else if ($clientReq['office'] == 'nabytok+technika') {
            echo "Áno-Nábytok + Technika";
        }
        ?>
    </p>
    <h3>
        Firemné údaje
    </h3>
    <p>
        <?= $company->name . ' ' . $company->appendix ?>
    </p>
    <h3>
        Údaje o majiteľovi nehnutelnosti
    </h3>
    <?php
    foreach ($owner as $data) {
        echo "<p> $data </p>";
    }
    ?>
    <h3>
        Údaje o správcovi vkladu
    </h3>
    <?php
    foreach ($spravcaVkladu as $data) {
        echo "<p> $data </p>";
    }
    ?>
    <h3>
        Údaje o konatelovi
    </h3>
    <?php
    foreach ($konatel as $data) {
        foreach ($data as $val) {
            echo "<p> $val </p>";
        }
    }
    ?>
    <h3>
        Údaje o spoločníkovi
    </h3>
    <?php
    foreach ($spolocnik as $data) {
        foreach ($data as $val) {
            echo "<p> $val </p>";
        }
    }
    ?>
    <?php
    if ($business) {
    ?>
        <h3>
            Predmety podnikania
        </h3>
    <?php
        foreach ($business as $data) {
            foreach ($data as $val) {
                echo "<p> $val </p>";
            }
        }
    }
    ?>

    <?php
    if ($zivnost) {
    ?>
        <h3>
            Údaje o živnosti
        </h3>
    <?php
        foreach ($zivnost as $data) {
            foreach ($data as $val) {
                echo "<p> $val </p>";
            }
        }
    }
    ?>
</div>
<?php

$css = <<<CSS
    .container {
        padding: 1.5rem;
    }
    .img {
        display: flex;
        align-items: center;
        justify-content:center;
    }
CSS;
$this->registerCSS($css);
