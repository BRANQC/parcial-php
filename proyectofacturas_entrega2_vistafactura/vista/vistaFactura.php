<?php
/**
 * Vista de Gestión de Facturas
 * Funcionalidades:
 * - Crear nueva factura con ID autoincremental
 * - Consultar factura por ID
 * - Editar factura existente
 * - Eliminar factura
 * - Manejo dinámico de productos
 */

// Incluir clases necesarias
require_once '../control/ControlConexionPdo.php';
require_once '../modelo/Cliente.php';
require_once '../control/ControlCliente.php';
require_once '../modelo/Vendedor.php';
require_once '../control/ControlVendedor.php';
require_once '../modelo/Producto.php';
require_once '../control/ControlProducto.php';

// Variables iniciales
$numero = "";
$fecha = date('Y-m-d');
$clienteSeleccionado = "";
$vendedorSeleccionado = "";
$total = 0.0;
$accion = "nueva"; // nueva, consultar, editar
$mensaje = "";
$productosFactura = [];

// Manejar mensajes de redirección
if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'factura_guardada':
            $numeroFactura = $_GET['numero'] ?? 'N/A';
            $totalFactura = $_GET['total'] ?? '0';
            $mensaje = "<div class='mensaje-exito'>Factura #$numeroFactura guardada correctamente con un total de $" . number_format($totalFactura, 2) . "</div>";
            break;
        case 'factura_actualizada':
            $numeroFactura = $_GET['numero'] ?? 'N/A';
            $mensaje = "<div class='mensaje-exito'>Factura #$numeroFactura actualizada correctamente</div>";
            break;
        case 'factura_eliminada':
            $numeroFactura = $_GET['numero'] ?? 'N/A';
            $mensaje = "<div class='mensaje-exito'>Factura #$numeroFactura eliminada correctamente</div>";
            break;
    }
}

// Inicializar arrays
$clientes = [];
$vendedores = [];
$productos = [];
$facturas = [];


// Función para cargar productos de una factura
function cargarProductosFactura($numeroFactura) {
    try {
        $objControlConexionPdo = new ControlConexionPdo();
        $objControlConexionPdo->abrirBd();
        
        // Primero verificar si existen productos para esta factura
        $sqlCheck = "SELECT COUNT(*) as total FROM productosporfactura WHERE fknumfactura = ?";
        $checkResult = $objControlConexionPdo->ejecutarSelect($sqlCheck, [$numeroFactura]);
        
        if ($checkResult[0]['total'] == 0) {
            $objControlConexionPdo->cerrarBd();
            return [];
        }
        
        $sql = "SELECT ppf.fknumfactura, ppf.fkcodproducto, ppf.cantidad, ppf.subtotal, 
                p.nombre, p.valorunitario
                FROM productosporfactura ppf 
                JOIN producto p ON ppf.fkcodproducto = p.codigo 
                WHERE ppf.fknumfactura = ?
                ORDER BY p.nombre";
        $productos = $objControlConexionPdo->ejecutarSelect($sql, [$numeroFactura]);
        
        $objControlConexionPdo->cerrarBd();
        return $productos ? $productos : [];
    } catch (Exception $e) {
        return [];
    }
}

