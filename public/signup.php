<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");
?>

<section class="home">
    <form class="form_container my-14" method="post" action="">
    <div class="form_logo">
            <img src="../assets/images/logo.png" alt="form logo">
        </div>
        <div class="title_container">
            <p class="title">Create an Account</p>
            <span class="subtitle">Get started with our app, just create an account and enjoy the experience.</span>
        </div>
        <br>
        <div class="input_container">
            <label class="input_label" for="username_field">Username</label>
            <i class="fas fa-user icon"></i>
            <input placeholder="Username" title="Username" name="username" type="text" class="input_field" id="username_field" required>
        </div>
        <div class="input_container">
            <label class="input_label" for="email_field">Email</label>
            <i class="fas fa-envelope icon"></i>
            <input placeholder="name@mail.com" title="Email" name="email" type="email" class="input_field" id="email_field" required>
        </div>
        <div class="input_container">
            <label class="input_label" for="password_field">Password</label>
            <i class="fa-solid fa-key"></i>
            <input placeholder="Password" title="Password" name="password" type="password" class="input_field" id="password_field" required>
        </div>
        <div class="input_container">
            <label class="input_label" for="confirm_password_field">Confirm Password</label>
            <i class="fas fa-lock icon"></i>
            <input placeholder="Confirm Password" title="Confirm Password" name="confirm_password" type="password" class="input_field" id="confirm_password_field" required>
        </div>
        <button title="Sign Up" type="submit" class="sign-up_btn" name="sign_up">
            <span>Sign Up</span>
        </button>
        
        <div class="create_account_container">
            <span class="create_account_text">Already have an account?</span>
            <a href="login.php" class="create_account_link">Login</a>
        </div>
    </form>
</section>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sign_up'])) {
    include_once("../includes/database.php");

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "All fields are required.",
                icon: "error"
            });
        </script>';
        exit;
    }

    if ($password !== $confirm_password) {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Passwords do not match.",
                icon: "error"
            });
        </script>';
        exit;
    }

    // Check if the email is already in use
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Email is already in use.",
                icon: "error"
            });
        </script>';
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, mdp) VALUES (:username, :email, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo '<script>
            Swal.fire({
                title: "Success!",
                text: "Account created successfully.",
                icon: "success"
            }).then(function() {
                window.location.href = "login.php"; 
            });
        </script>';
        exit;
    } else {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "There was an error creating your account. Please try again.",
                icon: "error"
            });
        </script>';
        exit;
    }
}
?>


