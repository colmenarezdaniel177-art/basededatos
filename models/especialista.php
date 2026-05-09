<?php
class EspecialistaModel {
    private $conn;
    private $table_name = "especialista";

    public $id;
    public $nombre;
    public $especialidad_id;
    

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear Especialista
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nombre=:nombre, especialidad_id=:especialidad_id";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->especialidad_id = htmlspecialchars(strip_tags($this->especialidad_id));
        

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":especialidad_id", $this->especialidad_id);
        

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las Especialistas
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una Especialista por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre = $row['nombre'];
            $this->especialidad_id = $row['especialidad_id'];
            return true;
        }
        return false;
    }

    // Actualizar Especialista
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET nombre=:nombre, especialidad_id=:especialidad_id
                 WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->especialidad_id = htmlspecialchars(strip_tags($this->especialidad_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":especialidad_id", $this->especialidad_id);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Especialista
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    
    public function especialistaExists($nombre, $exclude_id = null) {
         if ($exclude_id === null) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE nombre = :nombre ";        
        
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
        } else {   
            $query = "SELECT id FROM " . $this->table_name . " WHERE nombre = :nombre  AND id != :exclude_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":exclude_id", $exclude_id);
        }

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function GetPorNombre($nombre ) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nombre = :nombre";
        
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->nombre = $row['Nombre'];
            $this->id = $row['Id'];
    	    $this->especialidad_id = $row['especialidad_id'];
            return true;
        }
        return false;


    }
}
?>