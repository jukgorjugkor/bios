-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 14, 2025 at 02:26 PM
-- Server version: 10.11.6-MariaDB-cll-lve
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rotz3716_isolascreen`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$d82x09.FwY0Vw5YcFoxOXeOkwL6wSGYegNalF1dAZg8inF9Zhsze6', 'admin@isolascreen.com', '2025-11-13 03:24:54');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `seats` text NOT NULL COMMENT 'JSON array seat labels',
  `total_seats` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','confirmed','cancelled','expired') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','failed','refunded') DEFAULT 'unpaid',
  `booking_date` timestamp NULL DEFAULT current_timestamp(),
  `expired_at` timestamp NULL DEFAULT NULL COMMENT 'Waktu booking akan expired jika belum bayar'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `schedule_id`, `customer_name`, `customer_email`, `customer_phone`, `seats`, `total_seats`, `total_price`, `booking_status`, `payment_status`, `booking_date`, `expired_at`) VALUES
(1, 'ISOLA-62AD6914', 2, 'Yuda', 'myudar2301@gmail.com', '081233009283', '[\"D3\",\"D4\"]', 2, 30000.00, 'expired', 'unpaid', '2025-11-13 03:53:14', '2025-11-13 04:08:14'),
(2, 'ISOLA-2E48D804', 2, 'honda', 'myudar2301@gmail.com', '081233009283', '[\"E3\",\"E6\"]', 2, 30000.00, 'expired', 'unpaid', '2025-11-13 09:20:36', '2025-11-13 09:35:36'),
(3, 'ISOLA-E3589BF2', 2, 'Yuda', 'myudar2301@gmail.com', '8205805889', '[\"C3\"]', 1, 15000.00, 'expired', 'unpaid', '2025-11-13 10:08:53', '2025-11-13 10:23:53'),
(4, 'ISOLA-7F788E8C', 2, 'asdasd', 'myudar2301@gmail.com', '081233009283', '[\"D5\"]', 1, 15000.00, 'expired', 'unpaid', '2025-11-13 10:50:31', '2025-11-13 11:05:31'),
(5, 'ISOLA-C8D4A063', 2, 'SUMAIYAH', 'myudar2301@gmail.com', '081233009283', '[\"D6\"]', 1, 15000.00, 'expired', 'unpaid', '2025-11-13 11:10:05', '2025-11-13 11:25:05'),
(6, 'ISOLA-36D92D20', 2, 'jopi', 'zovie71@gmail.com', '676857764', '[\"A2\"]', 1, 15000.00, 'expired', 'unpaid', '2025-11-13 11:39:25', '2025-11-13 11:54:25'),
(7, 'ISOLA-09884A90', 1, 'hwjej', 'znsnsn@gmail.com', '085461995', '[\"A1\",\"A2\",\"A3\",\"A4\",\"A5\",\"A6\",\"A7\",\"A8\",\"A10\",\"A9\",\"B1\",\"B2\",\"B3\",\"B4\",\"B5\",\"B7\",\"B6\",\"B8\",\"B10\",\"B9\",\"C1\",\"C2\",\"C3\",\"C4\",\"C5\",\"C6\",\"C7\",\"C8\",\"C9\",\"C10\",\"D1\",\"D2\",\"D3\",\"D4\",\"D5\",\"D6\",\"D7\",\"D8\",\"D10\"]', 39, 585000.00, 'expired', 'unpaid', '2025-11-13 22:50:00', '2025-11-13 23:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `films`
--

CREATE TABLE `films` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) NOT NULL COMMENT 'Durasi dalam menit',
  `genre` varchar(100) DEFAULT NULL,
  `rating` varchar(10) DEFAULT NULL COMMENT 'G, PG, PG-13, R, dll',
  `release_date` date DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL COMMENT 'Path ke gambar cover',
  `trailer_url` varchar(255) DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `cast` text DEFAULT NULL COMMENT 'Pemain utama',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `films`
--

INSERT INTO `films` (`id`, `title`, `description`, `duration`, `genre`, `rating`, `release_date`, `cover_image`, `trailer_url`, `director`, `cast`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Avatar: The Way of Water', 'Sekuel epik Avatar yang membawa kita ke dunia Pandora yang menakjubkan dengan teknologi visual terkini. Jake Sully dan keluarganya berjuang melindungi planet mereka dari ancaman manusia.', 192, 'Action, Adventure, Sci-Fi', 'PG-13', '2024-01-15', '691551c2dd539_1763004866.jpg', '', 'James Cameron', 'Sam Worthington, Zoe Saldana, Sigourney Weaver', 'active', '2025-11-13 03:24:54', '2025-11-13 03:34:26'),
(2, 'The Batman', 'Seorang Batman muda mengungkap korupsi di Gotham City yang terhubung dengan keluarganya sendiri sambil memburu pembunuh berantai yang kejam.', 176, 'Action, Crime, Drama', 'PG-13', '2024-01-20', '691551cb12105_1763004875.jpg', '', 'Matt Reeves', 'Robert Pattinson, Zoë Kravitz, Paul Dano', 'active', '2025-11-13 03:24:54', '2025-11-13 03:34:35'),
(3, 'Everything Everywhere All at Once', 'Seorang ibu rumah tangga terseret ke dalam petualangan liar melintasi multiverse, di mana dia harus menggunakan kekuatan baru yang ditemukan untuk menyelamatkan dunia.', 139, 'Action, Adventure, Comedy', 'R', '2024-02-01', '691551d50ae12_1763004885.jpeg', '', 'Daniel Kwan, Daniel Scheinert', 'Michelle Yeoh, Stephanie Hsu, Jamie Lee Curtis', 'active', '2025-11-13 03:24:54', '2025-11-13 03:34:45'),
(4, 'Top Gun: Maverick', 'Setelah lebih dari 30 tahun berkarier, Pete \\\"Maverick\\\" Mitchell tetap melakukan apa yang dia kuasai sebagai pilot uji coba penerbangan yang berani.', 131, 'Action, Drama', 'PG-13', '2024-02-10', '691551e4856e5_1763004900.jpeg', '', 'Joseph Kosinski', 'Tom Cruise, Miles Teller, Jennifer Connelly', 'active', '2025-11-13 03:24:54', '2025-11-13 03:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `film_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_seats` int(11) DEFAULT 50 COMMENT 'Jumlah kursi tersedia',
  `total_seats` int(11) DEFAULT 50 COMMENT 'Total kursi di studio',
  `status` enum('active','inactive','full') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `film_id`, `show_date`, `show_time`, `price`, `available_seats`, `total_seats`, `status`, `created_at`) VALUES
(1, 2, '2025-11-14', '06:30:00', 15000.00, 50, 50, 'active', '2025-11-13 03:50:14'),
(2, 4, '2025-11-18', '21:50:00', 15000.00, 50, 50, 'active', '2025-11-13 03:50:33');

--
-- Triggers `schedules`
--
DELIMITER $$
CREATE TRIGGER `after_schedule_insert` AFTER INSERT ON `schedules` FOR EACH ROW BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE j INT DEFAULT 1;
    DECLARE row_char CHAR(1);
    DECLARE total_rows INT;
    DECLARE seats_per_row INT;
    
    -- Get settings
    SELECT CAST(setting_value AS UNSIGNED) INTO total_rows FROM settings WHERE setting_key = 'seat_rows';
    SELECT CAST(setting_value AS UNSIGNED) INTO seats_per_row FROM settings WHERE setting_key = 'seat_per_row';
    
    -- Loop untuk setiap baris
    WHILE i <= total_rows DO
        SET row_char = CHAR(64 + i); -- A=65, B=66, dst
        SET j = 1;
        
        -- Loop untuk setiap kursi di baris
        WHILE j <= seats_per_row DO
            INSERT INTO seats (schedule_id, seat_row, seat_number, seat_label, status)
            VALUES (NEW.id, row_char, j, CONCAT(row_char, j), 'available');
            SET j = j + 1;
        END WHILE;
        
        SET i = i + 1;
    END WHILE;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `seat_row` varchar(2) NOT NULL COMMENT 'A, B, C, dll',
  `seat_number` int(11) NOT NULL,
  `seat_label` varchar(5) NOT NULL COMMENT 'A1, A2, B1, dll',
  `status` enum('available','reserved','booked') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `schedule_id`, `seat_row`, `seat_number`, `seat_label`, `status`, `created_at`) VALUES
(1, 1, 'A', 1, 'A1', 'available', '2025-11-13 03:50:14'),
(2, 1, 'A', 2, 'A2', 'available', '2025-11-13 03:50:14'),
(3, 1, 'A', 3, 'A3', 'available', '2025-11-13 03:50:14'),
(4, 1, 'A', 4, 'A4', 'available', '2025-11-13 03:50:14'),
(5, 1, 'A', 5, 'A5', 'available', '2025-11-13 03:50:14'),
(6, 1, 'A', 6, 'A6', 'available', '2025-11-13 03:50:14'),
(7, 1, 'A', 7, 'A7', 'available', '2025-11-13 03:50:14'),
(8, 1, 'A', 8, 'A8', 'available', '2025-11-13 03:50:14'),
(9, 1, 'A', 9, 'A9', 'available', '2025-11-13 03:50:14'),
(10, 1, 'A', 10, 'A10', 'available', '2025-11-13 03:50:14'),
(11, 1, 'B', 1, 'B1', 'available', '2025-11-13 03:50:14'),
(12, 1, 'B', 2, 'B2', 'available', '2025-11-13 03:50:14'),
(13, 1, 'B', 3, 'B3', 'available', '2025-11-13 03:50:14'),
(14, 1, 'B', 4, 'B4', 'available', '2025-11-13 03:50:14'),
(15, 1, 'B', 5, 'B5', 'available', '2025-11-13 03:50:14'),
(16, 1, 'B', 6, 'B6', 'available', '2025-11-13 03:50:14'),
(17, 1, 'B', 7, 'B7', 'available', '2025-11-13 03:50:14'),
(18, 1, 'B', 8, 'B8', 'available', '2025-11-13 03:50:14'),
(19, 1, 'B', 9, 'B9', 'available', '2025-11-13 03:50:14'),
(20, 1, 'B', 10, 'B10', 'available', '2025-11-13 03:50:14'),
(21, 1, 'C', 1, 'C1', 'available', '2025-11-13 03:50:14'),
(22, 1, 'C', 2, 'C2', 'available', '2025-11-13 03:50:14'),
(23, 1, 'C', 3, 'C3', 'available', '2025-11-13 03:50:14'),
(24, 1, 'C', 4, 'C4', 'available', '2025-11-13 03:50:14'),
(25, 1, 'C', 5, 'C5', 'available', '2025-11-13 03:50:14'),
(26, 1, 'C', 6, 'C6', 'available', '2025-11-13 03:50:14'),
(27, 1, 'C', 7, 'C7', 'available', '2025-11-13 03:50:14'),
(28, 1, 'C', 8, 'C8', 'available', '2025-11-13 03:50:14'),
(29, 1, 'C', 9, 'C9', 'available', '2025-11-13 03:50:14'),
(30, 1, 'C', 10, 'C10', 'available', '2025-11-13 03:50:14'),
(31, 1, 'D', 1, 'D1', 'available', '2025-11-13 03:50:14'),
(32, 1, 'D', 2, 'D2', 'available', '2025-11-13 03:50:14'),
(33, 1, 'D', 3, 'D3', 'available', '2025-11-13 03:50:14'),
(34, 1, 'D', 4, 'D4', 'available', '2025-11-13 03:50:14'),
(35, 1, 'D', 5, 'D5', 'available', '2025-11-13 03:50:14'),
(36, 1, 'D', 6, 'D6', 'available', '2025-11-13 03:50:14'),
(37, 1, 'D', 7, 'D7', 'available', '2025-11-13 03:50:14'),
(38, 1, 'D', 8, 'D8', 'available', '2025-11-13 03:50:14'),
(39, 1, 'D', 9, 'D9', 'available', '2025-11-13 03:50:14'),
(40, 1, 'D', 10, 'D10', 'available', '2025-11-13 03:50:14'),
(41, 1, 'E', 1, 'E1', 'available', '2025-11-13 03:50:14'),
(42, 1, 'E', 2, 'E2', 'available', '2025-11-13 03:50:14'),
(43, 1, 'E', 3, 'E3', 'available', '2025-11-13 03:50:14'),
(44, 1, 'E', 4, 'E4', 'available', '2025-11-13 03:50:14'),
(45, 1, 'E', 5, 'E5', 'available', '2025-11-13 03:50:14'),
(46, 1, 'E', 6, 'E6', 'available', '2025-11-13 03:50:14'),
(47, 1, 'E', 7, 'E7', 'available', '2025-11-13 03:50:14'),
(48, 1, 'E', 8, 'E8', 'available', '2025-11-13 03:50:14'),
(49, 1, 'E', 9, 'E9', 'available', '2025-11-13 03:50:14'),
(50, 1, 'E', 10, 'E10', 'available', '2025-11-13 03:50:14'),
(51, 2, 'A', 1, 'A1', 'available', '2025-11-13 03:50:33'),
(52, 2, 'A', 2, 'A2', 'available', '2025-11-13 03:50:33'),
(53, 2, 'A', 3, 'A3', 'available', '2025-11-13 03:50:33'),
(54, 2, 'A', 4, 'A4', 'available', '2025-11-13 03:50:33'),
(55, 2, 'A', 5, 'A5', 'available', '2025-11-13 03:50:33'),
(56, 2, 'A', 6, 'A6', 'available', '2025-11-13 03:50:33'),
(57, 2, 'A', 7, 'A7', 'available', '2025-11-13 03:50:33'),
(58, 2, 'A', 8, 'A8', 'available', '2025-11-13 03:50:33'),
(59, 2, 'A', 9, 'A9', 'available', '2025-11-13 03:50:33'),
(60, 2, 'A', 10, 'A10', 'available', '2025-11-13 03:50:33'),
(61, 2, 'B', 1, 'B1', 'available', '2025-11-13 03:50:33'),
(62, 2, 'B', 2, 'B2', 'available', '2025-11-13 03:50:33'),
(63, 2, 'B', 3, 'B3', 'available', '2025-11-13 03:50:33'),
(64, 2, 'B', 4, 'B4', 'available', '2025-11-13 03:50:33'),
(65, 2, 'B', 5, 'B5', 'available', '2025-11-13 03:50:33'),
(66, 2, 'B', 6, 'B6', 'available', '2025-11-13 03:50:33'),
(67, 2, 'B', 7, 'B7', 'available', '2025-11-13 03:50:33'),
(68, 2, 'B', 8, 'B8', 'available', '2025-11-13 03:50:33'),
(69, 2, 'B', 9, 'B9', 'available', '2025-11-13 03:50:33'),
(70, 2, 'B', 10, 'B10', 'available', '2025-11-13 03:50:33'),
(71, 2, 'C', 1, 'C1', 'available', '2025-11-13 03:50:33'),
(72, 2, 'C', 2, 'C2', 'available', '2025-11-13 03:50:33'),
(73, 2, 'C', 3, 'C3', 'available', '2025-11-13 03:50:33'),
(74, 2, 'C', 4, 'C4', 'available', '2025-11-13 03:50:33'),
(75, 2, 'C', 5, 'C5', 'available', '2025-11-13 03:50:33'),
(76, 2, 'C', 6, 'C6', 'available', '2025-11-13 03:50:33'),
(77, 2, 'C', 7, 'C7', 'available', '2025-11-13 03:50:33'),
(78, 2, 'C', 8, 'C8', 'available', '2025-11-13 03:50:33'),
(79, 2, 'C', 9, 'C9', 'available', '2025-11-13 03:50:33'),
(80, 2, 'C', 10, 'C10', 'available', '2025-11-13 03:50:33'),
(81, 2, 'D', 1, 'D1', 'available', '2025-11-13 03:50:33'),
(82, 2, 'D', 2, 'D2', 'available', '2025-11-13 03:50:33'),
(83, 2, 'D', 3, 'D3', 'available', '2025-11-13 03:50:33'),
(84, 2, 'D', 4, 'D4', 'available', '2025-11-13 03:50:33'),
(85, 2, 'D', 5, 'D5', 'available', '2025-11-13 03:50:33'),
(86, 2, 'D', 6, 'D6', 'available', '2025-11-13 03:50:33'),
(87, 2, 'D', 7, 'D7', 'available', '2025-11-13 03:50:33'),
(88, 2, 'D', 8, 'D8', 'available', '2025-11-13 03:50:33'),
(89, 2, 'D', 9, 'D9', 'available', '2025-11-13 03:50:33'),
(90, 2, 'D', 10, 'D10', 'available', '2025-11-13 03:50:33'),
(91, 2, 'E', 1, 'E1', 'available', '2025-11-13 03:50:33'),
(92, 2, 'E', 2, 'E2', 'available', '2025-11-13 03:50:33'),
(93, 2, 'E', 3, 'E3', 'available', '2025-11-13 03:50:33'),
(94, 2, 'E', 4, 'E4', 'available', '2025-11-13 03:50:33'),
(95, 2, 'E', 5, 'E5', 'available', '2025-11-13 03:50:33'),
(96, 2, 'E', 6, 'E6', 'available', '2025-11-13 03:50:33'),
(97, 2, 'E', 7, 'E7', 'available', '2025-11-13 03:50:33'),
(98, 2, 'E', 8, 'E8', 'available', '2025-11-13 03:50:33'),
(99, 2, 'E', 9, 'E9', 'available', '2025-11-13 03:50:33'),
(100, 2, 'E', 10, 'E10', 'available', '2025-11-13 03:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(50) DEFAULT 'text' COMMENT 'text, number, boolean, json',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
(1, 'site_name', 'ISOLA SCREEN', 'text', 'Nama website', '2025-11-13 03:36:15'),
(2, 'studio_name', 'Studio 1', 'text', 'Nama studio bioskop', '2025-11-13 03:36:15'),
(3, 'total_seats', '50', 'number', 'Total kursi di studio', '2025-11-13 03:36:15'),
(4, 'seat_rows', '5', 'number', 'Jumlah baris kursi (A, B, C, D, E)', '2025-11-13 03:36:15'),
(5, 'seat_per_row', '10', 'number', 'Jumlah kursi per baris', '2025-11-13 03:36:15'),
(6, 'booking_timeout', '15', 'number', 'Waktu timeout booking dalam menit', '2025-11-13 03:36:15'),
(7, 'midtrans_server_key', 'Mid-server-y_VQ32Vn7ME4D5QjKGfe_RSS', 'text', 'Midtrans Server Key', '2025-11-13 03:36:15'),
(8, 'midtrans_client_key', 'Mid-client-iUuyzd9xYSgi4Eo3', 'text', 'Midtrans Client Key', '2025-11-13 03:36:15'),
(9, 'midtrans_merchant_id', 'G481003237', 'text', 'Midtrans Merchant ID', '2025-11-13 03:36:15'),
(10, 'midtrans_environment', 'production', 'text', 'Midtrans Environment (sandbox/production)', '2025-11-13 03:36:15'),
(11, 'currency', 'IDR', 'text', 'Mata uang', '2025-11-13 03:24:54'),
(12, 'contact_email', 'info@isolascreen.com', 'text', 'Email kontak', '2025-11-13 03:36:15'),
(13, 'contact_phone', '021-12345678', 'text', 'Telepon kontak', '2025-11-13 03:36:15');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL COMMENT 'Order ID untuk Midtrans',
  `transaction_id` varchar(100) DEFAULT NULL COMMENT 'Transaction ID dari Midtrans',
  `gross_amount` decimal(10,2) NOT NULL,
  `payment_type` varchar(50) DEFAULT NULL COMMENT 'credit_card, bank_transfer, dll',
  `transaction_status` varchar(50) DEFAULT 'pending',
  `transaction_time` timestamp NULL DEFAULT NULL,
  `settlement_time` timestamp NULL DEFAULT NULL,
  `fraud_status` varchar(50) DEFAULT NULL,
  `status_code` varchar(10) DEFAULT NULL,
  `midtrans_response` text DEFAULT NULL COMMENT 'JSON response dari Midtrans',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `booking_id`, `order_id`, `transaction_id`, `gross_amount`, `payment_type`, `transaction_status`, `transaction_time`, `settlement_time`, `fraud_status`, `status_code`, `midtrans_response`, `created_at`, `updated_at`) VALUES
(1, 1, 'ORDER-1763005995-6598', NULL, 30000.00, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2025-11-13 03:53:18', '2025-11-13 03:53:18'),
(2, 2, 'ORDER-1763025637-7915', 'b85f2149-7790-40e2-aee9-95192af5a263', 30000.00, 'qris', 'pending', '2025-11-13 09:20:52', NULL, 'accept', '201', '{\"status_code\":\"201\",\"transaction_id\":\"b85f2149-7790-40e2-aee9-95192af5a263\",\"gross_amount\":\"30000.00\",\"currency\":\"IDR\",\"order_id\":\"ORDER-1763025637-7915\",\"payment_type\":\"qris\",\"signature_key\":\"8986b88afccc0d72bee6d1fdd2def28803acb99cb1e5584ea53b278ce808f1f7c1550fcc38c9557eb333a0650978abd03f07d3fddabf74917e897c8fc9a58a1f\",\"transaction_status\":\"pending\",\"fraud_status\":\"accept\",\"status_message\":\"Success, transaction is found\",\"merchant_id\":\"G481003237\",\"transaction_time\":\"2025-11-13 16:20:52\",\"expiry_time\":\"2025-11-13 16:35:52\"}', '2025-11-13 09:20:42', '2025-11-13 09:20:54'),
(3, 3, 'ORDER-1763028533-6435', NULL, 15000.00, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2025-11-13 10:08:59', '2025-11-13 10:08:59'),
(4, 4, 'ORDER-1763031031-9153', NULL, 15000.00, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2025-11-13 10:50:32', '2025-11-13 10:50:32'),
(5, 4, 'ORDER-1763031448-7865', NULL, 15000.00, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2025-11-13 10:57:28', '2025-11-13 10:57:28'),
(6, 6, 'ORDER-1763033965-7706', NULL, 15000.00, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2025-11-13 11:39:31', '2025-11-13 11:39:31'),
(7, 7, 'ORDER-1763074200-3919', NULL, 585000.00, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2025-11-13 22:50:09', '2025-11-13 22:50:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `idx_booking_code` (`booking_code`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_booking_date` (`booking_date`);

--
-- Indexes for table `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_show_date` (`show_date`),
  ADD KEY `idx_film_date` (`film_id`,`show_date`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seat` (`schedule_id`,`seat_label`),
  ADD KEY `idx_schedule_status` (`schedule_id`,`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_transaction_status` (`transaction_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `films`
--
ALTER TABLE `films`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
