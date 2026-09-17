
let cart = JSON.parse(localStorage.getItem('digishop_cart')) || [];

function saveCart() {
    localStorage.setItem('digishop_cart', JSON.stringify(cart));
    updateCartUI();
}

function updateCartUI() {
    const count = cart.reduce((sum, item) => sum + item.qty, 0);
    const badge = document.getElementById('cartCount');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'flex';
        }
    }
}

function addToCart(id, name, price) {
    const exist = cart.find(item => item.id === id);
    if (exist) {
        exist.qty++;
    } else {
        cart.push({ id: id, name: name, price: price, qty: 1 });
    }
    saveCart();
    renderCart();
    showToast(`✅ ${name} به سبد خرید اضافه شد!`);
    
    // انیمیشن سبد
    const icon = document.querySelector('.cart-icon');
    icon.style.transform = 'scale(1.3)';
    setTimeout(() => {
        icon.style.transform = 'scale(1)';
    }, 300);
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    saveCart();
    renderCart();
    showToast('🗑️ محصول از سبد حذف شد');
}

function changeQty(id, delta) {
    const item = cart.find(x => x.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        removeFromCart(id);
        return;
    }
    saveCart();
    renderCart();
}

function getTotal() {
    return cart.reduce((sum, item) => sum + item.price * item.qty, 0);
}

function getTotalItems() {
    return cart.reduce((sum, item) => sum + item.qty, 0);
}


function renderCart() {
    const container = document.getElementById('cartContent');
    if (!container) return;
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h3>سبد خرید خالی است</h3>
                <p>محصولات مورد نظر خود را اضافه کنید</p>
                <a href="#" onclick="showPage('products')" class="btn-primary">مشاهده محصولات</a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <div class="cart-table">
            <table style="width:100%;">
                <thead>
                    <tr>
                        <th>محصول</th>
                        <th>قیمت</th>
                        <th>تعداد</th>
                        <th>مجموع</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    ${cart.map(item => `
                        <tr>
                            <td><strong>${item.name}</strong></td>
                            <td>${item.price.toLocaleString()} تومان</td>
                            <td>
                                <div class="qty">
                                    <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
                                    <span style="font-weight:700;min-width:30px;text-align:center;">${item.qty}</span>
                                    <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
                                </div>
                            </td>
                            <td style="font-weight:700;color:#e63e3e;">${(item.price * item.qty).toLocaleString()} تومان</td>
                            <td>
                                <button class="remove-btn" onclick="removeFromCart(${item.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        <div class="cart-total">
            <div>
                <span style="font-size:16px;color:#888;">مجموع سبد خرید:</span>
                <span class="total-price">${getTotal().toLocaleString()} تومان</span>
                <span style="font-size:14px;color:#888;margin-right:10px;">(${getTotalItems()} آیتم)</span>
            </div>
            <button class="btn-primary" onclick="checkout()">
                <i class="fas fa-check"></i> نهایی کردن خرید
            </button>
        </div>
    `;
}


function checkout() {
    if (cart.length === 0) {
        showToast('سبد خرید خالی است!', 'error');
        return;
    }
    

    fetch('checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(cart)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('🎉 خرید شما با موفقیت ثبت شد!');
            cart = [];
            saveCart();
            renderCart();
            updateCartUI();
        } else {
            showToast('❌ خطا در ثبت سفارش', 'error');
        }
    })
    .catch(() => {
        showToast('❌ خطا در ارتباط با سرور', 'error');
    });
}


let currentSlide = 0;
const slides = document.querySelectorAll('.slider-slide');

function initSlider() {
    if (slides.length === 0) return;
    slides.forEach((s, i) => {
        s.classList.toggle('active', i === 0);
    });
    
    const dotsContainer = document.getElementById('sliderDots');
    if (dotsContainer) {
        dotsContainer.innerHTML = '';
        slides.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.onclick = () => goToSlide(i);
            if (i === 0) dot.classList.add('active');
            dotsContainer.appendChild(dot);
        });
    }
    
    setInterval(() => {
        nextSlide();
    }, 5000);
}

function goToSlide(index) {
    slides.forEach((s, i) => {
        s.classList.toggle('active', i === index);
    });
    const dots = document.querySelectorAll('#sliderDots span');
    dots.forEach((d, i) => {
        d.classList.toggle('active', i === index);
    });
    currentSlide = index;
}

