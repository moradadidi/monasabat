<?php
session_start();
include_once("../includes/database.php");
$user_id = $_SESSION['id_user'] ?? null;

if ($user_id) {
    // Get cart details
    $orderStmt = $pdo->prepare("SELECT COUNT(id_cart) as n_orders, SUM(quantity) as quantity FROM cart WHERE id_user = ?");
    $orderStmt->execute([$user_id]);
    $orders = $orderStmt->fetch(PDO::FETCH_ASSOC);
} else {
    $orders = ['n_orders' => 0, 'quantity' => 0];
}

$product_name = $_POST['name'] ?? '';

// Fetch products based on search
$productStmt = $pdo->prepare('SELECT * FROM products WHERE nom_product LIKE ?');
$productStmt->execute(["%$product_name%"]);
$sel_products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories
$categoryStmt = $pdo->prepare("SELECT id_category, name FROM categories");
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user profile information
$userProfile = null;
if ($user_id) {
    $userStmt = $pdo->prepare("SELECT username, email, photo FROM users WHERE id_user = ?");
    $userStmt->execute([$user_id]);
    $userProfile = $userStmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link rel="stylesheet" href="../assets/css/tailwind-output.css"> -->
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <header class="header">
        <img src="../assets/images/logo.png" alt="Logo" class="logo" id="logo">
        <nav class="navbar flex items-center">
            <a href="main.php">Home</a>
            <a href="products.php">Products</a>
            <div class="relative group">
                <a class="flex items-center px-4 py-2 text-white pointer-events-none cursor-default no-underline">
                    Categories
                    <svg class="w-2.5 h-2.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                    </svg>
                </a>
                <!-- Dropdown menu -->
                <div class="absolute left-0 z-10 hidden mt-2 w-66 bg-white divide-y divide-gray-100 rounded-lg shadow group-hover:block">
                    <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDividerButton">
                        <?php foreach ($categories as $category) { ?>
                            <li>
                                <a href="categorie.php?cat=<?= htmlspecialchars($category['id_category'], ENT_QUOTES, 'UTF-8') ?>" class="block px-4 py-6"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <a href="contact.php">Contact Us</a>
        </nav>
        <div class="icons flex items-center space-x-4 relative">
    <div class="fas fa-bars text-gray-700 cursor-pointer" id="menu-btn"></div>
    <div class="fa fa-heart text-gray-700 cursor-pointer group relative" id="fav-btn" title="Favorite">
        <span class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 hidden w-max px-3 py-2 text-lg text-white bg-orange-500 rounded-md shadow-md group-hover:block">
            Favorite
        </span>
    </div>
    <div class="relative group" title="Cart">
        <div class="fas fa-shopping-cart text-gray-700 cursor-pointer" id="cart-btn"></div>
        <span class="absolute -top-4 -right-2 inline-flex items-center justify-center w-9 h-9 text-lg font-bold text-white bg-green-600 rounded-full shadow-md"><?= htmlspecialchars($orders['n_orders'], ENT_QUOTES, 'UTF-8'); ?></span>
        <span class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 hidden w-max px-3 py-2 text-lg text-white bg-orange-500 rounded-md shadow-md group-hover:block">
            Cart
        </span>
    </div>
</div>

        <form action="" class="search-form" method="post">
            <input type="search" name="name" id="search-box" placeholder="Search here...">
            <button type="submit" name="search" class="fas fa-search text-3xl mx-6"></button>
        </form>

        <div class="relative group">
            <div class="profil-btn cursor-pointer bg-white rounded-full border-4 border-gray-300 hover:border-orange-300" id="profil-btn">
                <a href="../public/profil.php"><img src="../public/pictures/<?=$userProfile["photo"]?>" class="rounded-full" alt="Profile"></a>
            </div>
            <div class="absolute top-full -left-32 mb-2 hidden w-max px-3 py-2 text-xl text-white bg-orange-500 rounded-md shadow-md group-hover:block">
                <div>
                    <span class="font-bold">Name:</span> <?= htmlspecialchars($userProfile['username'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div>
                    <span class="font-bold">Email:</span> <?= htmlspecialchars($userProfile['email'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>

        <div class="profil-btn w-32 cursor-pointer bg-white rounded-xl border-4 border-gray-300 hover:border-orange-300" id="profil-btn">
            <a href="../includes/deconexion.php" class="fa-solid fa-arrow-right-from-bracket rounded-lg text-3xl text-orange-500"></a>
            <span class="text-bold text-xl">Logout</span>
        </div>
     
      <!-- <i class="fa-solid fa-arrow-right-from-bracket"></i> -->
    </header>

    <script src="../assets/js/script.js"></script>
</body>
</html>
<style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .group:hover .group-hover\:block {
        display: block
    }
    .navbar.active {
        display: block;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: white;
        z-index: 1000;
    }

    .profile-form.hidden {
        display: none;
    }

    .profil-btn:hover + .profile-form, 
    .profile-form:hover {
        display: block;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded and parsed');

        let searchForm = document.querySelector('.search-form');
        let shoppingCart = document.querySelector('.shopping-cart');
        let loginForm = document.querySelector('.login-form');
        let navbar = document.querySelector('.navbar');
        let profileForm = document.getElementById('profile-form');
        
        let searchBtn = document.getElementById('search-btn');
        if (searchBtn) {
            searchBtn.onclick = () => {
                searchForm.classList.toggle('active');
                if (shoppingCart) shoppingCart.classList.remove('active');
                if (loginForm) loginForm.classList.remove('active');
                navbar.classList.remove('active');
                if (profileForm) profileForm.classList.add('hidden');
            };
        }

        let cartBtn = document.getElementById('cart-btn');
        if (cartBtn) {
            cartBtn.onclick = () => {
                console.log('Cart button clicked');
                window.location.href = "http://localhost/monasabat2/public/cart.php";
            };
        }

        let favBtn = document.getElementById('fav-btn');
        if (favBtn) {
            favBtn.onclick = () => {
                console.log('Favorites button clicked');
                window.location.href = "http://localhost/monasabat2/public/favorite.php";
            };
        }

        let logo = document.getElementById('logo');
        if (logo) {
            logo.addEventListener('click', () => {
                console.log('Logo clicked');
                window.location.href = "http://localhost/monasabat2/public/main.php";
            });
        }

        let menuBtn = document.getElementById('menu-btn');
        if (menuBtn) {
            menuBtn.onclick = () => {
                console.log('Menu button clicked');
                navbar.classList.toggle('active');
                if (searchForm) searchForm.classList.remove('active');
                if (shoppingCart) shoppingCart.classList.remove('active');
                if (loginForm) loginForm.classList.remove('active');
                if (profileForm) profileForm.classList.add('hidden');
            };
        }

        let profilBtn = document.getElementById('profil-btn');
        if (profilBtn) {
            profilBtn.onclick = (event) => {
                event.stopPropagation();
                console.log('Profile button clicked');
                if (profileForm) profileForm.classList.toggle('hidden');
                if (searchForm) searchForm.classList.remove('active');
                if (shoppingCart) shoppingCart.classList.remove('active');
                if (loginForm) loginForm.classList.remove('active');
                navbar.classList.remove('active');
            };
        }

        document.addEventListener('click', function(event) {
            if (profileForm && !profileForm.contains(event.target) && !profilBtn.contains(event.target)) {
                profileForm.classList.add('hidden');
            }
        });

        document.querySelectorAll('.add-to-cart-form').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                let formData = new FormData(form);

                fetch('cart.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire({
                              title: 'Added to cart!',
                              text: 'Product added to your cart successfully.',
                              icon: 'success',
                              confirmButtonText: 'OK'
                          });
                      } else {
                          Swal.fire({
                              title: 'Error!',
                              text: 'There was an error adding the product to your cart.',
                              icon: 'error',
                              confirmButtonText: 'OK'
                          });
                      }
                  }).catch(error => {
                      Swal.fire({
                          title: 'Error!',
                          text: 'There was an error adding the product to your cart.',
                          icon: 'error',
                          confirmButtonText: 'OK'
                      });
                  });
            });
        });
    });
</script>
