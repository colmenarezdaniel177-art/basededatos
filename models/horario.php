<?php
class HorarioModel {
    private $conn;
    private $table_name = "horario";

    public $id;
    public $especialistaId;
    public $dia_semana;
    public $hora_inicio;
    public $hora_fin;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Leer horarios de un especialista específico
    public function readByEspecialista($especialistaId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE especialistaId = ? ORDER BY dia_semana ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $especialistaId);
        $stmt->execute();
        return $stmt;
    }
    
    public function saveSchedule($especialistaId, $dias) {
        try {            
            $deleteQuery = "DELETE FROM " . $this->table_name . " WHERE especialistaId = ?";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->execute([$especialistaId]);

            
            $insertQuery = "INSERT INTO " . $this->table_name . " (especialistaId, dia_semana, hora_inicio, hora_fin) 
                            VALUES (:espId, :dia, :inicio, :fin)";
            $insertStmt = $this->conn->prepare($insertQuery);

            foreach ($dias as $diaNum => $datos) {
            
                if (isset($datos['activo']) && !empty($datos['inicio']) && !empty($datos['fin'])) {
                    $insertStmt->bindValue(':espId', $especialistaId);
                    $insertStmt->bindValue(':dia', $diaNum);
                    $insertStmt->bindValue(':inicio', $datos['inicio']);
                    $insertStmt->bindValue(':fin', $datos['fin']);
                    $insertStmt->execute();
                }
            }
            return true;
        } catch (Exception $e) {            
            return false;
        }
    }
}