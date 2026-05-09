<?php
require_once '../controllers/UsuarioController.php';
require_once '../models/Usuario.php';
require_once '../controllers/RolController.php';
require_once '../models/rol.php';
require_once '../config/database.php';

session_start();

// Inicializar arreglo de usuarios
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [];
}

// Obtener usuario por email

function get_user($email) {
    $database = new Database();
    $db = $database->getConnection();
    $usuarioModel = new UsuarioModel($db);
    $usuarioController = new UsuarioController($usuarioModel);
    $user = $usuarioController->getUserByLogin($email);
    return $user;
}
        

// Registrar usuario
function register_user($name, $email, $password) {
    $database = new Database();
    $db = $database->getConnection();
    $usuarioModel = new UsuarioModel($db);
    $usuarioController = new UsuarioController($usuarioModel);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $_POST['login'] = $email;
    $_POST['password_hash'] = $passwordHash;
    $rolModel = new RolModel($db);
    $rolController = new RolController($rolModel);
    $_POST['rol_id'] = $rolController->GetPorNombre('Invitado');    
    $usuarioController->register();
    return true;
}
?>
