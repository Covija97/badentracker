<?php
session_start();

/* // Solo permite acceso al usuario admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: /login.php");
    exit;
} */

// Configuración de la base de datos
$host = "localhost";
$user = "bt";
$pass = "BadenTracker2025*";
$dbname = "badentracker";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_usuario = trim($_POST['username']);
    $nueva_contraseña = $_POST['password'];

    // Verifica que no esté vacío
    if ($nuevo_usuario && $nueva_contraseña) {
        // Verifica si el usuario ya existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $nuevo_usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "El usuario ya existe.";
        } else {
            $hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt_insert->bind_param("ss", $nuevo_usuario, $hash);
            if ($stmt_insert->execute()) {
                $mensaje = "Usuario creado correctamente.";
            } else {
                $mensaje = "Error al crear el usuario.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    } else {
        $mensaje = "Debes completar todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear usuario - BadenTracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .login-box {
            background: #fff;
            padding: 30px;
            margin: 100px auto;
            width: 350px;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
        }

        .mensaje {
            color: #d9534f;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Crear nuevo usuario</h2>
        <?php if ($mensaje): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>
        <form action="crear_usuario.php" method="post">
            <input type="text" name="username" placeholder="Nuevo usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Crear usuario</button>
        </form>
        <a href="dashboard.php">Volver al panel</a>
    </div>
</body>

</html>