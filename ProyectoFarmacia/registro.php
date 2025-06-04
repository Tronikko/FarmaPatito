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

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["registro"])) {
    $usuario = trim($_POST["usuario"]);
    $contrasena = trim($_POST["contrasena"]);
    $fecha_hora = date("Y-m-d H:i:s");

    if (!empty($usuario) && !empty($contrasena)) {
        $sql = "INSERT INTO usuarios (usuario, contrasena) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("❌ Error al preparar la consulta SQL: " . $conn->error);
        }

        $stmt->bind_param("ss", $usuario, $contrasena);

        if ($stmt->execute()) {
            $mensaje = "✅ Registro exitoso. Serás redirigido en unos segundos...";

            // **Registrar en bitácora**
            $bitacora_sql = "INSERT INTO bitacora (usuario, contrasena, fecha_hora, operacion) VALUES (?, ?, ?, 'registrado')";
            $stmt_bitacora = $conn->prepare($bitacora_sql);
            if (!$stmt_bitacora) {
                die("❌ Error al preparar la consulta de bitácora: " . $conn->error);
            }

            $contraseña_oculta = str_repeat('*', strlen($contrasena)); // Ocultar contraseña
            $stmt_bitacora->bind_param("sss", $usuario, $contraseña_oculta, $fecha_hora);

            if (!$stmt_bitacora->execute()) {
                die("❌ Error al insertar en bitácora: " . $stmt_bitacora->error);
            }

            $stmt_bitacora->close();
            $stmt->close();
            $conn->close();

            // Mostrar mensaje y redirigir después de unos segundos
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000); // Redirigir después de 3 segundos
            </script>";

        } else {
            $mensaje = "❌ Error en el registro.";
        }

    } else {
        $mensaje = "❌ Todos los campos son obligatorios.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        /* Estilos generales */
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
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            display: inline-block;
            width: 320px;
            backdrop-filter: blur(10px);
        }
        h2 {
            margin-bottom: 15px;
            font-size: 22px;
        }
        input, button {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 8px;
            font-size: 14px;
        }
        input {
            background: rgba(255, 255, 255, 0.8);
            color: black;
            text-align: center;
        }
        button {
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .register-btn {
            background: #28a745;
            color: white;
        }
        .back-btn {
            background: #007bff;
            color: white;
            font-size: 14px;
        }
        button:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }
        .mensaje {
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Registro de Usuario</h2>
    <form method="post">
        <input type="text" name="usuario" placeholder="Nuevo Usuario" required>
        <input type="password" name="contrasena" placeholder="Contraseña" required>
        <button class="register-btn" type="submit" name="registro">✅ Registrar</button>
    </form>
    <p class="mensaje"><?php echo $mensaje; ?></p>

    <!-- Botón de regresar al index -->
    <button class="back-btn" onclick="window.location.href='index.php'">⬅ Regresar</button>
</div>

</body>
</html>