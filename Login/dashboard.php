<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<title>Dashboard - Ozono Vital</title>
</head>
<body>
<section class="login-section">
    <h1>Dashboard</h1>
    <div class="dashboard-content"> 
        <p>Hola, <?php echo $_SESSION['user']['login']; ?> </p>
        <p>Rol: <?php echo $_SESSION['user']['rol_nombre']; ?></p>
        <a href="logout.php"><button>Cerrar sesión</button></a>
         <?php if ($_SESSION['user']['rol_nombre']=='Invitado'): ?>
            <a href="../views/index.php"><button>Inicio</button></a>
        <?php else: ?>
            <a href="../public/index.php"><button>Inicio</button></a>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
