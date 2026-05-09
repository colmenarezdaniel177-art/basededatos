<?php
require_once 'Controller.php';

class RolController extends Controller
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        $roles = $this->model->read();
        $this->loadView('rol/index', ['roles' => $roles]);
    }

    public function create()
    {
        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];

            if ($this->model->rolExists($this->model->nombre)) {
                $_SESSION['error'] = "El rol ya existe";
                $this->loadView('rol/create');
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Rol creado exitosamente";
                $this->redirect('index.php?controller=rol&action=index');
            } else {
                $_SESSION['error'] = "Error al crear rol";
            }
            return;
        }
        $this->loadView('rol/create');
    }

    public function edit($id)
    {
        $this->model->id = $id;
        $Existe = $this->model->readOne();
        if (!$Existe) {
            $_SESSION['error'] = "Registro no encontrado";
            $this->redirect('index.php?controller=rol&action=index');
            return;
        }

        if ($_POST) {
            $this->model->id = $id;
            $this->model->nombre = $_POST['nombre'];
            if ($this->model->rolExists($this->model->nombre,$this->model->id)) {
                $_SESSION['error'] = "Ya existe otro registro con esas caracteristicas";
                $this->loadView('rol/edit', ['rol' => ['id' => $id, 'nombre' => $this->model->nombre]]);
                return;
            }

            if ($this->model->update()) {
                $_SESSION['success'] = "Rol actualizado exitosamente";
                $this->redirect('index.php?controller=rol&action=index');
            } else {
                $_SESSION['error'] = "Error al actualizar rol";
            }
        }
        $rol = [
            'id' => $this->model->id,
            'nombre' => $this->model->nombre
        ];

        $this->loadView('rol/edit', ['rol' => $rol]);
    }

    public function delete($id)
    {
        $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Rol eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar rol";
        }

        $this->redirect('index.php?controller=rol&action=index');
    }

    public function GetPorNombre($Nombre)
    {
        $this->model->Nombre = $Nombre;

        if ($this->model->GetPorNombre($Nombre)) {
            return $this->model->id;
        }
        return 0;
    }
}
