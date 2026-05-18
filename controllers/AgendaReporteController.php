<?php
// controllers/AgendaReporteController.php

// 1. Inclusiones seguras con rutas absolutas desde la raíz del proyecto
require_once dirname(__DIR__) . "/config/database.php"; 
require_once dirname(__DIR__) . "/models/cita.php";
require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

class AgendaReporteController {

    public function generarPdf() {
        $especialista_id = isset($_GET['especialista_id']) ? intval($_GET['especialista_id']) : 0;
        $inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d');
        $fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');

        $database = new Database();
        $db = $database->getConnection();
        $citaModel = new CitaModel($db);

        // Obtener el nombre del especialista de forma limpia
        $nomeMedico = "No especificado";
        if ($especialista_id > 0) {
            $qMed = "SELECT nombre FROM especialista WHERE id = ?";
            $sMed = $db->prepare($qMed);
            $sMed->execute([$especialista_id]);
            if($rMed = $sMed->fetch(PDO::FETCH_ASSOC)) {
                $nomeMedico = $rMed['nombre'];
            }
        }

        $stmt = $citaModel->consultarAgendaPorEspecialista($especialista_id, $inicio, $fin);
        $total = $stmt->rowCount();

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // --- ENCABEZADO ---
        $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Control de Citas'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, utf8_decode("Agenda Operacional del Especialista"), 0, 1, 'C');
        $pdf->Cell(0, 8, utf8_decode("Médico: " . $nomeMedico), 0, 1, 'C');
        $pdf->Cell(0, 8, utf8_decode("Período: $inicio al $fin"), 0, 1, 'C');
        $pdf->Ln(5);

        // --- CABECERA TABLA ---
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(20, 8, 'ID Cita', 1, 0, 'C', true);
        $pdf->Cell(70, 8, 'Paciente', 1, 0, 'L', true);
        $pdf->Cell(45, 8, 'Fecha Asignada', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Estado', 1, 1, 'C', true);

        // --- DATOS ---
        $pdf->SetFont('Arial', '', 10);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pdf->Cell(20, 7, $row['id'], 1, 0, 'C');
            $pdf->Cell(70, 7, utf8_decode($row['paciente_nombre']), 1, 0, 'L');
            $pdf->Cell(45, 7, $row['fecha'], 1, 0, 'C');
            $pdf->Cell(50, 7, utf8_decode($row['status']), 1, 1, 'C');
        }

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, utf8_decode("Total de citas agendadas: " . $total), 0, 1, 'L');

        $pdf->Output('I', "Agenda_Especialista_{$especialista_id}.pdf");
    }
}

// Inicializar y ejecutar la acción del controlador
$reporte = new AgendaReporteController();
$reporte->generarPdf();
