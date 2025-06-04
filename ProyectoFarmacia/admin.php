<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// **Verificación de sesión y permisos**
if (!isset($_SESSION['usuario'])) {
    $_SESSION['mensaje'] = "❌ Debes iniciar sesión.";
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    $_SESSION['mensaje'] = "❌ No tienes permisos para acceder a esta sección.";
    header("Location: pos.php");
    exit();
}

// **Configuración de la conexión a la base de datos**
$servidor = "127.0.0.1";
$usuario_db = "root";
$contrasena_db = "1234";
$bd = "Loggin";
$puerto = 3306;

$conn = new mysqli($servidor, $usuario_db, $contrasena_db, $bd, $puerto);
if ($conn->connect_error) {
    die("<div style='color: red;'>❌ Error de conexión a la BD: " . $conn->connect_error . "</div>");
}


$vista = isset($_GET['vista']) ? $_GET['vista'] : 'usuarios';
$admin_usuario = $_SESSION["usuario"]; // Captura el usuario que realiza la acción

// **Funciones**
// Registrar en la bitácora
function registrarBitacora($conn, $usuario, $operacion, $detalles) {
    $bitacora_sql = "INSERT INTO bitacora (usuario, operacion, detalles, fecha_hora) VALUES (?, ?, ?, NOW())";
    $bitacora_stmt = $conn->prepare($bitacora_sql);
    $bitacora_stmt->bind_param("sss", $usuario, $operacion, $detalles);

    if (!$bitacora_stmt->execute()) {
        echo "<script>alert('⚠ Error al registrar en la bitácora: " . $conn->error . "');</script>";
    }
}

// **Manejo de usuarios**
// Modificar usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirmarModificacion"])) {
    if (!empty($_POST["usuario_actual"]) && !empty($_POST["nuevo_usuario"]) && !empty($_POST["tipo"]) && !empty($_POST["contrasena"])) {
        $usuario_actual = $_POST["usuario_actual"];
        $nuevo_usuario = $_POST["nuevo_usuario"];
        $nuevo_tipo = trim(strtolower($_POST["tipo"]));
        $nueva_contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET usuario = ?, tipo = ?, contrasena = ? WHERE usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nuevo_usuario, $nuevo_tipo, $nueva_contrasena, $usuario_actual);

        if ($stmt->execute()) {
            $detalles = "Usuario actualizado: $usuario_actual → $nuevo_usuario, Tipo: $nuevo_tipo";
            registrarBitacora($conn, $admin_usuario, "MODIFICÓ USUARIO", $detalles);
            echo "<script>alert('✅ Usuario modificado correctamente.');</script>";
        }
    }
}

// Eliminar usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminarUsuario"])) {
    $usuario_eliminar = $_POST["usuario_eliminar"];

    $sql = "DELETE FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario_eliminar);

    if ($stmt->execute()) {
        registrarBitacora($conn, $admin_usuario, "ELIMINÓ USUARIO", "Usuario eliminado: $usuario_eliminar");
        echo "<script>alert('✅ Usuario eliminado correctamente.');</script>";
    }
}

// **Manejo de productos**
// Agregar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregarProducto"])) {
    if (!empty($_POST["nombre_producto"]) && !empty($_POST["precio"]) && !empty($_POST["stock"]) && !empty($_POST["descripcion"])) {
        $nombre_producto = $_POST["nombre_producto"];
        $precio = $_POST["precio"];
        $stock = $_POST["stock"];
        $descripcion = $_POST["descripcion"];

        $sql = "INSERT INTO productos (nombre, precio, stock, descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdis", $nombre_producto, $precio, $stock, $descripcion);

        if ($stmt->execute()) {
            registrarBitacora($conn, $admin_usuario, "AGREGÓ PRODUCTO", "Producto agregado: $nombre_producto, Precio: $precio, Stock: $stock");
            echo "<script>alert('✅ Producto agregado correctamente.');</script>";
        }
    }
}

