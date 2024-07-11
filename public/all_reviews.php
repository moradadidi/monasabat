<?php
include_once("../includes/database.php");
include_once("../includes/navbar.php");

// Fetch user profile information
$user_id = $_SESSION['id_user'] ?? null;
$reviews = [];

if ($user_id) {
    // Fetch all user reviews
    $reviewStmt = $pdo->prepare("SELECT r.rating, r.comment, r.created_at2, p.nom_product, p.photo FROM review r JOIN products p ON r.id_product = p.id_product WHERE r.id_user = ?");
    $reviewStmt->execute([$user_id]);
    $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reviews</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/tailwind-output.css">
    <style>
        body {
            background-color: #ff7a00;
            background-image: radial-gradient(at 12% 45%, #32CD32 40%, transparent 20%),
                radial-gradient(at 62% 33%, #ff7a00 50%, transparent 50%);
        }

        .form-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-logo img {
            max-width: 100px;
        }
    </style>
</head>
<body class="home bg-gray-100">
    <div class="container mx-auto mt-10 p-5">
        <h1 class="text-4xl font-bold text-white mb-6 text-center">All Reviews</h1>
        <div class=" p-6">
            <?php if (!empty($reviews)) { ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach ($reviews as $review) { ?>
                        <div class="bg-gray-50 p-6 rounded-lg shadow-md flex items-start space-x-4">
                            <img src="../admin/<?= htmlspecialchars($review['photo'], ENT_QUOTES, 'UTF-8') ?>" class="w-16 h-16 object-cover rounded-full" alt="<?= htmlspecialchars($review['nom_product'], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold"><?= htmlspecialchars($review['nom_product'], ENT_QUOTES, 'UTF-8') ?></h4>
                                <div class="text-sm text-gray-500 mb-2"><?= htmlspecialchars($review['created_at2'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-yellow-500">
                                        <?php for ($i = 0; $i < 5; $i++) { ?>
                                            <i class="fa<?= $i < $review['rating'] ? 's' : 'r' ?> fa-star"></i>
                                        <?php } ?>
                                    </span>
                                    <span class="text-sm text-gray-500">(<?= htmlspecialchars($review['rating'], ENT_QUOTES, 'UTF-8') ?> out of 5)</span>
                                </div>
                                <p class="text-sm text-black"><?= htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p class="text-gray-500">No reviews yet.</p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
