<?php
require_once 'includes/config.php';
require_once 'includes/midtrans-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$bookingCode = isset($input['booking_code']) ? sanitize($input['booking_code']) : '';
$orderId = isset($input['order_id']) ? sanitize($input['order_id']) : '';

if (empty($bookingCode) || empty($orderId)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit();
}

$booking = getBookingByCode($bookingCode);

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking tidak ditemukan']);
    exit();
}

if ($booking['payment_status'] === 'paid') {
    echo json_encode(['success' => false, 'message' => 'Booking sudah dibayar']);
    exit();
}

try {
    $transactionDetails = [
        'order_id' => $orderId,
        'gross_amount' => $booking['total_price'],
    ];
    
    $itemDetails = [
        [
            'id' => 'TICKET-' . $booking['id'],
            'price' => $booking['total_price'] / $booking['total_seats'],
            'quantity' => $booking['total_seats'],
            'name' => 'Tiket ' . $booking['title']
        ]
    ];
    
    $customerDetails = [
        'first_name' => $booking['customer_name'],
        'email' => $booking['customer_email'],
        'phone' => $booking['customer_phone']
    ];
    
    $transactionData = [
        'transaction_details' => $transactionDetails,
        'item_details' => $itemDetails,
        'customer_details' => $customerDetails,
        'enabled_payments' => ['credit_card', 'mandiri_clickpay', 'cimb_clicks', 'bca_klikbca', 'bca_klikpay', 'bri_epay', 'echannel', 'permata_va', 'bca_va', 'bni_va', 'other_va', 'gopay', 'shopeepay', 'indomaret', 'alfamart', 'akulaku']
    ];
    
    $snapToken = \Midtrans\Snap::getSnapToken($transactionData);
    
    $stmt = $conn->prepare("INSERT INTO transactions (booking_id, order_id, gross_amount, transaction_status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("isd", $booking['id'], $orderId, $booking['total_price']);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'snap_token' => $snapToken,
        'order_id' => $orderId
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
