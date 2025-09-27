<?php
// Se incluyen las clases del modelo y del controlador
require_once '../modelo/Producto.php';
require_once '../control/ControlProducto.php';

// Variables iniciales del formulario
$codigo = "";
$nombre = "";
$valorUnitario = "";
$existencia = "";
$accion = "Guardar"; // Acción por defecto del botón principal
$mensaje = "";       // Mensaje para mostrar al usuario

// ===============================
// 1. PROCESA ACCIONES DEL FORMULARIO (cuando se envía con POST)
// ===============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Se lee el botón que se presionó
    $botonAccion = $_POST['btnAccion'];

    if ($botonAccion === "Consultar") {
        // CONSULTAR: solo se necesita el código
        $codigo = $_POST['txtCodigo'];

        // Se crea un objeto producto con solo el código
        $producto = new Producto($codigo, "", 0.0, 0);
        $control = new ControlProducto($producto);

        // Se busca el producto en BD
        $resultado = $control->consultar();
        if ($resultado) {
            // Si se encuentra, se llenan los campos en el formulario
            $codigo = $resultado->getCodigo();
            $nombre = $resultado->getNombre();
            $valorUnitario = $resultado->getValorUnitario();
            $existencia = $resultado->getExistencia();
            $accion = "Modificar"; // Cambia el botón principal a "Modificar"
        } else {
            $mensaje = "<p class='mensaje-error'>Producto no encontrado.</p>";
        }
    } else {
        // GUARDAR o MODIFICAR: se leen todos los campos
        $codigo = $_POST['txtCodigo'];
        $nombre = $_POST['txtNombre'] ?? '';
        $valorUnitario = (float)($_POST['txtValorUnitario'] ?? 0);
        $existencia = (int)($_POST['txtExistencia'] ?? 0);

        // Se crea el objeto producto con todos los datos
        $producto = new Producto($codigo, $nombre, $valorUnitario, $existencia);
        $control = new ControlProducto($producto);

        // Según el botón, se guarda o modifica
        if ($botonAccion === "Guardar" && $control->guardar()) {
            $mensaje = "<p class='mensaje-exito'>Producto guardado correctamente.</p>";
        } elseif ($botonAccion === "Modificar" && $control->modificar()) {
            $mensaje = "<p class='mensaje-exito'>Producto modificado correctamente.</p>";
        } else {
            $mensaje = "<p class='mensaje-error'>No se pudo realizar la acción.</p>";
        }
    }
}

// ===============================
// 2. PROCESA ACCIÓN DE CONSULTAR DESDE LA TABLA (cuando se hace clic en Editar)
// ===============================
if (isset($_GET['accion']) && $_GET['accion'] === 'Consultar' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    // Se crea el objeto producto con solo el código
    $producto = new Producto($codigo, "", 0.0, 0);
    $control = new ControlProducto($producto);

    // Se busca en la BD
    $resultado = $control->consultar();
    if ($resultado) {
        $codigo = $resultado->getCodigo();
        $nombre = $resultado->getNombre();
        $valorUnitario = $resultado->getValorUnitario();
        $existencia = $resultado->getExistencia();
        $accion = "Modificar"; // Cambia el botón principal a "Modificar"
    } else {
        $mensaje = "<p class='mensaje-error'>Producto no encontrado.</p>";
    }
}

// ===============================
// 3. PROCESA ACCIÓN DE BORRAR DESDE LA TABLA
// ===============================
if (isset($_GET['accion']) && $_GET['accion'] === 'Borrar' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    // Se crea un producto con solo el código
    $producto = new Producto($codigo, "", 0.0, 0);
    $control = new ControlProducto($producto);

    // Se elimina de la BD
    if ($control->borrar()) {
        $mensaje = "<p class='mensaje-exito'>Producto eliminado correctamente.</p>";
    } else {
        $mensaje = "<p class='mensaje-error'>No se pudo eliminar el producto.</p>";
    }
}

// ===============================
// 4. CARGA EL LISTADO DE PRODUCTOS
// ===============================
$controlLista = new ControlProducto();
$productos = $controlLista->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: auto; padding: 20px; }
        h1, h2 { text-align: center; }
        form { background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        label, input, button { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; box-sizing: border-box; }
        .botones { text-align: center; }
        .botones button { margin: 0 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #eee; }
        .acciones a { margin-right: 5px; text-decoration: none; color: #007bff; }
        .acciones a.borrar { color: #dc3545; }
        .mensaje-exito { background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; }
        .mensaje-error { background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Gestión de Productos</h1>

    <!-- Muestra mensajes de éxito o error -->
    <?php if ($mensaje): ?>
        <?php echo $mensaje; ?>
    <?php endif; ?>

    <!-- Formulario de productos -->
    <form action="vistaProducto.php" method="POST">
        <h2><?php echo $accion; ?> Producto</h2>
        
        <!-- Campo código -->
        <label for="txtCodigo">Código:</label>
        <input type="text" id="txtCodigo" name="txtCodigo" value="<?php echo htmlspecialchars($codigo); ?>" required>

        <!-- Campo nombre -->
        <label for="txtNombre">Nombre:</label>
        <input type="text" id="txtNombre" name="txtNombre" value="<?php echo htmlspecialchars($nombre); ?>">

        <!-- Campo valor unitario -->
        <label for="txtValorUnitario">Valor Unitario:</label>
        <input type="number" step="0.01" id="txtValorUnitario" name="txtValorUnitario" value="<?php echo htmlspecialchars($valorUnitario); ?>">

        <!-- Campo existencia -->
        <label for="txtExistencia">Existencia:</label>
        <input type="number" id="txtExistencia" name="txtExistencia" value="<?php echo htmlspecialchars($existencia); ?>">

        <!-- Botones -->
        <div class="botones">
            <!-- Botón principal: Guardar o Modificar según el estado -->
            <button type="submit" name="btnAccion" value="<?php echo $accion; ?>"><?php echo $accion; ?></button>
            <!-- Botón extra para consultar -->
            <button type="submit" name="btnAccion" value="Consultar">Consultar</button>
            <!-- Botón de cancelar cuando se está editando -->
            <?php if ($accion == "Modificar"): ?>
                <button type="button" onclick="window.location.href='vistaProducto.php'">Cancelar</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Listado de productos -->
    <h2>Listado de Productos</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Valor Unitario</th>
                <th>Existencia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($productos) {
                foreach ($productos as $producto) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($producto->getCodigo()) . "</td>";
                    echo "<td>" . htmlspecialchars($producto->getNombre()) . "</td>";
                    echo "<td>$" . number_format($producto->getValorUnitario(), 2) . "</td>";
                    echo "<td>" . htmlspecialchars($producto->getExistencia()) . "</td>";
                    echo "<td class='acciones'>";
                    echo "<a href='vistaProducto.php?accion=Consultar&codigo=" . urlencode($producto->getCodigo()) . "'>Editar</a>";
                    echo "<a href='vistaProducto.php?accion=Borrar&codigo=" . urlencode($producto->getCodigo()) . "' class='borrar' onclick='return confirm(\"¿Está seguro de que desea eliminar este producto?\");'>Borrar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No hay productos registrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
