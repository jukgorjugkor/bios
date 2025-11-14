<?php
require_once 'includes/config.php';

$scheduleId = isset($_GET['schedule']) ? intval($_GET['schedule']) : 0;

if (!$scheduleId) {
    header('Location: index.php');
    exit();
}

$schedule = getScheduleById($scheduleId);

if (!$schedule) {
    header('Location: index.php');
    exit();
}

$seats = getSeats($scheduleId);

// Group seats by row
$seatsByRow = [];
while ($seat = $seats->fetch_assoc()) {
    $row = $seat['seat_row'];
    if (!isset($seatsByRow[$row])) {
        $seatsByRow[$row] = [];
    }
    $seatsByRow[$row][] = $seat;
}

$siteName = getSetting('site_name', 'ISOLA SCREEN');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilihan Kursi - <?php echo $siteName; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .seat {
            width: 40px;
            height: 40px;
            margin: 4px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        .seat.available {
            background-color: #4b5563;
            border: 2px solid #6b7280;
        }
        .seat.available:hover {
            background-color: #059669;
            transform: scale(1.1);
        }
        .seat.selected {
            background-color: #dc2626;
            border: 2px solid #ef4444;
            transform: scale(1.1);
        }
        .seat.booked, .seat.reserved {
            background-color: #1f2937;
            border: 2px solid #374151;
            cursor: not-allowed;
            opacity: 0.5;
        }
        .screen {
            background: linear-gradient(to bottom, #374151 0%, #1f2937 100%);
            border-bottom: 4px solid #fbbf24;
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-gray-900 shadow-lg border-b border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="film-detail.php?id=<?php echo $schedule['film_id']; ?>" 
                   class="flex items-center space-x-2 hover:text-red-500 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                    <span class="font-semibold">Kembali</span>
                </a>
                <h1 class="text-lg font-bold">Pilih Kursi</h1>
                <div class="w-16"></div>
            </div>
        </div>
    </header>

    <!-- Film Info -->
    <section class="bg-gray-800 border-b border-gray-700">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center space-x-4">
                <?php 
                $coverPath = !empty($schedule['cover_image']) 
                    ? 'assets/images/films/' . $schedule['cover_image'] 
                    : 'https://via.placeholder.com/100x150/1f2937/ffffff?text=Film'; 
                ?>
                <img src="<?php echo $coverPath; ?>" 
                     alt="<?php echo htmlspecialchars($schedule['title']); ?>" 
                     class="w-16 h-24 object-cover rounded"
                     onerror="this.src='https://via.placeholder.com/100x150/1f2937/ffffff?text=Film'">
                
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1"><?php echo htmlspecialchars($schedule['title']); ?></h3>
                    <div class="text-sm text-gray-400">
                        <div><i class="fas fa-calendar mr-2"></i><?php echo formatDateIndo($schedule['show_date']); ?></div>
                        <div><i class="fas fa-clock mr-2"></i><?php echo formatTime($schedule['show_time']); ?></div>
                        <div><i class="fas fa-tag mr-2"></i><?php echo formatCurrency($schedule['price']); ?> / kursi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Theater Screen -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <!-- Screen -->
            <div class="screen text-center py-4 mb-8 rounded-lg shadow-lg mx-4 md:mx-auto md:max-w-2xl">
                <p class="text-gray-400 text-sm font-semibold tracking-widest">LAYAR</p>
            </div>

            <!-- Seats -->
            <div class="overflow-x-auto pb-4">
                <div class="inline-block min-w-full">
                    <?php foreach ($seatsByRow as $row => $rowSeats): ?>
                        <div class="flex items-center justify-center mb-2">
                            <div class="w-8 text-center font-bold text-gray-400 mr-2"><?php echo $row; ?></div>
                            <div class="flex flex-wrap justify-center">
                                <?php foreach ($rowSeats as $seat): ?>
                                    <div 
                                        class="seat <?php echo $seat['status']; ?>"
                                        data-seat="<?php echo $seat['seat_label']; ?>"
                                        data-status="<?php echo $seat['status']; ?>"
                                        onclick="toggleSeat(this)"
                                        title="Kursi <?php echo $seat['seat_label']; ?> - <?php echo ucfirst($seat['status']); ?>">
                                        <?php echo $seat['seat_number']; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap justify-center gap-6 mt-8 mb-4">
                <div class="flex items-center">
                    <div class="seat available"></div>
                    <span class="ml-2 text-sm text-gray-400">Tersedia</span>
                </div>
                <div class="flex items-center">
                    <div class="seat selected"></div>
                    <span class="ml-2 text-sm text-gray-400">Dipilih</span>
                </div>
                <div class="flex items-center">
                    <div class="seat booked"></div>
                    <span class="ml-2 text-sm text-gray-400">Terpesan</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Form -->
    <section class="py-6 bg-gray-800">
        <div class="container mx-auto px-4 max-w-lg">
            <h3 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-user text-red-500 mr-3"></i>Data Pemesan
            </h3>
            
            <form id="bookingForm" class="space-y-4">
                <input type="hidden" name="schedule_id" value="<?php echo $scheduleId; ?>">
                <input type="hidden" name="selected_seats" id="selectedSeatsInput">
                
                <div>
                    <label class="block text-sm font-semibold mb-2">Nama Lengkap *</label>
                    <input type="text" 
                           name="customer_name" 
                           id="customerName"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500"
                           placeholder="Masukkan nama lengkap"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold mb-2">Email *</label>
                    <input type="email" 
                           name="customer_email" 
                           id="customerEmail"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500"
                           placeholder="email@example.com"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold mb-2">No. HP/WhatsApp *</label>
                    <input type="tel" 
                           name="customer_phone" 
                           id="customerPhone"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500"
                           placeholder="08xxxxxxxxxx"
                           required>
                </div>
            </form>
        </div>
    </section>

    <!-- Bottom Bar - Summary & Payment -->
    <div class="fixed bottom-0 left-0 right-0 bg-gray-900 border-t-2 border-red-600 shadow-2xl z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-sm text-gray-400">Kursi dipilih</p>
                    <p class="font-bold text-lg" id="selectedSeatsDisplay">Belum ada kursi dipilih</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400">Total Harga</p>
                    <p class="font-bold text-2xl text-red-500" id="totalPrice"><?php echo formatCurrency(0); ?></p>
                </div>
            </div>
            
            <button 
                id="proceedBtn"
                onclick="proceedToPayment()"
                disabled
                class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-bold py-4 rounded-lg transition">
                <i class="fas fa-credit-card mr-2"></i>Lanjut ke Pembayaran
            </button>
        </div>
    </div>

    <!-- Spacer for bottom bar -->
    <div class="h-48"></div>

    <script>
        const selectedSeats = [];
        const pricePerSeat = <?php echo $schedule['price']; ?>;

        function toggleSeat(element) {
            const seatLabel = element.dataset.seat;
            const status = element.dataset.status;

            if (status !== 'available') {
                return;
            }

            if (element.classList.contains('selected')) {
                element.classList.remove('selected');
                element.classList.add('available');
                const index = selectedSeats.indexOf(seatLabel);
                if (index > -1) {
                    selectedSeats.splice(index, 1);
                }
            } else {
                element.classList.remove('available');
                element.classList.add('selected');
                selectedSeats.push(seatLabel);
            }

            updateSummary();
        }

        function updateSummary() {
            const totalSeats = selectedSeats.length;
            const totalPrice = totalSeats * pricePerSeat;

            if (totalSeats > 0) {
                document.getElementById('selectedSeatsDisplay').textContent = 
                    selectedSeats.join(', ') + ' (' + totalSeats + ' kursi)';
                document.getElementById('totalPrice').textContent = 
                    formatCurrency(totalPrice);
                document.getElementById('proceedBtn').disabled = false;
            } else {
                document.getElementById('selectedSeatsDisplay').textContent = 'Belum ada kursi dipilih';
                document.getElementById('totalPrice').textContent = formatCurrency(0);
                document.getElementById('proceedBtn').disabled = true;
            }

            document.getElementById('selectedSeatsInput').value = JSON.stringify(selectedSeats);
        }

        function formatCurrency(amount) {
            return '<?php echo CURRENCY_SYMBOL; ?> ' + 
                   amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function proceedToPayment() {
            const form = document.getElementById('bookingForm');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (selectedSeats.length === 0) {
                alert('Silakan pilih kursi terlebih dahulu');
                return;
            }

            const formData = new FormData(form);
            
            fetch('process-booking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'payment.php?booking=' + data.booking_code;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        }
    </script>
</body>
</html>
