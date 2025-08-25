-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2025 at 01:24 AM
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
-- Database: `autohive`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isadmin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`, `isadmin`) VALUES
(1, 'admin', '123', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'New'),
(2, 'Used'),
(3, 'EV');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ev_stations`
--

CREATE TABLE `ev_stations` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `ports` int(11) DEFAULT 1,
  `fast_charging` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_requests`
--

CREATE TABLE `inspection_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `vehicle` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paperwork_requests`
--

CREATE TABLE `paperwork_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `source` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `user_vehicle_id` int(11) DEFAULT NULL,
  `admin_vehicle_id` int(11) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `buyer_id`, `seller_id`, `user_vehicle_id`, `admin_vehicle_id`, `price`, `status`, `completed_at`, `created_at`) VALUES
(1, 5, 6, 7, NULL, 2999999.99, 'completed', '2025-08-25 04:22:09', '2025-08-24 23:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('buyer','seller','admin') NOT NULL DEFAULT 'buyer',
  `verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `verified`, `created_at`, `phone`, `gender`, `profile_image`) VALUES
(1, 'Faiq Rehman', 'faiq@gmail.com', '202cb962ac59075b964b07152d234b70', 'seller', 1, '2025-07-17 21:28:59', '03483435965', 'male', 'default.png'),
(2, 'hamza', 'hamza@gmail.com', '202cb962ac59075b964b07152d234b70', 'buyer', 1, '2025-07-18 11:59:13', '03483435965', 'male', 'default.png'),
(3, 'Zain', 'zain@gmail.com', '202cb962ac59075b964b07152d234b70', 'buyer', 1, '2025-07-18 12:14:40', '03483435965', 'male', 'default.png'),
(4, 'amber', 'amber@gmail.com', '202cb962ac59075b964b07152d234b70', 'seller', 1, '2025-07-18 13:01:08', '03483435965', 'female', '1755883526_1754083251_deflaut.jpg'),
(5, 'Faiq', 'faiqr5490@gmail.com', '202cb962ac59075b964b07152d234b70', 'buyer', 1, '2025-07-19 00:42:43', '03483435965', 'male', '1754083251_deflaut.jpg'),
(6, 'Hadi', 'faiqrahman00021@gmail.com', '202cb962ac59075b964b07152d234b70', 'seller', 1, '2025-07-19 00:51:59', '03483435965', 'male', '1754091570_default-pfp-aesthetic-of-oreki-jf5c359wiyxt97i9.jpg'),
(7, 'Shahnoor', 'team.autohive@gmail.com', '202cb962ac59075b964b07152d234b70', 'buyer', 1, '2025-08-15 11:39:11', '03483435965', 'male', 'default.jpg'),
(8, 'Syed Hamza', 'syedhamza1310@gmail.com', '202cb962ac59075b964b07152d234b70', 'seller', 1, '2025-08-21 05:05:14', '03462064479', 'male', '1755752731_WhatsApp Image 2025-08-14 at 1.46.54 PM.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `user_vehicles`
--

CREATE TABLE `user_vehicles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `category` enum('new','used','ev') NOT NULL,
  `description` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `model_year` int(11) DEFAULT NULL,
  `mileage` int(11) DEFAULT NULL,
  `fuel_type` varchar(20) DEFAULT NULL,
  `transmission` varchar(20) DEFAULT NULL,
  `condition` varchar(20) DEFAULT NULL,
  `engine_capacity` varchar(20) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `registration_city` varchar(100) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `sell_type` enum('sell','trade') DEFAULT 'sell',
  `sold_at` datetime DEFAULT NULL,
  `status` enum('pending','available','sold') DEFAULT 'available',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` enum('new','used','ev') NOT NULL,
  `description` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 1,
  `status` enum('pending','available','sold') DEFAULT 'available',
  `model_year` int(11) DEFAULT NULL,
  `mileage` int(11) DEFAULT NULL,
  `fuel_type` varchar(20) DEFAULT NULL,
  `transmission` varchar(20) DEFAULT NULL,
  `condition` varchar(20) DEFAULT NULL,
  `engine_capacity` varchar(20) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `registration_city` varchar(100) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `sell_type` enum('sell','trade') DEFAULT 'sell',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `sold_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `name`, `brand`, `price`, `category`, `description`, `images`, `verified`, `status`, `model_year`, `mileage`, `fuel_type`, `transmission`, `condition`, `engine_capacity`, `color`, `registration_city`, `features`, `sell_type`, `created_at`, `updated_at`, `sold_at`) VALUES
