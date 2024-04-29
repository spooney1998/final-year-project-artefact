-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 31, 2024 at 08:10 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `library_books_review`
--

DROP TABLE IF EXISTS `library_books_review`;
CREATE TABLE IF NOT EXISTS `library_books_review` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `userID` varchar(10) NOT NULL,
  `bookID` varchar(10) NOT NULL,
  `rating` int NOT NULL,
  `reviews` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `date` varchar(25) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `library_books_review`
--

INSERT INTO `library_books_review` (`sn`, `userID`, `bookID`, `rating`, `reviews`, `date`) VALUES
(1, '4467842920', 'no43265784', 4, 'This is nice book', '22-5-2024, 10:42 AM');

-- --------------------------------------------------------

--
-- Table structure for table `library_book_category`
--

DROP TABLE IF EXISTS `library_book_category`;
CREATE TABLE IF NOT EXISTS `library_book_category` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `categoryID` varchar(15) NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `categoryName` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '''Fiction'',''Non-Fiction'',''Reference'',''Periodicals'',''Children''''s Literature'',''Special Collections'',''Educational Materials'',''Language Learning'',''Media'',''Genre Fiction''',
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `library_book_category`
--

INSERT INTO `library_book_category` (`sn`, `categoryID`, `type`, `categoryName`) VALUES
(1, '8acf3debe7', 'Novels', 'Fiction'),
(2, '8acf4028e7', 'Short Stories', 'Fiction'),
(3, '8acf41c3e7', 'Poetry', 'Fiction'),
(4, '8acf4304e7', 'Drama', 'Fiction'),
(5, '8acf4441e7', 'Graphic Novels', 'Fiction'),
(6, '8acf4561e7', 'Biography/Autobiography', 'Non-Fiction'),
(7, '8acf4684e7', 'Memoirs', 'Non-Fiction'),
(8, '8acf47a8e7', 'History', 'Non-Fiction'),
(9, '8acf48e1e7', 'Travel', 'Non-Fiction'),
(10, '8acf4a04e7', 'Science', 'Non-Fiction'),
(11, '8acf4b5de7', 'Technology', 'Non-Fiction'),
(12, '8acf4ca0e7', 'Self-Help', 'Non-Fiction'),
(13, '8acf4dbee7', 'Philosophy', 'Non-Fiction'),
(14, '8acf4ef5e7', 'Religion/Spirituality', 'Non-Fiction'),
(15, '8acf500ee7', 'Social Sciences', 'Non-Fiction'),
(16, '8acf5140e7', 'Political Science', 'Non-Fiction'),
(17, '8acf5278e7', 'Arts and Music', 'Non-Fiction'),
(18, '8acf53bae7', 'Health and Fitness', 'Non-Fiction'),
(19, '8acf54f4e7', 'Cooking/Food', 'Non-Fiction'),
(20, '8acf5634e7', 'Gardening', 'Non-Fiction'),
(21, '8acf5763e7', 'Encyclopedias', 'Reference'),
(22, '8acf5893e7', 'Dictionaries', 'Reference'),
(23, '8acf59d0e7', 'Thesauruses', 'Reference'),
(24, '8acf5b1ce7', 'Almanacs', 'Reference'),
(25, '8acf5c63e7', 'Atlases', 'Reference'),
(26, '8acf5db1e7', 'Directories', 'Reference'),
(27, '8acf5ebce7', 'Handbooks', 'Reference'),
(28, '8acf5fd5e7', 'Manuals', 'Reference'),
(29, '8acf60fee7', 'Magazines', 'Periodicals'),
(30, '8acf622de7', 'Journals', 'Periodicals'),
(31, '8acf6358e7', 'Newspapers', 'Periodicals'),
(32, '8acf6472e7', 'Picture Books', 'Children\'s Literature'),
(33, '8acf6609e7', 'Early Readers', 'Children\'s Literature'),
(34, '8acf6745e7', 'Chapter Books', 'Children\'s Literature'),
(35, '8acf688de7', 'Young Adult (YA) Fiction', 'Children\'s Literature'),
(36, '8acf69d9e7', 'Teen Non-Fiction', 'Children\'s Literature'),
(37, '8acf6b4fe7', 'Rare Books', 'Special Collections'),
(38, '8acf6c99e7', 'Local History', 'Special Collections'),
(39, '8acf6e29e7', 'Genealogy', 'Special Collections'),
(40, '8acf6ff4e7', 'Rare Manuscripts', 'Special Collections'),
(41, '8acf7144e7', 'Archives', 'Special Collections'),
(42, '8acf7288e7', 'Thesis and Dissertations', 'Special Collections'),
(43, '8acf73f7e7', 'Textbooks', 'Educational Materials'),
(44, '8acf7505e7', 'Workbooks', 'Educational Materials'),
(45, '8acf7625e7', 'Study Guides', 'Educational Materials'),
(46, '8acf775be7', 'Educational Kits', 'Educational Materials'),
(47, '8acf7889e7', 'Curriculum Materials', 'Educational Materials'),
(48, '8acf79b6e7', 'Language Instruction Books', 'Language Learning'),
(49, '8acf7adce7', 'Language Learning Kits', 'Language Learning'),
(50, '8acf7c18e7', 'Bilingual Books', 'Language Learning'),
(51, '8acf7d38e7', 'DVDs/Blu-rays', 'Media'),
(52, '8acf7e64e7', 'CDs/Audio Books', 'Media'),
(53, '8acf7f85e7', 'Digital Resources', 'Media'),
(54, '8acf80bbe7', 'Mystery', 'Genre Fiction'),
(55, '8acf81c6e7', 'Thriller/Suspense', 'Genre Fiction'),
(56, '8acf82e5e7', 'Romance', 'Genre Fiction'),
(57, '8acf83f0e7', 'Science Fiction', 'Genre Fiction'),
(58, '8acf84fae7', 'Fantasy', 'Genre Fiction'),
(59, '8acf8604e7', 'Horror', 'Genre Fiction');

