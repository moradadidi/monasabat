<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_product'])) {
        $id_product = $_POST['id_product'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM cart WHERE id_user = :id_user AND id_product = :id_product");
            $stmt->execute(['id_user' => $id_user, 'id_product' => $id_product]);
            $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cart_item) {
                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id_user = :id_user AND id_product = :id_product");
                $stmt->execute(['id_user' => $id_user, 'id_product' => $id_product]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO cart (id_user, id_product, quantity) VALUES (:id_user, :id_product, 1)");
                $stmt->execute(['id_user' => $id_user, 'id_product' => $id_product]);
            }

            header("Location: cart.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST["del"])) {
        $id_cart = $_POST["id_cart"];
        try {
            $del_order = $pdo->prepare("DELETE FROM cart WHERE id_cart = ?");
            $del_order->execute([$id_cart]);
            header("Location: cart.php");
            exit();
            echo json_encode(["status" => "success"]);
            exit();
        } catch (PDOException $e) {
            header("Location: cart.php");
            exit();
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit();
        }
    } elseif (isset($_POST['update_quantity'])) {
        $id_cart = $_POST['id_cart'];
        $quantity = $_POST['quantity'];

        try {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE id_cart = :id_cart");
            $stmt->execute(['quantity' => $quantity, 'id_cart' => $id_cart]);
            echo json_encode(["status" => "success"]);
            exit();
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
}
?>
<section class="home bg-gray-100 pt-12">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-semibold mb-6 border-b pb-4">Shopping Cart</h2>
            <div>
                <?php
                $stmt = $pdo->prepare("SELECT cart.*, products.nom_product, products.photo, products.price, products.quantity as max
                                                FROM cart 
                                                JOIN products ON cart.id_product = products.id_product 
                                                WHERE cart.id_user = :id_user");
                $stmt->execute(['id_user' => $id_user]);
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $total_price = 0;
                ?>

                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="p-4 text-left">Product</th>
                            <th class="p-4 text-right">Price</th>
                            <th class="p-4 text-center">Quantity</th>
                            <th class="p-4 text-right">Total</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($cart_items as $item) {
                            $product_total = $item['price'] * $item['quantity'];
                            $total_price += $product_total;
                        ?>
                            <tr class="border-b">
                                <td class="p-4 flex items-center">
                                    <img src="../admin/<?= htmlspecialchars($item['photo']); ?>" alt="<?= htmlspecialchars($item['nom_product']); ?>" class="product-image cursor-pointer w-24 h-24 object-cover rounded-lg mr-4" data-product-id="<?= htmlspecialchars($item['id_product']); ?>">
                                    <div>
                                        <h3 class="text-lg font-medium"><?= htmlspecialchars($item['nom_product']); ?></h3>
                                    </div>
                                </td>
                                <td class="p-4 text-right">$<?= htmlspecialchars(number_format($item['price'], 2)); ?></td>
                                <td class="p-4 text-center">
                                    <form action="cart.php" method="post" class="inline-block" id="form-<?= htmlspecialchars($item['id_cart']); ?>">
                                        <input type="hidden" name="id_cart" value="<?= htmlspecialchars($item['id_cart']); ?>">
                                        <input type="hidden" name="token" value="<?= $token; ?>">
                                        <div class="flex items-center justify-center">
                                            <button type="button" class="decrease-quantity text-lg bg-gray-300 px-2 py-1 rounded-l-md" data-id="<?= htmlspecialchars($item['id_cart']); ?>">-</button>
                                            <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']); ?>" min="1" max="<?= htmlspecialchars($item['max']); ?>" class="form-input w-12 text-center border-gray-300" id="quantity-<?= htmlspecialchars($item['id_cart']); ?>">
                                            <button type="button" class="increase-quantity text-lg bg-gray-300 px-2 py-1 rounded-r-md" data-id="<?= htmlspecialchars($item['id_cart']); ?>">+</button>
                                        </div>
                                        <button type="submit" name="update_quantity" class="hidden">Update</button>
                                    </form>
                                </td>
                                <td class="p-4 text-right text-xl font-semibold">$<?= number_format($product_total, 2); ?></td>
                                <td class="p-4 text-center">
                                    <form action="cart.php" method="post">
                                        <input type="hidden" name="id_cart" value="<?= htmlspecialchars($item['id_cart']); ?>">
                                        <input type="hidden" name="token" value="<?= $token; ?>">
                                        <button type="submit" name="del" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-2xl font-semibold mb-4 border-b pb-4">Order Summary</h2>
            <div class="flex justify-between py-4 border-b">
                <span class="font-medium">Subtotal</span>
                <span class="text-lg font-semibold text-gray-700">$<?= number_format($total_price, 2); ?></span>
            </div>
            <div class="flex justify-between py-4 border-b">
                <span class="font-medium">Shipping estimate</span>
                <span class="text-lg font-semibold text-gray-700">$5.00</span>
            </div>
            <div class="flex justify-between py-4 border-b">
                <span class="font-medium">Tax estimate</span>
                <span class="text-lg font-semibold text-gray-700">$8.32</span>
            </div>
            <div class="flex justify-between py-4 font-semibold text-xl">
                <span>Order total</span>
                <span class="text-gray-800">$<?= number_format($total_price + 5.00 + 8.32, 2); ?></span>
            </div>
            <a href="payement.php" class="bg-indigo-600 text-white text-center py-3 px-6 rounded-md hover:bg-indigo-700 transition block mt-6">Checkout</a>
        </div>
    </div>
    <?php include_once("../includes/footer.php"); ?>
</section>

<script>
document.querySelectorAll('.product-image').forEach(image => {
    image.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        window.location.href = 'selected_product.php?id=' + productId;
    });
});

document.querySelectorAll('.decrease-quantity').forEach(button => {
    button.addEventListener('click', function() {
        const cartId = this.getAttribute('data-id');
        const input = document.getElementById('quantity-' + cartId);
        let quantity = parseInt(input.value);
        if (quantity > 1) {
            input.value = quantity - 1;
            updateQuantity(cartId, quantity - 1);
        }
    });
});

document.querySelectorAll('.increase-quantity').forEach(button => {
    button.addEventListener('click', function() {
        const cartId = this.getAttribute('data-id');
        const input = document.getElementById('quantity-' + cartId);
        let quantity = parseInt(input.value);
        const max = parseInt(input.getAttribute('max'));
        if (quantity < max) {
            input.value = quantity + 1;
            updateQuantity(cartId, quantity + 1);
        }
    });
});

function updateQuantity(cartId, quantity) {
    const form = document.getElementById('form-' + cartId);
    const formData = new FormData(form);
    formData.set('quantity', quantity);
    formData.append('update_quantity', 'true');

    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            console.log('Quantity updated successfully');
            // Optionally update the total price here without reloading
        } else {
            console.error('Error:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

<style>
.decrease-quantity, .increase-quantity {
    cursor: pointer;
}
</style>
