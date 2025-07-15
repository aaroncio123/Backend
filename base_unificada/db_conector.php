<?php
class db_conector {
    private $host = "localhost";
    private $usuario = "root"; // corregido
    private $clave = "";
    private $db = "base_unificada";
    public $conexion;

    public function __construct() {
        $this->conexion = new mysqli($this->host, $this->usuario, $this->clave, $this->db);
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
