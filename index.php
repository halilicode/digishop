<?php
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>دیجی‌شاپ - فروشگاه اینترنتی</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
    <div class="header-top">
        <a href="index.php" class="logo">
            <i class="fas fa-store"></i>
            <span>دیجی</span>شاپ
        </a>
        
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="جستجوی محصول..." />
            <button onclick="searchProduct()"><i class="fas fa-search"></i></button>
        </div>
        
        <div class="header-icons">
            <a href="#" onclick="showPage('cart')" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cartCount">0</span>
            </a>
            <a href="#" onclick="handleUserIcon()" id="userIcon" title="حساب کاربری">
                <i class="fas fa-user"></i>
                <span id="userNameHeader" style="font-size:11px;display:block;margin-top:2px;font-weight:600;"></span>
            </a>
        </div>
    </div>
    
    <nav>
        <a href="#" data-page="home" class="active"><i class="fas fa-home"></i> خانه</a>
        <a href="#" data-page="products"><i class="fas fa-th-list"></i> محصولات</a>
        <a href="#" data-page="cart"><i class="fas fa-shopping-cart"></i> سبد خرید</a>
        <a href="#" data-page="about"><i class="fas fa-info-circle"></i> درباره ما</a>
        <a href="#" data-page="contact"><i class="fas fa-phone"></i> تماس با ما</a>
    </nav>
</header>

<!-- ============================================================
صفحه خانه
============================================================ -->
<div class="page active" id="page-home">
    <!-- اسلایدر -->
    <div class="slider">
        <div class="slider-slide active">
            <img src="images/slider1.jpg" alt="تخفیف ویژه" />
            <div class="slide-text">
                <h2>تخفیف ویژه تابستان</h2>
                <p>تا ۵۰٪ تخفیف برای محصولات منتخب</p>
            </div>
        </div>
        <div class="slider-slide">
            <img src="images/slider2.jpg" alt="ارسال رایگان" />
            <div class="slide-text">
                <h2>ارسال رایگان</h2>
                <p>برای خرید بالای ۲ میلیون تومان</p>
            </div>
        </div>
        <div class="slider-slide">
            <img src="images/slider3.jpg" alt="محصولات جدید" />
            <div class="slide-text">
                <h2>محصولات جدید</h2>
                <p>جدیدترین محصولات با بهترین قیمت</p>
            </div>
        </div>
        <button class="slider-btn prev" onclick="prevSlide()">❮</button>
        <button class="slider-btn next" onclick="nextSlide()">❯</button>
        <div class="slider-dots" id="sliderDots"></div>
    </div>
    
    <!-- دسته‌بندی‌ها -->
    <div class="categories">
        <a href="#" class="category-item" onclick="filterCategory('الکترونیک')">
            <i class="fas fa-laptop"></i>
            <span>الکترونیک</span>
        </a>
        <a href="#" class="category-item" onclick="filterCategory('پوشاک')">
            <i class="fas fa-tshirt"></i>
            <span>پوشاک</span>
        </a>
        <a href="#" class="category-item" onclick="filterCategory('کتاب')">
            <i class="fas fa-book"></i>
            <span>کتاب</span>
        </a>
        <a href="#" class="category-item" onclick="filterCategory('خانه')">
            <i class="fas fa-couch"></i>
            <span>خانه</span>
        </a>
        <a href="#" class="category-item" onclick="filterCategory('ورزشی')">
            <i class="fas fa-running"></i>
            <span>ورزشی</span>
        </a>
        <a href="#" class="category-item" onclick="filterCategory('اسباب‌بازی')">
            <i class="fas fa-gamepad"></i>
            <span>اسباب‌بازی</span>
        </a>
    </div>
    
    <!-- محصولات ویژه -->
    <div class="section-title">
        <i class="fas fa-star" style="color:#e63e3e;"></i>
        <span>محصولات ویژه</span>
    </div>
    <div class="products-grid" id="featuredProducts">
        <?php
        $stmt = $pdo->query("SELECT * FROM products WHERE featured = 1 LIMIT 8");
        while($product = $stmt->fetch()):
        ?>
        <div class="product-card" data-id="<?= $product['id'] ?>">
            <?php if($product['discount'] > 0): ?>
                <span class="badge"><?= $product['discount'] ?>%</span>
            <?php endif; ?>
            <div class="image">
                <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" />
                <div class="quick-view" onclick="showProduct(<?= $product['id'] ?>)">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <h3><?= $product['name'] ?></h3>
            <div class="rating">
                <?= str_repeat('⭐', floor($product['rating'])) ?> 
                <span class="rating-num"><?= $product['rating'] ?></span>
            </div>
            <div class="price">
                <?= number_format($product['price']) ?> تومان
                <?php if($product['old_price'] > 0): ?>
                    <span class="old-price"><?= number_format($product['old_price']) ?> تومان</span>
                <?php endif; ?>
            </div>
            <button class="add-btn" onclick="addToCart(<?= $product['id'] ?>, '<?= $product['name'] ?>', <?= $product['price'] ?>)">
                <i class="fas fa-plus"></i> افزودن به سبد
            </button>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- ============================================================
