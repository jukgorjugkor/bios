<?php
require_once 'includes/config.php';

$filmId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$filmId) {
    header('Location: index.php');
    exit();
}

$film = getFilmById($filmId);

if (!$film) {
    header('Location: index.php');
    exit();
}

// Get schedules for this film
$schedulesResult = getFilmSchedules($filmId);

// Group schedules by date
$schedulesByDate = [];
while ($schedule = $schedulesResult->fetch_assoc()) {
    $date = $schedule['show_date'];
    if (!isset($schedulesByDate[$date])) {
        $schedulesByDate[$date] = [];
    }
    $schedulesByDate[$date][] = $schedule;
}

$siteName = getSetting('site_name', 'ISOLA SCREEN');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($film['title']); ?> - <?php echo $siteName; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .film-cover-bg {
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .film-cover-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(17, 24, 39, 0.7) 0%, rgba(17, 24, 39, 0.95) 70%, rgb(17, 24, 39) 100%);
        }
        .schedule-btn {
            transition: all 0.3s ease;
        }
        .schedule-btn:hover:not(:disabled) {
            transform: scale(1.05);
        }
        .schedule-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-gray-900 shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="index.php" class="flex items-center space-x-2 hover:text-red-500 transition">
                    <i class="fas fa-arrow-left text-xl"></i>
                    <span class="font-semibold">Kembali</span>
                </a>
                <h1 class="text-lg font-bold truncate ml-4"><?php echo $siteName; ?></h1>
            </div>
        </div>
    </header>

    <!-- Film Cover Hero -->
    <?php 
    $coverPath = !empty($film['cover_image']) 
        ? 'assets/images/films/' . $film['cover_image'] 
        : 'https://via.placeholder.com/1920x1080/1f2937/ffffff?text=' . urlencode($film['title']); 
    ?>
    <section class="film-cover-bg" style="background-image: url('<?php echo $coverPath; ?>');">
        <div class="relative z-10 container mx-auto px-4 py-12">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-shrink-0">
                    <img src="<?php echo $coverPath; ?>" 
                         alt="<?php echo htmlspecialchars($film['title']); ?>" 
                         class="w-48 md:w-64 rounded-xl shadow-2xl mx-auto"
                         onerror="this.src='https://via.placeholder.com/300x450/1f2937/ffffff?text=No+Image'">
                </div>
                
                <div class="flex-1">
                    <div class="inline-block bg-red-600 text-white text-sm font-bold px-3 py-1 rounded mb-3">
                        <?php echo htmlspecialchars($film['rating']); ?>
                    </div>
                    
                    <h2 class="text-3xl md:text-4xl font-bold mb-4"><?php echo htmlspecialchars($film['title']); ?></h2>
                    
                    <div class="flex flex-wrap gap-4 mb-4 text-gray-300">
                        <div class="flex items-center">
                            <i class="fas fa-clock mr-2 text-red-500"></i>
                            <span><?php echo $film['duration']; ?> menit</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-theater-masks mr-2 text-red-500"></i>
                            <span><?php echo htmlspecialchars($film['genre']); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar mr-2 text-red-500"></i>
                            <span><?php echo date('Y', strtotime($film['release_date'])); ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($film['director'])): ?>
                    <div class="mb-3">
                        <span class="text-gray-400">Sutradara:</span>
                        <span class="ml-2 text-white"><?php echo htmlspecialchars($film['director']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($film['cast'])): ?>
                    <div class="mb-4">
                        <span class="text-gray-400">Pemeran:</span>
                        <span class="ml-2 text-white"><?php echo htmlspecialchars($film['cast']); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <p class="text-gray-300 leading-relaxed mb-4">
                        <?php echo nl2br(htmlspecialchars($film['description'])); ?>
                    </p>
                    
                    <?php if (!empty($film['trailer_url'])): ?>
                    <a href="<?php echo htmlspecialchars($film['trailer_url']); ?>" 
                       target="_blank" 
                       class="inline-block bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition">
                        <i class="fas fa-play mr-2"></i>Tonton Trailer
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedules Section -->
    <section class="py-8 bg-gray-900">
        <div class="container mx-auto px-4">
            <h3 class="text-2xl font-bold mb-6 flex items-center">
                <i class="fas fa-calendar-alt text-red-500 mr-3"></i>Pilih Jadwal Tayang
            </h3>

            <?php if (!empty($schedulesByDate)): ?>
                <?php foreach ($schedulesByDate as $date => $schedules): ?>
                    <div class="mb-8 bg-gray-800 rounded-xl p-6">
                        <h4 class="text-xl font-bold mb-4 text-red-400">
                            <?php echo formatDateIndo($date); ?>
                        </h4>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            <?php foreach ($schedules as $schedule): ?>
                                <?php 
                                $isFull = $schedule['available_seats'] <= 0;
                                $isActive = $schedule['status'] === 'active';
                                ?>
                                <button 
                                    onclick="selectSchedule(<?php echo $schedule['id']; ?>)"
                                    class="schedule-btn bg-gray-700 hover:bg-red-600 p-4 rounded-lg text-center <?php echo ($isFull || !$isActive) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                    <?php echo ($isFull || !$isActive) ? 'disabled' : ''; ?>>
                                    <div class="text-2xl font-bold mb-1">
                                        <?php echo formatTime($schedule['show_time']); ?>
                                    </div>
                                    <div class="text-sm text-gray-300 mb-2">
                                        <?php echo formatCurrency($schedule['price']); ?>
                                    </div>
                                    <div class="text-xs <?php echo $isFull ? 'text-red-400' : 'text-green-400'; ?>">
                                        <i class="fas fa-couch mr-1"></i>
                                        <?php 
                                        if ($isFull) {
                                            echo 'Penuh';
                                        } else {
                                            echo $schedule['available_seats'] . ' kursi tersisa';
                                        }
                                        ?>
                                    </div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-16 bg-gray-800 rounded-xl">
                    <i class="fas fa-calendar-times text-6xl text-gray-600 mb-4"></i>
                    <p class="text-xl text-gray-400">Belum ada jadwal tayang untuk film ini</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-6">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $siteName; ?>. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function selectSchedule(scheduleId) {
            window.location.href = 'booking.php?schedule=' + scheduleId;
        }
    </script>
</body>
</html>
