<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="main-container">

    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="content">

        <!-- HEADER -->
        <div class="dashboard-header">

            <div>
                <h1 class="page-title">
                    Dashboard Médico
                </h1>

                <p class="dashboard-subtitle">
                    Bienvenido al sistema Ozono Vital
                </p>
            </div>

        </div>

        <!-- STATS -->
        <div class="stats-grid">

            <div class="stats-card">

                <div class="stats-icon blue">
                    <i class="fas fa-user-injured"></i>
                </div>

                <div>
                    <h3>150</h3>
                    <p>Pacientes</p>
                </div>

            </div>

            <div class="stats-card">

                <div class="stats-icon green">
                    <i class="fas fa-calendar-check"></i>
                </div>

                <div>
                    <h3>35</h3>
                    <p>Citas Hoy</p>
                </div>

            </div>

            <div class="stats-card">

                <div class="stats-icon purple">
                    <i class="fas fa-user-md"></i>
                </div>

                <div>
                    <h3>12</h3>
                    <p>Especialistas</p>
                </div>

            </div>

            <div class="stats-card">

                <div class="stats-icon orange">
                    <i class="fas fa-heartbeat"></i>
                </div>

                <div>
                    <h3>89</h3>
                    <p>Atenciones</p>
                </div>

            </div>

        </div>

        <!-- TABLE -->
        <div class="card table-card">

            <div class="card-header-modern">

                <h4>Citas Recientes</h4>

                <button class="btn-modern btn-primary-modern">
                    Nueva Cita
                </button>

            </div>

            <table class="table-modern">

                <thead>

                    <tr>
                        <th>Paciente</th>
                        <th>Especialista</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>María González</td>
                        <td>Dr. Pérez</td>
                        <td>20/05/2026</td>
                        <td>
                            <span class="badge badge-success">
                                Confirmada
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>Carlos Ruiz</td>
                        <td>Dra. Moreno</td>
                        <td>20/05/2026</td>
                        <td>
                            <span class="badge badge-warning">
                                Pendiente
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>Ana Torres</td>
                        <td>Dr. Silva</td>
                        <td>21/05/2026</td>
                        <td>
                            <span class="badge badge-danger">
                                Cancelada
                            </span>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>