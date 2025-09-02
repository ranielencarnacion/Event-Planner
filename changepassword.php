<?php
session_start();
require("_db_mysql.php");

$message = ''; // Variable for feedback message
$message_type = ''; // Success or error type

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            // Validate new password length (4-16 characters)
            if (strlen($new_password) >= 4 && strlen($new_password) <= 16) {
                // Fetch user from database
                $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
                $stmt->bindParam(':id', $_SESSION['user_id']);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Verify current password
                    if (password_verify($current_password, $user['password'])) {
                        // Hash new password and update it
                        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Update the password in the database
                        $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
                        $stmt->bindParam(':password', $hashed_new_password);
                        $stmt->bindParam(':id', $_SESSION['user_id']);

                        if ($stmt->execute()) {
                            $message = "Password has successfully changed!";
                            $message_type = "success";
                        } else {
                            $message = "Failed to update password.";
                            $message_type = "error";
                        }
                    } else {
                        $message = "Current password is incorrect!";
                        $message_type = "error";
                    }
                } else {
                    $message = "User not found!";
                    $message_type = "error";
                }
            } else {
                $message = "New password must be between 4 and 16 characters!";
                $message_type = "error";
            }
        } else {
            $message = "New passwords do not match!";
            $message_type = "error";
        }
    } else {
        $message = "Please fill in all fields!";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style type="text/css">
    <title>Event Planner | Change Password</title>
    <style type="text/css">
    p, body, td, input, select, button { font-family: -apple-system,system-ui,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif; font-size: 14px; }
    body { padding: 0px; margin: 0px; background-color: #ffffff; }
    .space { margin: 10px 0px 10px 0px; }
    .header { 
    background: #87ceeb; /* Fallback color (sky blue) */
    background: linear-gradient(to right, #87cefa 0%, #b0e0e6 44%, #87cefa 100%);
    padding: 20px 10px; 
    color: white; 
    box-shadow: 0px 0px 10px 5px rgba(0, 0, 0, 0.75); 
}


    .header a { color: white; }
    .header h1 a { text-decoration: none; color: black;}
    .header h1 { padding: 0px; margin: 0px; }
    .main { padding: 10px; margin-top: 10px; }
  </style>
  <style>
        /* Minimal custom styles for event planner */
        .container {
    padding: 20px; /* Adjust padding if needed */
    background-color: #f0f0f0;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    margin: 20px auto; /* Centers the container horizontally */
    max-width: 600px; /* Set a maximum width to control its size */
    border-radius: 8px; /* Optional: adds rounded corners */
}


        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .message.success {
            color: #007bff;
            background-color: #e7f0ff;
        }

        .message.error {
            color: #dc3545;
            background-color: #f8d7da;
        }

        form button[type="submit"] {
            background-color: #0056b3;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 10px;
        }

        form button[type="submit"]:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        h1 {
            color: #333;
            font-size: 24px;
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <br>
    <a href="index.php" style="display: inline-block; padding: 10px 20px; background-color: #0056b3; color: #fff; text-decoration: none; border-radius: 5px; font-size: 16px;">Go back</a>


    <!-- Event Planner Content -->
    <div class="container">
        <h1>Change Password</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" placeholder="Current Password..." required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" placeholder="New Password..." required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password..." required>

            <button type="submit">Change Password</button>
        </form>
    </div>

</body>
</html>
