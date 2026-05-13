<?php

require_once '../config/database.php';
require_once '../libraries/fpdf/fpdf.php';

$database = new Database();
$db = $database->getConnection();

$totalUsuarios = $db->query("
    SELECT COUNT(*) AS total
    FROM usuarios
")->fetch(PDO::FETCH_ASSOC)['total'];

$totalPacientes = $db->query("
    SELECT COUNT(*) AS total
    FROM paciente
")->fetch(PDO::FETCH_ASSOC)['total'];

$totalCitas = $db->query("
    SELECT COUNT(*) AS total
    FROM cita
")->fetch(PDO::FETCH_ASSOC)['total'];

$pdf = new FPDF();

$pdf->AddPage();

$pdf->SetFont('Arial','B',18);

$pdf->Cell(190,10,'Reporte Dashboard',0,1,'C');

$pdf->Ln(10);

$pdf->SetFont('Arial','',12);

$pdf->Cell(95,10,'Total Usuarios',1);
$pdf->Cell(95,10,$totalUsuarios,1);
$pdf->Ln();

$pdf->Cell(95,10,'Total Pacientes',1);
$pdf->Cell(95,10,$totalPacientes,1);
$pdf->Ln();

$pdf->Cell(95,10,'Total Citas',1);
$pdf->Cell(95,10,$totalCitas,1);
$pdf->Ln();

$pdf->Output();