<?php
class Tipo_AntecedenteModel {
    private $conn;
    private $table_name = "Tipo_Antecedente";

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
            // Si hay especialistas usando este Tipo_Antecedente, la BD lanzará error de FK
            return false;
        }
        return false;
    }


    public function Tipo_AntecedenteExists($nombre, $id = null) {

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

        public function consultarPacientesPorAntecedente() {
        // Cuenta cuántos pacientes diferentes tienen registrado cada tipo de antecedente médico
        $query = "SELECT ta.id, ta.nombre AS antecedente_nombre, COUNT(DISTINCT am.paciente_id) AS total_pacientes
                  FROM " . $this->table_name . " ta
                  LEFT JOIN antecedentes_medicos am ON ta.id = am.tipo_antecedente_id
                  GROUP BY ta.id, ta.nombre
                  ORDER BY total_pacientes DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

}