

INSERT INTO `cart` (`id_cart`, `id_user`, `id_product`, `quantity`, `created_at`) VALUES
(1, 5, 3, 4, '2024-06-06 23:28:02'),
(3, 5, 4, 11, '2024-06-06 23:50:59'),
(4, 5, 2, 6, '2024-06-06 23:51:17'),
(5, 5, 6, 3, '2024-06-13 20:59:32');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_category`, `name`, `description`, `created_at`, `photo`) VALUES
(1, 'Electronics', 'Devices and gadgets including phones, laptops, and more.', '2024-06-03 18:45:11', 'pictures/electronic.jpg'),
(2, 'Books', ' A wide variety of books across different genres.', '2024-06-03 18:46:26', 'pictures/thought-catalog-o0Qqw21-0NI-unsplash.jpg'),
(3, 'Men\'s clothing', 'Men\'s clothing encompasses a wide range of styles from formal suits to casual jeans, designed to suit various occasions and personal tastes.', '2024-06-03 18:48:41', 'pictures/ns-37rVmK3jY-c-unsplash.jpg'),
(4, 'Women\'s Clothing', 'Women\'s clothing offers a diverse array of styles, from elegant dresses to casual jeans, catering to different occasions and fashion preferences.', '2024-06-03 18:50:21', 'pictures/kate-skumen-XsFiUIamdTo-unsplash.jpg'),
(5, 'Snacks', 'Snacks encompass a variety of small, convenient foods, from healthy fruit and nuts to indulgent chips and cookies, designed to satisfy cravings between meals.', '2024-06-03 18:51:32', 'pictures/famous_snack.jpeg'),
(6, 'Sport', 'Sport involves physical activities and competitions, promoting fitness and teamwork.', '2024-06-03 18:52:56', 'pictures/sport_product.jpeg');

-- --------------------------------------------------------

--
-- Structure de la table `favorites`
--

CREATE TABLE `favorites` (
  `id_favorite` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `favorites`
--

INSERT INTO `favorites` (`id_favorite`, `user_id`, `product_id`, `created_at`) VALUES
(4, 5, NULL, '2024-06-15 02:14:32'),
(5, 5, NULL, '2024-06-15 02:14:47'),
(6, 5, 6, '2024-06-15 02:14:54'),
(7, 5, 4, '2024-06-15 02:18:45');

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

CREATE TABLE `products` (
  `id_product` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `photo` varchar(150) DEFAULT NULL,
  `nom_product` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id_product`, `category_id`, `photo`, `nom_product`, `price`, `quantity`, `description`, `created_at`) VALUES
(2, 4, 'pictures/jalaba_3.jpeg', 'jalaba', 130.00, 15, 'women clothes cgivgc ', '2024-06-03 18:57:16'),
(3, 3, 'pictures/fo9iya.jpeg', 'fokiya', 125.00, 120, 'fb vg  gcvtvty jhvvy hbuy', '2024-06-03 19:11:28'),
(4, 5, 'pictures/chbakiya.jpeg', 'chbakiya', 11.00, 541, 'vhbvuhv  guyuf igugyui', '2024-06-03 19:13:48'),
(5, 4, 'pictures/jalaba_4.jpeg', '7alwa', 9.00, 148, ' bghvv hvyvyv hgvuvty', '2024-06-03 19:14:16'),
(6, 5, 'pictures/plateau_1.jpeg', '7alwa', 4.00, 552, 'trbfbg', '2024-06-04 22:46:21');

-- --------------------------------------------------------

--
-- Structure de la table `review`
--

CREATE TABLE `review` (
  `id_review` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `review`
--

INSERT INTO `review` (`id_review`, `id_product`, `id_user`, `rating`, `comment`, `created_at`) VALUES
(2, 4, 5, 4, 'vhvhgyv ghckj hg', '2024-06-15 17:39:26'),
(3, 4, 5, 3, 'its baddd', '2024-06-15 17:45:27');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `photo` varchar(150) DEFAULT 'defaultpic.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `tel` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `mdp`, `photo`, `created_at`, `is_admin`) VALUES
(1, 'Smith_John', 'john.smith@example.com', 'password123', 'defaulpic.jpg', '2024-06-03 16:48:12', 1),
(2, 'Doe_Jane', 'jane.doe@example.com', 'password456', 'defaulpic.jpg', '2024-06-03 16:48:12', 0),
(3, 'Brown_Charlie', 'charlie.brown@example.com', 'password789', 'defaulpic.jpg', '2024-06-03 16:48:12', 0),
(4, 'admin', 'admin@gmail.com', 'admin', 'profil.jpeg', '2024-06-03 16:48:34', 1),
(5, 'adidi_morad', 'moradadidi95@gmail.com', '$2y$10$CTGJKqQmGfzq8T7ZP7CNSOpc7R25mZVQsArEV.aOEAo.Fs8dtu3ka', 'defaulpic.jpg', '2024-06-04 22:45:15', 0),
(6, 'morad_adidi', 'adigyu7@gmail.com', '$2y$10$pdg4Tfsls/X/rztd7lflJOI.z.XFq43y3DgfvWOkNWlwM/BgwvQWy', 'defaulpic.jpg', '2024-06-04 22:47:07', 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id_cart`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_product` (`id_product`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`);

--
-- Index pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id_favorite`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Index pour la table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cart`
--
ALTER TABLE `cart`
  MODIFY `id_cart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id_favorite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`);

--
-- Contraintes pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`);

--
-- Contraintes pour la table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id_category`);

--
-- Contraintes pour la table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
