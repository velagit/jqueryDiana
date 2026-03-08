<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// --- LÓGICA DE ELIMINAR ---
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: usuarios_crud.php?status=deleted");
    exit();
}

// --- LÓGICA DE INSERTAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_registrar'])) {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nivel = $_POST['nivel'];
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nivel) VALUES (?, ?, ?)");
        $stmt->execute([$user, $pass, $nivel]);
        $res = "success";
    } catch (Exception $e) { $res = "error"; }
}

// --- LÓGICA DE MODIFICAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_update'])) {
    $id = $_POST['id_usuario'];
    $user = $_POST['username'];
    $nivel = $_POST['nivel'];
    
    try {
        // Solo actualizamos password si se escribió algo nuevo
        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE usuarios SET username=?, password=?, nivel=? WHERE id=?");
            $stmt->execute([$user, $pass, $nivel, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET username=?, nivel=? WHERE id=?");
            $stmt->execute([$user, $nivel, $id]);
        }
        $res = "updated";
    } catch (Exception $e) { $res = "error"; }
}

$usuarios = $pdo->query("SELECT * FROM usuarios")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">SISTEMA DIANA</a>
        <div class="ms-auto">
            <a class="btn btn-outline-light btn-sm" href="dashboard.php"><i class="bi bi-house"></i> Volver</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 text-primary fw-bold"><i class="bi bi-people-fill"></i> Administración de Usuarios</h5>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="bi bi-person-plus-fill"></i> Agregar Usuario
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nivel</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($u['username']) ?></td>
                            <td>
                                <span class="badge rounded-pill <?= $u['nivel']=='admin'?'bg-danger':'bg-info text-dark' ?>">
                                    <?= strtoupper($u['nivel']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm shadow-sm" 
                                        onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </button>
                                <button onclick="confirmarEliminar(<?= $u['id'] ?>)" class="btn btn-danger btn-sm shadow-sm">
                                    <i class="bi bi-trash-fill"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nivel</label>
                        <select name="nivel" class="form-select">
                            <option value="usuario">Usuario Estándar</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="btn_registrar" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-warning">
            <form method="POST">
                <input type="hidden" name="id_usuario" id="edit_id">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nuevo Password <small class="text-muted">(opcional)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nivel</label>
                        <select name="nivel" id="edit_nivel" class="form-select">
                            <option value="usuario">Usuario Estándar</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="btn_update" class="btn btn-warning">Actualizar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Función para cargar datos en el modal de edición
function editUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_nivel').value = user.nivel;
    var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
    myModal.show();
}

// Lógica de SweetAlerts
<?php if(isset($res)): ?>
    <?php if($res == "success"): ?>
        Swal.fire('¡Creado!', 'El usuario se registró con éxito', 'success');
    <?php elseif($res == "updated"): ?>
        Swal.fire('¡Actualizado!', 'Datos modificados correctamente', 'success');
    <?php elseif($res == "error"): ?>
        Swal.fire('Error', 'No se pudo completar la operación', 'error');
    <?php endif; ?>
<?php endif; ?>

function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Eliminar usuario?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "usuarios_crud.php?delete=" + id;
        }
    })
}

if(new URLSearchParams(window.location.search).get('status') === 'deleted') {
    Swal.fire('Eliminado', 'El usuario ya no existe en el sistema.', 'success');
}
</script>
</body>
</html>