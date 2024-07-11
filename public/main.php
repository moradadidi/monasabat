<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    $_SESSION['id_user'] = 'guest_' . uniqid();
}

$id_user = $_SESSION['id_user'];

$sql_categories = "SELECT id_category, name, description, photo FROM categories";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

$sql_hot_deals = "SELECT * FROM products ORDER BY created_at DESC LIMIT 4";
$stmt_hot_deals = $pdo->prepare($sql_hot_deals);
$stmt_hot_deals->execute();
$hot_deals = $stmt_hot_deals->fetchAll(PDO::FETCH_ASSOC);

$product_name = $_POST["name"] ?? '';
$sel_products = [];

if (!empty($product_name)) {
    $data = $pdo->prepare('SELECT * FROM products WHERE nom_product LIKE ?');
    $data->execute(["%$product_name%"]);
    $sel_products = $data->fetchAll(PDO::FETCH_ASSOC);
}
?>

<section class="home ">
    <?php if (!empty($sel_products)): ?>
        <section class="search-results mt-8">
            <div class="container mx-auto px-4">
                <h4 class="font-bold text-2xl text-gray-800 mb-8">Search Results</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($sel_products as $product): ?>
                        <div class="card bg-white shadow-lg rounded-lg overflow-hidden">
                            <img src="../admin/<?= htmlspecialchars($product['photo']) ?>" class="w-full h-48 object-cover" alt="<?= htmlspecialchars($product['nom_product']) ?>">
                            <div class="card-body p-4">
                                <h5 class="card-title font-semibold text-lg text-gray-900"><?= htmlspecialchars($product['nom_product']) ?></h5>
                                <p class="card-text text-gray-600 mb-4"><?= htmlspecialchars($product['description']) ?></p>
                                <span class="price text-lg font-bold text-gray-800">$<?= htmlspecialchars($product['price']) ?></span>
                                <div class="flex justify-between items-center mt-4">
                                    <button class="text-red-500 hover:text-red-700"><i class="fa fa-heart"></i></button>
                                    <button class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600">Add to Cart <i class="fa fa-shopping-cart"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php else: ?>
        <section id="Carousel" class="mb-8">
    <div id="carouselExampleIndicators" class="relative">
        <!-- Carousel Indicators -->
        <div class="absolute bottom-0 left-0 right-0 flex justify-center p-4 space-x-2">
            <button type="button" data-slide-to="0" class="w-3 h-3 bg-white rounded-full active"></button>
            <button type="button" data-slide-to="1" class="w-3 h-3 bg-white rounded-full"></button>
            <button type="button" data-slide-to="2" class="w-3 h-3 bg-white rounded-full"></button>
            <button type="button" data-slide-to="3" class="w-3 h-3 bg-white rounded-full"></button>
            <button type="button" data-slide-to="4" class="w-3 h-3 bg-white rounded-full"></button>
        </div>
        <!-- Carousel Items -->
        <div class="carousel-inner relative overflow-hidden w-full h-screen">
            <div class="carousel-item active absolute w-full transition-transform duration-500">
                <img src="../assets/images/sprt.jpeg" class="w-full h-full object-cover" alt="First slide">
            </div>
            <div class="carousel-item absolute w-full transition-transform duration-500">
                <img src="../assets/images/bok.webp" class="w-full h-full object-cover" alt="Second slide">
            </div>
            <div class="carousel-item absolute w-full transition-transform duration-500">
                <img src="../assets/images/elctron.jpeg" class="w-full h-full object-cover" alt="Third slide">
            </div>
            <div class="carousel-item absolute w-full transition-transform duration-500">
                <img src="../assets/images/men.webp" class="w-full h-full object-cover" alt="fourth slide">
            </div>
            <div class="carousel-item absolute w-full transition-transform duration-500">
                <img src="../assets/images/women.webp" class="w-full h-full object-cover" alt="five slide">
            </div>
        </div>
    </div>
