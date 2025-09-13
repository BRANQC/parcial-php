<?php
    class ControlVendedor {
        //CRUD
        public $objVendedor;
        function __construct($objVendedor) {
            $this->objVendedor = $objVendedor;
        }
        // Create(Insert into)
        function guardar(){
            $cod = $this->objVendedor->getCodigo();
            $carnet = $this->objVendedor->getcarnet();
            $direccion = $this->objVendedor->getdireccion();
            $nom = $this->objVendedor->getnombre();
            $email = $this->objVendedor->getemail();
            $tel = $this->objVendedor->gettelefono();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "INSERT INTO vendedor (codigo, carnet, direccion, nombre, email, telefono) VALUES ('$cod', '$carnet', '$direccion', '$nom', '$email', '$tel')";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function modificar(){
            $cod = $this->objVendedor->getCodigo();
            $carnet = $this->objVendedor->getcarnet();
            $direccion = $this->objVendedor->getdireccion();
            $nom = $this->objVendedor->getnombre();
            $email = $this->objVendedor->getemail();
            $tel = $this->objVendedor->gettelefono();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "UPDATE vendedor SET carnet = '$carnet', direccion='$direccion', nombre='$nom', telefono='$tel', email='$email' WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function eliminar(){
            $cod = $this->objVendedor->getCodigo();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "DELETE FROM vendedor WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }
    }
?>