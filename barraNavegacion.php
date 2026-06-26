<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-white" href="dashboard.php">
            <img src="logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top border border-white rounded-circle bg-white">
            <span class="navbar-brand fw-bold" > ZAPATERÍA DIANA </span>
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