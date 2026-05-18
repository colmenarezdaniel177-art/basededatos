<?php
// controllers/CitaReporteController.php

// 1. Inclusiones seguras con rutas absolutas
require_once dirname(__DIR__) . "/config/database.php"; 
require_once dirname(__DIR__) . "/models/cita.php";

// 2. Ruta exacta hacia la librería dentro de public (ya existiendo los archivos)
require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

class CitaReporteController {

    public function generarPdf() {
        $inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d');
        $fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

        $database = new Database();
        $db = $database->getConnection();
        $citaModel = new CitaModel($db);

        $stmt = $citaModel->consultarCitasPorRango($inicio, $fin);
        $totalRegistros = $stmt->rowCount();

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // --- ENCABEZADO DEL REPORTE ---
        $pdf->Cell(0, 10, utf8_decode('Sistema Clínico - Ozono Vital'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, utf8_decode("Reporte de Pacientes Atendidos"), 0, 1, 'C');
        $pdf->Cell(0, 8, utf8_decode("Período: $inicio al $fin"), 0, 1, 'C');
        $pdf->Ln(5);

        // --- CABECERA DE LA TABLA ---
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(25, 8, 'ID Cita', 1, 0, 'C', true);
        $pdf->Cell(65, 8, 'Paciente', 1, 0, 'L', true);
        $pdf->Cell(65, 8, 'Doctor Especialista', 1, 0, 'L', true);
        $pdf->Cell(35, 8, 'Fecha', 1, 1, 'C', true);

        // --- CUERPO DE LA TABLA ---
        $pdf->SetFont('Arial', '', 10);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pdf->Cell(25, 7, $row['id'], 1, 0, 'C');
            $pdf->Cell(65, 7, utf8_decode($row['paciente_nombre']), 1, 0, 'L');
            $pdf->Cell(65, 7, utf8_decode($row['especialista_nombre']), 1, 0, 'L');
            $pdf->Cell(35, 7, $row['fecha'], 1, 1, 'C');
        }

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, utf8_decode("Total de pacientes atendidos:" . $totalRegistros), 0, 1, 'L');

        $pdf->Output('I', "Reporte_Citas_{$inicio}_a_{$fin}.pdf");
    }
}

$reporte = new CitaReporteController();
$reporte->generarPdf();
