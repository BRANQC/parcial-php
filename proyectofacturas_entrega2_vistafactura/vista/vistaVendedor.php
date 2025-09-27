<?php
// ===============================
// vistaVendedor.php
// ===============================
// Esta vista permite gestionar vendedores: 
// Guardar, Modificar, Consultar y Borrar.
// Usa la clase Vendedor (modelo) y ControlVendedor (controlador).
// ===============================

// Se incluyen las clases necesarias
require_once '../modelo/Vendedor.php';
require_once '../control/ControlVendedor.php';

// Variables iniciales del formulario
$id = null;
$codigo = "";
$nombre = "";
$telefono = "";
$email = "";
$carnet = "";
$direccion = "";
$accion = "Guardar"; // Acción por defecto del botón principal
$mensaje = "";       // Mensaje de éxito o error para mostrar al usuario

// ===============================
// 1. PROCESA ACCIONES DEL FORMULARIO (cuando se envía con POST)
// ===============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $botonAccion = $_POST['btnAccion'];

    if ($botonAccion === "Consultar") {
        // CONSULTAR: solo necesitamos el ID
        $id = (int)$_POST['txtId'];

        $vendedor = new Vendedor($id, "", "", "", "", null, null);
        $control = new ControlVendedor($vendedor);

        $resultado = $control->consultar();
        if ($resultado) {
            // Se llenan los campos con los datos consultados
            $id = $resultado->getId();
            $codigo = $resultado->getCodigo();
            $nombre = $resultado->getNombre();
            $telefono = $resultado->getTelefono();
            $email = $resultado->getEmail();
            $carnet = $resultado->getCarnet();
            $direccion = $resultado->getDireccion();
            $accion = "Modificar";
        } else {
            $mensaje = "<p class='mensaje-error'>Vendedor no encontrado.</p>";
        }
    } else {
        // GUARDAR o MODIFICAR: se leen todos los campos
        $id = $_POST['txtId'] ? (int)$_POST['txtId'] : null;
        $codigo = $_POST['txtCodigo'] ?? '';
        $nombre = $_POST['txtNombre'] ?? '';
        $telefono = $_POST['txtTelefono'] ?? '';
        $email = $_POST['txtEmail'] ?? '';
        $carnet = $_POST['txtCarnet'] ?? '';
        $direccion = $_POST['txtDireccion'] ?? '';

        $vendedor = new Vendedor($id, $codigo, $nombre, $telefono, $email, $carnet, $direccion);
        $control = new ControlVendedor($vendedor);

        if ($botonAccion === "Guardar" && $control->guardar()) {
            $mensaje = "<p class='mensaje-exito'>Vendedor guardado correctamente.</p>";
        } elseif ($botonAccion === "Modificar" && $control->modificar()) {
            $mensaje = "<p class='mensaje-exito'>Vendedor modificado correctamente.</p>";
        } else {
            $mensaje = "<p class='mensaje-error'>No se pudo realizar la acción.</p>";
        }
    }
}

// ===============================
// 2. PROCESA ACCIÓN DE CONSULTAR DESDE LA TABLA (Editar)
// ===============================
if (isset($_GET['accion']) && $_GET['accion'] === 'Consultar' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $vendedor = new Vendedor($id, "", "", "", "", null, null);
    $control = new ControlVendedor($vendedor);

    $resultado = $control->consultar();
    if ($resultado) {
        $id = $resultado->getId();
        $codigo = $resultado->getCodigo();
        $nombre = $resultado->getNombre();
        $telefono = $resultado->getTelefono();
        $email = $resultado->getEmail();
        $carnet = $resultado->getCarnet();
        $direccion = $resultado->getDireccion();
        $accion = "Modificar";
    } else {
        $mensaje = "<p class='mensaje-error'>Vendedor no encontrado.</p>";
    }
}

