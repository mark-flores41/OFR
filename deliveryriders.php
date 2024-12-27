<?php
session_start();
include('database.php'); // Include the database connection

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize session variables for orders if they do not exist
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [];
}

if (!isset($_SESSION['delivering_orders'])) {
    $_SESSION['delivering_orders'] = [];
}

// Handle incoming order from the homepage (AJAX request)
if (isset($_POST['add_order_to_rider']) && $_POST['add_order_to_rider'] == 'true') {
    // Add order from homepage directly to the rider's list
    $food_item = $_POST['food_item'];
    $status = $_POST['status'];

    // Add to the session orders array
    $_SESSION['orders'][] = ['item' => $food_item, 'status' => $status];

    // Optionally insert the order into the database
    $user_id = $_SESSION['user_id'];
    $stmt = $mysqli->prepare("INSERT INTO orders (user_id, food_item, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $food_item, $status);
    $stmt->execute();
    $stmt->close();

    // Respond with success
    echo json_encode(['status' => 'success']);
    exit();
}

// Handle order acceptance (standard functionality)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_order'])) {
    if (isset($_POST['order_index']) && is_numeric($_POST['order_index']) && $_POST['order_index'] >= 0) {
        $order_index = (int)$_POST['order_index'];

        // Check if the index is valid
        if ($order_index < count($_SESSION['orders'])) {
            // Connect to the database
            $mysqli = new mysqli("localhost", "username", "password", "online_reservation");

            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }

            // Get the order id from the session
            $order_id = $_SESSION['orders'][$order_index]['order_id'];

            // Update the order status to 'delivering' in the database
            $sql = "UPDATE orders SET status = 'delivering', delivery_rider_id = ? WHERE order_id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ii", $_SESSION['user_id'], $order_id);
            $stmt->execute();

            // Move the order to delivering orders list in session
            $_SESSION['delivering_orders'][] = $_SESSION['orders'][$order_index];
            unset($_SESSION['orders'][$order_index]);

            // Re-index the orders array after removing the order
            $_SESSION['orders'] = array_values($_SESSION['orders']);

            $stmt->close();
            $mysqli->close();

            // Return a JSON response to indicate success
            echo json_encode(['status' => 'success']);
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delivery Rider Dashboard</title>
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
          position: relative;
      }
      .header-buttons {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
      }
      .logout-btn {
          background-color: #e74c3c;
          color: white;
          padding: 10px 20px;
          border-radius: 4px;
          border: none;
          cursor: pointer;
      }

      .logout-btn:hover {
          background-color: #c0392b;
      }

      .delivered-history-btn {
          background-color: #4a90e2;
          color: white;
          padding: 10px 20px;
          border-radius: 4px;
          cursor: pointer;
          text-decoration: none;
      }

      .delivered-history-btn:hover {
          background-color: #357ab7;
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
          transition: transform 0.3s ease;
      }
      .order-item:hover {
          transform: scale(1.05);
      }
      .order-item img {
          width: 60px;
          height: 60px;
          margin-right: 10px;
          border-radius: 8px;
      }
      .order-item div {
          flex-grow: 1;
      }
      .order-item p {
          font-size: 16px;
          margin: 5px 0;
      }
      .order-item .status {
          font-weight: bold;
          color: #4a90e2;
      }
      .accept-btn {
          background-color: orange;
          color: white;
          border: none;
          padding: 5px 10px;
          border-radius: 4px;
          cursor: pointer;
      }
      .accept-btn:hover {
          background-color: #357ab7;
      }
  </style>
</head>
<body>
<div class="container">
    <div class="header-buttons">
        <form action="logout.php" method="POST">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
        <a href="deliveringpage.php" class="delivered-history-btn">View Delivering Orders</a>
    </div>

    <h1>Delivery Rider Dashboard</h1>

    <div class="order-list">
        <?php
        // Check if there are any orders
        if (!empty($_SESSION['orders'])):
            foreach ($_SESSION['orders'] as $index => $order):
                $food_images = [
                    "Burger" => "image/burger.png",
                    "Crispy Fries" => "image/fries.jpg",
                    "Hot Pizza" => "image/pizza.jpg",
                    "Drink" => "image/drinks.jpg",
                    "Shake" => "image/shake.jpg",
                    "Spaghetti" => "image/spaghetti.jpg",
                    "Steak" => "image/steak.jpg",
                ];
                $image_path = isset($food_images[$order['food_item']]) ? $food_images[$order['food_item']] : 'food_items';
                ?>
                <div class="order-item">
                    <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($order['food_item']) ?>">
                    <div>
                        <h3><?= htmlspecialchars($order['food_item']) ?></h3>
                        <p>Status: <span class="status"><?= htmlspecialchars($order['status']) ?></span></p>
                    </div>
                    <?php if ($order['status'] == 'Waiting for orders'): ?>
                        <form action="deliveryrider.php" method="POST" class="accept-order-form" data-order-index="<?= $index ?>">
                            <input type="hidden" name="order_index" value="<?= $index ?>">
                            <button type="submit" name="accept_order" class="accept-btn">Accept Order</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No orders are available for delivery.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('submit', '.accept-order-form', function(e) {
        e.preventDefault(); // Prevent the form from submitting normally

        var form = $(this);
        var orderIndex = form.data('order-index'); // Get the order index

        $.ajax({
            url: 'deliveryrider.php', // Submit the form to deliveryrider.php
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var result = JSON.parse(response);
                if (result.status === 'success') {
                    // Remove the order item from the list visually
                    form.closest('.order-item').remove();
                    alert('Order is now being delivered!');
                } else {
                    alert('Failed to accept the order. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred while processing the request.');
            }
        });
    });
</script>
</body>
</html>
