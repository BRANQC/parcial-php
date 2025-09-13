<?php
include '../modelo/Producto.php';
include '../control/ControlProducto.php';
include '../control/ControlConexionPdo.php';
echo "Hola bienvenidos";
    $objProducto = new Producto("", "", "", "");
    $objControlProducto = new ControlPersona($objProducto);
    $objControlProducto->guardar();
    $objProducto = new Producto("", "", "", "");
    $objControlProducto = new ControlPersona($objProducto);
    $objControlProducto->modificar();
    $objProducto = new Producto("", "", "", "");
    $objControlProducto = new ControlPersona($objProducto);
    $objControlProducto->eliminar();
echo "<br>"."Listo!";
?>