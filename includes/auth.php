<?php
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function isAdmin(): bool {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function isMedico(): bool {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'medico';
}

function isAdminOrMedico(): bool {
    return isAdmin() || isMedico();
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    }
}

function requireAdminOrMedico(): void {
    requireLogin();
    if (!isAdminOrMedico()) {
        header('Location: ' . BASE_URL . '/home.php');
        exit;
    }
}

function login(PDO $pdo, string $credential, string $password): bool {
    // Permite login por correo electrónico O por nombre de usuario
    $stmt = $pdo->prepare(
        "SELECT u.*, r.nombre AS rol_nombre
         FROM usuarios u
         JOIN roles r ON u.rol_id = r.id
         WHERE (u.email = ? OR u.nombre = ?) AND u.activo = 1
         ORDER BY (u.email = ?) DESC
         LIMIT 1"
    );
    $stmt->execute([$credential, $credential, $credential]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']        = $user['id'];
        $_SESSION['user_name']      = $user['nombre'];
        $_SESSION['user_email']     = $user['email'];
        $_SESSION['rol']            = $user['rol_nombre'];
        $_SESSION['especialista_id'] = $user['especialista_id'] ?? null;
        return true;
    }
    return false;
}
