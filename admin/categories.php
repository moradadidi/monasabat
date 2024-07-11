<?php
include_once("dashbord.php");
include_once("../includes/database.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);

    if ($stmt->execute()) {
        $message = "La catégorie " . htmlspecialchars($name) . " a été ajoutée.";
        echo "<script>
                window.addEventListener('DOMContentLoaded', (event) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès!',
                        text: '$message',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                    });
                });
              </script>";
    } else {
        $message = "Erreur : Impossible de sauvegarder les données dans la base de données.";
        echo "<script>
                window.addEventListener('DOMContentLoaded', (event) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: '$message',
                        showConfirmButton: true
                    });
                });
              </script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["del"])) {
    $id_category = $_POST['id_category'];

    $sql = "DELETE FROM categories WHERE id_category = :id_category";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_category', $id_category);

    if ($stmt->execute()) {
        $message = "La catégorie a été supprimée.";
        echo "<script>
                window.addEventListener('DOMContentLoaded', (event) => {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Succès!',
                        text: '$message',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '" . $_SERVER['PHP_SELF'] . "';
                    });
                });
              </script>";
    } else {
        $message = "Erreur : Impossible de supprimer la catégorie.";
        echo "<script>
                window.addEventListener('DOMContentLoaded', (event) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: '$message',
                        showConfirmButton: true
                    });
                });
              </script>";
    }
}

// Fetch categories
$sql_categories = "SELECT id_category, name, description, created_at, photo FROM categories";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="home" style="background-color:white">
    <div class="container">
        <h2>Catégories Existantes</h2>
        <div class="table-container mt-12 shadow-sm border rounded-lg overflow-x-auto">
            <table class="w-full table-auto text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                    <tr class="text-center">
                        <th class="py-3 px-6">ID</th>
                        <th class="py-3 px-6">Image</th>
                        <th class="py-3 px-6">Nom</th>
                        <th class="py-3 px-6">Description</th>
                        <th class="py-3 px-6">Date de Création</th>
                        <th class="py-3 px-6">Delete</th>
                        <th class="py-3 px-6">Edit</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <tr class="text-center">
                                <td class="py-3 px-6"><?= htmlspecialchars($category['id_category']) ?></td>
                                <td>
                                    <?php if (!empty($category['photo'])): ?>
                                        <img src="<?= htmlspecialchars($category['photo']) ?>" alt="<?= htmlspecialchars($category['name']) ?>" style="width: 100px; height: auto;">
                                    <?php else: ?>
                                        <span>Aucune image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= htmlspecialchars($category['description']) ?></td>
                                <td><?= htmlspecialchars($category['created_at']) ?></td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="id_category" value="<?= htmlspecialchars($category['id_category']) ?>">
                                        <button class="del" name="del"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                                <td>
                                    <form action="edit_category.php" method="post">
                                        <input type="hidden" name="id_category" value="<?= htmlspecialchars($category['id_category']) ?>">
                                        <button class="edit" name="edit"><i class="bx bx-pencil"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">Aucune catégorie trouvée</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
</body>
</html>
