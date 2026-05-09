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
}
