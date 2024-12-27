<?php
session_start();
include('database.php');

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupals Online Food Reservation</title>
    <style>
        /* General container styling */
        body {
            background: url('background.jpg') no-repeat center center fixed; /* Background image */
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #333;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 1200px;
            overflow: hidden;
            margin: 0 auto;
            margin-top: 30px;
        }

        h1, h2 {
            text-align: center;
            color: #4a90e2;
        }

        .buttons-container {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .buttons-container a {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
        }

        .buttons-container a:hover {
            background-color: #357ab7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #4a90e2;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        .add-product-btn {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .add-product-btn:hover {
            background-color: #357ab7;
        }
    </style>
</head>
<body>
    <!-- Buttons Container -->
    <div class="buttons-container">
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Admin Dashboard</h2>

        <!-- Add Product Button -->
        <a href="addproduct.php" class="add-product-btn">Add New Product</a>

        <h3>Orders</h3>
        <table>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Food Item</th>
                <th>Status</th>
            </tr>
            <?php
            $orders = $conn->query("SELECT * FROM orders");
            while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?= $order['order_id'] ?></td>
                    <td><?= $order['user_id'] ?></td>
                    <td><?= $order['food_item'] ?></td>
                    <td><?= $order['status'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3>Food Items</h3>
        <table>
            <tr>
                <th>Food Item</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php
            $food_items = $conn->query("SELECT * FROM menu");
            while ($food_item = $food_items->fetch_assoc()): ?>
                <tr>
                    <td><?= $food_item['name'] ?></td>
                    <td><?= $food_item['price'] ?></td>
                    <td>
                        <a href="edit_food_item.php?id=<?= $food_item['id'] ?>">Edit</a>
                        <a href="delete_food_item.php?id=<?= $food_item['id'] ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
