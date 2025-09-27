<?php
// Se incluyen las clases del modelo y del controlador
require_once '../modelo/Empresa.php';
require_once '../control/ControlEmpresa.php';

// Variables iniciales del formulario
$codigo = "";
$nombre = "";
$accion = "Guardar"; // Acción por defecto del botón principal
$mensaje = "";       // Mensaje para mostrar al usuario

// ===============================
// 1. PROCESA ACCIONES DEL FORMULARIO (cuando se envía con POST)
// ===============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $botonAccion = $_POST['btnAccion'];

    if ($botonAccion === "Consultar") {
        // CONSULTAR: solo se necesita el código
        $codigo = $_POST['txtCodigo'];

        $empresa = new Empresa($codigo, "");
        $control = new ControlEmpresa($empresa);

        $resultado = $control->consultar();
        if ($resultado) {
            $codigo = $resultado['codigo'];
            $nombre = $resultado['nombre'];
            $accion = "Modificar";
        } else {
            $mensaje = "<p class='mensaje-error'>Empresa no encontrada.</p>";
        }
    } else {
        // GUARDAR o MODIFICAR
        $codigo = $_POST['txtCodigo'];
        $nombre = $_POST['txtNombre'] ?? '';

        $empresa = new Empresa($codigo, $nombre);
        $control = new ControlEmpresa($empresa);

        if ($botonAccion === "Guardar" && $control->guardar()) {
            $mensaje = "<p class='mensaje-exito'>Empresa guardada correctamente.</p>";
        } elseif ($botonAccion === "Modificar" && $control->modificar()) {
            $mensaje = "<p class='mensaje-exito'>Empresa modificada correctamente.</p>";
        } else {
            $mensaje = "<p class='mensaje-error'>No se pudo realizar la acción.</p>";
        }
    }
}

// ===============================
// 2. PROCESA ACCIÓN DE CONSULTAR DESDE LA TABLA (Editar)
// ===============================
if (isset($_GET['accion']) && $_GET['accion'] === 'Consultar' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    $empresa = new Empresa($codigo, "");
    $control = new ControlEmpresa($empresa);

    $resultado = $control->consultar();
    if ($resultado) {
        $codigo = $resultado['codigo'];
        $nombre = $resultado['nombre'];
        $accion = "Modificar";
    } else {
        $mensaje = "<p class='mensaje-error'>Empresa no encontrada.</p>";
    }
}

// ===============================
// 3. PROCESA ACCIÓN DE BORRAR DESDE LA TABLA
// ===============================
if (isset($_GET['accion']) && $_GET['accion'] === 'Borrar' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    $empresa = new Empresa($codigo, "");
    $control = new ControlEmpresa($empresa);

    if ($control->borrar()) {
        $mensaje = "<p class='mensaje-exito'>Empresa eliminada correctamente.</p>";
    } else {
        $mensaje = "<p class='mensaje-error'>No se pudo eliminar la empresa.</p>";
    }
}

// ===============================
// 4. CARGA EL LISTADO DE EMPRESAS
// ===============================
$controlLista = new ControlEmpresa(new Empresa("", ""));
$empresas = $controlLista->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empresas</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: auto; padding: 20px; }
        h1, h2 { text-align: center; }
        form { background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        label, input, button { display: block; margin-bottom: 10px; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }
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
    <h1>Gestión de Empresas</h1>

    <!-- Muestra mensajes de éxito o error -->
    <?php if ($mensaje): ?>
        <?php echo $mensaje; ?>
    <?php endif; ?>

    <!-- Formulario de empresa -->
    <form action="vistaEmpresa.php" method="POST">
        <h2><?php echo $accion; ?> Empresa</h2>

        <!-- Campo código -->
        <label for="txtCodigo">Código:</label>
        <input type="text" id="txtCodigo" name="txtCodigo" value="<?php echo htmlspecialchars($codigo); ?>" required>

        <!-- Campo nombre -->
        <label for="txtNombre">Nombre:</label>
        <input type="text" id="txtNombre" name="txtNombre" value="<?php echo htmlspecialchars($nombre); ?>">

        <!-- Botones -->
        <div class="botones">
            <button type="submit" name="btnAccion" value="<?php echo $accion; ?>"><?php echo $accion; ?></button>
            <button type="submit" name="btnAccion" value="Consultar">Consultar</button>
            <?php if ($accion == "Modificar"): ?>
                <button type="button" onclick="window.location.href='vistaEmpresa.php'">Cancelar</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Listado de empresas -->
    <h2>Listado de Empresas</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($empresas) {
                foreach ($empresas as $empresa) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($empresa['codigo']) . "</td>";
                    echo "<td>" . htmlspecialchars($empresa['nombre']) . "</td>";
                    echo "<td class='acciones'>";
                    echo "<a href='vistaEmpresa.php?accion=Consultar&codigo=" . urlencode($empresa['codigo']) . "'>Editar</a>";
                    echo "<a href='vistaEmpresa.php?accion=Borrar&codigo=" . urlencode($empresa['codigo']) . "' class='borrar' onclick='return confirm(\"¿Está seguro de que desea eliminar esta empresa?\");'>Borrar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay empresas registradas.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
