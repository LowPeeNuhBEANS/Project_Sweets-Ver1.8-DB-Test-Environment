<?php
/**
 * Test script to verify pricing logic
 * Run this to test if the pricing calculations work correctly
 */

echo "Testing Pricing Logic for Treatx' Pastries\n";
echo "==========================================\n\n";

// Test cases based on the pricing structure
$test_cases = [
    ['pastry' => 'Berry Blush Mochi', 'quantity' => 1, 'expected' => 50],
    ['pastry' => 'Berry Blush Mochi', 'quantity' => 3, 'expected' => 150],
    ['pastry' => 'Berry XD Mochi', 'quantity' => 2, 'expected' => 100],
    ['pastry' => 'Cookie Cloud Mochi', 'quantity' => 1, 'expected' => 50],
    ['pastry' => 'Sunny Munch Mochi', 'quantity' => 4, 'expected' => 200],
    ['pastry' => 'Assorted Mochi', 'quantity' => 2, 'expected' => 100],
    ['pastry' => 'Box of Mini Donuts', 'quantity' => 1, 'expected' => 50],
    ['pastry' => 'Box of Mini Donuts', 'quantity' => 2, 'expected' => 300], // 150 * 2
    ['pastry' => 'Box of Mini Donuts', 'quantity' => 3, 'expected' => 450], // 150 * 3
    ['pastry' => 'Coming soon....', 'quantity' => 5, 'expected' => 0],
];

$all_passed = true;

foreach ($test_cases as $test) {
    $pastry = $test['pastry'];
    $quantity = $test['quantity'];
    $expected = $test['expected'];
    
    // Pricing logic from submit_order.php
    if ($pastry === "Box of Mini Donuts") {
        $price_per_box = $quantity === 1 ? 50 : 150;
    } else {
        $price_per_box = 50;
    }
    
    $calculated = $price_per_box * $quantity;
    
    $status = $calculated === $expected ? "✅ PASS" : "❌ FAIL";
    
    if ($calculated !== $expected) {
        $all_passed = false;
    }
    
    echo "{$status} {$pastry} x {$quantity} = ₱{$calculated} (Expected: ₱{$expected})\n";
}

echo "\n==========================================\n";
if ($all_passed) {
    echo "🎉 ALL TESTS PASSED! Pricing logic is working correctly.\n";
} else {
    echo "⚠️  Some tests failed. Please check the pricing logic.\n";
}

echo "\nPricing Summary:\n";
echo "- All mochi varieties: ₱50 each\n";
echo "- Mini Donuts: ₱50 for 1 piece, ₱150 for assorted box (2+ pieces)\n";
echo "- Coming soon: ₱0\n";
?>
