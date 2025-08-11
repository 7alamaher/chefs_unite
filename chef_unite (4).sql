-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 10:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chef_unite`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmarks`
--

INSERT INTO `bookmarks` (`id`, `recipe_id`, `user_id`, `created_at`) VALUES
(15, 3, 7, '2025-08-11 01:38:26'),
(16, 3, 9, '2025-08-11 01:48:36'),
(17, 1, 9, '2025-08-11 01:48:41'),
(18, 4, 10, '2025-08-11 01:52:54'),
(19, 3, 13, '2025-08-11 02:25:34'),
(20, 3, 6, '2025-08-11 05:35:34'),
(21, 1, 6, '2025-08-11 05:59:51');

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) DEFAULT NULL,
  `following_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follows`
--

INSERT INTO `follows` (`id`, `follower_id`, `following_id`, `created_at`) VALUES
(1, 7, 8, '2025-08-11 01:17:22'),
(2, 9, 8, '2025-08-11 01:48:58'),
(3, 11, 11, '2025-08-11 01:58:56'),
(4, 13, 7, '2025-08-11 02:20:56'),
(7, 13, 8, '2025-08-11 02:25:59'),
(8, 13, 10, '2025-08-11 02:31:31'),
(10, 6, 8, '2025-08-11 05:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `recipe_id`, `user_id`, `created_at`) VALUES
(6, 3, 7, '2025-08-11 01:35:07'),
(9, 3, 9, '2025-08-11 01:48:34'),
(10, 5, 10, '2025-08-11 01:52:38'),
(11, 4, 10, '2025-08-11 01:52:42'),
(12, 1, 10, '2025-08-11 01:52:47'),
(13, 3, 10, '2025-08-11 01:52:50'),
(14, 6, 12, '2025-08-11 02:04:02'),
(15, 4, 12, '2025-08-11 02:04:12'),
(16, 6, 13, '2025-08-11 02:20:13'),
(17, 1, 13, '2025-08-11 02:20:16'),
(18, 5, 13, '2025-08-11 02:31:36'),
(19, 10, 13, '2025-08-11 02:40:11'),
(20, 3, 6, '2025-08-11 05:35:31'),
(21, 1, 6, '2025-08-11 05:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `steps` text DEFAULT NULL,
  `cuisine` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `recipe_type` varchar(25) NOT NULL,
  `recipe_difficulty` varchar(25) NOT NULL,
  `serves` varchar(2) NOT NULL,
  `Time_to_make` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `title`, `steps`, `cuisine`, `image_url`, `created_at`, `recipe_type`, `recipe_difficulty`, `serves`, `Time_to_make`) VALUES
(1, 7, 'Croc Stew', '1. Catch a Croc\r\n2. Get the meat\r\n3. Make a Broth\r\n4. Fuse\r\n5. Enjoy', 'italian', 'uploads/recipe_6898fc44bc4da4.54944078.png', '2025-08-10 20:08:36', 'lunch', 'easy', '1', '10'),
(3, 8, 'Real Dogs Lemonade', '1. Fuse ingredients\r\n2. Don\'t let greed consume you', 'southern', 'uploads/recipe_689920c6e4b6f2.75216714.png', '2025-08-10 22:44:22', 'breakfast', 'easy', '1', '10'),
(4, 9, 'Redgrass Roasted Bidou', '1. Create\r\n2. Enjoy!', 'vietnamese', 'uploads/recipe_68994be60729f6.21219993.jpg', '2025-08-11 01:48:22', 'dinner', 'easy', '2', '30+'),
(5, 10, 'Coney Stew', '1. Create\r\n2. Enjoy!', 'mediterranean', 'uploads/recipe_68994cdb238927.90644614.jpg', '2025-08-11 01:52:27', 'lunch', 'hard', 'mo', '3+'),
(6, 11, 'Critical Meatballs', '1. Make\r\n2. Enjoy!', 'mexican', 'uploads/recipe_68994e394857a9.03039208.jpg', '2025-08-11 01:58:17', 'dinner', 'easy', '2', '30'),
(7, 12, 'Invincible Noodles', '1. Create\r\n2. Enjoy!', 'african', 'uploads/recipe_68994f8bedd359.83116888.jpg', '2025-08-11 02:03:55', 'dinner', 'moderate', '4', '1+'),
(8, 13, 'Cursed Love Ballad', '1. Make\r\n2. Enjoy!', 'mexican', 'uploads/recipe_689952fb4ed8e6.37621808.jpg', '2025-08-11 02:18:35', 'breakfast', 'moderate', '1', '10'),
(9, 13, 'Honey Cake', '1. Make\r\n2. Enjoy!', 'indian', 'uploads/recipe_6899532ed0eba3.09184478.png', '2025-08-11 02:19:26', 'dessert', 'easy', '1', '10'),
(10, 13, 'Palafin Jet Punch', '1. Mix\r\n2. Enjoy!', 'southern', 'uploads/recipe_68995802e61054.41765022.png', '2025-08-11 02:40:02', 'dessert', 'easy', '1', '10'),
(11, 6, 'Quick Vanilla Cake', '1.Preheat oven to 180°C (350°F).\r\n2.In a bowl, cream together butter and sugar until light and fluffy.\r\n3.Beat in eggs one at a time, then stir in vanilla.\r\n4.In a separate bowl, combine flour, baking powder, and salt.\r\n5.Gradually add dry ingredients to the butter mixture, alternating with milk.\r\n6.Pour batter into a greased 8-inch pan.\r\n7.Bake for 25–30 minutes or until a toothpick comes out clean.\r\n8.Let cool before serving.', 'american', 'uploads/recipe_68998a9553c966.24147373.jpg', '2025-08-11 06:15:49', 'breakfast', 'easy', '4', '30'),
(13, 2, 'Birria Tacos', '1.Boil the dried chiles for 5 minutes, then blend with garlic, onion, tomatoes, oregano, cumin, and some broth until smooth.\r\n\r\n2.Season beef chunks with salt and pepper, then sear in a large pot until browned.\r\n\r\n3.Add chile sauce, cinnamon stick, cloves, bay leaves, and remaining beef broth. Simmer for 2.5–3 hours until beef is tender and shreds easily.\r\n\r\n4.Remove beef from sauce, shred it, and return some to the sauce to keep moist.\r\n\r\n5.Heat a skillet, dip tortillas in the sauce, then fry lightly, adding shredded beef and cheese (if using). Fold in half and crisp on both sides.\r\n\r\n6.Serve hot with chopped onions, cilantro, and a small bowl of consommé for dipping.', 'mexican', 'uploads/recipe_6899aaaa7542c8.92691714.png', '2025-08-11 08:32:42', 'lunch', 'hard', 'mo', '3+'),
(14, 2, 'Jambalaya', '1.Heat olive oil in a large pot over medium heat. Sauté onion, bell pepper, and celery until softened (about 5 minutes).\r\n\r\n2.Add garlic, sausage, and chicken; cook until chicken is no longer pink.\r\n\r\n3.Stir in rice and cook for 1–2 minutes to coat with oil.\r\n\r\n4.Add chicken broth, diced tomatoes, Cajun seasoning, paprika, thyme, cayenne (if using), salt, and pepper.\r\n\r\n5.Bring to a boil, then reduce heat to low, cover, and simmer for 20–25 minutes, stirring occasionally.\r\n\r\n6.In the last 5 minutes, stir in shrimp and cook until pink and cooked through.\r\n\r\n7.Sprinkle with fresh parsley before serving.', 'southern', 'uploads/recipe_6899ac3140aef9.63255037.png', '2025-08-11 08:39:13', 'dinner', 'easy', '4', '30+'),
(15, 2, 'Greek Salad', '1.In a large bowl, combine tomatoes, cucumber, onion, and bell pepper.\r\n\r\n2.Add feta cheese and olives.\r\n\r\n3.In a small bowl, whisk olive oil, vinegar, oregano, salt, and pepper.\r\n\r\n4.Pour dressing over salad, toss gently, and serve immediately.', 'mediterranean', 'uploads/recipe_6899ad572d2ce5.76332764.png', '2025-08-11 08:44:07', 'breakfast', 'easy', '4', '20'),
(16, 2, 'Garlic Butter Shrimp', '1.Melt butter in a skillet over medium heat.\r\n\r\n2.Add garlic and sauté for 30 seconds until fragrant.\r\n\r\n3.Add shrimp, season with salt and pepper, and cook for 2–3 minutes per side until pink and cooked through.\r\n\r\n4.Garnish with parsley and serve immediately.', 'italian', 'uploads/recipe_6899ae25de0c17.34632233.png', '2025-08-11 08:47:33', 'breakfast', 'easy', '1', '10');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `quantity` decimal(5,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_ingredients`
--

INSERT INTO `recipe_ingredients` (`id`, `recipe_id`, `name`, `quantity`, `unit`) VALUES
(1, 10, 'Cherry', 1.00, '1'),
(2, 10, 'Blue Raspberry', 1.00, '1'),
(3, 10, 'Lemonade', 1.00, '1');

-- --------------------------------------------------------

--
-- Table structure for table `test_users`
--

CREATE TABLE `test_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_users`
--

INSERT INTO `test_users` (`id`, `name`, `email`) VALUES
(1, 'Hala', 'hala@example.com'),
(2, 'Hala', 'hala@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `profile_image`, `created_at`, `country`) VALUES
(1, 'Hala', 'hala@example.com', '$2y$10$J.e6J.6.YTce4s8d9DwFyeg6zCL4sRmRuGM/8jZUajGbGeX1c.0I.', 'https://example.com/profile.jpg', '2025-07-27 18:00:04', NULL),
(2, 'Hala Elbayoumi', 'halaelbayoumi@gmail.com', '$2y$10$mTgDEp0uVCEQ3OmyLWcjpeZLKpadS.7JPbL9O9iDth0fn.hxDdY8.', NULL, '2025-08-08 07:02:41', NULL),
(6, 'layan', 'layan@email.com', '$2y$10$UMa2CtUEJINZUgs3VqIOiOD0YllP3j4UptSeQUHRq4TAir1SyjesC', NULL, '2025-08-10 18:26:17', NULL),
(7, 'krock', 'kyle.example@gmail.com', '$2y$10$DzySyss.JXJqmMm2tcmLr.tsKpk4odMGwIokQPK1.XXY3.0Lj8Zf6', NULL, '2025-08-10 19:38:10', NULL),
(8, 'krock2', 'yadayada@gmail.com', '$2y$10$JSuP.1sUv7DGbdUJ6nEpHOkywkTS2OnJUvI80e5lPHcdXF/ofuRYG', NULL, '2025-08-10 22:41:52', NULL),
(9, 'will_metaphor', 'will@gmail.com', '$2y$10$yRWHKCWvaclftZybZ8k2q.6WKwysDIT.crjI9Vlx26m4s6SS5HRQe', NULL, '2025-08-11 01:44:43', NULL),
(10, 'strohl_metaphor', 'strohl@gmail.com', '$2y$10$7npWZPwxf/nIHyLjjMPe8eq3N0Cw3yjnKy/xKPetnyusVgLnpAiwK', NULL, '2025-08-11 01:50:18', NULL),
(11, 'hulkenberg_metaphor', 'hulk@gmail.com', '$2y$10$71KDQn9GhhgupMe.Qgg1N.Rb0HfBvd8gmX1ckO7F5WOmeTvFjWjVW', NULL, '2025-08-11 01:54:57', NULL),
(12, 'heismay_metaphor', 'heismay@gmail.com', '$2y$10$SjFIla/5ezjr8ocYddNZeOgDx7mwAMWb4C1RjPEZ72IiSWEHGw1xq', NULL, '2025-08-11 02:02:37', NULL),
(13, 'junah_metaphor', 'junah@gmail.com', '$2y$10$uVIeUHLXY9P/v5Mav7VG2.xzGm5lt9Im9AiYGN8c6kawVtqnlxnAi', NULL, '2025-08-11 02:16:26', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `follower_id` (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `test_users`
--
ALTER TABLE `test_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `follows`
--
ALTER TABLE `follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `test_users`
--
ALTER TABLE `test_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
