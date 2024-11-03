<?php
header("Content-Type: application/json");
include 'db.php';

$uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/documentos/ejecucion/';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM Ejecucion_Presupuestaria;");
    $ejecuciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($ejecuciones);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['url']) && $_FILES['url']['error'] == 0) {
        $doc_name = basename($_FILES['url']['name']);
        $doc_temp = $_FILES['url']['tmp_name'];
        $doc_type = strtolower(pathinfo($doc_name, PATHINFO_EXTENSION));

        $allowed_types = ['pdf', 'doc', 'docx'];

        if (in_array($doc_type, $allowed_types)) {
            $doc_path = $uploadFileDir . $doc_name;
            $doc_url = 'https://muniupala.go.cr/documentos/ejecucion/' . $doc_name;

            // Mover el archivo al directorio de subida
            if (move_uploaded_file($doc_temp, $doc_path)) {
                // Extraer y sanitizar los datos del POST
                $year = isset($_POST['es_historico']) ? $_POST['es_historico'] : '';
                $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
                $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
                $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
                $historico = isset($_POST['es_historico']) ? $_POST['es_historico'] : '';

                // Consulta SQL
                $sql = "INSERT INTO Ejecucion_Presupuestaria (tipo, fecha, url, nombre, es_historico) VALUES (:tipo, :fecha, :url, :nombre,:es_historico)";
                $stmt = $pdo->prepare($sql);

                // Asignación de parámetros
                $stmt->bindParam(':tipo', $tipo);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->bindParam(':url', $doc_url);
                // En caso de no gustar con el nombre ingresar usar el nombre del documento real $doc_name
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':es_historico', $historico);

                // Ejecutar la consulta
                if ($stmt->execute()) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Ejecucion Presupuestaria guardado correctamente.',
                        'url' => $doc_url,
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al guardar el Ejecucion en la base de datos.',
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
        $sql = "SELECT url FROM Ejecucion_Presupuestaria WHERE id = :id";
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
            $sql = "DELETE FROM Ejecucion_Presupuestaria WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Ejecucion Presupuestaria eliminado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el Ejecucion en la base de datos.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ejecucion Presupuestaria no encontrado.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado.']);
    }
    exit;
}
