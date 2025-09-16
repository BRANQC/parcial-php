<?php
    class ControlProducto {
        //CRUD
        public $objProducto;
        function __construct($objProducto) {
            $this->objProducto = $objProducto;
        }
        // Create(Insert into)
        function guardar(){
            $cod = $this->objProducto->getCodigo();
            $nom = $this->objProducto->getNombre();
            $stock = $this->objProducto->getStock();
            $valor = $this->objProducto->getValorUnitario();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "INSERT INTO producto (codigo, nombre, Stock, valor_unitario) VALUES ('$cod', '$nom', '$stock', '$valor')";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function modificar(){
            $cod = $this->objProducto->getCodigo();
            $nom = $this->objProducto->getNombre();
            $stock = $this->objProducto->getStock();
            $valor = $this->objProducto->getValorUnitario();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "UPDATE producto SET nombre='$nom', stock='$stock', valor_unitario='$valor' WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function eliminar(){
            $cod = $this->objProducto->getCodigo();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "DELETE FROM producto WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }
    }
?>