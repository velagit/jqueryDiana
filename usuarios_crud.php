<?php
session_start();
require 'db.php';

// Protección de ruta: Solo admin
if (!isset($_SESSION['user_id']) || $_SESSION['nivel'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Lógica para Eliminar
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: usuarios_crud.php?status=deleted");
}

// Lógica para Insertar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_registrar'])) {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nivel = $_POST['nivel'];

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nivel) VALUES (?, ?, ?)");
        $stmt->execute([$user, $pass, $nivel]);
        $res = "success";
    } catch (Exception $e) {
        $res = "error";
    }
}

// Obtener todos los usuarios
$usuarios = $pdo->query("SELECT id, username, nivel, created_at FROM usuarios")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">SISTEMA DIANA</a>
        <div class="navbar-nav">
            <a class="nav-link" href="dashboard.php">Volver al Panel</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Registrar Nuevo Usuario</div>
                <div class="card-body">
                    <form method="POST shadow-sm">
                        <div class="mb-3">
                            <label>Nombre de Usuario</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nivel</label>
                            <select name="nivel" class="form-select">
                                <option value="usuario">Usuario Estándar</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <button type="submit" name="btn_registrar" class="btn btn-success w-100">Guardar Usuario</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Nivel</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($usuarios as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['username']) ?></td>
                                <td><span class="badge <?= $u['nivel']=='admin'?'bg-danger':'bg-info' ?>"><?= $u['nivel'] ?></span></td>
                                <td>
                                    <button onclick="confirmarEliminar(<?= $u['id'] ?>)" class="btn btn-danger btn-sm">Eliminar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Alerta de éxito al registrar
<?php if(isset($res) && $res == "success"): ?>
    Swal.fire('¡Logrado!', 'Usuario registrado correctamente', 'success');
<?php elseif(isset($res) && $res == "error"): ?>
    Swal.fire('Error', 'El usuario ya existe o hubo un problema', 'error');
<?php endif; ?>

// Alerta de confirmación para eliminar
function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "usuarios_crud.php?delete=" + id;
        }
    })
}

// Alerta tras eliminar
const urlParams = new URLSearchParams(window.location.search);
if(urlParams.get('status') === 'deleted') {
    Swal.fire('Eliminado', 'El usuario ha sido borrado.', 'success');
}
</script>

</body>
</html>