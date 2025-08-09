<?php
require_once '../includes/functions.php';
requireAdminLogin();

$admin = getCurrentAdmin();
$prizes = $db->fetchAll("SELECT * FROM prizes ORDER BY id DESC");

// Calculate percentage totals for validation
$wheelTotal = 0;
$boxTotal = 0;
foreach ($prizes as $prize) {
    if ($prize['enabled_in_wheel'] && $prize['is_manual_percentage'] && $prize['percentage']) {
        $wheelTotal += $prize['percentage'];
    }
    if ($prize['enabled_in_box'] && $prize['is_manual_percentage'] && $prize['percentage']) {
        $boxTotal += $prize['percentage'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">
                    <i class="fas fa-shield-alt text-red-400"></i>
                    Admin Panel - <?php echo SITE_NAME; ?>
                </h1>
                <div class="flex items-center space-x-4">
                    <span>Welcome, <?php echo htmlspecialchars($admin['username']); ?>!</span>
                    <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-gift text-blue-500 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-2xl font-bold"><?php echo count($prizes); ?></h3>
                        <p class="text-gray-600">Total Prizes</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-percentage text-green-500 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-2xl font-bold <?php echo $wheelTotal > 100 ? 'text-red-500' : 'text-green-500'; ?>">
                            <?php echo number_format($wheelTotal, 1); ?>%
                        </h3>
                        <p class="text-gray-600">Wheel Manual %</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-percentage text-purple-500 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-2xl font-bold <?php echo $boxTotal > 100 ? 'text-red-500' : 'text-green-500'; ?>">
                            <?php echo number_format($boxTotal, 1); ?>%
                        </h3>
                        <p class="text-gray-600">Box Manual %</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-trophy text-yellow-500 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-2xl font-bold">
                            <?php echo array_sum(array_column($prizes, 'times_won')); ?>
                        </h3>
                        <p class="text-gray-600">Total Wins</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prize Management -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-cogs mr-2"></i>Prize Management
                    </h2>
                    <button id="addPrizeBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-300">
                        <i class="fas fa-plus mr-2"></i>Add Prize
                    </button>
                </div>
            </div>

            <!-- Add Prize Form (Hidden by default) -->
            <div id="addPrizeForm" class="p-6 border-b border-gray-200 bg-gray-50 hidden">
                <h3 class="text-lg font-semibold mb-4">Add New Prize</h3>
                <form id="newPrizeForm" class="grid md:grid-cols-6 gap-4">
                    <input type="text" name="name" placeholder="Prize Name" required class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="number" name="price" placeholder="Price" step="0.01" required class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="number" name="quantity" placeholder="Quantity" required class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="number" name="percentage" placeholder="%" step="0.1" class="px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="flex space-x-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="enabled_in_wheel" checked class="mr-1"> Wheel
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="enabled_in_box" checked class="mr-1"> Box
                        </label>
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition duration-300">
                        <i class="fas fa-save mr-1"></i>Save
                    </button>
                </form>
            </div>

            <!-- Prizes Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prize</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Times Won</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enabled In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="prizesTable" class="bg-white divide-y divide-gray-200">
                        <?php foreach ($prizes as $prize): ?>
                            <tr data-prize-id="<?php echo $prize['id']; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" class="editable border-0 bg-transparent w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white rounded px-2 py-1" 
                                           data-field="name" value="<?php echo htmlspecialchars($prize['name']); ?>">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" class="editable border-0 bg-transparent w-20 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white rounded px-2 py-1" 
                                           data-field="price" value="<?php echo $prize['price']; ?>" step="0.01">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" class="editable border-0 bg-transparent w-16 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white rounded px-2 py-1" 
                                           data-field="quantity" value="<?php echo $prize['quantity']; ?>">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <input type="number" class="editable border-0 bg-transparent w-16 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white rounded px-2 py-1" 
                                               data-field="percentage" value="<?php echo $prize['percentage']; ?>" step="0.1">
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" class="editable mr-1" data-field="is_manual_percentage" 
                                                   <?php echo $prize['is_manual_percentage'] ? 'checked' : ''; ?>>
                                            Manual
                                        </label>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo $prize['times_won']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" class="editable mr-1" data-field="enabled_in_wheel" 
                                                   <?php echo $prize['enabled_in_wheel'] ? 'checked' : ''; ?>>
                                            Wheel
                                        </label>
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" class="editable mr-1" data-field="enabled_in_box" 
                                                   <?php echo $prize['enabled_in_box'] ? 'checked' : ''; ?>>
                                            Box
                                        </label>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="delete-prize text-red-600 hover:text-red-900 transition duration-300" 
                                            data-prize-id="<?php echo $prize['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Toggle add prize form
            $('#addPrizeBtn').click(function() {
                $('#addPrizeForm').toggle();
            });

            // Add new prize
            $('#newPrizeForm').submit(function(e) {
                e.preventDefault();
                
                const formData = {
                    action: 'add',
                    name: $('input[name="name"]').val(),
                    price: $('input[name="price"]').val(),
                    quantity: $('input[name="quantity"]').val(),
                    percentage: $('input[name="percentage"]').val() || null,
                    enabled_in_wheel: $('input[name="enabled_in_wheel"]').is(':checked') ? 1 : 0,
                    enabled_in_box: $('input[name="enabled_in_box"]').is(':checked') ? 1 : 0
                };

                $.post('api.php', formData)
                    .done(function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    })
                    .fail(function() {
                        alert('Failed to add prize');
                    });
            });

            // Inline editing
            $('.editable').on('change blur', function() {
                const $this = $(this);
                const prizeId = $this.closest('tr').data('prize-id');
                const field = $this.data('field');
                let value = $this.val();
                
                if ($this.is(':checkbox')) {
                    value = $this.is(':checked') ? 1 : 0;
                }

                // Validate percentage
                if (field === 'percentage' && value && (value < 0 || value > 100)) {
                    alert('Percentage must be between 0 and 100');
                    return;
                }

                $.post('api.php', {
                    action: 'update',
                    id: prizeId,
                    field: field,
                    value: value
                })
                .done(function(response) {
                    if (!response.success) {
                        alert('Error: ' + response.message);
                        location.reload();
                    }
                })
                .fail(function() {
                    alert('Failed to update prize');
                    location.reload();
                });
            });

            // Delete prize
            $('.delete-prize').click(function() {
                if (!confirm('Are you sure you want to delete this prize?')) {
                    return;
                }

                const prizeId = $(this).data('prize-id');
                const $row = $(this).closest('tr');

                $.post('api.php', {
                    action: 'delete',
                    id: prizeId
                })
                .done(function(response) {
                    if (response.success) {
                        $row.remove();
                    } else {
                        alert('Error: ' + response.message);
                    }
                })
                .fail(function() {
                    alert('Failed to delete prize');
                });
            });
        });
    </script>
</body>
</html>

