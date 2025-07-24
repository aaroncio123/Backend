<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

class db_conector {

    public $conexion;

    public function __construct() {
        // Configuración según el entorno
        if ($_SERVER['HTTP_HOST'] === 'localhost') {
            $host = "localhost";
            $usuario = "root";
            $clave = "";
            $db = "base_unificada"; // <-- cámbialo por el nombre real de tu base local
        } else {
            $host = "sql103.infinityfree.com";     // <-- reemplaza con el host real
            $usuario = "if0_39546848";      // <-- reemplaza con tu usuario InfinityFree
            $clave = "Yn2BxBdR5J5JB";       // <-- reemplaza con tu contraseña
            $db = "if0_39546848_base_unificada";      // <-- reemplaza con el nombre de base de datos
        }

        // Conectar a la base de datos
        $this->conexion = new mysqli($host, $usuario, $clave, $db);

        if ($this->conexion->connect_error) {
            die("Error de conexión inesperada: " . $this->conexion->connect_error);
        }

        $this->conexion->set_charset("utf8");
    }

    public function insert($tabla, $campos, $valores) {
        $query = "INSERT INTO $tabla ($campos) VALUES ($valores)";
        $resultado = $this->conexion->query($query);
        if (!$resultado) {
            echo "Error al insertar los datos: " . $this->conexion->error . "<br>";
        }
        return $resultado;
    }

    public function search($tabla, $condicion) {
        $resultado = $this->conexion->query("SELECT * FROM $tabla WHERE $condicion");
        if ($resultado && $resultado->num_rows > 0) {
            return $resultado->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
?>
