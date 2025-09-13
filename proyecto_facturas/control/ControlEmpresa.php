<?php
    class ControlEmpresa {
        //CRUD
        public $objEmpresa;
        function __construct($objEmpresa) {
            $this->objEmpresa = $objEmpresa;
        }
        // Create(Insert into)
        function guardar(){
            $cod = $this->objEmpresa->getCodigo();
            $nom = $this->objEmpresa->getNombre();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "INSERT INTO empresa (codigo, nombre) VALUES ('$cod', '$nom')";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function modificar(){
            $cod = $this->objEmpresa->getCodigo();
            $nom = $this->objEmpresa->getNombre();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "UPDATE empresa SET nombre='$nom' WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function eliminar(){
            $cod = $this->objEmpresa->getCodigo();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "DELETE FROM empresa WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }
    }
?>