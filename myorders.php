<?php
session_start();
include('database.php'); // Include the database connection

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get orders from session
$orders = isset($_SESSION['orders']) ? $_SESSION['orders'] : [];

// Handle removing an order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_order'])) {
    if (empty($_SESSION['orders'])) {
        echo json_encode(['success' => false, 'error' => 'No orders available in the session.']);
        exit();
    }

    $order_id = $_POST['order_id'] ?? null; // Get the order_id from the request

    if (!$order_id) {
        echo json_encode(['success' => false, 'error' => 'Invalid order ID.']);
        exit();
    }

    // Debugging: Log the order_id received
    error_log('Order ID: ' . $order_id); // This will log the order_id in the error log

    $order_found = false;

    // Remove the order from session
    foreach ($_SESSION['orders'] as $key => $order) {
        if ($order['order_id'] === $order_id) {
            unset($_SESSION['orders'][$key]); // Remove the order from session
            $_SESSION['orders'] = array_values($_SESSION['orders']); // Reindex array
            $order_found = true;
            break;
        }
    }

    // Respond with the updated session orders
    if ($order_found) {
        echo json_encode(['success' => true, 'orders' => $_SESSION['orders']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Order not found in session.']);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders</title>
  <style>
      body {
          font-family: Arial, sans-serif;
          background-color: #f3f4f6;
          padding: 20px;
      }

      .order-container {
          background-color: white;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          width: 80%;
          max-width: 800px;
          margin: auto;
      }

      h1 {
          text-align: center;
          color: #4a90e2;
      }

      .order-item {
          background-color: #ffffff;
          padding: 15px;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          margin-bottom: 15px;
          display: flex;
          justify-content: space-between;
          align-items: center;
      }

      .order-item img {
          width: 100px;
          height: auto;
          border-radius: 8px;
          margin-right: 15px;
      }

      .remove-btn {
          background-color: #f44336;
          color: white;
          border: none;
          padding: 10px 15px;
          border-radius: 8px;
          cursor: pointer;
          font-size: 14px;
          transition: background-color 0.3s ease, transform 0.3s ease;
      }

      .remove-btn:hover {
          background-color: #d32f2f;
          transform: scale(1.05);
      }

      .remove-btn:active {
          transform: scale(0.95);
      }

      .buttons-container {
          margin-top: 20px;
          text-align: center;
      }

      .buttons-container a {
          background-color: #4a90e2;
          color: white;
          text-decoration: none;
          padding: 10px 20px;
          border-radius: 8px;
          transition: background-color 0.3s ease;
      }

      .buttons-container a:hover {
          background-color: #357ab7;
      }
  </style>
</head>
<body>
<div class="order-container">
    <h1>Your Orders</h1>

    <?php if (empty($orders)): ?>
        <p>No orders placed yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-item" id="order-<?= htmlspecialchars($order['order_id'] ?? '') ?>">
                <img src="<?= htmlspecialchars($order['food_image'] ?? 'default-image.jpg') ?>" 
                     alt="<?= htmlspecialchars($order['food_item'] ?? 'No Image Available') ?>">
                <div>
                    <p>Food Item: <?= htmlspecialchars($order['food_item'] ?? 'Unknown Item') ?></p>
                    <p>Status: <?= htmlspecialchars($order['status'] ?? 'Pending') ?></p>
                </div>
                <button type="button" class="remove-btn" 
                        data-order-id="<?= htmlspecialchars($order['order_id'] ?? '') ?>">Remove Order</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="buttons-container">
        <a href="homepage.php">Back to Menu</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.getAttribute('data-order-id'); // Get the order ID from the button's data attribute
            const orderItem = document.getElementById(`order-${orderId}`); // Get the DOM element for this order

            // Check if orderId is valid
            if (!orderId) {
                alert('Invalid Order ID');
                return;
            }

            fetch('myorders.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    remove_order: true, // Indicate that this is a removal request
                    order_id: orderId  // Pass the order ID to be removed
                })
            })
            .then(response => response.json()) // Parse the response as JSON
            .then(data => {
                if (data.success) {
                    // Remove the order from the DOM immediately
                    orderItem.remove();  // This will remove the order from the page
                } else {
                    alert(data.error || 'Failed to remove order.'); // Show an error if removal fails
                }
            })
            .catch(error => {
                console.error('Error:', error); // Log any errors to the console
                alert('There was an error removing the order.');
            });
        });
    });
});
</script>  
</body>
</html>
