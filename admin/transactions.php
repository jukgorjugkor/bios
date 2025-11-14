<?php
require_once '../includes/config.php';
requireAdminLogin();

$filterStatus = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$sql = "SELECT t.*, b.booking_code, b.customer_name, f.title, s.show_date, s.show_time
        FROM transactions t
        JOIN bookings b ON t.booking_id = b.id
        JOIN schedules s ON b.schedule_id = s.id
        JOIN films f ON s.film_id = f.id";

if ($filterStatus) {
    $sql .= " WHERE t.transaction_status = '" . $conn->real_escape_string($filterStatus) . "'";
}

$sql .= " ORDER BY t.created_at DESC";

$transactions = $conn->query($sql);
$siteName = getSetting('site_name', 'ISOLA SCREEN');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Admin <?php echo $siteName; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white">
    
    <?php include 'includes/header.php'; ?>
    
    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="flex-1 p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2">Transaksi</h1>
                <p class="text-gray-400">Laporan transaksi pembayaran</p>
            </div>

            <div class="bg-gray-800 rounded-xl p-6 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-semibold mb-2">Filter Status</label>
                        <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-red-500">
                            <option value="">Semua Status</option>
                            <option value="pending" <?php echo $filterStatus === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="settlement" <?php echo $filterStatus === 'settlement' ? 'selected' : ''; ?>>Settlement</option>
                            <option value="capture" <?php echo $filterStatus === 'capture' ? 'selected' : ''; ?>>Capture</option>
                            <option value="deny" <?php echo $filterStatus === 'deny' ? 'selected' : ''; ?>>Deny</option>
                            <option value="cancel" <?php echo $filterStatus === 'cancel' ? 'selected' : ''; ?>>Cancel</option>
                            <option value="expire" <?php echo $filterStatus === 'expire' ? 'selected' : ''; ?>>Expire</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    
                    <?php if ($filterStatus): ?>
                        <a href="transactions.php" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Booking Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Pemesan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Film</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Payment Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php if ($transactions && $transactions->num_rows > 0): ?>
                                <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-700 transition">
                                        <td class="px-6 py-4 font-mono text-sm"><?php echo htmlspecialchars($transaction['order_id']); ?></td>
                                        <td class="px-6 py-4 font-mono text-sm text-red-400"><?php echo htmlspecialchars($transaction['booking_code']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($transaction['customer_name']); ?></td>
                                        <td class="px-6 py-4"><?php echo htmlspecialchars($transaction['title']); ?></td>
                                        <td class="px-6 py-4 font-semibold"><?php echo formatCurrency($transaction['gross_amount']); ?></td>
                                        <td class="px-6 py-4 text-sm"><?php echo $transaction['payment_type'] ? htmlspecialchars($transaction['payment_type']) : '-'; ?></td>
                                        <td class="px-6 py-4">
                                            <?php
                                            $status = $transaction['transaction_status'];
                                            $statusClass = 'bg-gray-600';
                                            if ($status === 'settlement' || $status === 'capture') {
                                                $statusClass = 'bg-green-600';
                                            } elseif ($status === 'pending') {
                                                $statusClass = 'bg-yellow-600';
                                            } elseif ($status === 'deny' || $status === 'cancel' || $status === 'expire') {
                                                $statusClass = 'bg-red-600';
                                            }
                                            ?>
                                            <span class="<?php echo $statusClass; ?> text-white text-xs px-3 py-1 rounded-full">
                                                <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm"><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
