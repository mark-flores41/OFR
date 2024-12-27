-- phpMyAdmin SQL Dump
-- version 5.2.1
-- Host: 127.0.0.1
-- Generation Time: [Current Date]
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Ensure you are in the correct database
CREATE DATABASE IF NOT EXISTS Online_reservation;
USE Online_reservation;

-- Create menu table first, adding price and food_image columns
CREATE TABLE IF NOT EXISTS `menu` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,               -- Added price column
    `food_image` VARCHAR(255) NOT NULL              -- Added food_image column
);

-- Create food_items table for food details and image path
CREATE TABLE IF NOT EXISTS `food_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `food_name` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `image_path` VARCHAR(255) NOT NULL              -- Path to the food image
);

-- Create users table with new columns for municipality, barangay, sitioorzone, and contact_number
CREATE TABLE IF NOT EXISTS `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `email` varchar(100) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `contact_number` varchar(15),
    `role` enum('Admin','Delivery Rider','Customer') DEFAULT 'Customer',
    `municipality` varchar(255),
    `barangay` varchar(255),
    `sitioorzone` varchar(255),
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create orders table with updated structure (using user_id instead of user_email)
CREATE TABLE IF NOT EXISTS `orders` (
    `order_id` INT(11) NOT NULL AUTO_INCREMENT,
    `product_id` INT(11) NOT NULL,           -- Product or food item ID
    `quantity` INT(11) NOT NULL,             -- Quantity of the item
    `price` DECIMAL(10, 2) NOT NULL,         -- Price per item
    `total` DECIMAL(10, 2) NOT NULL,         -- Total price for this order (quantity * price)
    `user_id` INT(11) NOT NULL,              -- User ID (foreign key linking to users table)
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `status` ENUM('pending', 'delivering', 'completed', 'cancelled') DEFAULT 'pending',
    `emailofdeliveryrider` VARCHAR(100) NULL,  -- New column to store delivery rider's email
    `delivery_rider_id` INT(11) NULL,         -- New column to link orders to a delivery rider
    `food_item` VARCHAR(255),
    PRIMARY KEY (`order_id`),
    CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_delivery_rider` FOREIGN KEY (`delivery_rider_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create reservations table
CREATE TABLE IF NOT EXISTS `reservations` (
    `reservation_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `reservation_date` timestamp NOT NULL DEFAULT current_timestamp(),
    `status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
    PRIMARY KEY (`reservation_id`),
    CONSTRAINT `fk_reservation_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create reservation_items table
CREATE TABLE IF NOT EXISTS `reservation_items` (
    `reservation_item_id` int(11) NOT NULL AUTO_INCREMENT,
    `reservation_id` int(11) NOT NULL,
    `item_name` varchar(255) NOT NULL,
    `quantity` int(11) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    PRIMARY KEY (`reservation_item_id`),
    CONSTRAINT `fk_reservation_item` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create carts table
CREATE TABLE IF NOT EXISTS `carts` (
    `cart_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `item_name` varchar(255) NOT NULL,
    `quantity` int(11) NOT NULL,
    PRIMARY KEY (`cart_id`),
    CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some test data into users table
INSERT INTO `users` (`email`, `password`, `created_at`, `contact_number`, `role`, `municipality`, `barangay`, `sitioorzone`) VALUES
('admin@example.com', '$2y$10$ZjZ0ZTQtM2FiMGRlZDlmZTQ5NTZkMzZlZWFiOTk1OTNjNTQyZDExZ', '2024-11-21 00:00:00', '09071234567', 'Admin', '', '', ''),
('customer1@example.com', '$2y$10$YjY4ZGVmMmZkZTJlMTRlZTdlY2M0ZGVjNjg4Nzk1MmNhMTgxNWRjZ', '2024-11-20 14:32:15', '09161234567', 'Customer', '', '', ''),
('deliveryrider@example.com', '$2y$10$YjY4ZGVmMmZkZTJlMTRlZTdlY2M0ZGVjNjg4Nzk1MmNhMTgxNWRjZ', '2024-11-22 10:45:12', '09231234567', 'Delivery Rider', '', '', '');

-- Insert some test data into reservations table
INSERT INTO `reservations` (`user_id`, `reservation_date`, `status`) VALUES
(2, '2024-11-23 12:00:00', 'Pending'),
(3, '2024-11-24 15:30:00', 'Confirmed');

-- Insert some test data into reservation_items table
INSERT INTO `reservation_items` (`reservation_id`, `item_name`, `quantity`, `price`) VALUES
(1, 'Burger', 2, 150.00),
(1, 'Fries', 1, 80.00),
(2, 'Pizza', 1, 500.00);

-- Insert some test data into carts table
INSERT INTO `carts` (`user_id`, `item_name`, `quantity`) VALUES
(2, 'Burger', 2),
(3, 'Fries', 1);

-- Insert test data into orders table (updated to use user_id instead of user_email)
INSERT INTO `orders` (`product_id`, `quantity`, `price`, `total`, `user_id`, `status`, `emailofdeliveryrider`, `delivery_rider_id`) VALUES
(1, 2, 150.00, 300.00, 2, 'pending', NULL, NULL),
(2, 1, 500.00, 500.00, 3, 'pending', 'deliveryrider@example.com', 3);

-- Insert test data into menu table (new data)
INSERT INTO `menu` (`name`, `price`, `food_image`) VALUES
('Burger', 150.00, 'burger.jpg'),
('Fries', 80.00, 'fries.jpg'),
('Pizza', 500.00, 'pizza.jpg');

-- Insert test data into food_items table (new data)
INSERT INTO `food_items` (`food_name`, `description`, `price`, `image_path`) VALUES
('Burger', 'A delicious beef burger', 150.00, 'uploads/food_images/burger.jpg'),
('Fries', 'Crispy golden fries', 80.00, 'uploads/food_images/fries.jpg'),
('Pizza', 'Cheesy and flavorful pizza', 500.00, 'uploads/food_images/pizza.jpg');

-- Auto increment values
ALTER TABLE `users`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reservations`
    MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reservation_items`
    MODIFY `reservation_item_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `carts`
    MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
    MODIFY `order_id` INT(11) NOT NULL AUTO_INCREMENT;

-- Commit the changes
COMMIT;
