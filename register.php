<?php
session_start();

// Database connection
$host = "127.0.0.1";
$port = 3306;
$username_db = "root";
$password_db = "";
$database = "calendar"; // The name of your database

try {
    // Connect to the server first
    $db = new PDO("mysql:host=$host;port=$port", $username_db, $password_db);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $db->exec("CREATE DATABASE IF NOT EXISTS `$database`");

    // Connect to the database
    $db = new PDO("mysql:host=$host;port=$port;dbname=$database", $username_db, $password_db);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the users table if it doesn't exist
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )
    ");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$successMessage = '';
$errorMessage = '';

// Handle registration process
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password for storage
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if username already exists
    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $errorMessage = "Username already taken.";
        } else {
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                $successMessage = "Registration successful! You can now log in.";
            } else {
                $errorMessage = "Registration failed.";
            }
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
    p, body, td, input, select, button { font-family: -apple-system,system-ui,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif; font-size: 14px; }
    body { padding: 0px; margin: 0px; background-color: #ffffff; }
    a { color: #1155a3; }
    .space { margin: 10px 0px 10px 0px; }
    .header { background: #003267; background: linear-gradient(to right, #011329 0%,#00639e 44%,#011329 100%); padding:20px 10px; color: white; box-shadow: 0px 0px 10px 5px rgba(0,0,0,0.75); }
    .header a { color: white; }
    .header h1 a { text-decoration: none; }
    .header h1 { padding: 0px; margin: 0px; }
    .main { padding: 10px; margin-top: 10px; }
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f0f0f0;
        width: 50%;
        height: auto;
        margin: auto;
        position: absolute;
        top: 45%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: 20px;
        border-radius: 10px;
    }
    h1 {
        margin-top: 80px;
        color: white;
        text-align: center;
    }
    button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px;
        cursor: pointer;
    }
    .error {
        margin-top: 10px;
        padding: 10px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        text-align: center;
    }
    .success {
        margin-top: 10px;
        padding: 10px;
        background-color: #dff0d8;
        color: #3c763d;
        border: 1px solid #d6e9c6;
        border-radius: 4px;
        text-align: center;
    }
  </style>
    <title>Register - Event Calendar</title>
</head>
<body>
<div class="header" style="display: flex; align-items: center; justify-content: center; position: relative; padding: 20px;">
    <img src="img/logo.png" alt="Event Calendar" style="width: 70px; position: absolute; left: 20px;">
    <h1>Create an Account</h1>
</div>
<br>

<!-- Registration Form -->
<div class="login-container">
    <form method="POST" action="register.php">
        <label for="password" style="color: rgba(0, 0, 0, 0.6); font-size: 14px;">
            (Your password must be at least 4 characters)
        </label>
        <br><br>
        <label for="username">Username:</label>
        <input type="text" name="username" required minlength="4" maxlength="16">
        <br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required minlength="4" maxlength="16">
        <br><br>
        <button type="submit" name="register">Register</button>
        <br><br>
        <a href="login.php">Already have an account? Log in here</a>
    </form>
</div>
<!-- Display Success or Error Message -->
<?php if ($successMessage): ?>
    <div class="success">
        <?php echo $successMessage; ?>
    </div>
<?php elseif ($errorMessage): ?>
    <div class="error">
        <?php echo $errorMessage; ?>
    </div>
<?php endif; ?>
</body>
