<?php
// ======================================
// Inclusión de las clases necesarias
// ======================================

// Clase de conexión a la base de datos mediante PDO
require_once 'ControlConexionPdo.php';
// Modelo Cliente (hereda de Persona)
require_once '../modelo/Cliente.php';
// Modelo Persona (base del Cliente)
require_once '../modelo/Persona.php';
// Controladores auxiliares (si se requiere usarlos en algún momento)
require_once 'ControlPersona.php';
require_once 'ControlEmpresa.php';

// ======================================
// Clase ControlCliente
// Encargada de la lógica para manejar clientes en la BD
// ======================================
class ControlCliente {
    // Objeto del tipo Cliente que se manipula
    var $objCliente;
    
    // Constructor: recibe un objeto Cliente y lo almacena
    function __construct($objCliente) {
        $this->objCliente = $objCliente;
    }
    
    // ======================================
    // Método para guardar un nuevo cliente
    // ======================================
    function guardar() {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            $objControlConexionPdo->beginTransaction(); // Inicia transacción (importante porque toca 2 tablas)

            // 1. Guardar en la tabla persona
            $sqlPersona = "INSERT INTO persona (codigo, nombre, email, telefono) VALUES (?, ?, ?, ?)";
            $parametrosPersona = [
                $this->objCliente->getCodigo(),
                $this->objCliente->getNombre(),
                $this->objCliente->getEmail(),
                $this->objCliente->getTelefono()
            ];
            $resultadoPersona = $objControlConexionPdo->ejecutarComandoSql($sqlPersona, $parametrosPersona);

            // 2. Guardar en la tabla cliente
            $cred = $this->objCliente->getCredito();
            $codPer = $this->objCliente->getCodigo(); // fk hacia persona
            $codEmp = $this->objCliente->getFkcodempresa();

            // Si no se especifica empresa, se guarda como null
            $codEmp = empty($codEmp) ? null : $codEmp;

            $sqlCliente = "INSERT INTO cliente (credito, fkcodpersona, fkcodempresa) VALUES (?, ?, ?)";
            $parametrosCliente = [$cred, $codPer, $codEmp];
            $resultadoCliente = $objControlConexionPdo->ejecutarComandoSql($sqlCliente, $parametrosCliente);

            // 3. Validar resultados y confirmar o deshacer
            if ($resultadoPersona && $resultadoCliente) {
                $objControlConexionPdo->commit(); // Confirma transacción
                return true;
            } else {
                $objControlConexionPdo->rollBack(); // Deshace cambios si algo falló
                return false;
            }
        } catch (Exception $e) {
            $objControlConexionPdo->rollBack(); // Asegura rollback en caso de excepción
            return false;
        } finally {
            $objControlConexionPdo->cerrarBd(); // Cierra conexión
        }
    }
    
    // ======================================
    // Método para modificar un cliente existente
    // ======================================
    function modificar() {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            $objControlConexionPdo->beginTransaction();

            // 1. Modificar tabla persona
            $sqlPersona = "UPDATE persona SET nombre = ?, email = ?, telefono = ? WHERE codigo = ?";
            $parametrosPersona = [
                $this->objCliente->getNombre(),
                $this->objCliente->getEmail(),
                $this->objCliente->getTelefono(),
                $this->objCliente->getCodigo()
            ];
            $resultadoPersona = $objControlConexionPdo->ejecutarComandoSql($sqlPersona, $parametrosPersona);

            // 2. Modificar tabla cliente
            $codEmp = $this->objCliente->getFkcodempresa();
            $codEmp = empty($codEmp) ? null : $codEmp; // Maneja null

            $sqlCliente = "UPDATE cliente SET credito = ?, fkcodempresa = ? WHERE fkcodpersona = ?";
            $parametrosCliente = [
                $this->objCliente->getCredito(),
                $codEmp,
                $this->objCliente->getCodigo()
            ];
            $resultadoCliente = $objControlConexionPdo->ejecutarComandoSql($sqlCliente, $parametrosCliente);

            // 3. Confirmar o deshacer
            if ($resultadoPersona && $resultadoCliente) {
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
    
    // ======================================
    // Método para borrar un cliente
    // ======================================
    function borrar() {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();
            $objControlConexionPdo->beginTransaction();
            
            // 1. Borrar primero en cliente (por la relación foránea)
            $sqlCliente = "DELETE FROM cliente WHERE fkcodpersona = ?";
            $parametrosCliente = [$this->objCliente->getCodigo()];
            $resultadoCliente = $objControlConexionPdo->ejecutarComandoSql($sqlCliente, $parametrosCliente);
            
            // 2. Borrar después en persona
            $sqlPersona = "DELETE FROM persona WHERE codigo = ?";
            $parametrosPersona = [$this->objCliente->getCodigo()];
            $resultadoPersona = $objControlConexionPdo->ejecutarComandoSql($sqlPersona, $parametrosPersona);
            
            // 3. Confirmar o revertir
            if ($resultadoCliente && $resultadoPersona) {
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
    
    // ======================================
    // Método para listar todos los clientes
    // ======================================
    function listar() {
        $objControlConexionPdo = new ControlConexionPdo();
        $objControlConexionPdo->abrirBd();
        
        // Se hace JOIN para obtener datos de persona y cliente
        $sql = "SELECT p.*, c.id AS idCliente, c.credito, c.fkcodempresa 
                FROM persona AS p 
                JOIN cliente AS c ON p.codigo = c.fkcodpersona";
        $recordSet = $objControlConexionPdo->ejecutarSelect($sql);
        
        $objControlConexionPdo->cerrarBd();
        
        return $recordSet;
    }
    
    // ======================================
    // Método para consultar un cliente por ID
    // ======================================
    function consultar() {
        $id = $this->objCliente->getId();
        
        $objControlConexionPdo = new ControlConexionPdo();
        $objControlConexionPdo->abrirBd();
        
        $sql = "SELECT p.*, c.id AS idCliente, c.credito, c.fkcodempresa 
                FROM persona AS p 
                JOIN cliente AS c ON p.codigo = c.fkcodpersona 
                WHERE c.id = ?";
        $parametros = [$id];
        
        $recordSet = $objControlConexionPdo->ejecutarSelect($sql, $parametros);
        
        $objControlConexionPdo->cerrarBd();
        
        // Devuelve un solo registro o null
        if (count($recordSet) > 0) {
            return $recordSet[0];
        } else {
            return null;
        }
    }
    
    // ======================================
    // Método para consultar un cliente por código de persona
    // ======================================
    function consultarPorCodigoPersona() {
        $codPer = $this->objCliente->getCodigo();
        
        $objControlConexionPdo = new ControlConexionPdo();
        $objControlConexionPdo->abrirBd();
        
        $sql = "SELECT p.*, c.id AS idCliente, c.credito, c.fkcodempresa 
                FROM persona AS p 
                JOIN cliente AS c ON p.codigo = c.fkcodpersona 
                WHERE c.fkcodpersona = ?";
        $parametros = [$codPer];
        
        $recordSet = $objControlConexionPdo->ejecutarSelect($sql, $parametros);
        
        $objControlConexionPdo->cerrarBd();
        
        if (count($recordSet) > 0) {
            return $recordSet[0];
        } else {
            return null;
        }
    }
}
