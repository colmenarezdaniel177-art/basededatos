<?php
$title = "Dashboard - Sistema de Gestión";
require_once '../views/layouts/header.php';
?>

<div class="row">
    <div class="col-md-2 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x mb-2"></i>
                <h3><?php echo $total_usuarios; ?></h3>
                <p>Usuarios</p>
            </div>
        </div>
    </div>
    <div class="col-md-2 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-hospital-user fa-3x mb-2"></i>
                <h3><?php echo $total_pacientes; ?></h3>
                <p>Pacientes</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-2 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-3x mb-2"></i>
                <h3><?php echo $total_citas; ?></h3>
                <p>Citas</p>
            </div>
        </div>
    </div>
    
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="index.php?controller=usuario&action=create" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus"></i> Nuevo Usuario
                    </a>
                    <a href="index.php?controller=paciente&action=create" class="btn btn-outline-success">
                        <i class="fas fa-plus-circle"></i> Nuevo Paciente
                    </a>
                    <a href="index.php?controller=cita&action=create" class="btn btn-outline-warning">
                        <i class="fas fa-calendar-plus"></i> Nueva Cita
                    </a>
                  
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Módulos del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="index.php?controller=rol&action=index" class="btn btn-light w-100">
                            <i class="fas fa-user-tag"></i> Roles
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="index.php?controller=usuario&action=index" class="btn btn-light w-100">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                    </div>                   
                    <div class="col-6 mb-3">
                        <a href="index.php?controller=paciente&action=index" class="btn btn-light w-100">
                            <i class="fas fa-hospital-user"></i> Pacientes
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="index.php?controller=especialista&action=index" class="btn btn-light w-100">
                            <i class="fas fa-user-md"></i> Especialistas
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>