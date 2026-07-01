<?php
require 'db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$marcano = isset($_GET['marcano']) ? (int)$_GET['marcano'] : (isset($_POST['marcano']) ? (int)$_POST['marcano'] : 0);

if (!$marcano) {
    echo json_encode(['status' => 'error', 'msg' => 'Falta el código de marca (MARCANO).']);
    exit;
}

switch ($action) {
    case 'listar':
        $buscar = $_GET['buscar'] ?? '';
        $por_pagina = 7; // Mantenemos tu paginación original de Laravel
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $inicio = ($pagina - 1) * $por_pagina;

        // 1. Obtener la información de la marca cabecera
        $stmt_marca = $pdo->prepare("SELECT * FROM marcas WHERE MARCANO = ?");
        $stmt_marca->execute([$marcano]);
        $marca = $stmt_marca->fetch(PDO::FETCH_ASSOC);

        // 2. Obtener los modelos paginados cruzando con Corridas y Clasificaciones (Reemplaza el @foreach anidado de Blade)
        $sql = "SELECT m.*, c.TALLAINI, c.TALLAFIN, cl.CLASIFIDES 
                FROM modelos m
                LEFT JOIN corridas c ON m.CORRIDANO = c.CORRIDANO AND m.MARCANO = c.MARCANO
                LEFT JOIN clasificaciones cl ON c.CLASIFICANO = cl.CLASIFICANO
                WHERE m.MARCANO = :marcano AND m.MODELODES LIKE :buscar 
                ORDER BY m.MODELONO ASC 
                LIMIT $inicio, $por_pagina";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['marcano' => $marcano, 'buscar' => "%$buscar%"]);
        $modelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Contar total de páginas
        $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM modelos WHERE MARCANO = :marcano AND MODELODES LIKE :buscar");
        $total_stmt->execute(['marcano' => $marcano, 'buscar' => "%$buscar%"]);
        $total_paginas = ceil($total_stmt->fetchColumn() / $por_pagina);

        echo json_encode([
            'marca' => $marca,
            'data' => $modelos,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina
        ]);
        break;

    case 'cargar_corridas':
        // Carga las corridas para los selectores de los formularios (altas/cambios)
        $sql = "SELECT c.*, cl.CLASIFIDES 
                FROM corridas c 
                LEFT JOIN clasificaciones cl ON c.CLASIFICANO = cl.CLASIFICANO 
                WHERE c.MARCANO = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$marcano]);
        $corridas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['corridas' => $corridas]);
        break;

    case 'siguiente_codigo':
        $stmt = $pdo->prepare("SELECT MAX(MODELONO) FROM modelos WHERE MARCANO = ?");
        $stmt->execute([$marcano]);
        $max = $stmt->fetchColumn();
        echo json_encode(['nuevoCodigo' => $max ? $max + 1 : 1]);
        break;

    case 'guardar':
        try {
            $stmt_max = $pdo->prepare("SELECT MAX(MODELONO) FROM modelos WHERE MARCANO = ?");
            $stmt_max->execute([$marcano]);
            $max = $stmt_max->fetchColumn();
            $modelono = $max ? $max + 1 : 1;

            $sql = "INSERT INTO modelos (MARCANO, MODELONO, MODELODES, COLOR, MATERIAL, SUELA, PRECIOCO, PRECIOVE, SITUACION, CORRIDANO, fecha) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $marcano,
                $modelono,
                $_POST['MODELODES'],
                $_POST['COLOR'] ?? null,
                $_POST['MATERIAL'] ?? null,
                $_POST['SUELA'] ?? null,
                $_POST['PRECIOCO'] ?? 0,
                $_POST['PRECIOVE'] ?? 0,
                $_POST['SITUACION'] ?? 'A',
                $_POST['CORRIDANO'],
                $_POST['fecha'] ?? null
            ]);

            echo json_encode(['status' => 'success', 'msg' => 'Modelo creado correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Error al guardar: ' . $e->getMessage()]);
        }
        break;

    case 'actualizar':
        try {
            $modelono = $_POST['MODELONO'];
            $sql = "UPDATE modelos SET 
                        MODELODES = ?, COLOR = ?, MATERIAL = ?, SUELA = ?, 
                        PRECIOCO = ?, PRECIOVE = ?, SITUACION = ?, CORRIDANO = ?, fecha = ? 
                    WHERE MARCANO = ? AND MODELONO = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['MODELODES'],
                $_POST['COLOR'] ?? null,
                $_POST['MATERIAL'] ?? null,
                $_POST['SUELA'] ?? null,
                $_POST['PRECIOCO'] ?? 0,
                $_POST['PRECIOVE'] ?? 0,
                $_POST['SITUACION'] ?? 'A',
                $_POST['CORRIDANO'],
                $_POST['fecha'] ?? null,
                $marcano,
                $modelono
            ]);

            echo json_encode(['status' => 'success', 'msg' => 'Modelo actualizado correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Error al actualizar: ' . $e->getMessage()]);
        }
        break;

    case 'eliminar':
        try {
            $modelono = $_POST['id'];

            // 1. Validar existencias activas en artículos (Regla de negocio crítica)
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM articulos WHERE MARCANO = ? AND MODELONO = ? AND EXISTENCIA > 0");
            $stmtCheck->execute([$marcano, $modelono]);
            if ($stmtCheck->fetchColumn() > 0) {
                echo json_encode(['status' => 'warning', 'msg' => 'No se puede eliminar: existen artículos con existencia mayor a cero.']);
                break;
            }

            // 2. Eliminar el modelo de la zapatería
            $stmtDelMod = $pdo->prepare("DELETE FROM modelos WHERE MARCANO = ? AND MODELONO = ?");
            $stmtDelMod->execute([$marcano, $modelono]);

            // 3. Eliminar artículos remanentes con existencia <= 0
            $stmtDelArt = $pdo->prepare("DELETE FROM articulos WHERE MARCANO = ? AND MODELONO = ? AND EXISTENCIA <= 0");
            $stmtDelArt->execute([$marcano, $modelono]);

            echo json_encode(['status' => 'success', 'msg' => 'Modelo y artículos con existencia cero eliminados correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'msg' => 'Error en el servidor: ' . $e->getMessage()]);
        }
        break;
}