<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173"); 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener todas las salidas
    $stmt = $pdo->query("SELECT * FROM salidas;");
    $ejecuciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($ejecuciones);
}

// Añadir una nueva salida de tinta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos de la solicitud
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

        // 1. Comprobar el stock de tinta disponible para el tipo seleccionado
        $sql = "SELECT stock FROM tintas WHERE impresoraId = ? AND tipo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$impresoraId, $tipo]);
        $impresora = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no hay stock suficiente, lanzar una excepción
        if (!$impresora || $impresora['stock'] < $stock) {
            throw new Exception("No hay suficiente stock disponible de tinta para el tipo: $tipo.");
        }

        // 2. Registrar la salida en la tabla 'salidas'
        $sql = "INSERT INTO salidas (impresoraId, usuarioId, stock, fecha, ubicacion, nota, tipo) 
                VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$impresoraId, $usuarioId, $stock, $ubicacion, $nota, $tipo]);

        // 3. Actualizar el stock de tinta en la tabla 'tintas' (restar el stock de la tinta correspondiente)
        $sql = "UPDATE tintas SET stock = stock - ? WHERE impresoraId = ? AND tipo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$stock, $impresoraId, $tipo]);

        // 4. Actualizar el stock total de la impresora (opcional, si lo deseas mantener en la tabla 'impresoras')
        // Puedes actualizar el stock total de la impresora si es necesario. Si no lo necesitas, puedes omitir esta parte.
        // $sql = "UPDATE impresoras SET stock = stock - ? WHERE serie = ?";
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute([$stock, $impresoraId]);

        // Confirmar la transacción
        $pdo->commit();

        // Respuesta de éxito
        echo json_encode(["message" => "Salida registrada y stock actualizado con éxito"]);
    } catch (Exception $e) {
        // Si ocurre un error, revertir los cambios
        $pdo->rollBack();
        echo json_encode(["error" => "Ocurrió un error al registrar la salida: " . $e->getMessage()]);
    }
}
?>
