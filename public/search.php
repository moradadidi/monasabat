<?php
include_once("../includes/database.php");

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($_GET['name'])) {
    $sql .= " AND nom_product LIKE ?";
    $params[] = "%" . $_GET['name'] . "%";
}
if (!empty($_GET['category'])) {
    $sql .= " AND category_id = ?";
    $params[] = $_GET['category'];
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="home container mx-auto px-4 py-6">
    <div class=" flex">
        <?php foreach ($products as $deal): ?>
            <div class="card bg-white shadow-md rounded-lg overflow-hidden relative group">
                <img src="../admin/<?= htmlspecialchars($deal['photo']) ?>" class="cursor-pointer product-image w-full h-48 object-cover" alt="<?= htmlspecialchars($deal['nom_product']) ?>" data-product-id="<?= $deal['id_product'] ?>">
                <div class="card-body p-4">
                    <div class="card-header flex justify-between items-center mb-2">
                        <?php
                        $id_product = $deal['id_product'];
                        $data = $pdo->prepare("
                            SELECT R.*, U.username, U.photo 
                            FROM review R 
                            INNER JOIN users U ON R.id_user = U.id_user 
                            WHERE R.id_product = :id_product
                        ");
                        $data->execute(['id_product' => $id_product]);
                        $reviews = $data->fetchAll(PDO::FETCH_ASSOC);

                        $avg = $pdo->prepare("SELECT AVG(R.rating) AS average_rating FROM review R WHERE R.id_product = :id_product");
                        $avg->execute(['id_product' => $id_product]);
                        $avg_rat = $avg->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <span class="reviews text-yellow-500">
                            <?php
                            $avg_rating = round($avg_rat['average_rating'] * 2) / 2; // Round to nearest half
                            for ($i = 0; $i < floor($avg_rating); $i++) {
                                echo '<i class="fas fa-star"></i>';
                            }
                            if ($avg_rating - floor($avg_rating) > 0) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            }
                            for ($i = ceil($avg_rating); $i < 5; $i++) {
                                echo '<i class="far fa-star"></i>';
                            }
                            ?>
                        </span>
                        <span class="price text-lg font-bold text-gray-800">$<?= htmlspecialchars($deal['price']) ?></span>
                    </div>
                    <h5 class="card-title font-semibold text-lg text-gray-900"><?= htmlspecialchars($deal['nom_product']) ?></h5>
                    <p class="card-text text-gray-600 mb-4"><?= htmlspecialchars($deal['description']) ?></p>
                </div>
                <form action="" method="post" class="absolute top-0 left-0 right-0 flex justify-between p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <input type="hidden" name="id_product" value="<?= htmlspecialchars($deal['id_product']) ?>">
                    <button name="like" class="text-red-500 hover:text-red-700"><i class="fa fa-heart text-3xl"></i></button>
                    <button name="add" class="bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-green-600"><i class="fa-solid fa-cart-plus text-2xl"></i></button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
                        </section>

<style>
.home {
    padding: 1.5rem 0;
}
.flex{
    flex-direction: row;
    display: flex;
    justify-content: center;
}
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.product-image {
    transition: transform 0.2s ease-in-out;
}

.card:hover .product-image {
    transform: scale(1.05);
}

.card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

.card-text {
    font-size: 1rem;
}

form {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

@media (max-width: 768px) {
    .card {
        flex: 0 0 50%;
    }
}

@media (max-width: 640px) {
    .card {
        flex: 0 0 100%;
    }
}
</style>
