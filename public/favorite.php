<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

$stmt = $pdo->prepare("SELECT products.* FROM favorites 
                        JOIN products ON favorites.product_id = products.id_product 
                        WHERE favorites.user_id = :user_id");
$stmt->execute(['user_id' => $id_user]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            } else {
                $stmt = $pdo->prepare("INSERT INTO cart (id_user, id_product, quantity) VALUES (:id_user, :id_product, 1)");
                $stmt->execute(['id_user' => $id_user, 'id_product' => $id_product]);
            }

            echo '<script>
                    window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "success",
                            title: "Added to cart!",
                            text: "Product added to your cart successfully.",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "favorite.php";
                        });
                    });
                </script>';
            exit();
        } elseif (isset($_POST['del'])) {
            $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->execute(['user_id' => $id_user, 'product_id' => $id_product]);

            echo '<script>
                    window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "warning",
                            title: "Deleted!",
                            text: "The product has been removed from your favorites.",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "favorite.php";
                        });
                    });
                </script>';
            exit();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.1.2/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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

        .delete-btn {
            display: none;
        }

        .card:hover .delete-btn {
            display: block;
        }
    </style>
</head>
<body>
    <section class="home mt-14">
        <section class="mt-8 container mx-auto px-4">
            <div class="header mb-8 text-center">
                <h4 class="font-bold text-2xl text-gray-800">Your Favorite Products</h4>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative" id="favorites-div">
                <?php if (count($favorites) > 0): ?>
                    <?php foreach ($favorites as $product): ?>
                        <div class="card bg-white shadow-lg rounded-lg overflow-hidden relative group">
                            <img src="../admin/<?= htmlspecialchars($product['photo']) ?>" class="product-image cursor-pointer" alt="<?= htmlspecialchars($product['nom_product']) ?>" data-product-id="<?= $product['id_product'] ?>">
                            <div class="card-body p-4">
                                <div class="card-header flex justify-between items-center mb-2">
                                    <span class="reviews text-yellow-500">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
                                    </span>
                                    <span class="price text-lg font-bold text-gray-800">$<?= htmlspecialchars($product['price']) ?></span>
                                </div>
                                <h5 class="card-title font-semibold text-lg text-gray-900"><?= htmlspecialchars($product['nom_product']) ?></h5>
                                <p class="card-text text-gray-600 mb-4"><?= htmlspecialchars($product['description']) ?></p>
                                <form action="" method="post" class="absolute top-0 left-0 right-0 flex justify-between p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <input type="hidden" name="id_product" value="<?= htmlspecialchars($product['id_product']) ?>">
                                    <button type="submit" name="del" class="delete-btn absolute top-2 right-2 text-3xl text-red-600 hover:text-red-600"><i class="fa fa-trash"></i></button>
                                    <button name="add" class="bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-green-600"><i class="fa-solid fa-cart-plus text-2xl"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-600">You have no favorite products.</p>
                <?php endif; ?>
            </div>
        </section>
        <?php include_once("../includes/footer.php"); ?>
    </section>
    <script>
        document.querySelectorAll('.product-image').forEach(image => {
            image.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                window.location.href = 'selected_product.php?id=' + productId;
            });
        });
    </script>
</body>
</html>
