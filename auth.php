<?php
// auth.php - پردازش ثبت‌نام و ورود
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// ============================================
// ثبت‌نام
// ============================================
if ($action === 'register') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    // اعتبارسنجی
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'همه فیلدهای اجباری را پر کنید']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'ایمیل معتبر نیست']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'رمز عبور باید حداقل ۶ کاراکتر باشد']);
        exit;
    }

    try {
        // بررسی تکراری نبودن ایمیل
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'این ایمیل قبلاً ثبت شده است']);
            exit;
        }

        // هش کردن رمز
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // درج کاربر جدید
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $phone]);
        $userId = $pdo->lastInsertId();

        // ورود خودکار
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => 'ثبت‌نام با موفقیت انجام شد',
            'user' => [
                'id' => $userId,
                'name' => $name,
                'email' => $email
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// ورود
// ============================================
if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'ایمیل و رمز عبور را وارد کنید']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'ایمیل یا رمز عبور اشتباه است']);
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'ایمیل یا رمز عبور اشتباه است']);
            exit;
        }

        // ورود
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        echo json_encode([
            'success' => true,
            'message' => 'ورود با موفقیت انجام شد',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ]
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// بررسی وضعیت ورود
// ============================================
if ($action === 'check') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => true,
            'logged_in' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email']
            ]
        ]);
    } else {
        echo json_encode(['success' => true, 'logged_in' => false]);
    }
    exit;
}

// ============================================
// خروج
// ============================================
if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'خارج شدید']);
    exit;
}

echo json_encode(['success' => false, 'message' => 'عملیات نامعتبر']);