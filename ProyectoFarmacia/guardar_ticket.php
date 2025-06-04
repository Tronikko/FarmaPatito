<?php
session_start();

$servidor = "127.0.0.1";
$usuario_db = "root";
$contrasena_db = "1234";
$bd = "Loggin";
$puerto = 3306;

$conn = new mysqli($servidor, $usuario_db, $contrasena_db, $bd, $puerto);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener datos enviados desde JavaScript
$datos = json_decode(file_get_contents("php://input"), true);
$fecha = $datos["fecha"];
$usuario = $datos["usuario"];
$productos = explode("; ", $datos["productos"]);
$total = $datos["total"];

// ✅ Insertar la venta en la BD
$sql = "INSERT INTO ventas (fecha, usuario, productos, total) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $fecha, $usuario, $datos["productos"], $total);
$stmt->execute();
$stmt->close();

// ✅ Actualizar el stock en la tabla `productos`
foreach ($productos as $producto) {
    preg_match('/^(.*?) - (\d+) pieza/', $producto, $matches);
    if ($matches) {
        $nombreProducto = $matches[1];
        $cantidadComprada = intval($matches[2]);

        // Restar la cantidad comprada del stock
        $updateStock = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE nombre = ?");
        $updateStock->bind_param("is", $cantidadComprada, $nombreProducto);
        $updateStock->execute();
        $updateStock->close();
    }
}

$conn->close();
echo "Venta guardada y stock actualizado";
?>