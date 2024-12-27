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
  <title>Delivering Orders</title>
  <style>
      body {
          font-family: Arial, sans-serif;
          margin: 0;
          padding: 0;
          background-color: #f3f4f6;
          display: flex;
          justify-content: center;
          align-items: flex-start;
          height: 100vh;
      }
      .container {
          background-color: white;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          width: 80%;
          max-width: 1200px;
      }
      h1 {
          text-align: center;
          color: #4a90e2;
          margin-bottom: 20px;
      }
      .order-list {
          margin-top: 20px;
      }
      .order-item {
          background-color: #f9f9f9;
          padding: 15px;
          margin-bottom: 10px;
          border-radius: 8px;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
          display: flex;
          align-items: center;
          justify-content: space-between;
      }
      .order-item img {
          width: 60px;  /* Image size */
          height: 60px;
          margin-right: 10px;
          border-radius: 8px;
      }
      .order-item div {
          flex-grow: 1;
      }
      .order-item .status {
          font-weight: bold;
          color: #4a90e2;
      }
      /* Style for Back to Orders Page button */
      .back-btn {
          display: inline-block;
          margin-top: 20px;
          padding: 12px 24px;
          background-color: #4a90e2;
          color: white;
          border: none;
          border-radius: 5px;
          cursor: pointer;
          text-decoration: none;
      }
      .back-btn:hover {
          background-color: #357ab7;
      }
  </style>
</head>
<body>
<div class="container">
    <h1>Delivering Orders</h1>
    <div class="order-list">
        <?php
        // Check if there are any delivering orders
        if (!empty($_SESSION['delivering_orders'])):
            foreach ($_SESSION['delivering_orders'] as $order):
                $food_images = [
                    "Burger" => "image/burger.png",
                    "Crispy Fries" => "image/fries.jpg",
                    "Hot Pizza" => "image/pizza.jpg",
                    "Drink" => "image/drinks.jpg",
                    "Shake" => "image/shake.jpg",
                    "Spaghetti" => "image/spaghetti.jpg",
                    "Steak" => "image/steak.jpg",
                ];
                $image_path = isset($food_images[$order['item']]) ? $food_images[$order['item']] : "";
        ?>
                <div class="order-item">
                    <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($order['item']) ?>">
                    <div>
                        <h3><?= htmlspecialchars($order['item']) ?></h3>
                        <p>Status: <span class="status"><?= htmlspecialchars($order['status']) ?></span></p>
                    </div>
                </div>
        <?php endforeach; ?>
        <?php else: ?>
            <p>No orders are being delivered right now.</p>
        <?php endif; ?>
    </div>
    <a href="deliveryriders.php" class="back-btn">Back to Orders</a>
</div>
</body>
</html>
