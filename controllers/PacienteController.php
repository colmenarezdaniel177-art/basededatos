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
}
