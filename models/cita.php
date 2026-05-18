<?php
class CitaModel
{
    private $conn;
    private $table_name = "cita";

    public $id;
    public $paciente_id;
    public $especialista_id;
    public $fecha;
    public $status_id; 
    public $nota;
    public $paciente_nombre;
    public $especialista_nombre;

    
    public $motivo_consulta;
    public $diagnostico;
    public $observaciones;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crear Cita
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET paciente_id=:paciente_id, especialista_id=:especialista_id, fecha=:fecha,status_id=:status_id,nota=:nota";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->paciente_id = htmlspecialchars(strip_tags($this->paciente_id));
        $this->especialista_id = htmlspecialchars(strip_tags($this->especialista_id));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->status_id = htmlspecialchars(strip_tags($this->status_id));
        $this->nota = htmlspecialchars(strip_tags($this->nota));

        // Vincular parámetros
        $stmt->bindParam(":paciente_id", $this->paciente_id);
        $stmt->bindParam(":especialista_id", $this->especialista_id);
        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":status_id", $this->status_id);
        $stmt->bindParam(":nota", $this->nota);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las Citas
    public function read()
    {
        $query = "SELECT c.*  ,p.nombre AS paciente_nombre, e.nombre AS especialista_nombre, s.nombre as Status_Nombre FROM " . $this->table_name . " c INNER JOIN paciente p ON c.paciente_id = p.id
        INNER JOIN especialista e ON c.especialista_id = e.id 
        INNER JOIN estatus_cita s ON c.status_id = s.id";
        if ($_SESSION['user']['rol_nombre'] == 'Invitado') {
            $query = $query . " WHERE p.usuario_id=" . $_SESSION['user']['id'];
        }
        $query = $query . " ORDER BY c.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una Cita por ID
    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . "  WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->paciente_id = $row['paciente_id'];
            $this->especialista_id = $row['especialista_id'];
            $this->fecha = $row['fecha'];
            $this->status_id = $row['status_id'];
            $this->nota = $row['nota'];
            return true;
        }
        return false;
    }

    // Actualizar Cita
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET paciente_id=:paciente_id, especialista_id=:especialista_id, fecha=:fecha ,nota=:nota,status_id=:status_id
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->paciente_id = htmlspecialchars(strip_tags($this->paciente_id));
        $this->especialista_id = htmlspecialchars(strip_tags($this->especialista_id));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->nota = htmlspecialchars(strip_tags($this->nota));
        $this->status_id = htmlspecialchars(strip_tags($this->status_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular parámetros
        $stmt->bindParam(":paciente_id", $this->paciente_id);
        $stmt->bindParam(":especialista_id", $this->especialista_id);
        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":nota", $this->nota);
        $stmt->bindParam(":status_id", $this->status_id);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Cita
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si cédula ya existe
    public function cita_Exists($paciente_id, $especialista_id, $fecha, $exclude_id = null)
    {
        if ($exclude_id === null) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE paciente_id = :paciente_id AND  especialista_id = :especialista_id AND  fecha = :fecha";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":paciente_id", $paciente_id);
            $stmt->bindParam(":especialista_id", $especialista_id);
            $stmt->bindParam(":fecha", $fecha);
        } else {
            $query = "SELECT id FROM " . $this->table_name . " WHERE paciente_id = :paciente_id AND  especialista_id = :especialista_id AND  fecha = :fecha AND id != :exclude_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":paciente_id", $paciente_id);
            $stmt->bindParam(":especialista_id", $especialista_id);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->bindParam(":exclude_id", $exclude_id);
        }

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function readOneFOrAtender()
    {
        $query = "SELECT c.*, p.nombre as paciente_nombre, e.nombre as especialista_nombre 
              FROM " . $this->table_name . " c
              INNER JOIN paciente p ON c.paciente_id = p.id
              INNER JOIN especialista e ON c.especialista_id = e.id
              WHERE c.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if ($row) {
        $this->id = $row['id'];
            $this->paciente_id = $row['paciente_id'];
            $this->especialista_id = $row['especialista_id'];
            $this->fecha = $row['fecha'];
            $this->status_id = $row['status_id'];
            $this->nota = $row['nota'];        
            $this->paciente_nombre = $row['paciente_nombre'];
            $this->especialista_nombre = $row['especialista_nombre'];
        return true;
    


        }
        return false;
    }

public function finalizarCita() {

        // 1. Insertar en historia_clinica
        $queryHistoria = "INSERT INTO consulta 
                          SET cita_id = :cita_id, 
                              motivo_consulta = :motivo_consulta, 
                              diagnostico = :diagnostico, 
                              observaciones = :observaciones";

        $stmtH = $this->conn->prepare($queryHistoria);
        
        $stmtH->bindParam(':cita_id', $this->id);
        $stmtH->bindParam(':motivo_consulta', $this->motivo_consulta);
        $stmtH->bindParam(':diagnostico', $this->diagnostico);
        $stmtH->bindParam(':observaciones', $this->observaciones);
        
        $stmtH->execute();

        $queryCita = "UPDATE " . $this->table_name . " 
                      SET status_id = :status_id 
                      WHERE id = :id";

        $stmtC = $this->conn->prepare($queryCita);
        
     // O el valor exacto de tu ENUM
        $stmtC->bindParam(':status_id', $this->status_id);
        $stmtC->bindParam(':id', $this->id);
        
        $stmtC->execute();
        return true;


    }


    public function consultarCitasPorRango($inicio, $fin) {
        $query = "SELECT c.id, p.nombre AS paciente_nombre, e.nombre AS especialista_nombre, c.fecha 
                  FROM " . $this->table_name . " c
                  INNER JOIN paciente p ON c.paciente_id = p.id
                  INNER JOIN especialista e ON c.especialista_id = e.id
                  WHERE c.fecha BETWEEN :inicio AND :fin
                  ORDER BY c.fecha ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":inicio", $inicio);
        $stmt->bindParam(":fin", $fin);
        $stmt->execute();
        
        return $stmt; // Retorna el objeto Statement para recorrer los registros con fetch()
    }

        public function consultarAgendaPorEspecialista($especialista_id, $inicio, $fin) {
        $query = "SELECT c.id, p.nombre AS paciente_nombre, c.fecha, sc.nombre, c.nota 
                  FROM " . $this->table_name . " c
                  INNER JOIN paciente p ON c.paciente_id = p.id
                  INNER JOIN estatus_cita sc ON c.status_id = sc.id 
                  WHERE c.especialista_id = :especialista_id 
                    AND c.fecha BETWEEN :inicio AND :fin
                  ORDER BY c.fecha ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":especialista_id", $especialista_id, PDO::PARAM_INT);
        $stmt->bindParam(":inicio", $inicio);
        $stmt->bindParam(":fin", $fin);
        $stmt->execute();
        
        return $stmt;
    }

    public function consultarCitasCanceladas($inicio, $fin) {
        $query = "SELECT c.id, p.nombre AS paciente_nombre, e.nombre AS especialista_nombre, c.fecha, sc.nombre AS status , c.nota 
                  FROM " . $this->table_name . " c
                  INNER JOIN paciente p ON c.paciente_id = p.id
                  INNER JOIN especialista e ON c.especialista_id = e.id
                  INNER JOIN estatus_cita sc ON c.status_id = sc.id
                  WHERE c.fecha BETWEEN :inicio AND :fin 
                    AND sc.nombre IN ('Cancelada', 'Ausente', 'No Asistio')
                  ORDER BY c.fecha ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":inicio", $inicio);
        $stmt->bindParam(":fin", $fin);
        $stmt->execute();
        
        return $stmt;
    }



}
