<?php
session_start();
include('database.php'); // Include the database connection

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize orders array if not set
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [];
}

// Define the food images
$food_images = [
    'Burger' => 'image/burger.png',
    'Crispy Fries' => 'image/fries.jpg',
    'Salad' => 'image/salad.jpg',
    'Hot Pizza' => 'image/pizza.jpg',
    'Shake' => 'image/shake.jpg',
    'Spaghetti' => 'image/spaghetti.jpg',
    'Steak' => 'image/steak.jpg',
    'Drinks' => 'image/drinks.jpg',
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $food_item = $_POST['food_item']; // Get food item from the form
    $food_image = isset($food_images[$food_item]) ? $food_images[$food_item] : 'default.jpg'; // Get food image from the map

    // Generate a unique order ID
    $order_id = uniqid();

    // Get user_id from session
    $user_id = $_SESSION['user_id'];
    $status = 'Waiting for orders'; // Default order status

    // Check if user_id exists in the users table
    $stmt_check_user = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt_check_user->bind_param("i", $user_id);
    $stmt_check_user->execute();
    $stmt_check_user->store_result();

    if ($stmt_check_user->num_rows > 0) {
        // User exists, proceed with adding to the orders table
        // SQL Query to insert order into database
        $stmt = $conn->prepare("INSERT INTO orders (user_id, food_item, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $food_item, $status); // Prepare and bind query parameters

        if ($stmt->execute()) {
            // Successfully added to database
            // Store the order in the session with the food name, image, and a unique ID
            $_SESSION['orders'][] = [
                'order_id' => $order_id,
                'food_item' => $food_item,
                'status' => $status,
                'food_image' => $food_image
            ];

            // No redirect, just stay on the homepage
            echo "<script>alert('Food item added to your order successfully!');</script>";
        } else {
            echo "<script>alert('Failed to add order to the database.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('User not found. Please login again.');</script>";
    }

    $stmt_check_user->close();
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
          height: 100vh;
          position: relative;
          overflow: hidden; /* Prevent body overflow */
      }

      .container {
          background-color: white;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          width: 80%;
          max-width: 1200px;
          overflow: hidden; /* Prevent overflowing content */
      }

      h1, h2 {
          text-align: center;
          color: #4a90e2;
      }

      /* Center the menu and add scroll */
      .food-container {
          max-height: 70vh; /* Restrict height of food menu */
          overflow-y: auto; /* Allow vertical scrolling */
          padding-right: 10px; /* To prevent horizontal scrolling */
      }

      .food-options {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Automatically adjust to the screen size */
          gap: 20px; /* Space between items */
          margin-top: 30px;
          justify-items: center; /* Centers the food items */
      }

      .food-item {
          background-color: #ffffff;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          text-align: center;
          cursor: pointer;
          transition: transform 0.3s ease;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: space-between;
          height: 100%; /* Ensures consistent height */
      }

      .food-item:hover {
          transform: scale(1.05);
      }

      .food-item img {
          width: 100%;
          height: 180px; /* Fixed height to ensure images are uniform */
          object-fit: cover; /* Ensures the image fills the area without distortion */
          border-radius: 8px;
      }

      .food-item h3 {
          color: #333333;
          font-size: 18px;
          margin-top: 15px;
      }

      .food-item p {
          color: #777777;
          font-size: 14px;
          margin-top: 5px;
      }

      .food-item .price {
          color: #4a90e2;
          font-size: 18px;
          font-weight: bold;
          margin-top: 5px;
      }

      .food-item button {
          background-color: #4a90e2;
          color: white;
          border: none;
          padding: 10px;
          border-radius: 4px;
          cursor: pointer;
          width: 100%;
          margin-top: 10px;
          transition: background-color 0.3s ease;
      }

      .food-item button:hover {
          background-color: #357ab7;
      }

      /* Buttons at top-right */
      .buttons-container {
          position: absolute;
          top: 20px;
          right: 20px;
          display: flex;
          gap: 10px; /* Space between buttons */
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

      /* Greeting styles */
      .greeting-container {
          position: absolute;
          top: 20px;
          left: 20px;
          display: flex;
          align-items: center;
          gap: 10px;
      }

      .greeting-container img {
          width: 50px;  /* Fixed width */
          height: 50px; /* Fixed height */
          border-radius: 50%;  /* Makes it round */
          cursor: pointer;
      }

      .greeting-container p {
          margin: 0;
          font-size: 16px;
          color: #333;
      }
  </style>
</head>
<body>
  <div class="container">
      <!-- Greeting and username -->
      <div class="greeting-container">
          <img src="image/eyy.jpg" alt="Customer Image" id="customerImage">
          <!-- Display username if it's set in the session -->
          <p>Hello, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest' ?>!</p>
      </div>

      <!-- Buttons Container at top-right (aligned horizontally) -->
      <div class="buttons-container">
          <!-- MyOrder Button (First) -->
          <a href="myorders.php">My Orders</a>

          <!-- Logout Button (Second) -->
          <a href="logout.php">Logout</a>
      </div>

      <h1>Welcome to Baladog Online Food Reservation</h1>
    
      <!-- Scrollable Food Items Container -->
      <div class="food-container">
          <!-- Food Items -->
          <div class="food-options">
              <!-- Example food item -->
              <div class="food-item">
                  <img src="image/burger.png" alt="Burger">
                  <h3>Burger</h3>
                  <p>Cheesy, juicy, and packed with flavor. A classic favorite!</p>
                  <div class="price">₱150.00</div>
                  <form action="homepage.php" method="POST">
                      <input type="hidden" name="food_item" value="Burger">
                      <button type="submit" name="add_to_cart">Add to Cart</button>
                  </form>
              </div>
               <!-- Fries -->
               <div class="food-item">
                  <img src="image/fries.jpg" alt="Fries">
                  <h3>Crispy Fries</h3>
                  <p>Golden, crispy fries that go perfectly with any meal!</p>
                  <div class="price">₱100.00</div>
                  <form action="homepage.php" method="POST">
                      <input type="hidden" name="food_item" value="Crispy Fries">
                      <button type="submit" name="add_to_cart">Add to Cart</button>
                  </form>
              </div>
                <!-- Salad-->
              <div class="food-item">
                  <img src="image/salad.jpg" alt="Salad">
                  <h3>Salad</h3>
                  <p>Delicious and healthy!</p>
                  <div class="price">₱105.00</div>
                  <form action="homepage.php" method="POST">
                      <input type="hidden" name="food_item" value="Salad">
                      <button type="submit" name="add_to_cart">Add to Cart</button>
                  </form>
              </div>
                <!-- Pizza -->
              <div class="food-item">
                  <img src="image/pizza.jpg" alt="Pizza">
                  <h3>Hot Pizza</h3>
                  <p>Freshly baked pizza with a variety of toppings to choose from!</p>
                  <div class="price">₱120.00</div>
                  <form action="homepage.php" method="POST">
                      <input type="hidden" name="food_item" value="Hot Pizza">
                      <button type="submit" name="add_to_cart">Add to Cart</button>
                  </form>
              </div>
                 <!-- Shake -->
               <div class="food-item">
                  <img src="image/shake.jpg" alt="Shake">
                  <h3>Shake</h3>
                  <p>Sweet and cold!</p>
                  <div class="price">₱50.00</div>
                  <form action="homepage.php" method="POST">
                      <input type="hidden" name="food_item" value="Shake">
                      <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
              </div>
                 <!-- Spaghetti -->
               <div class="food-item">
                  <img src="image/spaghetti.jpg" alt="Spaghetti">
                  <h3>Spaghetti</h3>
                  <p>Delicious and sweet!</p>
                  <div class="price">₱60.00</div>
                  <form action="homepage.php" method="POST">
                      <input type="hidden" name="food_item" value="Spaghetti">
                      <button type="submit" name="add_to_cart">Add to Cart</button>
                  </form>
              </div>
                  <!-- Steak -->
               <div class="food-item">
                  <img src="image/steak.jpg" alt="Steak">
                  <h3>Steak</h3>
                  <p>Savory and rich taste!</p>
                  <div class="price">₱500.00</div>
                  <form action="homepage.php" method="POST">
                      <input type="hidden" name="food_item" value="Steak">
                      <button type="submit" name="add_to_cart">Add to Cart</button>
                  </form>
            </div>
                <!-- Drinks -->
              <div class="food-item">
                  <img src="image/drinks.jpg" alt="Drinks">
                  <h3>Drinks</h3>
                  <p>Refreshing beverages!</p>
                  <div class="price">₱45.00</div>
                  <form action="homepage.php" method="POST">
                      <input type="hidden" name="food_item" value="Drinks">
                      <button type="submit" name="add_to_cart">Add to Cart</button>
                  </form>
             </div>
         </div>
      </div>
  </div>

</body>
</html>   