</section>

        <section id="categories" class="mt-5">
            <div class="container mx-auto px-4">
                <div class="header mb-8">
                    <h4 class="font-bold text-2xl text-gray-800">Our Categories</h4>
                </div>
                <div class="relative">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php foreach ($categories as $category): ?>
                                <div class="swiper-slide">
                                    <a href="categorie.php?cat=<?= htmlspecialchars($category['id_category']) ?>" class="block relative category-card">
                                        <img src="../admin/<?= htmlspecialchars($category['photo']) ?>" class="w-full h-56 object-cover" alt="<?= htmlspecialchars($category['name']) ?>">
                                        <div class="category-name-overlay absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center opacity-0 transition-opacity duration-300">
                                            <h5 class="font-semibold text-2xl text-white mb-2"><?= htmlspecialchars($category['name']) ?></h5>
                                            <p class="text-white text-center px-4"><?= htmlspecialchars($category['description']) ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Add Pagination -->
                        <div class="swiper-pagination"></div>
                        <!-- Add Navigation -->
                        <div class="swiper-button-next custom-control"></div>
                        <div class="swiper-button-prev custom-control"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="Bannar1" class="my-8">
            <div class="banner">
                <img src="../assets/images/sold.jpg" class="w-full h-96" alt="Banner">
            </div>
        </section>

        <section id="products" class="mt-5">
        <div class="container mx-auto px-4">
            
            <div class="grid  grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="products-div">
   

                <?php foreach ($hot_deals as $deal): ?>
                    
                    <div class="card bg-white shadow-lg rounded-lg overflow-hidden relative group">
                    <img src="../admin/<?= htmlspecialchars($deal['photo']) ?>" class="cursor-pointer  product-image" alt="<?= htmlspecialchars($deal['nom_product']) ?>" data-product-id="<?= $deal['id_product'] ?>">
                    <div class="card-body  p-4">
                            <div class="card-header flex justify-between items-center mb-2">
                                <?php
                                $id_product= $deal['id_product'] ;
                                $data = $pdo->prepare("
                                SELECT R.*, U.username, U.photo 
                                FROM review R 
                                INNER JOIN users U ON R.id_user = U.id_user 
                                WHERE R.id_product = :id_product
                            ");
                            $data->execute(['id_product' => $id_product]);
                            $reviews = $data->fetchAll(PDO::FETCH_ASSOC);
                            
                            $id_product= $deal['id_product'] ;
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
                            <button name="like" class="text-red-500 hover:text-red-700"><i class="fa fa-heart text-4xl"></i></button>
                            <button name="add" class="text-orange-500    rounded-lg  hover:text-orange-600"><i class="fa-solid fa-cart-plus text-4xl"></i></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

   
    <?php endif; ?>

    <?php
    include_once("../includes/footer.php");

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
                    echo '<script>
                    window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "success",
                            title: "Added to cart!",
                            text: "Product already in  your cart .",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "main.php";
                        });
                    });
                </script>';
                exit();
                } else {
                    $stmt = $pdo->prepare("INSERT INTO cart (id_user, id_product, quantity) VALUES (:id_user, :id_product, 1)");
                    $stmt->execute(['id_user' => $id_user, 'id_product' => $id_product]);
                    echo '<script>
                    window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "success",
                            title: "Added to cart!!",
                            text: "Product added to your cart successfully.",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "main.php";
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
                    echo '<script>
                    window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "success",
                            title: "Added to favorites!!",
                            text: "Product added to your favorites successfully.",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "main.php";
                        });
                    });
                </script>';
                exit();
                }
            }
        }
    }
    ?>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            slidesPerView: 1,
            spaceBetween: 10,
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
                1024: {
                    slidesPerView: 4,
                    spaceBetween: 40,
                },
            }
        });
    });
</script>
<script>
document.querySelectorAll('.product-image').forEach(image => {
    image.addEventListener('click', function() {
        const productId = this.getAttribute('data-product-id');
        window.location.href = 'selected_product.php?id=' + productId;
    });
});
</script>
<style>
    .category-card {
        position: relative;
    }

    .category-name-overlay {
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .category-card:hover .category-name-overlay {
        opacity: 1;
    }

    .category-card img {
        height: 300px; 
        object-fit: cover;
    }

    .category-name-overlay h5 {
        font-size: 24px; 
    }

    .category-name-overlay p {
        font-size: 16px; 
        margin: 0;
    }

    .swiper-button-next,
    .swiper-button-prev {
        width: 50px;
        height: 50px;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        margin-top: -25px; /* Align vertically with images */
    }

    .swiper-button-next::after,
    .swiper-button-prev::after {
        font-size: 20px;
    }

    .swiper-button-next:hover,
    .swiper-button-prev:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }
    .swiper-pagination{
        translate:0 50px;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
        let currentIndex = 0;
        const slides = document.querySelectorAll('.carousel-item');
        const indicators = document.querySelectorAll('[data-slide-to]');
        const totalSlides = slides.length;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                slide.style.transform = `translateX(${(i - index) * 100}%)`;
            });
            indicators.forEach(indicator => indicator.classList.remove('active'));
            indicators[index].classList.add('active');
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % totalSlides;
            showSlide(currentIndex);
        }

        let autoSlide = setInterval(nextSlide, 3000);

        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                clearInterval(autoSlide);
                showSlide(index);
                currentIndex = index;
                autoSlide = setInterval(nextSlide, 3000);
            });
        });

        showSlide(currentIndex);
    });
</script>

<style>
    .carousel-item {
        transform: translateX(100%);
    }
    .carousel-item.active {
        transform: translateX(0);
    }
</style>