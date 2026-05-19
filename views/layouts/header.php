<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Sistema de Gestión'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-clinic-medical"></i> Sistema Gestión
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?controller=usuario&action=index">Usuarios</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=rol&action=index">Roles</a></li>
                             <li><a class="dropdown-item" href="index.php?controller=paciente&action=index">Pacientes</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=especialista&action=index">Especialistas</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-hospital"></i> Médico
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php?controller=paciente&action=index">Pacientes</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=especialista&action=index">Especialistas</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=especialidad&action=index">Especialidad</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=cita&action=index">Citas</a></li>

                            <li><a class="dropdown-item" href="index.php?controller=estatus_cita&action=index">Status Cita</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=tipo_antecedente&action=index">Tipo Antecedente</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=medicamento&action=index">Medicamento</a></li>

                            
                            <li><hr class="dropdown-divider"></li>
                            <li class="dropdown-header font-weight-bold text-dark"><i class="fas fa-file-invoice"></i> REPORTES</li>
                            <li><a class="dropdown-item" href="index.php?controller=cita&action=reportePacientes"><i class="fas fa-chart-line me-1"></i> Pacientes Atendidos</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=cita&action=reporteAgenda"><i class="fas fa-calendar-check me-1"></i> Agenda por Especialista</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=cita&action=reporteFichaPaciente"><i class="fas fa-id-card me-1"></i> Ficha de Antecedentes</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=especialista&action=reporteRendimiento"><i class="fas fa-chart-bar me-1"></i> Rendimiento Médicos</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=estatus_cita&action=reporteCanceladas"><i class="fas fa-times-circle me-1"></i> Citas Canceladas</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=tipo_antecedente&action=reportePorAntecedente"><i class="fas fa-microscope me-1"></i> Carga por Antecedentes</a></li>

                            <li><hr class="dropdown-divider"></li>
                            <li class="dropdown-header text-primary font-weight-bold"><i class="fas fa-crown"></i> GERENCIAL DIRECTIVO</li>
                            <li><a class="dropdown-item" href="index.php?controller=cita&action=reporteGerencialMensual"><i class="fas fa-chart-line me-1"></i> Volumen Mensual Citas</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=paciente&action=reporteCrecimientoGerencial"><i class="fas fa-user-plus me-1"></i> Crecimiento Pacientes</a></li>
                            <li><a class="dropdown-item" href="index.php?controller=especialidad&action=reporteEspecialidadesGerencial"><i class="fas fa-crown me-1"></i> Ranking Especialidades</a></li>
                        
                        </ul>
                    </li>                    
                </ul>

                        </ul>
                    </li>                    
                </ul>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="../Login/logout.php">
                    <i class="fa fa-sign-out"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>