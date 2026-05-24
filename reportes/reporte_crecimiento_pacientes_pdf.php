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
    "SELECT DATE_FORMAT(created_at,'%Y-%m') AS mes,
            DATE_FORMAT(created_at,'%M %Y') AS mes_label,
            COUNT(*) AS total
     FROM pacientes
     GROUP BY mes
     ORDER BY mes ASC"
)->fetchAll();

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(40,167,69); $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Crecimiento Base de Datos Pacientes',0,1,'C',true);
        $this->SetTextColor(0);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,6,'Analisis estrategico mensual de captacion de usuarios en el sistema clinico.',0,1,'C');
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
$pdf->SetFillColor(220,255,220);
foreach ([['Mes de Registro',120],['Nuevos Pacientes Incorporados',70]] as [$h,$w])
    $pdf->Cell($w,8,$h,1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$fill = false; $total = 0;
foreach ($rows as $r) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(120,7,mb_convert_encoding($r['mes_label'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(70,7,$r['total'],1,1,'C',$fill);
    $total += $r['total'];
    $fill = !$fill;
}
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(200,240,200);
$pdf->Cell(120,7,'TOTAL',1,0,'R',true);
$pdf->Cell(70,7,$total,1,1,'C',true);

$pdf->Output('I','crecimiento_pacientes_'.date('Ymd').'.pdf');
