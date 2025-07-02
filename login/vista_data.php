<?php
    session_start();
    if(!isset($_SESSION["loggedin"])){
        header("Location: login.html");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_data.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <title>Vista de datos</title>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="../login/LOGO.jpg" alt="Logo Un Mundo sin Límites" height="40">
                <span class="ms-2">Un Mundo sin Límites</span>
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-mdb-toggle="collapse"
                data-mdb-target="#navbarToggleMenu"
                aria-controls="navbarToggleMenu"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarToggleMenu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0"> 
                    <span class="navbar-text-ms-3">
                        Bienvenido <?php echo htmlspecialchars($_SESSION['name']) ?>
                    </span>
                    <li class="nav-item"><a href="/GREGORIT - UMSL/contacto.html" class="nav-link">Salir</a></li>
                </ul>
            </div>
        </div>
    </nav>
    </div>
    </header>
    <div class="container mt-4">
        <h2>Selecciona la base de datos:</h2>
        <ul class="list-group">
            <li class="list-group-item">
            <a href="../adra_peru/adra_peru.php" class="btn btn-primary">Gestionar adra_peru</a>
            </li>
        </ul>
    </div>

</body>
</html>