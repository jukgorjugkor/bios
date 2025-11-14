<?php
require_once '../includes/config.php';
requireAdminLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = sanitize($_POST['site_name']);
    $studioName = sanitize($_POST['studio_name']);
    $totalSeats = intval($_POST['total_seats']);
    $seatRows = intval($_POST['seat_rows']);
    $seatPerRow = intval($_POST['seat_per_row']);
    $bookingTimeout = intval($_POST['booking_timeout']);
    $contactEmail = sanitize($_POST['contact_email']);
    $contactPhone = sanitize($_POST['contact_phone']);
    
    $midtransServerKey = sanitize($_POST['midtrans_server_key']);
    $midtransClientKey = sanitize($_POST['midtrans_client_key']);
    $midtransMerchantId = sanitize($_POST['midtrans_merchant_id']);
    $midtransEnvironment = sanitize($_POST['midtrans_environment']);
    
    $settings = [
        'site_name' => $siteName,
        'studio_name' => $studioName,
        'total_seats' => $totalSeats,
        'seat_rows' => $seatRows,
        'seat_per_row' => $seatPerRow,
        'booking_timeout' => $bookingTimeout,
        'contact_email' => $contactEmail,
        'contact_phone' => $contactPhone,
        'midtrans_server_key' => $midtransServerKey,
        'midtrans_client_key' => $midtransClientKey,
        'midtrans_merchant_id' => $midtransMerchantId,
        'midtrans_environment' => $midtransEnvironment
    ];
    
    $allSuccess = true;
    foreach ($settings as $key => $value) {
        if (!updateSetting($key, $value)) {
            $allSuccess = false;
            break;
        }
    }
    
    if ($allSuccess) {
        $success = 'Pengaturan berhasil disimpan';
    } else {
        $error = 'Gagal menyimpan pengaturan';
    }
}

$siteName = getSetting('site_name', 'ISOLA SCREEN');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin <?php echo $siteName; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white">
    
    <?php include 'includes/header.php'; ?>
    
    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="flex-1 p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2">Pengaturan</h1>
                <p class="text-gray-400">Konfigurasi sistem dan integrasi</p>
            </div>

            <?php if ($success): ?>
                <div class="bg-green-900 border border-green-700 text-white px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-900 border border-red-700 text-white px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="bg-gray-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-building text-red-500 mr-3"></i>Informasi Bioskop
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Nama Website</label>
                            <input type="text" name="site_name" value="<?php echo htmlspecialchars(getSetting('site_name')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2">Nama Studio</label>
                            <input type="text" name="studio_name" value="<?php echo htmlspecialchars(getSetting('studio_name')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2">Email Kontak</label>
                            <input type="email" name="contact_email" value="<?php echo htmlspecialchars(getSetting('contact_email')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2">Telepon Kontak</label>
                            <input type="text" name="contact_phone" value="<?php echo htmlspecialchars(getSetting('contact_phone')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-couch text-red-500 mr-3"></i>Konfigurasi Kursi
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Total Kursi</label>
                            <input type="number" name="total_seats" value="<?php echo htmlspecialchars(getSetting('total_seats')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2">Jumlah Baris</label>
                            <input type="number" name="seat_rows" value="<?php echo htmlspecialchars(getSetting('seat_rows')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" required>
                            <p class="text-xs text-gray-400 mt-1">Contoh: 5 untuk A, B, C, D, E</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2">Kursi per Baris</label>
                            <input type="number" name="seat_per_row" value="<?php echo htmlspecialchars(getSetting('seat_per_row')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-clock text-red-500 mr-3"></i>Konfigurasi Booking
                    </h2>
                    
                    <div class="max-w-md">
                        <label class="block text-sm font-semibold mb-2">Timeout Booking (menit)</label>
                        <input type="number" name="booking_timeout" value="<?php echo htmlspecialchars(getSetting('booking_timeout')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" required>
                        <p class="text-xs text-gray-400 mt-1">Waktu maksimal untuk menyelesaikan pembayaran</p>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-credit-card text-red-500 mr-3"></i>Konfigurasi Midtrans
                    </h2>
                    
                    <div class="bg-yellow-900 border border-yellow-700 text-yellow-100 px-4 py-3 rounded-lg mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Dapatkan API Key dari dashboard Midtrans Anda
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Server Key</label>
                            <input type="text" name="midtrans_server_key" value="<?php echo htmlspecialchars(getSetting('midtrans_server_key')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" placeholder="SB-Mid-server-...">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2">Client Key</label>
                            <input type="text" name="midtrans_client_key" value="<?php echo htmlspecialchars(getSetting('midtrans_client_key')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500" placeholder="SB-Mid-client-...">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Merchant ID</label>
                            <input type="text" name="midtrans_merchant_id" value="<?php echo htmlspecialchars(getSetting('midtrans_merchant_id')); ?>" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold mb-2">Environment</label>
                            <select name="midtrans_environment" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500">
                                <option value="sandbox" <?php echo getSetting('midtrans_environment') === 'sandbox' ? 'selected' : ''; ?>>Sandbox (Testing)</option>
                                <option value="production" <?php echo getSetting('midtrans_environment') === 'production' ? 'selected' : ''; ?>>Production</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                    </button>
                </div>
            </form>
        </main>
    </div>

</body>
</html>
