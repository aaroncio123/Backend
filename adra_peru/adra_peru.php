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
        $fp = fopen("adra_peru.csv", "r");
        $primeraLinea = true;
        while (($data = fgetcsv($fp, 1000, ";")) !== false) {
            if ($primeraLinea) {
                $primeraLinea = false;
                continue;
            }
            if (count($data) < 8) continue;

            $marca_temporal = date("Y-m-d H:i:s", strtotime(trim($data[0])));
            $consentimiento  = trim($data[1]);
            $nombres         = trim($data[2]);
            $tipo_doc        = trim($data[3]);
            $n_doc           = trim($data[4]);
            $tel_w           = trim($data[5]);
            $tel_l           = trim($data[6]);
            $correo          = trim($data[7]);

            $existe = $user->search("table_adra_peru", "n_documento = '$n_doc'");
            if (empty($existe)) {
                $datos = [
                    $user->conexion->real_escape_string($marca_temporal),
                    $user->conexion->real_escape_string($consentimiento),
                    $user->conexion->real_escape_string($nombres),
                    $user->conexion->real_escape_string($tipo_doc),
                    $user->conexion->real_escape_string($n_doc),
                    $user->conexion->real_escape_string($tel_w),
                    $user->conexion->real_escape_string($tel_l),
                    $user->conexion->real_escape_string($correo)
                ];
                $valores = "'" . implode("','", $datos) . "'";
                $campos  = "marca_temporal, consentimiento, nombres_completos, tipo_documento, n_documento, n_telefono_w, n_telefono_l, correo";
                $user->insert("table_adra_peru", $campos, $valores);
            }
        }
        fclose($fp);
        echo "<p style='color:green;text-align:center;'>Datos insertados correctamente desde CSV.</p>";
    }

    if (isset($_POST['eliminar_dni'])) {
        $dniEliminar = $user->conexion->real_escape_string($_POST['eliminar_dni']);
        $user->conexion->query("DELETE FROM table_adra_peru WHERE n_documento = '$dniEliminar'");
        echo "<p style='color:red;text-align:center;'>Registro con DNI $dniEliminar eliminado de la base de datos.</p>";
    }

    if (isset($_POST['descargar'])) {
        $resultado = $user->search("table_adra_peru", "1");
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="datos_exportados.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ["ID","Marca_Temporal","Consentimiento","Nombres_Completos","Tipo_Documento","Numero_Documento","Telefono_W","Telefono_L","Correo"]);
        foreach($resultado as $fila) {
            fputcsv($out, [
                $fila['id_persona'],
                $fila['marca_temporal'],
                $fila['consentimiento'],
                $fila['nombres_completos'],
                $fila['tipo_documento'],
                $fila['n_documento'],
                $fila['n_telefono_w'],
                $fila['n_telefono_l'],
                $fila['correo']
            ]);
        }
        fclose($out);
        exit;
    }

    // INSERTAR USUARIO MANUAL
    if (isset($_POST['insertar_manual'])) {
        $marca_temporal = date("Y-m-d H:i:s");
        $consentimiento = $_POST['consentimiento'];
        $nombres = $_POST['nombres'];
        $tipo_doc = $_POST['tipo_doc'];
        $n_doc = $_POST['n_doc'];
        $tel_w = $_POST['tel_w'];
        $tel_l = $_POST['tel_l'];
        $correo = $_POST['correo'];

        $existe = $user->search("table_adra_peru", "n_documento = '$n_doc'");
        if (empty($existe)) {
            $datos = [
                $user->conexion->real_escape_string($marca_temporal),
                $user->conexion->real_escape_string($consentimiento),
                $user->conexion->real_escape_string($nombres),
                $user->conexion->real_escape_string($tipo_doc),
                $user->conexion->real_escape_string($n_doc),
                $user->conexion->real_escape_string($tel_w),
                $user->conexion->real_escape_string($tel_l),
                $user->conexion->real_escape_string($correo)
            ];
            $valores = "'" . implode("','", $datos) . "'";
            $campos  = "marca_temporal, consentimiento, nombres_completos, tipo_documento, n_documento, n_telefono_w, n_telefono_l, correo";
            $user->insert("table_adra_peru", $campos, $valores);

            // también agregar al CSV
            $fp = fopen("adra_peru.csv", "a");
            fputcsv($fp, [$marca_temporal, $consentimiento, $nombres, $tipo_doc, $n_doc, $tel_w, $tel_l, $correo], ";");
            fclose($fp);

            echo "<p style='color:green;text-align:center;'>Usuario insertado correctamente y agregado al CSV.</p>";
        } else {
            echo "<p style='color:red;text-align:center;'>Ya existe un registro con ese DNI.</p>";
        }
    }
}

// Mostrar datos
if (!empty($dniBusqueda)) {
    $u = $user->search("table_adra_peru", "n_documento = '$dniBusqueda'");
} else {
    $u = $user->search("table_adra_peru", "1");
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
    <h2 style="text-align:center;">Gestión de Registros ADRA</h2>
    
    <form method="post">
        <label for="dni">Buscar por DNI:</label>
        <input type="text" name="dni" id="dni" placeholder="Ingrese DNI">
        <button type="submit" name="buscar">Buscar</button>
        <br><br>
        <button type="submit" name="insertar" style="background-color:green; color:white;">Insertar CSV</button>
        <button type="submit" name="descargar" style="background-color:blue; color:white;">Descargar Excel</button>
    </form>

    <h3 style="text-align:center;">Insertar Usuario Manual</h3>
    <form method="post" style="width: 50%; margin: 0 auto; margin-botom: 10px;">
        <input type="hidden" name="insertar_manual" value="1">
        <label>Nombres completos:</label><br>
        <input type="text" name="nombres" required><br><br>
        <label>Tipo documento:</label><br>
        <input type="text" name="tipo_doc" required><br><br>
        <label>N° documento:</label><br>
        <input type="text" name="n_doc" required><br><br>
        <label>Teléfono WhatsApp:</label><br>
        <input type="text" name="tel_w"><br><br>
        <label>Teléfono Local:</label><br>
        <input type="text" name="tel_l"><br><br>
        <label>Correo:</label><br>
        <input type="email" name="correo"><br><br>
        <label>Consentimiento:</label><br>
        <input type="text" name="consentimiento" required><br><br>
        <button class="subtitle_item" type="submit" style="background-color:orange; margin-bottom: 10px;">Insertar Usuario</button>
    </form>

    <!-- Tabla resultados -->
    <?php
    if (!empty($u)) {
        echo '<div style="max-height:400px; overflow:auto;">';
        echo "<table>";
        echo "<tr>
        <th>ID</th>
        <th>Marca_Temporal</th>
        <th>Consentimiento</th>
        <th>Nombres_Completos</th>
        <th>Tipo_Documento</th>
        <th>Numero_Documento</th>
        <th>Numero_Telefono_W</th>
        <th>Numero_Telefono_L</th>
        <th>Correo</th>
        <th>Acciones</th>
        </tr>";
        foreach ($u as $fila) {
            echo "<tr>
                <td>{$fila['id_persona']}</td>
                <td>{$fila['marca_temporal']}</td>
                <td>{$fila['consentimiento']}</td>
                <td>{$fila['nombres_completos']}</td>
                <td>{$fila['tipo_documento']}</td>
                <td>{$fila['n_documento']}</td>
                <td>{$fila['n_telefono_w']}</td>
                <td>{$fila['n_telefono_l']}</td>
                <td>{$fila['correo']}</td>
                <td>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='eliminar_dni' value='{$fila['n_documento']}'>
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
