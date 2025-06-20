<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$host = "localhost";
$user = "bt";
$pass = "BadenTracker2025*";
$dbname = "badentracker";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $new_password = $_POST['password'];

    if ($username && $new_password) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $message = "User not found.";
        } else {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt_update->bind_param("ss", $hash, $username);
            if ($stmt_update->execute()) {
                $message = "Password updated successfully.";
            } else {
                $message = "Error updating password.";
            }
            $stmt_update->close();
        }
        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change User Password - BadenTracker</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .login-box { background: #fff; padding: 30px; margin: 100px auto; width: 350px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        input { width: 100%; padding: 10px; margin: 10px 0; }
        button { width: 100%; padding: 10px; background: #ffc107; color: #333; border: none; border-radius: 4px; }
        .message { color: #d9534f; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Change user password</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="change-password.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="New password" required>
            <button type="submit">Change password</button>
        </form>
        <a href="/.">Back to admin panel</a>
    </div>
</body>
</html>