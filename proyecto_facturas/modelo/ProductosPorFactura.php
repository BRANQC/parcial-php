<?php
    class ProductosPorFactura {
        public $codigo;
        public $cantidad;
        public $subtotal;

        public function __construct($codigo, $cantidad, $subtotal) {
            $this->codigo = $codigo;
            $this->cantidad = $cantidad;
            $this->subtotal = $subtotal;
        }
        public function setCantidad($cantidad) {
            $this->cantidad = $cantidad;
        }
        public function setSubtotal($subtotal) {
            $this->subtotal = $subtotal;
        }
        public function getCantidad() {
            return $this->cantidad;
        }
        public function getSubtotal() {
            return $this->subtotal;
        }
        public function getCodigo() {
            return $this->codigo;
        }
        public function setCodigo($codigo) {
            $this->codigo = $codigo;
        }
    }
?>