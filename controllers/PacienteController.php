<?php
require_once 'Controller.php';

class PacienteController extends Controller
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        $pacientes= $this->model->read();
        $this->loadView('paciente/index', ['pacientes' => $pacientes]);
    }
    public function index2()
    {
       $pacientes= $this->model->read();
        $this->loadView('paciente/index2', ['pacientes' => $pacientes]);
    }

    public function create()
    {
        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];
            $this->model->cedula = $_POST['cedula'];
            $this->model->fecha_nacimiento = $_POST['fecha_nacimiento'];  
            
                $this->model->usuario_id = $_SESSION['user']['id'];           

            if ($this->model->cedulaExists($this->model->cedula)) {
                $_SESSION['error'] = "El paciente ya existe";
                $this->loadView('paciente/create');
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Paciente creado exitosamente";
                $this->redirect('index.php?controller=paciente&action=index');
            } else {
                $_SESSION['error'] = "Error al crear paciente";
                $this->loadView('paciente/create');
            }
            return;
        }
        $this->loadView('paciente/create');
    }

    public function create2()
    {
        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];
            $this->model->cedula = $_POST['cedula'];
            $this->model->fecha_nacimiento = $_POST['fecha_nacimiento'];  
            if ($_SESSION['user']['rol_nombre'] =='Invitado'){
                $this->model->usuario_id = $_SESSION['user']['id'];
            }else{
                $this->model->usuario_id = 0;
            }            

            if ($this->model->cedulaExists($this->model->cedula)) {
                $_SESSION['error'] = "El paciente ya existe";
                $this->loadView('paciente/create2');
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Paciente creado exitosamente";
                $this->redirect('index.php?controller=paciente&action=index2');
            } else {
                $_SESSION['error'] = "Error al crear paciente";
                $this->loadView('paciente/create2');
            }
            return;
        }
        $this->loadView('paciente/create2');
    }


    public function edit($id)
    {
        $this->model->id = $id;
        $Existe = $this->model->readOne();
        if (!$Existe) {
            $_SESSION['error'] = "Registro no encontrado";
            $this->redirect('index.php?controller=paciente&action=index');
            return;
        }

        if ($_POST) {
            $this->model->id = $id;
            $this->model->nombre = $_POST['nombre'];
            $this->model->cedula = $_POST['cedula'];
            $this->model->fecha_nacimiento = $_POST['fecha_nacimiento'];
            if ($this->model->cedulaExists($this->model->cedula,$this->model->id)) {
                $_SESSION['error'] = "Ya existe otro registro con esas caracteristicas";
                $this->loadView('paciente/edit', ['paciente' => ['id' => $id, 'nombre' => $this->model->nombre, 'cedula' => $this->model->cedula, 'fecha_nacimiento' => $this->model->fecha_nacimiento]]);
                return;
            }

            if ($this->model->update()) {
                $_SESSION['success'] = "Paciente actualizado exitosamente";
                $this->redirect('index.php?controller=paciente&action=index');
            } else {
                $_SESSION['error'] = "Error al actualizar Paciente";
            }
        }
        $paciente = [
            'id' => $this->model->id,
            'nombre' => $this->model->nombre,
            'cedula' => $this->model->cedula,
            'fecha_nacimiento' => $this->model->fecha_nacimiento,
        ];

        $this->loadView('paciente/edit', ['paciente' => $paciente]);
    }

    public function delete($id)
    {
        $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Paciente eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar Paciente";
        }

        $this->redirect('index.php?controller=paciente&action=index');
    }

        public function reporteCrecimientoGerencial() {
        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

        // 1. Si se solicita descargar el PDF Gerencial
        if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
            require_once dirname(__DIR__) . "/config/database.php"; 
            require_once dirname(__DIR__) . "/public/fpdf/fpdf.php";

            $database = new Database();
            $db = $database->getConnection();
            
            // Consulta para contar pacientes registrados agrupados por mes en base al ID o fecha del sistema
           $query = "SELECT MONTH(fecha_registro) AS mes_num, COUNT(*) AS total_nuevos 
          FROM paciente 
          GROUP BY MONTH(fecha_registro)
          ORDER BY mes_num ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();



            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            $pdf->Cell(0, 10, utf8_decode('Ozono Vital - Reporte Gerencial Directivo'), 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, utf8_decode("Tasa de Crecimiento y Registro de Pacientes Nuevos"), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFillColor(232, 232, 232);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(60, 8, 'Mes de Analisis', 1, 0, 'C', true);
            $pdf->Cell(130, 8, 'Cantidad de Pacientes Registrados', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 10);
            $meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Validación para evitar índices fuera de rango si la consulta retorna valores extraños
                $mes_idx = ($row['mes_num'] >= 1 && $row['mes_num'] <= 12) ? $row['mes_num'] : 1;
                $nombre_mes = $meses[$mes_idx];
                $pdf->Cell(60, 7, $nombre_mes, 1, 0, 'C');
                $pdf->Cell(130, 7, $row['total_nuevos'], 1, 1, 'C');
            }

            if (ob_get_contents()) ob_end_clean();
            $pdf->Output('I', "Reporte_Gerencial_Pacientes_{$anio}.pdf");
            exit;
        }

        // 2. Carga normal en pantalla HTML
        $title = "Crecimiento de Pacientes"; 
        require_once dirname(__DIR__) . "/config/database.php"; 
        $database = new Database();
        $db = $database->getConnection();
        
        include_once dirname(__DIR__) . "/views/layouts/header.php";
        include_once dirname(__DIR__) . "/views/paciente/reporte_crecimiento.php"; 
        include_once dirname(__DIR__) . "/views/layouts/footer.php";
    }

}
