-- ============================================================
-- EzyCommerce Production Seed Data
-- Auto-imported by Docker via docker-entrypoint-initdb.d
-- ============================================================

-- ============================================================
-- ROLES (must exist before any user inserts)
-- ============================================================
INSERT INTO roles (role_name) VALUES
    ('Customer'),
    ('Admin'),
    ('Vendor'),
    ('Logistics')
ON DUPLICATE KEY UPDATE role_name = role_name;

-- ============================================================
-- ADMIN USER (password: admin123)
-- ============================================================
INSERT INTO users (username, email, password, role_id) VALUES
    ('admin', 'admin@ezycommerce.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 2)
ON DUPLICATE KEY UPDATE username = username;

-- ============================================================
-- DISCOUNTS
-- ============================================================
INSERT INTO discounts (discount_name, discount_type, discount_value, start_date, end_date, apply_to, is_active) VALUES
('Summer Sale', 'percentage', 15.00, '2026-04-01 00:00:00', '2026-08-31 23:59:59', 'all', TRUE),
('Electronics Week', 'percentage', 20.00, '2026-05-01 00:00:00', '2026-05-15 23:59:59', 'categories', TRUE),
('Flash Deal', 'fixed', 10.00, '2026-05-01 00:00:00', '2026-06-30 23:59:59', 'selected', TRUE),
('New Customer', 'percentage', 10.00, '2026-01-01 00:00:00', '2026-12-31 23:59:59', 'all', TRUE),
('Clearance', 'percentage', 30.00, '2026-05-01 00:00:00', '2026-07-31 23:59:59', 'selected', TRUE);

-- ============================================================
-- CATEGORIES (with discount links)
-- ============================================================
UPDATE categories SET discount_id = NULL;
DELETE FROM categories WHERE category_id > 0;
ALTER TABLE categories AUTO_INCREMENT = 1;

INSERT INTO categories (category_name, discount_id) VALUES
('Electronics', 2),
('Clothing', NULL),
('Home & Kitchen', NULL),
('Sports & Outdoors', NULL),
('Books & Stationery', NULL),
('Health & Beauty', 1);

-- ============================================================
-- USERS: Vendor accounts (password: admin123)
-- ============================================================
INSERT INTO users (username, email, password, role_id) VALUES
('vendor_techzone', 'contact@techzone.store', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 3),
('vendor_urbanstyle', 'hello@urbanstyle.co', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 3),
('vendor_homecraft', 'sales@homecraft.io', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 3);

-- ============================================================
-- USERS: Customer accounts (password: admin123)
-- ============================================================
INSERT INTO users (username, email, password, role_id) VALUES
('sarah_johnson', 'sarah.johnson@gmail.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('mike_chen', 'mike.chen@outlook.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('emily_davis', 'emily.d@yahoo.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('james_wilson', 'jwilson@protonmail.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('priya_patel', 'priya.p@gmail.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('alex_martinez', 'alex.m@hotmail.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('jessica_lee', 'jess.lee@gmail.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('daniel_brown', 'dbrown@outlook.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('olivia_taylor', 'olivia.t@gmail.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1),
('ryan_anderson', 'ryan.a@yahoo.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 1);

-- ============================================================
-- USERS: Logistics account (password: admin123)
-- ============================================================
INSERT INTO users (username, email, password, role_id) VALUES
('logistics_ops', 'ops@ezycommerce.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 4),
('logistics_manager', 'logistics@ezycommerce.com', '$2y$10$BRRQyHe6fZfXuHTZszBFVO0nsauF3B8tnTkh310krhlyLadxQ9t0W', 4);

-- ============================================================
-- VENDORS
-- ============================================================
INSERT INTO vendors (user_id, vendor_name, contact_email) VALUES
((SELECT user_id FROM users WHERE username='vendor_techzone'), 'TechZone Electronics', 'contact@techzone.store'),
((SELECT user_id FROM users WHERE username='vendor_urbanstyle'), 'Urban Style Co.', 'hello@urbanstyle.co'),
((SELECT user_id FROM users WHERE username='vendor_homecraft'), 'HomeCraft Living', 'sales@homecraft.io');

-- ============================================================
-- PRODUCTS (30 realistic products across 6 categories)
-- ============================================================

-- Electronics (category_id=1, vendor=TechZone=1)
INSERT INTO products (name, description, price, stock, image_url, category_id, discount_id, vendor_id, is_active) VALUES
('Sony WH-1000XM5 Headphones', 'Industry-leading noise cancellation with exceptional sound quality. 30-hour battery life, multipoint connection, and speak-to-chat technology.', 349.99, 45, 'uploads/images/product1.jpg', 1, 2, 1, TRUE),
('Apple iPad Air M2 (2026)', '11-inch Liquid Retina display with M2 chip. Perfect for creative professionals and students. Supports Apple Pencil Pro.', 599.00, 30, 'uploads/images/product2.jpg', 1, NULL, 1, TRUE),
('Samsung Galaxy Watch Ultra', 'Premium smartwatch with titanium case, dual-band GPS, and 60-hour battery. Water resistant to 100m.', 649.99, 25, 'uploads/images/product3.jpg', 1, 2, 1, TRUE),
('Logitech MX Master 3S Mouse', 'Ergonomic wireless mouse with 8K DPI tracking, quiet clicks, and MagSpeed scroll wheel. USB-C fast charging.', 99.99, 80, 'uploads/images/product4.jpg', 1, NULL, 1, TRUE),
('Anker 737 Power Bank 24K mAh', 'Ultra-high capacity portable charger with 140W output. Charges MacBook Pro and phones simultaneously.', 109.99, 60, 'uploads/images/product5.jpg', 1, 3, 1, TRUE);

-- Clothing (category_id=2, vendor=UrbanStyle=2)
INSERT INTO products (name, description, price, stock, image_url, category_id, discount_id, vendor_id, is_active) VALUES
('Classic Denim Trucker Jacket', 'Timeless medium-wash denim jacket with button closure and chest pockets. 100% premium cotton with stretch comfort.', 89.99, 55, 'uploads/images/product6.jpg', 2, NULL, 2, TRUE),
('Merino Wool Crew Neck Sweater', 'Ultra-soft Australian merino wool sweater. Temperature-regulating, breathable, and naturally odor-resistant. Machine washable.', 79.99, 40, 'uploads/images/product1.jpg', 2, 1, 2, TRUE),
('Athletic Performance Sneakers', 'Lightweight running shoes with responsive cushioning foam and breathable knit upper. Reflective details for visibility.', 129.99, 70, 'uploads/images/product2.jpg', 2, NULL, 2, TRUE),
('Slim Fit Chino Pants', 'Versatile stretch chinos with modern slim fit. Wrinkle-resistant cotton blend fabric perfect for office or weekend wear.', 59.99, 90, 'uploads/images/product3.jpg', 2, NULL, 2, TRUE),
('Premium Cotton Oxford Shirt', 'Classic button-down Oxford shirt in crisp cotton. Tailored fit with reinforced collar and barrel cuffs.', 64.99, 65, 'uploads/images/product4.jpg', 2, 1, 2, TRUE);

-- Home & Kitchen (category_id=3, vendor=HomeCraft=3)
INSERT INTO products (name, description, price, stock, image_url, category_id, discount_id, vendor_id, is_active) VALUES
('Vitamix A3500 Blender', 'Professional-grade blender with touchscreen controls, 5 program settings, and self-cleaning cycle. Variable speed control.', 549.99, 20, 'uploads/images/product5.jpg', 3, NULL, 3, TRUE),
('Dyson V15 Detect Vacuum', 'Cordless stick vacuum with laser dust detection, piezo sensor, and LCD screen showing real-time particle counts.', 749.99, 15, 'uploads/images/product6.jpg', 3, 5, 3, TRUE),
('Le Creuset Dutch Oven 5.5 Qt', 'Enameled cast iron Dutch oven in signature Flame color. Superior heat distribution and retention for braising and baking.', 379.99, 25, 'uploads/images/product1.jpg', 3, NULL, 3, TRUE),
('Philips Hue Smart Bulb Starter Kit', 'Set of 4 color-changing smart LED bulbs with Bridge. 16 million colors, voice control compatible with Alexa and Google Home.', 199.99, 35, 'uploads/images/product2.jpg', 3, 3, 3, TRUE),
('Breville Barista Express Espresso', 'Semi-automatic espresso machine with integrated conical burr grinder, digital temperature control, and steam wand.', 699.99, 18, 'uploads/images/product3.jpg', 3, NULL, 3, TRUE);

-- Sports & Outdoors (category_id=4, vendor=TechZone=1)
INSERT INTO products (name, description, price, stock, image_url, category_id, discount_id, vendor_id, is_active) VALUES
('Hydro Flask 32 oz Water Bottle', 'Double-wall vacuum insulated stainless steel. Keeps drinks cold 24 hours or hot 12 hours. BPA-free and dishwasher safe.', 44.95, 120, 'uploads/images/product4.jpg', 4, NULL, 1, TRUE),
('Manduka PRO Yoga Mat', 'Lifetime guarantee yoga mat. 6mm thick, closed-cell surface prevents sweat absorption. Dense cushioning for joint protection.', 120.00, 40, 'uploads/images/product5.jpg', 4, NULL, 1, TRUE),
('Garmin Edge 540 Bike Computer', 'GPS cycling computer with solar charging, training guidance, and performance metrics. Touchscreen with turn-by-turn navigation.', 349.99, 22, 'uploads/images/product6.jpg', 4, 2, 1, TRUE),
('Coleman Sundome 4-Person Tent', 'Easy-setup camping tent with WeatherTec system. Welded floors and inverted seams keep water out. Fits queen-size air mattress.', 89.99, 30, 'uploads/images/product1.jpg', 4, NULL, 1, TRUE),
('Fitbit Charge 6 Fitness Tracker', 'Advanced health tracker with built-in GPS, heart rate monitoring, SpO2 sensor, and 7-day battery. Water resistant to 50m.', 159.95, 50, 'uploads/images/product2.jpg', 4, 1, 1, TRUE);

-- Books & Stationery (category_id=5, vendor=UrbanStyle=2)
INSERT INTO products (name, description, price, stock, image_url, category_id, discount_id, vendor_id, is_active) VALUES
('Moleskine Classic Notebook Large', 'Iconic hardcover notebook with 240 ruled pages, ivory-colored acid-free paper, and expandable inner pocket. Lay-flat binding.', 24.95, 100, 'uploads/images/product3.jpg', 5, NULL, 2, TRUE),
('Lamy Safari Fountain Pen', 'Lightweight, durable fountain pen with ergonomic grip and steel nib. Includes blue ink cartridge. Available in multiple colors.', 32.00, 75, 'uploads/images/product4.jpg', 5, NULL, 2, TRUE),
('Clean Code by Robert C. Martin', 'A handbook of agile software craftsmanship. Essential reading for every software developer who wants to write better code.', 39.99, 50, 'uploads/images/product5.jpg', 5, NULL, 2, TRUE),
('Desk Organizer Bamboo Wood', 'Multi-compartment desktop organizer with pen holder, phone stand, and mail sorter. Eco-friendly natural bamboo construction.', 34.99, 45, 'uploads/images/product6.jpg', 5, NULL, 2, TRUE),
('Leuchtturm1917 Bullet Journal', 'Dot grid notebook with numbered pages, table of contents, and index labels. 249 pages of 80g/m² acid-free paper.', 29.95, 60, 'uploads/images/product1.jpg', 5, NULL, 2, TRUE);

-- Health & Beauty (category_id=6, vendor=HomeCraft=3)
INSERT INTO products (name, description, price, stock, image_url, category_id, discount_id, vendor_id, is_active) VALUES
('Dyson Airwrap Multi-Styler', 'Complete hair styling tool using Coanda airflow. Includes barrels for curls, waves, and smoothing brush attachments.', 599.99, 20, 'uploads/images/product2.jpg', 6, 1, 3, TRUE),
('CeraVe Hydrating Facial Cleanser', 'Gentle, non-foaming face wash with ceramides, hyaluronic acid, and MVE technology. Dermatologist recommended for dry skin.', 16.99, 150, 'uploads/images/product3.jpg', 6, NULL, 3, TRUE),
('Oral-B iO Series 9 Electric Toothbrush', 'Smart electric toothbrush with 3D tracking, 7 cleaning modes, and AI-powered brushing coach via app. Magnetic charger.', 299.99, 30, 'uploads/images/product4.jpg', 6, 5, 3, TRUE),
('Theragun Elite Massage Gun', 'Percussive therapy device with 5 speeds, QuietForce technology, and ergonomic multi-grip. Bluetooth app integration.', 399.00, 25, 'uploads/images/product5.jpg', 6, NULL, 3, TRUE),
('Philips Sonicare DiamondClean', 'Premium sonic toothbrush with 5 modes, pressure sensor, and elegant charging glass. Includes USB travel case.', 199.99, 35, 'uploads/images/product6.jpg', 6, 3, 3, TRUE);

-- ============================================================
-- CUSTOMER DETAILS
-- ============================================================
INSERT INTO customerdetails (user_id, full_name, billing_address, shipping_address, address, phone) VALUES
((SELECT user_id FROM users WHERE username='sarah_johnson'), 'Sarah Johnson', '742 Evergreen Terrace, Springfield, IL 62704', '742 Evergreen Terrace, Springfield, IL 62704', '742 Evergreen Terrace, Springfield, IL 62704', '+1-555-0101'),
((SELECT user_id FROM users WHERE username='mike_chen'), 'Michael Chen', '1600 Amphitheatre Pkwy, Mountain View, CA 94043', '88 Colin P Kelly Jr St, San Francisco, CA 94107', '1600 Amphitheatre Pkwy, Mountain View, CA 94043', '+1-555-0102'),
((SELECT user_id FROM users WHERE username='emily_davis'), 'Emily Davis', '350 Fifth Avenue, New York, NY 10118', '350 Fifth Avenue, New York, NY 10118', '350 Fifth Avenue, New York, NY 10118', '+1-555-0103'),
((SELECT user_id FROM users WHERE username='james_wilson'), 'James Wilson', '233 S Wacker Dr, Chicago, IL 60606', '233 S Wacker Dr, Chicago, IL 60606', '233 S Wacker Dr, Chicago, IL 60606', '+1-555-0104'),
((SELECT user_id FROM users WHERE username='priya_patel'), 'Priya Patel', '1 Hacker Way, Menlo Park, CA 94025', '1 Hacker Way, Menlo Park, CA 94025', '1 Hacker Way, Menlo Park, CA 94025', '+1-555-0105'),
((SELECT user_id FROM users WHERE username='alex_martinez'), 'Alex Martinez', '410 Terry Ave N, Seattle, WA 98109', '410 Terry Ave N, Seattle, WA 98109', '410 Terry Ave N, Seattle, WA 98109', '+1-555-0106'),
((SELECT user_id FROM users WHERE username='jessica_lee'), 'Jessica Lee', '1 Apple Park Way, Cupertino, CA 95014', '1 Apple Park Way, Cupertino, CA 95014', '1 Apple Park Way, Cupertino, CA 95014', '+1-555-0107'),
((SELECT user_id FROM users WHERE username='daniel_brown'), 'Daniel Brown', '500 108th Ave NE, Bellevue, WA 98004', '500 108th Ave NE, Bellevue, WA 98004', '500 108th Ave NE, Bellevue, WA 98004', '+1-555-0108');

-- ============================================================
-- ORDERS (diverse statuses across multiple customers)
-- ============================================================
INSERT INTO orders (customer_id, order_status, total_amount, created_at) VALUES
((SELECT user_id FROM users WHERE username='sarah_johnson'), 'Delivered', 449.98, '2026-03-15 10:30:00'),
((SELECT user_id FROM users WHERE username='sarah_johnson'), 'Shipped', 129.99, '2026-04-22 14:15:00'),
((SELECT user_id FROM users WHERE username='mike_chen'), 'Delivered', 699.99, '2026-03-20 09:00:00'),
((SELECT user_id FROM users WHERE username='mike_chen'), 'Processing', 164.94, '2026-04-28 11:45:00'),
((SELECT user_id FROM users WHERE username='emily_davis'), 'Delivered', 379.99, '2026-02-10 16:20:00'),
((SELECT user_id FROM users WHERE username='emily_davis'), 'Pending', 89.98, '2026-04-30 08:00:00'),
((SELECT user_id FROM users WHERE username='james_wilson'), 'Shipped', 549.99, '2026-04-18 13:30:00'),
((SELECT user_id FROM users WHERE username='priya_patel'), 'Delivered', 229.94, '2026-03-05 10:00:00'),
((SELECT user_id FROM users WHERE username='priya_patel'), 'Cancelled', 749.99, '2026-04-10 15:00:00'),
((SELECT user_id FROM users WHERE username='alex_martinez'), 'Delivered', 159.95, '2026-03-28 12:00:00'),
((SELECT user_id FROM users WHERE username='alex_martinez'), 'Processing', 599.99, '2026-04-29 09:30:00'),
((SELECT user_id FROM users WHERE username='jessica_lee'), 'Delivered', 349.99, '2026-02-20 11:15:00'),
((SELECT user_id FROM users WHERE username='daniel_brown'), 'Pending', 299.99, '2026-04-30 17:00:00'),
((SELECT user_id FROM users WHERE username='olivia_taylor'), 'Shipped', 199.99, '2026-04-25 10:00:00'),
((SELECT user_id FROM users WHERE username='ryan_anderson'), 'Delivered', 89.99, '2026-03-10 14:30:00');

-- ============================================================
-- ORDER ITEMS
-- ============================================================
INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, vendor_status) VALUES
(1, 1, 1, 349.99, 'ReadyToShip'), (1, 4, 1, 99.99, 'ReadyToShip'),
(2, 8, 1, 129.99, 'ReadyToShip'),
(3, 15, 1, 699.99, 'ReadyToShip'),
(4, 9, 1, 59.99, 'Pending'), (4, 10, 1, 64.99, 'Pending'), (4, 22, 1, 39.99, 'Pending'),
(5, 13, 1, 379.99, 'ReadyToShip'),
(6, 6, 1, 89.99, 'Pending'),
(7, 11, 1, 549.99, 'ReadyToShip'),
(8, 14, 1, 199.99, 'ReadyToShip'), (8, 21, 1, 24.95, 'ReadyToShip'),
(9, 12, 1, 749.99, 'Cancelled'),
(10, 20, 1, 159.95, 'ReadyToShip'),
(11, 26, 1, 599.99, 'Pending'),
(12, 1, 1, 349.99, 'ReadyToShip'),
(13, 28, 1, 299.99, 'Pending'),
(14, 14, 1, 199.99, 'ReadyToShip'),
(15, 6, 1, 89.99, 'ReadyToShip');

-- ============================================================
-- SHIPPING
-- ============================================================
INSERT INTO shipping (order_id, shipping_status, tracking_number, handled_by) VALUES
(1, 'Delivered', 'TRK-2026-001542', (SELECT user_id FROM users WHERE username='logistics_ops')),
(2, 'Shipped', 'TRK-2026-002891', (SELECT user_id FROM users WHERE username='logistics_ops')),
(3, 'Delivered', 'TRK-2026-001803', (SELECT user_id FROM users WHERE username='logistics_manager')),
(5, 'Delivered', 'TRK-2026-000987', (SELECT user_id FROM users WHERE username='logistics_ops')),
(7, 'Shipped', 'TRK-2026-003104', (SELECT user_id FROM users WHERE username='logistics_manager')),
(8, 'Delivered', 'TRK-2026-001205', (SELECT user_id FROM users WHERE username='logistics_ops')),
(10, 'Delivered', 'TRK-2026-002156', (SELECT user_id FROM users WHERE username='logistics_manager')),
(12, 'Delivered', 'TRK-2026-001001', (SELECT user_id FROM users WHERE username='logistics_ops')),
(14, 'Shipped', 'TRK-2026-003298', (SELECT user_id FROM users WHERE username='logistics_manager')),
(15, 'Delivered', 'TRK-2026-001678', (SELECT user_id FROM users WHERE username='logistics_ops'));

-- ============================================================
-- PAYMENTS
-- ============================================================
INSERT INTO payments (order_id, amount, method, status, transaction_date) VALUES
(1, 449.98, 'Credit Card', 'Completed', '2026-03-15 10:32:00'),
(2, 129.99, 'Credit Card', 'Completed', '2026-04-22 14:17:00'),
(3, 699.99, 'Mobile Banking', 'Completed', '2026-03-20 09:05:00'),
(4, 164.94, 'Credit Card', 'Pending', '2026-04-28 11:47:00'),
(5, 379.99, 'Cash on Delivery', 'Completed', '2026-02-14 10:00:00'),
(6, 89.98, 'Credit Card', 'Pending', '2026-04-30 08:02:00'),
(7, 549.99, 'Mobile Banking', 'Completed', '2026-04-18 13:33:00'),
(8, 229.94, 'Credit Card', 'Completed', '2026-03-05 10:03:00'),
(9, 749.99, 'Credit Card', 'Failed', '2026-04-10 15:02:00'),
(10, 159.95, 'Cash on Delivery', 'Completed', '2026-03-30 12:00:00'),
(11, 599.99, 'Credit Card', 'Pending', '2026-04-29 09:32:00'),
(12, 349.99, 'Mobile Banking', 'Completed', '2026-02-20 11:18:00'),
(13, 299.99, 'Credit Card', 'Pending', '2026-04-30 17:02:00'),
(14, 199.99, 'Cash on Delivery', 'Completed', '2026-04-25 10:03:00'),
(15, 89.99, 'Credit Card', 'Completed', '2026-03-10 14:32:00');

-- ============================================================
-- RETURNS
-- ============================================================
INSERT INTO returns (order_id, reason, status, handled_by) VALUES
(1, 'Headphones had intermittent Bluetooth connectivity issues. Right ear cup audio cutting out during calls.', 'Approved', (SELECT user_id FROM users WHERE username='logistics_ops')),
(3, 'Received wrong color variant. Ordered Space Black but received Silver.', 'Approved', (SELECT user_id FROM users WHERE username='logistics_manager')),
(5, 'Minor chip on the enamel rim of the Dutch oven. Would like a replacement.', 'Processing', (SELECT user_id FROM users WHERE username='logistics_ops')),
(9, 'Order was cancelled before shipping. Requesting full refund.', 'Approved', (SELECT user_id FROM users WHERE username='logistics_manager'));

-- ============================================================
-- CARTS (active shopping carts)
-- ============================================================
INSERT INTO cart (customer_id) VALUES
((SELECT user_id FROM users WHERE username='sarah_johnson')),
((SELECT user_id FROM users WHERE username='emily_davis')),
((SELECT user_id FROM users WHERE username='priya_patel')),
((SELECT user_id FROM users WHERE username='jessica_lee'));

INSERT INTO cart_items (cart_id, product_id, quantity) VALUES
(1, 5, 1), (1, 22, 2),
(2, 26, 1), (2, 27, 3),
(3, 3, 1), (3, 16, 2),
(4, 11, 1);

-- ============================================================
-- WISHLISTS
-- ============================================================
INSERT INTO wishlist (user_id, product_id) VALUES
((SELECT user_id FROM users WHERE username='sarah_johnson'), 3),
((SELECT user_id FROM users WHERE username='sarah_johnson'), 15),
((SELECT user_id FROM users WHERE username='sarah_johnson'), 26),
((SELECT user_id FROM users WHERE username='mike_chen'), 1),
((SELECT user_id FROM users WHERE username='mike_chen'), 11),
((SELECT user_id FROM users WHERE username='emily_davis'), 7),
((SELECT user_id FROM users WHERE username='emily_davis'), 30),
((SELECT user_id FROM users WHERE username='james_wilson'), 2),
((SELECT user_id FROM users WHERE username='james_wilson'), 18),
((SELECT user_id FROM users WHERE username='priya_patel'), 22),
((SELECT user_id FROM users WHERE username='priya_patel'), 29),
((SELECT user_id FROM users WHERE username='alex_martinez'), 5),
((SELECT user_id FROM users WHERE username='jessica_lee'), 12),
((SELECT user_id FROM users WHERE username='jessica_lee'), 8),
((SELECT user_id FROM users WHERE username='daniel_brown'), 17);
