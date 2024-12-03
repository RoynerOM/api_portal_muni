<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173"); 
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
   // Consulta SQL para obtener impresoras y tintas disponibles
   $sql = "
   SELECT
       i.serie,
       i.modelo,
       i.tipo,
       i.modeloTinta,
       i.stock,
       i.disponible,
       SUM(CASE WHEN e.tipo = 'Negro' THEN e.stock ELSE 0 END) AS tinta_negra,
       SUM(CASE WHEN e.tipo = 'Cyan' THEN e.stock ELSE 0 END) AS tinta_cyan,
       SUM(CASE WHEN e.tipo = 'Magenta' THEN e.stock ELSE 0 END) AS tinta_magenta,
       SUM(CASE WHEN e.tipo = 'Amarillo' THEN e.stock ELSE 0 END) AS tinta_amarillo,
       SUM(CASE WHEN e.tipo = 'Cinta' THEN e.stock ELSE 0 END) AS tinta_cinta,
       SUM(CASE WHEN e.tipo = 'Otros' THEN e.stock ELSE 0 END) AS tinta_otros
   FROM
       impresoras i
   LEFT JOIN
       entradas e ON i.serie = e.impresoraId
   LEFT JOIN
       salidas s ON i.serie = s.impresoraId where I.disponible=TRUE
   GROUP BY
       i.serie
   ORDER BY
       i.modelo;
   ";

   // Ejecutar la consulta
   $stmt = $pdo->prepare($sql);
   $stmt->execute();
   $impresoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Preparar los datos en el formato deseado
   $resultado = [];
   foreach ($impresoras as $impresora) {
       $tintas = [
           "Negro" => $impresora['tinta_negra'] ?? 0,
           "Cyan" => $impresora['tinta_cyan'] ?? 0,
           "Magenta" => $impresora['tinta_magenta'] ?? 0,
           "Amarillo" => $impresora['tinta_amarillo'] ?? 0,
           "Cinta" => $impresora['tinta_cinta'] ?? 0,
           "Otros" => $impresora['tinta_otros'] ?? 0
       ];

       $resultado[] = [
           "serie" => $impresora['serie'],
           "modelo" => $impresora['modelo'],
           "tipo" => $impresora['tipo'],
           "modeloTinta" => $impresora['modeloTinta'],
           "stock" => $impresora['stock'],
           "disponible" => $impresora['disponible'],
           "tintas" => $tintas
       ];
   }
   echo json_encode($resultado, JSON_PRETTY_PRINT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $serie = $data['serie'];
    $modelo = $data['modelo'];
    $tipo = $data['tipo'];
    $modeloTinta = $data['modeloTinta'];
    $stock = $data['stock'];
    $disponible = $data['disponible'];

    $sql = "INSERT INTO impresoras(serie, modelo, tipo, modeloTinta, stock, disponible) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$serie, $modelo, $tipo, $modeloTinta, $stock, $disponible]);

    echo json_encode(["message" => "Impresora creada con éxito"]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $serie = $data['serie'];
    $modelo = $data['modelo'];
    $tipo = $data['tipo'];
    $modeloTinta = $data['modeloTinta'];
    $sql = "UPDATE impresoras SET modelo = ?, tipo = ?, modeloTinta = ? WHERE serie = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ $modelo, $tipo, $modeloTinta, $serie]);
    echo json_encode(["message" => "Impresora actualizada con éxito"]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['serie']; 
    $sql = "UPDATE impresoras SET disponible = 1 WHERE serie = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    echo json_encode(["message" => "Impresora eliminada con éxito"]);
}
?>