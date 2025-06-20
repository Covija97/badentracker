<?php
session_start();

/* // Solo permite acceso al usuario admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: /login.php");
    exit;
} */
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - BadenTracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .admin-box {
            background: #fff;
            padding: 30px;
            margin: 100px auto;
            width: 400px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin: 15px 0;
        }

        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .logout {
            color: #d9534f;
        }
    </style>
</head>

<body>
    <div class="admin-box">
        <h2>Panel de Administración</h2>
        <ul>
            <li><a href="create-user.php">Crear nuevo usuario</a></li>
            <li><a href="listar-usuarios.php">Listar usuarios</a></li>
            <li><a href="cambiar-password.php">Cambiar contraseña de usuario</a></li>
            <li><a href="/dashboard.php">Ir al dashboard</a></li>
            <li><a class="logout" href="/logout.php">Cerrar sesión</a></li>
        </ul>
    </div>
</body>

</html>