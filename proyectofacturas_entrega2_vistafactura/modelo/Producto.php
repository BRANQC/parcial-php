<?php
class Producto {
    private string $codigo;
    private string $nombre;
    private float $valorUnitario;
    private int $existencia;

    // Constructor con los mismos campos que la tabla
    public function __construct(string $codigo, string $nombre, float $valorUnitario, int $existencia) {
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->valorUnitario = $valorUnitario;
        $this->existencia = $existencia;
    }

    // Getters y Setters
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

    public function getValorUnitario(): float {
        return $this->valorUnitario;
    }

    public function setValorUnitario(float $valorUnitario): void {
        $this->valorUnitario = $valorUnitario;
    }

    public function getExistencia(): int {
        return $this->existencia;
    }

    public function setExistencia(int $existencia): void {
        $this->existencia = $existencia;
    }
}
?>
