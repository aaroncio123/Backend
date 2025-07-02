<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "login");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    header("Location: registro.html?status=error");
    exit;
}

$username = $conexion->real_escape_string($_POST['username']);
$email = $conexion->real_escape_string($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Verificar si el usuario ya existe
$stmt = $conexion->prepare("SELECT id_account FROM login_admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // usuario ya existe
    header("Location: registro.html?status=exists");
    exit;
}
$stmt->close();

// Insertar nuevo usuario
$stmt = $conexion->prepare("INSERT INTO login_admins (username, email, clave) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $password);

if ($stmt->execute()) {
    header("Location: registro.html?status=success");
} else {
    header("Location: registro.html?status=error");
}

$stmt->close();
$conexion->close();
?>
