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
}
