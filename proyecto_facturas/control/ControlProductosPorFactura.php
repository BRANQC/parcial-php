<?php
    class ControlProductosPorFactura {
        //CRUD
        public $objProductosPorFactura;
        function __construct($objProductosPorFactura) {
            $this->objProductosPorFactura = $objProductosPorFactura;
        }
        // Create(Insert into)
        function guardar(){
            $cod = $this->objProductosPorFactura->getCodigo();
            $codFactura = $this->objProductosPorFactura->getCodFactura();
            $codProducto = $this->objProductosPorFactura->getCodProducto();
            $cantidad = $this->objProductosPorFactura->getCantidad();
            $precio = $this->objProductosPorFactura->getPrecio();
            $subtotal = $this->objProductosPorFactura->getSubtotal();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "INSERT INTO productosporfactura (codigo, codfactura, codproducto, cantidad, precio, subtotal) VALUES ('$cod', '$codFactura', '$codProducto', '$cantidad', '$precio', '$subtotal')";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function modificar(){
            $cod = $this->objProductosPorFactura->getCodigo();
            $codFactura = $this->objProductosPorFactura->getCodFactura();
            $codProducto = $this->objProductosPorFactura->getCodProducto();
            $cantidad = $this->objProductosPorFactura->getCantidad();
            $precio = $this->objProductosPorFactura->getPrecio();
            $subtotal = $this->objProductosPorFactura->getSubtotal();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "UPDATE productosporfactura SET codfactura='$codFactura', codproducto='$codProducto', cantidad='$cantidad', precio='$precio', subtotal='$subtotal' WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function eliminar(){
            $cod = $this->objProductosPorFactura->getCodigo();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "DELETE FROM productosporfactura WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }
    }
?>