// Modificar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modificarProducto"])) {
    if (!empty($_POST["id_producto"])) {
        $id = $_POST["id_producto"];
        $nombre_nuevo = $_POST["nombre_producto"];
        $precio_nuevo = $_POST["precio"];
        $stock_nuevo = $_POST["stock"];
        $descripcion_nueva = $_POST["descripcion"];

        // **Obtener valores anteriores**
        $stmt_producto = $conn->prepare("SELECT nombre, precio, stock, descripcion FROM productos WHERE id=?");
        $stmt_producto->bind_param("i", $id);
        $stmt_producto->execute();
        $result = $stmt_producto->get_result();
        $producto = $result->fetch_assoc();

        if ($producto) {
            $cambios = [];
            if ($nombre_nuevo != $producto["nombre"]) $cambios[] = "Nombre: {$producto['nombre']} → $nombre_nuevo";
            if ($precio_nuevo != $producto["precio"]) $cambios[] = "Precio: {$producto['precio']} → $precio_nuevo";
            if ($stock_nuevo != $producto["stock"]) $cambios[] = "Stock: {$producto['stock']} → $stock_nuevo";
            if ($descripcion_nueva != $producto["descripcion"]) $cambios[] = "Descripción modificada";

            $sql = "UPDATE productos SET nombre=?, precio=?, stock=?, descripcion=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdssi", $nombre_nuevo, $precio_nuevo, $stock_nuevo, $descripcion_nueva, $id);

            if ($stmt->execute()) {
                $detalles = implode(", ", $cambios);
                registrarBitacora($conn, $admin_usuario, "MODIFICÓ PRODUCTO", $detalles);
                echo "<script>alert('✏ Producto modificado correctamente.');</script>";
            }
        }
    }
}

// Eliminar producto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminarProducto"])) {
    if (!empty($_POST["id_producto"])) {
        $id = $_POST["id_producto"];

        $stmt_producto = $conn->prepare("SELECT nombre FROM productos WHERE id=?");
        $stmt_producto->bind_param("i", $id);
        $stmt_producto->execute();
        $result = $stmt_producto->get_result();
        $producto = $result->fetch_assoc();

        if ($producto) {
            $nombre_producto = $producto["nombre"];

            $sql = "DELETE FROM productos WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                registrarBitacora($conn, $admin_usuario, "ELIMINÓ PRODUCTO", "Producto eliminado: $nombre_producto");
                echo "<script>alert('❌ Producto eliminado correctamente.');</script>";
            }
        }
    }
}

