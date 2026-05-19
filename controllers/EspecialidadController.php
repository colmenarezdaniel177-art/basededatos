<?php
require_once 'Controller.php';

class EspecialidadController extends Controller
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        
        $especialidades = $this->model->read();        
        $this->loadView('especialidad/index', ['especialidades' => $especialidades]);
    } 

    public function create()
    {
        if ($_POST) {
            // Asignamos el nombre enviado por el formulario
            $this->model->nombre = $_POST['nombre'];

            // Verificamos si ya existe una especialidad con ese nombre
            if ($this->model->especialidadExists($this->model->nombre)) {
                $_SESSION['error'] = "La especialidad ya existe";
                $this->loadView('especialidad/create');
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Especialidad creada exitosamente";
                $this->redirect('index.php?controller=especialidad&action=index');
            } else {
                $_SESSION['error'] = "Error al crear la especialidad";
                $this->loadView('especialidad/create');
            }
            return;
        }
        $this->loadView('especialidad/create');
    }

    public function edit($id)
    {
    if (!$id) {
        $this->redirect('index.php?controller=especialidad&action=index');
        return;
    }

        $this->model->id = $id;
        $existe = $this->model->readOne();

        if (!$existe) {
            $_SESSION['error'] = "Especialidad no encontrada";
            $this->redirect('index.php?controller=especialidad&action=index');
            return;
        }

        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];

            // Validar que el nuevo nombre no lo tenga otra especialidad
            if ($this->model->especialidadExists($this->model->nombre, $id)) {
                $_SESSION['error'] = "Ya existe otra especialidad con ese nombre";
            } else {
                if ($this->model->update()) {
                    $_SESSION['success'] = "Especialidad actualizada exitosamente";
                    $this->redirect('index.php?controller=especialidad&action=index');
                    return;
                } else {
                    $_SESSION['error'] = "Error al actualizar";
                }
            }
        }

        // Pasamos los datos actuales a la vista
        $especialidad = [
            'id' => $this->model->id,
            'nombre' => $this->model->nombre
        ];

        $this->loadView('especialidad/edit', ['especialidad' => $especialidad]);
    }

    public function delete($id)
    {
         $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Especialidad eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar Especialidad";
        }

        $this->redirect('index.php?controller=Especialidad&action=index');
    }

        public function reporteEspecialidadesGerencial() {
        // 1. Si el usuario solicita descargar el PDF Gerencial
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

            $database = new Database();
            $db = $database->getConnection();
            
            // Consulta para agrupar y contar citas según la especialidad del médico
            $query = "SELECT esp.nombre AS especialidad_nombre, COUNT(c.id) AS total_solicitudes
                      FROM especialidad esp
                      INNER JOIN especialista e ON esp.id = e.especialidad_id
                      INNER JOIN cita c ON e.id = c.especialista_id
                      GROUP BY esp.id, esp.nombre
                      ORDER BY total_solicitudes DESC";
            $stmt = $db->prepare($query);
            $stmt->execute();

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Reporte Gerencial Directivo'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Ranking Estratégico de Especialidades Médicas Más Demandadas"), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(232, 232, 232);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 8, 'Posicion', 1, 0, 'C', true);
            $pdf->Cell(110, 8, utf8_decode('Especialidad Médica'), 1, 0, 'L', true);
            $pdf->Cell(50, 8, 'Total Consultas', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 10);
            $ranking = 1;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdf->Cell(30, 7, $ranking . '°', 1, 0, 'C');
                $pdf->Cell(110, 7, utf8_decode($row['especialidad_nombre']), 1, 0, 'L');
                $pdf->Cell(50, 7, $row['total_solicitudes'], 1, 1, 'C');
                $ranking++;
            }

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Ranking_Gerencial_Especialidades.pdf");
            exit;
        }

        // 2. Carga normal en pantalla HTML
        $title = "Ranking de Especialidades"; 
        require_once dirname(__DIR__) . "/config/database.php"; 
        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/especialidad/reporte_ranking.php"; 
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }

}
