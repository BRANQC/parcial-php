<?php
// =======================================
// Clase Factura
// =======================================
// Representa una factura de venta, que incluye:
//
// - Un número identificador (clave primaria en BD).
// - Una fecha de emisión.
// - Un valor total.
// - La relación con un Cliente.
// - La relación con un Vendedor.
// - Una colección de ítems (productos vendidos con cantidad y valor).
//
// Nota: se asume que existe una tabla cabecera (factura) y
// una tabla detalle (productos_por_factura).
// =======================================

// Se incluyen las clases necesarias para las relaciones
require_once __DIR__ . '/Cliente.php';
require_once __DIR__ . '/Vendedor.php';
require_once __DIR__ . '/ProductosPorFactura.php';

class Factura {
    // --- Atributos privados ---
    private ?int $numero;                // Número de la factura (PK, autoincremental normalmente)
    private DateTime $fecha;             // Fecha de emisión de la factura
    private float $total;                // Valor total de la factura
    private Cliente $cliente;            // Relación con un cliente (FK en BD)
    private Vendedor $vendedor;          // Relación con un vendedor (FK en BD)
    private array $itemsPorFactura;      // Detalle: lista de objetos ProductosPorFactura

    /**
     * Constructor de Factura
     * @param ?int $numero Número de la factura (null si es nueva)
     * @param DateTime $fecha Fecha de emisión
     * @param float $total Valor total
     * @param Cliente $cliente Objeto cliente asociado
     * @param Vendedor $vendedor Objeto vendedor asociado
     * @param array $itemsPorFactura Colección de objetos ProductosPorFactura (detalle)
     */
    public function __construct(
        ?int $numero, 
        DateTime $fecha, 
        float $total, 
        Cliente $cliente, 
        Vendedor $vendedor, 
        array $itemsPorFactura
    ) {
        $this->numero = $numero;
        $this->fecha = $fecha;
        $this->total = $total;
        $this->cliente = $cliente;
        $this->vendedor = $vendedor;
        $this->itemsPorFactura = $itemsPorFactura;
    }
    
    // --- Getters y Setters ---

    /**
     * Devuelve el número de la factura.
     */
    public function getNumero(): ?int {
        return $this->numero;
    }

    /**
     * Establece el número de la factura.
     */
    public function setNumero(int $numero): void {
        $this->numero = $numero;
    }

    /**
     * Devuelve la fecha de la factura.
     */
    public function getFecha(): DateTime {
        return $this->fecha;
    }

    /**
     * Devuelve el valor total de la factura.
     */
    public function getTotal(): float {
        return $this->total;
    }

    /**
     * Devuelve el cliente asociado a la factura.
     */
    public function getCliente(): Cliente {
        return $this->cliente;
    }

    /**
     * Devuelve el vendedor asociado a la factura.
     */
    public function getVendedor(): Vendedor {
        return $this->vendedor;
    }

    /**
     * Devuelve el listado de productos incluidos en la factura.
     * Cada elemento es un objeto de la clase ProductosPorFactura.
     */
    public function getProductosPorFactura(): array {
        return $this->itemsPorFactura;
    }
}
?>
