<?php
// ===============================
// 1. INCLUSIÓN DE CLASES
// ===============================
require_once '../modelo/Cliente.php';
require_once '../modelo/Persona.php';
require_once '../modelo/Empresa.php';
require_once '../control/ControlCliente.php';
require_once '../control/ControlEmpresa.php';

// ===============================
// 2. VARIABLES INICIALES
// ===============================
$id = null;
$codigo = "";
$nombre = "";
$telefono = "";
$email = "";
$credito = "";
$fkcodempresa = "";
$accion = "Guardar"; // Acción por defecto del botón principal
$mensaje = "";       // Mensaje que se muestra al usuario

// ===============================
// 3. PROCESAMIENTO DE FORMULARIO (POST)
// ===============================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $botonAccion = $_POST['btnAccion'];

    if ($botonAccion === "Consultar") {
        // Se consulta un cliente a partir de su código de persona
        $codigo = $_POST['txtCodigo'];

        $cliente = new Cliente(null, $codigo, "", "", "", 0.0, null);
        $control = new ControlCliente($cliente);

        $resultado = $control->consultarPorCodigoPersona();
        if ($resultado) {
            $id = $resultado['idCliente'];
            $codigo = $resultado['codigo'];
            $nombre = $resultado['nombre'];
            $telefono = $resultado['telefono'];
            $email = $resultado['email'];
            $credito = $resultado['credito'];
            $fkcodempresa = $resultado['fkcodempresa'];
            $accion = "Modificar";
        } else {
            $mensaje = "<p class='mensaje-error'>Cliente no encontrado.</p>";
        }
    } else {
        // GUARDAR o MODIFICAR
        $id = $_POST['txtId'] ?: null;
        $codigo = $_POST['txtCodigo'];
        $nombre = $_POST['txtNombre'] ?? '';
        $telefono = $_POST['txtTelefono'] ?? '';
        $email = $_POST['txtEmail'] ?? '';
        $credito = (float)($_POST['txtCredito'] ?? 0);
        $fkcodempresa = $_POST['selEmpresa'] ?? null;

        // Se crea objeto Cliente con todos los datos
        $cliente = new Cliente($id, $codigo, $nombre, $telefono, $email, $credito, $fkcodempresa);
        $control = new ControlCliente($cliente);

        if ($botonAccion === "Guardar" && $control->guardar()) {
            $mensaje = "<p class='mensaje-exito'>Cliente guardado correctamente.</p>";
        } elseif ($botonAccion === "Modificar" && $control->modificar()) {
            $mensaje = "<p class='mensaje-exito'>Cliente modificado correctamente.</p>";
        } else {
            $mensaje = "<p class='mensaje-error'>No se pudo realizar la acción.</p>";
        }
    }
}

// ===============================
// 4. PROCESA ACCIÓN CONSULTAR DESDE TABLA
// ===============================
if (isset($_GET['accion']) && $_GET['accion'] === 'Consultar' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $cliente = new Cliente(null, $codigo, "", "", "", 0.0, null);
    $control = new ControlCliente($cliente);

    $resultado = $control->consultarPorCodigoPersona();
    if ($resultado) {
        $id = $resultado['idCliente'];
        $codigo = $resultado['codigo'];
        $nombre = $resultado['nombre'];
        $telefono = $resultado['telefono'];
        $email = $resultado['email'];
        $credito = $resultado['credito'];
        $fkcodempresa = $resultado['fkcodempresa'];
        $accion = "Modificar";
    } else {
        $mensaje = "<p class='mensaje-error'>Cliente no encontrado.</p>";
    }
}

// ===============================
// 5. PROCESA ACCIÓN BORRAR DESDE TABLA
// ===============================
if (isset($_GET['accion']) && $_GET['accion'] === 'Borrar' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $cliente = new Cliente(null, $codigo, "", "", "", 0.0, null);
    $control = new ControlCliente($cliente);

    if ($control->borrar()) {
        $mensaje = "<p class='mensaje-exito'>Cliente eliminado correctamente.</p>";
    } else {
        $mensaje = "<p class='mensaje-error'>No se pudo eliminar el cliente.</p>";
    }
}

// ===============================
// 6. LISTAR CLIENTES Y EMPRESAS
// ===============================
$controlCliente = new ControlCliente(new Cliente(null, "", "", "", "", 0.0, null));
$clientes = $controlCliente->listar();

// Se listan todas las empresas para mostrarlas en un select
$controlEmpresa = new ControlEmpresa();
$empresas = $controlEmpresa->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
    <style>
        body { font-family: sans-serif; max-width: 900px; margin: auto; padding: 20px; }
        h1, h2 { text-align: center; }
        form { background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        label, input, select, button { display: block; margin-bottom: 10px; }
        input[type="text"], input[type="number"], input[type="email"], select { width: 100%; padding: 8px; box-sizing: border-box; }
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
    <h1>Gestión de Clientes</h1>

    <!-- Mensajes -->
    <?php if ($mensaje): ?>
        <?php echo $mensaje; ?>
    <?php endif; ?>

    <!-- Formulario Cliente -->
    <form action="vistaCliente.php" method="POST">
        <h2><?php echo $accion; ?> Cliente</h2>

        <!-- Campo oculto para ID -->
        <input type="hidden" name="txtId" value="<?php echo htmlspecialchars($id ?? ''); ?>">

        <!-- Código -->
        <label for="txtCodigo">Código Persona:</label>
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

        <!-- Crédito -->
        <label for="txtCredito">Crédito:</label>
        <input type="number" step="0.01" id="txtCredito" name="txtCredito" value="<?php echo htmlspecialchars($credito); ?>">

        <!-- Empresa -->
        <label for="selEmpresa">Empresa:</label>
        <select id="selEmpresa" name="selEmpresa">
            <option value="">-- Seleccione una empresa --</option>
            <?php foreach ($empresas as $empresa): ?>
                <option value="<?php echo $empresa['codigo']; ?>" <?php echo ($fkcodempresa == $empresa['codigo']) ? 'selected' : ''; ?>>
                    <?php echo $empresa['nombre']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Botones -->
        <div class="botones">
            <button type="submit" name="btnAccion" value="<?php echo $accion; ?>"><?php echo $accion; ?></button>
            <button type="submit" name="btnAccion" value="Consultar">Consultar</button>
            <?php if ($accion == "Modificar"): ?>
                <button type="button" onclick="window.location.href='vistaCliente.php'">Cancelar</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- Tabla de clientes -->
    <h2>Listado de Clientes</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Crédito</th>
                <th>Empresa</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($clientes): ?>
                <?php foreach ($clientes as $cli): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cli['idCliente']); ?></td>
                        <td><?php echo htmlspecialchars($cli['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($cli['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($cli['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($cli['email']); ?></td>
                        <td><?php echo number_format($cli['credito'], 2); ?></td>
                        <td><?php echo htmlspecialchars($cli['fkcodempresa']); ?></td>
                        <td class="acciones">
                            <a href="vistaCliente.php?accion=Consultar&codigo=<?php echo urlencode($cli['codigo']); ?>">Editar</a>
                            <a href="vistaCliente.php?accion=Borrar&codigo=<?php echo urlencode($cli['codigo']); ?>" class="borrar" onclick="return confirm('¿Está seguro de eliminar este cliente?');">Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No hay clientes registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
