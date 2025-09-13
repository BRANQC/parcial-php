<?php
class Vendedor {
    public $codigo;
    public $carnet;
    public $direccion;
    public $nombre;
    public $email;
    public $telefono; // Heredado de Persona

    public function __construct($codigo, $carnet, $direccion, $nombre, $email, $telefono) {
        $this->codigo = $codigo;
        $this->carnet = $carnet;
        $this->direccion = $direccion;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->telefono = $telefono;
    }

    public function setCarnet($carnet) {
        $this->carnet = $carnet;
    }
    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }
    public function getCarnet() {
        return $this->carnet;
    }
    public function getDireccion() {
        return $this->direccion;
    }
    public function getNombre() {
        return $this->nombre;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getTelefono() {
        return $this->telefono;
    }
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function getCodigo() {
        return $this->codigo;
    }
    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }
}
?>