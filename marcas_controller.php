<?php
require 'db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'listar':
    $buscar = $_GET['buscar'] ?? '';
    $por_pagina = 6;
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $inicio = ($pagina - 1) * $por_pagina;

    // IMPORTANTE: ORDER BY MARCANO ASC garantiza el orden por código
    $sql = "SELECT * FROM marcas WHERE MARCADES LIKE :buscar ORDER BY MARCANO ASC LIMIT $inicio, $por_pagina";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['buscar' => "%$buscar%"]);
    $marcas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar total para saber cuántas páginas hay
    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM marcas WHERE MARCADES LIKE :buscar");
    $total_stmt->execute(['buscar' => "%$buscar%"]);
    $total_paginas = ceil($total_stmt->fetchColumn() / $por_pagina);

    echo json_encode(['data' => $marcas, 'total_paginas' => $total_paginas, 'pagina_actual' => $pagina]);
    break;

    case 'siguiente_codigo':
        $stmt = $pdo->query("SELECT MAX(MARCANO) FROM marcas");
        $max = $stmt->fetchColumn();
        echo json_encode(['nuevoCodigo' => $max + 1]);
        break;

    case 'guardar':
        try {
            $stmt = $pdo->prepare("INSERT INTO marcas (MARCANO, MARCADES) VALUES (?, ?)");
            $stmt->execute([$_POST['MARCANO'], $_POST['MARCADES']]);
            echo json_encode(['status' => 'success', 'msg' => 'Marca guardada']);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]); }
        break;

    case 'actualizar':
        try {
            $stmt = $pdo->prepare("UPDATE marcas SET MARCADES = ? WHERE MARCANO = ?");
            $stmt->execute([$_POST['MARCADES'], $_POST['MARCANO']]);
            echo json_encode(['status' => 'success', 'msg' => 'Marca actualizada']);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]); }
        break;

    case 'eliminar':
    $id = $_POST['id'];
    
    try {
        // 1. Verificar si existen modelos asociados
        $stmtModelos = $pdo->prepare("SELECT COUNT(*) FROM modelos WHERE MARCANO = ?");
        $stmtModelos->execute([$id]);
        if ($stmtModelos->fetchColumn() > 0) {
            echo json_encode(['status' => 'warning', 'msg' => 'No se puede eliminar: La marca tiene modelos registrados.']);
            break;
        }

        // 2. Verificar si existen corridas asociadas
        $stmtCorridas = $pdo->prepare("SELECT COUNT(*) FROM corridas WHERE MARCANO = ?");
        $stmtCorridas->execute([$id]);
        if ($stmtCorridas->fetchColumn() > 0) {
            echo json_encode(['status' => 'warning', 'msg' => 'No se puede eliminar: La marca tiene corridas registradas.']);
            break;
        }

        // 3. Si pasa las validaciones, procedemos a borrar
        $stmtDelete = $pdo->prepare("DELETE FROM marcas WHERE MARCANO = ?");
        $stmtDelete->execute([$id]);
        
        echo json_encode(['status' => 'success', 'msg' => 'Marca eliminada correctamente.']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => 'Error en el servidor: ' . $e->getMessage()]);
    }
    break;
}