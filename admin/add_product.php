<?php
include_once("dashbord.php");
include_once("../includes/database.php");

// Fetch categories
$sql_categories = "SELECT id_category, name FROM categories";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $category_id = $_POST['category_id'];
    $nom_product = $_POST['nom_product'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $target_dir = "pictures/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Create target directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Check if file is a real image
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "The file is not an image.";
        $message_type = "error";
        $uploadOk = 0;
    }

    // Rename file if a file with the same name exists
    if (file_exists($target_file)) {
        $i = 1;
        while (file_exists($target_file)) {
            $target_file = $target_dir . pathinfo($_FILES["photo"]["name"], PATHINFO_FILENAME) . "_{$i}." . $imageFileType;
            $i++;
        }
    }

    // Check file size
    if ($_FILES["photo"]["size"] > 500000) {
        $message = "Sorry, your file is too large.";
        $message_type = "error";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $message_type = "error";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $message = "Sorry, your file was not uploaded.";
        $message_type = "error";
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO products (category_id, photo, nom_product, price, quantity, description) VALUES (:category_id, :photo, :nom_product, :price, :quantity, :description)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':photo', $target_file);
            $stmt->bindParam(':nom_product', $nom_product);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':description', $description);

            if ($stmt->execute()) {
                $message = "Product added successfully.";
                $message_type = "success";
            } else {
                $message = "Error: Unable to save data in the database.";
                $message_type = "error";
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
            $message_type = "error";
        }
    }

    echo "<script>
            window.addEventListener('DOMContentLoaded', (event) => {
                Swal.fire({
                    icon: '$message_type',
                    title: 'Notification',
                    text: '$message',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    if ('$message_type' === 'success') {
                        window.location.href = 'show_product.php';
                    }
                });
            });
          </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 w-3/6">
        <form action="" method="post" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold mb-6 text-center">Add Product</h1>
            
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                <select id="category_id" name="category_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id_category']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="nom_product" class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
                <input type="text" id="nom_product" name="nom_product" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price</label>
                <input type="number" id="price" name="price" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                <input type="number" id="quantity" name="quantity" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <input type="file" id="file" name="photo" accept="image/*" hidden>
                <label for="file" class="block text-gray-700 text-sm font-bold mb-2 cursor-pointer">
                    <div class="img-area border-2 border-dashed border-gray-400 p-6 rounded-lg text-center" data-img="">
                        <i class='bx bxs-cloud-upload icon text-4xl text-gray-500'></i>
                        <h3 class="text-gray-500">Upload Image</h3>
                        <p class="text-gray-500">Upload an image or a video<span></span></p>
                    </div>
                </label>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" placeholder="Enter a description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>

            <div class="flex items-center justify-between">
                <input type="submit" name="submit" value="Upload" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            </div>
        </form>
    </div>
</body>
</html>
