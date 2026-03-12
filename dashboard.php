<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location: index.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Dashboard</title>

    <style>
    .bg-danger {
        background-color: #d32f2f !important; /* Un rojo un poco más formal */
    }
    .nav-link:hover {
        color: #ffcdd2 !important; /* Un rosa muy claro al pasar el mouse */
    }

    </style>

    <style>
    /* Asegura que el botón de admin no herede transparencias de la navbar */
    .navbar-nav .btn-outline-light, .navbar-nav .btn-white {
        opacity: 1 !important;
        display: inline-flex;
        align-items: center;
        margin-top: 2px; /* Alineación fina */
    }

    /* Efecto al pasar el mouse para que el usuario sienta el clic */
    .navbar-nav .btn-outline-light:hover {
        background-color: #f8f9fa !important;
        transform: translateY(-1px);
        transition: all 0.2s;
    }
 </style>
</head>

<body>
   <nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="dashboard.php">
            <img src="logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top border border-white rounded-circle bg-white">
            MiApp
        </a>
        <button class="navbar-toggler border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold" href="marcas.php"><i class="bi bi-tag"></i> Marcas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold" href="#"><i class="bi bi-box-seam"></i> Artículos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold" href="#"><i class="bi bi-cart"></i> Ventas</a>
                </li>
                <?php if($_SESSION['nivel'] == 'admin'): ?>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link btn btn-outline-light btn-sm fw-bold px-3 shadow-sm" 
                            href="usuarios_crud.php">
                            <i class="bi bi-people-fill"></i> Gestionar Usuarios
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </div>
</nav>

    <div class="container mt-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5>Marcas</h5>
                        <p>Gestión de fabricantes.</p>
                        <a href="#" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5>Artículos</h5>
                        <p>Inventario de productos.</p>
                        <a href="#" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5>Ventas</h5>
                        <p>Registro de transacciones.</p>
                        <a href="#" class="btn btn-primary">Ir</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>