<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="blur-bg-overlay"></div>
    <div class="form-popup">
        <div class="form-box reset" id="reset-form">
            <div class="form-details">
                <h2>Reset Password</h2>
                <p>Enter your email, reset token, and new password to reset your account password.</p>
            </div>
            <div class="form-content">
                <h2>RESET PASSWORD</h2>
                <form action="forgotpasswordreset.php" method="POST">
                    <div class="input-field">
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-field">
                        <input type="text" name="reset_token" required>
                        <label>Reset Token</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="new_password" required>
                        <label>New Password</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="confirm_password" required>
                        <label>Confirm New Password</label>
                    </div>
                    <button type="submit">Reset Password</button>
                </form>
                <div class="bottom-link">
                    Remember your password?
                    <a href="login.php">Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
