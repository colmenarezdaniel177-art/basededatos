<?php require_once '../views/layouts/header.php'; ?>

<div class="container-fluid mt-4">

    <!-- ESTADÍSTICAS PRINCIPALES -->
    <div class="row">

        <div class="col-md-4 mb-4">
            <div class="card border-primary shadow h-100">
                <div class="card-body text-center">

                    <i class="fas fa-users fa-3x text-primary mb-3"></i>

                    <h2><?= isset($total_usuarios) ? $total_usuarios : 0; ?></h2>

                    <h5>Total Usuarios</h5>

                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-success shadow h-100">
                <div class="card-body text-center">

                    <i class="fas fa-user-injured fa-3x text-success mb-3"></i>

                    <h2><?= isset($total_pacientes) ? $total_pacientes : 0; ?></h2>

                    <h5>Total Pacientes</h5>

                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-warning shadow h-100">
                <div class="card-body text-center">

                    <i class="fas fa-calendar-check fa-3x text-warning mb-3"></i>

                    <h2><?= isset($total_citas) ? $total_citas : 0; ?></h2>

                    <h5>Total Citas</h5>

                </div>
            </div>
        </div>

    </div>

    <!-- REPORTES -->
    <div class="row mt-4">

        <div class="col-12">

            <div class="card shadow border-0">

                <div class="card-header bg-dark text-white">

                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar"></i>
                        Reportes del Sistema
                    </h4>

                </div>

                <div class="card-body">

                    <!-- OPERACIONALES -->
                    <h5 class="text-primary mb-4">
                        <i class="fas fa-cogs"></i>
                        Reportes Operacionales
                    </h5>

                    <div class="row mb-5">

                        <div class="col-md-4 mb-3">
                            <div class="card border-primary shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-user-md fa-3x text-primary mb-3"></i>

                                    <h2><?= isset($total_historiales) ? $total_historiales : 0; ?></h2>

                                    <h6>Total Especialistas</h6>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-success shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-user-plus fa-3x text-success mb-3"></i>

                                    <h2><?= isset($pacientes_recientes) ? $pacientes_recientes : 0; ?></h2>

                                    <h6>Pacientes Registrados</h6>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-warning shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-users fa-3x text-warning mb-3"></i>

                                    <h2><?= isset($usuarios_activos) ? $usuarios_activos : 0; ?></h2>

                                    <h6>Usuarios Activos</h6>

                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- SUPERVISIÓN -->
                    <h5 class="text-success mb-4">
                        <i class="fas fa-eye"></i>
                        Reportes de Supervisión
                    </h5>

                    <div class="row mb-5">

                        <div class="col-md-4 mb-3">
                            <div class="card border-danger shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-stethoscope fa-3x text-danger mb-3"></i>

                                    <h2><?= isset($total_especialidades) ? $total_especialidades : 0; ?></h2>

                                    <h6>Especialidades Médicas</h6>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-info shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-file-medical fa-3x text-info mb-3"></i>

                                    <h2><?= isset($total_diagnosticos) ? $total_diagnosticos : 0; ?></h2>

                                    <h6>Total Citas Registradas</h6>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-secondary shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-user-md fa-3x text-secondary mb-3"></i>

                                    <h2><?= isset($especialistas_activos) ? $especialistas_activos : 0; ?></h2>

                                    <h6>Especialistas Activos</h6>

                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- GERENCIALES -->
                    <h5 class="text-dark mb-4">
                        <i class="fas fa-chart-line"></i>
                        Reportes Gerenciales
                    </h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <div class="card border-dark shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-chart-area fa-3x text-dark mb-3"></i>

                                    <h2><?= isset($promedio_citas) ? $promedio_citas : 0; ?></h2>

                                    <h6>Promedio de Citas</h6>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-primary shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-hospital-user fa-3x text-primary mb-3"></i>

                                    <h2><?= isset($crecimiento_pacientes) ? $crecimiento_pacientes : 0; ?></h2>

                                    <h6>Crecimiento de Pacientes</h6>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card border-success shadow-sm h-100">
                                <div class="card-body text-center">

                                    <i class="fas fa-percentage fa-3x text-success mb-3"></i>

                                    <h2><?= isset($ocupacion_sistema) ? $ocupacion_sistema : 0; ?>%</h2>

                                    <h6>Ocupación del Sistema</h6>

                                </div>
                            </div>
                        </div>

                        <a href="http://localhost/Ozono_vital/controllers/ReportePDFController.php"
                            target="_blank"
                            class="btn btn-danger">

                            Descargar PDF

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once '../views/layouts/footer.php'; ?>