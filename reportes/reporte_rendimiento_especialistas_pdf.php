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
    "SELECT CONCAT(e.nombre,' ',e.apellido) AS especialista, esp.nombre AS especialidad, COUNT(c.id) AS total_citas
     FROM especialistas e
     LEFT JOIN citas c ON e.id = c.especialista_id AND c.fecha BETWEEN ? AND ?
     LEFT JOIN especialidades esp ON e.especialidad_id = esp.id
     GROUP BY e.id, especialista, especialidad
     ORDER BY total_citas DESC"
);
$stmt->execute([$desde, $hasta]);
$rows = $stmt->fetchAll();

class PDF extends FPDF {
    public $desde, $hasta;
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(102,16,242); $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Rendimiento de Especialistas',0,1,'C',true);
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
$pdf->SetFont('Arial','',9);

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230,210,255);
foreach ([['Nombre del Especialista',100],['Especialidad',55],['Total Citas del Periodo',35]] as [$h,$w])
    $pdf->Cell($w,8,$h,1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$fill = false;
foreach ($rows as $r) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(100,7,mb_convert_encoding($r['especialista'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(55,7,mb_convert_encoding($r['especialidad']??'-','ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(35,7,$r['total_citas'],1,1,'C',$fill);
    $fill = !$fill;
}

$pdf->Output('I','rendimiento_especialistas_'.date('Ymd').'.pdf');
