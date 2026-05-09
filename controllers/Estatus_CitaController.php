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
}
