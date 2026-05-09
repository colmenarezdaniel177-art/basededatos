<?php
class UsuarioModel
{
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $login;
    public $password_hash;
    public $rol_id;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crear usuario
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET login=:login, password_hash=:password_hash, rol_id=:rol_id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->password_hash = htmlspecialchars(strip_tags($this->password_hash));
        $this->rol_id = htmlspecialchars(strip_tags($this->rol_id));

        // Vincular parámetros
        $stmt->bindParam(":login", $this->login);
        $stmt->bindParam(":password_hash", $this->password_hash);
        $stmt->bindParam(":rol_id", $this->rol_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todas las Usuarios
    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una Usuario por ID
    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->login = $row['login'];
            $this->password_hash = $row['password_hash'];
            $this->rol_id = $row['rol_id'];
            return true;
        }
        return false;
    }




    // Actualizar Usuario
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET login=:login, password_hash=:password_hash, rol_id=:rol_id 
                 WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->password_hash = htmlspecialchars(strip_tags($this->password_hash));
        $this->rol_id = htmlspecialchars(strip_tags($this->rol_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vincular parámetros
        $stmt->bindParam(":login", $this->login);
        $stmt->bindParam(":password_hash", $this->password_hash);
        $stmt->bindParam(":rol_id", $this->rol_id);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar Usuario
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

    // Verificar si login ya existe
    public function loginExists($login, $exclude_id = null)
    {
        if ($exclude_id === null) {
            $query = "SELECT id FROM " . $this->table_name . " WHERE login = :login ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":login", $login);
        } else {
            $query = "SELECT id FROM " . $this->table_name . " WHERE login = :login  AND id != :exclude_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":login", $login);
            $stmt->bindParam(":exclude_id", $exclude_id);
        }


        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getUserByLogin($login)
    {
        $query = "SELECT u.*, r.Nombre as rol_nombre
              FROM usuarios u  INNER JOIN rol r ON u.rol_id = r.Id 
              WHERE status=0 AND u.login = :login LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":login", $login);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCountByStatus()
    {
        $query = "
        SELECT 
            (CASE 
                WHEN Status = 0 THEN 'Activo'
                WHEN Status = 1 THEN 'Por Activar'
                ELSE 'Inactivo'
            END) AS EtiquetaStatus,             
            COUNT(*) as Total
        FROM 
            " . $this->table_name . "
        GROUP BY 
            EtiquetaStatus";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
    public function activar()
    {
        $query = "UPDATE " . $this->table_name . " SET status = 1 - status WHERE id = ?";
        $stmt = $this->conn->prepare($query);        
         $stmt->bindParam(1, $this->id);     
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
        
}
