<?php
class ClienteModel {
    private $conn;
    private $table_name = "cliente";

    public $id;
    public $nombre;
    public $cedula;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear Cliente
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nombre=:nombre, cedula=:cedula";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":cedula", $this->cedula);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las Clientes
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una Cliente por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre = $row['nombre'];
            $this->cedula = $row['cedula'];
            return true;
        }
        return false;
    }

    // Actualizar Cliente
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET nombre=:nombre, cedula=:cedula 
                 WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->cedula = htmlspecialchars(strip_tags($this->cedula));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":cedula", $this->cedula);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Cliente
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
    public function cedulaExists($cedula) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE cedula = :cedula";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>