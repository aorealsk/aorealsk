<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Untitled Document</title>
    <style>
        body{
            font-family: arial;
            font-size: 11pt;
        }
        @media print {
            body {
                width: 21cm;
                height: 29.7cm;
                font-family: arial;
                font-size: 11pt;
            }

            table.bordered, table.bordered th, table.bordered td {
                border: 1px solid black;
                border-collapse: collapse;
                padding: 5px;
            }
            table.bordered > thead > tr > th {
                font-weight: bold;
                color: white;
                background-color:  black;
                font-size: 11pt;
            }
        }
        .dokument_nazov{
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
        }
        p {
            text-align: justify;
            margin: 0px;
            padding: 0;
        }
        .w-full{
            width: 100%;
        }
        .w-50{
            width: 50%;
        }
        .f-11 {
            font-size: 11pt;
        }
        .f-10 {
            font-size: 10pt;
        }
        .mb-20 {
            margin-bottom: 20px;
        }
        .mb-40 {
            margin-bottom: 40px;
        }
        .w-80 {
            width: 80%;
        }
        .w-90 {
            width: 90%;
         }
        table.bordered, table.bordered th, table.bordered td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 5px;
        }
        table.bordered > thead > tr > th {
            font-weight: bold;
            color: white;
            background-color:  black;
            font-size: 11pt;
        }
    </style>
</head>

<body>
<table class="dokument_nazov w-full mb-40">
    <tr><td>PREBERACÍ  PROTOKOL</td></tr>
</table>
<table class="mb-40 w-50">
    <tr>
        <td colspan="3"><b>Predávajúci:</b></td>
    </tr>
    <tr>
        <td>Meno a priezvisko (názov): </td>
        <td width="5"></td>
        <td>{{seller_full_name}}</td>
    </tr>
    <tr>
        <td>Trvalé bydlisko (sídlo): </td>
        <td width="5"></td>
        <td>{{seller_full_address}}</td>
    </tr>
    <tr>
        <td>Dátum narodenia (IČO/r.č.): </td>
        <td width="5"></td>
        <td>{{seller_birth_date_tax_nr}}</td>
    </tr>
    <tr>
        <td>Telefón:</td>
        <td width="5"></td>
        <td>{{seller_phone}}</td>
    </tr>
    <tr>
        <td>Email:</td>
        <td width="5"></td>
        <td>{{seller_email}}</td>
    </tr>
</table>
<table class="mb-40">
    <tr>
        <td colspan="3"><b>Kupujúci:</b></td>
    </tr>
    <tr>
        <td>Meno a priezvisko (názov): </td>
        <td width="5"></td>
        <td>{{buyer_full_name}}</td>
    </tr>
    <tr>
        <td>Trvalé bydlisko (sídlo): </td>
        <td width="5"></td>
        <td>{{buyer_full_address}}</td>
    </tr>
    <tr>
        <td>Dátum narodenia (IČO/r.č.): </td>
        <td width="5"></td>
        <td>{{buyer_birth_date_tax_nr}}</td>
    </tr>
    <tr>
        <td>Telefón:</td>
        <td width="5"></td>
        <td>{{buyer_phone}}</td>
    </tr>
    <tr>
        <td>Email:</td>
        <td width="5"></td>
        <td>{{buyer_email}}</td>
    </tr>
</table>
<p class="f-10 mb-20"> <b>Určenie preberanej nehnuteľnosti:</b> {{property}} </p>
<p class="mb-20">
    Odovzdávajúci a preberajúci svojim podpisom potvrdzujú, že vyššie uvedená nehnuteľnosť bola riadne odovzdaná v stave zodpovedajúcom kúpnej 
    zmluve a zároveň bolo zistené a odovzdané nasledovné: 
