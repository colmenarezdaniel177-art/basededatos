<?php
require_once 'Controller.php';

class MedicamentoController extends Controller
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        
        $medicamentos = $this->model->read();        
        $this->loadView('medicamento/index', ['medicamentos' => $medicamentos]);
    }

    public function create()
    {
        if ($_POST) {
            // Asignamos el nombre enviado por el formulario
            $this->model->nombre = $_POST['nombre'];

            // Verificamos si ya existe un Medicamento con ese nombre
            if ($this->model->medicamentoExists($this->model->nombre)) {
                $_SESSION['error'] = "El Medicamento ya existe";
                $this->loadView('medicamento/create');
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Medicamento creado exitosamente";
                $this->redirect('index.php?controller=medicamento&action=index');
            } else {
                $_SESSION['error'] = "Error al crear el Medicamento";
                $this->loadView('medicamento/create');
            }
            return;
        }
        $this->loadView('medicamento/create');
    }

    public function edit($id)
    {
    if (!$id) {
        $this->redirect('index.php?controller=medicamento&action=index');
        return;
    }

        $this->model->id = $id;
        $existe = $this->model->readOne();

        if (!$existe) {
            $_SESSION['error'] = "Medicamento no encontrado";
            $this->redirect('index.php?controller=medicamento&action=index');
            return;
        }

        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];

            // Validar que el nuevo nombre no lo tenga otro Medicamento
            if ($this->model->medicamentoExists($this->model->nombre, $id)) {
                $_SESSION['error'] = "Ya existe otro Medicamento con ese nombre";
            } else {
                if ($this->model->update()) {
                    $_SESSION['success'] = "Medicamento actualizado exitosamente";
                    $this->redirect('index.php?controller=medicamento&action=index');
                    return;
                } else {
                    $_SESSION['error'] = "Error al actualizar";
                }
            }
        }

        // Pasamos los datos actuales a la vista
        $medicamento = [
            'id' => $this->model->id,
            'nombre' => $this->model->nombre
        ];

        $this->loadView('medicamento/edit', ['medicamento' => $medicamento]);
    }

    public function delete($id)
    {
         $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Medicamento eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar Medicamento";
        }

        $this->redirect('index.php?controller=medicamento&action=index');
    }


}
