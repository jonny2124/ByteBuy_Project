-- Seed data for ByteBuy (generated from shop.js product list)
USE bytebuy;

-- Note: password hash placeholder; create your own user via register.php if needed.
INSERT INTO users (email, password_hash, full_name, is_admin)
VALUES ('demo@example.com', '$2y$10$eImiTXuWVxfM37uY4JANj.QJ0G6fQe4Z3e2Yx1q8x/8z9s8GZ8yG', 'Demo User', 1);

-- Items following the shop.js PRODUCTS ids and names. Stock randomized between 1 and 50 using RAND().
INSERT INTO items (sku, name, description, price, stock, image) VALUES
('lap1','MacBook Air M2','MacBook Air M2',1099.00, FLOOR(RAND()*50)+1,'assets/deal2.jpeg'),
('lap2','MacBook Air M4','MacBook Air M4',1299.00, FLOOR(RAND()*50)+1,'assets/macbook.jpeg'),
('lap3','Dell XPS 13 (2024)','Dell XPS 13 (2024)',1199.00, FLOOR(RAND()*50)+1,'assets/placeholder_laptop1.jpg'),
('lap4','HP Spectre x360','HP Spectre x360',1149.00, FLOOR(RAND()*50)+1,'assets/placeholder_laptop2.jpg'),
('lap5','Lenovo Yoga 7','Lenovo Yoga 7',999.00, FLOOR(RAND()*50)+1,'assets/placeholder_laptop3.jpg'),
('lap6','ASUS ZenBook 14','ASUS ZenBook 14',1049.00, FLOOR(RAND()*50)+1,'assets/placeholder_laptop4.jpg'),
('lap7','Acer Swift Go','Acer Swift Go',899.00, FLOOR(RAND()*50)+1,'assets/placeholder_laptop5.jpg'),
('lap8','MSI Modern 14','MSI Modern 14',949.00, FLOOR(RAND()*50)+1,'assets/placeholder_laptop6.jpg'),

('ph1','Samsung Galaxy S23','Samsung Galaxy S23',899.00, FLOOR(RAND()*50)+1,'assets/deal1.webp'),
('ph2','iPhone 15','iPhone 15',999.00, FLOOR(RAND()*50)+1,'assets/placeholder_phone1.jpg'),
('ph3','Google Pixel 8','Google Pixel 8',799.00, FLOOR(RAND()*50)+1,'assets/placeholder_phone2.jpg'),
('ph4','OnePlus 12','OnePlus 12',749.00, FLOOR(RAND()*50)+1,'assets/placeholder_phone3.jpg'),
('ph5','Xiaomi 13T','Xiaomi 13T',599.00, FLOOR(RAND()*50)+1,'assets/placeholder_phone4.jpg'),
('ph6','Nothing Phone (2a)','Nothing Phone (2a)',499.00, FLOOR(RAND()*50)+1,'assets/placeholder_phone5.jpg'),
('ph7','iPhone 14','iPhone 14',799.00, FLOOR(RAND()*50)+1,'assets/placeholder_phone6.jpg'),
('ph8','Samsung A55','Samsung A55',449.00, FLOOR(RAND()*50)+1,'assets/placeholder_phone7.jpg'),

('au1','Sony WH-1000XM5','Sony WH-1000XM5',399.00, FLOOR(RAND()*50)+1,'assets/deal3.jpg'),
('au2','AirPods Pro (2nd Gen)','AirPods Pro (2nd Gen)',249.00, FLOOR(RAND()*50)+1,'assets/placeholder_audio1.jpg'),
('au3','Bose QC Ultra','Bose QC Ultra',349.00, FLOOR(RAND()*50)+1,'assets/placeholder_audio2.jpg'),
('au4','Sony WF-1000XM5','Sony WF-1000XM5',279.00, FLOOR(RAND()*50)+1,'assets/placeholder_audio3.jpg'),
('au5','Sennheiser Momentum 4','Sennheiser Momentum 4',329.00, FLOOR(RAND()*50)+1,'assets/placeholder_audio4.jpg'),
('au6','Beats Studio Pro','Beats Studio Pro',299.00, FLOOR(RAND()*50)+1,'assets/placeholder_audio5.jpg'),
('au7','JBL Live Pro 2','JBL Live Pro 2',149.00, FLOOR(RAND()*50)+1,'assets/placeholder_audio6.jpg'),
('au8','Anker Soundcore Q45','Anker Soundcore Q45',129.00, FLOOR(RAND()*50)+1,'assets/placeholder_audio7.jpg'),

('st1','Samsung T7 1TB SSD','Samsung T7 1TB SSD',119.00, FLOOR(RAND()*50)+1,'assets/placeholder_storage1.jpg'),
('st2','SanDisk Extreme 1TB SSD','SanDisk Extreme 1TB SSD',129.00, FLOOR(RAND()*50)+1,'assets/placeholder_storage2.jpg'),
('st3','WD MyPassport 2TB HDD','WD MyPassport 2TB HDD',89.00, FLOOR(RAND()*50)+1,'assets/placeholder_storage3.jpg'),
('st4','Lexar NM790 1TB NVMe','Lexar NM790 1TB NVMe',99.00, FLOOR(RAND()*50)+1,'assets/placeholder_storage4.jpg'),
('st5','Kingston KC3000 2TB','Kingston KC3000 2TB',189.00, FLOOR(RAND()*50)+1,'assets/placeholder_storage5.jpg'),
('st6','Crucial X9 2TB SSD','Crucial X9 2TB SSD',179.00, FLOOR(RAND()*50)+1,'assets/placeholder_storage6.jpg'),
('st7','Seagate Expansion 4TB','Seagate Expansion 4TB',129.00, FLOOR(RAND()*50)+1,'assets/placeholder_storage7.jpg'),
('st8','Samsung EVO Plus 256GB','Samsung EVO Plus 256GB',29.00, FLOOR(RAND()*50)+1,'assets/placeholder_storage8.jpg'),

('ac1','Apple Magic Mouse','Apple Magic Mouse',79.00, FLOOR(RAND()*50)+1,'assets/placeholder_acc1.jpg'),
('ac2','Logitech MX Master 3S','Logitech MX Master 3S',99.00, FLOOR(RAND()*50)+1,'assets/placeholder_acc2.jpg'),
('ac3','Keychron K2 V2','Keychron K2 V2',89.00, FLOOR(RAND()*50)+1,'assets/placeholder_acc3.jpg'),
('ac4','Anker 737 Charger','Anker 737 Charger',59.00, FLOOR(RAND()*50)+1,'assets/placeholder_acc4.jpg'),
('ac5','UGREEN USB-C Hub 7-in-1','UGREEN USB-C Hub 7-in-1',49.00, FLOOR(RAND()*50)+1,'assets/placeholder_acc5.jpg'),
('ac6','Apple Pencil (2nd Gen)','Apple Pencil (2nd Gen)',119.00, FLOOR(RAND()*50)+1,'assets/placeholder_acc6.jpg'),
('ac7','Samsung 45W PD Charger','Samsung 45W PD Charger',39.00, FLOOR(RAND()*50)+1,'assets/placeholder_acc7.jpg'),
('ac8','Baseus Laptop Stand','Baseus Laptop Stand',29.00, FLOOR(RAND()*50)+1,'assets/placeholder_acc8.jpg');

-- Example settings
INSERT INTO settings (`key`, `value`) VALUES
('store_name','ByteBuy Demo Store');

-- End of seed
