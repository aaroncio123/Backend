<?php
    session_start();
    if(!isset($_SESSION["loggedin"])){
        header("Location: login.html");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Vista de datos</title>

    <!-- Estilos y fuentes -->
    <link rel="stylesheet" href="style_data.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        .navbar-brand span {
            font-weight: 700;
            font-size: 1.2rem;
        }
        .navbar-text {
            display: flex;
            align-items: center;
            font-size: 1rem;
            font-weight: 500;
            color: #333;
        }
        .navbar-text i {
            margin-right: 8px;
            color: #0d6efd;
        }
        .logout-btn {
            margin-left: 15px;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="LOGOBASE.jpg" alt="Un Mundo sin Límites" height="40">
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
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item me-3">
                        <span class="navbar-text">
                            <i class="fas fa-user-circle"></i> 
                            Bienvenido, <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="btn btn-danger logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3"><i class="fas fa-database me-2 text-primary"></i>Selecciona la base de datos:</h4>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Base de datos general</span>
                    <a href="../base_unificada/base_unificada.php" class="btn btn-primary">
                        <i class="fas fa-cogs me-1"></i> Gestionar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</main>

</body>
</html>
