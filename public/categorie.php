<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}
$category_id = $_GET['cat'] ?? null;

if ($category_id) {
    // Fetch category name
    $category_stmt = $pdo->prepare('SELECT name FROM categories WHERE id_category = ?');
    $category_stmt->execute([$category_id]);
    $category = $category_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch products in the category
    $products_stmt = $pdo->prepare('SELECT * FROM products WHERE category_id = ?');
    $products_stmt->execute([$category_id]);
    $products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Redirect to main page if no category ID is provided
    header('Location: main.php');
    exit();
}
?>
<style>
     .home {
        height: 100vh;
            background-color: #ffffff;
            background-image: radial-gradient(at 12% 45%, #32CD32 40%, transparent 20%),
                radial-gradient(at 62% 33%, #ff7a00 50%, transparent 50%);
        }
</style>
<body>
    
    <section class="home mt-10">
        <h1 class="text-6xl text-center text-gray-100 font-bold mb-4 py-12"><?= htmlspecialchars($category['name']); ?></h1>
        <section id="products" class="mt-5">
            <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="products-div">
                <?php if ($products): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="card bg-white shadow-lg rounded-lg overflow-hidden transition-transform transform hover:scale-105">
                            <img src="../admin/<?= htmlspecialchars($product['photo']) ?>" class="w-full h-48 object-cover" alt="<?= htmlspecialchars($product['nom_product']) ?>">
                            <div class="card-body p-4">
                                <div class="card-header flex justify-between items-center mb-2">
                                    <span class="reviews text-yellow-500">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
                                    </span>
                                    <span class="price text-lg font-bold text-gray-800">$<?= htmlspecialchars($product['price']) ?></span>
                                </div>
                                <h5 class="card-title font-semibold text-lg text-gray-900"><?= htmlspecialchars($product['nom_product']) ?></h5>
                                <p class="card-text text-gray-600 mb-4"><?= htmlspecialchars($product['description']) ?></p>
                                <form action="" method="post">
                                    <div class="card-footer flex justify-between items-center">
                                        <input type="hidden" name="id_product" value="<?= htmlspecialchars($product['id_product']) ?>">
                                        <button name="like" class="text-red-500 hover:text-red-700 focus:outline-none"><i class="fa fa-heart"></i></button>
                                        <button name="add" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 focus:outline-none">Add to Cart <i class="fa fa-shopping-cart"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-4 text-center bg-gray-100 p-6 rounded-lg shadow-md">
                        <h2 class="text-2xl font-bold text-gray-700 mb-2">No products found in this category.</h2>
                        <p class="text-gray-500 mb-4">Please check back later or explore other categories.</p>
                        <a href="main.php" class="inline-block bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600">Check other products  <i class="fas fa-shopping-cart"></i></a>
                    </div>
                    <?php endif; ?>
            </div>
        </div>
    </section>
</section>

<script src="../assets/js/scriptss.js"></script>


</body>