<?php
/**
 * Archivo de configuración para la conexión a la base de datos.
 *
 * Utilizar constantes 'define' es una buena práctica porque
 * los valores no pueden ser modificados una vez definidos,
 * garantizando la inmutabilidad de la configuración.
 */

//$GLOBALS['serv']="localhost";
//$GLOBALS['usua']="root";
//$GLOBALS['pass']="";
//$GLOBALS['bdat']="bdsisindica";
//$GLOBALS['port']=3306;

//$GLOBALS['serv']="serv1mysql.mysql.database.azure.com";
//$GLOBALS['usua']="adminmysql@serv1mysql";
//$GLOBALS['pass']="***********";
//$GLOBALS['bdat']="dbclientes";

// Define el nombre del servidor o la dirección IP del servidor de la base de datos.
define('DB_SERVER', 'localhost');

// Define el nombre de usuario para autenticarse en la base de datos.
define('DB_USER', 'root');

// Define la contraseña para el usuario de la base de datos.
// Se deja en blanco en este caso.
define('DB_PASS', '');
//  IMPORTANTE: En un entorno de producción real, NUNCA se debe
// almacenar la contraseña en el código. Se debe manejar con una
// variable de entorno para mayor seguridad. Por ejemplo:
// define('DB_PASS', getenv('DB_PASSWORD'));

// Define el nombre de la base de datos a la que se desea conectar.
define('DB_NAME', 'dbfacturas');

// Define el puerto del servidor de la base de datos.
// El puerto 3306 es el estándar para MySQL.
define('DB_PORT', 3306);

?>