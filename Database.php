<?php
class Database {
    private static $conn;

    public static function getConnection() {
        if (self::$conn === null) {
            $config = include('config.php');

            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            try {
                self::$conn = new PDO($dsn, $config['username'], $config['password']);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("Error de conexión: " . $e->getMessage());
                echo $e;
                throw new Exception("Error de conexión a la base de datos");
            }
        } else { 
            echo "Lo siento, no se pudo encontrar db.properties";
        }
        return self::$conn;
    }
}
?>