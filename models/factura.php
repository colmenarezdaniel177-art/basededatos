<?php
class FacturaModel {
    private $conn;
    private $table_name = "factura";

    public $id;
    public $numero;
    public $numero_control;
    public $fechas;
    public $total;
    public $total_base_impuesto;
    public $total_impuesto;
    public $cliente_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear Factura
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET numero=:numero, numero_control=:numero_control, fecha=:fecha,
                 total_base_impuesto=:total_base_impuesto, total_impuesto=:total_impuesto, cliente_id=:cliente_id, fecha2=:fecha2";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->numero_control = htmlspecialchars(strip_tags($this->numero_control));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->total_base_impuesto = htmlspecialchars(strip_tags($this->total_base_impuesto));
        $this->total_impuesto = htmlspecialchars(strip_tags($this->total_impuesto));
        $this->cliente_id = htmlspecialchars(strip_tags($this->cliente_id));
         $this->fecha2 = htmlspecialchars(strip_tags($this->fecha2));
        

        // Vincular parámetros
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":numero_control", $this->numero_control);
        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":total_base_impuesto", $this->total_base_impuesto);
        $stmt->bindParam(":total_impuesto", $this->total_impuesto);
        $stmt->bindParam(":cliente_id", $this->cliente_id);
        $stmt->bindParam(":fecha2", $this->fecha2);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las Facturas
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una Factura por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->numero = $row['numero'];
            $this->numero_control = $row['numero_control'];
            $this->fecha = $row['fecha'];
            $this->total_base_impuesto = $row['total_base_impuesto'];
            $this->total_impuesto = $row['total_impuesto'];
            $this->cliente_id = $row['cliente_id'];
            $this->fecha2 = $row['fecha2'];

            return true;
        }
        return false;
    }

    // Actualizar Factura
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET  numero=:numero, numero_control=:numero_control, fecha=:fecha,
                 total_base_impuesto=:total_base_impuesto, total_impuesto=:total_impuesto, cliente_id=:cliente_id, fecha2=:fecha2";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->numero = htmlspecialchars(strip_tags($this->numero));
        $this->numero_control = htmlspecialchars(strip_tags($this->numero_control));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->total_base_impuesto = htmlspecialchars(strip_tags($this->total_base_impuesto));
        $this->total_impuesto = htmlspecialchars(strip_tags($this->total_impuesto));
        $this->cliente_id = htmlspecialchars(strip_tags($this->cliente_id));
        $this->fecha2 = htmlspecialchars(strip_tags($this->fecha2));

        // Vincular parámetros
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":numero_control", $this->numero_control);
        $stmt->bindParam(":fecha", $this->fecha);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":total_base_impuesto", $this->total_base_impuesto);
        $stmt->bindParam(":total_impuesto", $this->total_impuesto);
        $stmt->bindParam(":cliente_id", $this->cliente_id);
        $stmt->bindParam(":fecha2", $this->fecha2);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Factura
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si cliente_id ya existe
    public function cliente_idExists($cliente_id) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE cliente_id = :cliente_id";
          
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":numero_control", $numero_control);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>