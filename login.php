<?php
session_start();

// Database connection parameters
$host = "127.0.0.1";
$port = 3306;
$username_db = "root";
$password_db = "";
$database = "calendar";

// Step 1: Connect to MySQL server (without specifying a DB)
try {
    $db = new PDO("mysql:host=$host;port=$port", $username_db, $password_db);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $db->exec("CREATE DATABASE IF NOT EXISTS `$database`");

    // Reconnect to the database
    $db = new PDO("mysql:host=$host;port=$port;dbname=$database", $username_db, $password_db);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed or database creation failed: " . $e->getMessage());
}

$message = ''; // Initialize the message variable
$success = false; // Track if login was successful

// Handle login process
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Fetch user from the database
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect to dashboard
                header("Location: index.php");
                exit;
            } else {
                $message = "Invalid credentials. Please try again.";
            }
        } else {
            $message = "Invalid credentials. Please try again.";
        }
    } catch (PDOException $e) {
        $message = "Error during login: " . $e->getMessage();
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
        height: 22%;
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

    <title>Login - Event Calendar</title>
</head>
<body>
<div class="header" style="display: flex; align-items: center; justify-content: center; position: relative; padding: 20px;">
    <img src="img/logo.png" alt="Event Calendar" style="width: 70px; position: absolute; left: 20px;">
    <h1 style="margin: 0; text-align: center; flex: 1;">Login to Event Calendar</h1>
</div>
<br>

<div class="login-container">
    <form action="login.php" method="POST"><br><br>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>
        <button type="submit" name="login">Login</button>
        <br><br>
        <a href="register.php">Not a member? Register here</a>
    </form>
</div>

<!-- Display messages -->
<?php if (!empty($message)): ?>
    <div class="<?php echo ($success ? 'success' : 'error'); ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>
</body>
</html>
