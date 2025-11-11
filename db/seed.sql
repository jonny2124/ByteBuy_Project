-- Seed data for ByteBuy (generated from shop.js product list)
USE bytebuy;

-- Note: password hash placeholder
INSERT INTO users (email, password_hash, full_name, is_admin)
VALUES ('demo@example.com', '$2y$10$eImiTXuWVxfM37uY4JANj.QJ0G6fQe4Z3e2Yx1q8x/8z9s8GZ8yG', 'Demo User', 1);

-- Items following the shop.js PRODUCTS ids and names. Stock randomized between 1 and 50 using RAND().
INSERT INTO items (sku, name, description, price, stock, image) VALUES
('lap1','MacBook Air M2','Ultra-slim fanless design pairs new M2 speed with all-day mobility. The vivid 13.6-inch Liquid Retina display stays bright indoors or outdoors without draining power. MagSafe charging, Thunderbolt 4, and up to 18-hour battery make this Air effortless to travel with.',1099.00, FLOOR(RAND()*50)+1,'assets/products/macbookm2.jpeg'),
('lap2','MacBook Air M4','Next-gen M4 chip delivers pro-grade power in the iconic Air frame. Studio-quality microphones and six-speaker Spatial Audio make remote collaboration feel premium. Wi-Fi 6E, dual Thunderbolt 4, and a razor-thin design keep your desk minimal yet fast.',1299.00, FLOOR(RAND()*50)+1,'assets/products/macbookm4.jpeg'),
('lap3','Dell XPS 13 (2024)','InfinityEdge display and 13th-gen Intel power serious portable workflows. CNC-milled aluminum plus carbon fiber palm rests feel as rich as they look. ExpressCharge and dual Thunderbolt 4 ports keep creativity moving from café to studio.',1199.00, FLOOR(RAND()*50)+1,'assets/products/DellXPS.jpeg'),
('lap4','HP Spectre x360','360-degree convertible OLED touchscreen merges artistry, pen input, and speed. The gem-cut chassis hides next-gen Intel processors with dedicated AI acceleration. Bang & Olufsen audio and long battery life turn any seat into a mobile theater.',1149.00, FLOOR(RAND()*50)+1,'assets/products/HPSpectre.jpg'),
('lap5','Lenovo Yoga 7','All-metal Yoga chassis balances Ryzen performance with flexible creativity. 16:10 touchscreen, Dolby Vision, and Dolby Atmos speakers boost binge-worthy immersion. Smart AI tuning and rapid-charging battery support work, play, and sketching anywhere.',999.00, FLOOR(RAND()*50)+1,'assets/products/LenovoYoga7.avif'),
('lap6','ASUS ZenBook 14','ZenBook OLED clarity, Thunderbolt 4, and Wi-Fi 6E boost creative flow. The ErgoLift hinge lifts the keyboard for better thermals and typing comfort. Military-grade durability plus fast charging mean this ultrabook keeps up with nonstop travel.',1049.00, FLOOR(RAND()*50)+1,'assets/products/AsusZenbook.jpg'),
('lap7','Acer Swift Go','Swift Go keeps Evo-certified responsiveness inside a featherlight frame. 2.8K OLED display, twin fans, and LPDDR5 memory handle both edits and marathons. Killer Wi-Fi 6E and a 1440p webcam make hybrid work crisp and uninterrupted.',899.00, FLOOR(RAND()*50)+1,'assets/products/AcerSwiftGo.jpg'),
('lap8','MSI Modern 14','Modern 14 blends Ryzen efficiency with minimalist magnesium style. A full-size backlit keyboard, Hi-Res audio, and Nahimic software elevate daily grinding. USB-C PD charging and military-grade toughness keep productivity moving beyond the office.',949.00, FLOOR(RAND()*50)+1,'assets/products/MSIModern14.avif'),

