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

$pacientes = $pdo->query("SELECT p.*, CONCAT(p.nombre,' ',p.apellido) AS nombre_completo, u.nombre AS usuario, (SELECT COUNT(*) FROM citas WHERE paciente_id=p.id) AS total_citas, (SELECT COUNT(*) FROM consultas WHERE paciente_id=p.id) AS total_consultas FROM pacientes p LEFT JOIN usuarios u ON p.usuario_id=u.id ORDER BY p.nombre")->fetchAll();

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(22,163,74);
        $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Reporte de Pacientes',0,1,'C',true);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',9);
        $this->Ln(3);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(0,7,"Total de pacientes: " . count($pacientes) . "   |   Generado: " . date('d/m/Y H:i'),0,1);
$pdf->Ln(2);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(220,240,220);
$cols = [['#',10],['Nombre',60],['Fecha Nac.',28],['Género',20],['Teléfono',35],['Citas',18],['Consultas',22]];
foreach ($cols as [$h,$w]) $pdf->Cell($w,7,$h,1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',8);
$fill = false;
foreach ($pacientes as $i => $p) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(10,6,$p['id'],1,0,'C',$fill);
    $pdf->Cell(60,6,mb_strimwidth($p['nombre_completo'],0,35,'...'),1,0,'L',$fill);
    $pdf->Cell(28,6,$p['fecha_nacimiento']?date('d/m/Y',strtotime($p['fecha_nacimiento'])):'-',1,0,'C',$fill);
    $genero = $p['genero']==='M'?'Masculino':($p['genero']==='F'?'Femenino':'Otro');
    $pdf->Cell(20,6,$genero,1,0,'C',$fill);
    $pdf->Cell(35,6,$p['telefono']??'-',1,0,'L',$fill);
    $pdf->Cell(18,6,$p['total_citas'],1,0,'C',$fill);
    $pdf->Cell(22,6,$p['total_consultas'],1,1,'C',$fill);
    $fill = !$fill;
}

$pdf->Output('I','reporte_pacientes_'.date('Ymd').'.pdf');
