<?php session_start(); if(!isset($_SESSION['user_id'])) header("Location: index.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SISTEMA DIANA - Marcas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .pagination {
            flex-wrap: wrap; /* Permite que los números bajen si no caben */
            gap: 2px;
        }
        .page-link {
            padding: 0.4rem 0.75rem; /* Hace los botones un poco más compactos */
            font-size: 0.9rem;
        }

        /* Evita que la tabla cambie de altura bruscamente */
        #tablaMarcas {
        min-height: 200px;
                }
    </style>

    <style>
  
    /* Efecto Hover */
    .pagination .page-link:hover {
        background-color: #aad7f4;
        color: #a94442;
    }

    /* Botones deshabilitados */
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f2f4f6;
    }
</style>

</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">SISTEMA DIANA</a>
        <button class="btn btn-light btn-sm text-danger fw-bold" onclick="abrirModalCrear()">
            <i class="bi bi-file-earmark-plus"></i> Agregar Marca
        </button>
    </div>
</nav>

<div class="container">
    <div class="card border-primary shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <span>Listado de Marcas</span>
            
            <button class="btn btn-outline-light btn-sm text-white fw-bold" onclick="abrirModalCrear()">
              <i class="bi bi-file-earmark-plus"></i> Agregar Marca
            </button>
            <input type="text" id="buscarMarca" class="form-control form-control-sm w-25" placeholder="Buscar descripción...">
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr class="table-secondary">
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaMarcas"></tbody>
                </table>
            </div>
            <nav><ul class="pagination justify-content-center" id="paginacion"></ul></nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMarca" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitulo">Marca</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMarca">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Código Marca</label>
                        <input type="text" id="MARCANO" name="MARCANO" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" id="MARCADES" name="MARCADES" class="form-control" maxlength="20" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let paginaActual = 1;

$(document).ready(function() {
    listarMarcas();

    // Buscar al escribir
    $('#buscarMarca').on('keyup', function() {
        paginaActual = 1;
        listarMarcas();
    });

    // Guardar/Actualizar
    $('#formMarca').submit(function(e) {
        e.preventDefault();
        const action = $('#modalTitulo').text().includes('Agregar') ? 'guardar' : 'actualizar';
        $.post(`marcas_controller.php?action=${action}`, $(this).serialize(), function(res) {
            if(res.status == 'success') {
                $('#modalMarca').modal('hide');
                Swal.fire('Éxito', res.msg, 'success');
                listarMarcas();
            }
        });
    });
});

function listarMarcas() {
    let buscar = $('#buscarMarca').val();
    $.get('marcas_controller.php', { action: 'listar', buscar: buscar, pagina: paginaActual }, function(res) {
        let html = '';
        res.data.forEach(m => {
            html += `<tr>
                <td>${m.MARCANO}</td>
                <td>${m.MARCADES}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick='abrirModalEditar(${JSON.stringify(m)})'><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarMarca(${m.MARCANO})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
        });
        $('#tablaMarcas').html(html);
        renderPaginacion(res.total_paginas);
    });
}

function renderPaginacion(total) {
    let html = '';
    if (total <= 1) { $('#paginacion').html(''); return; }

    // Botón INICIO (Siempre va a la página 1)
    html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="cambiarPagina(1)">Inicio</a>
             </li>`;

    // LÓGICA DE DESPLAZAMIENTO (Ventana de 3 números)
    // Calculamos el inicio de la ventana para que la página actual intente estar al centro
    let startPage = Math.max(1, paginaActual - 1);
    let endPage = Math.min(total, startPage + 2);

    // Ajuste si estamos en las últimas páginas para que siempre muestre 3 números
    if (endPage === total) {
        startPage = Math.max(1, total - 2);
    }

    // Puntos suspensivos iniciales si no estamos cerca del inicio
    if (startPage > 1) {
        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    // Generar los 3 números dinámicos
    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === paginaActual ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPagina(${i})">${i}</a>
                 </li>`;
    }

    // Puntos suspensivos finales si no estamos cerca del fin
    if (endPage < total) {
        html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    }

    // Botón FIN (Siempre va a la última página)
    html += `<li class="page-item ${paginaActual === total ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="cambiarPagina(${total})">Fin</a>
             </li>`;

    $('#paginacion').html(html);
}
function cambiarPagina(p) { paginaActual = p; listarMarcas(); }

function abrirModalCrear() {
    $('#formMarca')[0].reset();
    $('#modalTitulo').text('Agregar Nueva Marca');
    $.get('marcas_controller.php?action=siguiente_codigo', function(res) {
        $('#MARCANO').val(res.nuevoCodigo);
        $('#modalMarca').modal('show');
    });
}

function abrirModalEditar(m) {
    $('#modalTitulo').text('Modificar Marca');
    $('#MARCANO').val(m.MARCANO);
    $('#MARCADES').val(m.MARCADES);
    $('#modalMarca').modal('show');
}

function eliminarMarca(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará la marca permanentemente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviamos la petición por POST
            $.post('marcas_controller.php?action=eliminar', { id: id }, function(res) {
                if(res.status === 'success') {
                    Swal.fire('¡Eliminado!', res.msg, 'success');
                    listarMarcas(); // Refrescar tabla
                } else if(res.status === 'warning') {
                    // Aquí mostramos el mensaje de que tiene modelos o corridas
                    Swal.fire('Atención', res.msg, 'info');
                } else {
                    Swal.fire('Error', 'No se pudo completar la operación', 'error');
                }
            }, 'json');
        }
    });
}
</script>
</body>
</html>