-- --------------------------------------------------------

--
-- Table structure for table `library_borrowings`
--

DROP TABLE IF EXISTS `library_borrowings`;
CREATE TABLE IF NOT EXISTS `library_borrowings` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `issueID` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userID` varchar(10) NOT NULL COMMENT 'Who borrowed the book',
  `bookID` varchar(10) NOT NULL,
  `borrowedDate` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'When the book was borrowed ',
  `dueDate` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'When the book will be due for return',
  `returnDate` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'when the book is returned',
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `library_borrowings`
--

INSERT INTO `library_borrowings` (`sn`, `issueID`, `userID`, `bookID`, `borrowedDate`, `dueDate`, `returnDate`) VALUES
(1, '3432453423', '1501329290', 'no43265784', '3-5-2023, 7:13 pm', '29-6-2023 9:25 am', '4-7-2023 9:25 am'),
(2, '2342342342', '1501329290', 'th38298374', '9-5-2023, 10:18 pm', '29-6-2024 9:25 am', '');

-- --------------------------------------------------------

--
-- Table structure for table `library_favorite_books`
--

DROP TABLE IF EXISTS `library_favorite_books`;
CREATE TABLE IF NOT EXISTS `library_favorite_books` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `userID` varchar(10) NOT NULL,
  `bookID` varchar(10) NOT NULL,
  `date` varchar(25) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `library_favorite_books`
--

INSERT INTO `library_favorite_books` (`sn`, `userID`, `bookID`, `date`) VALUES
(3, '1501329290', 'no43265784', '22-5-2024, 10:42 AM');

-- --------------------------------------------------------

--
-- Table structure for table `library_reservations`
--

DROP TABLE IF EXISTS `library_reservations`;
CREATE TABLE IF NOT EXISTS `library_reservations` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `reservationID` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bookID` varchar(10) NOT NULL,
  `userID` varchar(10) NOT NULL,
  `reservedDate` varchar(25) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `library_reservations`
