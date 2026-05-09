<?php
class Factura_detalleModel {
    private $conn;
    private $table_name = "factura_detalle";

    public $id;
    public $factura_id;
    public $articulo_id;
    public $cantidad;
    public $monto;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear Factura_detalle
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET factura_id=:factura_id, articulo_id=:articulo_id, cantidad=:cantidad, monto=:monto";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->factura_id = htmlspecialchars(strip_tags($this->factura_id));
        $this->articulo_id = htmlspecialchars(strip_tags($this->articulo_id));
        $this->cantidad = htmlspecialchars(strip_tags($this->cantidad));
        $this->monto = htmlspecialchars(strip_tags($this->monto));

        // Vincular parámetros
        $stmt->bindParam(":factura_id", $this->factura_id);
        $stmt->bindParam(":articulo_id", $this->articulo_id);
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":monto", $this->monto);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las Factura_detalles
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una Factura_detalle por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->factura_id = $row['factura_id'];
            $this->articulo_id = $row['articulo_id'];
            $this->cantidad = $row['cantidad'];
            $this->cantidad = $row['monto'];
            return true;
        }
        return false;
    }

    // Actualizar Factura_detalle
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET factura_id=:factura_id, articulo_id=:articulo_id, cantidad=:cantidad, monto=:monto, 
                 WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->factura_id = htmlspecialchars(strip_tags($this->factura_id));
        $this->articulo_id = htmlspecialchars(strip_tags($this->articulo_id));
        $this->cantidad = htmlspecialchars(strip_tags($this->cantidad));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->monto = htmlspecialchars(strip_tags($this->monto));

        // Vincular parámetros
        $stmt->bindParam(":factura_id", $this->factura_id);
        $stmt->bindParam(":articulo_id", $this->articulo_id);
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":monto", $this->monto);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Factura_detalle
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si factura_id ya existe
    public function factura_idExists($factura_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE factura_id = :factura_id";
       
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":factura_id", $factura_id);
       
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>