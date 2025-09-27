<?php
// "require_once" sirve para incluir un archivo PHP externo en este script.
// - En este caso, se incluye la clase "Persona.php", que está en la misma carpeta (por eso se usa __DIR__).
// - "__DIR__" es una constante mágica de PHP que devuelve la ruta absoluta del directorio actual.
// - Con esto se asegura que, sin importar desde dónde se ejecute el programa, se cargue correctamente el archivo Persona.php.
// - "once" garantiza que el archivo solo se incluya una vez, evitando errores por inclusiones repetidas.
require_once __DIR__ . '/Persona.php';
;

/**
 * Clase Cliente
 * Representa a un cliente dentro del sistema.
 * 
 * Extiende de Persona, por lo que hereda:
 * - código
 * - nombre
 * - teléfono
 * - email
 * 
 * Además agrega atributos propios de Cliente:
 * - id (identificador único en BD)
 * - crédito disponible
 * - fkcodempresa (empresa a la que pertenece)
 */
class Cliente extends Persona {
    // Atributos adicionales de Cliente
    private ?int $id = null;             // Identificador único en la tabla cliente
    private ?float $credito = null;      // Crédito disponible del cliente
    private ?string $fkcodempresa = null; // Código de la empresa asociada (FK)

    /**
     * Constructor de Cliente
     * Se ejecuta al crear un objeto Cliente, inicializando sus propiedades.
     * 
     * @param int|null $id Identificador único del cliente (puede ser null si aún no existe en BD).
     * @param string $codigo Código del cliente (heredado de Persona).
     * @param string $nombre Nombre del cliente (heredado de Persona).
     * @param string $telefono Teléfono del cliente (heredado de Persona).
     * @param string $email Email del cliente (heredado de Persona).
     * @param float $credito Crédito disponible del cliente.
     * @param string|null $fkcodempresa Código de la empresa asociada (puede ser null si no tiene empresa asignada).
     */
    public function __construct(
        ?int $id,
        string $codigo,
        string $nombre,
        string $telefono,
        string $email,
        float $credito,
        ?string $fkcodempresa
    ) {
        // Se llama al constructor de la clase padre (Persona)
        parent::__construct($codigo, $nombre, $telefono, $email);

        // Se inicializan los atributos propios de Cliente
        $this->id = $id;
        $this->credito = $credito;
        $this->fkcodempresa = $fkcodempresa;
    }

    // ===========================
    // Métodos GETTERS y SETTERS
    // ===========================

    /**
     * Devuelve el ID del cliente.
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Establece el ID del cliente.
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Devuelve el crédito del cliente.
     */
    public function getCredito(): ?float {
        return $this->credito;
    }

    /**
     * Establece el crédito del cliente.
     */
    public function setCredito(float $credito): void {
        $this->credito = $credito;
    }

    /**
     * Devuelve el código de la empresa a la que pertenece el cliente.
     */
    public function getFkcodempresa(): ?string {
        return $this->fkcodempresa;
    }

    /**
     * Establece el código de la empresa a la que pertenece el cliente.
     */
    public function setFkcodempresa(?string $fkcodempresa): void {
        $this->fkcodempresa = $fkcodempresa;
    }
}
