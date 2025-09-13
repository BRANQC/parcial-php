<?php
include '../modelo/Persona.php';
include '../control/ControlPersona.php';
include '../control/ControlConexionPdo.php';
echo "Hola bienvenidos";
    $objPersona = new Producto("", "", "", "");
    $objControlPersona = new ControlPersona($objProducto);
    $objControlPersona->guardar();
    $objPersona = new Producto("", "", "", "");
    $objControlPersona = new ControlPersona($objProducto);
    $objControlPersona->modificar();
    $objPersona = new Producto("", "", "", "");
    $objControlPersona = new ControlPersona($objProducto);
    $objControlPersona->eliminar();
echo "<br>"."Listo!";
?>