// PROCESAR ACCIONES
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accionPost = $_POST['accion'];
    
    if ($accionPost === "guardar") {
        // GUARDAR NUEVA FACTURA
        $fecha = $_POST['fecha'];
        $clienteSeleccionado = $_POST['cliente'];
        $vendedorSeleccionado = $_POST['vendedor'];
        $productosPost = $_POST['productos'] ?? [];
        $cantidadesPost = $_POST['cantidades'] ?? [];
        $preciosPost = $_POST['precios'] ?? [];
        
        if (!empty($clienteSeleccionado) && !empty($vendedorSeleccionado) && !empty($productosPost)) {
            $objControlConexionPdo = null;
            $transaccionIniciada = false;
            
            try {
                $objControlConexionPdo = new ControlConexionPdo();
                $objControlConexionPdo->abrirBd();
                $objControlConexionPdo->beginTransaction();
                $transaccionIniciada = true;
                
                // Calcular total
                $totalCalculado = 0;
                for ($i = 0; $i < count($productosPost); $i++) {
                    if (!empty($productosPost[$i])) {
                        $totalCalculado += $cantidadesPost[$i] * $preciosPost[$i];
                    }
                }
                
                // Insertar factura (solo fecha sin hora)
                $sqlFactura = "INSERT INTO factura (fecha, total, fkidcliente, fkidvendedor) VALUES (DATE(?), ?, ?, ?)";
                $objControlConexionPdo->ejecutarComandoSql($sqlFactura, [$fecha, $totalCalculado, $clienteSeleccionado, $vendedorSeleccionado]);
                
                // Obtener el ID de la factura recién insertada
                $sqlUltimaFactura = "SELECT LAST_INSERT_ID() as numero";
                $resultUltimaFactura = $objControlConexionPdo->ejecutarSelect($sqlUltimaFactura);
                $numeroFactura = $resultUltimaFactura[0]['numero'];
                
                // Insertar productos
                $sqlProducto = "INSERT INTO productosporfactura (fknumfactura, fkcodproducto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
                for ($i = 0; $i < count($productosPost); $i++) {
                    if (!empty($productosPost[$i])) {
                        $subtotal = $cantidadesPost[$i] * $preciosPost[$i];
                        $objControlConexionPdo->ejecutarComandoSql($sqlProducto, [
                            $numeroFactura, 
                            $productosPost[$i], 
                            $cantidadesPost[$i], 
                            $subtotal
                        ]);
                    }
                }
                
                $objControlConexionPdo->commit();
                $transaccionIniciada = false;
                
                // Redirigir para evitar reenvío del formulario
                header("Location: vistaFactura.php?mensaje=factura_guardada&numero=" . $numeroFactura . "&total=" . $totalCalculado);
                exit();
                
            } catch (Exception $e) {
                if ($transaccionIniciada && $objControlConexionPdo) {
                    $objControlConexionPdo->rollBack();
                }
                $mensaje = "<div class='mensaje-error'> Error al guardar factura: " . $e->getMessage() . "</div>";
            } finally {
                if ($objControlConexionPdo) {
                    $objControlConexionPdo->cerrarBd();
                }
            }
        } else {
            $mensaje = "<div class='mensaje-error'> Complete todos los campos requeridos</div>";
        }
        
    } elseif ($accionPost === "consultar") {
        // CONSULTAR FACTURA
        $numeroConsulta = $_POST['numero'];
        if (!empty($numeroConsulta)) {
            try {
                $objControlConexionPdo = new ControlConexionPdo();
                $objControlConexionPdo->abrirBd();
                
                $sql = "SELECT f.numero, DATE(f.fecha) as fecha, f.total, f.fkidcliente, f.fkidvendedor,
                        pc.nombre as cliente_nombre, pv.nombre as vendedor_nombre 
                        FROM factura f 
                        JOIN cliente cl ON f.fkidcliente = cl.id 
                        JOIN persona pc ON cl.fkcodpersona = pc.codigo
                        JOIN vendedor vd ON f.fkidvendedor = vd.id 
                        JOIN persona pv ON vd.fkcodpersona = pv.codigo
                        WHERE f.numero = ?";
                $result = $objControlConexionPdo->ejecutarSelect($sql, [$numeroConsulta]);
                
                if ($result) {
                    $facturaInfo = $result[0];
                    $productosInfo = cargarProductosFactura($numeroConsulta);
                    
                    
                    $mensaje = "<div class='consulta-resultado'>
                        <h3>Factura #" . $facturaInfo['numero'] . "</h3>
                        <div class='info-factura'>
                            <p><strong>Fecha:</strong> " . $facturaInfo['fecha'] . "</p>
                            <p><strong>Cliente:</strong> " . $facturaInfo['cliente_nombre'] . "</p>
                            <p><strong>Vendedor:</strong> " . $facturaInfo['vendedor_nombre'] . "</p>
                            <p><strong>Total:</strong> $" . number_format($facturaInfo['total'], 2) . "</p>
                        </div>
                        <div class='productos-factura'>
                            <h4>Productos:</h4>
                            <table class='tabla-productos-consulta'>
                                <tr><th>Producto</th><th>Cantidad</th><th>Subtotal</th></tr>";
                    
                    if (!empty($productosInfo)) {
                        foreach ($productosInfo as $prod) {
                            $mensaje .= "<tr>
                                <td>" . htmlspecialchars($prod['nombre']) . " (" . htmlspecialchars($prod['fkcodproducto']) . ")</td>
                                <td>" . $prod['cantidad'] . "</td>
                                <td>$" . number_format($prod['subtotal'], 2) . "</td>
                            </tr>";
                        }
                    } else {
                        $mensaje .= "<tr><td colspan='3' style='text-align: center; color: #999;'>No hay productos en esta factura</td></tr>";
                    }
                    
                    $mensaje .= "</table>
                        </div>
                        <div class='acciones-consulta'>
                            <button onclick=\"editarFactura(" . $numeroConsulta . ")\" class='btn btn-primary btn-sm'>Editar</button>
                            <button onclick=\"eliminarFactura(" . $numeroConsulta . ")\" class='btn btn-danger btn-sm'>Eliminar</button>
                        </div>
                    </div>";
                } else {
                    $mensaje = "<div class='mensaje-error'> Factura #$numeroConsulta no encontrada</div>";
                }
                
                $objControlConexionPdo->cerrarBd();
                
            } catch (Exception $e) {
                $mensaje = "<div class='mensaje-error'> Error al consultar: " . $e->getMessage() . "</div>";
            }
        }
        
    } elseif ($accionPost === "actualizar") {
        // ACTUALIZAR FACTURA EDITADA
        $numeroActualizar = $_POST['numero'];
        $fecha = $_POST['fecha'];
        $clienteSeleccionado = $_POST['cliente'];
        $vendedorSeleccionado = $_POST['vendedor'];
        $productosPost = $_POST['productos'] ?? [];
        $cantidadesPost = $_POST['cantidades'] ?? [];
        $preciosPost = $_POST['precios'] ?? [];
        
        if (!empty($numeroActualizar) && !empty($clienteSeleccionado) && !empty($vendedorSeleccionado)) {
            $objControlConexionPdo = null;
            $transaccionIniciada = false;
            
            try {
                $objControlConexionPdo = new ControlConexionPdo();
                $objControlConexionPdo->abrirBd();
                $objControlConexionPdo->beginTransaction();
                $transaccionIniciada = true;
                
                // Calcular nuevo total
                $totalCalculado = 0;
                for ($i = 0; $i < count($productosPost); $i++) {
                    if (!empty($productosPost[$i])) {
                        $totalCalculado += $cantidadesPost[$i] * $preciosPost[$i];
                    }
                }
                
                // Actualizar factura (solo fecha sin hora)
                $sqlActualizarFactura = "UPDATE factura SET fecha = DATE(?), total = ?, fkidcliente = ?, fkidvendedor = ? WHERE numero = ?";
                $objControlConexionPdo->ejecutarComandoSql($sqlActualizarFactura, [$fecha, $totalCalculado, $clienteSeleccionado, $vendedorSeleccionado, $numeroActualizar]);
                
                // Eliminar productos anteriores
                $sqlEliminarProductos = "DELETE FROM productosporfactura WHERE fknumfactura = ?";
                $objControlConexionPdo->ejecutarComandoSql($sqlEliminarProductos, [$numeroActualizar]);
                
                // Insertar productos nuevos
                $sqlInsertarProducto = "INSERT INTO productosporfactura (fknumfactura, fkcodproducto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
                for ($i = 0; $i < count($productosPost); $i++) {
                    if (!empty($productosPost[$i])) {
                        $subtotal = $cantidadesPost[$i] * $preciosPost[$i];
                        $objControlConexionPdo->ejecutarComandoSql($sqlInsertarProducto, [
                            $numeroActualizar, 
                            $productosPost[$i], 
                            $cantidadesPost[$i], 
                            $subtotal
                        ]);
                    }
                }
                
                $objControlConexionPdo->commit();
                $transaccionIniciada = false;
                
                // Redirigir para evitar reenvío del formulario
                header("Location: vistaFactura.php?mensaje=factura_actualizada&numero=" . $numeroActualizar);
                exit();
                
            } catch (Exception $e) {
                if ($transaccionIniciada && $objControlConexionPdo) {
                    $objControlConexionPdo->rollBack();
                }
                $mensaje = "<div class='mensaje-error'> Error al actualizar factura: " . $e->getMessage() . "</div>";
            } finally {
                if ($objControlConexionPdo) {
                    $objControlConexionPdo->cerrarBd();
                }
            }
        }
    }
}

