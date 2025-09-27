<?php
// ========================================
// ControlVendedor
// ========================================
// Este controlador maneja todas las operaciones CRUD
// (Crear, Leer, Actualizar, Eliminar) para la entidad Vendedor.
//
// Un vendedor está dividido en dos tablas relacionadas:
// 1. persona   -> contiene los datos básicos (codigo, nombre, teléfono, email)
// 2. vendedor  -> contiene los datos específicos (id, carnet, dirección, fkcodpersona)
//
// Por eso, cada operación trabaja con ambas tablas dentro de
// una transacción para asegurar que los datos se mantengan consistentes.
// ========================================

// Se incluyen las clases necesarias
require_once 'ControlConexionPdo.php';
require_once '../modelo/Vendedor.php';
require_once '../modelo/Persona.php';

class ControlVendedor {
    private ?Vendedor $objVendedor;

    /**
     * Constructor del controlador
     * 
     * @param Vendedor|null $objVendedor Objeto vendedor a gestionar (puede ser null para listar)
     */
    public function __construct(?Vendedor $objVendedor = null) {
        $this->objVendedor = $objVendedor;
    }

    // ==========================
    // MÉTODO GUARDAR
    // ==========================
    public function guardar(): bool {
        $objControlConexionPdo = new ControlConexionPdo();
        try {
            $objControlConexionPdo->abrirBd();
            $objControlConexionPdo->beginTransaction();

            // 1. Guardar en la tabla persona
            $sqlPersona = "INSERT INTO persona (codigo, nombre, telefono, email) VALUES (?, ?, ?, ?)";
            $parametrosPersona = [
                $this->objVendedor->getCodigo(),
                $this->objVendedor->getNombre(),
                $this->objVendedor->getTelefono(),
                $this->objVendedor->getEmail()
            ];
            $resultadoPersona = $objControlConexionPdo->ejecutarComandoSql($sqlPersona, $parametrosPersona);

            // 2. Guardar en la tabla vendedor
            $sqlVendedor = "INSERT INTO vendedor (carnet, direccion, fkcodpersona) VALUES (?, ?, ?)";
            $parametrosVendedor = [
                $this->objVendedor->getCarnet(),
                $this->objVendedor->getDireccion(),
                $this->objVendedor->getCodigo()
            ];
            $resultadoVendedor = $objControlConexionPdo->ejecutarComandoSql($sqlVendedor, $parametrosVendedor);

            if ($resultadoPersona && $resultadoVendedor) {
                $objControlConexionPdo->commit();
                return true;
            } else {
                $objControlConexionPdo->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $objControlConexionPdo->rollBack();
            return false;
        } finally {
            $objControlConexionPdo->cerrarBd();
        }
    }

    // ==========================
    // MÉTODO MODIFICAR
    // ==========================
    public function modificar(): bool {
        $objControlConexionPdo = new ControlConexionPdo();
        try {
            $objControlConexionPdo->abrirBd();
            $objControlConexionPdo->beginTransaction();

            // 1. Modificar en persona
            $sqlPersona = "UPDATE persona SET nombre = ?, telefono = ?, email = ? WHERE codigo = ?";
            $parametrosPersona = [
                $this->objVendedor->getNombre(),
                $this->objVendedor->getTelefono(),
                $this->objVendedor->getEmail(),
                $this->objVendedor->getCodigo()
            ];
            $resultadoPersona = $objControlConexionPdo->ejecutarComandoSql($sqlPersona, $parametrosPersona);

            // 2. Modificar en vendedor
            $sqlVendedor = "UPDATE vendedor SET carnet = ?, direccion = ? WHERE fkcodpersona = ?";
            $parametrosVendedor = [
                $this->objVendedor->getCarnet(),
                $this->objVendedor->getDireccion(),
                $this->objVendedor->getCodigo()
            ];
            $resultadoVendedor = $objControlConexionPdo->ejecutarComandoSql($sqlVendedor, $parametrosVendedor);

            if ($resultadoPersona && $resultadoVendedor) {
                $objControlConexionPdo->commit();
                return true;
            } else {
                $objControlConexionPdo->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $objControlConexionPdo->rollBack();
            return false;
        } finally {
            $objControlConexionPdo->cerrarBd();
        }
    }

    // ==========================
    // MÉTODO BORRAR
    // ==========================
    public function borrar(): bool {
        $objControlConexionPdo = new ControlConexionPdo();
        try {
            $objControlConexionPdo->abrirBd();
            $objControlConexionPdo->beginTransaction();

            // 1. Borrar de vendedor
            $sqlVendedor = "DELETE FROM vendedor WHERE fkcodpersona = ?";
            $parametrosVendedor = [$this->objVendedor->getCodigo()];
            $resultadoVendedor = $objControlConexionPdo->ejecutarComandoSql($sqlVendedor, $parametrosVendedor);

            // 2. Borrar de persona
            $sqlPersona = "DELETE FROM persona WHERE codigo = ?";
            $parametrosPersona = [$this->objVendedor->getCodigo()];
            $resultadoPersona = $objControlConexionPdo->ejecutarComandoSql($sqlPersona, $parametrosPersona);

            if ($resultadoVendedor && $resultadoPersona) {
                $objControlConexionPdo->commit();
                return true;
            } else {
                $objControlConexionPdo->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $objControlConexionPdo->rollBack();
            return false;
        } finally {
            $objControlConexionPdo->cerrarBd();
        }
    }

    // ==========================
    // MÉTODO LISTAR
    // ==========================
    public function listar(): array {
        $objControlConexionPdo = new ControlConexionPdo();
        try {
            $objControlConexionPdo->abrirBd();

            $sql = "SELECT p.*, v.id AS idVendedor, v.carnet, v.direccion 
                    FROM persona AS p 
                    JOIN vendedor AS v ON p.codigo = v.fkcodpersona";
            $recordSet = $objControlConexionPdo->ejecutarSelect($sql);

            $lista = [];
            foreach ($recordSet as $fila) {
                $lista[] = new Vendedor(
                    (int)$fila['idVendedor'],
                    $fila['codigo'],
                    $fila['nombre'],
                    $fila['telefono'],
                    $fila['email'],
                    $fila['carnet'],
                    $fila['direccion']
                );
            }

            return $lista;
        } catch (Exception $e) {
            return [];
        } finally {
            $objControlConexionPdo->cerrarBd();
        }
    }

    // ==========================
    // MÉTODO CONSULTAR (por ID de vendedor)
    // ==========================
    public function consultar(): ?Vendedor {
        $objControlConexionPdo = new ControlConexionPdo();
        try {
            $objControlConexionPdo->abrirBd();

            $sql = "SELECT p.*, v.id AS idVendedor, v.carnet, v.direccion 
                    FROM persona AS p 
                    JOIN vendedor AS v ON p.codigo = v.fkcodpersona
                    WHERE v.id = ?";
            $parametros = [$this->objVendedor->getId()];

            $recordSet = $objControlConexionPdo->ejecutarSelect($sql, $parametros);

            if (count($recordSet) > 0) {
                $fila = $recordSet[0];
                return new Vendedor(
                    (int)$fila['idVendedor'],
                    $fila['codigo'],
                    $fila['nombre'],
                    $fila['telefono'],
                    $fila['email'],
                    $fila['carnet'],
                    $fila['direccion']
                );
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        } finally {
            $objControlConexionPdo->cerrarBd();
        }
    }
}
?>
