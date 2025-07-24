<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Sesión cerrada</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f3f3f3;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    h1 {
      color: #333;
    }

    .boton {
      padding: 10px 20px;
      background-color: #007BFF;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin-top: 20px;
    }

    .boton:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <h1>Sesión cerrada correctamente</h1>
<a href="../index.html" class="boton">REGRESAR A LA PÁGINA DE INICIO</a>
</body>
</html>