// PROCESAR ACCIONES GET (Editar y Eliminar)
if (isset($_GET['accion'])) {
    $accionGet = $_GET['accion'];
    $numeroGet = $_GET['numero'] ?? '';
    
    if ($accionGet === 'editar' && !empty($numeroGet)) {
        // CARGAR FACTURA PARA EDITAR
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            
            $sql = "SELECT numero, DATE(fecha) as fecha, total, fkidcliente, fkidvendedor FROM factura WHERE numero = ?";
            $result = $objControlConexionPdo->ejecutarSelect($sql, [$numeroGet]);
            
            if ($result) {
                $facturaData = $result[0];
                $numero = $facturaData['numero'];
                $fecha = $facturaData['fecha']; // Ya viene en formato DATE (YYYY-MM-DD)
                $clienteSeleccionado = $facturaData['fkidcliente'];
                $vendedorSeleccionado = $facturaData['fkidvendedor'];
                $accion = "editar";
                
                // Cargar productos
                $productosFactura = cargarProductosFactura($numeroGet);
                
                $mensaje = "<div class='mensaje-info'> Editando factura #$numeroGet</div>";
            } else {
                $mensaje = "<div class='mensaje-error'> Factura #$numeroGet no encontrada para editar</div>";
            }
            
            $objControlConexionPdo->cerrarBd();
            
        } catch (Exception $e) {
            $mensaje = "<div class='mensaje-error'> Error al cargar factura: " . $e->getMessage() . "</div>";
        }
        
    } elseif ($accionGet === 'eliminar' && !empty($numeroGet)) {
        // ELIMINAR FACTURA
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            
            // Eliminar productos de la factura primero
            $sqlEliminarProductos = "DELETE FROM productosporfactura WHERE fknumfactura = ?";
            $objControlConexionPdo->ejecutarComandoSql($sqlEliminarProductos, [$numeroGet]);
            
            // Eliminar factura
            $sqlEliminarFactura = "DELETE FROM factura WHERE numero = ?";
            $result = $objControlConexionPdo->ejecutarComandoSql($sqlEliminarFactura, [$numeroGet]);
            
            // Verificar si quedan facturas y resetear AUTO_INCREMENT si no hay ninguna
            $sqlCount = "SELECT COUNT(*) as total FROM factura";
            $countResult = $objControlConexionPdo->ejecutarSelect($sqlCount);
            
            if ($countResult && $countResult[0]['total'] == 0) {
                // No quedan facturas, resetear AUTO_INCREMENT a 1
                $sqlReset = "ALTER TABLE factura AUTO_INCREMENT = 1";
                $objControlConexionPdo->ejecutarComandoSql($sqlReset, []);
            }
            
            $objControlConexionPdo->cerrarBd();
            
            if ($result) {
                // Redirigir para evitar reenvío del formulario
                header("Location: vistaFactura.php?mensaje=factura_eliminada&numero=" . $numeroGet);
                exit();
            } else {
                $mensaje = "<div class='mensaje-error'> No se pudo eliminar la factura</div>";
            }
            
        } catch (Exception $e) {
            if (isset($objControlConexionPdo)) {
                $objControlConexionPdo->cerrarBd();
            }
            $mensaje = "<div class='mensaje-error'> Error al eliminar: " . $e->getMessage() . "</div>";
        }
    }
}

