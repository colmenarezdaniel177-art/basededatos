<?php
// controllers/RendimientoReporteController.php

// 1. Inclusiones seguras con rutas absolutas desde la raíz del proyecto
require_once dirname(__DIR__) . "/config/database.php"; 
require_once dirname(__DIR__) . "/models/especialista.php";

// 2. Ruta exacta hacia la librería dentro de public que ya configuramos
require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

class RendimientoReporteController {

    public function generarPdf() {
        $inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
        $fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

        $database = new Database();
        $db = $database->getConnection();
        $especialistaModel = new EspecialistaModel($db); // Valida si tu clase se llama EspecialistaModel

        $stmt = $especialistaModel->consultarRendimientoEspecialistas($inicio, $fin);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // --- ENCABEZADO ---
        $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Reporte de Supervisión'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, utf8_decode("Rendimiento y Volumen de Citas por Especialista"), 0, 1, 'C');
        $pdf->Cell(0, 8, utf8_decode("Período: $inicio al $fin"), 0, 1, 'C');
        $pdf->Ln(5);

        // --- CABECERA TABLA ---
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(30, 8, 'ID Medico', 1, 0, 'C', true);
        $pdf->Cell(110, 8, 'Nombre del Especialista', 1, 0, 'L', true);
        $pdf->Cell(50, 8, 'Total Citas Asignadas', 1, 1, 'C', true);

        // --- DATOS ---
        $pdf->SetFont('Arial', '', 10);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pdf->Cell(30, 7, $row['id'], 1, 0, 'C');
            $pdf->Cell(110, 7, utf8_decode($row['nombre']), 1, 0, 'L');
            $pdf->Cell(50, 7, $row['total_citas'], 1, 1, 'C');
        }

        $pdf->Output('I', "Rendimiento_Especialistas_{$inicio}.pdf");
    }
}

$reporte = new RendimientoReporteController();
$reporte->generarPdf();
