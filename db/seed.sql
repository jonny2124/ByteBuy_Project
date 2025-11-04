-- Seed data for ByteBuy (generated from shop.js product list)
USE bytebuy;

-- Note: password hash placeholder; create your own user via register.php if needed.
INSERT INTO users (email, password_hash, full_name, is_admin)
VALUES ('demo@example.com', '$2y$10$eImiTXuWVxfM37uY4JANj.QJ0G6fQe4Z3e2Yx1q8x/8z9s8GZ8yG', 'Demo User', 1);

-- Items following the shop.js PRODUCTS ids and names. Stock randomized between 1 and 50 using RAND().
INSERT INTO items (sku, name, description, price, stock, image) VALUES
('lap1','MacBook Air M2','MacBook Air M2',1099.00, FLOOR(RAND()*50)+1,'assets/products/macbookm2.jpeg'),
('lap2','MacBook Air M4','MacBook Air M4',1299.00, FLOOR(RAND()*50)+1,'assets/products/macbookm4.jpeg'),
('lap3','Dell XPS 13 (2024)','Dell XPS 13 (2024)',1199.00, FLOOR(RAND()*50)+1,'assets/products/DellXPS.jpeg'),
('lap4','HP Spectre x360','HP Spectre x360',1149.00, FLOOR(RAND()*50)+1,'assets/products/HPSpectre.jpg'),
('lap5','Lenovo Yoga 7','Lenovo Yoga 7',999.00, FLOOR(RAND()*50)+1,'assets/products/LenovoYoga7.avif'),
('lap6','ASUS ZenBook 14','ASUS ZenBook 14',1049.00, FLOOR(RAND()*50)+1,'assets/products/AsusZenbook.jpg'),
('lap7','Acer Swift Go','Acer Swift Go',899.00, FLOOR(RAND()*50)+1,'assets/products/AcerSwiftGo.jpg'),
('lap8','MSI Modern 14','MSI Modern 14',949.00, FLOOR(RAND()*50)+1,'assets/products/MSIModern14.avif'),

('ph1','Samsung Galaxy S23','Samsung Galaxy S23',899.00, FLOOR(RAND()*50)+1,'assets/products/SamsungGalaxyS25.jpg'),
('ph2','iPhone 15','iPhone 15',999.00, FLOOR(RAND()*50)+1,'assets/products/iphone15.jpg'),
('ph3','Google Pixel 8','Google Pixel 8',799.00, FLOOR(RAND()*50)+1,'assets/products/GooglePixel10.png'),
('ph4','OnePlus 12','OnePlus 12',749.00, FLOOR(RAND()*50)+1,'assets/products/Oneplus15.jpg'),
('ph5','Xiaomi 13T','Xiaomi 13T',599.00, FLOOR(RAND()*50)+1,'assets/products/Xiaomi15.jpg'),
('ph6','Nothing Phone (2a)','Nothing Phone (2a)',499.00, FLOOR(RAND()*50)+1,'assets/products/Nothingphone3.jpg'),
('ph7','iPhone 14','iPhone 14',799.00, FLOOR(RAND()*50)+1,'assets/products/iphone14.jpg'),
('ph8','Samsung A55','Samsung A55',449.00, FLOOR(RAND()*50)+1,'assets/products/SamsungA56.webp'),

('au1','Sony WH-1000XM5','Sony WH-1000XM5',399.00, FLOOR(RAND()*50)+1,'assets/products/SonyWF-1000XM5.jpg'),
('au2','AirPods Pro 3','AirPods Pro 3',249.00, FLOOR(RAND()*50)+1,'assets/products/AirPodsPro3.png'),
('au3','Bose QC Ultra','Bose QC Ultra',349.00, FLOOR(RAND()*50)+1,'assets/products/BoseQuietComfort.jpg'),
('au4','Sony WF-1000XM5','Sony WF-1000XM5',279.00, FLOOR(RAND()*50)+1,'assets/products/SonyWF-1000XM5.jpg'),
('au5','Sennheiser Momentum 4','Sennheiser Momentum 4',329.00, FLOOR(RAND()*50)+1,'assets/products/Momentum4.webp'),
('au6','Beats Studio Pro','Beats Studio Pro',299.00, FLOOR(RAND()*50)+1,'assets/products/BeatsStudioPro.jpeg'),
('au7','JBL Live Pro 2','JBL Live Pro 2',149.00, FLOOR(RAND()*50)+1,'assets/products/JBLLivePro2.jpg'),
('au8','Anker Soundcore Q45','Anker Soundcore Q45',129.00, FLOOR(RAND()*50)+1,'assets/products/AnkerSoundcoreQ45.jpg'),

