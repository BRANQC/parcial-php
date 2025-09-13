<?php
    class Persona {
        // Atributos, propiedades o características de la clase
        public $codigo;
        public $nombre;
        public $telefono;
        public $email;

        // constructor debe inicializarse luego de los atributos como: public function __construct(){}
        public function __construct($codigo, $nombre, $telefono, $email) {
            $this->codigo = $codigo;
            $this->nombre = $nombre;
            $this->telefono = $telefono;
            $this->email = $email;
        }

        // Métodos o funciones de la clase
        // gets and sets 
            // SETS
        public function setCodigo($codigo) {
            $this->codigo = $codigo;
        }

        public function setNombre($nombre) {
            $this->nombre = $nombre;
        }

        public function setTelefono($telefono) {
            $this->telefono = $telefono;
        }

        public function setEmail($email) {
            $this->email = $email;
        }

            // GETS 
        public function getCodigo() {
            return $this->codigo;
        }

        public function getNombre() {
            return $this->nombre;
        }

        public function getTelefono() {
            return $this->telefono;
        }

        public function getEmail() {
            return $this->email;
        }
}
?>