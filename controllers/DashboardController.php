<?php

require_once '../config/database.php';

class DashboardController {

    private $db;

    public function __construct() {

        $database = new Database();

        $this->db = $database->getConnection();
    }

    public function index() {

        try {

            // =========================
            // TOTALES PRINCIPALES
            // =========================

            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM usuarios
            ");

            $total_usuarios = $query->fetch(PDO::FETCH_ASSOC)['total'];

            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM paciente
            ");

            $total_pacientes = $query->fetch(PDO::FETCH_ASSOC)['total'];

            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM cita
            ");

            $total_citas = $query->fetch(PDO::FETCH_ASSOC)['total'];

            // =========================
            // REPORTES OPERACIONALES
            // =========================

            // TOTAL ESPECIALISTAS
            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM especialista
            ");

            $total_historiales = $query->fetch(PDO::FETCH_ASSOC)['total'];

            // PACIENTES REGISTRADOS
            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM paciente
            ");

            $pacientes_recientes = $query->fetch(PDO::FETCH_ASSOC)['total'];

            // USUARIOS ACTIVOS
            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM usuarios
            ");

            $usuarios_activos = $query->fetch(PDO::FETCH_ASSOC)['total'];

            // =========================
            // REPORTES SUPERVISIÓN
            // =========================

            // ESPECIALIDADES
            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM especialidad
            ");

            $total_especialidades = $query->fetch(PDO::FETCH_ASSOC)['total'];

            // TOTAL CITAS
            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM cita
            ");

            $total_diagnosticos = $query->fetch(PDO::FETCH_ASSOC)['total'];

            // ESPECIALISTAS ACTIVOS
            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM especialista
            ");

            $especialistas_activos = $query->fetch(PDO::FETCH_ASSOC)['total'];

            // =========================
            // REPORTES GERENCIALES
            // =========================

            // PROMEDIO CITAS
            $query = $this->db->query("
                SELECT ROUND(COUNT(*) / 30, 2) AS promedio
                FROM cita
            ");

            $promedio_citas = $query->fetch(PDO::FETCH_ASSOC)['promedio'];

            // CRECIMIENTO PACIENTES
            $query = $this->db->query("
                SELECT COUNT(*) AS total
                FROM paciente
            ");

            $crecimiento_pacientes = $query->fetch(PDO::FETCH_ASSOC)['total'];

            // OCUPACIÓN SISTEMA
            $query = $this->db->query("
                SELECT ROUND((COUNT(*) * 100) / 500, 2) AS porcentaje
                FROM cita
            ");

            $ocupacion_sistema = $query->fetch(PDO::FETCH_ASSOC)['porcentaje'];

            // =========================
            // CARGAR VISTA
            // =========================

            require_once '../views/dashboard/index.php';

        } catch (Exception $e) {

            echo '<h2>Error Dashboard</h2>';
            echo '<p>' . $e->getMessage() . '</p>';
        }
    }
}