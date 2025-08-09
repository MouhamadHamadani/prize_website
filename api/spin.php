<?php
require_once '../includes/functions.php';
requireUserLogin();

header('Content-Type: application/json');

$method = $_POST['method'] ?? '';
$user = getCurrentUser();

if (!in_array($method, ['wheel', 'box'])) {
    errorResponse('Invalid method');
}

try {
    // Select random prize based on method
    $selectedPrize = selectRandomPrize($method);
    
    if (!$selectedPrize) {
        errorResponse('No prizes available at this time');
    }
    
    // Award the prize to the user
    $success = awardPrize($user['id'], $selectedPrize['id'], $method);
    
    if (!$success) {
        errorResponse('Failed to award prize. Please try again.');
    }
    
    // Return prize information
    successResponse([
        'prize' => [
            'id' => $selectedPrize['id'],
            'name' => $selectedPrize['name'],
            'price' => $selectedPrize['price'],
            'formatted_price' => formatPrice($selectedPrize['price'])
        ],
        'method' => $method
    ], 'Congratulations! You won a prize!');
    
} catch (Exception $e) {
    errorResponse('An error occurred: ' . $e->getMessage());
}
?>

