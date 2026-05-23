<div class="sidebar">

    <!-- LOGO -->
    <div class="sidebar-logo">
        <i class="fas fa-clinic-medical"></i>
        <span>Ozono Vital</span>
    </div>

    <!-- MENU -->
    <ul class="sidebar-menu">

        <!-- DASHBOARD -->
        <li>
            <a href="index.php">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- PACIENTES -->
        <li>
            <a href="index.php?controller=paciente&action=index">
                <i class="fas fa-user-injured"></i>
                <span>Pacientes</span>
            </a>
        </li>

        <!-- ESPECIALISTAS -->
        <li>
            <a href="index.php?controller=especialista&action=index">
                <i class="fas fa-user-md"></i>
                <span>Especialistas</span>
            </a>
        </li>

        <!-- ESPECIALIDADES -->
        <li>
            <a href="index.php?controller=especialidad&action=index">
                <i class="fas fa-stethoscope"></i>
                <span>Especialidades</span>
            </a>
        </li>

        <!-- CITAS -->
        <li>
            <a href="index.php?controller=cita&action=index">
                <i class="fas fa-calendar-check"></i>
                <span>Citas Médicas</span>
            </a>
        </li>

        <!-- MEDICAMENTOS -->
        <li>
            <a href="index.php?controller=medicamento&action=index">
                <i class="fas fa-pills"></i>
                <span>Medicamentos</span>
            </a>
        </li>

        <!-- ANTECEDENTES -->
        <li>
            <a href="index.php?controller=tipo_antecedente&action=index">
                <i class="fas fa-notes-medical"></i>
                <span>Antecedentes</span>
            </a>
        </li>

        <!-- USUARIOS -->
        <li>
            <a href="index.php?controller=usuario&action=index">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </a>
        </li>

        <!-- REPORTES -->
        <li class="sidebar-dropdown">

            <div class="sidebar-dropdown-toggle">

                <div>
                    <i class="fas fa-chart-pie"></i>
                    <span>Reportes</span>
                </div>

                <i class="fas fa-chevron-down dropdown-arrow"></i>

            </div>

            <ul class="sidebar-submenu">

                <!-- REPORTES OPERATIVOS -->
                <li class="submenu-title">
                    Operativos
                </li>

                <li>
                    <a href="index.php?controller=cita&action=reportePacientes">
                        <i class="fas fa-chart-line"></i>
                        Pacientes Atendidos
                    </a>
                </li>

                <li>
                    <a href="index.php?controller=cita&action=reporteAgenda">
                        <i class="fas fa-calendar-alt"></i>
                        Agenda Especialistas
                    </a>
                </li>

                <li>
                    <a href="index.php?controller=cita&action=reporteFichaPaciente">
                        <i class="fas fa-id-card"></i>
                        Ficha Paciente
                    </a>
                </li>

                <li>
                    <a href="index.php?controller=especialista&action=reporteRendimiento">
                        <i class="fas fa-user-md"></i>
                        Rendimiento Médicos
                    </a>
                </li>

                <li>
                    <a href="index.php?controller=estatus_cita&action=reporteCanceladas">
                        <i class="fas fa-times-circle"></i>
                        Citas Canceladas
                    </a>
                </li>

                <li>
                    <a href="index.php?controller=tipo_antecedente&action=reportePorAntecedente">
                        <i class="fas fa-microscope"></i>
                        Carga Antecedentes
                    </a>
                </li>

                <!-- GERENCIALES -->
                <li class="submenu-title">
                    Gerenciales
                </li>

                <li>
                    <a href="index.php?controller=cita&action=reporteGerencialMensual">
                        <i class="fas fa-chart-bar"></i>
                        Volumen Mensual
                    </a>
                </li>

                <li>
                    <a href="index.php?controller=paciente&action=reporteCrecimientoGerencial">
                        <i class="fas fa-user-plus"></i>
                        Crecimiento Pacientes
                    </a>
                </li>

                <li>
                    <a href="index.php?controller=especialidad&action=reporteEspecialidadesGerencial">
                        <i class="fas fa-trophy"></i>
                        Ranking Especialidades
                    </a>
                </li>

            </ul>

        </li>

        <!-- LOGOUT -->
        <li style="margin-top:30px;">

            <a href="../Login/logout.php">

                <i class="fas fa-sign-out-alt"></i>

                <span>Cerrar Sesión</span>

            </a>

        </li>

    </ul>

</div>

<!-- DROPDOWN SCRIPT -->
<script>

document.addEventListener("DOMContentLoaded", function () {

    const dropdowns = document.querySelectorAll(".sidebar-dropdown");

    dropdowns.forEach(dropdown => {

        const toggle = dropdown.querySelector(
            ".sidebar-dropdown-toggle"
        );

        toggle.addEventListener("click", () => {

            dropdown.classList.toggle("active");

        });

    });

});

</script>