<?php
    class ControlCodigo {
        //CRUD
        public $objCodigo;
        function __construct($objCodigo) {
            $this->objCodigo = $objCodigo;
        }
        // Create(Insert into)
        function guardar(){
            $cod = $this->objCodigo->getCodigo();
            $nom = $this->objCodigo->getNombre();
            $tel = $this->objCodigo->getTelefono();
            $email = $this->objCodigo->getEmail();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "INSERT INTO codigo (codigo, nombre, telefono, email) VALUES ('$cod', '$nom', '$tel', '$email')";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function modificar(){
            $cod = $this->objCodigo->getCodigo();
            $nom = $this->objCodigo->getNombre();
            $tel = $this->objCodigo->getTelefono();
            $email = $this->objCodigo->getEmail();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "UPDATE codigo SET nombre='$nom', telefono='$tel', email='$email' WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function eliminar(){
            $cod = $this->objCodigo->getCodigo();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "DELETE FROM codigo WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }
    }
?>