<?php
session_start();
define('BASE_PATH', dirname(__DIR__));
// Configuración
require_once '../config/database.php';

// Cargar modelos
function loadModel($modelName) {
    $modelFile = "../models/{$modelName}.php";
    if (file_exists($modelFile)) {
        require_once $modelFile;
        $database = new Database();
        $db = $database->getConnection();
        $modelClass = $modelName . 'Model';
        return new $modelClass($db);
    }
    return null;
}


// Cargar controlador
function loadController($controllerName) {
    $controllerFile = "../controllers/{$controllerName}Controller.php";
    
    if (!file_exists($controllerFile)) {
        throw new Exception("Controlador no encontrado: $controllerName");
    }    
    require_once $controllerFile;
    $controllersWithoutModel = ['dashboard'];
    
    

    if ($controllerName === 'dashboard') {
        $controllerClass = 'DashboardController';
        return new $controllerClass();
    }       
    $model = loadModel($controllerName);
    if (!$model) {
        throw new Exception("Modelo no encontrado para: $controllerName");
    }
    
    $controllerClass = $controllerName . 'Controller';
    return new $controllerClass($model);
}

// Routing
$controller = $_GET['controller'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Lista de controladores permitidos
$allowedControllers = [
    'rol','usuario','dashboard','test','especialista','paciente','cita','especialidad','Estatus_Cita','Tipo_Antecedente','Medicamento'  
];

if (!in_array($controller, $allowedControllers)) {
    $controller = 'dashboard';
}

try {
    $controllerInstance = loadController($controller);
    
    if (method_exists($controllerInstance, $action)) {
        // Pasar parámetros si existen
        $id = $_GET['id'] ?? null;
        if ($id) {
            $controllerInstance->$action($id);
        } else {
            $controllerInstance->$action();
        }
    } else {
        throw new Exception("Acción no encontrada");
    }
} catch (Exception $e) {
    // Página de error
    http_response_code(404);
    echo "Página no encontrada: " . $e->getMessage();
}
?>