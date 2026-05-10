<?php
require_once 'Controller.php';

class paciente_antecedenteController extends Controller {
    private $db;
    public function __construct($model) { 
         parent::__construct($model);
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function ver($id = null) {
        // Aseguramos capturar el ID del paciente desde la URL o el POST
        if (!$id) $id = $_GET['id'] ?? $_POST['paciente_id'] ?? null;

        if (!$id) {
            $this->redirect('index.php?controller=paciente&action=index');
            return;
        }

        // Cargamos los antecedentes actuales
        $antecedentes = $this->model->readByPaciente($id);

        // Para el formulario de "añadir", necesitamos los tipos de antecedentes
        require_once '../models/tipo_antecedente.php';
        $taModel = new tipo_antecedenteModel($this->db);
        $tipos = $taModel->read();

        $this->loadView('paciente_antecedente/ver', [
            'paciente_id' => $id,
            'antecedentes' => $antecedentes,
            'tipos' => $tipos
        ]);
    }

    public function store() {
        if ($_POST) {
            $data = [
                ':paciente_id' => $_POST['paciente_id'],
                ':tipo_id'     => $_POST['tipo_antecedente_id'],
                ':descripcion' => $_POST['descripcion'],
                ':fecha'       => $_POST['fecha']
            ];

            if ($this->model->create($data)) {
                $_SESSION['success'] = "Antecedente registrado";
            } else {
                $_SESSION['error'] = "Error al registrar antecedente";
            }
            $this->redirect('index.php?controller=paciente_antecedente&action=ver&id=' . $_POST['paciente_id']);
        }
    }

    public function delete($id = null) {
        if (!$id) $id = $_GET['id'] ?? null;
        $paciente_id = $_GET['paciente_id'] ?? null;
        if ($id) {
            if ($this->model->delete($id)) {
                $_SESSION['success'] = "Antecedente eliminado correctamente";
            } else {
                $_SESSION['error'] = "No se pudo eliminar el antecedente";
            }
        }
        $this->redirect("index.php?controller=paciente&action=index");
        
    }
}