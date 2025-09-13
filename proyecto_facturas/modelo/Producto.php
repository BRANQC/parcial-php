<?php
class Producto{
    public $codigo;
    public $nombre;
    public $stock;
    public $valor_unitario;

    public function __construct($codigo, $nombre, $stock, $valor_unitario) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->stock = $stock;
        $this->valor_unitario = $valor_unitario;
}
    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    public function setStock($stock) {
        $this->stock = $stock;
    }
    public function setValorUnitario($valor_unitario) {
        $this->valor_unitario = $valor_unitario;
    }
    public function getCodigo() {
        return $this->codigo;
    }
    public function getNombre() {
        return $this->nombre;
    }
    public function getStock() {
        return $this->stock;
    }
    public function getValorUnitario() {
        return $this->valor_unitario;
    }
}
?>