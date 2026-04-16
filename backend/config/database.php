<?php

/**
 * Clase Database — Gestión de la conexión a MySQL mediante PDO.
 *
 * Implementa el patrón Singleton para garantizar que existe UNA SOLA instancia
 * de conexión durante toda la ejecución de la petición HTTP.
 * Esto evita abrir múltiples conexiones innecesarias al servidor MySQL,
 * ahorrando recursos y facilitando la reutilización de la conexión en cualquier modelo.
 *
 * Uso en cualquier modelo:
 *   $pdo = Database::getInstance();
 *   $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
 */
class Database
{
    /**
     * Almacena la única instancia PDO activa.
     * Null hasta la primera llamada a getInstance().
     * Static porque pertenece a la clase, no a ningún objeto.
     */
    private static ?PDO $instance = null;

    /**
     * Constructor privado: nadie puede hacer `new Database()` desde fuera de la clase.
     * Es el mecanismo que fuerza el uso de getInstance() y garantiza el Singleton.
     */
    private function __construct() {}

    /**
     * Bloqueamos la clonación para que nadie pueda duplicar la instancia con clone $db.
     * Sin esto, el Singleton sería fácil de romper.
     */
    private function __clone() {}

    /**
     * Bloqueamos la deserialización para prevenir ataques de PHP Object Injection.
     * Sin esto, un atacante podría serializar un objeto Database manipulado
     * y deserializarlo para ejecutar código arbitrario.
     */
    public function __wakeup(): void
    {
        throw new \Exception('No se puede deserializar la conexión a la base de datos.');
    }

    /**
     * Devuelve la instancia PDO, creándola solo si aún no existe.
     *
     * Lazy initialization: la conexión se abre solo cuando se necesita por primera vez,
     * no en el arranque de la aplicación. Esto es más eficiente para rutas que no usan BD.
     *
     * Las credenciales vienen EXCLUSIVAMENTE de $_ENV, cargado desde .env en el Front Controller.
     * Nunca se hardcodean aquí para no exponer credenciales en el repositorio.
     *
     * @return PDO Instancia de conexión lista para ejecutar prepared statements.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // Leemos las credenciales desde el entorno con valores por defecto seguros.
            // Si alguna variable falta, la conexión fallará y lo logueamos.
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $name = $_ENV['DB_NAME'] ?? '';
            $user = $_ENV['DB_USER'] ?? '';
            $pass = $_ENV['DB_PASS'] ?? '';

            // DSN (Data Source Name): cadena de conexión que PDO interpreta.
            // utf8mb4 soporta el rango completo de Unicode incluyendo emojis.
            // El charset utf8 clásico de MySQL solo soporta hasta 3 bytes por carácter
            // y no puede almacenar emojis ni algunos caracteres especiales.
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

            $options = [
                // ERRMODE_EXCEPTION: cualquier error de BD lanza una PDOException.
                // Permite usar try/catch en los modelos para manejar errores de forma limpia.
                // Sin esto, los errores fallarían silenciosamente o mostrarían warnings.
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

                // FETCH_ASSOC: las filas se devuelven como arrays asociativos [columna => valor].
                // Evitamos el doble índice (numérico + asociativo) de FETCH_BOTH, que desperdicia memoria.
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // EMULATE_PREPARES = false: fuerza prepared statements REALES en el servidor MySQL.
                // Con true (valor por defecto), PDO emula los prepared statements en PHP,
                // lo que es menos seguro. Con false, MySQL procesa la query en dos fases
                // separadas (prepare + execute), imposibilitando la inyección SQL real.
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                // En producción NUNCA mostramos el mensaje de error real al navegador.
                // Podría revelar credenciales, estructura de BD o configuración del servidor.
                // El error detallado va al log del servidor para diagnóstico interno.
                error_log('Error de conexión a BD: ' . $e->getMessage());
                http_response_code(500);
                exit('Error interno del servidor. Por favor, inténtalo más tarde.');
            }
        }

        return self::$instance;
    }
}
