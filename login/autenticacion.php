<?php
session_start();

// credenciales de base de datos
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "login";

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
        echo "Usuario encontrado<br>"; 
        $stmt->bind_result($id_account, $password_from_db);
        $stmt->fetch();

        if (password_verify($_POST['password'], $password_from_db)) {
            echo "Contraseña verificada<br>"; 
            session_regenerate_id();
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id_account;
            header('Location: vista_data.php');
            exit;
        } else {
            echo "Contraseña incorrecta<br>"; 
            header('Location: login.html?error=pass');
            exit;
        
        }
    } else {
        echo "Usuario no encontrado<br>"; 
        header('Location: login.html?error=user');
        exit;
    }

    $stmt->close();
} else {
    echo 'Error en la consulta preparada';
}


?>