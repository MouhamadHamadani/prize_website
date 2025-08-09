<?php
require_once '../includes/functions.php';
requireAdminLogin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            $name = sanitizeInput($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 0);
            $percentage = !empty($_POST['percentage']) ? floatval($_POST['percentage']) : null;
            $enabledInWheel = intval($_POST['enabled_in_wheel'] ?? 0);
            $enabledInBox = intval($_POST['enabled_in_box'] ?? 0);
            
            if (empty($name) || $price < 0 || $quantity < 0) {
                throw new Exception('Invalid input data');
            }
            
            if ($percentage !== null && ($percentage < 0 || $percentage > 100)) {
                throw new Exception('Percentage must be between 0 and 100');
            }
            
            $isManualPercentage = $percentage !== null ? 1 : 0;
            
            // Validate percentage totals
            if ($percentage !== null) {
                if ($enabledInWheel) {
                    $wheelTotal = $db->fetch("
                        SELECT COALESCE(SUM(percentage), 0) as total 
                        FROM prizes 
                        WHERE enabled_in_wheel = 1 AND is_manual_percentage = 1 AND percentage IS NOT NULL
                    ")['total'];
                    
                    if ($wheelTotal + $percentage > 100) {
                        throw new Exception('Wheel percentage total would exceed 100%');
                    }
                }
                
                if ($enabledInBox) {
                    $boxTotal = $db->fetch("
                        SELECT COALESCE(SUM(percentage), 0) as total 
                        FROM prizes 
                        WHERE enabled_in_box = 1 AND is_manual_percentage = 1 AND percentage IS NOT NULL
                    ")['total'];
                    
                    if ($boxTotal + $percentage > 100) {
                        throw new Exception('Box percentage total would exceed 100%');
                    }
                }
            }
            
            $db->query("
                INSERT INTO prizes (name, price, quantity, percentage, is_manual_percentage, enabled_in_wheel, enabled_in_box) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ", [$name, $price, $quantity, $percentage, $isManualPercentage, $enabledInWheel, $enabledInBox]);
            
            successResponse(['id' => $db->lastInsertId()], 'Prize added successfully');
            break;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $field = $_POST['field'] ?? '';
            $value = $_POST['value'] ?? '';
            
            if (!$id || !$field) {
                throw new Exception('Invalid parameters');
            }
            
            // Get current prize data
            $prize = $db->fetch("SELECT * FROM prizes WHERE id = ?", [$id]);
            if (!$prize) {
                throw new Exception('Prize not found');
            }
            
            // Validate field
            $allowedFields = ['name', 'price', 'quantity', 'percentage', 'is_manual_percentage', 'enabled_in_wheel', 'enabled_in_box'];
            if (!in_array($field, $allowedFields)) {
                throw new Exception('Invalid field');
            }
            
            // Type conversion and validation
            switch ($field) {
                case 'name':
                    $value = sanitizeInput($value);
                    if (empty($value)) {
                        throw new Exception('Name cannot be empty');
                    }
                    break;
                case 'price':
                    $value = floatval($value);
                    if ($value < 0) {
                        throw new Exception('Price cannot be negative');
                    }
                    break;
                case 'quantity':
                    $value = intval($value);
                    if ($value < 0) {
                        throw new Exception('Quantity cannot be negative');
                    }
                    break;
                case 'percentage':
                    if ($value === '' || $value === null) {
                        $value = null;
                    } else {
                        $value = floatval($value);
                        if ($value < 0 || $value > 100) {
                            throw new Exception('Percentage must be between 0 and 100');
                        }
                        
                        // Validate percentage totals
                        if ($prize['enabled_in_wheel']) {
                            $wheelTotal = $db->fetch("
                                SELECT COALESCE(SUM(percentage), 0) as total 
                                FROM prizes 
                                WHERE enabled_in_wheel = 1 AND is_manual_percentage = 1 AND percentage IS NOT NULL AND id != ?
                            ", [$id])['total'];
                            
                            if ($wheelTotal + $value > 100) {
                                throw new Exception('Wheel percentage total would exceed 100%');
                            }
                        }
                        
                        if ($prize['enabled_in_box']) {
                            $boxTotal = $db->fetch("
                                SELECT COALESCE(SUM(percentage), 0) as total 
                                FROM prizes 
                                WHERE enabled_in_box = 1 AND is_manual_percentage = 1 AND percentage IS NOT NULL AND id != ?
                            ", [$id])['total'];
                            
                            if ($boxTotal + $value > 100) {
                                throw new Exception('Box percentage total would exceed 100%');
                            }
                        }
                    }
                    break;
                case 'is_manual_percentage':
                case 'enabled_in_wheel':
                case 'enabled_in_box':
                    $value = intval($value);
                    break;
            }
            
            // Special handling for percentage field changes
            if ($field === 'percentage') {
                // If setting a percentage value, mark as manual
                if ($value !== null) {
                    $db->query("UPDATE prizes SET percentage = ?, is_manual_percentage = 1 WHERE id = ?", [$value, $id]);
                } else {
                    // If clearing percentage, mark as auto-calculated
                    $db->query("UPDATE prizes SET percentage = NULL, is_manual_percentage = 0 WHERE id = ?", [$id]);
                }
            } elseif ($field === 'is_manual_percentage') {
                // If switching to auto-calculated, clear percentage
                if ($value == 0) {
                    $db->query("UPDATE prizes SET percentage = NULL, is_manual_percentage = 0 WHERE id = ?", [$id]);
                } else {
                    $db->query("UPDATE prizes SET is_manual_percentage = 1 WHERE id = ?", [$id]);
                }
            } else {
                $db->query("UPDATE prizes SET $field = ? WHERE id = ?", [$value, $id]);
            }
            
            successResponse([], 'Prize updated successfully');
            break;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            
            if (!$id) {
                throw new Exception('Invalid prize ID');
            }
            
            // Check if prize has been won
            $hasWins = $db->fetch("SELECT COUNT(*) as count FROM user_prizes WHERE prize_id = ?", [$id])['count'];
            if ($hasWins > 0) {
                throw new Exception('Cannot delete prize that has been won by users');
            }
            
            $db->query("DELETE FROM prizes WHERE id = ?", [$id]);
            
            successResponse([], 'Prize deleted successfully');
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    errorResponse($e->getMessage());
}
?>