(1, 'Toyota Corolla Altis', 'Toyota', 4200000.00, 'new', 'The Toyota Corolla Altis 2024 is a reliable and stylish family sedan, designed to deliver comfort, efficiency, and modern driving convenience. Powered by a robust 1800cc petrol engine with automatic transmission, it ensures a smooth and effortless drive on both city roads and highways. With features like ABS, airbags, alloy wheels, and navigation, it offers advanced safety and technology to enhance your driving experience. Finished in a sleek white exterior, registered in Karachi, and built with Toyota’s trusted durability, the Corolla Altis is an excellent choice for drivers seeking practicality, reliability, and long-term value.', '1756074324_new1.jpg', 1, 'available', 2024, 0, 'Petrol', 'Automatic', 'Excellent', '1800cc', 'grey', 'Karachi', 'ABS, Airbags, Alloy Wheels, Navigation', 'trade', '2025-08-24 22:25:24', '2025-08-24 23:20:03', NULL),
(2, 'Honda Civic RS Turbo', 'Honda', 7200000.00, 'new', 'This vehicle combines comfort and safety with modern driving features. Equipped with a sunroof for an open-air driving experience, cruise control for effortless long journeys, multiple airbags for maximum safety, and a rear camera for easy parking and reversing, it ensures both luxury and convenience on every trip.', '1756074675_new2.jpg', 1, 'available', 2024, 0, 'Petrol', 'Automatic', 'Excellent', '1500cc', 'White', 'Lahore', 'Sunroof, Cruise Control, Airbags, Rear Camera', 'sell', '2025-08-24 22:31:15', '2025-08-24 22:31:15', NULL),
(3, 'Suzuki Alto VXL', 'Suzuki', 2500000.00, 'new', 'The Suzuki Alto VXL 2023 is a compact and fuel-efficient hatchback, perfect for city driving and daily commutes. Powered by a 660cc petrol engine with automatic transmission, it offers smooth handling and impressive economy. With just 5,000 km driven, this silver-colored Alto remains in good condition and registered in Islamabad. Its practical features include power windows, air conditioning, and a multimedia system, making it a reliable and convenient choice for budget-conscious drivers seeking comfort and ease in urban environments.', '1756074843_new3.jpg', 1, 'available', 2023, 5000, 'Petrol', 'Automatic', 'Excellent', '660', 'White', 'Islamabad', 'Power Windows, AC, Multimedia', 'trade', '2025-08-24 22:34:03', '2025-08-24 23:20:21', NULL),
(4, 'Hyundai Tucson GLS', 'Hyundai', 8500000.00, 'new', 'The Hyundai Tucson GLS 2024 is a premium SUV that offers a perfect balance of style, comfort, and performance. Equipped with a powerful 2000cc petrol engine and automatic transmission, it ensures a smooth and responsive drive. With zero mileage and an excellent condition rating, this brand-new grey Tucson is registered in Karachi. Advanced features such as LED headlights for clear visibility, a smart key system for convenience, and lane assist technology for enhanced safety make it an ideal choice for families and professionals who value reliability, innovation, and a commanding road presence.', '1756075275_new4.png', 1, 'available', 2024, 0, 'Petrol', 'Automatic', 'Excellent', '2000cc', 'grey', 'Karachi', 'LED Lights, Smart Key, Lane Assist', 'sell', '2025-08-24 22:36:08', '2025-08-24 22:41:15', NULL),
(5, 'Kia Sportage AWD', 'Kia', 8700000.00, 'new', 'The Kia Sportage AWD 2024 is a stylish and versatile SUV, built to deliver comfort, safety, and performance in every drive. Powered by a 2000cc petrol engine with automatic transmission, it offers smooth handling and reliable efficiency. With zero mileage and excellent condition, this brand-new red Sportage is registered in Lahore. Premium features such as luxurious leather seats, multiple airbags for enhanced safety, and a sunroof for an open-air driving experience make it an attractive option for families and individuals who want both practicality and sophistication in their vehicle.', '1756075521_new5.jpeg', 1, 'available', 2024, 0, 'Petrol', 'Automatic', 'Excellent', '2000cc', 'White', 'Lahore', 'Leather Seats, Airbags, Sunroof', 'trade', '2025-08-24 22:45:21', '2025-08-24 23:20:37', NULL),
(6, 'MG HS Exclusive', 'MG', 9300000.00, 'new', 'The MG HS Exclusive 2024 is a premium crossover SUV that blends advanced technology with modern comfort. Powered by a 1500cc petrol engine and automatic transmission, it delivers smooth performance and efficiency. With zero mileage and excellent condition, this brand-new blue HS is registered in Karachi. Standout features include a 360-degree camera for complete driving awareness, adaptive cruise control for stress-free long journeys, and heated seats for enhanced comfort, making it a sophisticated and smart choice for modern drivers', '1756075680_new6.png', 1, 'available', 2024, 0, 'Petrol', 'Manual', 'Excellent', '1500cc', 'red', 'Karachi', '360 Camera, Adaptive Cruise, Heated Seats', 'sell', '2025-08-24 22:48:00', '2025-08-24 22:48:54', NULL),
(7, 'Audi A6 Sedan', 'Audi', 18000000.00, 'new', 'The Audi A6 Sedan 2024 is a luxury executive car that combines elegance, performance, and cutting-edge technology. Featuring a refined 2000cc petrol engine paired with an automatic transmission, it ensures a smooth and powerful driving experience. With zero mileage and in excellent condition, this brand-new black A6 is registered in Islamabad. Premium features such as Audi’s Virtual Cockpit digital display, a high-quality Bose audio system, and advanced parking sensors provide both convenience and sophistication, making it a top choice for professionals and enthusiasts who value style and innovation.', '1756076237_new7.jpg', 1, 'available', 2024, 0, 'Petrol', 'Automatic', 'Excellent', '2000cc', 'grey', 'Islamabad', 'Virtual Cockpit, Bose Audio, Parking Sensors', 'trade', '2025-08-24 22:57:17', '2025-08-24 23:21:23', NULL),
(8, 'BMW X5 M Sport', 'BMW', 35000000.00, 'new', 'The BMW X5 M Sport 2024 is a premium luxury SUV designed for performance and sophistication. Equipped with a powerful 3000cc petrol engine and automatic transmission, it offers dynamic handling with precision and comfort. This brand-new white X5, registered in Karachi, stands in excellent condition with zero mileage. Key highlights include a panoramic roof for an open-air experience, Sports Mode for enhanced driving dynamics, and wireless charging for modern convenience. The X5 M Sport delivers a perfect blend of power, elegance, and cutting-edge technology for discerning drivers.', '1756076466_new8.jpg', 1, 'available', 2024, 0, 'Petrol', 'Automatic', 'Excellent', '3000cc', 'black', 'Karachi', 'Panoramic Roof, Sports Mode, Wireless Charging', 'sell', '2025-08-24 23:01:06', '2025-08-24 23:01:06', NULL),
(9, 'Mercedes-Benz C200 AMG', 'Mercedes-Benz', 25000000.00, 'new', 'The Mercedes-Benz C200 AMG 2024 is a luxury sedan that blends sporty elegance with modern innovation. Powered by a refined 1500cc petrol engine and automatic transmission, it ensures smooth performance and efficiency. This brand-new silver C200 AMG, registered in Lahore, is in excellent condition with zero mileage. Premium features include ambient lighting for a luxurious cabin feel, a high-quality premium sound system, and advanced assistive drive technology for added safety and convenience. The C200 AMG delivers a perfect mix of style, comfort, and driving excellence.', '1756076587_new9.jpg', 1, 'available', 2024, 0, 'Petrol', 'Manual', 'Excellent', '1500cc', 'grey', 'Lahore', 'Ambient Lighting, Premium Sound, Assistive Drive', 'trade', '2025-08-24 23:03:07', '2025-08-24 23:21:06', NULL),
(10, 'Ford Ranger Raptor', 'Ford', 15000000.00, 'new', 'The Ford Ranger Raptor 2024 is a rugged pickup built for power and adventure. Equipped with a 2000cc diesel engine and automatic transmission, it delivers high performance on and off the road. This brand-new orange Raptor, registered in Lahore, is in excellent condition with zero mileage. Featuring a 4x4 drivetrain, multiple off-road modes, and heavy-duty suspension, it ensures unmatched durability and control in tough terrains. A perfect choice for those seeking strength, reliability, and off-road dominance.', '1756076934_new10.jpg', 1, 'available', 2024, 0, 'Diesal', 'Automatic', 'Excellent', '2000cc', 'black', 'Lahore', '4x4, Offroad Modes, Heavy Duty Suspension', 'sell', '2025-08-24 23:08:54', '2025-08-24 23:08:54', NULL),
(11, 'Jeep Wrangler Rubicon', 'Jeep', 17000000.00, 'new', 'The Jeep Wrangler Rubicon 2024 is an iconic off-road SUV designed for adventure seekers. Powered by a robust 3600cc petrol engine with a manual transmission, it delivers raw strength and complete driving control. This brand-new green Rubicon, registered in Islamabad, comes in excellent condition with zero mileage. Equipped with a removable roof, 4x4 lock system, and hill assist, it is engineered to handle the toughest terrains while offering an open-air driving experience. A perfect companion for thrill, freedom, and rugged exploration.', '1756077082_new11.jpg', 1, 'available', 2024, 0, 'Petrol', 'Manual', 'Excellent', '3600cc', 'grey', 'Islamabad', 'Removable Roof, 4x4 Lock, Hill Assist', 'trade', '2025-08-24 23:11:22', '2025-08-24 23:20:54', NULL),
(12, 'Porsche Cayenne Turbo', 'Porsche', 50000000.00, 'new', 'The Porsche Cayenne Turbo 2024 is a luxury performance SUV that combines power, elegance, and cutting-edge technology. Equipped with a commanding 4000cc petrol engine and automatic transmission, it delivers thrilling acceleration and dynamic handling. This brand-new black Cayenne Turbo, registered in Karachi, is in excellent condition with zero mileage. Highlight features include the Sports Chrono package for enhanced driving dynamics, premium leather interior for unmatched comfort, and adaptive suspension for superior ride quality. A masterpiece of luxury and performance, tailored for those who demand the best.', '1756077203_new12.jpg', 1, 'available', 2024, 0, 'Petrol', 'Manual', 'Excellent', '4000cc', 'grey', 'Islamabad', 'Sports Chrono, Premium Leather, Adaptive Suspension', 'sell', '2025-08-24 23:13:23', '2025-08-24 23:13:23', NULL),
(13, 'Toyota Premio', 'Toyota', 3200000.00, 'used', 'The Toyota Premio 2016 is a stylish and reliable sedan, known for its comfort and practicality. Powered by a 1500cc petrol engine with automatic transmission, it ensures smooth and efficient performance. This used white Premio, registered in Karachi, is in good condition with 95,000 km mileage. Key features include ABS, dual airbags, alloy wheels, and a multimedia system, providing both safety and convenience. A dependable family car that blends elegance with everyday usability.', '1756077365_used1.jpg', 1, 'available', 2016, 95000, 'Petrol', 'Automatic', 'Good', '1500cc', 'red', 'Karachi', 'ABS, Airbags, Alloy Wheels, Multimedia', 'trade', '2025-08-24 23:16:05', '2025-08-24 23:20:46', NULL),
(14, 'Honda Vezel Hybrid', 'Honda', 5200000.00, 'used', 'The Honda Vezel Hybrid 2018 is a smart crossover SUV, offering a perfect mix of style, comfort, and fuel efficiency. Powered by a 1500cc hybrid engine with automatic transmission, it delivers smooth performance and economical driving. This used silver Vezel, registered in Lahore, is in good condition with 65,000 km mileage. Equipped with cruise control, a sunroof, and a reverse camera, it ensures convenience, safety, and driving pleasure. An ideal choice for those seeking a modern and eco-friendly SUV.', '1756077579_used2.jpg', 1, 'available', 2018, 65000, 'Hybrid', 'Automatic', 'Good', '1500cc', 'red', 'Lahore', 'Cruise Control, Sunroof, Reverse Camera', 'sell', '2025-08-24 23:19:39', '2025-08-24 23:19:39', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ev_stations`
--
ALTER TABLE `ev_stations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inspection_requests`
--
ALTER TABLE `inspection_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paperwork_requests`
--
ALTER TABLE `paperwork_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ev_stations`
--
ALTER TABLE `ev_stations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_requests`
--
ALTER TABLE `inspection_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paperwork_requests`
--
ALTER TABLE `paperwork_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
