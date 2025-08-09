<?php
require_once '../includes/functions.php';
requireUserLogin();

$user = getCurrentUser();
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$prizes = $db->fetchAll("
    SELECT up.*, p.name as prize_name, p.price, up.method, up.won_at 
    FROM user_prizes up 
    JOIN prizes p ON up.prize_id = p.id 
    WHERE up.user_id = ? 
    ORDER BY up.won_at DESC 
    LIMIT ? OFFSET ?
", [$user['id'], $limit, $offset]);

$totalPrizes = $db->fetch("SELECT COUNT(*) as count FROM user_prizes WHERE user_id = ?", [$user['id']])['count'];
$totalPages = ceil($totalPrizes / $limit);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prize History - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <a href="dashboard.php" class="text-white hover:text-yellow-400 transition duration-300">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <span class="text-white">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</span>
                    <a href="/auth/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-4xl font-bold text-white mb-4">
                <i class="fas fa-history text-yellow-400"></i>
                Prize History
            </h2>
            <p class="text-blue-100">View all your amazing wins!</p>
        </div>

        <!-- Prize History -->
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20">
            <?php if (empty($prizes)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-gift text-gray-400 text-6xl mb-6"></i>
                    <h3 class="text-2xl font-bold text-white mb-4">No Prizes Yet</h3>
                    <p class="text-blue-100 mb-8">You haven't won any prizes yet. Try your luck!</p>
                    <a href="dashboard.php" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                        Start Playing
                    </a>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($prizes as $prize): ?>
                        <div class="bg-white/5 rounded-lg p-6 flex justify-between items-center">
                            <div class="flex items-center">
                                <div class="text-3xl mr-6">
                                    <?php echo $prize['method'] === 'wheel' ? '🎡' : '📦'; ?>
                                </div>
                                <div>
                                    <h4 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($prize['prize_name']); ?></h4>
                                    <p class="text-blue-200">
                                        Won via <?php echo ucfirst($prize['method']); ?> on <?php echo date('M j, Y \a\t g:i A', strtotime($prize['won_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-green-400"><?php echo formatPrice($prize['price']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center mt-8 space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded transition duration-300">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>"
                                class="px-4 py-2 rounded transition duration-300 <?php echo $i === $page ? 'bg-yellow-500 text-black' : 'bg-white/10 hover:bg-white/20 text-white'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded transition duration-300">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="text-center mt-4 text-blue-200">
                        Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $totalPrizes); ?> of <?php echo $totalPrizes; ?> prizes
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>