--

INSERT INTO `library_reservations` (`sn`, `reservationID`, `bookID`, `userID`, `reservedDate`) VALUES
(18, '', 'po38748374', '1501329290', '2024-03-26 04:55:15'),
(24, '', 'ya39847289', '1501329290', '2024-03-26 05:33:17'),
(31, '66991629', 'no43265784', '1501329290', '2024-03-27 03:31:14'),
(27, '', 'th38298374', '4538388090', '2024-03-26 06:11:25'),
(28, '', 'hf39283765', '4538388090', '2024-03-26 06:11:38'),
(29, '', 'hf39283765', '1501329290', '2024-03-26 06:14:23'),
(33, '32442898', 'ya39847289', '5843321294', '2024-03-29 21:47:24'),
(34, '57869608', 'hf39283765', '5843321294', '2024-03-29 21:47:32');

-- --------------------------------------------------------

--
-- Table structure for table `library_users`
--

DROP TABLE IF EXISTS `library_users`;
CREATE TABLE IF NOT EXISTS `library_users` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `userID` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(65) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `address` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(25) NOT NULL COMMENT 'user, librarian, admin',
  `passport` text NOT NULL,
  `fine` int NOT NULL COMMENT 'Fine to be paid incase a user is having a fine',
  `regDate` varchar(25) NOT NULL,
  `lastAccess` varchar(25) NOT NULL,
  `onlineStatus` int NOT NULL,
  `lastDeviceIP` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `library_users`
--

INSERT INTO `library_users` (`sn`, `userID`, `name`, `email`, `password`, `phone`, `address`, `username`, `role`, `passport`, `fine`, `regDate`, `lastAccess`, `onlineStatus`, `lastDeviceIP`) VALUES
(9, '4467842920', 'Daniel Pius', 'homdroidtech@gmail.com', 'e3afed0047b08059d0fada10f400c1e5', '090129102901', '1, denagan close ajara lopo', 'homdroid', 'user', 'passport/544558istockphoto-641398026-612x612.jpg', 0, '19-03-2024, 6:30 pm', '27-03-2024, 3:03 am', 0, '::1'),
(10, '1501329290', 'Joe Biden', 'joe@gmail.com', 'e3afed0047b08059d0fada10f400c1e5', '092039203902', 'United State', 'Joe', 'user', 'passport/527493student2.jpg', 0, '22-03-2024, 1:22 pm', '27-03-2024, 1:06 am', 0, '::1'),
(11, '9944113341', 'David Brain', 'dbrain@gmail.com', 'e3afed0047b08059d0fada10f400c1e5', '90909304345', 'Nigeria.', 'dbrain', 'librarian', 'null', 0, '22-03-2024, 1:24 pm', '22-03-2024, 9:15 pm', 0, '::1'),
(12, '5208618633', 'Jame Barnabas', 'paul@gmail.com', 'e3afed0047b08059d0fada10f400c1e5', '089898989898', 'United Kingdom', 'Paul', 'librarian', 'null', 0, '22-03-2024, 1:32 pm', '22-03-2024, 9:16 pm', 0, '::1'),
(13, '4538388090', 'Monday June', 'juneboy@gmail.com', 'e3afed0047b08059d0fada10f400c1e5', '09809809809', 'Londan, Uk', 'juneboy', 'user', 'passport/472698student3.jpg', 0, '22-03-2024, 1:34 pm', '26-03-2024, 6:11 am', 0, '::1'),
(14, '5843321294', 'Kennedy Gibson ', 'kennedy@gmail.com', 'e3afed0047b08059d0fada10f400c1e5', '099809809809', 'Accra Ghana', 'kennedy', 'user', 'passport/614044student3.jpg', 0, '29-03-2024, 9:29 pm', '29-03-2024, 9:53 pm', 0, '::1');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