('ph1','Samsung Galaxy S25','Galaxy S25 flagship cameras, 120Hz AMOLED, and Snapdragon Elite silicon. AI-powered Nightography sharpens every shot while Vision Booster fights glare. Knox Vault security, IP68 sealing, and 45W Super Fast Charging fit a pro toolkit in your pocket.',899.00, FLOOR(RAND()*50)+1,'assets/products/SamsungGalaxyS25.jpg'),
('ph2','iPhone 17','iPhone 17 A19 Pro chip pushes cinematic capture and marathon battery life. ProMotion display hits 120Hz for buttery scrolling while Dynamic Island surfaces live info. Crash Detection, satellite messaging, and USB-C keep it ready for unpredictable days.',999.00, FLOOR(RAND()*50)+1,'assets/products/iphone17.jpg'),
('ph3','Google Pixel 10','Pixel 10 delivers Tensor-powered AI photography with clean Android 15. Real-time HDR video, Best Take, and Magic Editor rewrite what mobile shooting can do. Seven years of updates plus Titan security give this phone a longer, safer lifespan.',799.00, FLOOR(RAND()*50)+1,'assets/products/GooglePixel10.png'),
('ph4','OnePlus 15','OnePlus 15 marries 120W charging with silky 2K ProXDR display for enthusiasts. Trinity Engine optimization keeps frames stable even during marathon gaming. Hasselblad tuning and ultra-wide spectral sensors let every photo pop with natural color.',749.00, FLOOR(RAND()*50)+1,'assets/products/Oneplus15.jpg'),
('ph5','Xiaomi 13T','Xiaomi 13T packs Leica optics, Dimensity muscle, and smart vapor-cooling. Custom color profiles, 3.5K HDR recording, and IP68 protection rival premium flagships. TurboCharge refuels to 100% in minutes so you miss fewer shots or meetings.',599.00, FLOOR(RAND()*50)+1,'assets/products/Xiaomi15.jpg'),
('ph6','Nothing Phone 3','Nothing Phone 3 Glyph interface and pure Android feel futuristic yet friendly. Transparent Gorilla Glass reveals the precision build while LED cues show alerts silently. Snapdragon performance, wireless charging, and IP54 sealing round out the vibe.',799.00, FLOOR(RAND()*50)+1,'assets/products/Nothingphone3.jpg'),
('ph7','iPhone 16','iPhone 16 keeps ProMotion fluidity and Spatial Video in a compact body. Ceramic Shield glass, aerospace aluminum, and IP68 water resistance tackle daily impacts. The A18 chip drives powerful on-device AI features for photos, music, and productivity.',799.00, FLOOR(RAND()*50)+1,'assets/products/iphone16.png'),
('ph8','Samsung A55','Galaxy A55 supplies AMOLED smoothness, 50MP clarity, and two-day endurance. Optical stabilization plus Nightography keep socials sharp even after sunset. Knox Vault, expandable storage, and stereo speakers make this midrange phone feel flagship.',449.00, FLOOR(RAND()*50)+1,'assets/products/SamsungA56.webp'),

