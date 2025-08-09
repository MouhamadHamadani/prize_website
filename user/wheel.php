<?php
require_once '../includes/functions.php';
requireUserLogin();

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wheel of Fortune - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
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
                    <a href="history.php" class="text-white hover:text-yellow-400 transition duration-300">
                        <i class="fas fa-history mr-2"></i>History
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
            <h2 class="text-5xl font-bold text-white mb-4 float">
                🎡 Wheel of Fortune
            </h2>
            <p class="text-xl text-blue-100">Spin the wheel and let luck decide your prize!</p>
        </div>

        <!-- Game Area -->
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col gap-8 items-center">
                <!-- Wheel Section -->
                <div class="text-center">
                    <div class="wheel-container mb-8">
                        <canvas id="wheelCanvas" width="500" height="500" class="wheel-canvas"></canvas>
                        <div class="wheel-pointer"></div>
                    </div>

                    <button class="spin-wheel-btn bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-4 px-8 rounded-full text-xl transition duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-play mr-2"></i>Spin Wheel!
                    </button>
                </div>

                <!-- Info Section -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20">
                    <h3 class="text-2xl font-bold text-white mb-6">
                        <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                        How It Works
                    </h3>

                    <div class="space-y-4 text-blue-100">
                        <div class="flex items-start">
                            <div class="bg-yellow-500 text-black rounded-full w-8 h-8 flex items-center justify-center font-bold mr-4 mt-1">1</div>
                            <div>
                                <h4 class="font-semibold text-white">Click Spin</h4>
                                <p>Press the "Spin Wheel" button to start the game</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-yellow-500 text-black rounded-full w-8 h-8 flex items-center justify-center font-bold mr-4 mt-1">2</div>
                            <div>
                                <h4 class="font-semibold text-white">Watch It Spin</h4>
                                <p>The wheel will spin and gradually slow down</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="bg-yellow-500 text-black rounded-full w-8 h-8 flex items-center justify-center font-bold mr-4 mt-1">3</div>
                            <div>
                                <h4 class="font-semibold text-white">Win Your Prize</h4>
                                <p>Where the pointer lands determines your prize!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Available Prizes -->
                    <div class="mt-8">
                        <h4 class="text-lg font-semibold text-white mb-4">
                            <i class="fas fa-gift text-yellow-400 mr-2"></i>
                            Available Prizes
                        </h4>
                        <div id="prizesList" class="space-y-2 max-h-48 overflow-y-auto">
                            <!-- Prizes will be loaded by JavaScript -->
                            <div class="text-center py-4">
                                <div class="spinner"></div>
                                <p class="text-blue-200 mt-2">Loading prizes...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Game Rules -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20 mt-8">
                <h3 class="text-2xl font-bold text-white mb-6">
                    <i class="fas fa-rules text-green-400 mr-2"></i>
                    Game Rules
                </h3>

                <div class="grid md:grid-cols-2 gap-6 text-blue-100">
                    <div>
                        <h4 class="font-semibold text-white mb-2">Fair Play</h4>
                        <p>All spins are completely random and fair. Each prize has a specific probability based on its value and availability.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-white mb-2">One Spin Per Session</h4>
                        <p>You can spin the wheel multiple times, but each spin is independent with fresh odds.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-white mb-2">Limited Quantities</h4>
                        <p>Some prizes have limited quantities. Once they're gone, they won't appear on the wheel.</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-white mb-2">Instant Rewards</h4>
                        <p>Your prize is awarded immediately and added to your account history.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="/assets/js/app.js"></script>
    <script>
        $(document).ready(function() {
            // Load wheel prizes and generate segments
            loadWheelPrizes();

            async function loadWheelPrizes() {
                try {
                    const prizes = await window.prizeApp.loadPrizes('wheel');

                    if (prizes && prizes.length > 0) {
                        // Store prizes in the app for wheel positioning
                        window.prizeApp.prizes = prizes;

                        // Generate wheel segments
                        window.prizeApp.generateWheelSegments(prizes);

                        // Update prizes list
                        updatePrizesList(prizes);
                    } else {
                        $('#prizeWheel').html('<div class="flex items-center justify-center h-full text-white text-center"><div><i class="fas fa-exclamation-triangle text-4xl mb-4"></i><br>No prizes available</div></div>');
                        $('#prizesList').html('<div class="text-center text-blue-200">No prizes available at this time.</div>');
                        $('.spin-wheel-btn').prop('disabled', true).html('<i class="fas fa-ban mr-2"></i>No Prizes Available');
                    }
                } catch (error) {
                    console.error('Failed to load prizes:', error);
                    $('#prizesList').html('<div class="text-center text-red-300">Failed to load prizes. Please refresh the page.</div>');
                }
            }

            function updatePrizesList(prizes) {
                const $list = $('#prizesList');
                $list.empty();

                prizes.forEach(prize => {
                    const prizeItem = $(`
                        <div class="flex justify-between items-center bg-white/5 rounded-lg p-3">
                            <div>
                                <span class="text-white font-medium">${prize.name}</span>
                                <span class="text-blue-200 text-sm ml-2">(${prize.quantity} left)</span>
                            </div>
                            <div class="text-right">
                                <div class="text-green-400 font-bold">${prize.formatted_price}</div>
                                <div class="text-blue-300 text-xs">${prize.percentage}% chance</div>
                            </div>
                        </div>
                    `);
                    $list.append(prizeItem);
                });
            }
        });
    </script>
</body>

</html>