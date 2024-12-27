<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $showModal = true; // Trigger modal if not logged in
    } else {
        // Add item to cart if logged in
        $food_item = $_POST['food_item'];
        if (!isset($_SESSION['orders'])) $_SESSION['orders'] = [];
        $_SESSION['orders'][] = ['item' => $food_item, 'status' => 'Waiting for orders'];
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Online Food Reservation System</title>
  <style>
      /* Common Styles */
      body {
          font-family: Arial, sans-serif;
          margin: 0;
          padding: 0;
          background-color: #f3f4f6;
          display: flex;
          justify-content: center;
          align-items: flex-start;
          min-height: 100vh;
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
      }

      .food-container {
          max-height: 70vh;
          overflow-y: auto;
      }

      .food-options {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
          gap: 20px;
          margin-top: 30px;
      }

      .food-item {
          background-color: #ffffff;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          text-align: center;
          transition: transform 0.3s ease;
          display: flex;
          flex-direction: column;
          align-items: center;
      }

      .food-item:hover {
          transform: scale(1.05);
      }

      .food-item img {
          width: 100%;
          height: 180px;
          object-fit: cover;
          border-radius: 8px;
      }

      .food-item h3 {
          color: #333333;
          font-size: 18px;
          margin-top: 15px;
      }

      .food-item p {
          color: #666666;
          font-size: 14px;
          margin: 10px 0;
      }

      .food-item .price {
          color: #4a90e2;
          font-size: 18px;
          font-weight: bold;
      }

      .food-item button {
          background-color: #4a90e2;
          color: white;
          border: none;
          padding: 10px;
          border-radius: 4px;
          cursor: pointer;
          margin-top: 10px;
      }

      .food-item button:hover {
          background-color: #357ab7;
      }

      /* Modal Styles */
      .modal {
          display: none; /* Hidden by default */
          position: fixed;
          z-index: 1000;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5); /* Transparent black background */
          justify-content: center;
          align-items: center;
      }

      .modal-content {
          background-color: white;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          text-align: center;
          width: 400px;
          max-width: 90%;
      }

      .modal-content h2 {
          color: #4a90e2;
      }

      .modal-content button {
          background-color: #4a90e2;
          color: white;
          border: none;
          padding: 10px 20px;
          border-radius: 4px;
          cursor: pointer;
          margin-top: 10px;
      }

      .modal-content button:hover {
          background-color: #357ab7;
      }

      .modal-content a {
          text-decoration: none;
          color: white;
      }

      /* Buttons Container at Top-Right */
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
  </style>
</head>
<body>
  <div class="buttons-container">
      <!-- Login and SignUp Buttons -->
      <a href="login.php">Login</a>
      <a href="registerform.php">Sign Up</a>
  </div>

  <div class="container">
      <h1>Welcome to kupals Online Food Reservation</h1>
      <div class="food-container">
          <div class="food-options">
              <!-- Food Items -->
              <?php
              $foods = [
                  ["name" => "Burger", "image" => "image/burger.png", "price" => "₱150.00", "desc" => "Juicy and delicious burger with fresh ingredients."],
                  ["name" => "Crispy Fries", "image" => "image/fries.jpg", "price" => "₱100.00", "desc" => "Golden, crispy fries that go perfectly with any meal!"],
                  ["name" => "Salad", "image" => "image/salad.jpg", "price" => "₱105.00", "desc" => "Healthy and fresh salad to keep you energized."],
                  ["name" => "Hot Pizza", "image" => "image/pizza.jpg", "price" => "₱120.00", "desc" => "Freshly baked pizza with a variety of toppings to choose from!"],
                  ["name" => "Shake", "image" => "image/shake.jpg", "price" => "₱50.00", "desc" => "Sweet and cold shake to refresh your day!"],
                  ["name" => "Spaghetti", "image" => "image/spaghetti.jpg", "price" => "₱60.00", "desc" => "Delicious and sweet spaghetti perfect for any occasion!"],
                  ["name" => "Steak", "image" => "image/steak.jpg", "price" => "₱500.00", "desc" => "Savory and rich steak cooked to perfection."],
                  ["name" => "Drinks", "image" => "image/drinks.jpg", "price" => "₱45.00", "desc" => "Refreshing beverages to complement your meal."]
              ];

              foreach ($foods as $food) {
                  echo "
                  <div class='food-item'>
                      <img src='{$food['image']}' alt='{$food['name']}'>
                      <h3>{$food['name']}</h3>
                      <p>{$food['desc']}</p>
                      <div class='price'>{$food['price']}</div>
                      <form method='POST'>
                          <input type='hidden' name='food_item' value='{$food['name']}'>
                          <button type='submit' name='add_to_cart'>Add to Cart</button>
                      </form>
                  </div>";
              }
              ?>
          </div>
      </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="loginModal">
      <div class="modal-content">
          <h2>Login Required</h2>
          <p>You need to log in or sign up to add items to your cart.</p>
          <button onclick="window.location.href='login.php'">Login</button>
          <button onclick="window.location.href='registerform.php'">Sign Up</button>
          <button onclick="closeModal()">Cancel</button>
      </div>
  </div>

  <script>
      // Close the modal
      function closeModal() {
          document.getElementById('loginModal').style.display = 'none';
      }

      // Open modal if the PHP script sets it
      <?php if (!empty($showModal)): ?>
      document.getElementById('loginModal').style.display = 'flex';
      <?php endif; ?>
  </script>
</body>
</html>
