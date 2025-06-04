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

// **Consultar bitácora general con accesos y modificaciones**
$sql = "SELECT usuario, fecha_hora, operacion FROM bitacora ORDER BY fecha_hora DESC";
$result = $conn->query($sql);
if (!$result) {
    die("❌ Error en la consulta SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora General</title>
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
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            display: inline-block;
            width: 80%;
            backdrop-filter: blur(10px);
        }
        h2 {
            margin-bottom: 15px;
            font-size: 24px;
        }
        /* Botón con apariencia real */
        .btn-back {
            display: inline-block;
            margin: 10px auto;
            padding: 6px 10px; /* Ajuste para hacerlo más compacto */
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            text-align: center;
        }
        .btn-back:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 16px;
            color: black;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .empty {
            color: #ffc107;
            font-weight: bold;
            margin-top: 15px;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .success {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Bitácora General</h2>

    <!-- Botón con apariencia real debajo del título -->
    <a href="index.php" class="btn-back">⬅ Volver al Inicio</a>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Usuario</th>
                <th>Fecha y Hora</th>
                <th>Operación</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_hora']); ?></td>
                    <td class="<?php echo (strpos($row['operacion'], 'fallido') !== false) ? 'error' : 'success'; ?>">
                        <?php echo htmlspecialchars($row['operacion']); ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="empty">⚠ No hay registros en la bitácora.</p>
    <?php endif; ?>
</div>

</body>
</html>