('st1','Samsung T7 1TB SSD','Samsung T7 1TB SSD',119.00, FLOOR(RAND()*50)+1,'assets/products/SamsungT7.jpg'),
('st2','SanDisk Extreme 1TB SSD','SanDisk Extreme 1TB SSD',129.00, FLOOR(RAND()*50)+1,'assets/products/SanDiskExtreme.jpeg'),
('st3','WD MyPassport 2TB HDD','WD MyPassport 2TB HDD',89.00, FLOOR(RAND()*50)+1,'assets/products/WDMyPassport.jpg'),
('st4','Lexar NM790 1TB NVMe','Lexar NM790 1TB NVMe',99.00, FLOOR(RAND()*50)+1,'assets/products/LexarNM790.jpg'),
('st5','Kingston KC3000 2TB','Kingston KC3000 2TB',189.00, FLOOR(RAND()*50)+1,'assets/products/KingstonKC3000.jpg'),
('st6','Crucial X9 2TB SSD','Crucial X9 2TB SSD',179.00, FLOOR(RAND()*50)+1,'assets/products/CrucialX9.jpg'),
('st7','Seagate Expansion 4TB','Seagate Expansion 4TB',129.00, FLOOR(RAND()*50)+1,'assets/products/SeagateExpansion.jpg'),
('st8','Samsung EVO Plus 256GB','Samsung EVO Plus 256GB',29.00, FLOOR(RAND()*50)+1,'assets/products/SamsungEvoPlus.jpg'),

('ac1','Apple Magic Mouse','Apple Magic Mouse',79.00, FLOOR(RAND()*50)+1,'assets/products/AppleMagicMouse.jpg'),
('ac2','Logitech MX Master 3S','Logitech MX Master 3S',99.00, FLOOR(RAND()*50)+1,'assets/products/LogitechMXMaster.webp'),
('ac3','Keychron K2 V2','Keychron K2 V2',89.00, FLOOR(RAND()*50)+1,'assets/products/KeychronK2.jpg'),
('ac4','Anker 737 Charger','Anker 737 Charger',59.00, FLOOR(RAND()*50)+1,'assets/products/Anker737.jpeg'),
('ac5','UGREEN USB-C Hub 7-in-1','UGREEN USB-C Hub 7-in-1',49.00, FLOOR(RAND()*50)+1,'assets/products/UgreenUSB-CHub.jpg'),
('ac6','Apple Pencil (2nd Gen)','Apple Pencil (2nd Gen)',119.00, FLOOR(RAND()*50)+1,'assets/products/ApplePencil.jpeg'),
('ac7','Samsung 45W PD Charger','Samsung 45W PD Charger',39.00, FLOOR(RAND()*50)+1,'assets/products/Samsung45WPDCharger.avif'),
('ac8','Baseus Laptop Stand','Baseus Laptop Stand',29.00, FLOOR(RAND()*50)+1,'assets/products/BaseusLaptopStand.webp');

-- Example settings
INSERT INTO settings (`key`, `value`) VALUES
('store_name','ByteBuy Demo Store');

-- Demo coupons (idempotent)
INSERT INTO coupons (code, type, value, active, min_subtotal, max_uses, expires_at)
VALUES
('BYTE10','percent',10,1,200.00,NULL,NULL),
('STUDENT5','percent',5,1,100.00,NULL,NULL),
('FREESHIP','fixed',15,1,150.00,500,NULL)
ON DUPLICATE KEY UPDATE
  type = VALUES(type),
  value = VALUES(value),
  active = VALUES(active),
  min_subtotal = VALUES(min_subtotal),
  max_uses = VALUES(max_uses),
  expires_at = VALUES(expires_at);

-- End of seed
