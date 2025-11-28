<?php
session_start();

// skontroluj meno
if (!isset($_SESSION['student_name'])) {
    die("No student name found.");
}

$name = $_SESSION['student_name'];
$percent = $_GET['percent'] ?? 0;
$date = date("Y-m-d");

// =============== LOAD TCPDF ===================
require_once __DIR__ . '/../includes/tcpdf/tcpdf.php';

// =============== CREATE PDF ===================
$pdf = new TCPDF("L", "mm", "A4", true, "UTF-8");
$pdf->SetCreator("Architecture Quiz");
$pdf->SetAuthor("Architecture Quiz");
$pdf->SetTitle("Certifikát");

// Nastavenie bez header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// DARK BACKGROUND
$pdf->SetFillColor(17, 24, 39);
$pdf->Rect(0, 0, 400, 400, "F");

// TITLE
$pdf->SetTextColor(56, 189, 248);
$pdf->SetFont("dejavusans", "B", 32);
$pdf->Cell(0, 30, "CERTIFIKÁT O ABSOLVOVANÍ", 0, 1, "C");

// WHITE PANEL
$pdf->SetFillColor(255,255,255);
$pdf->RoundedRect(20, 50, 250, 120, 5, "1111", "F");

// DATA
$pdf->SetXY(30, 70);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont("dejavusans", "", 18);
$pdf->MultiCell(230, 10,
    "Držiteľ certifikátu:

$name

úspešne absolvoval odborný test z predmetu Építészet
s výsledkom $percent%.

Dátum vystavenia: $date
", 0, "L");

// QR CODE
$qrString = "Meno: $name | Výsledok: $percent% | Dátum: $date";
$pdf->write2DBarcode($qrString, 'QRCODE,H', 220, 55, 40, 40);

$pdf->SetXY(230, 95);
$pdf->SetFont("dejavusans", "", 10);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(40, 10, "Overovací QR kód", 0, 0, "C");

// OUTPUT
$pdf->Output("certifikat_$name.pdf", "I");
