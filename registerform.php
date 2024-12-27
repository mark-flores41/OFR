<?php
// Include the database connection
include('database.php');
session_start();

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm-password']);
    $username = trim($_POST['username']);
    $municipality = trim($_POST['municipality']);
    $barangay = trim($_POST['barangay']);
    $sitioorzone = trim($_POST['sitioorzone']);
    $contact = trim($_POST['contact']);
    $role = trim($_POST['role']);

    // Validate input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registerError = "Invalid email format.";
    } elseif ($password !== $confirmPassword) {
        $registerError = "Passwords do not match.";
    } else {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();        

        if ($stmt->num_rows > 0) {
            $registerError = "Email already exists.";
        } else {
            // Encrypt the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password, contact_number, municipality, barangay, sitioorzone, role) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssssss", $username, $email, $hashedPassword, $contact, $municipality, $barangay, $sitioorzone, $role);
            if ($stmt->execute()) {
                // Registration successful, redirect to login automatically
                header("Location: login.php");
                exit(); // Ensure no further code is executed after the redirect
            } else {
                $registerError = "Registration failed, please try again.";
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Styling for the form and layout */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('image/pic.jpg');
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: rgba(255, 255, 255, 0.8);
            width: 100%;
            max-width: 450px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            backdrop-filter: blur(10px);
        }
        .register-container h1 {
            font-size: 26px;
            color: #333333;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 16px;
            color: #555;
            margin-bottom: 8px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f8f8f8;
            margin-bottom: 8px;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #4a90e2;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(74, 144, 226, 0.3);
            outline: none;
        }
        .submit-button {
            width: 100%;
            padding: 14px;
            background-color: #4a90e2;
            color: #fff;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit-button:hover {
            background-color: #357ab7;
        }
        .login-link {
            display: block;
            text-align: center;
            font-size: 16px;
            color: #4a90e2;
            margin-top: 20px;
            text-decoration: none;
        }
        .login-link:hover {
            text-decoration: underline;
        }
        .policy-text {
            font-size: 14px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .policy-text a {
            color: #4a90e2;
            text-decoration: none;
        }
        .policy-text a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) {
            .register-container {
                margin-top: 20px;
                padding: 20px;
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Create an Account</h1>

        <!-- Show error message if any -->
        <?php if (!empty($registerError)): ?>
            <div class="error-message"><?php echo $registerError; ?></div>
        <?php endif; ?>

        <form action="registerform.php" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Re-enter your password" required>
            </div>
            <div class="form-group">
                <label for="municipality">Municipality</label>
                <input type="text" id="municipality" name="municipality" placeholder="Enter your municipality" required>
            </div>
            <div class="form-group">
                <label for="barangay">Barangay</label>
                <input type="text" id="barangay" name="barangay" placeholder="Enter your barangay" required>
            </div>
            <div class="form-group">
                <label for="sitioorzone">Sitio/Zone</label>
                <input type="text" id="sitioorzone" name="sitioorzone" placeholder="Enter your sitio/zone" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="text" id="contact" name="contact" placeholder="Enter your contact number" required>
            </div>
            <div class="form-group">
                <label for="role">Choose Role</label>
                <select id="role" name="role" required>
                    <option value="Customer">Customer</option>
                    <option value="Admin">Admin</option>
                    <option value="Delivery Rider">Delivery Rider</option>
                </select>
            </div>
            <div class="policy-text">
                <input type="checkbox" id="policy" name="policy" required>
                <label for="policy">I agree to the <a href="#">Terms & Conditions</a></label>
            </div>
            <button type="submit" class="submit-button">Register</button>
        </form>
        <a href="login.php" class="login-link">Already have an account? Log In</a>
    </div>
</body>
</html>
