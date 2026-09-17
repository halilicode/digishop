-- ============================================
-- دیتابیس فروشگاه دیجی‌شاپ
-- ============================================

CREATE DATABASE IF NOT EXISTS shop_db;
USE shop_db;

-- ===== جدول محصولات =====
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    old_price INT DEFAULT 0,
    discount INT DEFAULT 0,
    image VARCHAR(255) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 4.0,
    featured BOOLEAN DEFAULT 0,
    stock INT DEFAULT 10,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== جدول کاربران =====
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== جدول سفارشات =====
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price INT NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ===== جدول آیتم‌های سفارش =====
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ===== محصولات نمونه =====
INSERT INTO products (name, category, price, old_price, discount, image, rating, featured, description) VALUES
('هدفون بی‌سیم سونی WH-1000XM5', 'الکترونیک', 12000000, 15000000, 20, 'sony-headphone.jpg', 4.8, 1, 'بهترین هدفون حذف نویز بازار'),
('ساعت هوشمند اپل سری 9', 'الکترونیک', 25000000, 0, 0, 'apple-watch.jpg', 4.9, 1, 'ساعت هوشمند اپل با صفحه نمایش همیشه روشن'),
('لپ‌تاپ ایسوس ROG Zephyrus G14', 'الکترونیک', 45000000, 52000000, 13, 'asus-rog.jpg', 4.7, 1, 'لپ‌تاپ گیمینگ با پردازنده قدرتمند'),
('گوشی سامسونگ گلکسی S24 Ultra', 'الکترونیک', 38000000, 42000000, 10, 'samsung-s24.jpg', 4.9, 1, 'پرچمدار سامسونگ با دوربین ۲۰۰ مگاپیکسلی'),
('تیشرت آدیداس اصل', 'پوشاک', 850000, 1200000, 29, 'adidas-tshirt.jpg', 4.3, 1, 'تیشرت ورزشی با کیفیت بالا'),
('کت زنانه زارا', 'پوشاک', 2500000, 3200000, 22, 'zara-coat.jpg', 4.6, 0, 'کت شیک و مدرن زنانه'),
('کتاب برنامه‌نویسی پایتون', 'کتاب', 420000, 0, 0, 'python-book.jpg', 4.9, 1, 'کتاب جامع آموزش پایتون'),
('رمان خانه ابری', 'کتاب', 280000, 350000, 20, 'cloud-house.jpg', 4.5, 0, 'رمان پرفروش ایرانی'),
('مبل شیک ایلیا', 'خانه', 8500000, 10000000, 15, 'sofa.jpg', 4.4, 1, 'مبل شیک و راحت با کیفیت بالا'),
('یخچال ساید بای ساید ال جی', 'خانه', 35000000, 40000000, 12, 'lg-fridge.jpg', 4.7, 1, 'یخچال مدرن با تکنولوژی جدید'),
('دوچرخه تریکس', 'ورزشی', 12000000, 15000000, 20, 'trix-bike.jpg', 4.3, 0, 'دوچرخه کوهستان حرفه‌ای'),
('پازل ۲۰۰۰ تکه', 'اسباب‌بازی', 450000, 0, 0, 'puzzle.jpg', 4.2, 0, 'پازل چالش‌برانگیز ۲۰۰۰ تکه');