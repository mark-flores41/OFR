<?php
session_start();
include('database.php');

// Check if the user is logged in and if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $food_name = $_POST['food_name'];
    $price = $_POST['price'];
    $image = $_FILES['image'];

    // Validate and process image upload
    $target_dir = "image/";  // Directory to save the image
    $target_file = $target_dir . basename($image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    if (getimagesize($image["tmp_name"]) === false) {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (max 5MB)
    if ($image["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if everything is okay
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Corrected to use 'tmp_name' instead of 'image'
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Insert product data into the database
            $stmt = $conn->prepare("INSERT INTO products (food_name, price, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $food_name, $price, $target_file);

            if ($stmt->execute()) {
                // Product added successfully, redirect to homepage with success message
                header("Location: homepage.php?product_added=true");
                exit();
            } else {
                echo "Error adding product: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
