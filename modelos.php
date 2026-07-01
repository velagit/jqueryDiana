<?php 
session_start(); 
if(!isset($_SESSION['user_id'])) header("Location: index.php"); 

// Recibimos la marca seleccionada vía URL (ej: modelos.php?marcano=1)
$marcano_get = isset($_GET['marcano']) ? (int)$_GET['marcano'] : 0;
if($marcano_get === 0) {
    die("Error: No se especificó un código de marca válido.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SISTEMA DIANA - Modelos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .pagination { flex-wrap: wrap; gap: 2px; }
        .page-link { padding: 0.4rem 0.75rem; font-size: 0.9rem; }
        #tablaModelos { min-height: 200px; }
        .pagination .page-link:hover { background-color: #aad7f4; color: #a94442; }
        .pagination .page-item.disabled .page-link { color: #6c757d; background-color: #f2f4f6; }
    </style>
</head>
<body class="bg-light">

<?php include "barraNavegacion.php"; ?>

<div class="container-fluid mt-5" style="max-width: 100rem;">
    <input type="hidden" id="global_marcano" value="<?php echo $marcano_get; ?>">

    <div class="card mb-3 border-primary text-dark shadow-sm">
        <div class="card-header bg-white border-primary"> 
            <h2 class="mb-0 text-primary" id="txtCabeceraPrincipal">Modelos de la Marca: ...</h2>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <button class="btn btn-primary w-100 fw-bold" onclick="abrirModalCrear()">
                        <i class="bi bi-file-earmark-plus me-1"></i> Agregar Modelo
                    </button>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscarModelo" class="form-control" placeholder="Buscar por descripción...">
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <a href="marcas.php" class="btn btn-secondary w-100 fw-bold">
                        <i class="bi bi-arrow-left-circle me-1"></i> Regresar a Marcas
                    </a>
                </div>
            </div>
        </div>
    </div>   

    <div class="card border-primary shadow-sm mb-5">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover table-sm">
                    <thead class="table-primary text-center">
                        <tr>
                            <th style="width: 7%;">NO</th>
                            <th>MODELODES</th>
                            <th>COLOR</th>
                            <th>MATERIAL</th>
                            <th>SUELA</th>
                            <th>PRECIOCO</th>
                            <th>PRECIOVE</th>
                            <th>Corrida</th>
                            <th style="width: 15%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaModelos">
                        </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                <nav><ul class="pagination" id="paginacion"></ul></nav>
            </div>
        </div> 
    </div>
</div>

<div class="modal fade" id="modalModelo" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-primary">
            <div class="modal-header text-white" style="background-color: #1a365d;"> 
                <h5 class="modal-title" id="modalTitulo">Registrar Modelo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModelo">
                <div class="modal-body text-dark">
                    <input type="hidden" name="marcano" value="<?php echo $marcano_get; ?>">

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Código Modelo</label>
                            <input type="text" id="MODELONO" name="MODELONO" class="form-control bg-light" readonly>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold">Descripción del Modelo</label>
                            <input type="text" id="MODELODES" name="MODELODES" class="form-control" maxlength="50" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Color</label>
                            <input type="text" id="COLOR" name="COLOR" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Material</label>
                            <input type="text" id="MATERIAL" name="MATERIAL" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Suela</label>
                            <input type="text" id="SUELA" name="SUELA" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Precio Compra</label>
                            <input type="number" step="0.01" id="PRECIOCO" name="PRECIOCO" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Precio Venta</label>
                            <input type="number" step="0.01" id="PRECIOVE" name="PRECIOVE" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Situación</label>
                            <select id="SITUACION" name="SITUACION" class="form-select">
                                <option value="A">Activo</option>
                                <option value="I">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Corrida</label>
                            <select id="CORRIDANO" name="CORRIDANO" class="form-select" required>
                                </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha</label>
                            <input type="date" id="fecha" name="fecha" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background-color:#1a365d; border-color: #1a365d;">
                        <i class="bi bi-file-earmark-check me-1"></i> Guardar Modelo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let paginaActual = 1;
const marcano = $('#global_marcano').val();

$(document).ready(function() {
    listarModelos();
    cargarCorridas();

    // Filtro instantáneo sin recargar pantalla
    $('#buscarModelo').on('keyup', function() {
        paginaActual = 1;
        listarModelos();
    });

    // Guardado y Actualizaciones centralizadas
    $('#formModelo').submit(function(e) {
        e.preventDefault();
        const action = $('#modalTitulo').text().includes('Agregar') ? 'guardar' : 'actualizar';
        
        $.post(`modelos_controller.php?action=${action}`, $(this).serialize(), function(res) {
            if(res.status === 'success') {
                $('#modalModelo').modal('hide');
                Swal.fire('Éxito', res.msg, 'success');
                listarModelos();
            } else {
                Swal.fire('Error', res.msg, 'error');
            }
        }, 'json');
    });
});

function listarModelos() {
    let buscar = $('#buscarModelo').val();
    $.get('modelos_controller.php', { action: 'listar', marcano: marcano, buscar: buscar, pagina: paginaActual }, function(res) {
        
        // Sincroniza dinámicamente la cabecera idéntica a Laravel
        if(res.marca) {
            $('#txtCabeceraPrincipal').text(`Modelos de la Marca: ${res.marca.MARCANO} - ${res.marca.MARCADES}`);
        }

        let html = '';
        if(res.data.length === 0) {
            html = `<tr><td colspan="9" class="text-center text-muted p-3">No se encontraron modelos registrados bajo este criterio.</td></tr>`;
        } else {
            res.data.forEach(m => {
                // Formateamos las corridas unificadas desde la consulta SQL limpia
                let corridaTexto = 'Sin corrida asignada';
                if(m.TALLAINI && m.TALLAFIN) {
                    let clasif = m.CLASIFIDES ? ` (${m.CLASIFIDES})` : '';
                    corridaTexto = `${m.TALLAINI} - ${m.TALLAFIN}${clasif}`;
                }

                html += `<tr>
                    <td class="text-center fw-bold">${m.MODELONO}</td>
                    <td>${m.MODELODES}</td>
                    <td>${m.COLOR || ''}</td>
                    <td>${m.MATERIAL || ''}</td>
                    <td>${m.SUELA || ''}</td>
                    <td class="text-end">$${parseFloat(m.PRECIOCO).toFixed(2)}</td>
                    <td class="text-end">$${parseFloat(m.PRECIOVE).toFixed(2)}</td>
                    <td class="text-center"><span class="badge bg-secondary">${corridaTexto}</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary me-1" onclick='abrirModalEditar(${JSON.stringify(m)})'>
                            <i class="bi bi-pencil-square"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarModelo(${m.MODELONO})">
                            <i class="bi bi-trash-fill"></i> Eliminar
                        </button>
                    </td>
                </tr>`;
            });
        }
        $('#tablaModelos').html(html);
        renderPaginacion(res.total_paginas);
    }, 'json');
}

function cargarCorridas() {
    $.get('modelos_controller.php', { action: 'cargar_corridas', marcano: marcano }, function(res) {
        let options = '<option value="">Seleccione una corrida</option>';
        res.corridas.forEach(c => {
            let clasif = c.CLASIFIDES ? ` (${c.CLASIFIDES})` : '';
            options += `<option value="${c.CORRIDANO}">${c.TALLAINI} - ${c.TALLAFIN}${clasif}</option>`;
        });
        $('#CORRIDANO').html(options);
    }, 'json');
}

function renderPaginacion(total) {
    let html = '';
    if (total <= 1) { $('#paginacion').html(''); return; }

    html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="cambiarPagina(1)">Inicio</a></li>`;
    let startPage = Math.max(1, paginaActual - 1);
    let endPage = Math.min(total, startPage + 2);

    if (endPage === total) startPage = Math.max(1, total - 2);
    if (startPage > 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;

    for (let i = startPage; i <= endPage; i++) {
        html += `<li class="page-item ${i === paginaActual ? 'active' : ''}"><a class="page-link" href="#" onclick="cambiarPagina(${i})">${i}</a></li>`;
    }

    if (endPage < total) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
    html += `<li class="page-item ${paginaActual === total ? 'disabled' : ''}"><a class="page-link" href="#" onclick="cambiarPagina(${total})">Fin</a></li>`;

    $('#paginacion').html(html);
}

function cambiarPagina(p) { paginaActual = p; listarModelos(); }

function abrirModalCrear() {
    $('#formModelo')[0].reset();
    $('#modalTitulo').text('Agregar Nuevo Modelo');
    
    $.get('modelos_controller.php', { action: 'siguiente_codigo', marcano: marcano }, function(res) {
        $('#MODELONO').val(res.nuevoCodigo);
        $('#modalModelo').modal('show');
    }, 'json');
}

function abrirModalEditar(m) {
    $('#modalTitulo').text('Modificar Modelo');
    $('#MODELONO').val(m.MODELONO);
    $('#MODELODES').val(m.MODELODES);
    $('#COLOR').val(m.COLOR);
    $('#MATERIAL').val(m.MATERIAL);
    $('#SUELA').val(m.SUELA);
    $('#PRECIOCO').val(m.PRECIOCO);
    $('#PRECIOVE').val(m.PRECIOVE);
    $('#SITUACION').val(m.SITUACION);
    $('#CORRIDANO').val(m.CORRIDANO);
    $('#fecha').val(m.fecha);
    $('#modalModelo').modal('show');
}

function eliminarModelo(modelono) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer. Se eliminarán los artículos con existencia cero asociados.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('modelos_controller.php?action=eliminar', { marcano: marcano, id: modelono }, function(res) {
                if(res.status === 'success') {
                    Swal.fire('¡Eliminado!', res.msg, 'success');
                    listarModelos();
                } else if(res.status === 'warning') {
                    Swal.fire('Atención', res.msg, 'info');
                } else {
                    Swal.fire('Error', res.msg, 'error');
                }
            }, 'json');
        }
    });
}
</script>
</body>
</html>