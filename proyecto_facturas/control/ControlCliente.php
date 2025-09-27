<?php
    class ControlCliente {
        //CRUD
        public $objCliente;
        function __construct($objCliente) {
            $this->objCliente = $objCliente;
        }
        // Create(Insert into)
        function guardar(){
            $cod = $this->objCliente->getCodigo();
            $credito = $this->objCliente->getCredito();
            $nom = $this->objCliente->getNombre();
            $tel = $this->objCliente->getTelefono();
            $email = $this->objCliente->getEmail();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "INSERT INTO cliente (codigo, credito, nombre, telefono, email) VALUES ('$cod', $credito, '$nom', '$tel', '$email')";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function modificar(){
            $cod = $this->objCliente->getCodigo();
            $credito = $this->objCliente->getCredito();
            $nom = $this->objCliente->getNombre();
            $tel = $this->objCliente->getTelefono();
            $email = $this->objCliente->getEmail();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "UPDATE cliente SET credito='$credito', nombre='$nom', telefono='$tel', email='$email' WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function eliminar(){
            $cod = $this->objCliente->getCodigo();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "DELETE FROM cliente WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }
    }
?>