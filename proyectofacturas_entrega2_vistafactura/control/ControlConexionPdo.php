<?php
// Incluye el archivo de configuración centralizado
require_once '../control/configBd.php';

/**
 * Clase para el control de la conexión a la base de datos utilizando PDO.
 * Esta clase sirve como una capa de abstracción para manejar las operaciones
 * de conexión, desconexión, y ejecución de sentencias SQL, incluyendo transacciones.
 */
class ControlConexionPdo {
    // Propiedad privada para almacenar el objeto de conexión PDO.
    private $conn;
    
    /**
     * Constructor de la clase.
     * Inicializa la propiedad de conexión a nulo.
     */
    public function __construct() {
        $this->conn = null;
    }

    /**
     * Establece una conexión con la base de datos utilizando PDO.
     * Configura el manejo de errores para lanzar excepciones.
     */
    public function abrirBd() {
        try {
            // Construyendo el Data Source Name (DSN) para PDO.
            $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";port=" . DB_PORT;
            
            // Creando una nueva instancia de PDO.
            $this->conn = new PDO($dsn, DB_USER, DB_PASS);
            
            // Configurando PDO para que lance excepciones cuando ocurra un error.
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Opcional: Establecer el juego de caracteres a UTF-8 para evitar problemas de codificación.
            $this->conn->exec("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            // Capturando y mostrando cualquier error de conexión.
            echo "ERROR AL CONECTARSE AL SERVIDOR: " . $e->getMessage() . "\n";
            exit();
        }
    }

    /**
     * Cierra la conexión a la base de datos.
     */
    public function cerrarBd() {
        $this->conn = null;
    }

    /**
     * Ejecuta una sentencia SQL de comando (INSERT, UPDATE, DELETE).
     * @param string $sql La sentencia SQL a ejecutar.
     * @param array $parametros Un array de parámetros.
     * @return bool True si la ejecución fue exitosa, false en caso de error.
     */
    public function ejecutarComandoSql($sql, $parametros = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $resultado = $stmt->execute($parametros);
            return ($resultado !== false);
        } catch (PDOException $e) {
            echo "Error al ejecutar el comando SQL: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Ejecuta una sentencia SQL de consulta (SELECT).
     * @param string $sql La sentencia SQL de consulta.
     * @param array $params Un array de parámetros para la consulta.
     * @return array Un array asociativo con los resultados de la consulta o un array vacío en caso de error.
     */
    public function ejecutarSelect($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $recordSet;
        } catch (PDOException $e) {
            echo "ERROR en la consulta SELECT: " . $e->getMessage() . "\n";
            return [];
        }
    }

    /**
     * Inicia una transacción de base de datos.
     * @return bool True si la transacción se inició correctamente.
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Confirma los cambios realizados durante la transacción.
     * @return bool True si la confirmación fue exitosa.
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Revierte todos los cambios realizados durante la transacción.
     * @return bool True si la reversión fue exitosa.
     */
    public function rollBack() {
        return $this->conn->rollBack();
    }
}
?>
