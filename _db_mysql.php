<?php
$host = "127.0.0.1";
$port = 3306;
$username = "root";
$password = "";
$database = "calendar";

// Connect to the database server (without specifying a DB yet)
try {
    $db = new PDO("mysql:host=$host;port=$port", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $db->exec("CREATE DATABASE IF NOT EXISTS `$database`");
    
    // Now connect to the specific database
    $db = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables if they don't exist
    createTables($db);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to create required tables
function createTables($db)
{
    try {
        // Check if events table exists
        if (!tableExists($db, "events")) {
            $db->exec("CREATE TABLE events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name TEXT,
                start DATETIME NOT NULL,
                end DATETIME NOT NULL,
                color VARCHAR(30),
                user_id INT NOT NULL
            )");
        }

        // Check if users table exists
        if (!tableExists($db, "users")) {
            $db->exec("CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL
            )");
        }
    } catch (PDOException $e) {
        die("Error creating tables: " . $e->getMessage());
    }
}

// Function to check if a table exists
function tableExists($dbh, $tableName)
{
    $results = $dbh->query("SHOW TABLES LIKE '$tableName'");
    if (!$results) {
        return false;
    }
    return $results->rowCount() > 0;
}

// Handle registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if username already exists
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "Username already taken.";
        } else {
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->execute();
            echo "User registered successfully!";
        }
    } catch (PDOException $e) {
        echo "Registration failed: " . $e->getMessage();
    }
}

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: index.php");
                exit;
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "User not found.";
        }
    } catch (PDOException $e) {
        echo "Login failed: " . $e->getMessage();
    }
}
?>
