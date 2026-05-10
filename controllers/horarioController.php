<?php
require_once 'Controller.php';

class HorarioController extends Controller {
    
    public function index() {
        $this->redirect('index.php?controller=especialista&action=index');
    }

    public function gestionar($id = null) {
        if (!$id) $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('index.php?controller=especialista&action=index');
            return;
        }


        $stmt = $this->model->readByEspecialista($id);
        $horariosActuales = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $horariosActuales[$row['dia_semana']] = $row;
        }

        if ($_POST) {
            $especialistaId = $_POST['especialistaId'];
            $dias = $_POST['dias']; // Array proveniente del formulario

            if ($this->model->saveSchedule($especialistaId, $dias)) {
                $_SESSION['success'] = "Horario actualizado correctamente";
                $this->redirect("index.php?controller=horario&action=gestionar&id=$especialistaId");
                return;
            } else {
                $_SESSION['error'] = "Error al guardar el horario";
            }
        }

        $this->loadView('horario/gestionar', [
            'especialistaId' => $id,
            'horarios' => $horariosActuales,
            'dias_nombres' => [
                1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 
                4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 0 => 'Doming'
            ]
        ]);
    }
}