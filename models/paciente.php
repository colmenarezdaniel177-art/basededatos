<?php
class PacienteModel
{
    private $conn;
    private $table_name = "paciente";

    public $id;
    public $nombre;
    public $cedula;
    public $fecha_nacimiento;
    public $usuario_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crear Paciente
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nombre=:nombre, cedula=:cedula, fecha_nacimiento=:fecha_nacimiento, usuario_id=:usuario_id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));        
        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":cedula", $this->cedula);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":usuario_id", $this->usuario_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las Pacientes
    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name ;
        if ($_SESSION['user']['rol_nombre'] == 'Invitado') {
            $query = $query . " WHERE usuario_id=" . $_SESSION['user']['id'];
        }
        $query = $query . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una Paciente por ID
    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nombre = $row['nombre'];
            $this->cedula = $row['cedula'];
            $this->fecha_nacimiento = $row['fecha_nacimiento'];
            $this->usuario_id = $row['usuario_id'];
            return true;
        }
        return false;
    }

    // Actualizar Paciente
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET nombre=:nombre, cedula=:cedula, fecha_nacimiento=:fecha_nacimiento 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->fecha_nacimiento = htmlspecialchars(strip_tags($this->fecha_nacimiento));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":cedula", $this->cedula);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Paciente
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
    public function cedulaExists($cedula, $exclude_id = null)
    {
         if ($exclude_id === null) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE cedula = :cedula";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":cedula", $cedula);
        } else {        
            $query = "SELECT id FROM " . $this->table_name . " WHERE cedula = :cedula AND id != :exclude_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":cedula", $cedula);
            $stmt->bindParam(":exclude_id", $exclude_id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function consultarAntecedentesPaciente($paciente_id) {
        $query = "SELECT p.nombre, p.cedula, a.descripcion AS antecedente 
                  FROM " . $this->table_name . " p
                  LEFT JOIN antecedentes_medicos a ON p.id = a.paciente_id
                  WHERE p.id = :paciente_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":paciente_id", $paciente_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }



}
