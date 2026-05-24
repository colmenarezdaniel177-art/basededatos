<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$fpdfPath = __DIR__ . '/../vendor/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) die('FPDF no encontrado en vendor/fpdf/fpdf.php');
require_once $fpdfPath;

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

$stmt = $pdo->prepare(
    "SELECT CONCAT(p.nombre,' ',p.apellido) AS paciente,
            CONCAT(e.nombre,' ',e.apellido) AS especialista,
            c.fecha
     FROM consultas c
     JOIN pacientes p ON c.paciente_id=p.id
     JOIN especialistas e ON c.especialista_id=e.id
     WHERE DATE(c.fecha) BETWEEN ? AND ?
     ORDER BY c.fecha ASC"
);
$stmt->execute([$desde, $hasta]);
$rows = $stmt->fetchAll();

class PDF extends FPDF {
    public $desde, $hasta;
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(40,167,69); $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Pacientes Atendidos',0,1,'C',true);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',9);
        $this->Cell(0,6,'Periodo: '.$this->desde.' al '.$this->hasta,0,1,'C');
        $this->Ln(2);
    }
    function Footer() {
        $this->SetY(-15); $this->SetFont('Arial','I',8); $this->SetTextColor(128);
        $this->Cell(0,10,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->desde = date('d/m/Y', strtotime($desde));
$pdf->hasta = date('d/m/Y', strtotime($hasta));
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(220,255,220);
foreach ([['Paciente',80],['Doctor Especialista',75],['Fecha',35]] as [$h,$w])
    $pdf->Cell($w,8,$h,1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$fill = false;
foreach ($rows as $r) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(80,7,mb_convert_encoding($r['paciente'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(75,7,mb_convert_encoding($r['especialista'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(35,7,date('d/m/Y',strtotime($r['fecha'])),1,1,'C',$fill);
    $fill = !$fill;
}
if (!$rows) {
    $pdf->SetFont('Arial','I',9);
    $pdf->Cell(0,10,'No hay consultas en el periodo seleccionado.',0,1,'C');
}
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(200,240,200);
$pdf->Cell(155,7,'Total atendidos: '.count($rows),1,1,'R',true);

$pdf->Output('I','pacientes_atendidos_'.date('Ymd').'.pdf');
