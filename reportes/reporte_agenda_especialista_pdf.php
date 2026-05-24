<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$fpdfPath = __DIR__ . '/../vendor/fpdf/fpdf.php';
if (!file_exists($fpdfPath)) die('FPDF no encontrado en vendor/fpdf/fpdf.php');
require_once $fpdfPath;

$especialista_id = (int)($_GET['especialista_id'] ?? 0);
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

if (!$especialista_id) die('Selecciona un especialista.');

$stmtE = $pdo->prepare("SELECT CONCAT(nombre,' ',apellido) AS nombre FROM especialistas WHERE id=?");
$stmtE->execute([$especialista_id]);
$esp = $stmtE->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT c.id, CONCAT(p.nombre,' ',p.apellido) AS paciente, c.fecha, sc.nombre AS estado
     FROM citas c
     JOIN pacientes p ON c.paciente_id=p.id
     LEFT JOIN status_cita sc ON c.status_id=sc.id
     WHERE c.especialista_id=? AND c.fecha BETWEEN ? AND ?
     ORDER BY c.fecha ASC"
);
$stmt->execute([$especialista_id, $desde, $hasta]);
$rows = $stmt->fetchAll();

class PDF extends FPDF {
    public $esp, $desde, $hasta;
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->SetFillColor(13,111,210); $this->SetTextColor(255);
        $this->Cell(0,12,'OzonoVital - Agenda de Citas por Especialista',0,1,'C',true);
        $this->SetTextColor(0);
        $this->SetFont('Arial','',9);
        $this->Cell(0,6,'Especialista: '.mb_convert_encoding($this->esp,'ISO-8859-1','UTF-8').'   |   Periodo: '.$this->desde.' al '.$this->hasta,0,1,'C');
        $this->Ln(2);
    }
    function Footer() {
        $this->SetY(-15); $this->SetFont('Arial','I',8); $this->SetTextColor(128);
        $this->Cell(0,10,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
    }
}

$pdf = new PDF('P','mm','A4');
$pdf->esp   = $esp;
$pdf->desde = date('d/m/Y', strtotime($desde));
$pdf->hasta = date('d/m/Y', strtotime($hasta));
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230,240,255);
foreach ([['ID Cita',20],['Paciente',100],['Fecha Asignada',35],['Estado',35]] as [$h,$w])
    $pdf->Cell($w,8,$h,1,0,'C',true);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
$fill = false;
foreach ($rows as $r) {
    $pdf->SetFillColor($fill?245:255,255,255);
    $pdf->Cell(20,7,$r['id'],1,0,'C',$fill);
    $pdf->Cell(100,7,mb_convert_encoding($r['paciente'],'ISO-8859-1','UTF-8'),1,0,'L',$fill);
    $pdf->Cell(35,7,date('d/m/Y',strtotime($r['fecha'])),1,0,'C',$fill);
    $pdf->Cell(35,7,mb_convert_encoding($r['estado']??'-','ISO-8859-1','UTF-8'),1,1,'C',$fill);
    $fill = !$fill;
}
if (!$rows) {
    $pdf->SetFont('Arial','I',9);
    $pdf->Cell(0,10,'No hay citas en el periodo seleccionado.',0,1,'C');
}

$pdf->Output('I','agenda_especialista_'.date('Ymd').'.pdf');
