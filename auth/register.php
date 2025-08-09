<?php
require_once '../includes/functions.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    redirect('/user/dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address.';
    } elseif (!validatePassword($password)) {
        $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $existingUser = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existingUser) {
            $error = 'Email address is already registered.';
        } else {
            // Create new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            try {
                $db->query("INSERT INTO users (name, email, password) VALUES (?, ?, ?)", 
                    [$name, $email, $hashedPassword]);
                $success = 'Registration successful! You can now login.';
            } catch (Exception $e) {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-600 via-blue-600 to-indigo-800 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">
                    <i class="fas fa-gift text-yellow-400"></i>
                    <?php echo SITE_NAME; ?>
                </h1>
                <p class="text-blue-100">Create your account to start winning!</p>
            </div>

            <!-- Registration Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 border border-white/20">
                <h2 class="text-2xl font-bold text-white mb-6 text-center">Sign Up</h2>
                
                <?php if ($error): ?>
                    <div class="bg-red-500/20 border border-red-500 text-red-100 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-500/20 border border-green-500 text-green-100 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="name" class="block text-white font-medium mb-2">
                            <i class="fas fa-user mr-2"></i>Full Name
                        </label>
                        <input type="text" id="name" name="name" required
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                               class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50"
                               placeholder="Enter your full name">
                    </div>

                    <div>
                        <label for="email" class="block text-white font-medium mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email Address
                        </label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50"
                               placeholder="Enter your email">
                    </div>

                    <div>
                        <label for="password" class="block text-white font-medium mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50"
                               placeholder="Enter your password">
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-white font-medium mb-2">
                            <i class="fas fa-lock mr-2"></i>Confirm Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full px-4 py-3 bg-white/10 border border-white/30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/50"
                               placeholder="Confirm your password">
                    </div>

                    <button type="submit" 
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 px-6 rounded-lg transition duration-300 transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-blue-100">
                        Already have an account? 
                        <a href="login.php" class="text-yellow-400 hover:text-yellow-300 font-medium">Sign In</a>
                    </p>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-6">
                <a href="/" class="text-blue-200 hover:text-white transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                const password = $('#password').val();
                const confirmPassword = $('#confirm_password').val();
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }
                
                if (password.length < <?php echo PASSWORD_MIN_LENGTH; ?>) {
                    e.preventDefault();
                    alert('Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long!');
                    return false;
                }
            });
        });
    </script>
</body>
</html>

