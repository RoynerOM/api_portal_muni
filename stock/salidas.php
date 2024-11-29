<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173"); 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $stmt = $pdo->query("SELECT * FROM salidas;");
    $ejecuciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($ejecuciones);
    /*
    $stmt = $pdo->query("SELECT tipo, SUM(stock) AS cantidad FROM entradas GROUP BY tipo");
    $resumen = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resumen);*/
}

// Añadir el nuevo estok desde la vista cantidad actual + nuevas entradas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
         $sql = "INSERT INTO salidas (impresoraId, usuarioId, stock, fecha, ubicacion, nota, tipo) 
                 VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?, ?, ?)";
         $stmt = $pdo->prepare($sql);
         $stmt->execute([$impresoraId, $usuarioId, $stock, $ubicacion, $nota, $tipo]);
 
        // 2. Actualizar el stock de la impresora (disminuir el stock)
        $sql = "UPDATE impresoras SET stock = stock - ? WHERE serie = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$stock, $impresoraId]);

         // Confirmar la transacción
         $pdo->commit();
         
         // Respuesta de éxito
         echo json_encode(["message" => "Entrada registrada y stock actualizado con éxito"]);
     } catch (Exception $e) {
         // Si ocurre un error, revertir los cambios
         $pdo->rollBack();
         echo json_encode(["error" => "Ocurrió un error al registrar la entrada: " . $e->getMessage()]);
     }
}
?>


{
        "serie": "1234",
        "modelo": "HP LaserJet",
        "tipo": "Tóner",
        "modeloTinta": "Negro",
        "stock": "20",
        "disponible": "1",
        "tintas":{
            "Negro": 10,
            "Cyan":10,
            "Magenta":10,
            "Amarillo":10,
        }
    }