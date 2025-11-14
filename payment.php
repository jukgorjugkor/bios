<?php
require_once 'includes/config.php';
require_once 'includes/midtrans-config.php';

$bookingCode = isset($_GET['booking']) ? sanitize($_GET['booking']) : '';

if (empty($bookingCode)) {
    header('Location: index.php');
    exit();
}

$booking = getBookingByCode($bookingCode);

if (!$booking) {
    header('Location: index.php');
    exit();
}

if ($booking['payment_status'] === 'paid') {
    header('Location: booking-success.php?booking=' . $bookingCode);
    exit();
}

$siteName = getSetting('site_name', 'ISOLA SCREEN');
$midtransClientKey = getSetting('midtrans_client_key', '');
$midtransEnvironment = getSetting('midtrans_environment', 'sandbox');

if (empty($midtransClientKey)) {
    $paymentError = 'Pembayaran belum dikonfigurasi. Silakan hubungi administrator.';
}

$orderId = generateOrderId();
$seats = json_decode($booking['seats'], true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - <?php echo $siteName; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php if (!empty($midtransClientKey)): ?>
    <script src="https://app.<?php echo $midtransEnvironment === 'production' ? '' : 'sandbox.'; ?>midtrans.com/snap/snap.js" data-client-key="<?php echo $midtransClientKey; ?>"></script>
    <?php endif; ?>
</head>
<body class="bg-gray-900 text-white">
    
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-gray-900 shadow-lg border-b border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <h1 class="text-xl font-bold text-center"><?php echo $siteName; ?></h1>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        
        <?php if (isset($paymentError)): ?>
            <div class="bg-red-900 border border-red-700 rounded-lg p-6 mb-6">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                    <h3 class="text-xl font-bold">Error</h3>
                </div>
                <p><?php echo $paymentError; ?></p>
            </div>
        <?php endif; ?>

        <!-- Booking Summary -->
        <div class="bg-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-2xl font-bold mb-6 flex items-center">
                <i class="fas fa-ticket-alt text-red-500 mr-3"></i>Detail Pemesanan
            </h2>
            
            <div class="flex mb-6">
                <?php 
                $coverPath = !empty($booking['cover_image']) 
                    ? 'assets/images/films/' . $booking['cover_image'] 
                    : 'https://via.placeholder.com/120x180/1f2937/ffffff?text=Film'; 
                ?>
                <img src="<?php echo $coverPath; ?>" 
                     alt="<?php echo htmlspecialchars($booking['title']); ?>" 
                     class="w-24 h-36 object-cover rounded-lg mr-4"
                     onerror="this.src='https://via.placeholder.com/120x180/1f2937/ffffff?text=Film'">
                
                <div class="flex-1">
                    <h3 class="font-bold text-xl mb-2"><?php echo htmlspecialchars($booking['title']); ?></h3>
                    <div class="text-sm text-gray-400 space-y-1">
                        <p><i class="fas fa-calendar w-5"></i><?php echo formatDateIndo($booking['show_date']); ?></p>
                        <p><i class="fas fa-clock w-5"></i><?php echo formatTime($booking['show_time']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-4 mb-4">
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <p class="text-sm text-gray-400">Kode Booking</p>
                        <p class="font-bold text-red-400"><?php echo $booking['booking_code']; ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Status</p>
                        <p class="font-bold text-yellow-400">Menunggu Pembayaran</p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="text-sm text-gray-400">Nama Pemesan</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($booking['customer_name']); ?></p>
                </div>
                
                <div class="mb-3">
                    <p class="text-sm text-gray-400">Kursi</p>
                    <p class="font-semibold"><?php echo implode(', ', $seats); ?> (<?php echo $booking['total_seats']; ?> kursi)</p>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                <span class="text-lg font-semibold">Total Pembayaran</span>
                <span class="text-2xl font-bold text-red-500"><?php echo formatCurrency($booking['total_price']); ?></span>
            </div>
        </div>

        <!-- Countdown Timer -->
        <div class="bg-yellow-900 border border-yellow-700 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-hourglass-half text-2xl text-yellow-400 mr-3"></i>
                    <div>
                        <p class="text-sm">Selesaikan pembayaran dalam</p>
                        <p class="text-2xl font-bold text-yellow-400" id="countdown">--:--</p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($paymentError)): ?>
        <!-- Payment Button -->
        <button 
            id="payButton"
            onclick="payNow()"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-lg transition text-lg">
            <i class="fas fa-credit-card mr-2"></i>Bayar Sekarang
        </button>
        
        <p class="text-center text-sm text-gray-400 mt-4">
            Powered by Midtrans - Pembayaran Aman & Terpercaya
        </p>
        <?php endif; ?>
    </div>

    <script>
        const expiredAt = new Date('<?php echo $booking['expired_at']; ?>').getTime();
        
        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiredAt - now;
            
            if (distance < 0) {
                clearInterval(countdownInterval);
                document.getElementById('countdown').textContent = 'EXPIRED';
                document.getElementById('payButton').disabled = true;
                document.getElementById('payButton').classList.add('bg-gray-600', 'cursor-not-allowed');
                alert('Waktu pemesanan telah habis. Silakan pesan ulang.');
                window.location.href = 'index.php';
            } else {
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById('countdown').textContent = 
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }
        }, 1000);

        function payNow() {
            const payButton = document.getElementById('payButton');
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            
            fetch('create-payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    booking_code: '<?php echo $booking['booking_code']; ?>',
                    order_id: '<?php echo $orderId; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            window.location.href = 'payment-callback.php?order_id=' + result.order_id + '&status=success';
                        },
                        onPending: function(result) {
                            window.location.href = 'payment-callback.php?order_id=' + result.order_id + '&status=pending';
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal. Silakan coba lagi.');
                            payButton.disabled = false;
                            payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Bayar Sekarang';
                        },
                        onClose: function() {
                            payButton.disabled = false;
                            payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Bayar Sekarang';
                        }
                    });
                } else {
                    alert('Error: ' + (data.message || 'Gagal membuat pembayaran'));
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Bayar Sekarang';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Bayar Sekarang';
            });
        }
    </script>
</body>
</html>
