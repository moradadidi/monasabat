<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if (!isset($_GET['id'])) {
    header("Location: main.php");
    exit();
}

$id_product = $_GET['id'];

// Fetch reviews with user information
$data = $pdo->prepare("
    SELECT R.*, U.username, U.photo 
    FROM review R 
    INNER JOIN users U ON R.id_user = U.id_user 
    WHERE R.id_product = :id_product
");
$data->execute(['id_product' => $id_product]);
$reviews = $data->fetchAll(PDO::FETCH_ASSOC);

// Fetch average rating
$avg = $pdo->prepare("SELECT AVG(R.rating) AS average_rating FROM review R WHERE R.id_product = :id_product");
$avg->execute(['id_product' => $id_product]);
$avg_rat = $avg->fetch(PDO::FETCH_ASSOC);

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id_product = :id_product");
$stmt->execute(['id_product' => $id_product]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_product']) && isset($id_user)) {
        $id_product = $_POST['id_product'];

        if (isset($_POST['add'])) {
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

            echo '<script>
                Swal.fire({
                    title: "Added to cart!",
                    text: "Product added to your cart successfully.",
                    icon: "success"
                }).then(function() {
                    window.location.href = "selected_product.php?id=' . htmlspecialchars($id_product) . '"; 
                });
            </script>';
        } elseif (isset($_POST['like'])) {
            $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute(['user_id' => $id_user, 'product_id' => $id_product]);
            $favorite_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$favorite_item) {
                $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (:user_id, :product_id)");
                $stmt->execute(['user_id' => $id_user, 'product_id' => $id_product]);

                echo '<script>
                    Swal.fire({
                        title: "Added to favorites!",
                        text: "Product added to your favorites successfully.",
                        icon: "success"
                    }).then(function() {
                        window.location.href = "selected_product.php?id=' . htmlspecialchars($id_product) . '"; 
                    });
                </script>';
            }
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['nom_product']) ?> - Online Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="home">
        <div class="max-w-screen-lg mx-auto mt-12 p-4">
            <div class="bg-white p-12 rounded-lg shadow-lg flex flex-col md:flex-row">
                <div class="md:w-1/2">
                    <img src="../admin/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['nom_product']) ?>" class="w-full rounded-lg">
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        <img src="../admin/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['nom_product']) ?>" class="w-full h-24 rounded-lg object-cover">
                        <img src="../admin/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['nom_product']) ?>" class="w-full h-24 rounded-lg object-cover">
                        <img src="../admin/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['nom_product']) ?>" class="w-full h-24 rounded-lg object-cover">
                    </div>
                </div>
                
                <div class="md:w-1/2 md:pl-12 mt-6 md:mt-0">
                    <h1 class="text-6xl font-bold text-gray-800"><?= htmlspecialchars($product['nom_product']) ?></h1>
                    <div class="flex items-center mt-4">
                        <div class="text-5xl text-gray-800 font-bold">$<?= htmlspecialchars($product['price']) ?></div>
                        <div class="text-2xl text-red-500 ml-4">Discount 10%</div>
                        <div class="flex items-center ml-4 text-yellow-500">
                        <?php
                                $avg_rating = round($avg_rat['average_rating'] * 2) / 2; // Round to nearest half
                                for ($i = 0; $i < floor($avg_rating); $i++) {
                                    echo '<span class="fas fa-star"></span>';
                                }
                                if ($avg_rating - floor($avg_rating) > 0) {
                                    echo '<span class="fas fa-star-half-alt"></span>';
                                }
                                for ($i = ceil($avg_rating); $i < 5; $i++) {
                                    echo '<span class="far fa-star"></span>';
                                }
                            ?>
                            <span class="ml-2 text-gray-600 text-lg">(<?= count($reviews) ?> Reviews)</span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <a href="add_review.php?id=<?= htmlspecialchars($product['id_product']) ?>" class="inline-block text-orange-600 text-lg font-semibold py-3 rounded-md underline focus:ring-2 focus:ring-blue-400">Add review</a>
                    </div>
                    <div class="mt-6 text-gray-700 text-xl">
                        <p class="text-2xl font-bold"><?= htmlspecialchars($product['description']) ?></p>
                    </div>
                    <?php if ($product["category_id"] == 2 || $product["category_id"] == 3) { ?>
                        <label for="category" class="block text-xl my-7 font-semibold text-gray-700 mb-2">Size:</label>
                        <div class="relative">
                            <select id="category" class="block appearance-none text-lg font-bold w-5xl bg-white border border-gray-300 text-gray-700 py-3 px-4 pr-8 rounded-lg shadow leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="x-small">X-small</option>
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                                <option value="x-large">X-Large</option>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="mt-8">
                        <h2 class="text-2xl font-semibold text-gray-800">Product Details</h2>
                        <ul class="mt-4 text-xl list-disc list-inside text-gray-700">
                            <li class="text-blue-700">FREE International Returns</li>
                            <li class="text-gray-700">No Import Charges & $38.44 Shipping to Morocco Details</li>
                            <li class="text-gray-700">Available at a lower price from other sellers that may not offer free Prime shipping.</li>
                        </ul>
                    </div

                    <form action="" method="post">
                        <div class="flex mt-8 space-y-4">
                            <input type="hidden" name="id_product" value="<?= htmlspecialchars($product['id_product']) ?>">
                            <button name="like" class="bg-red-300 text-gray-800 px-6 py-3 rounded-md mr-4 hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-300 flex items-center text-lg">
                                <i class="fas fa-heart mr-2 text-red-600"></i> Add to favorites
                            </button>
                            <button name="add" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 flex items-center text-lg">
                                <i class="fas fa-shopping-cart mr-2"></i> Add to cart
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-12 bg-white p-12 rounded-lg shadow-lg">
                <h2 class="text-3xl font-semibold text-gray-800">Customer Reviews</h2>
                <div class="mt-8 space-y-8">
                    
                    <?php foreach ($reviews as $review) { 
                        if(count($reviews)){?>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <img src="../admin/pictures/<?= htmlspecialchars($review['photo']) ?>" alt="Reviewer" class="w-14 h-14 rounded-full">
                            </div>
                            <div class="ml-6">
                                <div class="text-2xl font-semibold text-gray-800"><?= htmlspecialchars($review['username']) ?></div>
                                <div class="flex items-center text-yellow-500 text-xl">
                                    <?php 
                                        for ($i = 0; $i < htmlspecialchars($review['rating']); $i++) { 
                                            echo '<span class="fas fa-star"></span>';
                                        }
                                    ?>
                                    <span class="ml-2 text-gray-600 text-lg"><?= htmlspecialchars($review['created_at2']) ?></span>
                                </div>
                                
                                <div class="mt-4 text-gray-700 text-xl"><?= htmlspecialchars($review['comment']) ?></div>
                            </div>
                        </div>
                    <?php }else{
                        ?>
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                            
                            </div>
                        </div>
                        <?php
                    }
                } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

