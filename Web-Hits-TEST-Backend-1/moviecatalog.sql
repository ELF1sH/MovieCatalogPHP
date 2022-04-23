-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 10, 2022 at 04:53 PM
-- Server version: 8.0.24
-- PHP Version: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moviecatalog`
--

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE `genre` (
  `id` int NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`id`, `name`) VALUES
(1, 'genre1'),
(2, 'genre2'),
(3, 'genre3'),
(4, 'genre4');

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

DROP TABLE IF EXISTS `movie`;
CREATE TABLE `movie` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `poster` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `year` int NOT NULL,
  `country` varchar(40) NOT NULL,
  `time` int DEFAULT NULL,
  `tagline` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `director` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `budget` int DEFAULT NULL,
  `fees` int DEFAULT NULL,
  `ageLimit` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`id`, `name`, `poster`, `description`, `year`, `country`, `time`, `tagline`, `director`, `budget`, `fees`, `ageLimit`) VALUES
(1, 'name', 'poster', 'desciption', 2010, 'country', 123, 'tagline', 'director', 23425256, 32423, 18),
(2, 'string', NULL, NULL, 0, 'string', 0, 'string', 'string', 0, 0, 0),
(28, 'afsdfasf', NULL, NULL, 324, 'stasdgdasgdaring', 324032, 'sadfdsa', 'stasdgdasgdaring', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `movies-genres`
--

DROP TABLE IF EXISTS `movies-genres`;
CREATE TABLE `movies-genres` (
  `movieId` int NOT NULL,
  `genreId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movies-genres`
--

INSERT INTO `movies-genres` (`movieId`, `genreId`) VALUES
(28, 1),
(28, 2),
(28, 3);

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `userId` int NOT NULL,
  `value` varchar(255) NOT NULL,
  `expiryDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `token`
--

INSERT INTO `token` (`userId`, `value`, `expiryDate`) VALUES
(11, '60f7dbc6d6137f3772a3c52cda8c7000', '2022-03-10 20:23:22'),
(9, '85416028b61266da2f4908a08d53e3d3', '2022-03-10 19:04:13'),
(7, '961e0a3215524b14c3d9be58c99b0f73', '2022-03-10 18:57:33'),
(11, '9c02c9f5c9cbed2fefdcaea030f9f5eb', '2022-03-10 20:23:27'),
(5, 'c59ae755291cfd790da48de94332f121', '2022-03-10 18:55:32'),
(8, 'e06cf424cc4030f5026601820652a04e', '2022-03-10 18:57:46'),
(11, 'e7c408701be75f7ac80977b9b2f1d5cf', '2022-03-10 20:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(30) NOT NULL,
  `birthDate` date DEFAULT NULL,
  `isAdmin` int NOT NULL DEFAULT '0',
  `gender` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `username`, `birthDate`, `isAdmin`, `gender`) VALUES
(5, 'nameeeee', 'string@mail.ru', '785212dac7a1d5e3d8032ff691ec9bcc0abf5a00', 'username', '2022-03-10', 0, 0),
(7, 'nameeeee', 'stringdsf@mail.ru', 'f44136e0a8f667db2b31abcc2813f66db64b7061', 'usernamesdfs', NULL, 0, 0),
(8, 'testputput1', 'testputput1@mail.com', 'f44136e0a8f667db2b31abcc2813f66db64b7061', 'testputput1', '2010-03-12', 0, 0),
(9, 'adminname', 'admin@mail.ru', 'efacc4001e857f7eba4ae781c2932dedf843865e', 'adminusername', NULL, 1, NULL),
(10, 'dsgsagdsg', 'mail@mail.com', '66331baa8e48f220e3c43cfd0d2bf6e07faf5033', 'adminusername1', NULL, 0, NULL),
(11, 'stringsdfdsa', 'stringsdf@mai.com', 'd60d6bab932670572dd0bd9d376d520384092aaf', 'stringsafds', '2022-03-10', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `movies-genres`
--
ALTER TABLE `movies-genres`
  ADD PRIMARY KEY (`movieId`,`genreId`),
  ADD KEY `movieId` (`movieId`),
  ADD KEY `genreId` (`genreId`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`value`),
  ADD KEY `userId` (`userId`),
  ADD KEY `value` (`value`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `movies-genres`
--
ALTER TABLE `movies-genres`
  ADD CONSTRAINT `movies-genres_ibfk_1` FOREIGN KEY (`genreId`) REFERENCES `genre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movies-genres_ibfk_2` FOREIGN KEY (`movieId`) REFERENCES `movie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
