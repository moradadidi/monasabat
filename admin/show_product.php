<?php
include_once("dashbord.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: ../public/login.php");
    exit();
}

$sql = "SELECT p.id_product, c.name AS category, p.nom_product, p.price, p.quantity, p.description, p.photo 
        FROM products p 
        JOIN categories c ON p.category_id = c.id_category";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = "";
$message_type = "";

if (isset($_POST["del"])) {
    $id = $_POST["id"];
    $data = $pdo->prepare('DELETE FROM products WHERE id_product = ?');
    if ($data->execute([$id])) {
        $message = "Produit supprimé avec succès.";
        $message_type = "success";
    } else {
        $message = "Erreur lors de la suppression du produit.";
        $message_type = "error";
    }
    echo "<script>
            window.addEventListener('DOMContentLoaded', (event) => {
                Swal.fire({
                    icon: '$message_type',
                    title: 'Notification',
                    text: '$message',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = 'show_product.php';
                });
            });
          </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Products</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body "\>
    <section class="home" style="background-color:white">
        <div class="container">
            <div class="max-w-lg">
                <h3 class="text-gray-800 text-xl font-bold sm:text-2xl">Products</h3>
            </div>
            <div class="table-container mt-12 shadow-sm border rounded-lg overflow-x-auto">
                <table class="w-full table-auto text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                        <tr class="text-center">
                            <th class="py-3 px-6">Category</th>
                            <th class="py-3 px-6">Photo</th>
                            <th class="py-3 px-6">Product Name</th>
                            <th class="py-3 px-6">Price</th>
                            <th class="py-3 px-6">Quantity</th>
                            <th class="py-3 px-6">Description</th>
                            <th class="py-3 px-6">Delete</th>
                            <th class="py-3 px-6">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 divide-y">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr class="text-center">
                                    <td class="py-3 px-6"><?= htmlspecialchars($product['category']) ?></td>
                                    <td class="py-3 px-6">
                                        <img src="<?= htmlspecialchars($product['photo']) ?>" class="product-img" alt="Product Photo">
                                    </td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($product['nom_product']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($product['price']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($product['quantity']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($product['description']) ?></td>

                                    <td>
                                        <form action="" method="post">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id_product']) ?>">
                                            <button class="del" name="del"><i class="bx bx-trash"></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="edit_product.php" method="post">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id_product']) ?>">
                                            <button class="edit" name="edit"><i class="bx bx-pencil"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="py-3 px-6">No products found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
</html>
