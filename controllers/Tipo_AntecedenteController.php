<?php
require_once 'Controller.php';

class Tipo_AntecedenteController extends Controller
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        
        $tipo_antecedentes = $this->model->read();        
        $this->loadView('tipo_antecedente/index', ['tipo_antecedentes' => $tipo_antecedentes]);
    }

    public function create()
    {
        if ($_POST) {
            // Asignamos el nombre enviado por el formulario
            $this->model->nombre = $_POST['nombre'];

            // Verificamos si ya existe un Tipo_Antecedente con ese nombre
            if ($this->model->Tipo_AntecedenteExists($this->model->nombre)) {
                $_SESSION['error'] = "El Tipo_Antecedente ya existe";
                $this->loadView('tipo_antecedente/create');
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Tipo_Antecedente creado exitosamente";
                $this->redirect('index.php?controller=tipo_antecedente&action=index');
            } else {
                $_SESSION['error'] = "Error al crear el Tipo_Antecedente";
                $this->loadView('tipo_antecedente/create');
            }
            return;
        }
        $this->loadView('tipo_antecedente/create');
    }

    public function edit($id)
    {
    if (!$id) {
        $this->redirect('index.php?controller=tipo_antecedente&action=index');
        return;
    }

        $this->model->id = $id;
        $existe = $this->model->readOne();

        if (!$existe) {
            $_SESSION['error'] = "Tipo_Antecedente no encontrado";
            $this->redirect('index.php?controller=tipo_antecedente&action=index');
            return;
        }

        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];

            // Validar que el nuevo nombre no lo tenga otro Tipo_Antecedente
            if ($this->model->Tipo_AntecedenteExists($this->model->nombre, $id)) {
                $_SESSION['error'] = "Ya existe otro Tipo_Antecedente con ese nombre";
            } else {
                if ($this->model->update()) {
                    $_SESSION['success'] = "Tipo_Antecedente actualizado exitosamente";
                    $this->redirect('index.php?controller=tipo_antecedente&action=index');
                    return;
                } else {
                    $_SESSION['error'] = "Error al actualizar";
                }
            }
        }

        // Pasamos los datos actuales a la vista
        $tipo_antecedente = [
            'id' => $this->model->id,
            'nombre' => $this->model->nombre
        ];

        $this->loadView('tipo_antecedente/edit', ['tipo_antecedente' => $tipo_antecedente]);
    }

    public function delete($id)
    {
         $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Tipo_Antecedente eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar Tipo_Antecedente";
        }

        $this->redirect('index.php?controller=tipo_antecedente&action=index');
    }

    public function reportePorAntecedente() {
        // 1. Si se solicita descargar el PDF
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

            $database = new Database();
            $db = $database->getConnection();
            
            // Consulta directa para el archivo PDF
            $query = "SELECT ta.id, ta.nombre AS antecedente_nombre, COUNT(DISTINCT am.paciente_id) AS total_pacientes
                      FROM tipo_antecedente ta
                      LEFT JOIN antecedentes_medicos am ON ta.id = am.tipo_antecedente_id
                      GROUP BY ta.id, ta.nombre
                      ORDER BY total_pacientes DESC";
            $stmt = $db->prepare($query);
            $stmt->execute();

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Reporte de Supervisión'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Distribución de Carga Clínica por Tipo de Antecedente"), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(232, 232, 232);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(30, 8, 'ID Tipo', 1, 0, 'C', true);
            $pdf->Cell(110, 8, utf8_decode('Categoría de Antecedente Médico'), 1, 0, 'L', true);
            $pdf->Cell(50, 8, 'Pacientes Registrados', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 10);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $pdf->Cell(30, 7, $row['id'], 1, 0, 'C');
                $pdf->Cell(110, 7, utf8_decode($row['antecedente_nombre']), 1, 0, 'L');
                $pdf->Cell(50, 7, $row['total_pacientes'], 1, 1, 'C');
            }

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Pacientes_Por_Antecedente.pdf");
            exit;
        }

        // 2. Carga normal en pantalla HTML
        $title = "Carga por Antecedentes"; 
        require_once dirname(__DIR__) . "/config/database.php"; 

        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/Tipo_Antecedente/reporte_carga.php"; 
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }



}
