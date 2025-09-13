<?php
include '../modelo/Persona.php';
include '../control/ControlPersona.php';
include '../control/ControlConexionPdo.php';
echo "Hola bienvenidos";
    //$objPersona = new Persona("1", "Bran", "412", "bran@gmail.com");
    //$objControlPersona = new ControlPersona($objPersona);
    //$objControlPersona->guardar();
    //$objControlPersona = new ControlPersona(null);
    //$objControlPersona->consultar();
    //$objPersona = new Persona("1", "Bran Quintero", "412", "bran@gamil.com");
    //$objControlPersona = new ControlPersona($objPersona);
    //$objControlPersona->modificar();
    $objPersona = new Persona("1", "", "", "");
    $objControlPersona = new ControlPersona($objPersona);
    $objControlPersona->eliminar();
echo "<br>"."Listo!";
?>
