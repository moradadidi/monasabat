<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}
$id_user=$_SESSION['id_user'];
$message_sent = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    try {
        $stmt = $pdo->prepare("INSERT INTO user_contacts (name,id_user, email, subject, message, created_at) VALUES (:name,$id_user, :email, :subject, :message, NOW())");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            $message_sent = true;
        } else {
            echo "Error: Could not execute query.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Contact Us</title>
    <style>
        .bg-custom {
            background-color: #ffffff;
            background-image: radial-gradient(at 12% 45%, #32CD32 40%, transparent 20%),
                radial-gradient(at 62% 33%, #ff7a00 50%, transparent 50%);
        }
        .form-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .contact-info {
            background: #10b981;
            color: #ffffff;
            border-radius: 15px;
        }
        .contact button {
            background-color: #32CD32;
        }
    </style>
</head>
<body>
    <div class="home contact bg-custom min-h-screen flex flex-col justify-center items-center p-4">
        <section class="form-container mt-14 max-w-4xl w-full p-8 md:p-12">
            <h2 class="mb-6 text-3xl font-bold text-center text-gray-900">Contact Us</h2>
            <p class="mb-8 text-center text-gray-700">Have a question? Need help with a product? Looking for more information? We're here to help.</p>
            <form action="" method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Your name</label>
                    <input type="text" id="name" name="name" class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="Your name" required>
                </div>
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Your email</label>
                    <input type="email" id="email" name="email" class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="name@example.com" required>
                </div>
                <div>
                    <label for="subject" class="block mb-2 text-sm font-medium text-gray-900">Subject</label>
                    <input type="text" id="subject" name="subject" class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="How can we help you?" required>
                </div>
                <div>
                    <label for="message" class="block mb-2 text-sm font-medium text-gray-900">Your message</label>
                    <textarea id="message" name="message" rows="6" class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="Your message here..." required></textarea>
                </div>
                <button type="submit" class="w-full py-3 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300">Send Message</button>
            </form>
        </section>

        <section class="contact-info max-w-4xl w-full p-8 mt-12 md:mt-16 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="p-6 text-center">
                <i class="fa fa-envelope text-6xl mb-4"></i>
                <h3 class="text-2xl font-semibold">Email Us</h3>
                <p class="mt-2">For general queries, including partnership opportunities.</p>
                <a href="mailto:monasabatweb@gmail.com" class="mt-2 block text-orange-200 hover:text-white">monasabatweb@gmail.com</a>
            </div>
            <div class="p-6 text-center">
                <i class="fa fa-phone text-6xl mb-4"></i>
                <h3 class="text-2xl font-semibold">Call Us</h3>
                <p class="mt-2">Speak to a member of our team. We are always happy to help.</p>
                <a href="tel:+212678650605" class="mt-2 block text-orange-200 hover:text-white">+212-6 786-50605</a>
            </div>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($message_sent): ?>
            Swal.fire({
                title: 'Success!',
                text: 'Your message has been sent.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>
</html>
