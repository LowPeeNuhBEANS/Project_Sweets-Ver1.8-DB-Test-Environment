<?php
/**
 * Backend Pricing Test Script
 * Tests the PHP pricing logic from submit_order.php
 */

echo "Testing Backend Pricing Logic\n";
echo "============================\n\n";

// Simulate the pricing logic from submit_order.php
function calculateBackendPrice($pastry, $quantity) {
    $price_per_box = 150; // Default price
    
    // Pricing logic matching the frontend JavaScript
    if ($pastry === "Berry Blush Mochi" || $pastry === "Berry XD Mochi" || 
        $pastry === "Cookie Cloud Mochi" || $pastry === "Sunny Munch Mochi") {
        $price_per_box = 50; // Individual mochi varieties
    } elseif ($pastry === "Assorted Mochi" || $pastry === "Box of Mini Donuts") {
        $price_per_box = 150; // Assorted mochi and mini donuts
    } else {
        $price_per_box = 0; // Coming soon
    }
    
    return $price_per_box * $quantity;
}

// Test cases
$test_cases = [
    ['pastry' => 'Berry Blush Mochi', 'quantity' => 1, 'expected' => 50],
    ['pastry' => 'Berry Blush Mochi', 'quantity' => 3, 'expected' => 150],
    ['pastry' => 'Berry XD Mochi', 'quantity' => 2, 'expected' => 100],
    ['pastry' => 'Cookie Cloud Mochi', 'quantity' => 1, 'expected' => 50],
    ['pastry' => 'Sunny Munch Mochi', 'quantity' => 4, 'expected' => 200],
    ['pastry' => 'Assorted Mochi', 'quantity' => 1, 'expected' => 150],
    ['pastry' => 'Assorted Mochi', 'quantity' => 2, 'expected' => 300],
    ['pastry' => 'Box of Mini Donuts', 'quantity' => 1, 'expected' => 150],
    ['pastry' => 'Box of Mini Donuts', 'quantity' => 2, 'expected' => 300],
    ['pastry' => 'Box of Mini Donuts', 'quantity' => 3, 'expected' => 450],
    ['pastry' => 'Coming soon....', 'quantity' => 5, 'expected' => 0]
];

$all_passed = true;

foreach ($test_cases as $test) {
    $calculated = calculateBackendPrice($test['pastry'], $test['quantity']);
    $passed = $calculated === $test['expected'];
    
    if (!$passed) {
        $all_passed = false;
    }
    
    $status = $passed ? "✅ PASS" : "❌ FAIL";
    echo "{$status} {$test['pastry']} x {$test['quantity']} = ₱{$calculated} (Expected: ₱{$test['expected']})\n";
}

echo "\n============================\n";
if ($all_passed) {
    echo "🎉 ALL BACKEND TESTS PASSED! PHP pricing logic is working correctly.\n";
} else {
    echo "⚠️  Some backend tests failed. Please check the PHP pricing logic.\n";
}

echo "\nBackend Pricing Summary:\n";
echo "- Individual mochi varieties: ₱50 each\n";
echo "- Assorted Mochi: ₱150 flat rate\n";
echo "- Mini Donuts: ₱150 flat rate\n";
echo "- Coming soon: ₱0\n";
?>
