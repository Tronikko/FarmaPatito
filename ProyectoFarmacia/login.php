<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servidor = "127.0.0.1";
$usuario_db = "root";
$contrasena_db = "1234";
$bd = "Loggin";
$puerto = 3306;

$conn = new mysqli($servidor, $usuario_db, $contrasena_db, $bd, $puerto);
if ($conn->connect_error) {
    die("❌ Error de conexión a la BD: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $usuario = trim($_POST["usuario"]);
    $contrasena = trim($_POST["contrasena"]);
    $fecha_hora = date("Y-m-d H:i:s");

    // Consulta para obtener usuario y contraseña en texto plano
    $sql = "SELECT tipo, contrasena FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("❌ Error al preparar la consulta SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si el usuario no existe
    if ($result->num_rows === 0) { 
        echo "<div style='color: red;'>❌ Usuario no registrado.</div>";
    } else {
        // Obtener datos del usuario
        $row = $result->fetch_assoc();

        // Validación: Contraseña incorrecta
        if (!isset($row['contrasena']) || $contrasena !== $row['contrasena']) {
            echo "<div style='color: red;'>❌ Contraseña incorrecta.</div>";
        } else {
            // Configurar sesión para usuario válido
            $_SESSION['usuario'] = $usuario;
            $_SESSION['admin'] = isset($row['tipo']) && trim(strtolower($row['tipo'])) === 'admin';

            // Registrar acceso en bitácora SIN contraseña
            $bitacora_sql = "INSERT INTO bitácora (usuario, fecha_hora, operacion) VALUES (?, ?, ?)";
            $stmt_bitacora = $conn->prepare($bitacora_sql);
            if (!$stmt_bitacora) {
                die("❌ Error al preparar la consulta de bitácora: " . $conn->error);
            }

            $operacion = "exitoso";
            $stmt_bitacora->bind_param("sss", $usuario, $fecha_hora, $operacion);
            $stmt_bitacora->execute();
            $stmt_bitacora->close();

            // Redirección según el tipo de usuario
            header("Location: " . ($_SESSION['admin'] ? "admin.php" : "pos.php"));
            exit();
        }
    }
}

$stmt->close();
$conn->close();
?>
