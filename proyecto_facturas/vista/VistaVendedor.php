<?php
include '../modelo/Vendedor.php';
include '../control/ControlVendedor.php';
include '../control/ControlConexionPdo.php';
echo "Hola bienvenidos";
    $objVendedor = new Vendedor("", "", "", "", "", "");
    $objControlVendedor = new ControlPersona($objVendedor);
    $objControlVendedor->guardar();
    $objVendedor = new Vendedor("", "", "", "", "", "");
    $objControlVendedor = new ControlPersona($objVendedor);
    $objControlVendedor->modificar();
    $objVendedor = new Vendedor("", "", "", "", "", "");
    $objControlVendedor = new ControlPersona($objVendedor);
    $objControlVendedor->eliminar();
echo "<br>"."Listo!";
?>