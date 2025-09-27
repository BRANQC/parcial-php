<?php
// Se incluye la clase de conexión a la base de datos
require_once 'ControlConexionPdo.php';
// Se incluye el modelo Empresa para trabajar con sus objetos
require_once '../modelo/Empresa.php';

/**
 * Clase ControlEmpresa
 * Encargada de gestionar las operaciones de base de datos relacionadas
 * con la entidad Empresa.
 */
class ControlEmpresa {
    // Atributo que puede contener un objeto Empresa o ser null
    private ?Empresa $objEmpresa;

    /**
     * Constructor de la clase
     * Puede recibir un objeto Empresa o nada (null).
     * Esto permite reutilizar la clase para operaciones que no
     * requieren un objeto específico (por ejemplo, listar todas las empresas).
     */
    public function __construct(?Empresa $objEmpresa = null) {
        $this->objEmpresa = $objEmpresa;
    }

    /**
     * Guarda una nueva empresa en la base de datos.
     * @return bool true si se insertó correctamente, false en caso de error.
     */
    public function guardar(): bool {
        try {
            $cod = $this->objEmpresa->getCodigo();
            $nom = $this->objEmpresa->getNombre();

            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "INSERT INTO empresa (codigo, nombre) VALUES(?, ?)";
            $parametros = [$cod, $nom];

            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);
            $objControlConexionPdo->cerrarBd();

            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Modifica los datos de una empresa existente.
     * @return bool true si se actualizó correctamente, false en caso de error.
     */
    public function modificar(): bool {
        try {
            $cod = $this->objEmpresa->getCodigo();
            $nom = $this->objEmpresa->getNombre();

            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "UPDATE empresa SET nombre = ? WHERE codigo = ?";
            $parametros = [$nom, $cod];

            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);
            $objControlConexionPdo->cerrarBd();

            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Elimina una empresa de la base de datos.
     * @return bool true si se eliminó correctamente, false en caso de error.
     */
    public function borrar(): bool {
        try {
            $cod = $this->objEmpresa->getCodigo();

            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "DELETE FROM empresa WHERE codigo = ?";
            $parametros = [$cod];

            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);
            $objControlConexionPdo->cerrarBd();

            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtiene un listado de todas las empresas registradas.
     * No requiere un objeto Empresa para ejecutarse.
     * @return array con todas las filas encontradas o vacío si no hay datos.
     */
    public function listar(): array {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "SELECT * FROM empresa";
            $recordSet = $objControlConexionPdo->ejecutarSelect($sql);

            $objControlConexionPdo->cerrarBd();

            return $recordSet;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Consulta una empresa por su código.
     * @return array|null Devuelve un array con los datos de la empresa o null si no se encuentra.
     */
    public function consultar(): ?array {
        try {
            $cod = $this->objEmpresa->getCodigo();

            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "SELECT * FROM empresa WHERE codigo = ?";
            $parametros = [$cod];

            $recordSet = $objControlConexionPdo->ejecutarSelect($sql, $parametros);
            $objControlConexionPdo->cerrarBd();

            if (count($recordSet) > 0) {
                return $recordSet[0]; // Devuelve el primer resultado
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }
}

