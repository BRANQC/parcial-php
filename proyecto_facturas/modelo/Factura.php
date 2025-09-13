<?php
class Factura {
    public $codigo;
    public $fecha;
    public $numero;
    public $total;

    public function __construct($codigo, $fecha, $numero, $total) {
        $this->codigo = $codigo;
        $this->fecha = $fecha;
        $this->numero = $numero;
        $this->total = $total;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }
    public function setNumero($numero) {
        $this->numero = $numero;
    }
    public function setTotal($total) {
        $this->total = $total;
    }
    public function getFecha() {
        return $this->fecha;
    }
    public function getNumero() {
        return $this->numero;
    }
    public function getTotal() {
        return $this->total;
    }
    public function getCodigo() {
        return $this->codigo;
    }
    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }
}
?>