document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    let searchForm = document.querySelector('.search-form');
    let shoppingCart = document.querySelector('.shopping-cart');
    let loginForm = document.querySelector('.login-form');
    let navbar = document.querySelector('.navbar');
    let profileForm = document.getElementById('profile-form');
    
    let searchBtn = document.getElementById('search-btn');
    if (searchBtn) {
        searchBtn.onclick = () => {
            searchForm.classList.toggle('active');
            shoppingCart.classList.remove('active');
            loginForm.classList.remove('active');
            navbar.classList.remove('active');
            profileForm.classList.add('hidden');
        }
    }

    let cartBtn = document.getElementById('cart-btn');
    if (cartBtn) {
        cartBtn.onclick = () => {
            console.log('DOM fully loaded and parsed');
            window.location.href = "http://localhost/monasabat2/public/cart.php";
        }
    }

    let favorits = document.getElementById('fav-btn');
    if (favorits) {
        favorits.onclick = () => {
            console.log('DOM fully loaded and parsed');
            window.location.href = "http://localhost/monasabat2/public/favorite.php";
        }
    }

    let logo = document.getElementById("logo");
    if (logo) {
        logo.addEventListener("click", () => {
            console.log('DOM fully loaded and parsed');
            window.location.href = "http://localhost/monasabat2/public/main.php";
        });
    }

    let menuBtn = document.getElementById('menu-btn');
    if (menuBtn) {
        menuBtn.onclick = () => {
            console.log('DOM fully loaded and parsed');
            navbar.classList.toggle('active');
            searchForm.classList.remove('active');
            shoppingCart.classList.remove('active');
            loginForm.classList.remove('active');
            profileForm.classList.add('hidden');
        }
    }

    let profilBtn = document.getElementById('profil-btn');
    if (profilBtn) {
        profilBtn.onclick = (event) => {
            event.stopPropagation();
            profileForm.classList.toggle('hidden');
            searchForm.classList.remove('active');
            shoppingCart.classList.remove('active');
            loginForm.classList.remove('active');
            navbar.classList.remove('active');
        }
    }

    document.addEventListener('click', function(event) {
        if (!profileForm.contains(event.target) && !profilBtn.contains(event.target)) {
            profileForm.classList.add('hidden');
        }
    });

    document.querySelectorAll('.add-to-cart-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            let formData = new FormData(form);

            fetch('cart.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      Swal.fire({
                          title: 'Added to cart!',
                          text: 'Product added to your cart successfully.',
                          icon: 'success',
                          confirmButtonText: 'OK'
                      });
                  } else {
                      Swal.fire({
                          title: 'Error!',
                          text: 'There was an error adding the product to your cart.',
                          icon: 'error',
                          confirmButtonText: 'OK'
                      });
                  }
              }).catch(error => {
                  Swal.fire({
                      title: 'Error!',
                      text: 'There was an error adding the product to your cart.',
                      icon: 'error',
                      confirmButtonText: 'OK'
                  });
              });
        });
    });
});
