<?php
session_start();

// Cambia esta bandera según el entorno
$entorno = 'hosting'; // 'local' o 'hosting'

if ($entorno === 'local') {
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "login"; // local
} else {
    $dbhost = "sql103.infinityfree.com"; // tu host remoto
    $dbuser = "if0_39546848";            // tu usuario en InfinityFree
    $dbpass = "Yn2BxBdR5J5JB";           // tu contraseña real
    $dbname = "if0_39546848_base_unificada"; // base en el hosting
}

// conexión
$conexion = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_error()) {
    exit('Error de conexión: ' . mysqli_connect_error());
}

// validamos datos
if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    header('Location: login.html?error=faltan_datos');
    exit;
}

if ($stmt = $conexion->prepare('SELECT id_account, clave FROM login_admins WHERE username = ?')) {
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_account, $password_from_db);
        $stmt->fetch();

        if (password_verify($_POST['password'], $password_from_db)) {
            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id_account;
            header('Location: vista_data.php');
            exit;
        } else {
            header('Location: login.html?error=pass');
            exit;
        }
    } else {
        header('Location: login.html?error=user');
        exit;
    }

    $stmt->close();
} else {
    echo 'Error en la consulta preparada';
}
?>
