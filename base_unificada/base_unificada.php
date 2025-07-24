<?php
include("db_conector.php");
$user = new db_conector();

$dniBusqueda = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buscar']) && !empty($_POST['dni'])) {
        $dniBusqueda = $_POST['dni'];
    }

    if (isset($_POST['insertar'])) {
        $fp = fopen("base_unificada.csv", "r");
        $primeraLinea = true;
        while (($data = fgetcsv($fp, 1000, ",")) !== false) {
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
                $marca_temporal = date("Y-m-d H:i:s");
                $datos = [
                    $user->conexion->real_escape_string($correo),
                    $user->conexion->real_escape_string($nombre_completo),
                    $user->conexion->real_escape_string($distrito),
                    $user->conexion->real_escape_string($tipo_documento),
                    $user->conexion->real_escape_string($numero_documento),
                    $user->conexion->real_escape_string($telefono),
                    $user->conexion->real_escape_string($vulnerabilidades),
                    $user->conexion->real_escape_string($marca_temporal)
                ];
                $valores = "'" . implode("','", $datos) . "'";
                $campos  = "correo, nombre_completo, distrito, tipo_documento, numero_documento, telefono, vulnerabilidades, marca_temporal";
                $user->insert("base_unificada_dates", $campos, $valores);
            }
        }
        fclose($fp);
        echo "<p style='color:green;text-align:center;'>✅ Datos insertados correctamente desde CSV.</p>";
    }

    if (isset($_POST['eliminar_dni'])) {
        $dniEliminar = $user->conexion->real_escape_string($_POST['eliminar_dni']);
        $user->conexion->query("DELETE FROM base_unificada_dates WHERE numero_documento = '$dniEliminar'");
        echo "<p style='color:red;text-align:center;'>❌ Registro con DNI $dniEliminar eliminado de la base de datos.</p>";
    }

    if (isset($_POST['descargar'])) {
        $resultado = $user->search("base_unificada_dates", "1");
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="datos_exportados.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ["ID", "Correo", "Nombres Completos", "Distrito", "Tipo de documento", "Numero de documento", "Telefono", "Vulnerabilidades", "Marca Temporal"]);
        foreach ($resultado as $fila) {
            fputcsv($out, [
                $fila['id_persona'],
                $fila['correo'],
                $fila['nombre_completo'],
                $fila['distrito'],
                $fila['tipo_documento'],
                $fila['numero_documento'],
                $fila['telefono'],
                $fila['vulnerabilidades'],
                $fila['marca_temporal']
            ]);
        }
        fclose($out);
        exit;
    }
}

