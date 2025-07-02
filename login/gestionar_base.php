<?php
session_start();
if (!isset($_SESSION["loggedin"])) {
    header("Location: login.html");
    exit;
}

if (!isset($_GET['dbname'])) {
    echo "No se especificó ninguna base de datos.";
    exit;
}

$dbname = $_GET['dbname'];

// conexión
$conexion = new mysqli("localhost", "root", "", $dbname);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// obtener las tablas
$tablas = $conexion->query("SHOW TABLES");

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestionar <?php echo htmlspecialchars($dbname); ?></title>
  <link rel="stylesheet" href="style_data.css">
</head>
<body>
  <header>
    <h1>Gestionando la base: <?php echo htmlspecialchars($dbname); ?></h1>
    <a href="vista_data.php">Volver al panel</a>
  </header>

  <main>
    <h2>Tablas disponibles</h2>
    <ul>
      <?php
      if ($tablas) {
          while ($row = $tablas->fetch_array()) {
              echo "<li><a href=\"gestion_tabla.php?dbname=$dbname&tabla=$row[0]\">$row[0]</a></li>";
          }
      } else {
          echo "<li>No hay tablas</li>";
      }
      ?>
    </ul>
  </main>
</body>
</html>
