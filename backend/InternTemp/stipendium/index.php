<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/*
    {\~~~~/}
    ( * _* )
    / U  U \
    ^^^^^^^^
     .U  U.


#164065 – tmavomodrá(BG)
#ed4322 – oranžovo-červená (BUTTONS)
#0c95db – azúrová modrá(ICONS)
*/
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

// Check file exists
if (!file_exists('template.docx')) {
    die("File template.docx not found");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ico   = $_POST['ico'] ?? '';
    $dic   = $_POST['dic'] ?? '';
    $banka = $_POST['banka'] ?? '';
    $ucet  = $_POST['ucet'] ?? '';
    $iban  = $_POST['iban'] ?? '';
    $dob   = $_POST['dob'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $employer_address = $_POST['employer_address'] ?? '';
    $employer_iban = $_POST['employer_iban'] ?? '';
    $employer_name = $_POST['employer_name'] ?? '';
    $employer_ico = $_POST['employer_ico'] ?? '';
    $employer_email = $_POST['employer_email'] ?? '';
    $employer_dic = $_POST['employer_dic'] ?? '';
    $employer_bank = $_POST['employer_bank'] ?? '';
    $student_name = $_POST['student_name'] ?? '';   
    
    $study_code = $_POST['study_code'] ?? '';
    $study_name = $_POST['study_name'] ?? '';
    $study_form = $_POST['study_form'] ?? '';
    $study_length = $_POST['study_length'] ?? '';
    $study_degree = $_POST['study_degree'] ?? '';
    $study_code_educational = $_POST['study_code_educational'] ?? '';
    $study_name_educational = $_POST['study_name_educational'] ?? '';
    $study_form_educational = $_POST['study_form_educational'] ?? '';
    $study_length_educational = $_POST['study_length_educational'] ?? '';
    $study_degree_educational = $_POST['study_degree_educational'] ?? '';
    $guardian_name = $_POST['guardian_name'] ?? '';
    $guardian_email = $_POST['guardian_email'] ?? '';
    $guardian_phone = $_POST['guardian_phone'] ?? '';
    $employer_name = $_POST['employer_name'] ?? '';
    $employer_address = $_POST['employer_address'] ?? '';
    $PPV_address = $_POST['PPV_address'] ?? '';
    $school_name = $_POST['school_name'] ?? '';
    $school_address = $_POST['school_address'] ?? '';
    $contract_end = $_POST['contract_end'] ?? '';
    $Reg_cislo_SDV = $_POST['Reg_cislo_SDV'] ?? '';
    $registracia = $_POST['registracia'] ?? '';

    date_default_timezone_set('Europe/Bratislava');
    $submitted_at = date("d.m.Y H:i");


    // Load template
    $template = new TemplateProcessor('template.docx');

    // Replace placeholders
    $template->setValue('ico', $ico);
    $template->setValue('dic', $dic);
    $template->setValue('banka', $banka);
    $template->setValue('ucet', $ucet);
    $template->setValue('iban', $iban);
    $template->setValue('student_name', $student_name);
    $template->setValue('email', $email);
    $template->setValue('phone', $phone);
    $template->setValue('study_code', $study_code);
    $template->setValue('study_name', $study_name);
    $template->setValue('study_form', $study_form);
    $template->setValue('study_length', $study_length);
    $template->setValue('study_degree', $study_degree);
    $template->setValue('study_code_educational', $study_code_educational);
    $template->setValue('study_name_educational', $study_name_educational);
    $template->setValue('study_form_educational', $study_form_educational);
    $template->setValue('study_length_educational', $study_length_educational);
    $template->setValue('study_degree_educational', $study_degree_educational);
    $template->setValue('Reg_cislo_SDV', $Reg_cislo_SDV);
    $template->setValue('registracia', $registracia);
    $template->setValue('dob', $dob);
    $template->setValue('guardian_name', $guardian_name);
    $template->setValue('guardian_email', $guardian_email);
    $template->setValue('guardian_phone', $guardian_phone);
    $template->setValue('employer_name', $employer_name);
    $template->setValue('employer_address', $employer_address);
    $template->setValue('employer_ico', $employer_ico);
    $template->setValue('employer_iban', $employer_iban);
    $template->setValue('employer_email', $employer_email);
    $template->setValue('employer_dic', $employer_dic);
    $template->setValue('employer_bank', $employer_bank);
    $template->setValue('PPV_address', $PPV_address);
    $template->setValue('school_name', $school_name);
    $template->setValue('school_address', $school_address);
    $template->setValue('contract_end', $contract_end);

    
    $template->setValue('submitted_at', $submitted_at);
    
    

    // Save to temp file
    $outputFile = 'done.docx';
    $template->saveAs($outputFile);

    // Force download
    if (ob_get_level()) {
        ob_end_clean();
    }
    header("Content-Description: File Transfer");
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="Štipendijný_formulár.docx"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($outputFile));
    readfile($outputFile);
    unlink($outputFile); // clean up only if download succeeded
    exit;
}