// ===============================
// 3. PROCESA ACCIÓN DE BORRAR DESDE LA TABLA
// ===============================
if (isset($_GET['accion']) && $_GET['accion'] === 'Borrar' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    $vendedor = new Vendedor(null, $codigo, "", "", "", null, null);
    $control = new ControlVendedor($vendedor);

    if ($control->borrar()) {
        $mensaje = "<p class='mensaje-exito'>Vendedor eliminado correctamente.</p>";
    } else {
        $mensaje = "<p class='mensaje-error'>No se pudo eliminar el vendedor.</p>";
    }
}

// ===============================
// 4. CARGA EL LISTADO DE VENDEDORES
// ===============================
$controlLista = new ControlVendedor();
$vendedores = $controlLista->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Vendedores</title>
    <style>
        body { font-family: sans-serif; max-width: 900px; margin: auto; padding: 20px; }
        h1, h2 { text-align: center; }
        form { background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        label, input, button { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; box-sizing: border-box; }
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
    <h1>Gestión de Vendedores</h1>

    <!-- Mensaje de éxito o error -->
    <?php if ($mensaje): ?>
        <?php echo $mensaje; ?>
    <?php endif; ?>

    <!-- Formulario de vendedores -->
    <form action="vistaVendedor.php" method="POST">
        <h2><?php echo $accion; ?> Vendedor</h2>

        <!-- Campo oculto ID -->
        <input type="hidden" id="txtId" name="txtId" value="<?php echo htmlspecialchars($id ?? ""); ?>">

        <!-- Código -->
        <label for="txtCodigo">Código:</label>
        <input type="text" id="txtCodigo" name="txtCodigo" value="<?php echo htmlspecialchars($codigo); ?>" required>

        <!-- Nombre -->
        <label for="txtNombre">Nombre:</label>
        <input type="text" id="txtNombre" name="txtNombre" value="<?php echo htmlspecialchars($nombre); ?>">

        <!-- Teléfono -->
        <label for="txtTelefono">Teléfono:</label>
        <input type="text" id="txtTelefono" name="txtTelefono" value="<?php echo htmlspecialchars($telefono); ?>">

        <!-- Email -->
        <label for="txtEmail">Email:</label>
        <input type="email" id="txtEmail" name="txtEmail" value="<?php echo htmlspecialchars($email); ?>">

        <!-- Carnet -->
        <label for="txtCarnet">Carnet:</label>
        <input type="text" id="txtCarnet" name="txtCarnet" value="<?php echo htmlspecialchars($carnet); ?>">

        <!-- Dirección -->
        <label for="txtDireccion">Dirección:</label>
        <input type="text" id="txtDireccion" name="txtDireccion" value="<?php echo htmlspecialchars($direccion); ?>">

        <!-- Botones -->
        <div class="botones">
            <button type="submit" name="btnAccion" value="<?php echo $accion; ?>"><?php echo $accion; ?></button>
            <button type="submit" name="btnAccion" value="Consultar">Consultar</button>
            <?php if ($accion == "Modificar"): ?>
                <button type="button" onclick="window.location.href='vistaVendedor.php'">Cancelar</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Listado de vendedores -->
    <h2>Listado de Vendedores</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Carnet</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($vendedores) {
                foreach ($vendedores as $vend) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($vend->getId()) . "</td>";
                    echo "<td>" . htmlspecialchars($vend->getCodigo()) . "</td>";
                    echo "<td>" . htmlspecialchars($vend->getNombre()) . "</td>";
                    echo "<td>" . htmlspecialchars($vend->getTelefono()) . "</td>";
                    echo "<td>" . htmlspecialchars($vend->getEmail()) . "</td>";
                    echo "<td>" . htmlspecialchars($vend->getCarnet()) . "</td>";
                    echo "<td>" . htmlspecialchars($vend->getDireccion()) . "</td>";
                    echo "<td class='acciones'>";
                    echo "<a href='vistaVendedor.php?accion=Consultar&id=" . urlencode($vend->getId()) . "'>Editar</a>";
                    echo "<a href='vistaVendedor.php?accion=Borrar&codigo=" . urlencode($vend->getCodigo()) . "' class='borrar' onclick='return confirm(\"¿Está seguro de que desea eliminar este vendedor?\");'>Borrar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No hay vendedores registrados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