// CARGAR DATOS PARA LOS COMBOS
// Cargar clientes - versión simplificada
$clientes = [];
try {
    $objControlConexionPdo = new ControlConexionPdo();
    $objControlConexionPdo->abrirBd();
    
    $sql = "SELECT c.id, p.nombre 
            FROM cliente c 
            JOIN persona p ON c.fkcodpersona = p.codigo 
            ORDER BY p.nombre";
    $clientesData = $objControlConexionPdo->ejecutarSelect($sql);
    
    if ($clientesData) {
        foreach ($clientesData as $cliente) {
            $clientes[] = [
                'id' => $cliente['id'],
                'nombre' => $cliente['nombre']
            ];
        }
    }
    
    $objControlConexionPdo->cerrarBd();
} catch (Exception $e) {
    $clientes = [];
    error_log("Error cargando clientes: " . $e->getMessage());
}

// Cargar vendedores - versión simplificada
$vendedores = [];
try {
    $objControlConexionPdo = new ControlConexionPdo();
    $objControlConexionPdo->abrirBd();
    
    $sql = "SELECT v.id, p.nombre 
            FROM vendedor v 
            JOIN persona p ON v.fkcodpersona = p.codigo 
            ORDER BY p.nombre";
    $vendedoresData = $objControlConexionPdo->ejecutarSelect($sql);
    
    if ($vendedoresData) {
        foreach ($vendedoresData as $vendedor) {
            $vendedores[] = [
                'id' => $vendedor['id'],
                'nombre' => $vendedor['nombre']
            ];
        }
    }
    
    $objControlConexionPdo->cerrarBd();
} catch (Exception $e) {
    $vendedores = [];
    error_log("Error cargando vendedores: " . $e->getMessage());
}

