<?php
include_once("dashbord.php");


$product_stats = $pdo->prepare("SELECT COUNT(id_product) as product_stats FROM products");
$product_stats->execute();
$products = $product_stats->fetch(PDO::FETCH_ASSOC);

$user_stats = $pdo->prepare("SELECT COUNT(id_user) as user_stats FROM users");
$user_stats->execute();
$user_stats = $user_stats->fetch(PDO::FETCH_ASSOC);

$stats_cat = $pdo->prepare("SELECT COUNT(id_category) as stats_cat FROM categories");
$stats_cat->execute();
$categories = $stats_cat->fetch(PDO::FETCH_ASSOC);


$sql = "SELECT * FROM users";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="home" style="background-color:white">
    <section id="stats">
        <div class="max-w-screen-xl mx-auto px-4 md:px-8 text-center">
            <div class="max-w-2xl mx-auto">
                <h3>Our customers are always happy</h3>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi venenatis sollicitudin quam ut tincidunt.
                </p>
            </div>
            <div class="stats-container mt-12">
                <div class="stats-item" id="users">
                    <h4><?= htmlspecialchars($user_stats['user_stats']) ?></h4>
                    <p>Users</p>
                </div>
                <div class="stats-item" id="products">
                    <h4><?= htmlspecialchars($products['product_stats']) ?></h4>
                    <p>Products</p>
                </div>
                <div class="stats-item" id="categories">
                    <h4><?= htmlspecialchars($categories['stats_cat']) ?></h4>
                    <p>Categories</p>
                </div>
            </div>
        </div>
    </section>
    <div class="container">
        <div class="max-w-lg">
            <h3 class="text-gray-800 text-xl font-bold sm:text-2xl">Users</h3>
        </div>
        <div class="table-container mt-12 shadow-sm border rounded-lg overflow-x-auto">
            <table class="w-full table-auto text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                    <tr>
                        <th class="py-3 px-6">ID</th>
                        <th class="py-3 px-6">Username</th>
                        <th class="py-3 px-6">Tel</th>
                        <th class="py-3 px-6">Created At</th>
                        <th class="py-3 px-6">Is Admin</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="py-3 px-6"><?= htmlspecialchars($user['id_user']) ?></td>
                                <td class="py-3 px-6"><div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="../public/pictures/<?= htmlspecialchars($user['photo']) ?>" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['username']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($user['email']) ?></div>
                                        </div>
                                    </div></td>
                                    <td class="py-3 px-6"><?= !empty($user['tel']) ? htmlspecialchars($user['tel']) : 'No phone number' ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($user['created_at']) ?></td>
                                
                                <td class="py-3 px-6"><?= $user['is_admin'] ? 'Yes' : 'No' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="py-3 px-6">No users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
