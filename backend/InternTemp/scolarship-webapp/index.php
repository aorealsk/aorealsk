<?php
// Only run PDF generation if the form was submitted
use setasign\Fpdi\Fpdi;

function formatIban($iban) {
    // Keep only digits
    $iban = preg_replace('/\D/', '', $iban);

    // First 2 digits, then groups of 4
    $firstTwo = substr($iban, 0, 2);
    $rest = substr($iban, 2);

    // Split rest into groups of 4
    $groups = str_split($rest, 4);

    // For each group, add space between each character
    $groupsSpaced = [];
    foreach ($groups as $group) {
        $chars = str_split($group);
        $groupsSpaced[] = implode(' ', $chars);
    }

    // Join groups with '   ' spacing
    $ibanFormatted = $firstTwo . '    ' . implode('   ', $groupsSpaced);

    return $ibanFormatted;
}


function printField($pdf, $x, $y, $width, $text, $lineSpacing = 10, $offset = 5) {
    // Split text into lines that fit inside given width
    $words = explode(' ', trim($text));
    $line = '';
    $lines = [];

    foreach ($words as $word) {
        $testLine = $line ? $line . ' ' . $word : $word;
        $testWidth = $pdf->GetStringWidth($testLine);

        if ($testWidth > $width) {
            $lines[] = $line;
            $line = $word;
        } else {
            $line = $testLine;
        }
    }
    if ($line !== '') {
        $lines[] = $line;
    }

    // Print lines
    foreach ($lines as $i => $l) {
        // For 2nd line and further: apply offset
        $currentX = ($i === 0) ? $x : $x - $offset;

        $pdf->SetXY($currentX, $y);
        $pdf->Cell($width, $lineSpacing, $l, 0, 0);

        $y += $lineSpacing;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('fpdf186/fpdf.php');
    require_once('FPDI/src/autoload.php');

    $pdf = new FPDI();

    // Load your template PDF and get page count
    $pageCount = $pdf->setSourceFile('templates/ziadost_aoreal.pdf');
    $pdf->SetAutoPageBreak(false);
    // Loop through all pages
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $pdf->AddPage();
        $tplIdx = $pdf->importPage($pageNo);
        $pdf->useTemplate($tplIdx, 0, 0, 210);

        $pdf->SetFont('Arial','B',10);
        

        // Example: Write form fields only on the first page
        if ($pageNo === 1) {
            $pdf->SetXY(65, 79);
            $pdf->Cell(0, 10, $_POST['ziak_meno']);

            $pdf->SetXY(65, 86);
            $pdf->Cell(0, 10, $_POST['ziak_datum']);

            $pdf->SetXY(65, 93);
            $pdf->Cell(0, 10, $_POST['ziak_rok']);

            $pdf->SetXY(65, 110);
            $pdf->Cell(0, 10, $_POST['ziak_ulica']);

            $pdf->SetXY(65, 117);
            $pdf->Cell(0, 10, $_POST['ziak_cislo']);

            $pdf->SetXY(121, 117);
            $pdf->Cell(0, 10, $_POST['ziak_psc']);

            $pdf->SetXY(65, 124);
            $pdf->Cell(0, 10, $_POST['ziak_obec']);

            $pdf->SetXY(65, 141);
            $pdf->Cell(0, 10, $_POST['ziak_k_ulica']);

            $pdf->SetXY(65, 148);
            $pdf->Cell(0, 10, $_POST['ziak_k_cislo']);

            $pdf->SetXY(121, 148);
            $pdf->Cell(0, 10, $_POST['ziak_k_psc']);

            $pdf->SetXY(65, 155);
            $pdf->Cell(0, 10, $_POST['ziak_obec']);

            $pdf->SetXY(65, 173);
            $pdf->Cell(0, 10, $_POST['ziak_tel']);

            $pdf->SetXY(65, 180);
            $pdf->Cell(0, 10, $_POST['ziak_email']);

            
            $pdf->SetFont('Arial','B',20.2);
            $ibanFormatted = formatIban($_POST['ziak_iban']);
            $pdf->SetXY(37.2, 197.5);
            $pdf->Cell(100, 10, $ibanFormatted);


        $pdf->SetFont('Arial','B',10);

            $pdf->SetXY(65, 235);
            $pdf->Cell(0, 10, $_POST['zz1_meno']);

            $pdf->SetXY(65, 242);
            $pdf->Cell(0, 10, $_POST['zz1_datum']);

            $pdf->SetXY(65, 260);
            $pdf->Cell(0, 10, $_POST['zz1_ulica']);

            $pdf->SetXY(65, 267);
            $pdf->Cell(0, 10, $_POST['zz1_cislo']);

            $pdf->SetXY(121, 267);
            $pdf->Cell(0, 10, $_POST['zz1_psc']);

            $pdf->SetXY(65, 274);
            $pdf->Cell(0, 10, $_POST['zz1_obec']);



            
        }
        if ($pageNo === 2) {

            $pdf->SetXY(65, 32);
            $pdf->Cell(0, 10, $_POST['zz1_k_ulica']);

            $pdf->SetXY(65, 39);
            $pdf->Cell(0, 10, $_POST['zz1_k_cislo']);

            $pdf->SetXY(121, 39);
            $pdf->Cell(0, 10, $_POST['zz1_k_psc']);

            $pdf->SetXY(65, 46);
            $pdf->Cell(0, 10, $_POST['zz1_k_obec']);

            $pdf->SetXY(65, 63);
            $pdf->Cell(0, 10, $_POST['zz1_tel']);
            
            $pdf->SetXY(65, 70);
            $pdf->Cell(0, 10, $_POST['zz1_email']);



            $pdf->SetXY(65, 108);
            $pdf->Cell(0, 10, $_POST['zz2_meno']);

            $pdf->SetXY(65, 115);
            $pdf->Cell(0, 10, $_POST['zz2_datum']);

            $pdf->SetXY(65, 132);
            $pdf->Cell(0, 10, $_POST['zz2_ulica']);

            $pdf->SetXY(65, 139);
            $pdf->Cell(0, 10, $_POST['zz2_cislo']);

            $pdf->SetXY(121, 139);
            $pdf->Cell(0, 10, $_POST['zz2_psc']);

            $pdf->SetXY(65, 146);
            $pdf->Cell(0, 10, $_POST['zz2_obec']);

            $pdf->SetXY(65, 164);
            $pdf->Cell(0, 10, $_POST['zz2_k_ulica']);

            $pdf->SetXY(65, 171);
            $pdf->Cell(0, 10, $_POST['zz2_k_cislo']);

            $pdf->SetXY(121, 171);
            $pdf->Cell(0, 10, $_POST['zz2_k_psc']);

            $pdf->SetXY(65, 178);
            $pdf->Cell(0, 10, $_POST['zz2_k_obec']);

            $pdf->SetXY(65, 195);
            $pdf->Cell(0, 10, $_POST['zz2_tel']);

            $pdf->SetXY(65, 202);
            $pdf->Cell(0, 10, $_POST['zz2_email']);

            // Handle checkboxes for "Čestné vyhlásenie"
            if (isset($_POST['dovod1'])) {
                $pdf->SetXY(30.3, 241.5); // Adjust coordinates as needed
                $pdf->Cell(5, 5, 'X');
            }
            if (isset($_POST['dovod2'])) {
                $pdf->SetXY(30.3, 246.5); // Adjust coordinates as needed
                $pdf->Cell(5, 5, 'X');
            }
            if (isset($_POST['dovod3'])) {
                $pdf->SetXY(30.3, 251); // Adjust coordinates as needed
                $pdf->Cell(5, 5, 'X');
            }
            if (isset($_POST['dovod4'])) {
                $pdf->SetXY(30.3, 255.5); // Adjust coordinates as needed
                $pdf->Cell(5, 5, 'X');
            }
            if (isset($_POST['dovod5'])) {
                $pdf->SetXY(30.3, 260.5); // Adjust coordinates as needed
                $pdf->Cell(5, 5, 'X');
            }
            printField($pdf, 53, 260, 115, $_POST['dovod5_text'], 4, 27); 
        }
        if ($pageNo === 3) {
            printField($pdf, 25, 46, 152, $_POST['zam_nazov'], 4, 0); 

            $pdf->SetXY(25, 75);
            $pdf->Cell(0, 10, $_POST['zam_cislo']);

            $pdf->SetXY(25, 89);
            $pdf->Cell(0, 10, $_POST['zam_odbor']);

            $pdf->SetXY(73, 107);
            $pdf->Cell(0, 10, $_POST['zam_ulica']);

            $pdf->SetXY(73, 114);
            $pdf->Cell(0, 10, $_POST['zam_orientacne_cislo']);

            $pdf->SetXY(121, 114);
            $pdf->Cell(0, 10, $_POST['zam_psc']);

            $pdf->SetXY(73, 121);
            $pdf->Cell(0, 10, $_POST['zam_obec']);

            $pdf->SetXY(73, 139);
            $pdf->Cell(0, 10, $_POST['zam_z_meno']);

            $pdf->SetXY(73, 146);
            $pdf->Cell(0, 10, $_POST['zam_z_tel']);

            $pdf->SetXY(73, 153);
            $pdf->Cell(0, 10, $_POST['zam_z_email']);

            printField($pdf, 25, 193, 152, $_POST['skola_nazov'], 4, 0); 

            $pdf->SetXY(65, 221);
            $pdf->Cell(0, 10, $_POST['skola_ulica']);

            $pdf->SetXY(65, 228);
            $pdf->Cell(0, 10, $_POST['skola_cislo']);

            $pdf->SetXY(120, 228);
            $pdf->Cell(0, 10, $_POST['skola_psc']);

            $pdf->SetXY(65, 235);
            $pdf->Cell(0, 10, $_POST['skola_ulica']);

            $pdf->SetXY(65, 252);
            $pdf->Cell(0, 10, $_POST['riaditel']);

            
            printField($pdf, 25, 269, 152, $_POST['datum_podania'], 4, 0); 
        }
        if ($pageNo === 4) {
            if (isset($_POST['souhlas'])) {
                $pdf->SetXY(29.1, 38);
                $pdf->Cell(5, 5, 'X');
            }
            if (isset($_POST['oznamenie'])) {
                $pdf->SetXY(29.1, 54.6);
                $pdf->Cell(5, 5, 'X');
            }
            if (isset($_POST['pravdivost'])) {
                $pdf->SetXY(29.1, 94.4);
                $pdf->Cell(5, 5, 'X');
            }
        }
    }
    $pdf->Output('D', 'ziadost_vyplnena.pdf');
    exit;
} 
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Žiadosť o župné štipendium</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .form-header h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 300;
        }

        form {
            padding: 40px;
        }

        .form-section {
            margin-bottom: 40px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        h3 {
            color: #2c3e50;
            font-size: 1.3rem;
            margin-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        h4 {
            color: #34495e;
            font-size: 1.1rem;
            margin: 25px 0 15px 0;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="date"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .checkbox-group {
            margin: 15px 0;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .checkbox-group label {
            display: inline;
            margin-left: 5px;
            font-weight: normal;
        }

        .submit-section {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #ecf0f1;
        }

        input[type="submit"] {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        hr {
            display: none;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .section-toggle {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .section-toggle:hover {
            background: #e9ecef;
            border-color: #667eea;
        }

        .section-toggle.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }

        .section-toggle input[type="checkbox"] {
            margin-right: 15px;
            transform: scale(1.3);
            cursor: pointer;
        }

        .section-toggle label {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            cursor: pointer;
            flex: 1;
        }

        .section-toggle .toggle-icon {
            margin-left: 10px;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .section-toggle.active .toggle-icon {
            transform: rotate(180deg);
        }

        .section-content {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .section-content.hidden {
            display: none;
        }
        
        .required {
          color: red;
          font-weight: bold;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }

            form {
                padding: 20px;
            }

            .form-section {
                padding: 15px;
            }

            .form-header {
                padding: 20px;
            }

            .form-header h2 {
                font-size: 1.4rem;
            }

            .two-column {
                grid-template-columns: 1fr;
            }

            .section-toggle {
                padding: 12px 15px;
            }

            .section-toggle label {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
  <div class="form-header">
    <h2>Žiadosť o poskytnutie župného štipendia</h2>
    <p>Štipendijný program Trenčianskeho samosprávneho kraja</p>
  </div>
  
  <form action="" method="post">

    <!-- 1. ÚDAJE O ŽIAKOVI -->
    <div class="section-toggle active" role="button" tabindex="0" onclick="handleToggleClick('section1')" aria-expanded="true" aria-controls="section1">
      <input type="checkbox" id="toggle_section1" checked>
      <label for="toggle_section1">1. ÚDAJE O ŽIAKOVI</label>
      <span class="toggle-icon">▼</span>
    </div>
    <div class="form-section section-content" id="section1">
      <h3>1. ÚDAJE O ŽIAKOVI</h3>
      
      <div class="form-group">
        <label for="ziak_meno">Meno a priezvisko:<span class="required">*</span></label>
        <input type="text" id="ziak_meno" name="ziak_meno" required>
      </div>

      <div class="two-column">
        <div class="form-group">
          <label for="ziak_datum">Dátum narodenia:<span class="required">*</span></label>
          <input type="date" id="ziak_datum" name="ziak_datum" required>
        </div>
        <div class="form-group">
          <label for="ziak_rok">Školský rok:<span class="required">*</span></label>
          <input type="text" id="ziak_rok" name="ziak_rok" required>
        </div>
      </div>

      <h4>Adresa trvalého pobytu</h4>
      <div class="form-group">
        <label for="ziak_ulica">Ulica:</label>
        <input type="text" id="ziak_ulica" name="ziak_ulica">
      </div>
      <div class="two-column">
        <div class="form-group">
          <label for="ziak_cislo">Orientačné číslo:</label>
          <input type="text" id="ziak_cislo" name="ziak_cislo">
        </div>
        <div class="form-group">
          <label for="ziak_psc">PSČ:</label>
          <input type="text" id="ziak_psc" name="ziak_psc">
        </div>
      </div>
      <div class="form-group">
        <label for="ziak_obec">Obec:</label>
        <input type="text" id="ziak_obec" name="ziak_obec">
      </div>

      <h4>Korešpondenčná adresa</h4>
      <div class="form-group">
        <label for="ziak_k_ulica">Ulica:</label>
        <input type="text" id="ziak_k_ulica" name="ziak_k_ulica">
      </div>
      <div class="two-column">
        <div class="form-group">
          <label for="ziak_k_cislo">Orientačné číslo:</label>
          <input type="text" id="ziak_k_cislo" name="ziak_k_cislo">
        </div>
        <div class="form-group">
          <label for="ziak_k_psc">PSČ:</label>
          <input type="text" id="ziak_k_psc" name="ziak_k_psc">
        </div>
      </div>
      <div class="form-group">
        <label for="ziak_k_obec">Obec:</label>
        <input type="text" id="ziak_k_obec" name="ziak_k_obec">
      </div>

      <h4>Kontaktné informácie</h4>
      <div class="two-column">
        <div class="form-group">
          <label for="ziak_tel">Telefónne číslo:<span class="required">*</span></label>
          <input type="tel" id="ziak_tel" name="ziak_tel" required>
        </div>
        <div class="form-group">
          <label for="ziak_email">E-mailová adresa:<span class="required">*</span></label>
          <input type="email" id="ziak_email" name="ziak_email" required>
        </div>
      </div>
      <div class="form-group">
        <label for="ziak_iban">Číslo účtu (IBAN):</label>
        <input type="text" id="ziak_iban" name="ziak_iban">
      </div>
    </div>

    <!-- 2. PRVÝ ZÁKONNÝ ZÁSTUPCA -->
    <div class="section-toggle" role="button" tabindex="0" onclick="handleToggleClick('section2')" aria-expanded="false" aria-controls="section2">
      <input type="checkbox" id="toggle_section2">
      <label for="toggle_section2">2. ÚDAJE O ZÁKONNOM ZÁSTUPCOVI (ak je žiak neplnoletý)</label>
      <span class="toggle-icon">▶</span>
    </div>
    <div class="form-section section-content hidden" id="section2">
      <h3>2. ÚDAJE O ZÁKONNOM ZÁSTUPCOVI (ak je žiak neplnoletý)</h3>
  <label for="zz1_meno">Meno, priezvisko, titul:</label>
  <input type="text" id="zz1_meno" name="zz1_meno"><br><br>
  <label for="zz1_datum">Dátum narodenia:</label>
  <input type="date" id="zz1_datum" name="zz1_datum"><br><br>

  <h4>Adresa trvalého pobytu</h4>
  <label for="zz1_ulica">Ulica:</label>
  <input type="text" id="zz1_ulica" name="zz1_ulica"><br><br>
  <label for="zz1_cislo">Orientačné číslo:</label>
  <input type="text" id="zz1_cislo" name="zz1_cislo"><br><br>
  <label for="zz1_psc">PSČ:</label>
  <input type="text" id="zz1_psc" name="zz1_psc"><br><br>
  <label for="zz1_obec">Obec:</label>
  <input type="text" id="zz1_obec" name="zz1_obec"><br><br>

  <h4>Korešpondenčná adresa</h4>
  <label for="zz1_k_ulica">Ulica:</label>
  <input type="text" id="zz1_k_ulica" name="zz1_k_ulica"><br><br>
  <label for="zz1_k_cislo">Orientačné číslo:</label>
  <input type="text" id="zz1_k_cislo" name="zz1_k_cislo"><br><br>
  <label for="zz1_k_psc">PSČ:</label>
  <input type="text" id="zz1_k_psc" name="zz1_k_psc"><br><br>
  <label for="zz1_k_obec">Obec:</label>
  <input type="text" id="zz1_k_obec" name="zz1_k_obec"><br><br>

  <h4>Kontaktné informácie</h4>
  <label for="zz1_tel">Telefónne číslo:</label>
  <input type="tel" id="zz1_tel" name="zz1_tel"><br><br>
  <label for="zz1_email">E-mailová adresa:</label>
  <input type="email" id="zz1_email" name="zz1_email"><br><br>

    </div>

    <!-- 3. DRUHÝ ZÁKONNÝ ZÁSTUPCA -->
    <div class="section-toggle" role="button" tabindex="0" onclick="handleToggleClick('section3')" aria-expanded="false" aria-controls="section3">
      <input type="checkbox" id="toggle_section3">
      <label for="toggle_section3">3. ÚDAJE O DRUHOM ZÁKONNOM ZÁSTUPCOVI</label>
      <span class="toggle-icon">▶</span>
    </div>
    <div class="form-section section-content hidden" id="section3">
      <h3>3. ÚDAJE O DRUHOM ZÁKONNOM ZÁSTUPCOVI</h3>
  <label for="zz2_meno">Meno, priezvisko, titul:</label>
  <input type="text" id="zz2_meno" name="zz2_meno"><br><br>
  <label for="zz2_datum">Dátum narodenia:</label>
  <input type="date" id="zz2_datum" name="zz2_datum"><br><br>

  <h4>Adresa trvalého pobytu</h4>
  <label for="zz2_ulica">Ulica:</label>
  <input type="text" id="zz2_ulica" name="zz2_ulica"><br><br>
  <label for="zz2_cislo">Orientačné číslo:</label>
  <input type="text" id="zz2_cislo" name="zz2_cislo"><br><br>
  <label for="zz2_psc">PSČ:</label>
  <input type="text" id="zz2_psc" name="zz2_psc"><br><br>
  <label for="zz2_obec">Obec:</label>
  <input type="text" id="zz2_obec" name="zz2_obec"><br><br>

  <h4>Korešpondenčná adresa</h4>
  <label for="zz2_k_ulica">Ulica:</label>
  <input type="text" id="zz2_k_ulica" name="zz2_k_ulica"><br><br>
  <label for="zz2_k_cislo">Orientačné číslo:</label>
  <input type="text" id="zz2_k_cislo" name="zz2_k_cislo"><br><br>
  <label for="zz2_k_psc">PSČ:</label>
  <input type="text" id="zz2_k_psc" name="zz2_k_psc"><br><br>
  <label for="zz2_k_obec">Obec:</label>
  <input type="text" id="zz2_k_obec" name="zz2_k_obec"><br><br>

  <h4>Kontaktné informácie</h4>
  <label for="zz2_tel">Telefónne číslo:</label>
  <input type="tel" id="zz2_tel" name="zz2_tel"><br><br>
  <label for="zz2_email">E-mailová adresa:</label>
  <input type="email" id="zz2_email" name="zz2_email"><br><br>

    </div>

    <!-- 4. ČESTNÉ VYHLÁSENIE -->
    <div class="section-toggle" role="button" tabindex="0" onclick="handleToggleClick('section4')" aria-expanded="false" aria-controls="section4">
      <input type="checkbox" id="toggle_section4">
      <label for="toggle_section4">4. Čestné vyhlásenie</label>
    </div>
    <div class="form-section section-content hidden" id="section4">
      <h3>4. Čestné vyhlásenie <br></br>(Dolu podpísaný, ako jediný zákonný zástupca žiaka týmto vyhlasujem, že: )</h3>
      <div class="checkbox-group">
        <input type="checkbox" id="dovod1" name="dovod1">
        <label for="dovod1">mi nie je známe bydlisko druhého zákonného zástupcu</label>
      </div>
      <div class="checkbox-group">
        <input type="checkbox" id="dovod2" name="dovod2">
        <label for="dovod2">bolo mi dieťa zverené súdom do výhradnej opatery</label>
      </div>
      <div class="checkbox-group">
        <input type="checkbox" id="dovod3" name="dovod3">
        <label for="dovod3">druhý zákonný zástupca nepreberá poštové zásielky</label>
      </div>
      <div class="checkbox-group">
        <input type="checkbox" id="dovod4" name="dovod4">
        <label for="dovod4">druhý zákonný zástupca zomrel</label>
      </div>
      <div class="checkbox-group">
        <input type="checkbox" id="dovod5" name="dovod5">
        <label for="dovod5">iný dôvod:</label>
      </div>
      <div class="form-group">
        <label for="dovod5_text">Popis iného dôvodu:</label>
        <input type="text" id="dovod5_text" name="dovod5_text" placeholder="Uveďte iný dôvod">
      </div>

    </div>

    <!-- 5. ZAMESTNÁVATEĽ -->
    <div class="section-toggle" role="button" tabindex="0" onclick="handleToggleClick('section5')" aria-expanded="false" aria-controls="section5">
      <input type="checkbox" id="toggle_section5">
      <label for="toggle_section5">5. ÚDAJE O ZAMESTNÁVATEĽOVI</label>
      <span class="toggle-icon">▶</span>
    </div>
    <div class="form-section section-content hidden" id="section5">
      <h3>5. ÚDAJE O ZAMESTNÁVATEĽOVI</h3>
  <label for="zam_nazov">Názov zamestnávateľa:<span class="required">*</span></label>
  <input type="text" id="zam_nazov" name="zam_nazov" required><br><br>
  <label for="zam_cislo">Číslo Duálnej zmluvy:<span class="required">*</span></label>
  <input type="text" id="zam_cislo" name="zam_cislo" required><br><br>
  <label for="zam_odbor">Názov študijného/učebného odboru:<span class="required">*</span></label>
  <input type="text" id="zam_odbor" name="zam_odbor" required><br><br>

  <h4>Adresa prevádzky</h4>
  <label for="zam_ulica">Ulica:</label>
  <input type="text" id="zam_ulica" name="zam_ulica"><br><br>
  <label for="zam_orientacne_cislo">Orientačné číslo:</label>
  <input type="text" id="zam_orientacne_cislo" name="zam_orientacne_cislo"><br><br>
  <label for="zam_psc">PSČ:</label>
  <input type="text" id="zam_psc" name="zam_psc"><br><br>
  <label for="zam_obec">Obec:</label>
  <input type="text" id="zam_obec" name="zam_obec"><br><br>

  <h4>Kontakt na zástupcu zamestnávateľa</h4>
  <label for="zam_z_meno">Meno, priezvisko, titul:<span class="required">*</span></label>
  <input type="text" id="zam_z_meno" name="zam_z_meno" required><br><br>
  <label for="zam_z_tel">Telefónne číslo:<span class="required">*</span></label>
  <input type="tel" id="zam_z_tel" name="zam_z_tel" required><br><br>
  <label for="zam_z_email">E-mailová adresa:<span class="required">*</span></label>
  <input type="email" id="zam_z_email" name="zam_z_email" required><br><br>

    </div>

    <!-- 6. ŠKOLA -->
    <div class="section-toggle" role="button" tabindex="0" onclick="handleToggleClick('section6')" aria-expanded="false" aria-controls="section6">
      <input type="checkbox" id="toggle_section6">
      <label for="toggle_section6">6. INFORMÁCIA O ŠKOLE</label>
      <span class="toggle-icon">▶</span>
    </div>
    <div class="form-section section-content hidden" id="section6">
      <h3>6. INFORMÁCIA O ŠKOLE</h3>
  <label for="skola_nazov">Názov školy:<span class="required">*</span></label>
  <input type="text" id="skola_nazov" name="skola_nazov" required><br><br>

  <h4>Adresa školy</h4>
  <label for="skola_ulica">Ulica:<span class="required">*</span></label>
  <input type="text" id="skola_ulica" name="skola_ulica" required><br><br>
  <label for="skola_cislo">Orientačné číslo:<span class="required">*</span></label>
  <input type="text" id="skola_cislo" name="skola_cislo" required><br><br>
  <label for="skola_psc">PSČ:<span class="required">*</span></label>
  <input type="text" id="skola_psc" name="skola_psc" required><br><br>
  <label for="skola_obec">Obec:<span class="required">*</span></label>
  <input type="text" id="skola_obec" name="skola_obec" required><br><br>

  <label for="riaditel">Riaditeľ školy (meno, priezvisko, titul):<span class="required">*</span></label>
  <input type="text" id="riaditel" name="riaditel" required><br><br>
  <label for="datum_podania">Dátum podania žiadosti:<span class="required">*</span></label>
  <input type="date" id="datum_podania" name="datum_podania" required><br><br>

    </div>

    <!-- 7. VYHLÁSENIA ŽIADATEĽA -->
    <div class="section-toggle" role="button" tabindex="0" onclick="handleToggleClick('section7')" aria-expanded="false" aria-controls="section7">
      <input type="checkbox" id="toggle_section7">
      <label for="toggle_section7">7. Vyhlásenia žiadateľa</label>
      <span class="toggle-icon">▶</span>
    </div>
    <div class="form-section section-content hidden" id="section7">
      <h3>7. Vyhlásenia žiadateľa</h3>
      <div class="checkbox-group">
        <input type="checkbox" id="souhlas" name="souhlas" required>
        <label for="souhlas">Súhlasím so spracovaním osobných údajov<span class="required">*</span></label>
      </div>
      <div class="checkbox-group">
        <input type="checkbox" id="oznamenie" name="oznamenie" required>
        <label for="oznamenie">Som si vedomý povinnosti oznámiť zmeny podmienok štipendia<span class="required">*</span></label>
      </div>
      <div class="checkbox-group">
        <input type="checkbox" id="pravdivost" name="pravdivost" required>
        <label for="pravdivost">Čestne vyhlasujem, že údaje sú pravdivé<span class="required">*</span></label>
      </div>

    </div>

    <div class="submit-section">
      <input type="submit" value="Generate PDF">
    </div>
  </form>
</div>

<script>
function handleToggleClick(sectionId) {
    const checkbox = document.getElementById('toggle_' + sectionId);
    
    // Toggle checkbox state when clicking on the row
    checkbox.checked = !checkbox.checked;
    
    // Update section visibility
    updateSectionState(sectionId);
}

function updateSectionState(sectionId) {
    const checkbox = document.getElementById('toggle_' + sectionId);
    const section = document.getElementById(sectionId);
    const toggle = checkbox.parentElement;
    const icon = toggle.querySelector('.toggle-icon');
    
    if (checkbox.checked) {
        // Show section
        section.classList.remove('hidden');
        toggle.classList.add('active');
        toggle.setAttribute('aria-expanded', 'true');
        icon.textContent = '▼';
        
        // Re-enable form inputs and restore required attributes
        const inputs = section.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            input.disabled = false;
            if (input.dataset.originalRequired === 'true') {
                input.required = true;
            }
        });
    } else {
        // Hide section
        section.classList.add('hidden');
        toggle.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        icon.textContent = '▶';
        
        // Disable form inputs and remove required attributes to prevent validation issues
        const inputs = section.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            input.disabled = true;
            // Store original required state
            input.dataset.originalRequired = input.required;
            input.required = false;
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all section states
    const allSections = ['section1', 'section2', 'section3', 'section4', 'section5', 'section6', 'section7'];
    allSections.forEach(function(sectionId) {
        updateSectionState(sectionId);
    });
    
    // Add keyboard support for toggle buttons
    const toggleButtons = document.querySelectorAll('.section-toggle[role="button"]');
    toggleButtons.forEach(function(button) {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const sectionId = button.getAttribute('aria-controls');
                handleToggleClick(sectionId);
            }
        });
    });
    
    // Handle direct checkbox clicks
    const checkboxes = document.querySelectorAll('.section-toggle input[type="checkbox"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        checkbox.addEventListener('change', function(e) {
            e.stopPropagation();
            const sectionId = this.id.replace('toggle_', '');
            updateSectionState(sectionId);
        });
    });
    
    // Prevent label clicks from bubbling to row click
    const labels = document.querySelectorAll('.section-toggle label');
    labels.forEach(function(label) {
        label.addEventListener('click', function(e) {
            e.stopPropagation();
            // Let the native label behavior handle the toggle via the checkbox change event
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const telInput = document.getElementById('ziak_tel');
    const emailInput = document.getElementById('ziak_email');
    const ibanInput = document.getElementById('ziak_iban');

    // Predvyplnenie telefónu a IBANu
    telInput.value = '+421 ';
    ibanInput.value = 'SK';

    // Funkcia na validáciu e-mailu
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Funkcia na validáciu telefónu
    function isValidPhone(phone) {
        const cleaned = phone.replace(/\D/g, ''); // zmaže všetko okrem číslic
        // Slovenské číslo: 9 číslic + predvoľba +421
        return cleaned.length === 12; // +421 + 9 číslic
    }

    // Funkcia na validáciu IBANu
    function isValidIBAN(iban) {
        const cleaned = iban.replace(/\s+/g, ''); // odstráni medzery
        return /^SK\d{22}$/.test(cleaned); // SK + 22 číslic = 24 znakov
    }

    // Pri odoslaní formulára
    document.querySelector('form').addEventListener('submit', function(e) {
        const tel = telInput.value.trim();
        const email = emailInput.value.trim();
        const iban = ibanInput.value.trim().replace(/\s+/g, '');

        if (!isValidPhone(tel)) {
            alert('Telefónne číslo nie je v správnom tvare. Použite formát +421xxxxxxxxx');
            e.preventDefault();
            telInput.focus();
            return false;
        }

        if (!isValidEmail(email)) {
            alert('E-mailová adresa nie je platná.');
            e.preventDefault();
            emailInput.focus();
            return false;
        }

        if (iban && !isValidIBAN(iban)) {
            alert('IBAN musí byť platný slovenský IBAN (SK + 22 číslic).');
            e.preventDefault();
            ibanInput.focus();
            return false;
        }

        return true; // všetko OK
    });

    // Obmedzenie písmen pri IBAN (okrem predvoľby SK)
    ibanInput.addEventListener('input', function() {
        let val = ibanInput.value.toUpperCase();
        if (!val.startsWith('SK')) val = 'SK';
        val = 'SK' + val.slice(2).replace(/\D/g, '');
        ibanInput.value = val;
    });

    // Obmedzenie písmen pri telefóne (len + a číslice)
    telInput.addEventListener('input', function() {
        let val = telInput.value;
        if (!val.startsWith('+421')) val = '+421';
        val = '+421' + val.slice(4).replace(/\D/g, '');
        telInput.value = val;
    });
});
</script>

</body>
</html>
