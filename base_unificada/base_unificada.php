<?php
include("db_conector.php");
$user = new db_conector();

$dniBusqueda = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // BUSCAR
    if (isset($_POST['buscar']) && !empty($_POST['dni'])) {
        $dniBusqueda = $_POST['dni'];
    }

    // INSERTAR DESDE CSV
    if (isset($_POST['insertar'])) {
        $fp = fopen("base_unificada.csv", "r");
        $primeraLinea = true;
        while (($data = fgetcsv($fp, 1000, ",")) !== false) { // ✅ usar coma
            if ($primeraLinea) {
                $primeraLinea = false;
                continue;
            }
            if (count($data) < 7) continue;
            $correo           = trim($data[0]);
            $nombre_completo  = trim($data[1]);
            $distrito         = trim($data[2]);
            $tipo_documento   = trim($data[3]);
            $numero_documento = trim($data[4]);
            $telefono         = trim($data[5]);
            $vulnerabilidades = trim($data[6]);

            $existe = $user->search("base_unificada_dates", "numero_documento = '$numero_documento'");
            if (empty($existe)) {
                $datos = [
                    $user->conexion->real_escape_string(string: $correo),
                    $user->conexion->real_escape_string($nombre_completo),
                    $user->conexion->real_escape_string($distrito),
                    $user->conexion->real_escape_string($tipo_documento),
                    $user->conexion->real_escape_string($numero_documento),
                    $user->conexion->real_escape_string($telefono),
                    $user->conexion->real_escape_string($vulnerabilidades)
                ];
                $valores = "'" . implode("','", $datos) . "'";
                $campos  = "correo, nombre_completo, distrito, tipo_documento, numero_documento, telefono, vulnerabilidades";
                $user->insert("base_unificada_dates", $campos, $valores);
            }
        }
        fclose($fp);
        echo "<p style='color:green;text-align:center;'>Datos insertados correctamente desde CSV.</p>";
    }

    if (isset($_POST['eliminar_dni'])) {
        $dniEliminar = $user->conexion->real_escape_string($_POST['eliminar_dni']);
        $user->conexion->query("DELETE FROM base_unificada_dates WHERE numero_documento = '$dniEliminar'");
        echo "<p style='color:red;text-align:center;'>Registro con DNI $dniEliminar eliminado de la base de datos.</p>";
    }

    if (isset($_POST['descargar'])) {
        $resultado = $user->search("base_unificada_dates", "1");
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="datos_exportados.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ["ID", "Correo", "Nombres Completos", "Distrito", "Tipo de documento", "Numero de documento", "Telefono", "Vulnerabilidades"]);
        foreach ($resultado as $fila) {
            fputcsv($out, [
                $fila['id_persona'],
                $fila['correo'],
                $fila['nombre_completo'],
                $fila['distrito'],
                $fila['tipo_documento'],
                $fila['numero_documento'],
                $fila['telefono'],
                $fila['vulnerabilidades']
            ]);
        }
        fclose($out);
        exit;
    }

    // INSERTAR USUARIO MANUAL
    if (isset($_POST['insertar_manual'])) {
        $correo = $_POST['correo'];
        $nombre_completo = $_POST['nombre_completo'];
        $distrito = $_POST['distrito'];
        $tipo_documento = $_POST['tipo_documento'];
        $numero_documento = $_POST['numero_documento'];
        $telefono = $_POST['telefono'];
        $vulnerabilidades = $_POST['vulnerabilidades'];

        $existe = $user->search("base_unificada_dates", "numero_documento = '$numero_documento'");
        if (empty($existe)) {
            $datos = [
                $user->conexion->real_escape_string($correo),
                $user->conexion->real_escape_string($nombre_completo),
                $user->conexion->real_escape_string($distrito),
                $user->conexion->real_escape_string($tipo_documento),
                $user->conexion->real_escape_string($numero_documento),
                $user->conexion->real_escape_string($telefono),
                $user->conexion->real_escape_string($vulnerabilidades)
            ];
            $valores = "'" . implode("','", $datos) . "'";
            $campos  = "correo, nombre_completo, distrito, tipo_documento, numero_documento, telefono, vulnerabilidades";
            $user->insert("base_unificada_dates", $campos, $valores);

            // ✅ escribir usando coma
            $fp = fopen("base_unificada.csv", "a");
            fputcsv($fp, [$correo, $nombre_completo, $distrito, $tipo_documento, $numero_documento, $telefono, $vulnerabilidades]);
            fclose($fp);

            echo "<p style='color:green;text-align:center;'>Usuario insertado correctamente y agregado al CSV.</p>";
        } else {
            echo "<p style='color:red;text-align:center;'>Ya existe un registro con ese DNI.</p>";
        }
    }
}

