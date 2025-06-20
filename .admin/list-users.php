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

$result = $conn->query("SELECT id, username FROM users ORDER BY username ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List Users - BadenTracker</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .user-list { background: #fff; padding: 30px; margin: 100px auto; width: 350px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="user-list">
        <h2>User List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <a href="./">Back to admin panel</a>
    </div>
</body>
</html>