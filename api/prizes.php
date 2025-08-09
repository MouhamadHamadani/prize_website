<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

$method = $_GET['method'] ?? 'wheel';

if (!in_array($method, ['wheel', 'box'])) {
    errorResponse('Invalid method');
}

try {
    // Get prizes with calculated percentages
    $prizes = calculatePrizePercentages($method);
    
    // Format prizes for frontend
    $formattedPrizes = [];
    foreach ($prizes as $prize) {
        $formattedPrizes[] = [
            'id' => $prize['id'],
            'name' => $prize['name'],
            'price' => $prize['price'],
            'formatted_price' => formatPrice($prize['price']),
            'percentage' => round($prize['final_percentage'], 1),
            'quantity' => $prize['quantity']
        ];
    }
    
    successResponse([
        'prizes' => $formattedPrizes,
        'method' => $method,
        'total_prizes' => count($formattedPrizes)
    ]);
    
} catch (Exception $e) {
    errorResponse('Failed to load prizes: ' . $e->getMessage());
}
?>

