<?php
include_once("../includes/database.php");
include_once("../includes/navbar.php");

// Fetch user profile information
$user_id = $_SESSION['id_user'] ?? null;
$userProfile = null;
$cartItems = [];
$favorites = [];
$reviews = [];
$message = '';

if ($user_id) {
    $userStmt = $pdo->prepare("SELECT username, email, photo, created_at, is_admin, tel FROM users WHERE id_user = ?");
    $userStmt->execute([$user_id]);
    $userProfile = $userStmt->fetch(PDO::FETCH_ASSOC);

    // Fetch user cart items
    $cartStmt = $pdo->prepare("SELECT c.quantity, p.nom_product, p.photo, p.price FROM cart c JOIN products p ON c.id_product = p.id_product WHERE c.id_user = ? LIMIT 3");
    $cartStmt->execute([$user_id]);
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch first 3 user favorite items
    $favoriteStmt = $pdo->prepare("SELECT p.nom_product, p.photo, p.price FROM favorites f JOIN products p ON f.product_id = p.id_product WHERE f.user_id = ? LIMIT 3");
    $favoriteStmt->execute([$user_id]);
    $favorites = $favoriteStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch first 3 user reviews
    $reviewStmt = $pdo->prepare("SELECT r.rating, r.comment, p.nom_product, p.photo FROM review r JOIN products p ON r.id_product = p.id_product WHERE r.id_user = ? LIMIT 3");
    $reviewStmt->execute([$user_id]);
    $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Update Username
        if (isset($_POST['save_username'])) {
            $username = $_POST["username"];
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id_user = ?");
            $stmt->execute([$username, $user_id]);
            if ($stmt->rowCount() > 0) {
                $message = "Username updated successfully.";
            } else {
                $message = "Error: Unable to update username.";
            }
            
        }

        // Update Email
        if (isset($_POST['save_email'])) {
            $email = $_POST["email"];
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id_user = ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->rowCount() > 0) {
                $message = "Email updated successfully.";
            } else {
                $message = "Error: Unable to update email.";
            }
        }

        // Update Phone
        if (isset($_POST['save_phone'])) {
            $phone = $_POST["phone"];
            $stmt = $pdo->prepare("UPDATE users SET tel = ? WHERE id_user = ?");
            $stmt->execute([$phone, $user_id]);
            if ($stmt->rowCount() > 0) {
                $message = "Phone number updated successfully.";
            } else {
                $message = "Error: Unable to update phone number.";
            }
        }

        // Update Photo
        if (isset($_POST['save_photo'])) {
            if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
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
                    $uploadOk = 0;
                }

                // Allow certain file formats
                if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }

                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    $message = "Sorry, your file was not uploaded.";
                } else {
                    // if everything is ok, try to upload file
                    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                        $photo_path = $target_file;
                        $stmt = $pdo->prepare("UPDATE users SET photo = ? WHERE id_user = ?");
                        $stmt->execute([$photo_path, $user_id]);
                        if ($stmt->rowCount() > 0) {
                            $message = "Photo updated successfully.";
                        } else {
                            $message = "Error: Unable to update photo.";
                        }
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                    }
                }
            }
        }

        // Update Password
        if (isset($_POST['reset_password'])) {
            $old_password = $_POST['old_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if ($new_password === $confirm_password) {
                // Fetch the current password hash from the database
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id_user = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($old_password, $user['password'])) {
                    // Update the password
                    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id_user = ?");
                    $updateStmt->execute([$new_password_hash, $user_id]);

                    if ($updateStmt->rowCount() > 0) {
                        $message = "Password reset successfully.";
                    } else {
                        $message = "Error: Unable to reset the password.";
                    }
                } else {
                    $message = "Old password is incorrect.";
                }
            } else {
                $message = "New passwords do not match.";
            }
        }
    }
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
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/tailwind-output.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .prof {
            border-radius: 47% 53% 88% 12% / 49% 32% 68% 51%;
        }
    </style>
</head>

