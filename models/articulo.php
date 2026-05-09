<?php
class ArticuloModel {
    private $conn;
    private $table_name = "articulo";

    public $id;
    public $nombre;
    public $descripcion;
    public $categoria;
    public $stock;
    public $stock_minimo;
    public $stock_maximo;
    public $precio;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear Articulo
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET nombre=:nombre, descripcion=:descripcion, categoria=:categoria,
                 stock_minimo=:stock_minimo, stock_maximo=:stock_maximo, precio=:precio, categoria2=:categoria2";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->stock_maximo = htmlspecialchars(strip_tags($this->stock_maximo));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
         $this->categoria2 = htmlspecialchars(strip_tags($this->categoria2));
        

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":categoria", $this->categoria);
        $stmt->bindParam(":stock_minimo", $this->stock_minimo);
        $stmt->bindParam(":stock_maximo", $this->stock_maximo);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":categoria2", $this->categoria2);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las Articulos
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una Articulo por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->categoria = $row['categoria'];
            $this->stock_minimo = $row['stock_minimo'];
            $this->stock_maximo = $row['stock_maximo'];
            $this->precio = $row['precio'];
            $this->categoria2 = $row['categoria2'];

            return true;
        }
        return false;
    }

    // Actualizar Articulo
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET  nombre=:nombre, descripcion=:descripcion, categoria=:categoria,
                 stock_minimo=:stock_minimo, stock_maximo=:stock_maximo, precio=:precio, categoria2=:categoria2";
        
        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->stock_maximo = htmlspecialchars(strip_tags($this->stock_maximo));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->categoria2 = htmlspecialchars(strip_tags($this->categoria2));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":categoria", $this->categoria);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":stock_minimo", $this->stock_minimo);
        $stmt->bindParam(":stock_maximo", $this->stock_maximo);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":categoria2", $this->categoria2);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Articulo
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si nombre ya existe
    public function nombreExists($nombre) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE nombre = :nombre";
          
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>