$u = !empty($dniBusqueda)
    ? $user->search("base_unificada_dates", "numero_documento = '$dniBusqueda'")
    : $user->search("base_unificada_dates", "1");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>ADRA PERÚ</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f1f4f8;
      margin: 0;
      padding: 30px;
      position: relative;
    }
    .regresar-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      padding: 10px 18px;
      background-color: #2c3e50;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s ease;
    }
    .regresar-btn:hover {
      background-color: #1a252f;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    form {
      text-align: center;
      margin-bottom: 30px;
    }
    input[type="text"] {
      padding: 8px 12px;
      font-size: 14px;
    }
    button {
      padding: 8px 14px;
      margin: 5px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    button[name="buscar"] {
      background-color: #1e88e5;
      color: white;
    }
    button[name="insertar"] {
      background-color: #43a047;
      color: white;
    }
    button[name="descargar"] {
      background-color: #3949ab;
      color: white;
    }
    button[type="submit"]:hover {
      opacity: 0.9;
    }
    table {
      border-collapse: collapse;
      margin: 0 auto;
      background: white;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      table-layout: fixed;
      width: 100%;
    }
    th, td {
      padding: 10px 15px;
      border: 1px solid #ccc;
      font-size: 14px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: normal;
      word-wrap: break-word;
      word-break: break-word;
    }
    th {
      background-color: #f2f2f2;
    }
    /* ancho de cada columna */
    th.id, td.id {
      width: 40px;      
      max-width: 40px;
      text-align: center;
    }
    th.correo, td.correo {
      width: 150px;     
      max-width: 150px;
    }
    th.nombre, td.nombre {
      width: 250px;    
      max-width: 250px;
    }
    th.distrito, td.distrito {
      width: 120px;    
      max-width: 120px;
    }
    th.tipo_documento, td.tipo_documento {
      width: 130px;     
      max-width: 130px;
    }
    th.numero_documento, td.numero_documento {
      width: 130px;     
      max-width: 130px;
    }
    th.telefono, td.telefono {
      width: 120px;     
      max-width: 120px;
    }
    th.vulnerabilidades, td.vulnerabilidades {
      width: 180px;    
      max-width: 180px;
    }
    th.marca_temporal, td.marca_temporal {
      width: 160px;     
      max-width: 160px;
    }
    th.acciones, td.acciones {
      width: 90px;      
      max-width: 90px;
      text-align: center;
    }

    .scrollable {
      max-height: 400px;
      overflow-y: auto;
      margin: 0 auto;
      width: 100%;
    }
    .btn-eliminar {
      background-color: #e53935;
      color: white;
      padding: 6px 10px;
      border-radius: 5px;
      font-size: 12px;
    }
  </style>
</head>
<body>

  <a href="../Login/vista_data.php" class="regresar-btn">Regresar</a>

  <h2>Gestión de Registros de Base de Datos Principal</h2>

  <form method="post">
      <label for="dni">Buscar por DNI:</label>
      <input type="text" name="dni" id="dni" placeholder="Ingrese DNI">
      <button type="submit" name="buscar">Buscar</button><br><br>
      <button type="submit" name="insertar">Insertar desde CSV</button>
      <button type="submit" name="descargar">Descargar CSV</button>
  </form>

  <?php if (!empty($u)): ?>
    <div class="scrollable">
      <table>
        <tr>
          <th class="id">ID</th>
          <th class="correo">Correo</th>
          <th class="nombre">Nombre Completo</th>
          <th class="distrito">Distrito</th>
          <th class="tipo_documento">Tipo de Documento</th>
          <th class="numero_documento">N° Documento</th>
          <th class="telefono">Teléfono</th>
          <th class="vulnerabilidades">Vulnerabilidades</th>
          <th class="marca_temporal">marca_temporal</th>
          <th class="acciones">Acciones</th>
        </tr>
        <?php foreach ($u as $fila): ?>
        <tr>
          <td class="id"><?= htmlspecialchars($fila['id_persona']) ?></td>
          <td class="correo"><?= htmlspecialchars($fila['correo']) ?></td>
          <td class="nombre"><?= htmlspecialchars($fila['nombre_completo']) ?></td>
          <td class="distrito"><?= htmlspecialchars($fila['distrito']) ?></td>
          <td class="tipo_documento"><?= htmlspecialchars($fila['tipo_documento']) ?></td>
          <td class="numero_documento"><?= htmlspecialchars($fila['numero_documento']) ?></td>
          <td class="telefono"><?= htmlspecialchars($fila['telefono']) ?></td>
          <td class="vulnerabilidades"><?= htmlspecialchars($fila['vulnerabilidades']) ?></td>
          <td class="marca_temporal"><?= htmlspecialchars($fila['marca_temporal']) ?></td>
          <td class="acciones">
            <form method="post" onsubmit="return confirm('¿Seguro que deseas eliminar este registro?')">
              <input type="hidden" name="eliminar_dni" value="<?= htmlspecialchars($fila['numero_documento']) ?>">
              <button type="submit" class="btn-eliminar">Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  <?php else: ?>
    <p style="text-align:center;">No hay datos para mostrar.</p>
  <?php endif; ?>

</body>
</html>
