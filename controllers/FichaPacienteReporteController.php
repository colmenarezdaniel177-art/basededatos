<?php
// controllers/FichaPacienteReporteController.php

require_once dirname(__DIR__) . "/config/database.php"; 
require_once dirname(__DIR__) . "/models/paciente.php";
require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

class FichaPacienteReporteController {

    public function generarPdf() {
        $paciente_id = isset($_GET['paciente_id']) ? intval($_GET['paciente_id']) : 0;

        if ($paciente_id === 0) {
            die("ID de paciente no válido.");
        }

        $database = new Database();
        $db = $database->getConnection();
        $pacienteModel = new PacienteModel($db); // Asegúrate de que tu clase en models/paciente.php se llame PacienteModel

        $stmt = $pacienteModel->consultarAntecedentesPaciente($paciente_id);
        
        $antecedentes = [];
        $nombre = "No especificado";
        $cedula = "No especificada";

        // Agrupar la información del paciente y sus múltiples antecedentes
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nombre = $row['nombre'];
            $cedula = $row['cedula'];
            if (!empty($row['antecedente'])) {
                $antecedentes[] = $row;
            }
        }

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // --- ENCABEZADO ---
        $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Ficha Clínica'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, utf8_decode("Ficha Operacional de Antecedentes Médicos"), 0, 1, 'C');
        $pdf->Ln(5);

        // --- DATOS PERSONALES DEL PACIENTE ---
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 7, utf8_decode("DATOS DEL PACIENTE"), 0, 1, 'L', true);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 7, utf8_decode("Nombre y Apellido:"), 0, 0, 'L');
        $pdf->Cell(0, 7, utf8_decode($nombre), 0, 1, 'L');
        $pdf->Cell(40, 7, utf8_decode("Cédula de Identidad:"), 0, 0, 'L');
        $pdf->Cell(0, 7, utf8_decode($cedula), 0, 1, 'L');
        $pdf->Ln(5);

        // --- TABLA DE ANTECEDENTES ---
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(190, 8, utf8_decode('Descripción del Antecedente Médico'), 1, 1, 'L', true);

        $pdf->SetFont('Arial', '', 10);
        if (count($antecedentes) > 0) {
            foreach ($antecedentes as $ant) {
                $pdf->Cell(190, 7, utf8_decode($ant['antecedente']), 1, 1, 'L');
            }
        } else {
            $pdf->Cell(190, 7, utf8_decode('No registra antecedentes médicos en el sistema.'), 1, 1, 'C');
        }


        $pdf->Output('I', "Ficha_Paciente_{$cedula}.pdf");
    }
}

$reporte = new FichaPacienteReporteController();
$reporte->generarPdf();
