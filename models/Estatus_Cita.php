<?php
class Estatus_CitaModel {
    private $conn;
    private $table_name = "Estatus_Cita";

    // Propiedades de la entidad
    public $id;
    public $nombre;

    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function read() {
        $query = "SELECT id, nombre FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    
    public function readOne() {
        $query = "SELECT id, nombre FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nombre = $row['nombre'];
            return true;
        }
        return false;
    }

    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=:nombre";
        $stmt = $this->conn->prepare($query);

    
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $stmt->bindParam(":nombre", $this->nombre);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            // Si hay especialistas usando este Estatus_Cita, la BD lanzará error de FK
            return false;
        }
        return false;
    }


    public function Estatus_CitaExists($nombre, $id = null) {

        $query = "SELECT id FROM " . $this->table_name . " WHERE nombre = :nombre";
        if ($id) {
            $query .= " AND id != :id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        if ($id) {
            $stmt->bindParam(":id", $id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    
    public function getStatusIdByName($nombre) {
        $query = "SELECT id FROM estatus_cita WHERE nombre = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$nombre]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id'] : null;
    }
    
      public function consultarCitasCanceladasYAusentes($inicio, $fin) {
        // Consulta las citas cruzando datos según sus estados de inasistencia o cancelación
        $query = "SELECT c.id, p.nombre AS paciente_nombre, e.nombre AS especialista_nombre, c.fecha, c.status, c.nota 
                  FROM cita c
                  INNER JOIN paciente p ON c.paciente_id = p.id
                  INNER JOIN especialista e ON c.especialista_id = e.id
                  WHERE c.fecha BETWEEN :inicio AND :fin 
                    AND c.status IN ('Cancelada', 'Ausente', 'No Asistio')
                  ORDER BY c.fecha ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":inicio", $inicio);
        $stmt->bindParam(":fin", $fin);
        $stmt->execute();
        
        return $stmt;
    }
}