<?php
/**
 * Clase Empresa
 * Representa una empresa con un código y un nombre.
 * 
 * Es un modelo de datos (entidad) que se conecta con la base de datos 
 * a través de un controlador (por ejemplo: ControlEmpresa.php).
 */
class Empresa {
    // Atributos privados que definen las propiedades de la empresa
    private string $codigo;  // Código único de la empresa
    private string $nombre;  // Nombre de la empresa

    /**
     * Constructor de la clase
     * Se ejecuta automáticamente al crear un nuevo objeto Empresa.
     * @param string $codigo Código de la empresa
     * @param string $nombre Nombre de la empresa
     */
    public function __construct(string $codigo, string $nombre) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
    }

    /**
     * Getter del código
     * Devuelve el código de la empresa.
     */
    public function getCodigo(): string {
        return $this->codigo;
    }

    /**
     * Setter del código
     * Permite cambiar el valor del código.
     */
    public function setCodigo(string $codigo): void {
        $this->codigo = $codigo;
    }

    /**
     * Getter del nombre
     * Devuelve el nombre de la empresa.
     */
    public function getNombre(): string {
        return $this->nombre;
    }

    /**
     * Setter del nombre
     * Permite cambiar el valor del nombre.
     */
    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }
}