<body class="home bg-gray-100">
    <div class="container mx-auto mt-10 p-5">
        <h1 class="text-4xl font-bold text-white mb-6 text-center">User Profile</h1>
        <div class="flex justify-center">
            <!-- User Profile Section -->
            <div class="prof bg-gray-200 text-black shadow-md -mt-16 text-center py-28  w-2/5 h-1/5">
                <div class="flex items-center justify-center space-x-6 mb-4">
                    <img src="pictures/<?= htmlspecialchars($userProfile['photo'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-full w-24 h-24 shadow-md border-4 border-gray-300 hover:border-orange-300" alt="Profile">
                    <div>
                        <h3 class="text-4xl font-bold text-black"><?= htmlspecialchars($userProfile['username'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="text-lg text-black"><?= $userProfile['is_admin'] ? 'Admin Profile' : 'User Profile' ?></p>
                        <p class="text-lg text-black">Member since: <?= htmlspecialchars(date('d/m/Y', strtotime($userProfile['created_at'])), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <div class="flex justify-center space-x-4 mb-4">
                    <div class="flex items-center text-xl text-black">
                        <i class="fas fa-envelope mr-2"></i>
                        <span><?= htmlspecialchars($userProfile['email'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>
                <div class="flex justify-center space-x-4 mb-4">
                    <div class="flex items-center text-xl text-black">
                        <i class="fas fa-phone mr-2"></i>
                        <span><?= htmlspecialchars($userProfile['tel'] ?? 'No phone number provided', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>

                <div class="flex items-center flex-col gap-4">
                    <!-- Edit Username Button -->
                    <button onclick="toggleModal('usernameModal')" class="bg-orange-500 text-white px-4 py-2 w-4/12 rounded"><i class="fa-solid fa-user"></i> Edit Username </button>

                    <!-- Edit Email Button -->
                    <button onclick="toggleModal('emailModal')" class="bg-orange-500 text-white px-4 py-2 w-4/12 rounded"><i class="fa-solid fa-envelope"></i> Edit Email</button>

                    <!-- Edit Phone Button -->
                    <button onclick="toggleModal('phoneModal')" class="bg-orange-500 text-white px-4 py-2 w-4/12 rounded"><i class="fa-solid fa-phone"></i> Edit Phone</button>

                    <!-- Edit Photo Button -->
                    <button onclick="toggleModal('photoModal')" class="bg-orange-500 text-white px-4 py-2 w-4/12 rounded"><i class="fa-solid fa-image"></i> Edit Photo</button>

                    <!-- Edit Password Button -->
                    <button onclick="toggleModal('passwordModal')" class="bg-orange-500 text-white px-4 py-2 w-4/12 rounded"><i class="fa-solid fa-lock"></i> Change Password</button>

                    <!-- Logout Button -->
                    <a href="../includes/delete_acc.php" class="bg-red-500 hover:bg-red-700 text-gray-100 font-bold py-2 px-4 rounded inline-flex items-center w-4/12 justify-center">
                        <span>Delete Account </span>
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </div>

            </div>

            <!-- User Content Section -->
            <div class="ml-auto w-1/2">
                <!-- User Cart Items -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Cart Items</h3>
                    <?php if (!empty($cartItems)) { ?>
                        <ul>
                            <?php foreach ($cartItems as $item) { ?>
                                <li class="flex items-center space-x-4 mb-4">
                                    <img src="../admin/<?= htmlspecialchars($item['photo'], ENT_QUOTES, 'UTF-8') ?>" class="w-16 h-16 object-cover rounded-md" alt="<?= htmlspecialchars($item['nom_product'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold"><?= htmlspecialchars($item['nom_product'], ENT_QUOTES, 'UTF-8') ?></h4>
                                        <p class="text-sm text-gray-500">Quantity: <?= htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="text-sm text-gray-500">Price: $<?= htmlspecialchars(number_format($item['price'], 2), ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                        <a href="cart.php" class="text-blue-500 hover:underline">See all items in cart <i class="fa-solid fa-right-long"></i></a>

                    <?php } else { ?>
                        <p class="text-gray-500">No items in cart.</p>
                    <?php } ?>
                </div>

                <!-- User Favorites -->
                <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Favorite Products</h3>
                    <?php if (!empty($favorites)) { ?>
                        <ul>
                            <?php foreach ($favorites as $favorite) { ?>
                                <li class="flex items-center space-x-4 mb-4">
                                    <img src="../admin/<?= htmlspecialchars($favorite['photo'], ENT_QUOTES, 'UTF-8') ?>" class="w-16 h-16 object-cover rounded-md" alt="<?= htmlspecialchars($favorite['nom_product'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold"><?= htmlspecialchars($favorite['nom_product'], ENT_QUOTES, 'UTF-8') ?></h4>
                                        <p class="text-sm text-gray-500">Price: $<?= htmlspecialchars(number_format($favorite['price'], 2), ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                        <a href="favorite.php" class="text-blue-500 hover:underline">See all favorite products <i class="fa-solid fa-right-long"></i></a>
                    <?php } else { ?>
                        <p class="text-gray-500">No favorite products.</p>
                    <?php } ?>
                </div>

                <!-- User Reviews -->
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Reviews</h3>
                    <?php if (!empty($reviews)) { ?>
                        <ul>
                            <?php foreach ($reviews as $review) { ?>
                                <li class="flex items-center space-x-4 mb-4">
                                    <img src="../admin/<?= htmlspecialchars($review['photo'], ENT_QUOTES, 'UTF-8') ?>" class="w-16 h-16 object-cover rounded-md" alt="<?= htmlspecialchars($review['nom_product'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold"><?= htmlspecialchars($review['nom_product'], ENT_QUOTES, 'UTF-8') ?></h4>
                                        <p class="text-sm text-gray-500">Rating: <?= htmlspecialchars($review['rating'], ENT_QUOTES, 'UTF-8') ?>/5</p>
                                        <p class="text-sm text-gray-500">Comment: <?= htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                        <a href="all_reviews.php" class="text-blue-500 hover:underline">See all reviews <i class="fa-solid fa-right-long"></i></a>
                    <?php } else { ?>
                        <p class="text-gray-500">No reviews yet.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals (Username, Tel ,Email, Photo, Password) -->
    <div id="usernameModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="form-logo">
                <img src="../assets/images/logo.png" alt="Logo">
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Username</h3>
                <form action="" method="post">
                    <input type="text" name="username" class="w-full p-2 border rounded mt-2" value="<?= htmlspecialchars($userProfile['username'], ENT_QUOTES, 'UTF-8') ?>" required>
                    <div class="items-center px-4 py-3">
                        <button type="submit" name="save_username" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700">Save</button>
                        <button type="button" onclick="toggleModal('usernameModal')" class="mt-2 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="form-logo">
                <img src="../assets/images/logo.png" alt="Logo">
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Email</h3>
                <form action="" method="post">
                    <input type="email" name="email" class="w-full p-2 border rounded mt-2" value="<?= htmlspecialchars($userProfile['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    <div class="items-center px-4 py-3">
                        <button type="submit" name="save_email" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700">Save</button>
                        <button type="button" onclick="toggleModal('emailModal')" class="mt-2 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Phone Modal -->
    <div id="phoneModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h3 class="text-2xl mb-4">Edit Phone</h3>
            <form action="" method="POST">
                <input type="hidden" name="edit_phone" value="1">
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700">New Phone:</label>
                    <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border rounded">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="toggleModal('phoneModal')" id="num" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                    <button type="submit" name="save_phone" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>
    </div>


    <div id="photoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="form-logo">
                <img src="../assets/images/logo.png" alt="Logo">
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Photo</h3>
                <form action="profil.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="photo" class="w-full p-2 border rounded mt-2" required>
                    <div class="items-center px-4 py-3">
                        <button type="submit" name="save_photo" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700">Save</button>
                        <button type="button" onclick="toggleModal('photoModal')" class="mt-2 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="form-logo">
                <img src="../assets/images/logo.png" alt="Logo">
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Change Password</h3>
                <form action="profil.php" method="post">
                    <input type="password" name="old_password" class="w-full p-2 border rounded mt-2" placeholder="Old Password" required>
                    <input type="password" name="new_password" class="w-full p-2 border rounded mt-2" placeholder="New Password" required>
                    <input type="password" name="confirm_password" class="w-full p-2 border rounded mt-2" placeholder="Confirm Password" required>
                    <div class="items-center px-4 py-3">
                        <button type="submit" name="reset_password" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700">Reset</button>
                        <button type="button" onclick="toggleModal('passwordModal')" class="mt-2 px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleModal(modalId) {
            var modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
        }

        // Close modals when clicking the close button
        

        // document.getElementById("num").addEventListener("click",()=>{
        //     window.location.href = "http://localhost/monasabat2/public/profil.php";
        // })
    </script>
    <?php if ($message) : ?>
        <script>
            function toggleModal(modalId) {
            var modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
        }

            window.addEventListener("DOMContentLoaded", (event) => {
                        Swal.fire({
                            icon: "info",
                            title: "Notification!",
                            text: "<?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>",
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "profil.php";
                        });
                    });
                    var closeButtons = document.querySelectorAll('.close');
        closeButtons.forEach(function(button) {
            button.onclick = function() {
                button.parentElement.parentElement.classList.add('hidden');
            }
        });

        // // Only reload once after form submission
        // if (performance.navigation.type === 1) {
        //     // Page was reloaded
        //     window.history.replaceState(null, null, window.location.href);
        // }

        </script>
    <?php endif; ?>
</body>

</html>