صفحه محصولات
============================================================ -->
<div class="page" id="page-products">
    <div class="section-title">
        <i class="fas fa-th-list"></i>
        <span>همه محصولات</span>
    </div>
    <div class="filter-bar">
        <button class="filter-btn active" onclick="filterProducts('all')">همه</button>
        <button class="filter-btn" onclick="filterProducts('الکترونیک')">الکترونیک</button>
        <button class="filter-btn" onclick="filterProducts('پوشاک')">پوشاک</button>
        <button class="filter-btn" onclick="filterProducts('کتاب')">کتاب</button>
        <button class="filter-btn" onclick="filterProducts('خانه')">خانه</button>
        <button class="filter-btn" onclick="filterProducts('ورزشی')">ورزشی</button>
        <button class="filter-btn" onclick="filterProducts('اسباب‌بازی')">اسباب‌بازی</button>
    </div>
    <div class="products-grid" id="allProducts">
        <?php
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
        while($product = $stmt->fetch()):
        ?>
        <div class="product-card" data-category="<?= $product['category'] ?>" data-id="<?= $product['id'] ?>">
            <?php if($product['discount'] > 0): ?>
                <span class="badge"><?= $product['discount'] ?>%</span>
            <?php endif; ?>
            <div class="image">
                <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" />
                <div class="quick-view" onclick="showProduct(<?= $product['id'] ?>)">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <h3><?= $product['name'] ?></h3>
            <div class="rating">
                <?= str_repeat('⭐', floor($product['rating'])) ?> 
                <span class="rating-num"><?= $product['rating'] ?></span>
            </div>
            <div class="price">
                <?= number_format($product['price']) ?> تومان
                <?php if($product['old_price'] > 0): ?>
                    <span class="old-price"><?= number_format($product['old_price']) ?> تومان</span>
                <?php endif; ?>
            </div>
            <button class="add-btn" onclick="addToCart(<?= $product['id'] ?>, '<?= $product['name'] ?>', <?= $product['price'] ?>)">
                <i class="fas fa-plus"></i> افزودن به سبد
            </button>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- ============================================================
صفحه سبد خرید
============================================================ -->
<div class="page" id="page-cart">
    <div class="section-title">
        <i class="fas fa-shopping-cart"></i>
        <span>سبد خرید</span>
    </div>
    <div id="cartContent">
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <h3>سبد خرید خالی است</h3>
            <p>محصولات مورد نظر خود را اضافه کنید</p>
            <a href="#" onclick="showPage('products')" class="btn-primary">مشاهده محصولات</a>
        </div>
    </div>
</div>

