<?php
require_once 'Controller.php';

class DashboardController extends Controller {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
           try {
        // Cargar múltiples modelos para estadísticas
        
        require_once '../models/Usuario.php';
        require_once '../models/Paciente.php';
        require_once '../models/Cita.php';
        //require_once '../models/ClienteModel.php';
        //require_once '../models/FacturaModel.php';
        
        $usuarioModel = new UsuarioModel($this->db);
        $pacienteModel = new PacienteModel($this->db);
        $citaModel = new CitaModel($this->db);
        //$clienteModel = new ClienteModel($this->db);
        //$facturaModel = new FacturaModel($this->db);
        
        $stats = [
            //'total_usuarios' => $usuarioModel->getAll()->rowCount(),
            //'total_pacientes' => $pacienteModel->getAll()->rowCount(),
            //'total_clientes' => $clienteModel->getAll()->rowCount(),
            //'total_citas' => $citaModel->getAll()->rowCount(),
            //'total_facturas' => $facturaModel->getAll()->rowCount()

              'total_usuarios' => $usuarioModel->read()->rowCount(),
            'total_pacientes' => $pacienteModel->read()->rowCount(),
            'total_clientes' => 90,
            'total_citas' => $citaModel->read()->rowCount(),
            'total_facturas' => 200
        ];
        
        $this->loadView('dashboard/index', $stats);
         } catch (Exception $e) {
            // Vista de fallback si hay error
            echo "<h1>Dashboard</h1>";
            echo "<p>Error cargando estadísticas: " . $e->getMessage() . "</p>";
            echo "<a href='index.php?controller=usuario&action=index'>Ir a Usuarios</a>";
        }
    }
    
}
?>