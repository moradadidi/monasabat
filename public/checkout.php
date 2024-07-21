<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_proprietaire = $_POST['nom_proprietaire'];
    $phone_number = $_POST['phone_number'];
    $street_address = $_POST['street_address'];
    $apt_suite = $_POST['apt_suite'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $payment_method = $_POST['payment_method'];

    try {
        $shipping_address = $street_address . ", " . $apt_suite . ", " . $city . ", " . $state . ", " . $zip_code;
        $stmt = $pdo->prepare("INSERT INTO orders (id_user, nom_proprietaire, phone_number, shipping_address, payment_method) VALUES (:id_user, :nom_proprietaire, :phone_number, :shipping_address, :payment_method)");
        $stmt->execute([
            'id_user' => $id_user,
            'nom_proprietaire' => $nom_proprietaire,
            'phone_number' => $phone_number,
            'shipping_address' => $shipping_address,
            'payment_method' => $payment_method
        ]);
        $order_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("SELECT * FROM cart WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, id_product, quantity, price) VALUES (:order_id, :id_product, :quantity, :price)");
            $stmt->execute([
                'order_id' => $order_id,
                'id_product' => $item['id_product'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        $stmt = $pdo->prepare("DELETE FROM cart WHERE id_user = :id_user");
        $stmt->execute(['id_user' => $id_user]);

        header("Location: confirmation.php?order_id=" . $order_id);
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Checkout</title>
</head>
<style>
  .home {
    background-color: #ffffff;
    background-image: radial-gradient(at 12% 45%, #32CD32 40%, transparent 20%),
                     radial-gradient(at 62% 33%, #ff7a00 50%, transparent 50%);
  }
</style>
<body class="bg-gray-100">
    <section class="home pt-12">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white text-xl font-bold shadow-lg rounded-lg p-8">
                <h2 class="text-3xl font-semibold mb-6 border-b pb-4">Checkout</h2>
                <form id="checkout-form" action="checkout.php" method="post" onsubmit="return confirmOrder(event)">
                    <div class="mb-6">
                        <label for="nom_proprietaire" class="block text-gray-700">Full name (First and Last name)</label>
                        <input type="text" name="nom_proprietaire" id="nom_proprietaire" required class="w-full p-2 border rounded-lg mb-4">
                    </div>
                    <div class="mb-6">
                        <label for="phone_number" class="block text-gray-700">Phone number</label>
                        <input type="text" name="phone_number" id="phone_number" required class="w-full p-2 border rounded-lg mb-4">
                    </div>
                    <div class="mb-6">
                        <label for="street_address" class="block text-gray-700">Street address or P.O. Box</label>
                        <input type="text" name="street_address" id="street_address" required class="w-full p-2 border rounded-lg mb-4">
                    </div>
                    <div class="mb-6">
                        <label for="apt_suite" class="block text-gray-700">Apt, suite, unit, building, floor, etc.</label>
                        <input type="text" name="apt_suite" id="apt_suite" class="w-full p-2 border rounded-lg mb-4">
                    </div>
                    <div class="mb-6">
                        <label for="city" class="block text-gray-700">City</label>
                        <input type="text" name="city" id="city" required class="w-full p-2 border rounded-lg mb-4">
                    </div>
                    <div class="mb-6">
                        <label for="state" class="block text-gray-700">State</label>
                        <select name="state" id="state" required class="w-full p-2 border rounded-lg mb-4">
                            <option value="">Select</option>
                            <option value="NY">New York</option>
                            <option value="CA">California</option>
                            <!-- Add other states as needed -->
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="zip_code" class="block text-gray-700">ZIP Code</label>
                        <input type="text" name="zip_code" id="zip_code" required class="w-full p-2 border rounded-lg mb-4">
                    </div>
                    <div class="mb-6">
                        <label for="payment_method" class="block text-gray-700">Payment Method</label>
                        <select name="payment_method" id="payment_method" required class="w-full p-2 border rounded-lg mb-4">
                            <option value="credit_card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-green-600 text-white py-3 px-6 rounded-md hover:bg-green-700 transition">Place Order</button>
                </form>
            </div>
            <div class="bg-white text-xl font-bold shadow-lg rounded-lg p-8 order-summary">
                <h2 class="text-2xl font-semibold mb-4 border-b pb-4">Order Summary</h2>
                <?php
                $stmt = $pdo->prepare("SELECT cart.*, products.nom_product, products.price , products.photo FROM cart JOIN products ON cart.id_product = products.id_product WHERE cart.id_user = :id_user");
                $stmt->execute(['id_user' => $id_user]);
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $total_price = 0;

                foreach ($cart_items as $item) {
                    $product_total = $item['price'] * $item['quantity'];
                    $total_price += $product_total;
                ?>
                    <div class="flex justify-between py-4 border-b">
                        <div class="flex items-center">
                            <img src="../admin/<?= htmlspecialchars($item['photo']); ?>" alt="<?= htmlspecialchars($item['nom_product']); ?>" class="w-16 h-16 mr-4">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($item['nom_product']); ?></p>
                                <p class="text-sm text-gray-600">x<?= htmlspecialchars($item['quantity']); ?></p>
                            </div>
                        </div>
                        <span class="text-lg font-semibold text-gray-700">$<?= number_format($product_total, 2); ?></span>
                    </div>
                <?php } ?>
                <div class="flex justify-between py-4 border-b">
                    <span class="font-medium">Subtotal</span>
                    <span class="text-lg font- text-gray-700">$<?= number_format($total_price, 2); ?></span>
                </div>
                <div class="flex justify-between py-4 border-b">
                    <span class="font-medium">Shipping estimate</span>
                    <span class="text-lg font- text-gray-700">$5.00</span>
                </div>
                <div class="flex justify-between py-4 border-b">
                    <span class="font-medium">Tax estimate</span>
                    <span class="text-lg font- text-gray-700">$8.32</span>
                </div>
                <div class="flex justify-between py-4 font- text-xl order-total">
                    <span>Order total</span>
                    <span class="text-gray-800">$<?= number_format($total_price + 5.00 + 8.32, 2); ?></span>
                </div>
            </div>
        </div>
        <?php include_once("../includes/footer.php"); ?>
    </section>
    <script>
        function confirmOrder(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Confirm Order',
                text: "Are you sure you want to place this order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#32CD32',
                cancelButtonColor: '#dc2626',
                confirmButtonText: 'Yes, place order!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('checkout-form').submit();
                }
            });
        }
    </script>
</body>
</html>