<!-- ============================================================
صفحه ورود / ثبت‌نام
============================================================ -->
<div class="page" id="page-login">
    <div class="section-title">
        <i class="fas fa-user-circle"></i>
        <span id="loginTitle">ورود به حساب کاربری</span>
    </div>

    <div class="auth-container">
        <!-- فرم ورود -->
        <div class="auth-card" id="loginForm">
            <h3>ورود به حساب</h3>
            <form onsubmit="handleLogin(event)">
                <input type="email" id="loginEmail" placeholder="ایمیل" required />
                <input type="password" id="loginPassword" placeholder="رمز عبور" required />
                <button type="submit" class="btn-primary" style="width:100%;">🔓 ورود</button>
            </form>
            <p class="auth-switch">
                حساب کاربری نداری؟
                <a href="#" onclick="switchAuth('register')">ثبت‌نام کن</a>
            </p>
        </div>

        <!-- فرم ثبت‌نام -->
        <div class="auth-card hidden" id="registerForm">
            <h3>ثبت‌نام</h3>
            <form onsubmit="handleRegister(event)">
                <input type="text" id="registerName" placeholder="نام و نام خانوادگی" required />
                <input type="email" id="registerEmail" placeholder="ایمیل" required />
                <input type="tel" id="registerPhone" placeholder="شماره تماس (اختیاری)" />
                <input type="password" id="registerPassword" placeholder="رمز عبور (حداقل ۶ کاراکتر)" required />
                <button type="submit" class="btn-primary" style="width:100%;">✨ ثبت‌نام</button>
            </form>
            <p class="auth-switch">
                قبلاً ثبت‌نام کرده‌ای؟
                <a href="#" onclick="switchAuth('login')">وارد شو</a>
            </p>
        </div>

        <!-- پروفایل کاربر -->
        <div class="auth-card hidden" id="profileCard">
            <div class="profile-header">
                <div class="profile-avatar" id="profileAvatar">👤</div>
                <h3 id="profileName">کاربر</h3>
                <p id="profileEmail">email@example.com</p>
            </div>
            <div class="profile-actions">
                <button class="action-btn" onclick="showPage('cart')">
                    🛒 سبد خرید
                </button>
                <button class="action-btn" onclick="showUserOrders()">
                    📦 سفارشات من
                </button>
                <button class="action-btn logout-btn" onclick="handleLogout()">
                    🚪 خروج از حساب
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
صفحه درباره ما
============================================================ -->
<div class="page" id="page-about">
    <div class="section-title">
        <i class="fas fa-info-circle"></i>
        <span>درباره ما</span>
    </div>
    <div class="about-container">
        <div class="about-content">
            <h2>🛍️ به دیجی‌شاپ خوش آمدید</h2>
            <p>
                دیجی‌شاپ یک فروشگاه اینترنتی مدرن است که با هدف ارائه بهترین 
                تجربه خرید به کاربران عزیز راه‌اندازی شده است. ما به دنبال 
                ارائه محصولات با کیفیت و قیمت مناسب هستیم.
            </p>
            <p>
                تیم ما متشکل از متخصصان حوزه تکنولوژی و بازاریابی است که 
                همواره در تلاشیم تا بهترین خدمات را به شما ارائه دهیم.
            </p>
            <div class="about-stats">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <h3 id="statUsers">0</h3>
                    <p>کاربران فعال</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-shopping-bag"></i>
                    <h3 id="statOrders">0</h3>
                    <p>سفارشات</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-star"></i>
                    <h3 id="statRating">0</h3>
                    <p>امتیاز کاربران</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-truck"></i>
                    <h3 id="statDeliveries">0</h3>
                    <p>ارسال‌های موفق</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
صفحه تماس با ما
============================================================ -->
<div class="page" id="page-contact">
    <div class="section-title">
        <i class="fas fa-phone"></i>
        <span>تماس با ما</span>
    </div>
    <div class="contact-container">
        <div class="contact-info">
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <h4>آدرس</h4>
                    <p><?= ADDRESS ?></p>
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <div>
                    <h4>تلفن</h4>
                    <p><?= PHONE ?></p>
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h4>ایمیل</h4>
                    <p><?= ADMIN_EMAIL ?></p>
                </div>
            </div>
            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <div>
                    <h4>ساعت کاری</h4>
                    <p>شنبه تا پنجشنبه ۹ الی ۱۸</p>
                </div>
            </div>
        </div>
        <div class="contact-form">
            <h3>ارسال پیام</h3>
            <form action="send-message.php" method="POST">
                <input type="text" name="name" placeholder="نام و نام خانوادگی" required />
                <input type="email" name="email" placeholder="ایمیل" required />
                <input type="text" name="subject" placeholder="موضوع" />
                <textarea name="message" placeholder="متن پیام..." rows="5" required></textarea>
                <button type="submit" class="btn-primary">ارسال پیام</button>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================
فوتر
============================================================ -->
<footer>
    <div class="footer-grid">
        <div class="footer-col">
            <h4>🛍️ دیجی‌شاپ</h4>
            <p>فروشگاه اینترنتی با بهترین قیمت‌ها</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-telegram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h4>دسترسی سریع</h4>
            <a href="#" onclick="showPage('home')">خانه</a>
            <a href="#" onclick="showPage('products')">محصولات</a>
            <a href="#" onclick="showPage('about')">درباره ما</a>
            <a href="#" onclick="showPage('contact')">تماس با ما</a>
        </div>
        <div class="footer-col">
            <h4>خدمات مشتریان</h4>
            <a href="#">سوالات متداول</a>
            <a href="#">راهنمای خرید</a>
            <a href="#">رویه بازگشت</a>
            <a href="#">قوانین سایت</a>
        </div>
        <div class="footer-col">
            <h4>اطلاعات تماس</h4>
            <p><i class="fas fa-phone"></i> <?= PHONE ?></p>
            <p><i class="fas fa-envelope"></i> <?= ADMIN_EMAIL ?></p>
            <p><i class="fas fa-map-marker-alt"></i> <?= ADDRESS ?></p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2026 دیجی‌شاپ - تمام حقوق محفوظ است</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js"></script>
</body>
</html>