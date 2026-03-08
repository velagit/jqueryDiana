<?php
require 'db.php';

$usuario = 'admin';
$password_plano = 'admin123'; // Esta será tu contraseña real
$password_hash = password_hash($password_plano, PASSWORD_BCRYPT);
$nivel = 'admin';

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, nivel) VALUES (?, ?, ?)");
    $stmt->execute([$usuario, $password_hash, $nivel]);
    echo "✅ Usuario creado con éxito.<br>";
    echo "Usuario: <b>$usuario</b><br>";
    echo "Password: <b>$password_plano</b><br>";
    echo "Hash guardado: <small>$password_hash</small>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>