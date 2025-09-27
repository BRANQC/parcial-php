<?php
// ========================================
// Clase Vendedor
// ========================================
// Esta clase representa a un vendedor en el sistema.
// Hereda de la clase Persona, por lo que también tiene
// código, nombre, teléfono y email.
//
// Además, agrega sus propios atributos específicos:
// - id: identificador único en la tabla "vendedor".
// - carnet: número o código de identificación laboral.
// - direccion: dirección física del vendedor.
//
// Este modelo se conecta con la base de datos a través de
// un controlador (ejemplo: ControlVendedor.php).
// ========================================

// Se incluye la clase base Persona para poder heredar de ella
require_once __DIR__ . '/Persona.php';

class Vendedor extends Persona {
    // Atributos adicionales propios de un vendedor
    private ?int $id = null;            // ID interno (clave primaria en BD)
    private ?string $carnet = null;     // Carnet o identificación laboral
    private ?string $direccion = null;  // Dirección del vendedor
    
    /**
     * Constructor de la clase Vendedor.
     * 
     * @param int|null $id        ID del vendedor (puede ser null si es nuevo).
     * @param string   $codigo    Código de la persona (heredado de Persona).
     * @param string   $nombre    Nombre de la persona (heredado de Persona).
     * @param string   $telefono  Teléfono de la persona (heredado de Persona).
     * @param string   $email     Email de la persona (heredado de Persona).
     * @param string|null $carnet Carnet o identificación laboral.
     * @param string|null $direccion Dirección física del vendedor.
     */
    public function __construct(
        ?int $id,
        string $codigo,
        string $nombre,
        string $telefono,
        string $email,
        ?string $carnet,
        ?string $direccion
    ) {
        // Se llama al constructor de la clase padre (Persona)
        parent::__construct($codigo, $nombre, $telefono, $email);
        
        // Se inicializan los atributos específicos de Vendedor
        $this->id = $id;
        $this->carnet = $carnet;
        $this->direccion = $direccion;
    }
    
    // ==========================
    // Métodos Getters y Setters
    // ==========================

    /** Devuelve el ID del vendedor */
    public function getId(): ?int {
        return $this->id;
    }
    
    /** Establece el ID del vendedor */
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    /** Devuelve el carnet del vendedor */
    public function getCarnet(): ?string {
        return $this->carnet;
    }
    
    /** Establece el carnet del vendedor */
    public function setCarnet(?string $carnet): void {
        $this->carnet = $carnet;
    }
    
    /** Devuelve la dirección del vendedor */
    public function getDireccion(): ?string {
        return $this->direccion;
    }
    
    /** Establece la dirección del vendedor */
    public function setDireccion(?string $direccion): void {
        $this->direccion = $direccion;
    }
}
?>
