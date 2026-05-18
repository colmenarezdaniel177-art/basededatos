<?php
require_once 'Controller.php';

class Estatus_CitaController extends Controller
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        
        $estatus_citas = $this->model->read();        
        $this->loadView('estatus_cita/index', ['estatus_citas' => $estatus_citas]);
    }

    public function create()
    {
        if ($_POST) {
            // Asignamos el nombre enviado por el formulario
            $this->model->nombre = $_POST['nombre'];

            // Verificamos si ya existe un Estatus_Cita con ese nombre
            if ($this->model->Estatus_CitaExists($this->model->nombre)) {
                $_SESSION['error'] = "El Estatus_Cita ya existe";
                $this->loadView('estatus_cita/create');
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Estatus_Cita creado exitosamente";
                $this->redirect('index.php?controller=estatus_cita&action=index');
            } else {
                $_SESSION['error'] = "Error al crear el Estatus_Cita";
                $this->loadView('estatus_cita/create');
            }
            return;
        }
        $this->loadView('estatus_cita/create');
    }

    public function edit($id)
    {
    if (!$id) {
        $this->redirect('index.php?controller=estatus_cita&action=index');
        return;
    }

        $this->model->id = $id;
        $existe = $this->model->readOne();

        if (!$existe) {
            $_SESSION['error'] = "Estatus_Cita no encontrado";
            $this->redirect('index.php?controller=estatus_cita&action=index');
            return;
        }

        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];

            // Validar que el nuevo nombre no lo tenga otro Estatus_Cita
            if ($this->model->Estatus_CitaExists($this->model->nombre, $id)) {
                $_SESSION['error'] = "Ya existe otro Estatus_Cita con ese nombre";
            } else {
                if ($this->model->update()) {
                    $_SESSION['success'] = "Estatus_Cita actualizado exitosamente";
                    $this->redirect('index.php?controller=estatus_cita&action=index');
                    return;
                } else {
                    $_SESSION['error'] = "Error al actualizar";
                }
            }
        }

        // Pasamos los datos actuales a la vista
        $estatus_cita = [
            'id' => $this->model->id,
            'nombre' => $this->model->nombre
        ];

        $this->loadView('estatus_cita/edit', ['estatus_cita' => $estatus_cita]);
    }

    public function delete($id)
    {
         $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Estatus_Cita eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar Estatus_Cita";
        }

        $this->redirect('index.php?controller=estatus_cita&action=index');
    }

    public function reporteCanceladas() {
        // 1. Si se solicita descargar el PDF
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            $inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
            $fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";
            require_once dirname(__DIR__) . "/models/cita.php"; // Cargamos el modelo de citas

            $database = new Database();
            $db = $database->getConnection();
            $citaModel = new CitaModel($db);
            $stmt = $citaModel->consultarCitasCanceladas($inicio, $fin);

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Reporte de Supervisión'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Control de Citas Canceladas e Inasistencias"), 0, 1, 'C');
            $pdf->Cell(0, 8, utf8_decode("Período: $inicio al $fin"), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(232, 232, 232);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
            $pdf->Cell(50, 8, 'Paciente', 1, 0, 'L', true);
            $pdf->Cell(50, 8, 'Especialista', 1, 0, 'L', true);
            $pdf->Cell(25, 8, 'Fecha', 1, 0, 'C', true);
            $pdf->Cell(50, 8, 'Motivo / Nota', 1, 1, 'L', true);

            $pdf->SetFont('Arial', '', 9);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdf->Cell(15, 7, $row['id'], 1, 0, 'C');
                $pdf->Cell(50, 7, utf8_decode($row['paciente_nombre']), 1, 0, 'L');
                $pdf->Cell(50, 7, utf8_decode($row['especialista_nombre']), 1, 0, 'L');
                $pdf->Cell(25, 7, $row['fecha'], 1, 0, 'C');
                $pdf->Cell(50, 7, utf8_decode($row['nota']), 1, 1, 'L');
            }

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Citas_Canceladas.pdf");
            exit;
        }

        // 2. Carga normal en pantalla HTML
        $title = "Citas Canceladas"; 
        require_once dirname(__DIR__) . "/config/database.php"; 
        require_once dirname(__DIR__) . "/models/cita.php"; 

        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/Estatus_Cita/citas_canceladas.php"; 
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }


}
