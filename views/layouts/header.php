<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo isset($title) ? $title : 'Ozono Vital'; ?>
    </title>

    <!-- GOOGLE FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FONT AWESOME -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- CSS DASHBOARD -->
    <link rel="stylesheet" href="assets/css/global.css">

    <link rel="stylesheet" href="assets/css/sidebar.css">

    <link rel="stylesheet" href="assets/css/dashboard.css">

    <link rel="stylesheet" href="assets/css/components.css">

</head>

<body>

    <div class="container-fluid mt-3">

        <?php if (isset($_SESSION['success'])): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>

            </div>

        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>

            </div>

        <?php endif; ?>

    </div>