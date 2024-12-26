<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, DELETE"); 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM directorio_telefonico ORDER BY nombre ASC;");
    $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($contactos);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extraer y sanitizar los datos del POST
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $departamento = isset($_POST['departamento']) ? $_POST['departamento'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $extension = isset($_POST['extension']) ? $_POST['extension'] : null;
    $puesto = isset($_POST['puesto']) ? $_POST['puesto'] : null;

    // Validar que se proporcionen los campos requeridos
    if (empty($nombre) || empty($departamento) || empty($telefono)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Faltan campos obligatorios (nombre, departamento, teléfono).'
        ]);
        exit;
    }

    // Consulta SQL para insertar el nuevo contacto en la base de datos
    $sql = "INSERT INTO directorio_telefonico (nombre, departamento, telefono, extension, email,puesto) 
            VALUES (:nombre, :departamento, :telefono, :extension, :email,:puesto)";
    $stmt = $pdo->prepare($sql);

    // Asignar los parámetros de la consulta
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':departamento', $departamento);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':extension', $extension);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':puesto', $puesto);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Contacto registrado correctamente.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al registrar el contacto en la base de datos.'
        ]);
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtener el ID del contacto a eliminar
    parse_str(file_get_contents("php://input"), $data);
    $id = isset($data['id']) ? $data['id'] : '';

    if ($id) {
        // Eliminar el contacto de la base de datos
        $sql = "DELETE FROM directorio_telefonico WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Contacto eliminado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el contacto en la base de datos.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado.']);
    }
}
?>
