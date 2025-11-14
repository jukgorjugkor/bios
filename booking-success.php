<?php
require_once 'includes/config.php';

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

$seats = json_decode($booking['seats'], true);
$siteName = getSetting('site_name', 'ISOLA SCREEN');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil - <?php echo $siteName; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            
            <!-- Success Icon -->
            <div class="text-center mb-8">
                <div class="inline-block bg-green-600 rounded-full p-6 mb-4 animate-bounce">
                    <i class="fas fa-check text-6xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">Pembayaran Berhasil!</h1>
                <p class="text-gray-400">Terima kasih telah memesan tiket di <?php echo $siteName; ?></p>
            </div>

            <!-- Ticket -->
            <div class="bg-gray-800 rounded-xl overflow-hidden shadow-2xl mb-6">
                <div class="bg-gradient-to-r from-red-600 to-purple-600 p-6 text-center">
                    <h2 class="text-2xl font-bold mb-2">E-TICKET</h2>
                    <p class="text-sm opacity-90"><?php echo $siteName; ?></p>
                </div>
                
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="inline-block bg-gray-700 px-6 py-3 rounded-lg">
                            <p class="text-sm text-gray-400 mb-1">Kode Booking</p>
                            <p class="text-3xl font-bold text-red-400 tracking-wider"><?php echo $booking['booking_code']; ?></p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-700 pt-6">
                        <h3 class="font-bold text-xl mb-4"><?php echo htmlspecialchars($booking['title']); ?></h3>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-400 mb-1"><i class="fas fa-calendar w-5"></i>Tanggal</p>
                                <p class="font-semibold"><?php echo formatDateIndo($booking['show_date']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400 mb-1"><i class="fas fa-clock w-5"></i>Waktu</p>
                                <p class="font-semibold"><?php echo formatTime($booking['show_time']); ?></p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-400 mb-1"><i class="fas fa-user w-5"></i>Nama Pemesan</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($booking['customer_name']); ?></p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-400 mb-1"><i class="fas fa-couch w-5"></i>Kursi</p>
                            <p class="font-semibold"><?php echo implode(', ', $seats); ?></p>
                        </div>
                        
                        <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                            <span class="text-lg font-semibold">Total Dibayar</span>
                            <span class="text-2xl font-bold text-green-400"><?php echo formatCurrency($booking['total_price']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-700 p-4 text-center border-t-2 border-dashed border-gray-600">
                    <p class="text-sm text-gray-400">
                        <i class="fas fa-info-circle mr-2"></i>
                        Tunjukkan kode booking ini saat memasuki studio
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="space-y-3">
                <button 
                    onclick="window.print()"
                    class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition">
                    <i class="fas fa-print mr-2"></i>Cetak Tiket
                </button>
                
                <a href="index.php" 
                   class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition text-center">
                    <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .max-w-2xl, .max-w-2xl * {
                visibility: visible;
            }
            .max-w-2xl {
                position: absolute;
                left: 0;
                top: 0;
            }
            button, a {
                display: none !important;
            }
        }
    </style>
</body>
</html>
