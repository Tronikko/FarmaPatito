<?php
session_start();
$conn = new mysqli("127.0.0.1", "root", "1234", "Loggin", 3306);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '';
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';

$sql = "SELECT fecha, SUM(total) AS total_diario FROM ventas ";
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= "WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' ";
}
$sql .= "GROUP BY fecha ORDER BY fecha ASC";

$result = $conn->query($sql);

$fechas = [];
$totales = [];

while ($fila = $result->fetch_assoc()) {
    $fechas[] = $fila['fecha'];
    $totales[] = $fila['total_diario'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas Diarias</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f0f2f5;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        #reporte {
            width: 90%;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        td {
            background: #f8f9fa;
            color: #333;
        }
        .btn {
            display: inline-block;
            margin: 15px 5px;
            padding: 10px 16px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #reporte, #reporte * {
                visibility: visible;
            }
            #reporte {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <h2>📈 Reporte de Ventas Diarias</h2>

    <form method="POST">
        <label for="fecha_inicio">Fecha Inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" required>
        
        <label for="fecha_fin">Fecha Fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" required>
        
        <button type="submit" class="btn">🔎 Filtrar</button>
    </form>

    <div id="reporte">
        <table>
            <tr>
                <th>Fecha</th>
                <th>Total de Ventas</th>
            </tr>
            <?php foreach ($fechas as $i => $fecha): ?>
                <tr>
                    <td><?= htmlspecialchars($fecha) ?></td>
                    <td>$<?= number_format($totales[$i], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <canvas id="ventasChart"></canvas>
    </div>

    <button class="btn" onclick="window.print()">🖨 Imprimir Reporte</button>
    <a href="admin.php" class="btn">⬅ Volver a Admin</a>
    <a href="productos.php" class="btn">⬅ Volver a Tienda</a>

    <script>
        var ctx = document.getElementById('ventasChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($fechas) ?>,
                datasets: [{
                    label: 'Ventas Diarias',
                    data: <?= json_encode($totales) ?>,
                    backgroundColor: 'rgba(0,123,255,0.5)',
                    borderColor: 'rgba(0,123,255,1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>

</body>
</html>