function nextSlide() {
    goToSlide((currentSlide + 1) % slides.length);
}

function prevSlide() {
    goToSlide((currentSlide - 1 + slides.length) % slides.length);
}


function showPage(pageId) {
   
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    
   
    const page = document.getElementById('page-' + pageId);
    if (page) page.classList.add('active');

    document.querySelectorAll('nav a').forEach(a => a.classList.remove('active'));
    const navLink = document.querySelector(`nav a[data-page="${pageId}"]`);
    if (navLink) navLink.classList.add('active');
   
    if (pageId === 'cart') renderCart();
    
    // اگر صفحه درباره بود آمار رو نشون بده
    if (pageId === 'about') animateStats();
    
    // اسکرول به بالا
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================================
// فیلتر محصولات
// ============================================================
function filterProducts(category) {
    // بروزرسانی دکمه‌های فیلتر
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.textContent.trim() === category || 
            (category === 'all' && btn.textContent.trim() === 'همه'));
    });
    
    // فیلتر کردن محصولات
    document.querySelectorAll('#allProducts .product-card').forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.5s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterCategory(category) {
    showPage('products');
    filterProducts(category);
}

// ============================================================
// جستجو
// ============================================================
function searchProduct() {
    const input = document.getElementById('searchInput');
    const search = input.value.trim().toLowerCase();
    if (!search) {
        showToast('لطفاً عبارت جستجو را وارد کنید', 'error');
        return;
    }
    
    showPage('products');
    
    let found = false;
    document.querySelectorAll('#allProducts .product-card').forEach(card => {
        const name = card.querySelector('h3').textContent.toLowerCase();
        if (name.includes(search)) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.5s ease';
            found = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    if (!found) {
        showToast('❌ محصولی با این نام پیدا نشد', 'error');
    }
}

// جستجوی زنده
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProduct();
            }
        });
    }
});

// ============================================================
// آمار شمارنده
// ============================================================
function animateStats() {
    const targets = [1240, 856, 4.8, 320];
    const ids = ['statUsers', 'statOrders', 'statRating', 'statDeliveries'];
    
    targets.forEach((target, i) => {
        const el = document.getElementById(ids[i]);
        if (!el) return;
        let current = 0;
        const step = target / 50;
        const interval = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(interval);
            }
            el.textContent = target % 1 !== 0 ? current.toFixed(1) : Math.floor(current);
        }, 20);
    });
}

// ============================================================
// نمایش محصول (نمایش سریع)
// ============================================================
function showProduct(id) {
    showToast('🔍 در حال نمایش محصول...');
    // اینجا می‌توانید به صفحه جزئیات محصول بروید
    // window.location.href = 'product-detail.php?id=' + id;
}

// ============================================================
// توست (پیام)
// ============================================================
function showToast(message, type = 'success') {
    const old = document.querySelector('.toast');
    if (old) old.remove();
    
    const div = document.createElement('div');
    div.className = `toast ${type}`;
    div.textContent = message;
    document.body.appendChild(div);
    
    setTimeout(() => {
        div.classList.add('hide');
        setTimeout(() => div.remove(), 400);
    }, 3000);
}

// ============================================================
// مقداردهی اولیه
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // اسلایدر
    initSlider();
    
    // سبد خرید
    renderCart();
    updateCartUI();
    
    // منو
    document.querySelectorAll('nav a[data-page]').forEach(a => {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            showPage(this.dataset.page);
        });
    });
    
    // آمار در صفحه درباره
    if (document.getElementById('page-about').classList.contains('active')) {
        animateStats();
    }
    
    // دکمه‌های فیلتر
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const cat = this.textContent.trim();
            filterProducts(cat === 'همه' ? 'all' : cat);
        });
    });
    
    console.log('🛍️ دیجی‌شاپ با موفقیت بارگذاری شد!');
});

// ============================================================
// احراز هویت (ورود / ثبت‌نام)
// ============================================================

let currentUser = null;

// بررسی وضعیت ورود هنگام لود
async function checkAuth() {
    try {
        const res = await fetch('auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=check'
        });
        const data = await res.json();
        if (data.logged_in) {
            currentUser = data.user;
            updateUserUI();
        }
    } catch (e) {
        console.log('Auth check failed');
    }
}

