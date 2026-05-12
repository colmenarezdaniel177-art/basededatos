<?php
require_once 'Controller.php';

class CitaController extends Controller
{
    private $pacienteModel;
    private $especialistaModel;
    private $horarioModel;
    public function __construct($model)
    {
        parent::__construct($model);
        require_once '../config/database.php';
        require_once '../models/Paciente.php';
        require_once '../models/Especialista.php';
        require_once '../models/horario.php';
        $database = new Database();
        $db = $database->getConnection();
        $this->pacienteModel = new PacienteModel($db);
        $this->especialistaModel = new EspecialistaModel($db);
        $this->horarioModel = new HorarioModel($db);
    }

    public function index()
    {
        $citas = $this->model->read();
        $this->loadView('cita/index', ['citas' => $citas]);
    }
    public function index2()
    {
        $citas = $this->model->read();
        $this->loadView('cita/index2', ['citas' => $citas]);
    }

    public function create()
    {
        if ($_POST) {



            $this->model->paciente_id = $_POST['paciente_id'];
            $this->model->especialista_id = $_POST['especialista_id'];
            $this->model->fecha = $_POST['fecha'];
            $this->model->status = 1;
            $this->model->nota = $_POST['nota'];


            $fecha = new DateTime($this->model->fecha);
            $diascita = $fecha->format('N');

            // 2. Obtener la lista de horarios (es un array)
            $horariosDisponibles = $this->horarioModel->readHorarios($this->model->especialista_id);

            $atencionEncontrada = false;
            foreach ($horariosDisponibles as $horario) {
                if ((int)$diascita === (int)$horario->dia_semana) {
                    $atencionEncontrada = true;
                    break;
                }
            }

            if (!$atencionEncontrada) {
                $nombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                $nombreDia = $nombres[$diascita] ?? 'desconocido';
                $_SESSION['error'] = "El especialista no atiende los días $nombreDia.";
                $this->loadView('cita/create', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
                return;
            }



            if ($this->model->cita_Exists($this->model->paciente_id, $this->model->especialista_id, $this->model->fecha)) {
                $_SESSION['error'] = "La Cita ya existe";
                $this->loadView('cita/create', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Cita creada exitosamente";
                $this->redirect('index.php?controller=cita&action=index');
            } else {
                $_SESSION['error'] = "Error al crear cita";
                $this->loadView('cita/create', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
            }
            return;
        }
        $this->loadView('cita/create', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
    }

    public function create2()
    {
        if ($_POST) {
            $this->model->paciente_id = $_POST['paciente_id'];
            $this->model->especialista_id = $_POST['especialista_id'];
            $this->model->fecha = $_POST['fecha'];
            $this->model->status = 1;
            $this->model->nota = $_POST['nota'];

            if ($this->model->cita_Exists($this->model->paciente_id, $this->model->especialista_id, $this->model->fecha)) {
                $_SESSION['error'] = "La Cita ya existe";
                $this->loadView('cita/create2', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
                return;
            }

            if ($this->model->create()) {
                $_SESSION['success'] = "Cita creada exitosamente";
                $this->redirect('index.php?controller=cita&action=index2');
            } else {
                $_SESSION['error'] = "Error al crear cita";
                $this->loadView('cita/create2', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
            }
            return;
        }
        $this->loadView('cita/create2', ['especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes()]);
    }

    public function edit($id)
    {
        $this->model->id = $id;
        $Existe = $this->model->readOne();
        if (!$Existe) {
            $_SESSION['error'] = "Registro no encontrado";
            $this->redirect('index.php?controller=cita&action=index');
            return;
        }

        if ($_POST) {
            $this->model->paciente_id = $_POST['paciente_id'];
            $this->model->especialista_id = $_POST['especialista_id'];
            $this->model->fecha = $_POST['fecha'];
            $this->model->status = $_POST['status'];
            $this->model->nota = $_POST['nota'];
            if ($this->model->cita_Exists($this->model->paciente_id, $this->model->especialista_id, $this->model->fecha, $this->model->id)) {
                $_SESSION['error'] = "Ya existe otro registro con esas caracteristicas";
                $this->loadView('cita/edit', ['cita' => ['id' => $id, 'paciente_id' => $this->model->paciente_id, 'especialista_id' => $this->model->especialista_id, 'fecha' => $this->model->fecha, 'status' => $this->model->status, 'nota' => $this->model->nota], 'especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes(), 'listStatus' => $this->GetStatus()]);
                return;
            }

            if ($this->model->update()) {
                $_SESSION['success'] = "Cita actualizada exitosamente";
                $this->redirect('index.php?controller=cita&action=index');
            } else {
                $_SESSION['error'] = "Error al actualizar especialista";
            }
        }
        $cita = [
            'id' => $this->model->id,
            'paciente_id' => $this->model->paciente_id,
            'especialista_id' => $this->model->especialista_id,
            'fecha' => $this->model->fecha,
            'status' => $this->model->status,
            'nota' => $this->model->nota,
        ];
        $this->loadView('cita/edit', ['cita' => $cita, 'especialistas' => $this->GetEspecialistas(), 'pacientes' => $this->GetPacientes(), 'listStatus' => $this->GetStatus()]);
    }

    public function delete($id)
    {
        $this->model->id = $id;

        if ($this->model->delete()) {
            $_SESSION['success'] = "Cita eliminado exitosamente";
        } else {
            $_SESSION['error'] = "Error al eliminar Cita";
        }

        $this->redirect('index.php?controller=cita&action=index');
    }

    public function cancelar($id)
    {
        $this->model->id = $id;
        $this->model->readOne();
        $this->model->status = 'Cancelada';

        if ($this->model->update()) {
            $_SESSION['success'] = "Cita cancelada exitosamente";
        } else {
            $_SESSION['error'] = "Error al cancelar Cita";
        }

        $this->redirect('index.php?controller=cita&action=index2');
    }

    public function atender($id)
    {
        $this->model->id = $id;
        $existe = $this->model->readOneFOrAtender();

        if (!$existe) {
            $_SESSION['error'] = "Cita no encontrada";
            $this->redirect('index.php?controller=cita&action=index');
            return;
        }
        $cita = [
            'id' => $this->model->id,
            'paciente_nombre' => $this->model->paciente_nombre,
            'especialista_nombre' => $this->model->especialista_nombre,
            'fecha' => $this->model->fecha
        ];


        $this->loadView('cita/atender', ['cita' => $cita]);
    }


    public function GetEspecialistas()
    {
        return $this->especialistaModel->read();
    }
    public function GetPacientes()
    {
        return $this->pacienteModel->read();
    }

    public function GetStatus()
    {
        return [
            'Programada',
            'Confirmada',
            'En Espera',
            'En Consulta',
            'Completada',
            'Cancelada',
            'No Asistió',
            'Reprogramada'
        ];
    }

    public function guardarConsulta()
    {
        if ($_POST) {
            // Asignamos los valores recibidos del formulario al modelo
            $this->model->id = $_POST['cita_id'];
            $this->model->motivo_consulta = $_POST['motivo_consulta'];
            $this->model->tratamiento = $_POST['tratamiento'];
            $this->model->observaciones = $_POST['observaciones'];

            // Cambiamos el status a uno de los valores de tu ENUM
            $this->model->status = 'Completada';

            if ($this->model->finalizarCita()) {
                $_SESSION['success'] = "Consulta finalizada y guardada correctamente.";
                $this->redirect('index.php?controller=cita&action=index');
            } else {
                $_SESSION['error'] = "Error al intentar guardar los detalles de la consulta.";
                $this->redirect('index.php?controller=cita&action=index');
            }
        }
    }
}
