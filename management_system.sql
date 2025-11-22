-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-11-22 16:03:37
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `management_system`
--

-- --------------------------------------------------------

--
-- 資料表結構 `package`
--

CREATE TABLE `package` (
  `package_id` int(255) NOT NULL,
  `room` int(255) NOT NULL,
  `student_name` varchar(30) NOT NULL,
  `student_id` int(11) NOT NULL,
  `arrive_time` datetime NOT NULL,
  `receive_time` datetime DEFAULT NULL,
  `state` varchar(10) NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `public`
--

CREATE TABLE `public` (
  `public_id` int(255) NOT NULL,
  `public_name` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL DEFAULT 'Y',
  `borrow_time` datetime DEFAULT NULL,
  `max_use_time` time NOT NULL,
  `expected_return_time` datetime DEFAULT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `borrower_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--
 
CREATE TABLE `users` (
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `role` text CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`username`, `password`, `name`, `role`) VALUES
('root', 'password', '管理員', 'admin'),
('student', 'pw4', '學生', 'resident');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`package_id`);

--
-- 資料表索引 `public`
--
ALTER TABLE `public`
  ADD PRIMARY KEY (`public_id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `package`
--
ALTER TABLE `package`
  MODIFY `package_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
 