<?php
class Controller {
    protected $model;
    protected $view;

    public function __construct($model) {
        $this->model = $model;
    }    

    protected function loadView($view, $data = []) {
        extract($data);
         $viewPath = "../views/$view.php";
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vista no encontrada: $viewPath");
        }
        
        require_once $viewPath;        
    }

    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
}
?>