<?php
    class ControlFactura {
        //CRUD
        public $objFactura;
        function __construct($objFactura) {
            $this->objFactura = $objFactura;
        }
        // Create(Insert into)
        function guardar(){
            $cod = $this->objFactura->getCodigo();
            $fecha = $this->objFactura->getFecha();
            $num = $this->objFactura->getNumero();
            $total = $this->objFactura->getTotal();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "INSERT INTO factura (codigo, fecha, numero, total) VALUES ('$cod', '$fecha', '$num', '$total')";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function modificar(){
            $cod = $this->objFactura->getCodigo();
            $fecha = $this->objFactura->getFecha();
            $num = $this->objFactura->getNumero();
            $total = $this->objFactura->getTotal();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "UPDATE factura SET fecha='$fecha', numero='$num', total='$total' WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function eliminar(){
            $cod = $this->objFactura->getCodigo();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "DELETE FROM factura WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }
    }
?>