// ثبت‌نام
async function handleRegister(e) {
    e.preventDefault();

    const name = document.getElementById('registerName').value.trim();
    const email = document.getElementById('registerEmail').value.trim();
    const phone = document.getElementById('registerPhone').value.trim();
    const password = document.getElementById('registerPassword').value;

    if (password.length < 6) {
        showToast('رمز عبور باید حداقل ۶ کاراکتر باشد', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'register');
    formData.append('name', name);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('password', password);

    try {
        const res = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            currentUser = data.user;
            showToast('🎉 ثبت‌نام با موفقیت انجام شد!');
            updateUserUI();
        } else {
            showToast('❌ ' + data.message, 'error');
        }
    } catch (e) {
        showToast('❌ خطا در ارتباط با سرور', 'error');
    }
}

// ورود
async function handleLogin(e) {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

    const formData = new FormData();
    formData.append('action', 'login');
    formData.append('email', email);
    formData.append('password', password);

    try {
        const res = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            currentUser = data.user;
            showToast('🎉 خوش آمدید ' + data.user.name + '!');
            updateUserUI();
        } else {
            showToast('❌ ' + data.message, 'error');
        }
    } catch (e) {
        showToast('❌ خطا در ارتباط با سرور', 'error');
    }
}

// خروج
async function handleLogout() {
    const formData = new FormData();
    formData.append('action', 'logout');

    try {
        await fetch('auth.php', { method: 'POST', body: formData });
        currentUser = null;
        showToast('👋 با موفقیت خارج شدید');
        updateUserUI();
        showPage('home');
    } catch (e) {
        showToast('❌ خطا', 'error');
    }
}

// بروزرسانی UI
function updateUserUI() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const profileCard = document.getElementById('profileCard');

    if (!loginForm) return;

    if (currentUser) {
        loginForm.classList.add('hidden');
        registerForm.classList.add('hidden');
        profileCard.classList.remove('hidden');

        document.getElementById('profileName').textContent = currentUser.name;
        document.getElementById('profileEmail').textContent = currentUser.email;
        document.getElementById('profileAvatar').textContent = currentUser.name.charAt(0).toUpperCase();
    } else {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        profileCard.classList.add('hidden');
    }
}

// سوییچ بین فرم‌ها
function switchAuth(mode) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const title = document.getElementById('loginTitle');

    if (mode === 'register') {
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        title.textContent = 'ثبت‌نام در دیجی‌شاپ';
    } else {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        title.textContent = 'ورود به حساب کاربری';
    }
}

// نمایش سفارشات (اختیاری)
function showUserOrders() {
    showToast('📦 هنوز سفارشی ثبت نکرده‌اید');
}

// لود اولیه
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
});

function handleUserIcon() {
    if (currentUser) {
        // اگه لاگین هست، برو به پروفایل
        showPage('login');
        updateUserUI();
    } else {
        // اگه لاگین نیست، برو به فرم ورود
        showPage('login');
        switchAuth('login');
    }
}

// ============================================================
// منوی موبایل (Drawer)
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const drawer = document.getElementById('drawer');
    const drawerOverlay = document.getElementById('drawerOverlay');
    const drawerClose = document.getElementById('drawerClose');

    function openDrawer() {
        drawer.classList.add('active');
        drawerOverlay.classList.add('active');
        document.body.classList.add('no-scroll');
    }

    function closeDrawer() {
        drawer.classList.remove('active');
        drawerOverlay.classList.remove('active');
        document.body.classList.remove('no-scroll');
    }

    if (menuToggle) menuToggle.addEventListener('click', openDrawer);
    if (drawerClose) drawerClose.addEventListener('click', closeDrawer);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeDrawer);

    // لینک‌های داخل drawer
    document.querySelectorAll('.drawer-nav a[data-page]').forEach(a => {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.dataset.page;
            closeDrawer();
            setTimeout(() => showPage(page), 200);

            // بروزرسانی active
            document.querySelectorAll('.drawer-nav a').forEach(x => x.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // بستن با کلید Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDrawer();
    });
});

// تابع کمکی برای بستن drawer
function closeDrawer() {
    document.getElementById('drawer')?.classList.remove('active');
    document.getElementById('drawerOverlay')?.classList.remove('active');
    document.body.classList.remove('no-scroll');
}
