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
    "SELECT CONCAT(p.nombre,' ',p.apellido) AS paciente,
            CONCAT(e.nombre,' ',e.apellido) AS especialista,
            c.fecha, sc.nombre AS estado, c.motivo
     FROM citas c
     JOIN pacientes p ON c.paciente_id = p.id
     JOIN especialistas e ON c.especialista_id = e.id
     JOIN status_cita sc ON c.status_id = sc.id
     WHERE LOWER(sc.nombre) IN ('cancelada','ausente','no asistio')
     ORDER BY c.fecha DESC"
)->fetchAll();

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(220,53,69); $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Control de Citas Canceladas / Ausencias',0,1,'C',true);
        $this->SetTextColor(0); $this->Ln(3);
    }
    function Footer() {
        $this->SetY(-15); $this->SetFont('Arial','I',8); $this->SetTextColor(128);
        $this->Cell(0,10,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
    }
}

$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(255,210,210);
foreach ([['Paciente',65],['Especialista',60],['Fecha',25],['Estado',30],['Nota / Motivo',97]] as [$h,$w])
    $pdf->Cell($w,8,$h,1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',8);
$fill = false;
foreach ($rows as $r) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(65,7,mb_convert_encoding($r['paciente'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(60,7,mb_convert_encoding($r['especialista'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(25,7,date('d/m/Y',strtotime($r['fecha'])),1,0,'C',$fill);
    $pdf->Cell(30,7,mb_convert_encoding($r['estado'],'ISO-8859-1','UTF-8'),1,0,'C',$fill);
    $pdf->Cell(97,7,mb_convert_encoding(mb_strimwidth($r['motivo']??'-',0,55,'...','UTF-8'),'ISO-8859-1','UTF-8'),1,1,'L',$fill);
    $fill = !$fill;
}
if (!$rows) {
    $pdf->SetFont('Arial','I',9);
    $pdf->Cell(0,10,'No hay citas canceladas registradas.',0,1,'C');
}

$pdf->Output('I','citas_canceladas_'.date('Ymd').'.pdf');
