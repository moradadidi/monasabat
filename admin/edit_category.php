<?php
include_once("dashbord.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: ../public/login.php");
    exit();
}
$id_category = $_POST["id_category"];

// Fetch category details
$sql_category = "SELECT * FROM categories WHERE id_category=?";
$stmt_category = $pdo->prepare($sql_category);
$stmt_category->execute([$id_category]);
$category = $stmt_category->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $target_dir = "pictures/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Create target directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Check if the file is an actual image
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "The file is not an image.";
        $uploadOk = 0;
    }

    // Rename file if it already exists
    if (file_exists($target_file)) {
        $i = 1;
        while (file_exists($target_file)) {
            $target_file = $target_dir . pathinfo($_FILES["photo"]["name"], PATHINFO_FILENAME) . "_{$i}." . $imageFileType;
            $i++;
        }
    }

    // Check file size
    if ($_FILES["photo"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Attempt to upload file
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $sql = "UPDATE categories SET name = :name, photo = :photo, description = :description WHERE id_category = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':photo', $target_file);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                echo "The file " . htmlspecialchars(basename($_FILES["photo"]["name"])) . " has been uploaded.";
                header("location: show_categories.php");
            } else {
                echo "Error: Unable to save data in the database.";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Catégorie</title>
    <link rel="stylesheet" href="path/to/your/custom.css"> <!-- Ensure this points to your actual CSS file path -->
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 w-3/6">
        <form action="" method="post" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold mb-6 text-center">Modifier Catégorie</h1>

            <input type="hidden" id="id" name="id" value="<?= htmlspecialchars($category['id_category']) ?>" required>

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom de la Catégorie</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" placeholder="Entrez une description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?= htmlspecialchars($category['description']) ?></textarea>
            </div>

            <div class="mb-4">
                <input type="file" id="file" name="photo" accept="image/*" hidden>
                <label for="file" class="block text-gray-700 text-sm font-bold mb-2 cursor-pointer">
                    <div class="img-area border-2 border-dashed border-gray-400 p-6 rounded-lg text-center" data-img="">
                        <i class='bx bxs-cloud-upload icon text-4xl text-gray-500'></i>
                        <h3 class="text-gray-500">Télécharger l'image</h3>
                        <p class="text-gray-500">Télécharger une image ou une vidéo<span></span></p>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <input type="submit" name="submit" value="Télécharger" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            </div>
        </form>
    </div>
</body>
</html>
