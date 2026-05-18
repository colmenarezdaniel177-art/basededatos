<?php
require_once 'Controller.php';

class CitaController extends Controller
{
    private $pacienteModel;
    private $especialistaModel;
    private $horarioModel;
    private $statuCitaModel;
    public function __construct($model)
    {
        parent::__construct($model);
        require_once '../config/database.php';
        require_once '../models/Paciente.php';
        require_once '../models/Especialista.php';
        require_once '../models/horario.php';
        require_once '../models/estatus_cita.php';
        $database = new Database();
        $db = $database->getConnection();
        $this->pacienteModel = new PacienteModel($db);
        $this->especialistaModel = new EspecialistaModel($db);
        $this->horarioModel = new HorarioModel($db);
        $this->statuCitaModel = new Estatus_CitaModel($db);
    }

    public function index()
    {
        $citas = $this->model->read();
        $this->loadView('cita/index', ['citas' => $citas]);
    }
    public function index2()
    {
        $citas = $this->model->read();
        $this->loadView('cita/index2', ['citas' => $citas]);
    }

    public function create()
    {
        if ($_POST) {



            $this->model->paciente_id = $_POST['paciente_id'];
            $this->model->especialista_id = $_POST['especialista_id'];
            $this->model->fecha = $_POST['fecha'];
            $this->model->nota = $_POST['nota'];

            $statusId = $this->statuCitaModel->getStatusIdByName('Programada');
            if ($statusId) {
                $this->model->status_id = $statusId;
            } else {
                // Valor por defecto en caso de que no exista en la BD
                $this->model->status_id = 1;
            }

            $fecha = new DateTime($this->model->fecha);
            $diascita = $fecha->format('N');

            // 2. Obtener la lista de horarios (es un array)
            $horariosDisponibles = $this->horarioModel->readHorarios($this->model->especialista_id);

            $atencionEncontrada = false;
            foreach ($horariosDisponibles as $horario) {
                if ((int)$diascita === (int)$horario->dia_semana) {
                    $atencionEncontrada = true;
                    break;
                }
            }

            if (!$atencionEncontrada) {
                $nombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                $nombreDia = $nombres[$diascita] ?? 'desconocido';
                $_SESSION['error'] = "El especialista no atiende los días $nombreDia.";
                $this->loadView('cita/create', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
                return;
            }



            if ($this->model->cita_Exists($this->model->paciente_id, $this->model->especialista_id, $this->model->fecha)) {
                $_SESSION['error'] = "La Cita ya existe";
                $this->loadView('cita/create', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Cita creada exitosamente";
                $this->redirect('index.php?controller=cita&action=index');
            } else {
                $_SESSION['error'] = "Error al crear cita";
                $this->loadView('cita/create', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
            }
            return;
        }
        $this->loadView('cita/create', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
    }



    public function create2()
    {
        if ($_POST) {
            $this->model->paciente_id = $_POST['paciente_id'];
            $this->model->especialista_id = $_POST['especialista_id'];
            $this->model->fecha = $_POST['fecha'];
            $this->model->status = 1;
            $this->model->nota = $_POST['nota'];

            if ($this->model->cita_Exists($this->model->paciente_id, $this->model->especialista_id, $this->model->fecha)) {
                $_SESSION['error'] = "La Cita ya existe";
                $this->loadView('cita/create2', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Cita creada exitosamente";
                $this->redirect('index.php?controller=cita&action=index2');
            } else {
                $_SESSION['error'] = "Error al crear cita";
                $this->loadView('cita/create2', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
            }
            return;
        }
        $this->loadView('cita/create2', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
    }

    public function edit($id)
    {
        $this->model->id = $id;
        $Existe = $this->model->readOne();
        if (!$Existe) {
            $_SESSION['error'] = "Registro no encontrado";
            $this->redirect('index.php?controller=cita&action=index');
            return;
        }

        if ($_POST) {
            $this->model->paciente_id = $_POST['paciente_id'];
            $this->model->especialista_id = $_POST['especialista_id'];
            $this->model->fecha = $_POST['fecha'];
            $this->model->status_id = $_POST['status_id'];
            $this->model->nota = $_POST['nota'];
            if ($this->model->cita_Exists($this->model->paciente_id, $this->model->especialista_id, $this->model->fecha, $this->model->id)) {
                $_SESSION['error'] = "Ya existe otro registro con esas caracteristicas";
                $this->loadView('cita/edit', ['cita' => ['id' => $id, 'paciente_id' => $this->model->paciente_id, 'especialista_id' => $this->model->especialista_id, 'fecha' => $this->model->fecha, 'status' => $this->model->status, 'nota' => $this->model->nota], 'especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes(), 'listStatus' => $this->GetStatus()]);
                return;
            }

            if ($this->model->update()) {
                $_SESSION['success'] = "Cita actualizada exitosamente";
                $this->redirect('index.php?controller=cita&action=index');
            } else {
                $_SESSION['error'] = "Error al actualizar especialista";
            }
        }
        $cita = [
            'id' => $this->model->id,
            'paciente_id' => $this->model->paciente_id,
            'especialista_id' => $this->model->especialista_id,
            'fecha' => $this->model->fecha,
            'status_id' => $this->model->status_id,
            'nota' => $this->model->nota,
        ];
        $this->loadView('cita/edit', ['cita' => $cita, 'especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes(), 'listStatus' => $this->GetStatus()]);
    }

    public function delete($id)
    {
        $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Cita eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar Cita";
        }

        $this->redirect('index.php?controller=cita&action=index');
    }

    public function cancelar($id)
    {
        $this->model->id = $id;
        $this->model->readOne();
        $this->model->status = 'Cancelada';

        if ($this->model->update()) {
            $_SESSION['success'] = "Cita cancelada exitosamente";
        } else {
            $_SESSION['error'] = "Error al cancelar Cita";
        }

        $this->redirect('index.php?controller=cita&action=index2');
    }

    public function atender($id)
    {
        $this->model->id = $id;
        $existe = $this->model->readOneFOrAtender();

        if (!$existe) {
            $_SESSION['error'] = "Cita no encontrada";
            $this->redirect('index.php?controller=cita&action=index');
            return;
        }
        $cita = [
            'id' => $this->model->id,
            'paciente_nombre' => $this->model->paciente_nombre,
            'especialista_nombre' => $this->model->especialista_nombre,
            'fecha' => $this->model->fecha
        ];


        $this->loadView('cita/atender', ['cita' => $cita]);
    }


    public function GetEspecialistas()
    {
        return $this->especialistaModel->read();
    }
    public function GetPacientes()
    {
        return $this->pacienteModel->read();
    }

    public function GetStatus()
    {
        return $this->statuCitaModel->read();
    }

    public function guardarConsulta()
    {
        if ($_POST) {
            // Asignamos los valores recibidos del formulario al modelo
            $this->model->id = $_POST['cita_id'];
            $this->model->motivo_consulta = $_POST['motivo_consulta'];
            $this->model->tratamiento = $_POST['tratamiento'];
            $this->model->observaciones = $_POST['observaciones'];
            $statusId = $this->statuCitaModel->getStatusIdByName('Completada');
            // Cambiamos el status a uno de los valores de tu ENUM
            $this->model->status_id = $statusId;

            if ($this->model->finalizarCita()) {
                $_SESSION['success'] = "Consulta finalizada y guardada correctamente.";
                $this->redirect('index.php?controller=cita&action=index');
            } else {
                $_SESSION['error'] = "Error al intentar guardar los detalles de la consulta.";
                $this->redirect('index.php?controller=cita&action=index');
            }
        }
    }
    public function reportePacientes() {
        $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
        $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

        // 1. Si el usuario solicita descargar el PDF del reporte operacional
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";
            require_once dirname(__DIR__) . "/models/cita.php";

            $database = new Database();
            $db = $database->getConnection();
            $citaModel = new CitaModel($db);

            $stmt = $citaModel->consultarCitasPorRango($fecha_inicio, $fecha_fin);
            $totalRegistros = $stmt->rowCount();

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Cell(0, 10, utf8_decode('Sistema Clínico - Ozono Vital'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Reporte de Pacientes Atendidos"), 0, 1, 'C');
            $pdf->Cell(0, 8, utf8_decode("Período: $fecha_inicio al $fecha_fin"), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(232, 232, 232);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(25, 8, 'ID Cita', 1, 0, 'C', true);
            $pdf->Cell(65, 8, 'Paciente', 1, 0, 'L', true);
            $pdf->Cell(65, 8, 'Doctor Especialista', 1, 0, 'L', true);
            $pdf->Cell(35, 8, 'Fecha', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 10);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdf->Cell(25, 7, $row['id'], 1, 0, 'C');
                $pdf->Cell(65, 7, utf8_decode($row['paciente_nombre']), 1, 0, 'L');
                $pdf->Cell(65, 7, utf8_decode($row['especialista_nombre']), 1, 0, 'L');
                $pdf->Cell(35, 7, $row['fecha'], 1, 1, 'C');
            }

            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 8, utf8_decode("Total de pacientes atendidos: " . $totalRegistros), 0, 1, 'L');

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Reporte_Citas_{$fecha_inicio}_a_{$fecha_fin}.pdf");
            exit;
        }

        // 2. Si es una carga web normal, inicializa y conecta la vista
        $title = "Reporte de Pacientes Atendidos"; 
        require_once dirname(__DIR__) . "/config/database.php"; 
        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/cita/pacientes_atendidos.php";
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }


    public function reporteAgenda() {
        $especialista_id = isset($_GET['especialista_id']) ? intval($_GET['especialista_id']) : 0;
        $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
        $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

        // 1. Si el usuario solicita descargar el PDF de la agenda
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";
            require_once dirname(__DIR__) . "/models/cita.php";

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

            $stmt = $citaModel->consultarAgendaPorEspecialista($especialista_id, $fecha_inicio, $fecha_fin);
            $total = $stmt->rowCount();

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Control de Citas'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Agenda Operacional del Especialista"), 0, 1, 'C');
            $pdf->Cell(0, 8, utf8_decode("Médico: " . $nomeMedico), 0, 1, 'C');
            $pdf->Cell(0, 8, utf8_decode("Período: $fecha_inicio al $fecha_fin"), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(232, 232, 232);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(20, 8, 'ID Cita', 1, 0, 'C', true);
            $pdf->Cell(70, 8, 'Paciente', 1, 0, 'L', true);
            $pdf->Cell(45, 8, 'Fecha Asignada', 1, 0, 'C', true);
            $pdf->Cell(50, 8, 'Estado', 1, 1, 'C', true);

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

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Agenda_Especialista_{$especialista_id}.pdf");
            exit;
        }

        // 2. Carga normal en pantalla HTML
        $title = "Agenda por Especialista"; 
        require_once dirname(__DIR__) . "/config/database.php"; 
        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/cita/agenda_especialista.php"; 
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }


    public function reporteFichaPaciente() {
        // 1. Detectar si el usuario presionó el botón de descargar PDF de la ficha
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            $paciente_id = isset($_GET['paciente_id']) ? intval($_GET['paciente_id']) : 0;

            if ($paciente_id === 0) {
                die("ID de paciente no válido.");
            }

            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";
            require_once dirname(__DIR__) . "/models/paciente.php";

            $database = new Database();
            $db = $database->getConnection();
            $pacienteModel = new PacienteModel($db);
            $stmt = $pacienteModel->consultarAntecedentesPaciente($paciente_id);
            
            $antecedentes = [];
            $nombre = "No especificado";
            $cedula = "No especificada";

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

            $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Ficha Clínica'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Ficha Operacional de Antecedentes Médicos"), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 7, utf8_decode("DATOS DEL PACIENTE"), 0, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(40, 7, utf8_decode("Nombre y Apellido:"), 0, 0, 'L');
            $pdf->Cell(0, 7, utf8_decode($nombre), 0, 1, 'L');
            $pdf->Cell(40, 7, utf8_decode("Cédula de Identidad:"), 0, 0, 'L');
            $pdf->Cell(0, 7, utf8_decode($cedula), 0, 1, 'L');
            $pdf->Ln(5);

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

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Ficha_Paciente_{$cedula}.pdf");
            exit;
        }

        // 2. Si no es descarga, procesar la carga normal de la pantalla HTML
        $title = "Antecedentes por Paciente"; 
        require_once dirname(__DIR__) . "/config/database.php"; 
        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/cita/ficha_paciente.php"; 
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }

        public function reporteGerencialMensual() {
        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

        // 1. Si el usuario solicita descargar el PDF Gerencial
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT MONTH(fecha) AS mes_num, COUNT(*) AS total_citas 
                      FROM cita 
                      WHERE YEAR(fecha) = :anio
                      GROUP BY MONTH(fecha)
                      ORDER BY mes_num ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":anio", $anio, PDO::PARAM_INT);
            $stmt->execute();

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Reporte Gerencial Directivo'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Estadística de Volumen Mensual de Consultas - Año " . $anio), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(232, 232, 232);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(60, 8, 'Mes de Analisis', 1, 0, 'C', true);
            $pdf->Cell(130, 8, utf8_decode('Total de Consultas Atendidas'), 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 10);
            $meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $nombre_mes = $meses[$row['mes_num']];
                $pdf->Cell(60, 7, $nombre_mes, 1, 0, 'C');
                $pdf->Cell(130, 7, $row['total_citas'], 1, 1, 'C');
            }

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Reporte_Gerencial_Mensual_{$anio}.pdf");
            exit;
        }

        // 2. Carga normal en pantalla HTML
        $title = "Volumen Mensual Gerencial"; 
        require_once dirname(__DIR__) . "/config/database.php"; 
        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/cita/reporte_gerencial_mensual.php"; 
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }
}
