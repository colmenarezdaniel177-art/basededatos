<?php
require 'data.php';

$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (!$name || !$email || !$password || !$confirm) {
        $message = "Todos los campos son obligatorios.";
        $messageClass = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email inválido.";
        $messageClass = "error";
    } elseif ($password !== $confirm) {
        $message = "Las contraseñas no coinciden.";
        $messageClass = "error";
    } else {
        $result = register_user($name, $email, $password);
        if ($result === true) {
            $message = "Registro exitoso. <a href='login.php'>Inicia sesión</a>";
            $messageClass = "success";
        } else {
            $message = $result;
            $messageClass = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">
<title>Registro - Ozono Vital</title>
</head>
<body>
<section class="login-section">
    <h1>Registro en <span class="highlight">Ozono Vital</span></h1>
    <?php if ($message) echo "<p class='message $messageClass'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required>
        <button type="submit">Registrarse</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</section>
</body>
</html>
