<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mensaje = "";
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

    // 🔹 Verificar si el usuario existe en la BD
    $sql = "SELECT tipo, contrasena FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        // Usuario no encontrado
        $mensaje = "❌ Usuario no registrado.";
        $operacion = "fallido (usuario no existe)";
    } else {
        $row = $resultado->fetch_assoc();

        // Validación: Contraseña incorrecta
        if ($contrasena !== $row['contrasena']) {
            $mensaje = "❌ Contraseña incorrecta.";
            $operacion = "fallido (contraseña incorrecta)";
        } else {
            // Usuario válido, iniciar sesión
            $_SESSION['usuario'] = $usuario;
            $_SESSION['admin'] = ($row['tipo'] === 'admin');

            $mensaje = "✅ Login exitoso.";
            $operacion = "exitoso";

            // Registrar en bitácora y redirigir
            $bitacora_sql = "INSERT INTO bitacora (usuario, fecha_hora, operacion) VALUES (?, ?, ?)";
            $stmt_bitacora = $conn->prepare($bitacora_sql);
            $stmt_bitacora->bind_param("sss", $usuario, $fecha_hora, $operacion);
            $stmt_bitacora->execute();
            $stmt_bitacora->close();

            header("Location: " . ($_SESSION['admin'] ? "admin.php" : "pos.php"));
            exit();
        }
    }

    // 🔹 Registrar intentos fallidos en bitácora
    $bitacora_sql = "INSERT INTO bitacora (usuario, fecha_hora, operacion) VALUES (?, ?, ?)";
    $stmt_bitacora = $conn->prepare($bitacora_sql);
    $stmt_bitacora->bind_param("sss", $usuario, $fecha_hora, $operacion);
    $stmt_bitacora->execute();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            text-align: center;
            padding: 50px;
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
        }
        .container {
            background: rgba(255, 255, 255, 0.15);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            display: inline-block;
            width: 350px;
            backdrop-filter: blur(10px);
        }
        .titulo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .titulo-container img {
            width: 50px;
            height: auto;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }
        input {
            background: rgba(255, 255, 255, 0.8);
            color: black;
        }
        button {
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .login-btn {
            background: #28a745;
            color: white;
        }
        .register-btn {
            background: #17a2b8;
            color: white;
        }
        .log-btn {
            background: #ffc107;
            color: black;
        }
        button:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }
        h2 {
            margin-bottom: 15px;
        }
        p {
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="titulo-container">
        <h2>Farma-Patito</h2>
        <img src="imagenes/pato.jpg" alt="Logo de Farma-Patito">
    </div>

    <form method="post">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button class="login-btn" type="submit" name="login">Ingresar</button>
    </form>

    <button class="register-btn" onclick="window.location.href='registro.php'">Registrar</button>
   
    <p><?php echo $mensaje; ?></p>
</div>

</body>
</html>
