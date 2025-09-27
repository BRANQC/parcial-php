<?php
// Se incluye la clase de conexión a la BD
require_once 'ControlConexionPdo.php';
// Se incluye el modelo Persona
require_once '../modelo/Persona.php';

/**
 * Clase ControlPersona
 * Sirve como intermediario entre el modelo (Persona.php) y la base de datos.
 * Aquí se definen los métodos CRUD (Crear, Leer, Actualizar, Eliminar).
 */
class ControlPersona {
    // Atributo que almacenará un objeto de tipo Persona
    private Persona $objPersona;

    /**
     * Constructor de la clase
     * @param Persona $objPersona instancia del modelo que se va a manejar
     */
    public function __construct(Persona $objPersona) {
        $this->objPersona = $objPersona;
    }

    /**
     * Guarda una persona en la base de datos.
     * Antes valida si ya existe (consultando por código).
     * @return bool True si se guarda, False si ya existe o falla.
     */
    public function guardar(): bool {
        // Primero se consulta si ya existe
        if ($this->consultar()) {
            return false; // Ya existe la persona
        }

        try {
            // Se extraen los datos desde el objeto Persona
            $cod = $this->objPersona->getCodigo();
            $nom = $this->objPersona->getNombre();
            $ema = $this->objPersona->getEmail();
            $tel = $this->objPersona->getTelefono();

            // Se abre la conexión
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            // Sentencia preparada para insertar
            $sql = "INSERT INTO persona (codigo, nombre, email, telefono) VALUES (?, ?, ?, ?)";
            $parametros = [$cod, $nom, $ema, $tel];

            // Se ejecuta
            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);

            // Se cierra la conexión
            $objControlConexionPdo->cerrarBd();

            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Modifica los datos de una persona existente en la base de datos.
     * @return bool True si se modifica, False si falla.
     */
    public function modificar(): bool {
        try {
            $cod = $this->objPersona->getCodigo();
            $nom = $this->objPersona->getNombre();
            $ema = $this->objPersona->getEmail();
            $tel = $this->objPersona->getTelefono();

            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "UPDATE persona SET nombre = ?, email = ?, telefono = ? WHERE codigo = ?";
            $parametros = [$nom, $ema, $tel, $cod];

            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);
            $objControlConexionPdo->cerrarBd();

            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Elimina una persona de la base de datos.
     * @return bool True si se elimina, False si falla.
     */
    public function borrar(): bool {
        try {
            $cod = $this->objPersona->getCodigo();

            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "DELETE FROM persona WHERE codigo = ?";
            $parametros = [$cod];

            $resultado = $objControlConexionPdo->ejecutarComandoSql($sql, $parametros);
            $objControlConexionPdo->cerrarBd();

            return $resultado;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Lista todas las personas registradas.
     * @return array Retorna un arreglo con todas las filas de la tabla persona.
     */
    public function listar(): array {
        try {
            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "SELECT * FROM persona";
            $recordSet = $objControlConexionPdo->ejecutarSelect($sql);

            $objControlConexionPdo->cerrarBd();

            return $recordSet;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Consulta una persona por su código.
     * @return array|null Retorna un array con los datos si existe, o null si no.
     */
    public function consultar(): ?array {
        try {
            $cod = $this->objPersona->getCodigo();

            $objControlConexionPdo = new ControlConexionPdo();
            $objControlConexionPdo->abrirBd();

            $sql = "SELECT * FROM persona WHERE codigo = ?";
            $parametros = [$cod];

            $recordSet = $objControlConexionPdo->ejecutarSelect($sql, $parametros);

            $objControlConexionPdo->cerrarBd();

            // Si encontró registros, devuelve el primero
            if (count($recordSet) > 0) {
                return $recordSet[0];
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }
}
?>
