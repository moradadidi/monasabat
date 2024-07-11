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
            <p class="title">Login to your Account</p>
            <span class="subtitle">Get started with our app, just create an account and enjoy the experience.</span>
        </div>
        <br>
        <div class="input_container">
            <label class="input_label" for="email_field">Email</label>
            <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg" class="icon">
                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" stroke="#141B34" d="M7 8.5L9.94202 10.2394C11.6572 11.2535 12.3428 11.2535 14.058 10.2394L17 8.5"></path>
                <path stroke-linejoin="round" stroke-width="1.5" stroke="#141B34" d="M2.01577 13.4756C2.08114 16.5412 2.11383 18.0739 3.24496 19.2094C4.37608 20.3448 5.95033 20.3843 9.09883 20.4634C11.0393 20.5122 12.9607 20.5122 14.9012 20.4634C18.0497 20.3843 19.6239 20.3448 20.7551 19.2094C21.8862 18.0739 21.9189 16.5412 21.9842 13.4756C22.0053 12.4899 22.0053 11.5101 21.9842 10.5244C21.9189 7.45886 21.8862 5.92609 20.7551 4.79066C19.6239 3.65523 18.0497 3.61568 14.9012 3.53657C12.9607 3.48781 11.0393 3.48781 9.09882 3.53656C5.95033 3.61566 4.37608 3.65521 3.24495 4.79065C2.11382 5.92608 2.08114 7.45885 2.01576 10.5244C1.99474 11.5101 1.99475 12.4899 2.01577 13.4756Z"></path>
            </svg>
            <input placeholder="name@mail.com" title="Email" name="email" type="email" class="input_field" id="email_field" >
        </div>
        <div class="input_container">
            <label class="input_label" for="password_field">Password</label>
            <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg" class="icon">
                <path stroke-linecap="round" stroke-width="1.5" stroke="#141B34" d="M18 11.0041C17.4166 9.91704 16.273 9.15775 14.9519 9.0993C13.477 9.03404 11.9788 9 10.329 9C8.67911 9 7.18091 9.03404 5.70604 9.0993C3.95328 9.17685 2.51295 10.4881 2.27882 12.1618C2.12602 13.2541 2 14.3734 2 15.5134C2 16.6534 2.12602 17.7727 2.27882 18.865C2.51295 20.5387 3.95328 21.8499 5.70604 21.9275C6.42013 21.9591 7.26041 21.9834 8 22"></path>
                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" stroke="#141B34" d="M6 9V6.5C6 4.01472 8.01472 2 10.5 2C12.9853 2 15 4.01472 15 6.5V9"></path>
            </svg>
            <input placeholder="Password" title="Password" name="password" type="password" class="input_field" id="password_field" >
        </div>
        <button title="Sign In" type="submit" class="sign-in_btn" name="sign_in">
            <span>Sign In</span>
        </button>
        
        <div class="create_account_container">
            <span class="create_account_text">Don't have an account?</span>
            <a href="signup.php" class="create_account_link">Create an account</a>
        </div>
    </form>
</section>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sign_in'])) {
    include_once("../includes/database.php");

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "All fields are required.",
                icon: "error"
            });
        </script>';
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['mdp'])) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];

        if ($user['is_admin']) {
            echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "Admin login successful.",
                    icon: "success"
                }).then(function() {
                    window.location.href = "../admin/main.php"; 
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    title: "Success!",
                    text: "Login successful.",
                    icon: "success"
                }).then(function() {
                    window.location.href = "main.php"; 
                });
            </script>';
        }
        exit;
    } else {
        echo '<script>
            Swal.fire({
                title: "Error!",
                text: "Invalid email or password.",
                icon: "error"
            });
        </script>';
        exit;
    }
}
?>
