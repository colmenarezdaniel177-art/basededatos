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
    "SELECT ta.id, ta.nombre AS categoria, COUNT(DISTINCT a.paciente_id) AS pacientes
     FROM tipo_antecedente ta
     LEFT JOIN antecedentes a ON ta.id = a.tipo_id
     GROUP BY ta.id, ta.nombre
     ORDER BY pacientes DESC"
)->fetchAll();

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(255,140,0); $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Carga Clinica por Tipo de Antecedente',0,1,'C',true);
        $this->SetTextColor(0);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,6,'Consolida la cantidad de pacientes unicos asociados a cada patologia o antecedente registrado.',0,1,'C');
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
$pdf->SetFillColor(255,235,200);
foreach ([['ID Tipo',25],['Categoria de Antecedente Medico',120],['Pacientes Diagnosticados',45]] as [$h,$w])
    $pdf->Cell($w,8,$h,1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$fill = false;
foreach ($rows as $r) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(25,7,$r['id'],1,0,'C',$fill);
    $pdf->Cell(120,7,mb_convert_encoding($r['categoria'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(45,7,$r['pacientes'],1,1,'C',$fill);
    $fill = !$fill;
}

$pdf->Output('I','carga_clinica_'.date('Ymd').'.pdf');
