-- GreenGuru Database Initialization Script

CREATE DATABASE IF NOT EXISTS `project`;
USE `project`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    UNIQUE KEY `unique_username` (`username`),
    UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE IF NOT EXISTS `products` (
    `product_index_no` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `cost_price` DECIMAL(10,2) DEFAULT 0.00,
    `rating` DECIMAL(3,2) DEFAULT 0.00,
    `sustainability_score` INT DEFAULT 0,
    `image` VARCHAR(255) DEFAULT NULL,
    `DESCRIPTION` TEXT DEFAULT NULL,
    `stock` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart table
CREATE TABLE IF NOT EXISTS `cart` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_index_no` INT NOT NULL,
    `quantity` INT DEFAULT 1,
    UNIQUE KEY `unique_cart_item` (`user_id`, `product_index_no`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_index_no`) REFERENCES `products`(`product_index_no`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE IF NOT EXISTS `orders` (
    `order_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `address` TEXT NOT NULL,
    `city` VARCHAR(255) DEFAULT NULL,
    `country` VARCHAR(255) DEFAULT NULL,
    `postal_code` VARCHAR(20) DEFAULT NULL,
    `subtotal` DECIMAL(10,2) DEFAULT 0.00,
    `tax` DECIMAL(10,2) DEFAULT 0.00,
    `total` DECIMAL(10,2) DEFAULT 0.00,
    `payment_id` VARCHAR(255) DEFAULT NULL,
    `status` VARCHAR(50) DEFAULT 'Processing',
    `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items table
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_index_no` INT NOT NULL,
    `quantity` INT DEFAULT 1,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_index_no`) REFERENCES `products`(`product_index_no`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Billing info table (used by greenguru-billing service)
CREATE TABLE IF NOT EXISTS `billing_info` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `address` TEXT NOT NULL,
    `city` VARCHAR(255) DEFAULT NULL,
    `country` VARCHAR(255) DEFAULT NULL,
    `postal_code` VARCHAR(20) DEFAULT NULL,
    `card_number` VARCHAR(20) DEFAULT NULL,
    `expiry_date` VARCHAR(10) DEFAULT NULL,
    `cvv` VARCHAR(5) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default admin user (id=0 is used for admin detection in login.php)
-- Password: guruadmin123 (hashed with password_hash)
INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(0, 'guru_admin', 'admin@greenguru.com', '$2y$10$ZQGF5yEd5rOALb3lELZRHS06UCDGdyQShULxp4q/xL')
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`), `password` = VALUES(`password`);

-- Insert some sample products
INSERT INTO `products` (`name`, `price`, `cost_price`, `rating`, `sustainability_score`, `image`, `DESCRIPTION`, `stock`) VALUES
('Bamboo Toothbrush', 5.99, 2.50, 4.5, 9, 'products/product1.jpg', 'Made of Neem Tree, biodegradable and eco-friendly alternative to plastic toothbrushes.', 100),
('Family Toothbrush Set', 15.99, 7.00, 4.8, 8, 'products/product2.jpg', 'Made of three different sustainable materials, perfect for the whole family.', 50),
('Eco Water Bottle', 12.99, 5.00, 4.3, 9, 'products/product3.jpg', 'Reusable stainless steel water bottle, keeps drinks cold for 24 hours.', 75),
('Eco Bag', 9.99, 4.00, 4.6, 10, 'products/product4.jpg', 'Reusable eco-friendly bag, reduces plastic waste.', 200)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `products` (`name`, `price`, `cost_price`, `rating`, `sustainability_score`, `image`, `DESCRIPTION`, `stock`) VALUES
('Reusable Bamboo Straws', 8.99, 2.50, 4.7, 9, 'products/product5.jpg', 'Set of 6 natural bamboo straws with a cleaning brush.', 150),
('Natural Loofah Sponges', 5.99, 1.50, 4.4, 10, 'products/product6.jpg', '100% biodegradable loofah sponges for kitchen and bath.', 200),
('Beeswax Food Wraps', 14.99, 6.00, 4.8, 9, 'products/product7.jpg', 'Reusable alternative to plastic wrap. Comes in assorted sizes.', 120),
('Silicone Food Storage Bags', 19.99, 8.00, 4.6, 8, 'products/product8.jpg', 'Set of 4 leakproof, reusable silicone bags for snacks and meals.', 100),
('Biodegradable Trash Bags', 12.99, 5.00, 4.3, 10, 'products/product9.jpg', 'Compostable and tough trash bags made from plant starch.', 300),
('Stainless Steel Lunch Box', 24.99, 12.00, 4.8, 9, 'products/product10.jpg', 'Durable, plastic-free bento box for sustainable lunches.', 80),
('Bamboo Cotton Swabs', 6.99, 2.00, 4.5, 9, 'products/product11.jpg', 'Pack of 200 100% biodegradable bamboo and organic cotton swabs.', 250),
('Reusable Makeup Remover Pads', 11.99, 4.50, 4.7, 9, 'products/product12.jpg', '16 bamboo cotton rounds with a washable laundry bag.', 140),
('Eco Laundry Detergent Strips', 16.99, 7.00, 4.6, 9, 'products/product13.jpg', 'Zero-waste, ultra-concentrated laundry detergent sheets.', 110),
('Solid Shampoo Bar', 9.99, 3.50, 4.4, 10, 'products/product14.jpg', 'Plastic-free, natural shampoo bar for all hair types.', 160),
('Biodegradable Dental Floss', 7.99, 2.50, 4.2, 9, 'products/product15.jpg', 'Silk dental floss in a refillable glass container.', 200),
('Bamboo Cutlery Set', 13.99, 5.50, 4.8, 9, 'products/product16.jpg', 'Portable bamboo fork, knife, spoon, and straw in a travel pouch.', 90),
('Wooden Hair Brush', 15.99, 6.50, 4.6, 8, 'products/product17.jpg', 'Natural bamboo hair brush with wooden bristles.', 75),
('Natural Deodorant Stone', 10.99, 4.00, 4.3, 10, 'products/product18.jpg', 'Crystal deodorant body stick lasting up to a year.', 130),
('Cork Yoga Mat', 39.99, 18.00, 4.9, 9, 'products/product19.jpg', 'Non-slip, eco-friendly yoga mat made from natural cork and rubber.', 40),
('Vegan Leather Wallet', 29.99, 14.00, 4.5, 8, 'products/product20.jpg', 'Cruelty-free, sustainable faux-leather wallet.', 60),
('Organic Cotton Towels', 34.99, 16.00, 4.7, 9, 'products/product1.jpg', 'Set of 2 soft, highly absorbent 100% organic cotton bath towels.', 50),
('Plant-based Dish Sponges', 8.99, 3.00, 4.4, 9, 'products/product2.jpg', 'Pack of 6 eco-friendly sponges made from natural cellulose.', 180),
('Stainless Steel Razors', 22.99, 10.00, 4.8, 9, 'products/product3.jpg', 'Double-edge safety razor for a plastic-free shave.', 85),
('Reusable Tea Strainer', 6.99, 2.00, 4.6, 8, 'products/product4.jpg', 'Stainless steel loose leaf tea infuser.', 200),
('Recycled Paper Notebook', 12.99, 5.00, 4.5, 10, 'products/product5.jpg', 'Journal made 100% from post-consumer recycled paper.', 120),
('Plantable Pencils', 9.99, 3.50, 4.7, 10, 'products/product6.jpg', 'Set of 8 wood pencils that can be planted to grow herbs.', 150),
('Solar Powered Power Bank', 49.99, 25.00, 4.4, 9, 'products/product7.jpg', 'Portable charger with built-in solar panels.', 30),
('Upcycled Tote Bag', 18.99, 8.00, 4.6, 9, 'products/product8.jpg', 'Durable shopping bag made from repurposed fabrics.', 90),
('Bamboo Coffee Cup', 15.99, 6.00, 4.7, 9, 'products/product9.jpg', 'Reusable travel mug made from organic bamboo fibers.', 110),
('Biodegradable Pet Waste Bags', 14.99, 5.50, 4.5, 10, 'products/product10.jpg', '120 thick, compostable dog poop bags.', 200),
('Eco-friendly Dish Soap Block', 11.99, 4.50, 4.6, 9, 'products/product11.jpg', 'Plastic-free solid dish washing soap.', 140),
('Compostable Phone Case', 24.99, 10.00, 4.7, 9, 'products/product12.jpg', 'Plant-based phone case that protects your phone and the earth.', 70),
('Hemp Shower Curtain', 35.99, 16.00, 4.8, 9, 'products/product13.jpg', 'Natural, mold-resistant hemp fabric shower curtain.', 45),
('Reusable Produce Bags', 13.99, 5.50, 4.6, 9, 'products/product14.jpg', 'Set of 9 mesh cotton bags for grocery shopping.', 130),
('Organic Hemp Backpack', 59.99, 28.00, 4.9, 9, 'products/product15.jpg', 'Spacious, durable backpack made from 100% organic hemp.', 25),
('Bamboo Toilet Paper', 29.99, 14.00, 4.5, 9, 'products/product16.jpg', '24 rolls of soft, tree-free bamboo toilet tissue.', 80),
('Natural Coir Doormat', 21.99, 10.00, 4.7, 9, 'products/product17.jpg', 'Durable welcome mat made from coconut fibers.', 60),
('Biodegradable Party Plates', 19.99, 8.50, 4.4, 10, 'products/product18.jpg', '50-pack of strong, fallen palm leaf compostable plates.', 95),
('Eco-Friendly Yoga Block', 16.99, 7.00, 4.8, 9, 'products/product19.jpg', 'Supportive yoga block made entirely from renewable cork.', 100),
('Stainless Steel Ice Cubes', 14.99, 6.00, 4.5, 8, 'products/product20.jpg', 'Set of 8 reusable metal ice cubes for cooling drinks without diluting.', 110),
('Bamboo Charcoal Bags', 18.99, 7.50, 4.6, 9, 'products/product1.jpg', 'Natural air purifying bags that eliminate odors and moisture.', 120),
('Sustainable Wooden Toys', 34.99, 15.00, 4.9, 9, 'products/product2.jpg', 'Non-toxic, plastic-free wooden building blocks for children.', 40);
