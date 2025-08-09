<?php
require_once '../includes/functions.php';
requireUserLogin();

$user = getCurrentUser();
$recentPrizes = $db->fetchAll("
    SELECT up.*, p.name as prize_name, p.price, up.method, up.won_at 
    FROM user_prizes up 
    JOIN prizes p ON up.prize_id = p.id 
    WHERE up.user_id = ? 
    ORDER BY up.won_at DESC 
    LIMIT 5
", [$user['id']]);

$totalWins = $db->fetch("SELECT COUNT(*) as count FROM user_prizes WHERE user_id = ?", [$user['id']])['count'];
$totalValue = $db->fetch("
    SELECT COALESCE(SUM(p.price), 0) as total 
    FROM user_prizes up 
    JOIN prizes p ON up.prize_id = p.id 
    WHERE up.user_id = ?
", [$user['id']])['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-600 via-blue-600 to-indigo-800 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/10 backdrop-blur-lg border-b border-white/20">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">
                    <i class="fas fa-gift text-yellow-400"></i>
                    <?php echo SITE_NAME; ?>
                </h1>
                <div class="flex items-center space-x-4">
                    <span class="text-white">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</span>
                    <a href="/auth/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 text-center">
                <i class="fas fa-trophy text-yellow-400 text-3xl mb-3"></i>
                <h3 class="text-2xl font-bold text-white"><?php echo $totalWins; ?></h3>
                <p class="text-blue-100">Total Wins</p>
            </div>
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 text-center">
                <i class="fas fa-dollar-sign text-green-400 text-3xl mb-3"></i>
                <h3 class="text-2xl font-bold text-white"><?php echo formatPrice($totalValue); ?></h3>
                <p class="text-blue-100">Total Value Won</p>
            </div>
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20 text-center">
                <i class="fas fa-calendar text-blue-400 text-3xl mb-3"></i>
                <h3 class="text-2xl font-bold text-white"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></h3>
                <p class="text-blue-100">Member Since</p>
            </div>
        </div>

        <!-- Prize Games -->
        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <!-- Wheel of Fortune -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 text-center border border-white/20">
                <div class="text-6xl mb-4">🎡</div>
                <h2 class="text-3xl font-bold text-white mb-4">Wheel of Fortune</h2>
                <p class="text-blue-100 mb-6">Spin the wheel and let luck decide your prize!</p>
                <a href="wheel.php" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                    <i class="fas fa-play mr-2"></i>Spin Now!
                </a>
            </div>

            <!-- Mystery Box -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 text-center border border-white/20">
                <div class="text-6xl mb-4">📦</div>
                <h2 class="text-3xl font-bold text-white mb-4">Mystery Box</h2>
                <p class="text-blue-100 mb-6">Open a mystery box and discover what's inside!</p>
                <a href="box.php" class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                    <i class="fas fa-box-open mr-2"></i>Open Box!
                </a>
            </div>
        </div>

        <!-- Recent Wins -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20">
            <h3 class="text-2xl font-bold text-white mb-6">
                <i class="fas fa-history mr-2"></i>Recent Wins
            </h3>
            
            <?php if (empty($recentPrizes)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-gift text-gray-400 text-4xl mb-4"></i>
                    <p class="text-blue-100">No prizes won yet. Try your luck!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($recentPrizes as $prize): ?>
                        <div class="bg-white/5 rounded-lg p-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="text-2xl mr-4">
                                    <?php echo $prize['method'] === 'wheel' ? '🎡' : '📦'; ?>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold"><?php echo htmlspecialchars($prize['prize_name']); ?></h4>
                                    <p class="text-blue-200 text-sm">
                                        Won via <?php echo ucfirst($prize['method']); ?> • <?php echo timeAgo($prize['won_at']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-green-400 font-bold"><?php echo formatPrice($prize['price']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($totalWins > 5): ?>
                    <div class="text-center mt-6">
                        <a href="history.php" class="text-yellow-400 hover:text-yellow-300 font-medium">
                            View All Wins <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

