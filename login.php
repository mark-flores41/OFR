<?php
// Include the database connection
include('database.php');
session_start(); // Start the session

$loginError = ''; // To store error messages

// If the user is already logged in, redirect to homepage
if (isset($_SESSION['user_id'])) {
    header("Location: homepage.php"); // Redirect if already logged in
    exit();
}

// Login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loginError = "Invalid email format.";
    } else {
        // Ensure $conn is used for the database connection
        if ($conn) {
            // Prepare statement to check if the email exists in the database
            $stmt = $conn->prepare("SELECT user_id, email, password, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Fetch the result
                $stmt->bind_result($user_id, $email_db, $password_db, $role_db);
                $stmt->fetch();

                // Verify password
                if (password_verify($password, $password_db)) {
                    // Password correct, set session variables
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['email'] = $email_db;
                    $_SESSION['role'] = $role_db;

                    // Redirect based on the role stored in the database
                    if ($role_db === 'Customer') {
                        header("Location: homepage.php");
                    } elseif ($role_db === 'Delivery Rider') {
                        header("Location: deliveryriders.php");
                    } elseif ($role_db === 'Admin') {
                        header("Location: admin.php");
                    }
                    exit();
                } else {
                    $loginError = "Incorrect password.";
                }
            } else {
                $loginError = "No user found with this email.";
            }
            $stmt->close();
        } else {
            $loginError = "Database connection error.";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Styling for login page */
        body {
            font-family: Arial, sans-serif;
            background-image: url('image/pic.jpg');
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        .login-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #333333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #555555;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .submit-button {
            width: 100%;
            padding: 10px;
            background-color: #4a90e2;
            color: #ffffff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-button:hover {
            background-color: #357ab7;
        }
        .forgot-password, .sign-up {
            display: block;
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
            color: #4a90e2;
            text-decoration: none;
        }
        .forgot-password:hover, .sign-up:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h1>Login</h1>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="submit-button">Login</button>
        <input type="hidden" name="action" value="login">
    </form>
    <p>Don't have an account? <a href="registerform.php" class="sign-up">Sign Up</a></p>
    <a href="#" class="forgot-password">Forgot your password?</a>
</div>

<?php
// If there's an error, display a SweetAlert2 notification
if ($loginError != '') {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '$loginError',
                position: 'top',
                toast: true,
                showConfirmButton: false,
                timer: 3000
            });
          </script>";
}
?>
</body>
</html>
