<?php
    class ControlPersona {
        //CRUD
        public $objPersona;
        function __construct($objPersona) {
            $this->objPersona = $objPersona;
        }
        // Create(Insert into)
        function guardar(){
            $cod = $this->objPersona->getCodigo();
            $nom = $this->objPersona->getNombre();
            $tel = $this->objPersona->getTelefono();
            $email = $this->objPersona->getEmail();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "INSERT INTO persona (codigo, nombre, telefono, email) VALUES ('$cod', '$nom', '$tel', '$email')";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function modificar(){
            $cod = $this->objPersona->getCodigo();
            $nom = $this->objPersona->getNombre();
            $tel = $this->objPersona->getTelefono();
            $email = $this->objPersona->getEmail();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "UPDATE persona SET nombre='$nom', telefono='$tel', email='$email' WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }

        function eliminar(){
            $cod = $this->objPersona->getCodigo();
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);
            $sql = "DELETE FROM persona WHERE codigo='$cod'";
            $objControlConexion->ejecutarComandoSql($sql);
            $objControlConexion->cerrarBd();
        }


        // Read(Select)
        function consultar(){
            $objControlConexion = new ControlConexionPdo();
            $objControlConexion->abrirBd("localhost", "root", "", "dbfactura", 3306);

            try {
                $sql = "SELECT * FROM persona";
                $resultado = $objControlConexion->ejecutarSelect($sql);

                $entidades = [];
                foreach ($resultado as $fila) {
                    $objPersona = new Persona(
                        $fila['codigo'],
                        $fila['nombre'],
                        $fila['telefono'],
                        $fila['email']
                    );
                    $entidades[] = $objPersona;
                }

                return $entidades;
                } catch (PDOException $e) {
                    echo "Error al consultar personas: " . $e->getMessage();
                    return [];
                } finally {
                    $objControlConexion->cerrarBd();
                }
            }
    }
?>