<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login page/login.html");
    exit();
}
$sessionRole = isset($_SESSION['role']) ? strtolower(trim((string)$_SESSION['role'])) : '';
$sessionUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : -1;
$isAdminSession = ($sessionRole === 'admin' || $sessionUserId === 0);
if ($isAdminSession) {
    header("Location: ../dashboard/admin_dashboard_cleaned/index.php");
    exit();
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenGuru - Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .cart-grid {
            display: grid;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .cart-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .quantity-input {
            width: 40px;
            text-align: center;
            margin: 0 5px;
        }

        .btn {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-outline {
            background-color: white;
            color: #4CAF50;
            border: 1px solid #4CAF50;
        }

        .btn-quantity {
            background-color: #f2f2f2;
            color: #333;
            border: 1px solid #ddd;
            padding: 5px 10px;
            font-size: 14px;
        }

        .summary-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-total {
            font-weight: bold;
            font-size: 1.2em;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        #loading {
            text-align: center;
            font-size: 1.2em;
            margin-top: 20px;
        }

        .quantity-wrapper {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Cart</h1>
        <div id="loading">Loading cart...</div>
        <div class="cart-grid" id="cart-container" style="display: none;">
            <div class="cart-items">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cart-items">
                        <!-- Cart items will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
            <div class="summary-card">
                <h2>Order Summary</h2>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">₹0.00</span>
                </div>
                <div class="summary-row">
                    <span>Tax</span>
                    <span id="tax">₹0.00</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span id="total">₹0.00</span>
                </div>
                <a href="../Billing page/billing-page.html"    class="btn" style="width: 89%; margin-top: 20px;">Proceed to Checkout</a>
            </div>
        </div>
    </div>

    <script>
        let cartItems = [];
        const userId = <?php echo $user_id; ?>;

        async function loadCart() {
            try {
                const response = await fetch('get_cart.php?user_id=' + userId);
                if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                
                const items = await response.json();

                if (items.error) {
                    console.error(items.error);
                    alert('Error fetching cart items');
                    return;
                }

                cartItems = items;
                renderCartItems();
                document.getElementById('loading').style.display = 'none';
                document.getElementById('cart-container').style.display = 'grid';
            } catch (error) {
                console.error('Error loading cart:', error);
                alert('An unexpected error occurred');
            }
        }

        function renderCartItems() {
            const cartItemsElement = document.getElementById('cart-items');
            if (cartItems.length === 0) {
                cartItemsElement.innerHTML = '<tr><td colspan="5">Your cart is empty</td></tr>';
            } else {
                cartItemsElement.innerHTML = cartItems.map(item => {
                    const price = parseFloat(item.price) || 0;
                    const total = price * item.quantity;

                    return `
                        <tr>
                            <td>
                                <img src="../Product Page/${item.image}" alt="${item.name}" class="product-image">
                                ${item.name}
                            </td>
                            <td>₹${price.toFixed(2)}</td>
                            <td>
                                <div class="quantity-wrapper">
                                    <button class="btn-quantity decrement" data-index="${item.product_index_no}">-</button>
                                    <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-index="${item.product_index_no}">
                                    <button class="btn-quantity increment" data-index="${item.product_index_no}">+</button>
                                </div>
                            </td>
                            <td>₹${total.toFixed(2)}</td>
                            <td><button class="btn btn-outline remove-item" data-index="${item.product_index_no}">Remove</button></td>
                        </tr>
                    `;
                }).join('');
            }
            updateSummary();
        }

        function updateSummary() {
            const subtotal = cartItems.reduce((sum, item) => sum + parseFloat(item.price) * item.quantity, 0);
            const tax = subtotal * 0.1; // Assuming 10% tax
            const total = subtotal + tax;

            document.getElementById('subtotal').textContent = `₹${subtotal.toFixed(2)}`;
            document.getElementById('tax').textContent = `₹${tax.toFixed(2)}`;
            document.getElementById('total').textContent = `₹${total.toFixed(2)}`;
        }

        document.addEventListener('DOMContentLoaded', loadCart);


        document.getElementById('cart-items').addEventListener('click', async function (e) {
            const target = e.target;

            if (target.classList.contains('remove-item')) {
                const productIndexNo = target.dataset.index;

                try {
                    const response = await fetch('remove_cart_item.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            product_index_no: productIndexNo
                        })
                    });

                    const result = await response.json();

if (result.success) {
    // Reload cart data from the server
    await loadCart();
    alert('Item removed from cart');
} else {
    alert('Failed to remove item');
}
                }catch (error) {
                    console.error('Error removing item:', error);
                    alert('An unexpected error occurred');
                }
            }
            else if (target.classList.contains('increment') || target.classList.contains('decrement')) {
                const productIndexNo = target.dataset.index;
                const item = cartItems.find(item => item.product_index_no == productIndexNo);
                if (item) {
                    if (target.classList.contains('increment')) {
                        item.quantity++;
                    } else if (item.quantity > 1) {
                        item.quantity--;
                    }
                    await updateCartItemQuantity(productIndexNo, item.quantity);
                    renderCartItems();
                }
            }
        });

        document.getElementById('cart-items').addEventListener('change', async function (e) {
            if (e.target.classList.contains('quantity-input')) {
                const productIndexNo = e.target.dataset.index;
                const newQuantity = parseInt(e.target.value);
                if (newQuantity > 0) {
                    await updateCartItemQuantity(productIndexNo, newQuantity);
                    renderCartItems();
                } else {
                    e.target.value = 1;
                }
            }
        });

        async function updateCartItemQuantity(productIndexNo, quantity) {
            const userId = <?php echo $user_id; ?>; // Replace with the actual user ID

            try {
                const response = await fetch('update_cart_quantity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        product_index_no: productIndexNo,
                        quantity: quantity
                    })
                });

                const result = await response.json();

                if (result.success) {
                    const item = cartItems.find(item => item.product_index_no == productIndexNo);
                    if (item) {
                        item.quantity = quantity;
                    }
                } else {
                    alert('Failed to update quantity');
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                alert('An unexpected error occurred');
            }
        }
        document.querySelector('.btn').addEventListener('click', () => {
            updateSummary();
            const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace('₹', ''));
            const tax = parseFloat(document.getElementById('tax').textContent.replace('₹', ''));
            const total = parseFloat(document.getElementById('total').textContent.replace('₹', ''));

            // Store values in localStorage
            localStorage.setItem('orderSummary', JSON.stringify({ subtotal, tax, total, userId }));
        });

        // Load cart when the page loads
        document.addEventListener('DOMContentLoaded', loadCart);
    </script>
</body>
</html>

