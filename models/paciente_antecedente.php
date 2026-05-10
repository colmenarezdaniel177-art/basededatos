<?php
class paciente_antecedenteModel {
    private $conn;
    private $table_name = "antecedentes_medicos";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los antecedentes de un paciente específico
    public function readByPaciente($pacienteId) {
        $query = "SELECT pa.*, ta.nombre as tipo_nombre 
                  FROM " . $this->table_name . " pa
                  JOIN tipo_antecedente ta ON pa.tipo_antecedente_id = ta.id
                  WHERE pa.paciente_id = ? 
                  ORDER BY pa.fecha DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $pacienteId);
        $stmt->execute();
        return $stmt;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (paciente_id, tipo_antecedente_id, descripcion, fecha) 
                  VALUES (:paciente_id, :tipo_id, :descripcion, :fecha)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}