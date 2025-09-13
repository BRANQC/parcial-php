<?php
    class Empresa {
        public $codigo;
        public $nombre;

        public function __construct($codigo, $nombre) {
            $this->codigo = $codigo;
            $this->nombre = $nombre;
        }
        public function setCodigo($codigo) {
            $this->codigo = $codigo;
        }
        public function setNombre($nombre) {
            $this->nombre = $nombre;
        }
        public function getCodigo() {
            return $this->codigo;
        }
        public function getNombre() {
            return $this->nombre;
        }
    }
?>