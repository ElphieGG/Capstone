<?php
session_start();
include('config.php'); // Assuming you need DB connection

// Initialize cart session if not yet created
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If a product is added
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = trim($_POST['product_name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    // Validate required fields
    if ($product_id && $product_name && $price && $quantity) {

        // Check if product already exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] === $product_id) {
                // Product exists, update quantity
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        unset($item); // Break reference after foreach

        if (!$found) {
            // Add new product
            $_SESSION['cart'][] = [
                'product_id' => $product_id,
                'product_name' => $product_name,
                'price' => $price,
                'quantity' => $quantity
            ];
        }

        echo "<script>alert('Product added to cart!'); window.location.href='cart.php';</script>";
        exit();
    } else {
        echo "<script>alert('Invalid product data.'); window.location.href='products.php';</script>";
        exit();
    }
}

// If user wants to remove an item
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
    echo "<script>alert('Item removed from cart.'); window.location.href='cart.php';</script>";
    exit();
}

// View Cart
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f8; padding: 20px; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; }
        th, td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        th { background: #38ef7d; color: white; }
        a.btn { padding: 8px 12px; background: red; color: white; border-radius: 5px; text-decoration: none; }
        a.btn:hover { background: darkred; }
        .checkout-btn { background: #38ef7d; padding: 10px 20px; color: white; text-decoration: none; border-radius: 5px; }
        .checkout-btn:hover { background: #2ecc71; }
    </style>
</head>
<body>

<h2 style="text-align:center;">My Shopping Cart</h2>

<?php if (!empty($_SESSION['cart'])): ?>
<table>
    <tr>
        <th>Product Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Action</th>
    </tr>
    <?php
    $grand_total = 0;
    foreach ($_SESSION['cart'] as $item):
        $total = $item['price'] * $item['quantity'];
        $grand_total += $total;
    ?>
    <tr>
        <td><?= htmlspecialchars($item['product_name']); ?></td>
        <td><?= number_format($item['price'], 2); ?></td>
        <td><?= $item['quantity']; ?></td>
        <td><?= number_format($total, 2); ?></td>
        <td><a href="cart.php?remove=<?= $item['product_id']; ?>" class="btn" onclick="return confirm('Remove this item?')">Remove</a></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3" style="text-align:right;"><strong>Grand Total:</strong></td>
        <td colspan="2"><strong><?= number_format($grand_total, 2); ?> SAR</strong></td>
    </tr>
</table>

<div style="text-align:center; margin-top:20px;">
    <a href="checkout_form.php" class="checkout-btn">Proceed to Checkout</a>
</div>

<?php else: ?>
<p style="text-align:center;">Your cart is empty.</p>
<?php endif; ?>

</body>
</html>
