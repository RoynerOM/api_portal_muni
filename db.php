<?php
$host = 'localhost'; 
$db = 'adminupala_turismo';
$user = 'adminupala_usr'; 
$pass = 'zNNF523YSvB5';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>