// Cargar productos - versión simplificada para evitar errores
$productos = [];
try {
    $objControlConexionPdo = new ControlConexionPdo();
    $objControlConexionPdo->abrirBd();
    
    // Query muy simple para evitar cualquier problema
    $sql = "SELECT codigo, nombre, valorunitario FROM producto ORDER BY nombre";
    $productosData = $objControlConexionPdo->ejecutarSelect($sql);
    
    if ($productosData) {
        foreach ($productosData as $producto) {
            $productos[] = [
                'codigo' => $producto['codigo'],
                'nombre' => $producto['nombre'],
                'precio' => floatval($producto['valorunitario'])
            ];
        }
    }
    
    $objControlConexionPdo->cerrarBd();
} catch (Exception $e) {
    // En caso de error, continuar con array vacío
    $productos = [];
    error_log("Error cargando productos: " . $e->getMessage());
}

try {
    // Cargar listado de facturas
    $objControlConexionPdo = new ControlConexionPdo();
    $objControlConexionPdo->abrirBd();
    $sql = "SELECT f.numero, DATE(f.fecha) as fecha, f.total, pc.nombre as cliente, pv.nombre as vendedor 
            FROM factura f 
            JOIN cliente c ON f.fkidcliente = c.id 
            JOIN persona pc ON c.fkcodpersona = pc.codigo
            JOIN vendedor v ON f.fkidvendedor = v.id 
            JOIN persona pv ON v.fkcodpersona = pv.codigo
            ORDER BY f.numero DESC";
    $facturas = $objControlConexionPdo->ejecutarSelect($sql);
    $objControlConexionPdo->cerrarBd();
} catch (Exception $e) {
    $facturas = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Facturas</title>
    <link rel="stylesheet" href="estilos/vistaFactura.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestión de Facturas</h1>
        </div>
        
        <div class="content">
            <!-- Mostrar mensajes -->
            <?php if (!empty($mensaje)): ?>
                <?php echo $mensaje; ?>
            <?php endif; ?>
            
            <div class="form-section">
                <h2>
                    <?php if ($accion === "editar"): ?>
                        Editar Factura #<?php echo $numero; ?>
                    <?php else: ?>
                        Nueva Factura
                    <?php endif; ?>
                </h2>
                
                <form method="POST" id="facturaForm">
                    <input type="hidden" name="accion" value="<?php echo ($accion === 'editar') ? 'actualizar' : 'guardar'; ?>">
                    <?php if ($accion === "editar"): ?>
                        <input type="hidden" name="numero" value="<?php echo $numero; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="fecha">Fecha: </label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente">Cliente: </label>
                        <select id="cliente" name="cliente" required>
                            <option value="">-- Seleccione cliente --</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>" 
                                        <?php echo ($clienteSeleccionado == $cliente['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cliente['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="vendedor">Vendedor: </label>
                        <select id="vendedor" name="vendedor" required>
                            <option value="">-- Seleccione vendedor --</option>
                            <?php foreach ($vendedores as $vendedor): ?>
                                <option value="<?php echo $vendedor['id']; ?>" 
                                        <?php echo ($vendedorSeleccionado == $vendedor['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($vendedor['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="productos-section">
                        <h3>Productos</h3>
                        <button type="button" class="btn btn-agregar btn-sm" onclick="agregarProducto()">+ Agregar producto</button>
                        
                        <table class="productos-table" id="tablaProductos">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Valor unitario</th>
                                    <th>Subtotal</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="productosBody">
                                <?php if ($accion === "editar" && !empty($productosFactura)): ?>
                                    <?php foreach ($productosFactura as $index => $prodFactura): ?>
                                        <tr>
                                            <td>
                                                <select name="productos[]" onchange="actualizarPrecio(this)" required>
                                                    <option value="">-- Seleccione --</option>
                                                    <?php foreach ($productos as $producto): ?>
                                                        <option value="<?php echo $producto['codigo']; ?>" 
                                                                data-precio="<?php echo $producto['precio']; ?>"
                                                                <?php echo ($producto['codigo'] === $prodFactura['fkcodproducto']) ? 'selected' : ''; ?>>
                                                            <?php echo $producto['codigo'] . ' - ' . $producto['nombre']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="cantidades[]" min="1" 
                                                    value="<?php echo $prodFactura['cantidad']; ?>" 
                                                    onchange="calcularSubtotal(this)" required>
                                            </td>
                                            <td>
                                                <input type="number" name="precios[]" step="0.01" 
                                                    value="<?php echo number_format($prodFactura['subtotal'] / $prodFactura['cantidad'], 2, '.', ''); ?>" 
                                                    onchange="calcularSubtotal(this)" required readonly>
                                            </td>
                                            <td class="subtotal">$<?php echo number_format($prodFactura['subtotal'], 2); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">Eliminar</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td>
                                            <select name="productos[]" onchange="actualizarPrecio(this)" required>
                                                <option value="">-- Seleccione --</option>
                                                <?php foreach ($productos as $producto): ?>
                                                    <option value="<?php echo $producto['codigo']; ?>" data-precio="<?php echo $producto['precio']; ?>">
                                                        <?php echo $producto['codigo'] . ' - ' . $producto['nombre']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="cantidades[]" min="1" value="1" onchange="calcularSubtotal(this)" required>
                                        </td>
                                        <td>
                                            <input type="number" name="precios[]" step="0.01" value="0" onchange="calcularSubtotal(this)" required readonly>
                                        </td>
                                        <td class="subtotal">$0.00</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">Eliminar</button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <div class ="total-section">
                            <h3>Total estimado: $<span id="totalEstimado">0.00</span></h3>
                        </div>
                    </div>
                    
                    <div class="actions">
                        <button type="submit" class="btn btn-success">
                            <?php echo ($accion === 'editar') ? 'Actualizar Factura' : 'Guardar Factura'; ?>
                        </button>
                        
                        <?php if ($accion === 'editar'): ?>
                            <a href="vistaFactura.php" class="btn btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Sección de consulta -->
            <div class="form-section">
                <h2>Consultar Factura</h2>
                <form method="POST">
                    <input type="hidden" name="accion" value="consultar">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="numeroConsulta">Número de Factura:</label>
                            <input type="number" id="numeroConsulta" name="numero" min="1" placeholder="Ingrese el número de factura" required>
                        </div>
                        <div class="form-group" style="display: flex; align-items: end;">
                            <button type="submit" class="btn btn-primary">Consultar</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Listado de facturas -->
            <div class="listado-facturas">
                <h2>Listado de Facturas</h2>
                <table class="tabla-facturas">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($facturas)): ?>
                            <?php foreach ($facturas as $factura): ?>
                                <tr>
                                    <td><?php echo $factura['numero']; ?></td>
                                    <td><?php echo $factura['fecha']; ?></td>
                                    <td><?php echo htmlspecialchars($factura['cliente']); ?></td>
                                    <td><?php echo htmlspecialchars($factura['vendedor']); ?></td>
                                    <td>$<?php echo number_format($factura['total'], 2); ?></td>
                                    <td>
                                        <a href="?accion=editar&numero=<?php echo $factura['numero']; ?>" class="btn btn-primary btn-sm">Editar</a>
                                        <a href="?accion=eliminar&numero=<?php echo $factura['numero']; ?>" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('¿Está seguro de eliminar la factura #<?php echo $factura['numero']; ?>?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">
                                    No hay facturas registradas
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function agregarProducto() {
            const tbody = document.getElementById('productosBody');
            const newRow = document.createElement('tr');
            
            newRow.innerHTML = `
                <td>
                    <select name="productos[]" onchange="actualizarPrecio(this)" required>
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?php echo $producto['codigo']; ?>" data-precio="<?php echo $producto['precio']; ?>">
                                <?php echo $producto['codigo'] . ' - ' . $producto['nombre']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="cantidades[]" min="1" value="1" onchange="calcularSubtotal(this)" required>
                </td>
                <td>
                    <input type="number" name="precios[]" step="0.01" value="0" onchange="calcularSubtotal(this)" required readonly>
                </td>
                <td class="subtotal">$0.00</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(this)">Eliminar</button>
                </td>
            `;
            
            tbody.appendChild(newRow);
        }
        
        function eliminarProducto(button) {
            const row = button.closest('tr');
            const tbody = row.parentNode;
            
            if (tbody.children.length > 1) {
                row.remove();
                calcularTotal();
            } else {
                alert('Debe mantener al menos un producto en la factura');
            }
        }
        
        function actualizarPrecio(select) {
            const row = select.closest('tr');
            const precioInput = row.querySelector('input[name="precios[]"]');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const precio = selectedOption.getAttribute('data-precio');
                precioInput.value = parseFloat(precio).toFixed(2);
                calcularSubtotal(precioInput);
            } else {
                precioInput.value = '0.00';
                calcularSubtotal(precioInput);
            }
        }
        
        function calcularSubtotal(element) {
            const row = element.closest('tr');
            const cantidad = parseFloat(row.querySelector('input[name="cantidades[]"]').value) || 0;
            const precio = parseFloat(row.querySelector('input[name="precios[]"]').value) || 0;
            const subtotal = cantidad * precio;
            
            row.querySelector('.subtotal').textContent = '$' + subtotal.toFixed(2);
            calcularTotal();
        }
        
        function calcularTotal() {
            const subtotales = document.querySelectorAll('.subtotal');
            let total = 0;
            
            subtotales.forEach(function(subtotalElement) {
                const valor = subtotalElement.textContent.replace('$', '').replace(',', '');
                total += parseFloat(valor) || 0;
            });
            
            document.getElementById('totalEstimado').textContent = total.toFixed(2);
        }
        
        function editarFactura(numero) {
            window.location.href = '?accion=editar&numero=' + numero;
        }
        
        function eliminarFactura(numero) {
            if (confirm('¿Está seguro de eliminar la factura #' + numero + '?')) {
                window.location.href = '?accion=eliminar&numero=' + numero;
            }
        }
        
        // Calcular total al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            calcularTotal();
        });
    </script>
</body>
</html>
