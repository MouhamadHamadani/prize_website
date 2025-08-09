<?php
require_once 'includes/functions.php';

// Redirect logged-in users to dashboard
if (isUserLoggedIn()) {
    redirect('/user/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Win Amazing Prizes!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-600 via-blue-600 to-indigo-800 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="text-center mb-12">
            <h1 class="text-6xl font-bold text-white mb-4">
                <i class="fas fa-gift text-yellow-400"></i>
                <?php echo SITE_NAME; ?>
            </h1>
            <p class="text-xl text-blue-100">Win amazing prizes with our Wheel of Fortune and Mystery Box!</p>
        </header>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto">
            <!-- Prize Methods -->
            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <!-- Wheel of Fortune -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 text-center border border-white/20">
                    <div class="text-6xl mb-4">🎡</div>
                    <h2 class="text-3xl font-bold text-white mb-4">Wheel of Fortune</h2>
                    <p class="text-blue-100 mb-6">Spin the wheel and let luck decide your prize!</p>
                    <a href="/auth/login.php" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                        Try Your Luck!
                    </a>
                </div>

                <!-- Mystery Box -->
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 text-center border border-white/20">
                    <div class="text-6xl mb-4">📦</div>
                    <h2 class="text-3xl font-bold text-white mb-4">Mystery Box</h2>
                    <p class="text-blue-100 mb-6">Open a mystery box and discover what's inside!</p>
                    <a href="/auth/login.php" class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                        Open Box!
                    </a>
                </div>
            </div>

            <!-- Features -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-12 border border-white/20">
                <h3 class="text-2xl font-bold text-white mb-6 text-center">Why Choose Us?</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <i class="fas fa-trophy text-yellow-400 text-3xl mb-3"></i>
                        <h4 class="text-lg font-semibold text-white mb-2">Amazing Prizes</h4>
                        <p class="text-blue-100 text-sm">Win iPhones, gift cards, and more!</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-shield-alt text-green-400 text-3xl mb-3"></i>
                        <h4 class="text-lg font-semibold text-white mb-2">Fair & Secure</h4>
                        <p class="text-blue-100 text-sm">Transparent odds and secure platform</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-clock text-blue-400 text-3xl mb-3"></i>
                        <h4 class="text-lg font-semibold text-white mb-2">Instant Results</h4>
                        <p class="text-blue-100 text-sm">Know your prize immediately!</p>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center">
                <h3 class="text-3xl font-bold text-white mb-4">Ready to Win?</h3>
                <p class="text-blue-100 mb-8">Join thousands of winners and try your luck today!</p>
                <div class="space-x-4">
                    <a href="/auth/register.php" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-full transition duration-300 transform hover:scale-105">
                        Sign Up Now
                    </a>
                    <a href="/auth/login.php" class="inline-block bg-transparent border-2 border-white text-white hover:bg-white hover:text-purple-600 font-bold py-3 px-8 rounded-full transition duration-300">
                        Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-16 text-blue-200">
            <p>&copy; 2024 <?php echo SITE_NAME; ?>. All rights reserved.</p>
            <p class="mt-2">
                <a href="/admin/login.php" class="text-blue-300 hover:text-white transition duration-300">Admin Login</a>
            </p>
        </footer>
    </div>
</body>
</html>

