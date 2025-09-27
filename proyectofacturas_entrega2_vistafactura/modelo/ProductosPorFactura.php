<?php
// =======================================
// Clase ProductosPorFactura
// =======================================
// Representa un ítem dentro de una factura:
//
// - Está asociada a una factura (FK).
// - Está asociada a un producto (FK).
// - Tiene una cantidad y un valor unitario.
// - Se puede calcular un subtotal = cantidad * valorUnitario.
//
// Nota: en BD se suele llamar "detalle_factura" o similar.
// =======================================

// Se incluye la clase Producto porque cada ítem hace referencia a un producto
require_once __DIR__ . '/Producto.php';

class ProductosPorFactura {
    // --- Atributos privados ---
    private ?int $id;               // Identificador del ítem (PK en tabla detalle, puede ser autoincremental)
    private Producto $producto;     // Producto vendido (relación con tabla producto)
    private int $cantidad;          // Cantidad del producto
    private float $valorUnitario;   // Precio unitario al momento de la venta
    private ?int $numeroFactura;    // Número de factura a la que pertenece (FK a Factura)

    /**
     * Constructor de ProductosPorFactura
     * 
     * @param ?int $id Identificador del ítem (null si es nuevo)
     * @param Producto $producto Objeto producto asociado
     * @param int $cantidad Cantidad vendida
     * @param float $valorUnitario Precio unitario al momento de la venta
     * @param ?int $numeroFactura Número de factura a la que pertenece
     */
    public function __construct(
        ?int $id,
        Producto $producto,
        int $cantidad,
        float $valorUnitario,
        ?int $numeroFactura
    ) {
        $this->id = $id;
        $this->producto = $producto;
        $this->cantidad = $cantidad;
        $this->valorUnitario = $valorUnitario;
        $this->numeroFactura = $numeroFactura;
    }

    // --- Getters y Setters ---

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getProducto(): Producto {
        return $this->producto;
    }

    public function setProducto(Producto $producto): void {
        $this->producto = $producto;
    }

    public function getCantidad(): int {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): void {
        $this->cantidad = $cantidad;
    }

    public function getValorUnitario(): float {
        return $this->valorUnitario;
    }

    public function setValorUnitario(float $valorUnitario): void {
        $this->valorUnitario = $valorUnitario;
    }

    public function getNumeroFactura(): ?int {
        return $this->numeroFactura;
    }

    public function setNumeroFactura(?int $numeroFactura): void {
        $this->numeroFactura = $numeroFactura;
    }

    /**
     * Calcula el subtotal del ítem (cantidad * valor unitario).
     */
    public function getSubtotal(): float {
        return $this->cantidad * $this->valorUnitario;
    }
}
?>
