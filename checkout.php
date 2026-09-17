<?php
require_once 'db.php';
header('Content-Type: application/json');

// دریافت داده‌های ارسالی
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data)) {
    echo json_encode(['success' => false, 'message' => 'سبد خرید خالی است']);
    exit;
}

try {
    // شروع تراکنش
    $pdo->beginTransaction();
    
    // محاسبه قیمت کل
    $total = 0;
    foreach ($data as $item) {
        $total += $item['price'] * $item['qty'];
    }
    
    // ذخیره سفارش (بدون کاربر)
    $stmt = $pdo->prepare("INSERT INTO orders (total_price, status, address) VALUES (?, 'pending', ?)");
    $stmt->execute([$total, 'آدرس نمونه']);
    $orderId = $pdo->lastInsertId();
    
    // ذخیره آیتم‌های سفارش
    foreach ($data as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$orderId, $item['id'], $item['qty'], $item['price']]);
        
        // کاهش موجودی
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$item['qty'], $item['id']]);
    }
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'order_id' => $orderId]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>