('au1','Sony WH-1000XM5','WH-1000XM5 noise canceling cocoons studio-grade lows and airy highs. Eight microphones and auto NC optimizer adapt instantly to planes, trains, or voices. Multipoint Bluetooth, 30-hour battery, and soft vegan leather keep listening blissful.',399.00, FLOOR(RAND()*50)+1,'assets/products/SonyWF-1000XM5.jpg'),
('au2','AirPods Pro 3','AirPods Pro 3 personalize ANC, conversation awareness, and USB-C convenience. Custom H3 chip powers head-tracked spatial audio with richer bass and clarity. Sweat resistance plus MagSafe charging case make them the ultimate Apple everyday buds.',249.00, FLOOR(RAND()*50)+1,'assets/products/AirPodsPro3.png'),
('au3','Bose Quiet Comfort','Bose QuietComfort cushions deliver balanced tuning with effortless isolation. Adjustable modes let outside sound in only when you need awareness. Soft protein leather, 24-hour battery, and quick-charge perks simplify long commutes.',349.00, FLOOR(RAND()*50)+1,'assets/products/BoseQuietComfort.jpg'),
('au4','Sony WF-1000XM5','WF-1000XM5 earbuds shrink flagship ANC into a featherweight shell. Custom V2 processor, improved drivers, and bone-conduction sensors sharpen calls. Qi wireless charging and IPX4 resistance keep them ready for gym sessions or flights.',279.00, FLOOR(RAND()*50)+1,'assets/products/SonyWF-1000XM5.jpg'),
('au5','Sennheiser Momentum 4','Momentum 4 wireless offers 60-hour stamina with lush, detailed sound. Adaptive ANC learns your surroundings while smart pause handles every remove. Plush fabric headband and fold-flat hinges make luxury audio surprisingly travel friendly.',329.00, FLOOR(RAND()*50)+1,'assets/products/Momentum4.webp'),
('au6','Beats Studio Pro','Beats Studio Pro amps spatial audio and USB-C wired versatility for pros. Personalized ANC, Transparency mode, and lossless playback keep you locked in. On-ear controls plus 40-hour battery ensure the beat never stops between gigs.',299.00, FLOOR(RAND()*50)+1,'assets/products/BeatsStudioPro.jpeg'),
('au7','JBL Live Pro 2','JBL Live Pro 2 brings punchy bass, clear calls, and adaptive ANC to everyday buds. Six beamforming mics fight wind while Smart Ambient keeps surroundings audible. Qi wireless case, multi-point pairing, and 40 total hours make them commuteworthy.',149.00, FLOOR(RAND()*50)+1,'assets/products/JBLLivePro2.jpg'),
('au8','Anker Soundcore Q45','Soundcore Q45 mixes hybrid ANC, 50-hour battery, and cozy fit for travelers. HearID software personalizes EQ, and dual mics enhance call clarity anywhere. Foldable design plus a hard-shell case make packing them effortless.',129.00, FLOOR(RAND()*50)+1,'assets/products/AnkerSoundcoreQ45.jpg'),

('st1','Samsung T7 1TB SSD','Samsung T7 safeguards 1TB of fast USB-C storage in pocket-size metal. Hardware encryption and Dynamic Thermal Guard maintain performance under pressure. Plug it into PC, Mac, console, or camera to move massive files in seconds.',119.00, FLOOR(RAND()*50)+1,'assets/products/SamsungT7.jpg'),
('st2','SanDisk Extreme 1TB SSD','SanDisk Extreme endures drops while serving NVMe-class portable speeds. IP65 water and dust resistance plus carabiner loop make it adventure ready. USB-C + USB-A cables ensure compatibility with every workstation you meet.',129.00, FLOOR(RAND()*50)+1,'assets/products/SanDiskExtreme.jpeg'),
('st3','WD MyPassport 2TB HDD','WD MyPassport stores 2TB of reliable backups with password protection. Automatic backup software keeps photos and projects synced without thought. Slim styling and multiple colors let you carry extra space without extra bulk.',89.00, FLOOR(RAND()*50)+1,'assets/products/WDMyPassport.jpg'),
('st4','Lexar NM790 1TB NVMe','Lexar NM790 rockets PCIe 4.0 performance for creators and gamers alike. Low-profile graphene heat spreader keeps temps in check inside tight builds. 7400MB/s reads mean massive libraries load almost instantly.',99.00, FLOOR(RAND()*50)+1,'assets/products/LexarNM790.jpg'),
('st5','Kingston KC3000 2TB','Kingston KC3000 sustains desktop-class throughput with dynamic cooling layers. NVMe 1.4 compliance and advanced wear leveling ensure long-term reliability. Perfect for high-resolution editing or next-gen gaming rigs.',189.00, FLOOR(RAND()*50)+1,'assets/products/KingstonKC3000.jpg'),
('st6','Crucial X9 2TB SSD','Crucial X9 carries 2TB of USB-C speed inside a palm-sized anodized shell. Works across phones, Macs, PCs, and consoles without extra software fuss. Drop-tested and rubberized edges protect your portfolio on the go.',179.00, FLOOR(RAND()*50)+1,'assets/products/CrucialX9.jpg'),
('st7','Seagate Expansion 4TB','Seagate Expansion desktop drive delivers 4TB plug-and-play capacity. Just connect the included power and USB 3.0 cable to start backing up libraries. Whisper-quiet operation and polished housing blend into modern setups.',129.00, FLOOR(RAND()*50)+1,'assets/products/SeagateExpansion.jpg'),
('st8','Samsung EVO Plus 256GB','Samsung EVO Plus records 4K footage with UHS-I reliability and speed. Built-in proofing resists water, X-rays, magnets, and extreme temps. Adapter included so it slips between cameras, drones, and laptops seamlessly.',29.00, FLOOR(RAND()*50)+1,'assets/products/SamsungEvoPlus.jpg'),