</p>
<h4>Stav energií a služieb:</h4>
<table class="mb-40 w-90 bordered f-11">
    <thead>
        <tr>
            <th>Služby</th>
            <th>Stav merača</th>
            <th>Číslo merača (označenie merača)</th>
            <th>Poznámky</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Elektrina</td>
            <td>{{electricity_meter_status}}</td>
            <td>{{electricity_meter_number}}</td>
            <td>{{electricity_meter_note}}</td>
        </tr>
        <tr>
            <td>Merače studenej vody</td>
            <td>{{water_cold_meter_status}}</td>
            <td>{{water_cold_meter_number}}</td>
            <td>{{water_cold_meter_note}}</td>
        </tr>
        <tr>
            <td>Merače teplej úžitkovej vody</td>
            <td>{{water_hot_meter_status}}</td>
            <td>{{water_hot_meter_number}}</td>
            <td>{{water_hot_meter_note}}</td>
        </tr>
        <tr>
            <td>Plyn</td>
            <td>{{gas_meter_status}}</td>
            <td>{{gas_meter_number}}</td>
            <td>{{gas_meter_note}}</td>
        </tr>
        <tr>
            <td>Kúrenie</td>
            <td>{{heating_meter_status}}</td>
            <td>{{heating_meter_number}}</td>
            <td>{{heating_meter_note}}</td>
        </tr>
    </tbody>
</table>

<h4>Odovzdanie kľúčov a iných otváracích zariadení:</h4>
<table class="mb-40 w-50 bordered f-11">
    <thead style="background-color: black;">
        <tr>
            <th>Kľúče</th>
            <th>Počet kusov</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Brány</td>
            <td>{{gate_count}} ks</td>
        </tr>
        <tr>
            <td>Diaľkový ovládač</td>
            <td>{{remote_control_count}} ks</td>
        </tr>
        <tr>
            <td>Vchodové dvere do bytového domu</td>
            <td>{{entrance_door_count}} ks</td>
        </tr>
        <tr>
            <td>Čipová karta k vstupu do bytového domu</td>
            <td>{{chip_card_count}} ks</td>
        </tr>
        <tr>
            <td>Čipy k vstupu do bytového domu</td>
            <td>{{chip_count}} ks</td>
        </tr>
        <tr>
            <td>Vstupné dvere do bytu</td>
            <td>{{flat_entrance_door_count}} ks</td>
        </tr>
        <tr>
            <td>Bezpečnostná vložka do bytu</td>
            <td>{{security_insert_count}} ks</td>
        </tr>
        <tr>
            <td>Pivnica</td>
            <td>{{cellar_count}} ks</td>
        </tr>
        <tr>
            <td>Pivničné priestory</td>
            <td>{{cellar_space_count}} ks</td>
        </tr>
        <tr>
            <td>Spoločné priestory</td>
            <td>{{common_spaces_count}} ks</td>
        </tr>
        <tr>
            <td>Poštová schránka</td>
            <td>{{mailbox_count}} ks</td>
        </tr>
        <tr>
            <td>Vonkajšia garáž</td>
            <td>{{outside_garage_count}} ks</td>
        </tr>
        <tr>
            <td>Vnútorná garáž</td>
            <td>{{inside_garage_count}}  ks</td>
        </tr>
        <tr>
            <td>Práčovňa</td>
            <td>{{laundry_count}} ks</td>
        </tr>
        <tr>
            <td>Iné</td>
            <td>{{other_count}} ks</td>
        </tr>
    </tbody>
</table>

<table class="w-full mb-40">
    <tr>
        <td><b>Poznámky k stavu nehnuteľnosti:</b></td>
    </tr>
    <tr>
        <td> {{note}} </td>
    </tr>
</table>
<table class="w-full mb-40">
    <tr>
        <td>Miesto odovzdania:</td>
        <td width="20"></td>
        <td>Miesto preberania:</td>
    </tr>
    <tr>
        <td style="padding-top: 10px">{{place}}</td>
        <td width="20"></td>
        <td style="padding-top: 10px">{{place}}</td>
    </tr>
</table>
<table class="w-full mb-40">
    <tr>
        <td>Dátum odovzdania:</td>
        <td width="20"></td>
        <td>Dátum preberania:</td>
    </tr>
    <tr>
        <td style="padding-top: 10px">{{date}}</td>
        <td width="20"></td>
        <td style="padding-top: 10px">{{date}}</td>
    </tr>
</table>
<table class="w-full mb-40">
    <tr>
        <td>Odovzdávajúci:</td>
        <td width="20"></td>
        <td>Preberajúci:</td>
    </tr>
    <tr>
        <td style="padding-top: 50px">________________________</td>
        <td width="20"></td>
        <td style="padding-top: 50px">________________________</td>
    </tr>
    <tr>
        <td>{{seller_full_name}}</td>
        <td width="20"></td>
        <td>{{buyer_full_name}}</td>
    </tr>
</table>
</body>
</html>