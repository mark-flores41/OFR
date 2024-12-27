<?php
session_start();
include('database.php');

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
if ($_SESSION['role'] !== 'Admin') {
    echo "Access denied. Only administrators can add products.";
    exit();
}

// Check if the form is submitted successfully
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Product added successfully!";
} else {
    $message = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        /* Styling for the add product page */
        body {
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 50%;
            margin: 0 auto;
            margin-top: 30px;
        }

        h1 {
            text-align: center;
            color: #4a90e2;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .submit-button {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            display: block;
            width: 100%;
        }

        .submit-button:hover {
            background-color: #357ab7;
        }

        .success-message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* Position the back to admin button at the top right */
        .back-to-admin-btn {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            position: fixed;
            top: 20px;
            right: 20px;
            text-align: center;
        }

        .back-to-admin-btn:hover {
            background-color: #357ab7;
        }
    </style>
</head>
<body>

    <!-- Back to Admin Button -->
    <a href="admin.php" class="back-to-admin-btn">Back to Admin page</a>

    <div class="container">
        <h1>Add New Food Product</h1>

        <!-- Success Message -->
        <?php if ($message): ?>
            <div class="success-message">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form action="add_process.php" method="POST" enctype="multipart/form-data">
            <!-- Food Name -->
            <div class="form-group">
                <label for="food_name">Food Name</label>
                <input type="text" id="food_name" name="food_name" required placeholder="Enter the food name">
            </div>

            <!-- Price -->
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" required placeholder="Enter the price">
            </div>

            <!-- Image -->
            <div class="form-group">
                <label for="image">Choose Image</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>

            <button type="submit" class="submit-button">Add Product</button>
        </form>
    </div>

</body>
</html>