('ac1','Apple Magic Mouse','Magic Mouse glides seamlessly with multi-touch gestures and instant pairing. Low-profile rechargeable design tracks smoothly on almost any surface. Integrates perfectly with macOS shortcuts to keep workflows fluid.',79.00, FLOOR(RAND()*50)+1,'assets/products/AppleMagicMouse.jpg'),
('ac2','Logitech MX Master 3S','MX Master 3S sculpts productivity using MagSpeed scroll and Flow control. Quiet-click buttons reduce distractions while Darkfield sensor tracks on glass. Pair with up to three devices and copy files across platforms effortlessly.',99.00, FLOOR(RAND()*50)+1,'assets/products/LogitechMXMaster.webp'),
('ac3','Keychron K2 V2','Keychron K2 V2 combines hot-swappable switches with compact wireless layout. Aluminum frame, RGB lighting, and Mac/Windows keycaps flex between rigs. 4000mAh battery keeps mechanical typing alive for weeks on a charge.',89.00, FLOOR(RAND()*50)+1,'assets/products/KeychronK2.jpg'),
('ac4','Anker 737 Charger','Anker 737 smart charger powers laptops at 140W with dual USB-C outputs. Intelligent Power Allocation keeps each port efficient whether you plug one or three devices. Foldable prongs and active temperature monitoring suit jet-set techies.',59.00, FLOOR(RAND()*50)+1,'assets/products/Anker737.jpeg'),
('ac5','UGREEN USB-C Hub 7-in-1','UGREEN 7-in-1 hub unlocks HDMI, USB-A, and media slots instantly. Aluminum shell dissipates heat while braided cable withstands daily travel. Plug in monitors, keyboards, and SD cards without searching for extra adapters.',49.00, FLOOR(RAND()*50)+1,'assets/products/UgreenUSB-CHub.jpg'),
('ac6','Apple Pencil (2nd Gen)','Apple Pencil 2 magnetically snaps, charges, and tracks notes precisely. Double-tap gestures switch tools instantly while ultra-low latency keeps strokes natural. Works with iPadOS hover preview for more accurate shading and markup.',119.00, FLOOR(RAND()*50)+1,'assets/products/ApplePencil.jpeg'),
('ac7','Samsung 45W PD Charger','Samsung 45W charger keeps Galaxy laptops and phones cool while fast-charging. PPS intelligence tailors voltage for safer, longer-lasting batteries. Compact brick includes a USB-C cable so you are road-ready out of the box.',39.00, FLOOR(RAND()*50)+1,'assets/products/Samsung45WPDCharger.avif'),
('ac8','Baseus Laptop Stand','Baseus stand lifts laptops with adjustable aluminum support and airflow. Six tilt levels improve posture, while silicon pads lock devices in place. Folds flat into a backpack sleeve yet handles 17-inch notebooks confidently.',29.00, FLOOR(RAND()*50)+1,'assets/products/BaseusLaptopStand.webp');
('ac9','Apple Watch Ultra 3','Titanium Apple Watch Ultra 3 stays our bestseller with its 3000‑nit Retina display, dual-frequency GPS, and S10 SiP that keeps fitness + safety features live all day. 36-hour battery, 100m water resistance, and the bundled Ocean Band make it the go-to pick for endurance athletes and travelers alike.',399.00,FLOOR(RAND()*50)+35,'assets/products/AppleWatch.png')


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
