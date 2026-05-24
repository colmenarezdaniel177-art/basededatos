<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$fpdfPath = __DIR__ . '/../vendor/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) die('FPDF no encontrado en vendor/fpdf/fpdf.php');
require_once $fpdfPath;

$rows = $pdo->query(
    "SELECT esp.nombre AS especialidad, COUNT(c.id) AS total
     FROM citas c
     JOIN especialistas e ON c.especialista_id = e.id
     JOIN especialidades esp ON e.especialidad_id = esp.id
     GROUP BY esp.id, esp.nombre
     ORDER BY total DESC"
)->fetchAll();

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(13,111,210); $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Especialidades Medicas Mas Demandadas',0,1,'C',true);
        $this->SetTextColor(0);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,6,'Indicador de Toma de Decisiones: Evalúe qué áreas de atención clínica tienen mayor volumen de ocupación para planificar futuras contrataciones.',0,1,'C');
        $this->Ln(2);
    }
    function Footer() {
        $this->SetY(-15); $this->SetFont('Arial','I',8); $this->SetTextColor(128);
        $this->Cell(0,10,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230,240,255);
foreach ([['Lugar (Top)',30],['Especialidad Medica',110],['Total Solicitudes',50]] as [$h,$w])
    $pdf->Cell($w,8,$h,1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$fill = false;
foreach ($rows as $i => $r) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(30,7,'#'.($i+1),1,0,'C',$fill);
    $pdf->Cell(110,7,mb_convert_encoding($r['especialidad'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(50,7,$r['total'],1,1,'C',$fill);
    $fill = !$fill;
}

$pdf->Output('I','especialidades_demandadas_'.date('Ymd').'.pdf');
