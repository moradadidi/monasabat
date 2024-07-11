<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");

// if (!isset($_SESSION['id_user'])) {
//     header("Location: login.php");
//     exit();
// }

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_product']) && isset($id_user)) {
        $id_product = $_POST['id_product'];

        if (isset($_POST['add'])) {
            // Add to cart logic
            $stmt = $pdo->prepare("SELECT * FROM cart WHERE id_user = :id_user AND id_product = :id_product");
            $stmt->execute(['id_user' => $id_user, 'id_product' => $id_product]);
            $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cart_item) {
                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id_user = :id_user AND id_product = :id_product");
                $stmt->execute(['id_user' => $id_user, 'id_product' => $id_product]);
                // echo '<script>
                //     Swal.fire({
                //         title: "Added to cart!",
                //         text: "Product added to your cart successfully.",
                //         icon: "success"
                //     }).then(function() {
                //         window.location.href = "products.php"; 
                //     });
                // </script>';
                echo '<script>
                    window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "success",
                            title: "Added to cart!",
                            text: "Product already in  your cart .",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "products.php";
                        });
                    });
                </script>';
                exit();
            } else {
                $stmt = $pdo->prepare("INSERT INTO cart (id_user, id_product, quantity) VALUES (:id_user, :id_product, 1)");
                $stmt->execute(['id_user' => $id_user, 'id_product' => $id_product]);
                // echo '<script>
                //     Swal.fire({
                //         title: "Added to cart!",
                //         text: "Product added to your cart successfully.",
                //         icon: "success"
                //     }).then(function() {
                //         window.location.href = "products.php"; 
                //     });
                // </script>';
                echo '<script>
                    window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "success",
                            title: "Added to cart!!",
                            text: "Product added to your cart successfully.",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "products.php";
                        });
                    });
                </script>';
                exit();
            }
        } elseif (isset($_POST['like'])) {
            // Add to favorites logic
            $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute(['user_id' => $id_user, 'product_id' => $id_product]);
            $favorite_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$favorite_item) {
                $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (:user_id, :product_id)");
                $stmt->execute(['user_id' => $id_user, 'product_id' => $id_product]);
                // echo '<script>
                //     Swal.fire({
                //         title: "Added to favorites!",
                //         text: "Product added to your favorites successfully.",
                //         icon: "success"
                //     }).then(function() {
                //         window.location.href = "products.php"; 
                //     });
                // </script>';
                echo '<script>
                    window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "success",
                            title: "Added to favorites!!",
                            text: "Product added to your favorites successfully.",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "products.php";
                        });
                    });
                </script>';
                exit();
            }
        }
    } else {
        header("Location: login.php");
        exit();
    }
}
?>

<div class="home">
    <nav class="bg-orange-500 p-4 fixed w-full z-40">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white">
                <h1 class="text-xl font-bold">Product Search</h1>
            </div>
            <form action="" method="get" class="flex items-center border border-gray-300 rounded-md overflow-hidden bg-white ">
                <select name="category" class="px-4 py-2 border-none focus:ring-0">
                    <option value="">All Categories</option>
                    <?php
                    $categories = $pdo->query("SELECT id_category, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($categories as $category) {
                        echo '<option value="' . htmlspecialchars($category['id_category']) . '">' . htmlspecialchars($category['name']) . '</option>';
                    }
                    ?>
                </select>
                <input type="text" name="name" placeholder="Search products..." class="px-4 py-2 border-none focus:ring-0">
                <button type="submit" class="bg-orange-400 text-white px-4 py-2 border-none focus:ring-0"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </nav>

    <section id="products" class="mt-5">
        <div class="container mx-auto px-4">
            <div class="grid  grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="products-div">
                <?php
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
                if (!empty($_GET['min_price'])) {
                    $sql .= " AND price >= ?";
                    $params[] = $_GET['min_price'];
                }
                if (!empty($_GET['max_price'])) {
                    $sql .= " AND price <= ?";
                    $params[] = $_GET['max_price'];
                }

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php foreach ($products as $deal): ?>
                    <div class="card bg-white shadow-lg rounded-lg overflow-hidden relative group">
                        <img src="../admin/<?= htmlspecialchars($deal['photo']) ?>" class="cursor-pointer product-image" alt="<?= htmlspecialchars($deal['nom_product']) ?>" data-product-id="<?= $deal['id_product'] ?>">
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
        </div>
    </section>

    <?php include_once("../includes/footer.php"); ?>
</div>
<script>
document.querySelectorAll('.product-image').forEach(image => {
    image.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        window.location.href = 'selected_product.php?id=' + productId;
    });
});
</script>
<style>
    .home nav {
    height: 60px; 
}

.home nav h1 {
    font-size: 24px; 
}

.home nav form {
    display: flex;
    align-items: center;
    border: 1px solid #d1d5db;
    border-radius: 50px;
    background-color: #fff;
    overflow: hidden;
}

.home nav form select, 
.home nav form input, 
.home nav form button {
    height: 38px; /* Make form elements bigger */
    margin: 0; /* Remove margin between elements */
    border: none; /* Remove border from elements */
    font-size: 16px; /* Increase font size */
    outline: none; /* Remove outline on focus */
}

.home nav form select {
    padding-left: 0.5rem;
}

.home nav form input {
    padding-left: 0.5rem;
    flex-grow: 1;
}

.home nav form button {
    background-color: #f97316; 
    padding-left: 1rem;
    padding-right: 1rem;
}

.home #products {
    padding-top: 64px;
}

.group:hover form {
    opacity: 1;
}

.card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-image {
    width: 100%;
    height: 70%; /* Set a fixed height for the image container */
    object-fit: contain; /* Ensure the whole image is shown */
}

.card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

</style>