<?php
session_start();

// Configuración de la base de datos
$host = "localhost";
$user = "";
$pass = "";
$dbname = "badentracker";

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Consulta preparada para evitar inyección SQL
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - BadenTracker</title>
</head>
<body>
    <h2>Iniciar sesión</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="login.php" method="post">
        <input type="text" name="username" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>