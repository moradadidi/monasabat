<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");


if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_produit = $_GET["id"];
$alertMessage = "";
$alertType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_product'], $_POST['rating'], $_POST['comment'])) {
        $id_product = $_POST['id_product'];
        $rating = $_POST['rating'];
        $comment = $_POST['comment'];

        try {
            $stmt = $pdo->prepare("INSERT INTO review (id_product, id_user, rating, comment) VALUES (:id_product, :id_user, :rating, :comment)");
            $stmt->execute(['id_product' => $id_product, 'id_user' => $id_user, 'rating' => $rating, 'comment' => $comment]);

            $alertMessage = "Review added successfully!";
            $alertType = "success";
        } catch (PDOException $e) {
            $alertMessage = "Error: " . $e->getMessage();
            $alertType = "error";
        }
    } else {
        $alertMessage = "Required fields are missing!";
        $alertType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            font-size: 3rem; /* Increased font size */
            color: #d1d5db; /* Gray-300 */
            cursor: pointer;
        }
        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #34d399; /* Green-400 */
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="home">
        <section class="container max-w-6xl mx-auto p-6 bg-white shadow-md rounded-lg mt-10">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Add Review</h2>
            <form action="" method="post" class="space-y-6">
                <input type="hidden" name="id_product" value="<?=$id_produit?>">
                <div class="form-group">
                    <label for="rating" class="block text-gray-700">Rate the product - <span class="text-red-500">*</span></label>
                    <div class="star-rating flex text-4xl justify-center mt-2">
                        <input type="radio" name="rating" id="rating-5" value="5" required>
                        <label for="rating-5" class="star">&#9733;</label>
                        <input type="radio" name="rating" id="rating-4" value="4">
                        <label for="rating-4" class="star">&#9733;</label>
                        <input type="radio" name="rating" id="rating-3" value="3">
                        <label for="rating-3" class="star">&#9733;</label>
                        <input type="radio" name="rating" id="rating-2" value="2">
                        <label for="rating-2" class="star">&#9733;</label>
                        <input type="radio" name="rating" id="rating-1" value="1">
                        <label for="rating-1" class="star">&#9733;</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment" class="block text-gray-700">Comment</label>
                    <textarea name="comment" id="comment" class="block w-full mt-1 p-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="5" placeholder="Your comment" required></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-green-500 text-white font-semibold rounded-md shadow hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-200">+ Add Review</button>
            </form>
        </section>
    </div>

    <?php if ($alertMessage): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?=$alertType?>',
                title: '<?=($alertType == "success") ? "Success" : "Error"?>',
                text: '<?=htmlspecialchars($alertMessage, ENT_QUOTES, 'UTF-8')?>',
                showConfirmButton: <?=$alertType == 'success' ? 'false' : 'true'?>,
                timer: <?=$alertType == 'success' ? '2000' : 'null'?>
            }).then(() => {
                <?php if ($alertType == 'success'): ?>
                window.location.href = "selected_product.php?id=<?=$id_produit?>";
                <?php endif; ?>
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
