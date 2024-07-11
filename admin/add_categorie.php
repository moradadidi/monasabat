<?php
include_once("dashbord.php");
include_once("../includes/database.php");

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Handle file upload
    $target_dir = "pictures/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["photo"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "Le fichier n'est pas une image.";
        $message_type = "error";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $message = "Désolé, le fichier existe déjà.";
        $message_type = "error";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["photo"]["size"] > 5000000) { // 5MB limit
        $message = "Désolé, votre fichier est trop grand.";
        $message_type = "error";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $message = "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
        $message_type = "error";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<script>
                window.addEventListener('DOMContentLoaded', (event) => {
                    Swal.fire({
                        icon: '$message_type',
                        title: 'Erreur!',
                        text: '$message',
                        showConfirmButton: true
                    });
                });
              </script>";
    } else {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // File is uploaded, now insert data into database
            $sql = "INSERT INTO categories (name, description, photo) VALUES (:name, :description, :photo)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':photo', $target_file);

            if ($stmt->execute()) {
                $message = "La catégorie " . htmlspecialchars($name) . " a été ajoutée.";
                $message_type = "success";
                echo "<script>
                        window.addEventListener('DOMContentLoaded', (event) => {
                            Swal.fire({
                                icon: '$message_type',
                                title: 'Succès!',
                                text: '$message',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.href = 'categories.php';
                            });
                        });
                      </script>";
            } else {
                $message = "Erreur : Impossible de sauvegarder les données dans la base de données.";
                $message_type = "error";
                echo "<script>
                        window.addEventListener('DOMContentLoaded', (event) => {
                            Swal.fire({
                                icon: '$message_type',
                                title: 'Erreur!',
                                text: '$message',
                                showConfirmButton: true
                            });
                        });
                      </script>";
            }
        } else {
            $message = "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
            $message_type = "error";
            echo "<script>
                    window.addEventListener('DOMContentLoaded', (event) => {
                        Swal.fire({
                            icon: '$message_type',
                            title: 'Erreur!',
                            text: '$message',
                            showConfirmButton: true
                        });
                    });
                  </script>";
        }
    }
}
?>


<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 w-3/6">
        <form action="" method="post" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl font-bold mb-6 text-center">Ajouter Catégorie</h1>
            
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom de la Catégorie</label>
                <input type="text" id="name" name="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea id="description" name="description" placeholder="Entrez une description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>

            <div class="mb-4">
                <input type="file" id="photo" name="photo" accept="image/*" hidden>
                <label for="photo" class="block text-gray-700 text-sm font-bold mb-2 cursor-pointer">
                    <div class="img-area border-2 border-dashed border-gray-400 p-6 rounded-lg text-center" data-img="">
                        <i class='bx bxs-cloud-upload icon text-4xl text-gray-500'></i>
                        <h3 class="text-orange-500">Télécharger l'image</h3>
                        <p class="text-gray-500">Télécharger une image ou une vidéo<span></span></p>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <input type="submit" name="submit" value="Ajouter" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            </div>
        </form>
    </div>
</body>
