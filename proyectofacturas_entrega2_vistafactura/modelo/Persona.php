<?php
/**
 * Clase Persona
 * Representa un registro de la tabla "persona".
 * Contiene los atributos y sus respectivos getters y setters.
 */
class Persona {
    // Atributos privados, igual que en la tabla
    private string $codigo;
    private string $nombre;
    private string $email;
    private string $telefono;

    /**
     * Constructor: inicializa los atributos de la persona
     */
    public function __construct(string $codigo, string $nombre, string $email, string $telefono) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->telefono = $telefono;
    }

    // ======================
    // Getters y Setters
    // ======================

    public function getCodigo(): string {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void {
        $this->codigo = $codigo;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getTelefono(): string {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void {
        $this->telefono = $telefono;
    }
}
?>
