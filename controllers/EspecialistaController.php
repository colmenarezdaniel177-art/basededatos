<?php
require_once 'Controller.php';

class EspecialistaController extends Controller
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        $especialistas = $this->model->read();
        $this->loadView('especialista/index', ['especialistas' => $especialistas]);
    }

    public function create()
    {
        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];
            $this->model->especialidad_id = $_POST['especialidad_id'];

            if ($this->model->especialistaExists($this->model->nombre)) {
                $_SESSION['error'] = "El especialista ya existe";
                $this->loadView('especialista/create', ['especialidades' => $this->GetEspecialidades()]);
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Especialista creado exitosamente";
                $this->redirect('index.php?controller=especialista&action=index');
            } else {
                $_SESSION['error'] = "Error al crear especialista";
                $this->loadView('especialista/create', ['especialidades' => $this->GetEspecialidades()]);
            }
            return;
        }
        $this->loadView('especialista/create', ['especialidades' => $this->GetEspecialidades()]);
    }

    public function edit($id)
    {
        $this->model->id = $id;
        $Existe = $this->model->readOne();
        if (!$Existe) {
            $_SESSION['error'] = "Registro no encontrado";
            $this->redirect('index.php?controller=especialista&action=index');
            return;
        }

        if ($_POST) {
            $this->model->id = $id;
            $this->model->nombre = $_POST['nombre'];
            $this->model->especialidad_id = $_POST['especialidad_id'];
            if ($this->model->especialistaExists($this->model->nombre, $this->model->id)) {
                $_SESSION['error'] = "Ya existe otro registro con esas caracteristicas";
                $this->loadView('especialista/edit', ['especialista' => ['id' => $id, 'nombre' => $this->model->nombre, 'especialidad' => $this->model->especialidad]]);
                return;
            }

            if ($this->model->update()) {
                $_SESSION['success'] = "Especialista actualizado exitosamente";
                $this->redirect('index.php?controller=especialista&action=index');
            } else {
                $_SESSION['error'] = "Error al actualizar especialista";
            }
        }
        $especialista = [
            'id' => $this->model->id,
            'nombre' => $this->model->nombre,
            'especialidad_id' => $this->model->especialidad_id,
        ];

        $this->loadView('especialista/edit', ['especialista' => $especialista, 'especialidades' => $this->GetEspecialidades()]);
    }

    public function delete($id)
    {
        $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Especialista eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar especialista";
        }

        $this->redirect('index.php?controller=especialista&action=index');
    }


    public function GetEspecialidades()
    {
        return [
            'Medicina Interna',
            'Psicología',
            'Conducta'
        ];
    }

    public function reporteRendimiento() {
        // 1. Detectar si el usuario presionó el botón de descargar PDF
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            $inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
            $fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

            $database = new Database();
            $db = $database->getConnection();
            
            // En tu controlador, $this->model ya está disponible e instanciado dinámicamente
            $stmt = $this->model->consultarRendimientoEspecialistas($inicio, $fin);

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Reporte de Supervisión'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Rendimiento y Volumen de Citas por Especialista"), 0, 1, 'C');
            $pdf->Cell(0, 8, utf8_decode("Período: $inicio al $fin"), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(232, 232, 232);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 8, 'ID Medico', 1, 0, 'C', true);
            $pdf->Cell(110, 8, 'Nombre del Especialista', 1, 0, 'L', true);
            $pdf->Cell(50, 8, 'Total Citas Asignadas', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 10);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdf->Cell(30, 7, $row['id'], 1, 0, 'C');
                $pdf->Cell(110, 7, utf8_decode($row['nombre']), 1, 0, 'L');
                $pdf->Cell(50, 7, $row['total_citas'], 1, 1, 'C');
            }

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Rendimiento_Especialistas.pdf");
            exit; // Frena la carga para que el enrutador no intente meter HTML
        }

        // 2. Si no es descarga, procesa la carga normal de la pantalla
        $title = "Rendimiento de Especialistas"; 
        require_once dirname(__DIR__) . "/config/database.php"; 
        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/especialista/rendimiento_especialistas.php"; 
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }

    



}
