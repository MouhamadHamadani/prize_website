<?php
require_once 'config.php';
require_once 'database.php';

// Authentication functions
function isUserLoggedIn() {
    return isset($_SESSION[USER_SESSION_NAME]) && !empty($_SESSION[USER_SESSION_NAME]);
}

function isAdminLoggedIn() {
    return isset($_SESSION[ADMIN_SESSION_NAME]) && !empty($_SESSION[ADMIN_SESSION_NAME]);
}

function requireUserLogin() {
    if (!isUserLoggedIn()) {
        header('Location: /auth/login.php');
        exit;
    }
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function getCurrentUser() {
    global $db;
    if (!isUserLoggedIn()) {
        return null;
    }
    return $db->fetch("SELECT * FROM users WHERE id = ?", [$_SESSION[USER_SESSION_NAME]]);
}

function getCurrentAdmin() {
    global $db;
    if (!isAdminLoggedIn()) {
        return null;
    }
    return $db->fetch("SELECT * FROM admin_users WHERE id = ?", [$_SESSION[ADMIN_SESSION_NAME]]);
}

// Validation functions
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    return strlen($password) >= PASSWORD_MIN_LENGTH;
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Response functions
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function errorResponse($message, $status = 400) {
    jsonResponse(['error' => $message], $status);
}

function successResponse($data = [], $message = 'Success') {
    jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
}

// Prize calculation functions
function calculatePrizePercentages($method = 'wheel') {
    global $db;
    
    $column = $method === 'wheel' ? 'enabled_in_wheel' : 'enabled_in_box';
    $prizes = $db->fetchAll("SELECT * FROM prizes WHERE $column = 1 AND quantity > 0");
    
    $manualTotal = 0;
    $autoCount = 0;
    
    // Calculate manual percentages total and count auto prizes
    foreach ($prizes as $prize) {
        if ($prize['is_manual_percentage'] && $prize['percentage'] !== null) {
            $manualTotal += $prize['percentage'];
        } else {
            $autoCount++;
        }
    }
    
    // Calculate remaining percentage for auto prizes
    $remainingPercentage = MAX_PERCENTAGE - $manualTotal;
    $autoPercentage = $autoCount > 0 ? $remainingPercentage / $autoCount : 0;
    
    // Apply calculated percentages
    $finalPrizes = [];
    foreach ($prizes as $prize) {
        $finalPercentage = $prize['is_manual_percentage'] && $prize['percentage'] !== null 
            ? $prize['percentage'] 
            : $autoPercentage;
        
        $prize['final_percentage'] = $finalPercentage;
        $finalPrizes[] = $prize;
    }
    
    return $finalPrizes;
}

function selectRandomPrize($method = 'wheel') {
    $prizes = calculatePrizePercentages($method);
    
    if (empty($prizes)) {
        return null;
    }
    
    // Create weighted array
    $weightedPrizes = [];
    foreach ($prizes as $prize) {
        $weight = (int)($prize['final_percentage'] * 100); // Convert to integer for better precision
        for ($i = 0; $i < $weight; $i++) {
            $weightedPrizes[] = $prize;
        }
    }
    
    if (empty($weightedPrizes)) {
        return null;
    }
    
    // Select random prize
    $randomIndex = array_rand($weightedPrizes);
    return $weightedPrizes[$randomIndex];
}

function awardPrize($userId, $prizeId, $method) {
    global $db;
    
    try {
        $db->beginTransaction();
        
        // Check if prize is available
        $prize = $db->fetch("SELECT * FROM prizes WHERE id = ? AND quantity > 0", [$prizeId]);
        if (!$prize) {
            throw new Exception("Prize not available");
        }
        
        // Update prize statistics
        $db->query("UPDATE prizes SET quantity = quantity - 1, times_won = times_won + 1 WHERE id = ?", [$prizeId]);
        
        // Record user prize
        $db->query("INSERT INTO user_prizes (user_id, prize_id, method) VALUES (?, ?, ?)", [$userId, $prizeId, $method]);
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

// Utility functions
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>

