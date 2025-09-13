<?php
class Cliente {
    public $codigo;
    public $credito;
    public $nombre;
    public $telefono;
    public $email;


    public function __construct($codigo, $credito, $nombre, $telefono, $email) {
        $this->codigo = $codigo;
        $this->credito = $credito;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->email = $email;
    }
    public function setCredito($credito) {
        $this->credito = $credito;
    }
    public function getCredito() {
        return $this->credito;
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
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }
    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getCodigo() {
        return $this->codigo;
    }
    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }
}
?>