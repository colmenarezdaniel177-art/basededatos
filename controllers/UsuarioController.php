<?php
require_once 'Controller.php';

class UsuarioController extends Controller {
    private $rolModel;
    public function __construct($model) {
        parent::__construct($model);
        require_once '../config/database.php';
        require_once '../models/rol.php';
        $database = new Database();
        $db = $database->getConnection();
        $this->rolModel = new RolModel($db);
    }

    public function index() {
        $usuarios = $this->model->read();
        $roles = $this->rolModel->read();
        $rolNombres = [];
        foreach ($roles as $rol) {
            $rolNombres[$rol['id']] = $rol['nombre'];
        }
        error_log("Roles cargados: " . print_r($roles, true));
        $this->loadView('usuario/index', ['usuarios' => $usuarios , 'rolNombres' => $rolNombres]);
    }

    public function create() {
        $roles = $this->rolModel->read();
        error_log("Estructura de roles: " . print_r($roles, true));
        if ($_POST) {
            $this->model->login = $_POST['login'];
            $this->model->password_hash = password_hash($_POST['password_hash'], PASSWORD_DEFAULT);
            $this->model->rol_id = $_POST['rol_id'];

            if ($this->model->loginExists($this->model->login)) {
                $_SESSION['error'] = "El usuario ya existe";
                $this->loadView('usuario/create', ['roles' => $roles]);                
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Usuario creado exitosamente";
                $this->redirect('index.php?controller=usuario&action=index');
            } else {
                $_SESSION['error'] = "Error al crear usuario";
            }
        }
        $this->loadView('usuario/create', ['roles' => $roles]);        
}

    public function edit($id) {
        $this->model->id = $id;
        $roles = $this->rolModel->read();
        $Existe = $this->model->readOne();
        if (!$Existe) {
            $_SESSION['error'] = "Registro no encontrado";
            $this->redirect('index.php?controller=usuario&action=index');
            return;
        }


        if ($_POST) {
            $this->model->id = $id;
            $this->model->login = $_POST['login'];
            $this->model->rol_id = $_POST['rol_id'];
            if ($this->model->loginExists($this->model->login,$this->model->id)) {
                $_SESSION['error'] = "Ya existe otro registro con esas caracteristicas";                
                return;
            }

            if (!empty($_POST['password_hash'])) {
                $this->model->password = password_hash($_POST['password_hash'], PASSWORD_DEFAULT);
            }

            if ($this->model->update()) {
                $_SESSION['success'] = "Usuario actualizado exitosamente";
                $this->redirect('index.php?controller=usuario&action=index');
            } else {
                $_SESSION['error'] = "Error al actualizar usuario";
            }
        }

         $usuario = [
            'id' => $this->model->id,
            'login' => $this->model->login,
            'password_hash'=> $this->model->password_hash,
            'rol_id'=> $this->model->rol_id,
        ];
       
        $this->loadView('usuario/edit', ['usuario' => $usuario, 'roles' => $roles]);
    }

    public function delete($id) {
        $this->model->id = $id;
        
        if ($this->model->delete()) {
            $_SESSION['success'] = "Usuario eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar usuario";
        }
        
        $this->redirect('index.php?controller=usuario&action=index');
    }
    
    public function register() {
        if ($_POST) {
            $this->model->login = $_POST['email']; // Usar email como login
            $this->model->password_hash = $_POST['password_hash'];
            $this->model->rol_id = $_POST['rol_id'] ?? 2; // Rol por defecto

        
            if ($this->model->loginExists($this->model->login)) {
                $_SESSION['error'] = "El usuario ya existe";
                return false;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Usuario creado exitosamente";
                return true;
            } else {
                $_SESSION['error'] = "Error al crear usuario";
                return false;
            }
        }
        return false;
    }

    public function getUserByLogin($login) {
        return $this->model->getUserByLogin($login);
    }
    public function activar($id) {
         $this->model->id = $id;
        
        if ($this->model->activar()) {
            $_SESSION['success'] = "Usuario activado exitosamente";
        } else {
            $_SESSION['error'] = "Error al activar usuario";
        }
        
        $this->redirect('index.php?controller=usuario&action=index');
    }
}
?>