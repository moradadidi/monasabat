<?php
include_once("../includes/database.php");

$id_product = $_GET['id'];

$stmt = $pdo->prepare("SELECT review.*, users.username FROM review JOIN users ON review.id_user = users.id_user WHERE id_product = :id_product ORDER BY created_at DESC");
$stmt->execute(['id_product' => $id_product]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


    <section class="home">
        <h2>Product Reviews</h2>
        <?php if ($reviews): ?>
            <ul class="list-group">
                <?php foreach ($reviews as $review): ?>
                    <li class="list-group-item">
                        <h5><?php echo htmlspecialchars($review['username']); ?></h5>
                        <p>Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                        <p><?php echo htmlspecialchars($review['comment']); ?></p>
                        <small>Reviewed on: <?php echo htmlspecialchars($review['created_at']); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
    </section>


