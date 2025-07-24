<?php
include("db_conector.php");
$user = new db_conector();

$mensaje = '';
$clase = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insertar_manual'])) {
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

        // Guarda en CSV
        $fp = fopen("base_unificada.csv", "a");
        fputcsv($fp, [$correo, $nombre_completo, $distrito, $tipo_documento, $numero_documento, $telefono, $vulnerabilidades]);
        fclose($fp);

        $mensaje = "✅ Usuario insertado correctamente y agregado al CSV.";
        $clase = "success";
    } else {
        $mensaje = "⚠️ Ya existe un registro con ese DNI.";
        $clase = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro Manual</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(to right, #e3eafc, #f5f8ff);
      color: #333;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
    }

    .regresar-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      padding: 10px 18px;
      background-color: #136e7aff;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 14px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.3s ease;
    }

    .regresar-btn:hover {
      background-color: #063db1;
    }

    .form-container {
      background: white;
      width: 100%;
      max-width: 500px;
      padding: 25px 30px;
      border-radius: 20px;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .form-container h3 {
      text-align: center;
      margin-bottom: 25px;
      color: #041a61;
      font-size: 26px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      font-size: 14px;
    }

    input[type="text"],
    input[type="email"] {
      width: 100%;
      padding: 12px 14px;
      margin-bottom: 16px;
      border: 1px solid #ccc;
      border-radius: 15px;
      background-color: #f9f9f9;
      transition: border-color 0.3s, box-shadow 0.3s;
      font-size: 13px;
    }

    input[type="text"]:focus,
    input[type="email"]:focus {
      border-color: #041a61;
      box-shadow: 0 0 8px rgba(4, 26, 97, 0.3);
      outline: none;
      background-color: #fff;
    }

    button[type="submit"] {
      width: 100%;
      padding: 12px;
      background-color: #041a61;
      color: white;
      border: none;
      border-radius: 18px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #063db1;
    }

    .alert {
      text-align: center;
      padding: 12px;
      border-radius: 12px;
      margin-bottom: 20px;
      font-weight: 500;
      font-size: 14px;
    }

    .alert.success {
      color: #155724;
      background-color: #d4edda;
    }

    .alert.error {
      color: #721c24;
      background-color: #f8d7da;
    }
  </style>
</head>
<body>

<a href="../login/login.html" class="regresar-btn">Regresar</a>
  <div class="form-container">
    <h3>Registro Manual de Usuario</h3>

    <?php if ($mensaje): ?>
      <div class="alert <?= $clase ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" name="insertar_manual" value="1">

      <label for="correo">Correo:</label>
      <input type="email" name="correo" id="correo" required>

      <label for="nombre_completo">Nombres completos:</label>
      <input type="text" name="nombre_completo" id="nombre_completo" required>

      <label for="distrito">Distrito:</label>
      <input type="text" name="distrito" id="distrito" required>

      <label for="tipo_documento">Tipo de documento:</label>
      <input type="text" name="tipo_documento" id="tipo_documento" required>

      <label for="numero_documento">N° documento:</label>
      <input type="text" name="numero_documento" id="numero_documento" required>

      <label for="telefono">Teléfono WhatsApp:</label>
      <input type="text" name="telefono" id="telefono">

      <label for="vulnerabilidades">Vulnerabilidades:</label>
      <input type="text" name="vulnerabilidades" id="vulnerabilidades" required>

      <button type="submit">Registrar Usuario</button>
    </form>
  </div>

</body>
</html>