// **Obtener datos para mostrar**
$usuarios = $conn->query("SELECT usuario, tipo FROM usuarios ORDER BY tipo DESC, usuario ASC");
$bitacora = $conn->query("SELECT usuario, operacion, detalles, fecha_hora FROM bitacora ORDER BY fecha_hora DESC");
$productos = $conn->query("SELECT id, nombre, precio, stock, descripcion FROM productos ORDER BY id DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 15px #aaa; display: inline-block; width: 80%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #007bff; color: white; }
        .menu-btn { margin: 10px; padding: 10px; background-color: #007bff; color: white; border: none; cursor: pointer; }
        .btn-back { display: block; margin: 20px auto; padding: 10px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; width: fit-content; }
    </style>
</head>
<body>

<div class="container">
    <h2>Panel de Administración</h2>

    <div>
        <a href="?vista=usuarios"><button class="menu-btn">Modificar Usuarios</button></a>
        <a href="?vista=bitacora"><button class="menu-btn">Ver Bitácora</button></a>
        <a href="?vista=productos"><button class="menu-btn">Administrar Productos</button></a>
        <a href="Reportes.php" class="btn">📊 Ver Reportes</a>
    </div>

    <?php if ($vista == "usuarios"): ?>
    <h2>Gestión de Usuarios</h2>
    <table>
        <tr><th>Usuario</th><th>Tipo</th><th>Acciones</th></tr>
        <?php while ($row = $usuarios->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                <td>
                    <div style="display: flex; gap: 10px;">
                        <form method="post">
                            <input type="hidden" name="usuario_actual" value="<?php echo $row['usuario']; ?>">
                            <button type="submit" name="editarUsuario" style="background-color: #ffc107; color: black; border: none; padding: 8px 15px; cursor: pointer; border-radius: 5px; font-weight: bold;">✏ Modificar</button>
                        </form>
                        <form method="post" onsubmit="return confirm('¿Seguro que quieres eliminar a <?php echo $row['usuario']; ?>?')">
                            <input type="hidden" name="usuario_eliminar" value="<?php echo $row['usuario']; ?>">
                            <button type="submit" name="eliminarUsuario" style="background-color: #dc3545; color: white; border: none; padding: 8px 15px; cursor: pointer; border-radius: 5px; font-weight: bold;">🗑 Eliminar</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>

<?php
// **Formulario de edición de usuario (solo se muestra si se presionó "Modificar")**
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editarUsuario"])) {
    $usuario_actual = $_POST["usuario_actual"];
    $conn = new mysqli($servidor, $usuario_db, $contrasena_db, $bd, $puerto);
    if ($conn->connect_error) {
        die("❌ Error de conexión a la BD: " . $conn->connect_error);
    }
    
    $datos_usuario = $conn->query("SELECT usuario, tipo FROM usuarios WHERE usuario = '$usuario_actual'")->fetch_assoc();
    ?>
    <h2>Modificar Usuario</h2>
    <form method="post" style="background-color: #f8f9fa; padding: 15px; border-radius: 10px; box-shadow: 0px 0px 10px #ccc;">
        <input type="hidden" name="usuario_actual" value="<?php echo $usuario_actual; ?>">
        <label>Usuario:</label>
        <input type="text" name="nuevo_usuario" value="<?php echo htmlspecialchars($datos_usuario['usuario']); ?>" required style="margin-bottom: 10px; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
        <label>Tipo:</label>
        <select name="tipo" style="margin-bottom: 10px; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <option value="admin" <?php echo ($datos_usuario['tipo'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
            <option value="usuario" <?php echo ($datos_usuario['tipo'] == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
        </select>
        <label>Contraseña:</label>
        <input type="password" name="contrasena" required style="margin-bottom: 10px; padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
        <button type="submit" name="confirmarModificacion" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 5px; font-weight: bold;">✅ Guardar Cambios</button>
    </form>
    <?php
}


// **Bitacora**
?>
<?php if ($vista == "bitacora"): ?>
    <h2>Registro de Bitácora</h2>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Operación</th>
            <th>Detalles</th>
            <th>Fecha y Hora</th>
        </tr>
        <?php while ($row = $bitacora->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['usuario'] ?? 'Desconocido', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="<?php echo (strpos($row['operacion'], 'fallido') !== false) ? 'error' : 'success'; ?>">
                    <?php echo htmlspecialchars($row['operacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td><?php echo htmlspecialchars($row['detalles'] ?? 'Sin detalles', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($row['fecha_hora'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>




<!-- Boton de regreso  -->
<a href="index.php" class="btn-back">⬅ Volver al Inicio</a>


</div>
    <!-- Tabla con productos -->

    <?php if ($vista == "productos"): ?>
    <h2>Administración de Productos</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Descripción</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </tr>
        <?php 
        while ($producto = $productos->fetch_assoc()): 
            $directorio_imagenes = "../imagenes/";
            $imagenes_disponibles = [
                4 => "paracetamol.jpg",
                6 => "omeprazol.jpg",
                7 => "loratadina.jpg",
                8 => "amoxicilina.jpg",
                9 => "diclofenaco.jpg",
                10 => "salbutamol.jpg",
                11 => "metformina.jpg",
                12 => "ranitidina.jpg",
                13 => "vitamina_c.jpg",
                14 => "ibuprofeno.jpg"
            ];

            // Obtener imagen según el ID del producto, si no existe, usar imagen por defecto
            $imagen = isset($imagenes_disponibles[$producto["id"]]) ? $imagenes_disponibles[$producto["id"]] : "imagen_por_defecto.jpg";
            $ruta_imagen = $directorio_imagenes . $imagen;
        ?>
            <tr>
                <td><?= $producto["id"] ?></td>
                <td><?= htmlspecialchars($producto["nombre"]) ?></td>
                <td><?= number_format($producto["precio"], 2) ?></td>
                <td><?= $producto["stock"] ?></td>
                <td><?= htmlspecialchars($producto["descripcion"]) ?></td>
                <td>
                    <img src="<?= $ruta_imagen ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" width="100">
                </td>
                <td>
                    <!-- Botón para mostrar campos de modificación -->
                    <button onclick="mostrarFormulario('formModificar<?= $producto['id'] ?>')" style="background-color: orange; color: white; padding: 10px; border: none; cursor: pointer;">
                        ✏ Modificar
                    </button>

                    <!-- Formulario de modificación (oculto por defecto) -->
                    <form method="POST" id="formModificar<?= $producto['id'] ?>" style="display:none;">
                        <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                        <input type="text" name="nombre_producto" value="<?= htmlspecialchars($producto['nombre']) ?>">
                        <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>">
                        <input type="number" name="stock" value="<?= $producto['stock'] ?>">
                        <textarea name="descripcion"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                        <button type="submit" name="modificarProducto" style="background-color: orange; color: white; padding: 10px; border: none; cursor: pointer;">
                            ✅ Guardar Cambios
                        </button>
                    </form>

                    <!-- Botón para eliminar con confirmación -->
                    <form method="POST" onsubmit="return confirmarEliminacion();">
                        <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                        <button type="submit" name="eliminarProducto" style="background-color: red; color: white; padding: 10px; border: none; cursor: pointer;">
                            ❌ Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>


<!-- Funciones JavaScript -->
<script>
function mostrarFormulario(idFormulario) {
    var formulario = document.getElementById(idFormulario);
    formulario.style.display = formulario.style.display === "none" ? "block" : "none";
}

function confirmarEliminacion() {
    return confirm("⚠ ¿Estás seguro de que quieres eliminar este producto?");
}
</script>

</body>
</html>

