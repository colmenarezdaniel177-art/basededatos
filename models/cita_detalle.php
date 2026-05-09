<?php
class Cita_detalleModel {
    private $conn;
    private $table_name = "cita_detalle";

    public $id;
    public $cita_id;
    public $observacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear cita_detalle
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET cita_id=:cita_id, observacion=:observacion";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->cita_id = htmlspecialchars(strip_tags($this->cita_id));
        $this->observacion = htmlspecialchars(strip_tags($this->observacion));

        // Vincular parámetros
        $stmt->bindParam(":cita_id", $this->cita_id);
        $stmt->bindParam(":observacion", $this->observacion);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las cita_detalles
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una cita_detalle por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->cita_id = $row['cita_id'];
            $this->observacion = $row['observacion'];
            return true;
        }
        return false;
    }

    // Actualizar cita_detalle
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET cita_id=:cita_id, observacion=:observacion 
                 WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->cita_id = htmlspecialchars(strip_tags($this->cita_id));
        $this->observacion = htmlspecialchars(strip_tags($this->observacion));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular parámetros
        $stmt->bindParam(":cita_id", $this->cita_id);
        $stmt->bindParam(":observacion", $this->observacion);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar cita_detalle
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si cédula ya existe
    public function observacionExists($cita_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE cita_id = :cita_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":observacion", $observacion);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>