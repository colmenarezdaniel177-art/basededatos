<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$fpdfPath = __DIR__ . '/../vendor/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) die('FPDF no encontrado en vendor/fpdf/fpdf.php');
require_once $fpdfPath;

$paciente_id = (int)($_GET['paciente_id'] ?? 0);
if (!$paciente_id) die('Selecciona un paciente.');

$stmtP = $pdo->prepare("SELECT * FROM pacientes WHERE id=?");
$stmtP->execute([$paciente_id]);
$p = $stmtP->fetch();
if (!$p) die('Paciente no encontrado.');

$antecedentes = $pdo->prepare(
    "SELECT a.descripcion, a.fecha, ta.nombre AS tipo FROM antecedentes a
     LEFT JOIN tipo_antecedente ta ON a.tipo_id=ta.id
     WHERE a.paciente_id=? ORDER BY ta.nombre, a.fecha DESC"
);
$antecedentes->execute([$paciente_id]);
$ants = $antecedentes->fetchAll();

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(13,111,210); $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Ficha de Paciente',0,1,'C',true);
        $this->SetTextColor(0); $this->Ln(3);
    }
    function Footer() {
        $this->SetY(-15); $this->SetFont('Arial','I',8); $this->SetTextColor(128);
        $this->Cell(0,10,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
    }
    function LabelValue($label, $value, $w1=50, $w2=130) {
        $this->SetFont('Arial','B',9);
        $this->Cell($w1,7,mb_convert_encoding($label,'ISO-8859-1','UTF-8'),0,0);
        $this->SetFont('Arial','',9);
        $this->Cell($w2,7,mb_convert_encoding($value,'ISO-8859-1','UTF-8'),0,1);
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// Datos personales
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(230,240,255);
$pdf->Cell(0,8,'Resumen de Datos Personales',0,1,'L',true);
$pdf->Ln(2);

$pdf->LabelValue('Nombre:', $p['nombre'].' '.$p['apellido']);
$pdf->LabelValue('Cedula:', $p['cedula'] ?? '-');
$pdf->LabelValue('Genero:', $p['genero']==='M'?'Masculino':'Femenino');
$pdf->LabelValue('Fecha Nac.:', $p['fecha_nacimiento'] ? date('d/m/Y',strtotime($p['fecha_nacimiento'])) : '-');
$pdf->LabelValue('Telefono:', $p['telefono'] ?? '-');
$pdf->LabelValue('Email:', $p['email'] ?? '-');
$pdf->LabelValue('Direccion:', $p['direccion'] ?? '-');
$pdf->Ln(5);

// Antecedentes
$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(255,240,200);
$pdf->Cell(0,8,'Descripcion del Antecedente Medico',0,1,'L',true);
$pdf->Ln(2);

if ($ants) {
    $pdf->SetFont('Arial','B',9);
    $pdf->SetFillColor(255,235,180);
    foreach ([['Tipo',40],['Descripcion',120],['Fecha',30]] as [$h,$w])
        $pdf->Cell($w,7,$h,1,0,'C',true);
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    $fill = false;
    foreach ($ants as $a) {
        $pdf->SetFillColor($fill?245:255,255,255);
        $pdf->Cell(40,7,mb_convert_encoding($a['tipo']??'-','ISO-8859-1','UTF-8'),1,0,'L',$fill);
        $pdf->Cell(120,7,mb_convert_encoding(mb_strimwidth($a['descripcion'],0,70,'...','UTF-8'),'ISO-8859-1','UTF-8'),1,0,'L',$fill);
        $pdf->Cell(30,7,$a['fecha']?date('d/m/Y',strtotime($a['fecha'])):'-',1,1,'C',$fill);
        $fill = !$fill;
    }
} else {
    $pdf->SetFont('Arial','I',9);
    $pdf->Cell(0,7,'Sin antecedentes registrados.',0,1);
}

$pdf->Output('I','ficha_paciente_'.$paciente_id.'_'.date('Ymd').'.pdf');
