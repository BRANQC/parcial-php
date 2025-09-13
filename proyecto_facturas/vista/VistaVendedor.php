<?php
include '../modelo/Persona.php';
include '../control/ControlPersona.php';
include '../control/ControlConexionPdo.php';
echo "Hola bienvenidos";
    $objPersona = new Vendedor("", "", "", "", "", "");
    $objControlPersona = new ControlPersona($objVendedor);
    $objControlPersona->guardar();
    $objPersona = new Vendedor("", "", "", "", "", "");
    $objControlPersona = new ControlPersona($objVendedor);
    $objControlPersona->modificar();
    $objPersona = new Vendedor("", "", "", "", "", "");
    $objControlPersona = new ControlPersona($objVendedor);
    $objControlPersona->eliminar();
echo "<br>"."Listo!";
?>