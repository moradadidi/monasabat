<?php
include_once("../includes/database.php");
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../public/login.php");
    exit();
}
$sql = "SELECT id_user, username, email,created_at, photo, is_admin FROM users WHERE is_admin=1 order by id_user desc limit 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Side Navbar - monasabat</title>

  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="../assets/css/table.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>

  <section class="sidebar">
    <div class="nav-header">
    <img class="h-10 w-10 rounded-full" src="pictures/profil.jpeg" id="pic" alt="">
      <i class="bx bx-menu-alt-right btn-menu"></i>
    </div>
    <ul class="nav-links">
      <li>
        <i class="bx bx-search search-btn"></i>
        <input type="text" placeholder="search..." />
        <span class="tooltip">Search</span>
      </li>
      <li>
        <a href="main.php">
          <i class="bx bx-home-alt-2"></i>
          <span class="title">Home</span>
        </a>
        <span class="tooltip">Home</span>
      </li>
      <li>
        <a href="show_product.php">
          <i class="bx bx-collection"></i>
          <span class="title">Products</span>
        </a>
        <span class="tooltip">Products</span>
      </li>
      <li>
        <a href="add_product.php">
          <i class="bx bx-duplicate"></i>
          <span class="title">Add Products</span>
        </a>
        <span class="tooltip">Add Products</span>
      </li>
      <li>
        <a href="categories.php">
          <i class="bx bx-wallet-alt"></i>
          <span class="title">Categories</span>
        </a>
        <span class="tooltip">Categories</span>
      </li>
      <li>
        <a href="add_categorie.php">
          <i class="bx bxs-devices"></i>
          <span class="title">Add Categories</span>
        </a>
        <span class="tooltip">Add Categories</span>
      </li>
      <li>
        <a href="#">
          <i class="bx bx-cog"></i>
          <span class="title">Setting</span>
        </a>
        <span class="tooltip">Setting</span>
      </li>
      <li>
        <a href="../includes/deconexion.php">
          <i class="bx bx-log-out"></i>
          <span class="title">log out</span>
        </a>
        <span class="tooltip">log out</span>
      </li>
    </ul>
    <div class="theme-wrapper">
      
      <i class="bx bxs-moon theme-icon"></i>
      <p>Dark Theme</p>
      <div class="theme-btn">
        <span class="theme-ball"></span>
        
      </div>
      
    </div>
   
  </section>

  <script>
    const btn_menu = document.querySelector(".btn-menu");
    const side_bar = document.querySelector(".sidebar");
    const pic = document.getElementById("pic");
    pic.style.display="none";
    btn_menu.addEventListener("click", function() {
      side_bar.classList.toggle("expand");
      changebtn();
    });

    function changebtn() {
      if (side_bar.classList.contains("expand")) {
        btn_menu.classList.replace("bx-menu", "bx-menu-alt-right");
        pic.style.display="block";
      } else {
        btn_menu.classList.replace("bx-menu-alt-right", "bx-menu");
        pic.style.display="none";
      }
    }
  </script>
</body>

</html>