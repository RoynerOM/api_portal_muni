<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173"); 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener todas las entradas de tintas
    $stmt = $pdo->query("SELECT * FROM entradas;");
    $ejecuciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($ejecuciones);
}

// Añadir nuevo stock desde la vista (cantidad actual + nuevas entradas)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    $impresoraId = $data['impresoraId'];
    $usuarioId = $data['usuarioId'];
    $stock = $data['stock'];
    $ubicacion = $data['ubicacion'];
    $nota = $data['nota'];
    $tipo = $data['tipo'];

    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // 1. Insertar en la tabla 'entradas'
        $sql = "INSERT INTO entradas (impresoraId, usuarioId, stock, fecha, ubicacion, nota, tipo) 
                VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$impresoraId, $usuarioId, $stock, $ubicacion, $nota, $tipo]);

        // 2. Actualizar el stock de tinta en la tabla 'tintas' (sumar el stock de acuerdo al tipo)
        $sql = "INSERT INTO tintas (impresoraId, tipo, stock) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE stock = stock + ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$impresoraId, $tipo, $stock, $stock]);

        // 3. Actualizar el stock total de la impresora (opcional, si también lo deseas)
        // Esta parte es opcional, solo si quieres mantener el stock total en la tabla 'impresoras'.
        // $sql = "UPDATE impresoras SET stock = stock + ? WHERE serie = ?";
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute([$stock, $impresoraId]);

        // Confirmar la transacción
        $pdo->commit();

        // Respuesta de éxito
        echo json_encode(["message" => "Entrada registrada y stock de tinta actualizado con éxito"]);

    } catch (Exception $e) {
        // Si ocurre un error, revertir los cambios
        $pdo->rollBack();
        echo json_encode(["error" => "Ocurrió un error al registrar la entrada: " . $e->getMessage()]);
    }
}
?>
