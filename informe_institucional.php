<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
include 'db.php';

$uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/documentos/informes_institucionales/';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM Informes_Institucionales;");
    $presupuestos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($presupuestos);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['url']) && $_FILES['url']['error'] == 0) {
        $doc_name = basename($_FILES['url']['name']);
        $doc_temp = $_FILES['url']['tmp_name'];
        $doc_type = strtolower(pathinfo($doc_name, PATHINFO_EXTENSION));

        $allowed_types = ['pdf', 'doc', 'docx'];

        if (in_array($doc_type, $allowed_types)) {
            $doc_path = $uploadFileDir . $doc_name;
            $doc_url = 'https://muniupala.go.cr/documentos/informes_institucionales/' . $doc_name;

            // Mover el archivo al directorio de subida
            if (move_uploaded_file($doc_temp, $doc_path)) {
                // Extraer y sanitizar los datos del POST
                $year = isset($_POST['year']) ? $_POST['year'] : '';
                $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
                $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
                $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';

                // Consulta SQL
                $sql = "INSERT INTO Informes_Institucionales (year,tipo, fecha, url, nombre) VALUES (:year,:tipo, :fecha, :url, :nombre)";
                $stmt = $pdo->prepare($sql);

                // Asignación de parámetros
                $stmt->bindParam(':year', $year);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->bindParam(':url', $doc_url);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':tipo', $tipo);

                // Ejecutar la consulta
                if ($stmt->execute()) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Presupuesto guardado correctamente.',
                        'url' => $doc_url,
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al guardar el presupuesto en la base de datos.',
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al mover el archivo.',
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tipo de archivo no permitido. Solo se permiten PDF, DOC, y DOCX.',
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se ha subido ningún archivo o ha ocurrido un error en la carga.',
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtener el ID del presupuesto a eliminar
    parse_str(file_get_contents("php://input"), $data);
    $id = isset($data['id']) ? $data['id'] : '';

    if ($id) {
        // Consultar para obtener el URL del documento antes de eliminarlo
        $sql = "SELECT url FROM Informes_Institucionales WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $doc_url = $result['url'];
            // Eliminar el documento del sistema de archivos
            if (file_exists($uploadFileDir . basename($doc_url))) {
                unlink($uploadFileDir . basename($doc_url)); // Eliminar el archivo
            }

            // Eliminar el registro de la base de datos
            $sql = "DELETE FROM Informes_Institucionales WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Reporte Financiero  eliminado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el reporte en la base de datos.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Reporte Financiero  no encontrado.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado.']);
    }
    exit;
}
