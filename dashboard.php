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

                <!-- estilos de marcas de los card -->
  <style>
    /* Efecto elegante de elevación y escala al pasar el cursor (Hover) */
    .card-marca-elegante {
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        border: none;
        overflow: hidden;
        border-radius: 15px;
    }

    .card-marca-elegante:hover {
        /* Mueve la tarjeta 10px hacia arriba y la agranda un 3% */
        transform: translateY(-10px) scale(1.03); 
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3) !important;
    }

    /* Fondo Azul Obscuro para la zona de la imagen */
    .bg-navy-dark {
        background-color: #1a365d; /* Azul elegante */
    }

    /* Fondo Rojo para el cuerpo */
    .bg-red-elegant {
        background-color: #b91c1c; /* Rojo zapatería */
    }

    /* Contenedor de la imagen para que se vea estilizado */
    .img-container {
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .img-container img {
        max-height: 140px;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
</style>



<style>
    /* Efecto elegante de elevación y escala al pasar el cursor (Hover) */
    .card-marca-elegante {
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        /* SE AGREGA EL BORDE AZUL PARA DELIMITAR LA PARTE BLANCA */
        border: 2px solid #1a365d;
        overflow: hidden;
        border-radius: 15px;
        background-color: #ffffff; /* Fondo base blanco */
    }

    .card-marca-elegante:hover {
        transform: translateY(-10px) scale(1.03); 
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
    }

    /* Fondo Azul Obscuro para la zona de la imagen */
    .bg-navy-dark {
        background-color: #1a365d; 
    }

    /* Botón Rojo Personalizado */
    .btn-red-elegant {
        background-color: #b91c1c;
        color: #ffffff;
        transition: background-color 0.2s ease;
    }
    
    .btn-red-elegant:hover {
        background-color: #991b1b; /* Un rojo un poco más oscuro al pasar el cursor */
        color: #ffffff;
    }

    /* Contenedor de la imagen */
    .img-container {
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .img-container img {
        max-height: 140px;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
</style>

</head>

<body>

    <?php
      include "barraNavegacion.php";
    ?>


    <div class="container mt-5">
        <div class="row g-4">
            
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow card-marca-elegante h-100">
                <div class="bg-navy-dark img-container">
                    <img src="logo.png" alt="Marcas de Calzado" class="img-fluid bg-white p-2">
                </div>
                
                <div class="card-body bg-red-elegant text-white d-flex flex-column justify-content-between p-4">
                    <div>
                        <h4 class="fw-bold mb-2 tracking-wide text-uppercase" style="letter-spacing: 1px;">Marcas</h4>
                        <p class="text-white-50 small mb-4">
                            Explora y gestiona los principales fabricantes de zapatería. Encuentra las últimas tendencias y colecciones.
                        </p>
                    </div>
                    
                    <div class="d-grid">
                        <a href="marcas.php" class="btn btn-light btn-sm fw-bold shadow-sm rounded-pill py-2 text-dark">
                            <i class="bi bi-arrow-right-circle-fill me-1 text-danger"></i> Ir a Marcas
                        </a>
                    </div>
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
    
        
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow card-marca-elegante h-100">
                <div class="bg-navy-dark img-container">
                    <img src="logo.png" alt="Ventas de Calzado" class="img-fluid bg-white p-2">
                </div>
                
                <div class="card-body bg-red-elegant text-white d-flex flex-column justify-content-between p-4">
                    <div>
                        <h4 class="fw-bold mb-2 tracking-wide text-uppercase" style="letter-spacing: 1px;">Marcas</h4>
                        <p class="text-white-50 small mb-4">
                            Explora y gestiona los principales fabricantes de zapatería. Encuentra las últimas tendencias y colecciones.
                        </p>
                    </div>
                    
                    <div class="d-grid">
                        <a href="marcas.php" class="btn btn-light btn-sm fw-bold shadow-sm rounded-pill py-2 text-dark">
                            <i class="bi bi-arrow-right-circle-fill me-1 text-danger"></i> Ir a Marcas
                        </a>
                    </div>
                </div>
            </div>
        </div>
        


        <div class="col-md-4 mb-4">
    <div class="card text-center shadow-sm card-marca-elegante h-100">
        <div class="bg-navy-dark img-container">
            <img src="logo.png" alt="Marcas de Calzado" class="img-fluid bg-white p-2">
        </div>
        
        <div class="card-body bg-white text-dark d-flex flex-column justify-content-between p-4">
            <div>
                <h4 class="fw-bold mb-2 text-uppercase text-dark" style="letter-spacing: 1px;">Marcas</h4>
                <p class="text-muted small mb-4">
                    Explora y gestiona los principales fabricantes de zapatería. Encuentra las últimas tendencias y colecciones.
                </p>
            </div>
            
            <div class="d-grid">
                <a href="marcas.php" class="btn btn-red-elegant btn-sm fw-bold shadow-sm rounded-pill py-2">
                    Ir a Marcas <i class="bi bi-arrow-right-short ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>


        </div>
    </div>

    


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>