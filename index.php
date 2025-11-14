<?php
require_once 'includes/config.php';

$siteName = getSetting('site_name', 'ISOLA SCREEN');

// Get active films
$films = getActiveFilms();

// Data untuk carousel slides - ambil dari film aktif
$carouselSlides = [];
if ($films && $films->num_rows > 0) {
    while ($film = $films->fetch_assoc()) {
        $carouselSlides[] = [
            'id' => $film['id'],
            'title' => $film['title'],
            'image' => !empty($film['cover_image']) 
                ? 'assets/images/films/' . $film['cover_image'] 
                : 'https://via.placeholder.com/800x400/1f2937/ffffff?text=' . urlencode($film['title']),
            'rating' => $film['rating'],
            'genre' => $film['genre']
        ];
        
        // Batasi hanya 5 slide untuk carousel
        if (count($carouselSlides) >= 5) break;
    }
} else {
    // Fallback slides jika tidak ada film
    $carouselSlides = [
        [
            'id' => 1,
            'title' => 'Film Terbaru',
            'image' => 'https://via.placeholder.com/800x400/1f2937/ffffff?text=ISOLA+SCREEN',
            'rating' => 'SU',
            'genre' => 'Action'
        ],
        [
            'id' => 2,
            'title' => 'Film Terpopuler',
            'image' => 'https://via.placeholder.com/800x400/374151/ffffff?text=Coming+Soon',
            'rating' => 'R13+',
            'genre' => 'Drama'
        ],
        [
            'id' => 3,
            'title' => 'Special Screening',
            'image' => 'https://via.placeholder.com/800x400/6b21a8/ffffff?text=Special+Event',
            'rating' => 'D17+',
            'genre' => 'Thriller'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteName; ?> - Pemesanan Tiket Bioskop</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .film-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .film-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .film-cover {
            aspect-ratio: 2/3;
            object-fit: cover;
            width: 100%;
        }
        .film-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .film-title {
            min-height: 3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .film-info {
            flex: 1;
        }
        .gradient-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
        }
        
        /* Carousel Styles */
        .carousel {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            margin: 20px auto;
            max-width: 1200px;
        }
        .carousel-inner {
            display: flex;
            transition: transform 0.5s ease;
            border-radius: 16px;
        }
        .carousel-item {
            min-width: 100%;
            transition: opacity 0.5s ease;
            border-radius: 16px;
            overflow: hidden;
        }
        .carousel-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 16px;
        }
        .carousel-control {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
        }
        .carousel-control:hover {
            background-color: rgba(0, 0, 0, 0.8);
            transform: translateY(-50%) scale(1.1);
        }
        .carousel-control.prev {
            left: 20px;
        }
        .carousel-control.next {
            right: 20px;
        }
        .carousel-indicators {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 8px;
            z-index: 10;
        }
        .carousel-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .carousel-indicator.active {
            background-color: white;
            transform: scale(1.2);
        }
        
        /* Rating Badge on Carousel */
        .carousel-rating {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: rgba(220, 38, 38, 0.9);
            color: white;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            backdrop-filter: blur(4px);
        }
        
        /* Film Info Overlay */
        .film-info-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
            padding: 30px 20px 20px;
            border-radius: 0 0 16px 16px;
        }

        /* Grid container untuk film cards */
        .films-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 640px) {
            .films-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    
    <!-- Header / Navigation -->
    <header class="sticky top-0 z-50 bg-gradient-to-b from-gray-900 to-gray-800 shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-film text-3xl text-red-500"></i>
                    <h1 class="text-2xl font-bold tracking-wide"><?php echo $siteName; ?></h1>
                </div>
                <a href="search.php" class="p-2 hover:bg-gray-700 rounded-full transition">
                    <i class="fas fa-search text-xl"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section with Carousel -->
    <section class="py-4 bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="carousel">
                <div class="carousel-inner">
                    <?php foreach ($carouselSlides as $index => $slide): ?>
                        <div class="carousel-item relative" data-index="<?php echo $index; ?>">
                            <img 
                                src="<?php echo $slide['image']; ?>" 
                                alt="<?php echo htmlspecialchars($slide['title']); ?>" 
                                class="carousel-image"
                                onerror="this.src='https://via.placeholder.com/800x400/1f2937/ffffff?text=No+Image'">
                            
                            <!-- Rating Badge -->
                            <div class="carousel-rating">
                                <?php echo htmlspecialchars($slide['rating']); ?>
                            </div>
                            
                            <!-- Film Info Overlay -->
                            <div class="film-info-overlay">
                                <h3 class="text-xl font-bold mb-1"><?php echo htmlspecialchars($slide['title']); ?></h3>
                                <p class="text-gray-300 text-sm"><?php echo htmlspecialchars($slide['genre']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Carousel Controls -->
                <button class="carousel-control prev">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                <button class="carousel-control next">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>
                
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <?php foreach ($carouselSlides as $index => $slide): ?>
                        <div class="carousel-indicator <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Now Showing Section -->
    <section class="py-8 bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-fire text-red-500 mr-3"></i>Sedang Tayang
                </h3>
            </div>

            <?php 
            // Reset films pointer untuk digunakan kembali
            $films = getActiveFilms();
            if ($films && $films->num_rows > 0): ?>
                <div class="films-grid">
                    <?php while ($film = $films->fetch_assoc()): ?>
                        <a href="film-detail.php?id=<?php echo $film['id']; ?>" class="film-card bg-gray-800 rounded-xl overflow-hidden shadow-xl">
                            <div class="relative">
                                <?php 
                                $coverPath = !empty($film['cover_image']) 
                                    ? 'assets/images/films/' . $film['cover_image'] 
                                    : 'https://via.placeholder.com/300x450/1f2937/ffffff?text=' . urlencode($film['title']); 
                                ?>
                                <img src="<?php echo $coverPath; ?>" 
                                     alt="<?php echo htmlspecialchars($film['title']); ?>" 
                                     class="film-cover"
                                     onerror="this.src='https://via.placeholder.com/300x450/1f2937/ffffff?text=No+Image'">
                                
                                <!-- Rating Badge -->
                                <div class="absolute top-3 right-3 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
                                    <?php echo htmlspecialchars($film['rating']); ?>
                                </div>
                            </div>
                            
                            <div class="film-content p-4">
                                <div class="film-info">
                                    <h4 class="font-bold text-lg mb-2 film-title" title="<?php echo htmlspecialchars($film['title']); ?>">
                                        <?php echo htmlspecialchars($film['title']); ?>
                                    </h4>
                                    
                                    <div class="flex items-center text-sm text-gray-400 mb-2">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span><?php echo $film['duration']; ?> menit</span>
                                    </div>
                                    
                                    <div class="flex items-center text-sm text-gray-400 mb-3">
                                        <i class="fas fa-theater-masks mr-2"></i>
                                        <span class="line-clamp-1"><?php echo htmlspecialchars($film['genre']); ?></span>
                                    </div>
                                </div>
                                
                                <button class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition mt-auto">
                                    <i class="fas fa-ticket-alt mr-2"></i>Pesan Tiket
                                </button>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-16 bg-gray-800 rounded-xl">
                    <i class="fas fa-film text-6xl text-gray-600 mb-4"></i>
                    <p class="text-xl text-gray-400">Belum ada film yang sedang tayang</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Info Section -->
    <section class="py-12 bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 bg-gray-700 rounded-xl">
                    <i class="fas fa-couch text-4xl text-red-500 mb-4"></i>
                    <h4 class="text-xl font-bold mb-2">Kursi Nyaman</h4>
                    <p class="text-gray-300">Studio dengan kursi premium untuk pengalaman menonton terbaik</p>
                </div>
                
                <div class="text-center p-6 bg-gray-700 rounded-xl">
                    <i class="fas fa-mobile-alt text-4xl text-red-500 mb-4"></i>
                    <h4 class="text-xl font-bold mb-2">Pesan Online</h4>
                    <p class="text-gray-300">Pesan tiket kapan saja, di mana saja dengan mudah</p>
                </div>
                
                <div class="text-center p-6 bg-gray-700 rounded-xl">
                    <i class="fas fa-credit-card text-4xl text-red-500 mb-4"></i>
                    <h4 class="text-xl font-bold mb-2">Pembayaran Aman</h4>
                    <p class="text-gray-300">Berbagai metode pembayaran yang aman dan terpercaya</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-8">
        <div class="container mx-auto px-4">
            <div class="text-center mb-4">
                <div class="flex items-center justify-center space-x-3 mb-4">
                    <i class="fas fa-film text-3xl text-red-500"></i>
                    <h3 class="text-2xl font-bold"><?php echo $siteName; ?></h3>
                </div>
                <p class="text-gray-400 mb-4">Pengalaman menonton film terbaik di kota Anda</p>
            </div>
            
            <div class="flex flex-col md:flex-row justify-center items-center space-y-2 md:space-y-0 md:space-x-6 mb-6">
                <div class="flex items-center text-gray-400">
                    <i class="fas fa-envelope mr-2"></i>
                    <span><?php echo getSetting('contact_email', 'info@isolascreen.com'); ?></span>
                </div>
                <div class="flex items-center text-gray-400">
                    <i class="fas fa-phone mr-2"></i>
                    <span><?php echo getSetting('contact_phone', '021-12345678'); ?></span>
                </div>
            </div>
            
            <div class="text-center text-gray-500 text-sm border-t border-gray-800 pt-6">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $siteName; ?>. All rights reserved.</p>
                <p class="mt-2">
                    <a href="admin/login.php" class="hover:text-red-500 transition">Admin Login</a>
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Carousel Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const carouselInner = document.querySelector('.carousel-inner');
            const carouselItems = document.querySelectorAll('.carousel-item');
            const indicators = document.querySelectorAll('.carousel-indicator');
            const prevBtn = document.querySelector('.carousel-control.prev');
            const nextBtn = document.querySelector('.carousel-control.next');
            
            let currentIndex = 0;
            const totalItems = carouselItems.length;
            
            // Function to update carousel position
            function updateCarousel() {
                carouselInner.style.transform = `translateX(-${currentIndex * 100}%)`;
                
                // Update active indicator
                indicators.forEach((indicator, index) => {
                    if (index === currentIndex) {
                        indicator.classList.add('active');
                    } else {
                        indicator.classList.remove('active');
                    }
                });
            }
            
            // Next slide
            function nextSlide() {
                currentIndex = (currentIndex + 1) % totalItems;
                updateCarousel();
            }
            
            // Previous slide
            function prevSlide() {
                currentIndex = (currentIndex - 1 + totalItems) % totalItems;
                updateCarousel();
            }
            
            // Auto advance slides
            let slideInterval = setInterval(nextSlide, 5000);
            
            // Pause auto-advance on hover
            const carousel = document.querySelector('.carousel');
            carousel.addEventListener('mouseenter', () => {
                clearInterval(slideInterval);
            });
            
            carousel.addEventListener('mouseleave', () => {
                slideInterval = setInterval(nextSlide, 5000);
            });
            
            // Event listeners for controls
            prevBtn.addEventListener('click', prevSlide);
            nextBtn.addEventListener('click', nextSlide);
            
            // Event listeners for indicators
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    currentIndex = index;
                    updateCarousel();
                });
            });
            
            // Touch swipe functionality
            let startX = 0;
            let endX = 0;
            
            carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            });
            
            carousel.addEventListener('touchmove', (e) => {
                endX = e.touches[0].clientX;
            });
            
            carousel.addEventListener('touchend', () => {
                const diffX = startX - endX;
                
                // Minimum swipe distance
                if (Math.abs(diffX) > 50) {
                    if (diffX > 0) {
                        // Swipe left - next slide
                        nextSlide();
                    } else {
                        // Swipe right - previous slide
                        prevSlide();
                    }
                }
            });

            // Set equal height for film cards
            function setEqualCardHeights() {
                const filmCards = document.querySelectorAll('.film-card');
                let maxHeight = 0;
                
                // Reset heights first
                filmCards.forEach(card => {
                    card.style.height = 'auto';
                });
                
                // Find the maximum height
                filmCards.forEach(card => {
                    const cardHeight = card.offsetHeight;
                    if (cardHeight > maxHeight) {
                        maxHeight = cardHeight;
                    }
                });
                
                // Set all cards to the maximum height
                filmCards.forEach(card => {
                    card.style.height = maxHeight + 'px';
                });
            }

            // Set equal heights on load and resize
            window.addEventListener('load', setEqualCardHeights);
            window.addEventListener('resize', setEqualCardHeights);
        });
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>