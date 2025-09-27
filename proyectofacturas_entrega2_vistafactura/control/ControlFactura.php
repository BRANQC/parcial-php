<?php
// Se incluyen las clases necesarias
require_once 'ControlConexionPdo.php';
require_once '../modelo/Factura.php';
require_once '../modelo/Cliente.php';
require_once '../modelo/Vendedor.php';
require_once '../modelo/ProductosPorFactura.php';

/**
 * Clase ControlFactura
 * 
 * Se encarga de la lógica de negocio para manejar facturas:
 * guardar, consultar, listar y borrar.
 * Aquí se conecta el modelo (Factura) con la base de datos.
 */
class ControlFactura {
    private Factura $objFactura; // Factura actual que se quiere procesar

    /**
     * Constructor de la clase
     * @param Factura $objFactura Instancia del modelo Factura
     */
    public function __construct(Factura $objFactura) {
        $this->objFactura = $objFactura;
    }

    /**
     * Guardar factura con sus productos.
     * 
     * Utiliza el procedimiento almacenado `insertar_factura_y_productos`
     * que recibe: idCliente, idVendedor y los productos en formato JSON.
     */
    public function guardar(): bool {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            // Convertir la lista de objetos ProductosPorFactura en JSON
            $productos = [];
            foreach ($this->objFactura->getProductosPorFactura() as $item) {
                $productos[] = [
                    "fkcodproducto" => $item->getProducto()->getCodigo(),
                    "cantidad"      => $item->getCantidad()
                ];
            }
            $productosJson = json_encode($productos);

            // Preparar parámetros: cliente, vendedor, productos
            $idCliente  = $this->objFactura->getCliente()->getId();
            $idVendedor = $this->objFactura->getVendedor()->getId();

            // Llamar al procedimiento almacenado
            $sql = "CALL insertar_factura_y_productos(?, ?, ?)";
            $parametros = [$idCliente, $idVendedor, $productosJson];

            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);

            $objControlConexionPdo->cerrarBd();
            return $resultado !== false;
        } catch (Exception $e) {
            // Si ocurre error, se muestra el mensaje en el log
            error_log("Error al guardar factura: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Listar todas las facturas registradas.
     * Devuelve un arreglo con facturas y sus datos principales.
     */
    public function listar(): array {
        $objControlConexionPdo = new ControlConexionPdo();
        $objControlConexionPdo->abrirBd();

        $sql = "SELECT numero, fecha, total, fkidcliente, fkidvendedor 
                FROM factura";
        $recordSet = $objControlConexionPdo->ejecutarSelect($sql);

        $objControlConexionPdo->cerrarBd();
        return $recordSet;
    }

    /**
     * Consultar una factura específica por número.
     * Devuelve la fila con los datos de la factura o null si no existe.
     */
    public function consultar(): ?array {
        $numero = $this->objFactura->getNumero();

        $objControlConexionPdo = new ControlConexionPdo();
        $objControlConexionPdo->abrirBd();

        $sql = "SELECT numero, fecha, total, fkidcliente, fkidvendedor
                FROM factura WHERE numero = ?";
        $parametros = [$numero];

        $recordSet = $objControlConexionPdo->ejecutarSelect($sql, $parametros);

        $objControlConexionPdo->cerrarBd();

        return count($recordSet) > 0 ? $recordSet[0] : null;
    }

    /**
     * Borrar una factura (y sus productos asociados).
     * Gracias a la clave foránea con ON DELETE CASCADE,
     * los productos relacionados se borran automáticamente.
     */
    public function borrar(): bool {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "DELETE FROM factura WHERE numero = ?";
            $parametros = [$this->objFactura->getNumero()];

            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);

            $objControlConexionPdo->cerrarBd();
            return $resultado !== false;
        } catch (Exception $e) {
            error_log("Error al borrar factura: " . $e->getMessage());
            return false;
        }
    }
}
?>
