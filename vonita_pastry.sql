-- =====================================================
-- Vonita Pastry database
-- Import this file in phpMyAdmin: http://localhost/phpmyadmin > Import
-- Creates the database, all tables, the products, and an admin account.
-- Safe to import again: existing tables and rows are kept.
-- =====================================================

CREATE DATABASE IF NOT EXISTS vonita_pastry;
USE vonita_pastry;

-- Customers and admins
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'customer',
    contact_number VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL
);

-- Items sold in the shop
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL
);

-- One row per order (status: Pending, Completed, Cancelled)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- The products inside each order
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Default admin account
-- Email: admin@vonitapastry.com
-- Password: admin12345
INSERT IGNORE INTO users (first_name, last_name, email, password, role, contact_number, address)
VALUES ('Admin', 'Vonita', 'admin@vonitapastry.com',
        '$2y$12$hEcjmooJhYzb045exycNVOHrRKNBNlJVjce8iKquzEK2IVF66oB9u',
        'admin', '09123456789', '123 Main Street, Puerto Princesa City, Palawan');

-- Products (skipped if a product with the same name already exists)
INSERT INTO products (name, price, category, description, image) SELECT 'Classic Vanilla Bean Cake', 850.00, 'cakes', 'Experience the soft, fluffy texture of our Classic Vanilla Bean Cake, layered with rich vanilla buttercream for a timeless treat.', 'images/classic-vanilla-bean-cake.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Classic Vanilla Bean Cake');
INSERT INTO products (name, price, category, description, image) SELECT 'Rich Chocolate Fudge Cake', 950.00, 'cakes', 'Savor the rich, moist layers of our Chocolate Fudge Cake, filled with luscious chocolate fudge for a decadent treat.', 'images/rich-chocolate-fudge-cake.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Rich Chocolate Fudge Cake');
INSERT INTO products (name, price, category, description, image) SELECT 'Red Velvet Delight', 900.00, 'cakes', 'Experience the rich, moist texture of our Red Velvet Delight, layered with cream cheese frosting for a truly indulgent treat.', 'images/red-velvet-delight.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Red Velvet Delight');
INSERT INTO products (name, price, category, description, image) SELECT 'Ube Cheesecake Fusion', 1000.00, 'cakes', 'A delightful fusion of the beloved Filipino ube (purple yam) with a creamy cheesecake base, creating a unique and flavorful dessert.', 'images/ube-cheesecake-fusion.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Ube Cheesecake Fusion');
INSERT INTO products (name, price, category, description, image) SELECT 'Fruit Medley Cake', 950.00, 'cakes', 'A refreshing take on the classic cake, featuring a light sponge cake topped with a colorful assortment of fresh, seasonal fruits.', 'images/fruit-medley-cake.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Fruit Medley Cake');
INSERT INTO products (name, price, category, description, image) SELECT 'Lemon Blueberry Cake', 900.00, 'cakes', 'Enjoy the refreshing combination of zesty lemon and juicy blueberries, layered with smooth and creamy frosting.', 'images/lemon-blueberry-layer-cake.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Lemon Blueberry Cake');
INSERT INTO products (name, price, category, description, image) SELECT 'Carrot Walnut Cake', 900.00, 'cakes', 'Savor our moist Carrot Walnut Cake, warmly spiced and layered with smooth cream cheese frosting and crunchy walnuts.', 'images/rustic-carrot-cake-with-walnuts.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Carrot Walnut Cake');
INSERT INTO products (name, price, category, description, image) SELECT 'Strawberry Shortcake', 950.00, 'cakes', 'Delight in our light Strawberry Shortcake, layered with fluffy cream and juicy fresh strawberries for a refreshing treat.', 'images/strawberry-shortcake-with-cream-layers.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Strawberry Shortcake');
INSERT INTO products (name, price, category, description, image) SELECT 'Chocolate Strawberry Cake', 1000.00, 'cakes', 'Indulge in rich chocolate cake layered with creamy frosting and sweet strawberries for a luxurious flavor combination.', 'images/decadent-chocolate-strawberry-cake.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chocolate Strawberry Cake');
INSERT INTO products (name, price, category, description, image) SELECT 'Mango Cream Cake', 950.00, 'cakes', 'Enjoy the tropical sweetness of our Mango Cream Cake, featuring fluffy sponge, smooth cream, and juicy ripe mangoes.', 'images/mango-cream-cake.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Mango Cream Cake');
INSERT INTO products (name, price, category, description, image) SELECT 'Butter Croissant', 120.00, 'pastries', 'Enjoy our golden Butter Croissant, freshly baked with crisp flaky layers and a rich buttery center.', 'images/butter-croissant.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Butter Croissant');
INSERT INTO products (name, price, category, description, image) SELECT 'Chocolate Croissant', 140.00, 'pastries', 'Savor our flaky Chocolate Croissant, filled with rich chocolate and baked to golden perfection.', 'images/chocolate-croissant.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chocolate Croissant');
INSERT INTO products (name, price, category, description, image) SELECT 'Almond Croissant', 160.00, 'pastries', 'Indulge in our buttery Almond Croissant, filled with almond cream and topped with toasted almonds and powdered sugar.', 'images/almond-croissant.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Almond Croissant');
INSERT INTO products (name, price, category, description, image) SELECT 'Danish Pastry', 130.00, 'pastries', 'Enjoy our delicate Danish Pastry, featuring buttery flaky layers with a lightly sweet and creamy filling.', 'images/danish-pastry.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Danish Pastry');
INSERT INTO products (name, price, category, description, image) SELECT 'Apple Turnover', 130.00, 'pastries', 'Taste our golden Apple Turnover, filled with warm cinnamon-spiced apples and finished with a delicate glaze.', 'images/apple-turnover.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Apple Turnover');
INSERT INTO products (name, price, category, description, image) SELECT 'Cinnamon Roll', 150.00, 'pastries', 'Enjoy our soft Cinnamon Roll, swirled with fragrant cinnamon sugar and finished with creamy glaze.', 'images/cinnamon-roll.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Cinnamon Roll');
INSERT INTO products (name, price, category, description, image) SELECT 'Cream Cheese Danish', 150.00, 'pastries', 'Savor our buttery Cream Cheese Danish, filled with smooth cream cheese and baked until perfectly golden.', 'images/cream-cheese-danish.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Cream Cheese Danish');
INSERT INTO products (name, price, category, description, image) SELECT 'Fruit Tart', 180.00, 'pastries', 'Enjoy our elegant Fruit Tart, featuring a crisp pastry shell, creamy filling, and a colorful assortment of fresh fruits.', 'images/fruit-tart.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Fruit Tart');
INSERT INTO products (name, price, category, description, image) SELECT 'Chocolate Éclair', 150.00, 'pastries', 'Indulge in our Chocolate Éclair, filled with silky cream and topped with a rich glossy chocolate glaze.', 'images/chocolate-eclair.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chocolate Éclair');
INSERT INTO products (name, price, category, description, image) SELECT 'Vanilla Éclair', 140.00, 'pastries', 'Savor our delicate Vanilla Éclair, filled with smooth vanilla cream and finished with a refined sweet glaze.', 'images/vanilla-eclair.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Vanilla Éclair');
INSERT INTO products (name, price, category, description, image) SELECT 'Chocolate Chip Cookies', 90.00, 'cookies', 'Enjoy our classic Chocolate Chip Cookies, baked soft and chewy with generous pockets of rich chocolate.', 'images/chocolate-chip-cookies.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chocolate Chip Cookies');
INSERT INTO products (name, price, category, description, image) SELECT 'Double Chocolate Cookies', 100.00, 'cookies', 'Satisfy your chocolate cravings with our rich Double Chocolate Cookies, soft-baked with generous chocolate chunks.', 'images/double-chocolate-cookies.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Double Chocolate Cookies');
INSERT INTO products (name, price, category, description, image) SELECT 'Red Velvet Cookies', 100.00, 'cookies', 'Enjoy our soft Red Velvet Cookies, combining rich cocoa flavor with a delicate sweetness and creamy chocolate accents.', 'images/red-velvet-cookies.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Red Velvet Cookies');
INSERT INTO products (name, price, category, description, image) SELECT 'Oatmeal Raisin Cookies', 90.00, 'cookies', 'Savor our chewy Oatmeal Raisin Cookies, packed with wholesome oats, plump raisins, and warm cinnamon flavor.', 'images/oatmeal-raisin-cookies.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Oatmeal Raisin Cookies');
INSERT INTO products (name, price, category, description, image) SELECT 'Butter Cookies', 80.00, 'cookies', 'Delight in our delicate Butter Cookies, baked to a crisp golden finish with a rich, buttery flavor.', 'images/butter-cookies.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Butter Cookies');
INSERT INTO products (name, price, category, description, image) SELECT 'Brown Butter Madeleines', 100.00, 'cookies', 'Enjoy our tender Brown Butter Madeleines, delicately sweet with a rich nutty flavor and golden edges.', 'images/brown-butter-madeleines.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Brown Butter Madeleines');
INSERT INTO products (name, price, category, description, image) SELECT 'Chocolate Brownies', 100.00, 'cookies', 'Savor our rich Chocolate Brownies, baked with a fudgy center and deep chocolate flavor in every bite.', 'images/chocolate-brownies.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chocolate Brownies');
INSERT INTO products (name, price, category, description, image) SELECT 'Fudge Brownies', 110.00, 'cookies', 'Indulge in our decadent Fudge Brownies, irresistibly dense and gooey with an intense chocolate flavor.', 'images/fudge-brownies.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Fudge Brownies');
INSERT INTO products (name, price, category, description, image) SELECT 'Lemon Bars', 100.00, 'cookies', 'Brighten your day with our zesty Lemon Bars, featuring a buttery crust and smooth, refreshing lemon filling.', 'images/lemon-bars.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Lemon Bars');
INSERT INTO products (name, price, category, description, image) SELECT 'Mini Cheesecake Bites', 120.00, 'cookies', 'Enjoy our creamy Mini Cheesecake Bites, featuring a buttery crust and smooth cheesecake filling in every bite.', 'images/mini-cheesecake-bites.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Mini Cheesecake Bites');
INSERT INTO products (name, price, category, description, image) SELECT 'Classic Milk Bread', 140.00, 'breads', 'Enjoy our soft and fluffy Classic Milk Bread, delicately sweet with a tender texture perfect for any occasion.', 'images/classic-milk-bread.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Classic Milk Bread');
INSERT INTO products (name, price, category, description, image) SELECT 'Brioche Loaf', 180.00, 'breads', 'Savor our buttery Brioche Loaf, rich and tender with a beautifully golden crust and soft pillowy interior.', 'images/brioche-loaf.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Brioche Loaf');
INSERT INTO products (name, price, category, description, image) SELECT 'Cinnamon Raisin Bread', 160.00, 'breads', 'Enjoy our soft Cinnamon Raisin Bread, swirled with fragrant cinnamon and sweet plump raisins.', 'images/cinnamon-raisin-bread.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Cinnamon Raisin Bread');
INSERT INTO products (name, price, category, description, image) SELECT 'Garlic Cream Cheese Bread', 150.00, 'breads', 'Indulge in our savory Garlic Cream Cheese Bread, filled with creamy cheese and infused with rich roasted garlic flavor.', 'images/garlic-cream-cheese-bread.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Garlic Cream Cheese Bread');
INSERT INTO products (name, price, category, description, image) SELECT 'Chocolate Babka', 250.00, 'breads', 'Treat yourself to our rich Chocolate Babka, featuring soft braided bread swirled with luscious chocolate filling.', 'images/chocolate-babka.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chocolate Babka');
INSERT INTO products (name, price, category, description, image) SELECT 'Banana Bread', 150.00, 'breads', 'Savor our moist Banana Bread, naturally sweet and warmly baked with ripe bananas for a comforting homemade flavor.', 'images/banana-bread.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Banana Bread');
INSERT INTO products (name, price, category, description, image) SELECT 'Ube Cheese Bread', 150.00, 'breads', 'Enjoy our soft Ube Cheese Bread, combining sweet Filipino ube with a creamy, savory cheese filling.', 'images/ube-cheese-bread.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Ube Cheese Bread');
INSERT INTO products (name, price, category, description, image) SELECT 'Ensaymada', 130.00, 'breads', 'Delight in our soft Filipino Ensaymada, topped with creamy butter, sugar, and delicate grated cheese.', 'images/filipino-ensaymada.png' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Ensaymada');