if (!empty($dniBusqueda)) {
    $u = $user->search("base_unificada_dates", "numero_documento = '$dniBusqueda'");
} else {
    $u = $user->search("base_unificada_dates", "1");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ADRA PERÚ</title>
  <style>
    table { border-collapse: collapse; margin: 0 auto; }
    table, th, td { border: 1px solid black; }
    th, td { padding: 6px 12px; }
    form { text-align: center; margin-bottom: 20px; }
  </style>
</head>
<body>
    <h2 style="text-align:center;">Gestión de Registros de Base de datos principal</h2>
    
    <form method="post">
        <label for="dni">Buscar por DNI:</label>
        <input type="text" name="dni" id="dni" placeholder="Ingrese DNI">
        <button type="submit" name="buscar">Buscar</button>
        <br><br>
        <button type="submit" name="insertar" style="background-color:green; color:white;">Insertar CSV</button>
        <button type="submit" name="descargar" style="background-color:blue; color:white;">Descargar Excel</button>
    </form>

    <h3 style="text-align:center;">Insertar Usuario Manual</h3>
    <!-- ✅ corregido method -->
    <form method="post" style="width: 50%; margin: 0 auto; margin-bottom: 10px;">
        <input type="hidden" name="insertar_manual" value="1">
        <label>Correo: </label><br>
        <input type="email" name="correo"><br>
        <label>Nombres completos:</label><br>
        <input type="text" name="nombre_completo" required><br>
        <label>Distrito:</label><br>
        <input type="text" name="distrito"><br>
        <label>Tipo de documento:</label><br>
        <input type="text" name="tipo_documento" required><br>
        <label>N° documento:</label><br>
        <input type="text" name="numero_documento" required><br>
        <label>Teléfono WhatsApp:</label><br>
        <input type="text" name="telefono"><br>
        <label>Vulnerabilidades:</label><br>
        <input type="text" name="vulnerabilidades" required><br>
        <button type="submit" style="background-color:orange; margin-bottom: 10px;">Insertar Usuario</button>
    </form>

    <?php
    if (!empty($u)) {
        echo '<div style="max-height:400px; overflow:auto;">';
        echo "<table>";
        echo "<tr>
            <th>ID</th>
            <th>Correo</th>
            <th>Nombre Completo</th>
            <th>Distrito</th>
            <th>Tipo de documento</th>
            <th>Numero de documento</th>
            <th>Telefono</th>
            <th>Vulnerabilidades</th>
            <th>Acciones</th>
        </tr>";
        foreach ($u as $fila) {
            echo "<tr>
                <td>{$fila['id_persona']}</td>
                <td>{$fila['correo']}</td>
                <td>{$fila['nombre_completo']}</td>
                <td>{$fila['distrito']}</td>
                <td>{$fila['tipo_documento']}</td>
                <td>{$fila['numero_documento']}</td>
                <td>{$fila['telefono']}</td>
                <td>{$fila['vulnerabilidades']}</td>
                <td>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='eliminar_dni' value='{$fila['numero_documento']}'>
                        <button type='submit' onclick='return confirm(\"¿Seguro que deseas eliminar este registro?\")'>Eliminar</button>
                    </form>
                </td>
            </tr>";
        }
        echo "</table></div>";
    } else {
        echo "<p style='text-align:center;'>No hay datos para mostrar.</p>";
    }
    ?>
</body>
</html>