?>
<!DOCTYPE html>
<html lang="sk">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Štipendijný formulár</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            header {
                background-color: #164065;
                color: white;
                padding: 1rem;
                text-align: center;
            }
            form {
                max-width: 900px;
                margin: 2rem auto;
                background-color: white;
                padding: 2rem;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            h2 {
                color: #164065;
                border-bottom: 2px solid #0c95db;
                padding-bottom: 0.5rem;
                margin-bottom: 1rem;
            }
            label {
                display: block;
                margin-top: 1rem;
                font-weight: bold;
            }
            input, select, textarea {
                width: 100%;
                padding: 0.5rem;
                margin-top: 0.3rem;
                border-radius: 5px;
                border: 1px solid #ccc;
            }
            button {
                background-color: #ed4322;
                color: white;
                border: none;
                padding: 1rem 2rem;
                border-radius: 5px;
                margin-top: 1.5rem;
                cursor: pointer;
                font-size: 1rem;
            }
            button:hover {
                background-color: #c32f1c;
            }
            .section {
                margin-bottom: 2rem;
            }

            /* date picker */
            input[type="date"] {
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: textfield;
                border: 1px solid #ccc;
                border-radius: 8px;
                padding: 0.6rem 1rem;
                font-size: 1rem;
                width: 50%;
                background-color: #f4f4f4;
                color: #333;
            }

            input[type="date"]:focus {
                outline: none;
                border-color: #164065;
                box-shadow: 0 0 10px rgba(22,64,101,0.3);
                background-color: #fff;
            }

            /* checkbox */
            .checkbox-container {
                display: flex;
                align-items: center;
                cursor: pointer;
                font-weight: normal;
                position: relative;
                padding-left: 35px;
                margin-top: 10px;
                user-select: none;
                font-size: 1rem;
            }

            /* hide checkbox */
            .checkbox-container input {
                position: absolute;
                opacity: 0;
                cursor: pointer;
                height: 0;
                width: 0;
            }

            /* own checkmark */
            .checkmark {
                position: absolute;
                left: 0;
                top: 50%;
                transform: translateY(-50%);
                height: 20px;
                width: 20px;
                background-color: #eee;
                border-radius: 5px;
                border: 1px solid #ccc;
                transition: all 0.2s ease;
            }

            /* Checked state */
            .checkbox-container input:checked ~ .checkmark {
                background-color: #164065;
                border-color: #164065;
            }

            /* Add tick */
            .checkmark:after {
                content: "";
                position: absolute;
                display: none;
            }

            .checkbox-container input:checked ~ .checkmark:after {
                display: block;
            }

            .checkbox-container .checkmark:after {
                left: 6px;
                top: 2px;
                width: 5px;
                height: 10px;
                border: solid white;
                border-width: 0 2px 2px 0;
                transform: rotate(45deg);
            }
        </style>

    </head>


    <body>
    <header>
        <h1>Štipendijný formulár</h1>
    </header>

    <form action="index.php" method="POST">

        <div class="section">
            <h2>Finančné a identifikačné údaje</h2>
            <label>IČO: <input type="text" name="ico"></label><br>
            <label>DIČ: <input type="text" name="dic"></label><br>
            <label>Banka: <input type="text" name="banka"></label><br>
            <label>Číslo účtu: <input type="text" name="ucet"></label><br>
        </div>

        <div class="section">
            <h2>Údaje študenta</h2>
            <label for="student_name">Meno a priezvisko:</label>
            <input type="text" id="student_name" name="student_name" required>

            <label for="dob">Dátum narodenia:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Telefón:</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="address">Adresa:</label>
            <textarea id="address" name="address" rows="2" required></textarea>
        </div>


        <div class="section">
            <h2>Študijný odbor</h2>
            <label for="study_code">Kód odboru štúdia:</label>
            <input type="text" id="study_code" name="study_code">
            
            <label for="study_name">Názov odboru štúdia:</label>
            <input type="text" id="study_name" name="study_name">
            
            <label for="study_form">Forma štúdia:</label>
            <input type="text" id="study_form" name="study_form">
            
            <label for="study_length">Dĺžka štúdia:</label>
            <input type="text" id="study_length" name="study_length">
            
            <label for="study_degree">Stupeň dosiahnutého vzdelávania:</label>
            <input type="text" id="study_degree" name="study_degree">
        </div>

        <div class="section">
            <h2>Učebný odbor</h2>
            <label for="study_code_educational">Kód odboru štúdia:</label>
            <input type="text" id="study_code_educational" name="study_code_educational">
            
            <label for="study_name_educational">Názov odboru štúdia:</label>
            <input type="text" id="study_name_educational" name="study_name_educational">
            
            <label for="study_form_educational">Forma štúdia:</label>
            <input type="text" id="study_form_educational" name="study_form_educational">
            
            <label for="study_length_educational">Dĺžka štúdia:</label>
            <input type="text" id="study_length_educational" name="study_length_educational">
            
            <label for="study_degree_educational">Stupeň dosiahnutého vzdelávania:</label>
            <input type="text" id="study_degree_educational" name="study_degree_educational">
        </div>


        <div class="section">
            <h2>Zákonný zástupca</h2>
            <label for="guardian_name">Meno a priezvisko:</label>
            <input type="text" id="guardian_name" name="guardian_name" required>

            <label for="guardian_email">Email:</label>
            <input type="email" id="guardian_email" name="guardian_email" required>

            <label for="guardian_phone">Telefón:</label>
            <input type="tel" id="guardian_phone" name="guardian_phone" required>
        </div>

        <div class="section">
            <h2>Zamestnávateľ</h2>
            <label for="employer_name">Názov:</label>
            <input type="text" id="employer_name" name="employer_name" required>

            <label for="employer_address">Adresa:</label>
            <textarea id="employer_address" name="employer_address" rows="2" required></textarea>

            <label for="employer_iban">IBAN:</label>
            <input type="text" id="employer_iban" name="employer_iban" required>

            <label for="school_name">Adresa PPV:</label>
            <input type="text" id="PPV_address" name="PPV_address" required>

            <label for="employer_ico">IČO:</label>
            <input type="text" id="employer_ico" name="employer_ico" required>

            <label for="employer_dic">DIČ:</label>
            <input type="text" id="employer_dic" name="employer_dic" required>

            <label for="bank">Banka:</label>
            <input type="text" id="employer_bank" name="employer_bank" required>

            <label for="ucet">Číslo účtu:</label>
            <input type="text" id="employer_ucet" name="employer_ucet" required>

            <label for="ucet">Registrácia:</label>
            <input type="text" id="registracia" name="registracia" required>

            <label for="ucet">Registračné číslo osvedčenia pre SDV:</label>
            <input type="text" id="Reg_cislo_SDV" name="Reg_cislo_SDV" required>
        </div>


        <div class="section">
            <h2>Škola</h2>
            <label for="school_name">Názov školy:</label>
            <input type="text" id="school_name" name="school_name" required>

            <label for="school_address">Adresa školy:</label>
            <textarea id="school_address" name="school_address" rows="2" required></textarea>
        </div>

        <div class="section">
            <h2>Zmluva</h2>
            <label for="contract_end">Zmluva sa uzatvára na dobu určitú do:</label>
            <input type="date" id="contract_end" name="contract_end" required>
        </div>

        <div class="section">
            <h2>Vyhlásenia</h2>
            <label>
                <input type="checkbox" name="agreement" required>
                Súhlasím s podmienkami štipendijného programu.
            </label>
        </div>

        <button type="submit">Odoslať</button>
    </form>
</body>


</html>