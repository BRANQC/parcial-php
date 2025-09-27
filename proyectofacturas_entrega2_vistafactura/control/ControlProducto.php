<?php
// Incluir las clases necesarias
require_once 'ControlConexionPdo.php';
require_once '../modelo/Producto.php';

class ControlProducto {
    private ?Producto $objProducto; // Puede ser null

    // El constructor acepta Producto o null (para listar, por ejemplo)
    public function __construct(?Producto $objProducto = null) {
        $this->objProducto = $objProducto;
    }
    
    /**
     * Guarda un nuevo producto en la base de datos.
     */
    public function guardar(): bool {
        try {
            if ($this->objProducto === null) return false;

            $cod = $this->objProducto->getCodigo();
            $nom = $this->objProducto->getNombre();
            $exi = $this->objProducto->getExistencia();
            $val = $this->objProducto->getValorUnitario();
            
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            
            $sql = "INSERT INTO producto (codigo, nombre, existencia, valorunitario) VALUES (?, ?, ?, ?)";
            $parametros = [$cod, $nom, $exi, $val];
            
            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);
            $objControlConexionPdo->cerrarBd();
            
            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Modifica los datos de un producto existente.
     */
    public function modificar(): bool {
        try {
            if ($this->objProducto === null) return false;

            $cod = $this->objProducto->getCodigo();
            $nom = $this->objProducto->getNombre();
            $exi = $this->objProducto->getExistencia();
            $val = $this->objProducto->getValorUnitario();
            
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            
            $sql = "UPDATE producto SET nombre = ?, existencia = ?, valorunitario = ? WHERE codigo = ?";
            $parametros = [$nom, $exi, $val, $cod];
            
            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);
            $objControlConexionPdo->cerrarBd();
            
            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Elimina un producto de la base de datos.
     */
    public function borrar(): bool {
        try {
            if ($this->objProducto === null) return false;

            $cod = $this->objProducto->getCodigo();
            
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            
            $sql = "DELETE FROM producto WHERE codigo = ?";
            $parametros = [$cod];
            
            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);
            $objControlConexionPdo->cerrarBd();
            
            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Obtiene un listado de todos los productos.
     * Retorna una lista de objetos Producto.
     */
    public function listar(): array {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "SELECT * FROM producto";
            $recordSet = $objControlConexionPdo->ejecutarSelect($sql);

            $objControlConexionPdo->cerrarBd();

            $lista = [];
            foreach ($recordSet as $fila) {
                $lista[] = new Producto(
                    $fila['codigo'],
                    $fila['nombre'],
                    (float)$fila['valorunitario'],
                    (int)$fila['existencia']
                );
            }
            return $lista;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Consulta un producto por su código.
     * Retorna un objeto Producto o null.
     */
    public function consultar(): ?Producto {
        try {
            if ($this->objProducto === null) return null;

            $cod = $this->objProducto->getCodigo();
            
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            
            $sql = "SELECT * FROM producto WHERE codigo = ?";
            $parametros = [$cod];
            
            $recordSet = $objControlConexionPdo->ejecutarSelect($sql, $parametros);
            
            $objControlConexionPdo->cerrarBd();
            
            if (count($recordSet) > 0) {
                $fila = $recordSet[0];
                return new Producto(
                    $fila['codigo'],
                    $fila['nombre'],
                    (float)$fila['valorunitario'],
                    (int)$fila['existencia']
                );
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }
    // Método: devuelve arrays asociativos (para combos en vistaFactura)
    public function listarComoArray() {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "SELECT codigo, nombre, existencia, valorunitario FROM producto ORDER BY nombre";
            $recordSet = $objControlConexionPdo->ejecutarSelect($sql);

            $objControlConexionPdo->cerrarBd();

            return $recordSet ? $recordSet : [];

        } catch (Exception $e) {
            return [];
        }
    }
}
?>
