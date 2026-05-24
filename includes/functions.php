<?php
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function formatDate(?string $date): string {
    return $date ? date('d/m/Y', strtotime($date)) : '-';
}

function formatTime(?string $t): string {
    return $t ? date('H:i', strtotime($t)) : '-';
}

function formatDateTime(?string $dt): string {
    return $dt ? date('d/m/Y H:i', strtotime($dt)) : '-';
}

function calcularEdad(?string $fecha): string {
    if (!$fecha) return '-';
    return (new DateTime())->diff(new DateTime($fecha))->y . ' años';
}

function estadoBadge(string $estado): string {
    $map = ['Pendiente'=>'warning','Confirmada'=>'info','Completada'=>'success','Cancelada'=>'danger'];
    $c = $map[$estado] ?? 'secondary';
    return "<span class=\"badge bg-{$c}\">{$estado}</span>";
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function showFlash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['type']) . ' alert-dismissible fade show" role="alert">'
            . e($f['msg'])
            . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

function tipoAntecedenteColor(string $tipo): string {
    $map = ['Personal'=>'primary','Familiar'=>'secondary','Quirurgico'=>'danger','Alergico'=>'warning','Farmacologico'=>'info'];
    return $map[$tipo] ?? 'dark';
}
