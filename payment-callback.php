<?php
require_once 'includes/config.php';
require_once 'includes/midtrans-config.php';

$orderId = isset($_GET['order_id']) ? sanitize($_GET['order_id']) : '';
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

if (!empty($orderId)) {
    try {
        $statusResponse = \Midtrans\Transaction::status($orderId);
        
        $transactionStatus = $statusResponse->transaction_status;
        $fraudStatus = isset($statusResponse->fraud_status) ? $statusResponse->fraud_status : '';
        
        $stmt = $conn->prepare("SELECT booking_id FROM transactions WHERE order_id = ?");
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction = $result->fetch_assoc();
        
        if ($transaction) {
            $bookingId = $transaction['booking_id'];
            
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $paymentStatus = 'paid';
                    $bookingStatus = 'confirmed';
                    $seatStatus = 'booked';
                } else {
                    $paymentStatus = 'failed';
                    $bookingStatus = 'cancelled';
                    $seatStatus = 'available';
                }
            } elseif ($transactionStatus == 'settlement') {
                $paymentStatus = 'paid';
                $bookingStatus = 'confirmed';
                $seatStatus = 'booked';
            } elseif ($transactionStatus == 'pending') {
                $paymentStatus = 'unpaid';
                $bookingStatus = 'pending';
                $seatStatus = 'reserved';
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $paymentStatus = 'failed';
                $bookingStatus = 'cancelled';
                $seatStatus = 'available';
            }
            
            $stmt = $conn->prepare("UPDATE transactions SET transaction_id = ?, transaction_status = ?, fraud_status = ?, payment_type = ?, transaction_time = ?, settlement_time = ?, status_code = ?, midtrans_response = ? WHERE order_id = ?");
            $transactionId = $statusResponse->transaction_id;
            $paymentType = isset($statusResponse->payment_type) ? $statusResponse->payment_type : '';
            $transactionTime = isset($statusResponse->transaction_time) ? $statusResponse->transaction_time : null;
            $settlementTime = isset($statusResponse->settlement_time) ? $statusResponse->settlement_time : null;
            $statusCode = isset($statusResponse->status_code) ? $statusResponse->status_code : '';
            $midtransResponse = json_encode($statusResponse);
            $stmt->bind_param("sssssssss", $transactionId, $transactionStatus, $fraudStatus, $paymentType, $transactionTime, $settlementTime, $statusCode, $midtransResponse, $orderId);
            $stmt->execute();
            
            $stmt = $conn->prepare("UPDATE bookings SET payment_status = ?, booking_status = ? WHERE id = ?");
            $stmt->bind_param("ssi", $paymentStatus, $bookingStatus, $bookingId);
            $stmt->execute();
            
            $stmt = $conn->prepare("SELECT schedule_id, seats FROM bookings WHERE id = ?");
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $result = $stmt->get_result();
            $booking = $result->fetch_assoc();
            
            if ($booking) {
                $seats = json_decode($booking['seats'], true);
                updateSeatStatus($booking['schedule_id'], $seats, $seatStatus);
                updateAvailableSeats($booking['schedule_id']);
            }
            
            if ($paymentStatus === 'paid') {
                $stmt = $conn->prepare("SELECT booking_code FROM bookings WHERE id = ?");
                $stmt->bind_param("i", $bookingId);
                $stmt->execute();
                $result = $stmt->get_result();
                $bookingData = $result->fetch_assoc();
                
                header('Location: booking-success.php?booking=' . $bookingData['booking_code']);
                exit();
            }
        }
        
    } catch (Exception $e) {
        error_log('Midtrans Error: ' . $e->getMessage());
    }
}

header('Location: index.php');
exit();
?>
