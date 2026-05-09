<?php
require_once 'Controller.php';

class EspecialistaController extends Controller
{
    public function __construct($model)
    {
        parent::__construct($model);
    }

    public function index()
    {
        $especialistas = $this->model->read();
        $this->loadView('especialista/index', ['especialistas' => $especialistas]);
    }

    public function create()
    {
        if ($_POST) {
            $this->model->nombre = $_POST['nombre'];
            $this->model->especialidad_id = $_POST['especialidad_id'];

            if ($this->model->especialistaExists($this->model->nombre)) {
                $_SESSION['error'] = "El especialista ya existe";
                $this->loadView('especialista/create', ['especialidades' => $this->GetEspecialidades()]);
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Especialista creado exitosamente";
                $this->redirect('index.php?controller=especialista&action=index');
            } else {
                $_SESSION['error'] = "Error al crear especialista";
                $this->loadView('especialista/create', ['especialidades' => $this->GetEspecialidades()]);
            }
            return;
        }
        $this->loadView('especialista/create', ['especialidades' => $this->GetEspecialidades()]);
    }

    public function edit($id)
    {
        $this->model->id = $id;
        $Existe = $this->model->readOne();
        if (!$Existe) {
            $_SESSION['error'] = "Registro no encontrado";
            $this->redirect('index.php?controller=especialista&action=index');
            return;
        }

        if ($_POST) {
            $this->model->id = $id;
            $this->model->nombre = $_POST['nombre'];
            $this->model->especialidad_id = $_POST['especialidad_id'];
            if ($this->model->especialistaExists($this->model->nombre, $this->model->id)) {
                $_SESSION['error'] = "Ya existe otro registro con esas caracteristicas";
                $this->loadView('especialista/edit', ['especialista' => ['id' => $id, 'nombre' => $this->model->nombre, 'especialidad' => $this->model->especialidad]]);
                return;
            }

            if ($this->model->update()) {
                $_SESSION['success'] = "Especialista actualizado exitosamente";
                $this->redirect('index.php?controller=especialista&action=index');
            } else {
                $_SESSION['error'] = "Error al actualizar especialista";
            }
        }
        $especialista = [
            'id' => $this->model->id,
            'nombre' => $this->model->nombre,
            'especialidad_id' => $this->model->especialidad_id,
        ];

        $this->loadView('especialista/edit', ['especialista' => $especialista, 'especialidades' => $this->GetEspecialidades()]);
    }

    public function delete($id)
    {
        $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Especialista eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar especialista";
        }

        $this->redirect('index.php?controller=especialista&action=index');
    }


    public function GetEspecialidades()
    {
        return [
            'Medicina Interna',
            'Psicología',
            'Conducta'
        ];
    }
}
