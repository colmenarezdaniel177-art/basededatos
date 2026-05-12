<?php
require 'data.php';

$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $user = get_user($username);    
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $message = "username o contraseña incorrectos.";
        $messageClass = "error";
    } else {       
         $_SESSION['user'] = [
                'id' => $user['id'],
                'login' => $user['login'],
                'rol_id' => $user['rol_id'],
                'rol_nombre' => $user['rol_nombre'],                
            ];
        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<title>Login - Ozono Vital</title>
</head>
<body>
<section class="login-section">
    <h1>Bienvenido a <span class="highlight">Ozono Vital</span></h1>
    <?php if ($message) echo "<p class='message $messageClass'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar sesión</button>
    </form>
    <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</section>
</body>
</html>
