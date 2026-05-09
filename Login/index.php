<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
    <link rel="stylesheet" href="style.css">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
</head>

<body>
    <h1>Bienvenido al Sistema</h1>
    <?php if (isset($_SESSION['user'])): ?>
        <p>Hola, <?php echo $_SESSION['user']['name']; ?> | <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Cerrar sesión</a></p>
    <?php else: ?>
        <p><a href="register.php">Registrarse</a> | <a href="login.php">Iniciar sesión</a></p>
    <?php endif; ?>
</body>
</html>
