<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$fpdfPath = __DIR__ . '/../vendor/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    die('FPDF no encontrado. Descárgalo en http://www.fpdf.org/ y colócalo en vendor/fpdf/fpdf.php');
}
require_once $fpdfPath;

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, CONCAT(e.nombre,' ',e.apellido) AS especialista, esp.nombre AS especialidad, sc.nombre AS estado FROM citas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id JOIN especialidades esp ON e.especialidad_id=esp.id LEFT JOIN status_cita sc ON c.status_id=sc.id WHERE c.fecha BETWEEN ? AND ? ORDER BY c.fecha");
$stmt->execute([$desde, $hasta]);
$citas = $stmt->fetchAll();

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(13,111,210);
        $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Reporte de Citas',0,1,'C',true);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',9);
        $this->Ln(3);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->SetTextColor(128);
        $this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,0,'C');
    }
}

$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

// Período
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,7,"Período: " . date('d/m/Y', strtotime($desde)) . " al " . date('d/m/Y', strtotime($hasta)) . "  |  Total: " . count($citas) . " citas",0,1);
$pdf->Ln(2);

// Cabecera tabla
$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(230,240,255);
$cols = [['#',10],['Fecha',25],['Paciente',55],['Especialista',50],['Especialidad',40],['Status',25]];
foreach ($cols as [$h,$w]) $pdf->Cell($w,7,$h,1,0,'C',true);
$pdf->Ln();

// Filas
$pdf->SetFont('Arial','',8);
$fill = false;
foreach ($citas as $c) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(10,6,$c['id'],1,0,'C',$fill);
    $pdf->Cell(25,6,date('d/m/Y',strtotime($c['fecha'])),1,0,'C',$fill);
    $pdf->Cell(55,6,mb_strimwidth($c['paciente'],0,30,'...'),1,0,'L',$fill);
    $pdf->Cell(50,6,mb_strimwidth($c['especialista'],0,28,'...'),1,0,'L',$fill);
    $pdf->Cell(40,6,mb_strimwidth($c['especialidad'],0,22,'...'),1,0,'L',$fill);
    $pdf->Cell(25,6,$c['estado']??'-',1,1,'C',$fill);
    $fill = !$fill;
}

$pdf->Output('I','reporte_citas_'.date('Ymd').'.pdf');
