-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 100.101.113.86:3307
-- Generation Time: Sep 13, 2026 at 04:50 AM
-- Server version: 8.0.46
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coaching_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `student_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('present','absent') COLLATE utf8mb4_general_ci DEFAULT 'present',
  `latitude` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `longitude` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `face_scan_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `device_info` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `signature_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo_path` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `student_id`, `student_name`, `date`, `status`, `latitude`, `longitude`, `is_verified`, `face_scan_path`, `in_time`, `out_time`, `ip_address`, `device_info`, `signature_path`, `photo_path`) VALUES
(29, 60, 60, 'PARV JAIN', '2026-08-11', 'present', '23.3919106', '79.5363786', 0, 'uploads/attendance/60_photo_1786431065.png', '12:21:05', NULL, '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1786431065.png', NULL),
(31, 60, 60, 'PARV JAIN', '2026-08-12', 'present', '23.3919089', '79.5363766', 0, 'uploads/attendance/60_photo_1786510656.png', '10:27:36', '12:11:27', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1786510656.png', NULL),
(32, 52, 52, 'JYOTI LODHI', '2026-08-12', 'present', '23.3919108', '79.5363794', 0, 'uploads/attendance/52_photo_1786512695.png', '11:01:35', '12:10:44', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Mobile Safari/537.36 TC_MOBILE_APP', 'uploads/signatures/52_sig_1786512695.png', NULL),
(36, 60, 60, 'PARV JAIN', '2026-08-13', 'present', '23.3919298', '79.5364067', 0, 'uploads/attendance/60_photo_1786599333.png', '11:05:33', '11:55:14', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1786599333.png', NULL),
(37, 52, 52, 'JYOTI LODHI', '2026-08-13', 'present', '23.3919291', '79.5364057', 0, 'uploads/attendance/52_photo_1786601533.png', '11:42:13', '12:01:52', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.183 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1786601533.png', NULL),
(38, 52, 52, 'JYOTI LODHI', '2026-08-14', 'present', '23.3919289', '79.5364056', 0, 'uploads/attendance/52_photo_1786680711.png', '09:41:51', '11:56:50', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.183 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1786680711.png', NULL),
(39, 60, 60, 'PARV JAIN', '2026-08-14', 'present', '23.3919291', '79.5364063', 0, 'uploads/attendance/60_photo_1786683919.png', '10:35:19', '12:02:00', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1786683919.png', NULL),
(40, 64, 64, 'SHRIKANT SAHU', '2026-08-14', 'present', '23.39191', '79.5363782', 0, 'uploads/attendance/64_photo_1786683926.png', '10:35:26', '12:00:51', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1786683926.png', NULL),
(41, 61, 61, 'PEEYUSH MEHRA', '2026-08-14', 'present', '23.3919284', '79.5364053', 0, 'uploads/attendance/61_photo_1786684069.png', '10:37:49', '12:01:50', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1786684069.png', NULL),
(42, 61, 61, 'PEEYUSH MEHRA', '2026-08-17', 'present', '23.3919361', '79.5364053', 0, 'uploads/attendance/61_photo_1786942057.png', '10:17:37', '11:34:16', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1786942057.png', NULL),
(43, 64, 64, 'SHRIKANT SAHU', '2026-08-17', 'present', '23.3921437', '79.5364191', 0, 'uploads/attendance/64_photo_1786946567.png', '11:32:47', NULL, '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1786946567.png', NULL),
(44, 52, 52, 'JYOTI LODHI', '2026-08-18', 'present', '23.3919307', '79.5364063', 0, 'uploads/attendance/52_photo_1787025668.png', '09:31:08', '12:04:27', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.183 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1787025669.png', NULL),
(45, 61, 61, 'PEEYUSH MEHRA', '2026-08-18', 'present', '23.3919433', '79.53655', 0, 'uploads/attendance/61_photo_1787027485.png', '10:01:25', '12:04:24', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1787027485.png', NULL),
(46, 64, 64, 'SHRIKANT SAHU', '2026-08-18', 'present', '23.3919292', '79.5364059', 0, 'uploads/attendance/64_photo_1787028475.png', '10:17:55', '12:05:52', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1787028475.png', NULL),
(47, 60, 60, 'PARV JAIN', '2026-08-18', 'present', '23.3919287', '79.5364059', 0, 'uploads/attendance/60_photo_1787028476.png', '10:17:56', '12:06:01', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1787028476.png', NULL),
(48, 52, 52, 'JYOTI LODHI', '2026-08-19', 'present', '23.3927164', '79.5359312', 0, 'uploads/attendance/52_photo_1787112581.png', '09:39:41', '11:55:12', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.183 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1787112581.png', NULL),
(49, 60, 60, 'PARV JAIN', '2026-08-19', 'present', '23.3919289', '79.5364055', 0, 'uploads/attendance/60_photo_1787114238.png', '10:07:18', '12:00:36', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1787114238.png', NULL),
(50, 61, 61, 'PEEYUSH MEHRA', '2026-08-19', 'present', '23.3919304', '79.5364062', 0, 'uploads/attendance/61_photo_1787116112.png', '10:38:32', '12:03:38', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1787116112.png', NULL),
(51, 64, 64, 'SHRIKANT SAHU', '2026-08-19', 'present', '23.3919307', '79.5364063', 0, 'uploads/attendance/64_photo_1787118792.png', '11:23:12', '11:55:59', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1787118792.png', NULL),
(52, 52, 52, 'JYOTI LODHI', '2026-08-20', 'present', '23.3919289', '79.5364055', 0, 'uploads/attendance/52_photo_1787199065.png', '09:41:05', '12:04:37', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.183 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1787199065.png', NULL),
(53, 64, 64, 'SHRIKANT SAHU', '2026-08-20', 'present', '23.391929', '79.536406', 0, 'uploads/attendance/64_photo_1787203101.png', '10:48:21', '12:04:33', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1787203101.png', NULL),
(54, 60, 60, 'PARV JAIN', '2026-08-20', 'present', '23.3919307', '79.5364063', 0, 'uploads/attendance/60_photo_1787203181.png', '10:49:41', '12:05:12', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1787203181.png', NULL),
(55, 61, 61, 'PEEYUSH MEHRA', '2026-08-20', 'present', '23.3919302', '79.5364067', 0, 'uploads/attendance/61_photo_1787203389.png', '10:53:09', '12:05:23', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1787203389.png', NULL),
(56, 60, 60, 'PARV JAIN', '2026-08-21', 'present', '23.3919165', '79.5363892', 0, 'uploads/attendance/60_photo_1787289715.png', '10:51:55', '12:31:33', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1787289715.png', NULL),
(57, 64, 64, 'SHRIKANT SAHU', '2026-08-21', 'present', '23.3919292', '79.5364062', 0, 'uploads/attendance/64_photo_1787289751.png', '10:52:31', '11:47:33', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1787289751.png', NULL),
(58, 61, 61, 'PEEYUSH MEHRA', '2026-08-21', 'present', '23.3919168', '79.5363902', 0, 'uploads/attendance/61_photo_1787290781.png', '11:09:41', '12:35:44', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1787290781.png', NULL),
(59, 64, 64, 'SHRIKANT SAHU', '2026-08-22', 'present', '23.3919304', '79.5364062', 0, 'uploads/attendance/64_photo_1787375910.png', '10:48:30', '11:36:13', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.85 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1787375910.png', NULL),
(60, 60, 60, 'PARV JAIN', '2026-08-22', 'present', '23.391918', '79.5363904', 0, 'uploads/attendance/60_photo_1787375921.png', '10:48:41', '11:35:37', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.181 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1787375921.png', NULL),
(61, 52, 52, 'JYOTI LODHI', '2026-08-22', 'present', '23.391916', '79.5363886', 0, 'uploads/attendance/52_photo_1787378791.png', '11:36:31', NULL, '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.183 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1787378791.png', NULL),
(62, 60, 60, 'PARV JAIN', '2026-08-25', 'present', '23.3919161', '79.5363893', 0, 'uploads/attendance/60_photo_1787639378.png', '11:59:38', '12:04:08', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.169 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1787639378.png', NULL),
(63, 64, 64, 'SHRIKANT SAHU', '2026-08-25', 'present', '23.3919305', '79.5364066', 0, 'uploads/attendance/64_photo_1787639422.png', '12:00:22', '12:04:31', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.85 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1787639422.png', NULL),
(64, 52, 52, 'JYOTI LODHI', '2026-08-25', 'present', '23.3919171', '79.5363896', 0, 'uploads/attendance/52_photo_1787639471.png', '12:01:11', '12:06:24', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/150.0.7871.183 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1787639471.png', NULL),
(65, 64, 64, 'SHRIKANT SAHU', '2026-09-02', 'present', '23.3916869', '79.5371047', 0, 'uploads/attendance/64_photo_1788331058.png', '12:07:38', '12:08:13', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.200 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1788331058.png', NULL),
(66, 60, 60, 'PARV JAIN', '2026-09-02', 'present', '23.3921135', '79.5365562', 0, 'uploads/attendance/60_photo_1788331135.png', '12:08:55', '12:09:52', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1788331135.png', NULL),
(67, 61, 61, 'PEEYUSH MEHRA', '2026-09-02', 'present', '23.3921367', '79.5365117', 0, 'uploads/attendance/61_photo_1788331150.png', '12:09:10', '12:12:17', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1788331150.png', NULL),
(68, 64, 64, 'SHRIKANT SAHU', '2026-09-03', 'present', '23.3918379', '79.5366297', 0, 'uploads/attendance/64_photo_1788411676.png', '10:31:16', NULL, '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.200 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1788411676.png', NULL),
(69, 60, 60, 'PARV JAIN', '2026-09-03', 'present', '23.3920855', '79.5362191', 0, 'uploads/attendance/60_photo_1788411939.png', '10:35:39', NULL, '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1788411939.png', NULL),
(70, 61, 61, 'PEEYUSH MEHRA', '2026-09-03', 'present', '23.391935', '79.5363767', 0, 'uploads/attendance/61_photo_1788412556.png', '10:45:56', NULL, '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1788412556.png', NULL),
(71, 52, 52, 'JYOTI LODHI', '2026-09-05', 'present', '23.3928346', '79.5376877', 0, 'uploads/attendance/52_photo_1788581707.png', '09:45:07', '12:01:20', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/152.0.7977.66 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1788581707.png', NULL),
(72, 60, 60, 'PARV JAIN', '2026-09-07', 'present', '23.3913166', '79.5369851', 0, 'uploads/attendance/60_photo_1788759176.png', '11:02:56', '11:05:00', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1788759176.png', NULL),
(73, 52, 52, 'JYOTI LODHI', '2026-09-07', 'present', '23.392396', '79.5366338', 0, 'uploads/attendance/52_photo_1788759205.png', '11:03:25', NULL, '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/152.0.7977.66 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1788759205.png', NULL),
(74, 61, 61, 'PEEYUSH MEHRA', '2026-09-07', 'present', '23.3920667', '79.536875', 0, 'uploads/attendance/61_photo_1788759342.png', '11:05:42', '13:13:04', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1788759342.png', NULL),
(75, 64, 64, 'SHRIKANT SAHU', '2026-09-07', 'present', '23.3920956', '79.5358615', 0, 'uploads/attendance/64_photo_1788759479.png', '11:07:59', '11:12:48', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.200 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1788759479.png', NULL),
(76, 52, 52, 'JYOTI LODHI', '2026-09-08', 'present', '23.392396', '79.5366338', 0, 'uploads/attendance/52_photo_1788841249.png', '09:50:49', '12:09:39', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/152.0.7977.66 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1788841249.png', NULL),
(77, 61, 61, 'PEEYUSH MEHRA', '2026-09-08', 'present', '23.3928917', '79.5370638', 0, 'uploads/attendance/61_photo_1788844513.png', '10:45:13', '12:14:46', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1788844513.png', NULL),
(78, 64, 64, 'SHRIKANT SAHU', '2026-09-08', 'present', '23.391958', '79.5366541', 0, 'uploads/attendance/64_photo_1788844553.png', '10:45:53', '12:17:56', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.200 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1788844553.png', NULL),
(79, 60, 60, 'PARV JAIN', '2026-09-08', 'present', '23.3920633', '79.5365685', 0, 'uploads/attendance/60_photo_1788844611.png', '10:46:51', '12:12:25', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1788844611.png', NULL),
(80, 66, 66, 'PRIYANKA GOUND', '2026-09-09', 'present', '23.3920047', '79.5362825', 0, 'uploads/attendance/66_photo_1788935683.png', '12:04:43', '12:10:14', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Mobile Safari/537.36 TC_MOBILE_APP', 'uploads/signatures/66_sig_1788935683.png', NULL),
(81, 52, 52, 'JYOTI LODHI', '2026-09-10', 'present', '23.3920351', '79.5363616', 0, 'uploads/attendance/52_photo_1789013332.png', '09:38:52', '11:57:51', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/152.0.7977.66 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1789013332.png', NULL),
(82, 61, 61, 'PEEYUSH MEHRA', '2026-09-10', 'present', '23.3920369', '79.5363625', 0, 'uploads/attendance/61_photo_1789017285.png', '10:44:45', '11:57:57', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1789017285.png', NULL),
(83, 64, 64, 'SHRIKANT SAHU', '2026-09-10', 'present', '23.3923517', '79.5378913', 0, 'uploads/attendance/64_photo_1789017333.png', '10:45:33', '11:57:01', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.200 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1789017333.png', NULL),
(84, 60, 60, 'PARV JAIN', '2026-09-10', 'present', '23.3920084', '79.5364415', 0, 'uploads/attendance/60_photo_1789017558.png', '10:49:18', '11:58:00', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1789017558.png', NULL),
(85, 52, 52, 'JYOTI LODHI', '2026-09-11', 'present', '23.3920346', '79.5363619', 0, 'uploads/attendance/52_photo_1789099686.png', '09:38:06', '12:06:00', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/152.0.7977.66 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1789099686.png', NULL),
(86, 64, 64, 'SHRIKANT SAHU', '2026-09-11', 'present', '23.3920321', '79.536355', 0, NULL, '10:51:50', '12:14:11', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.200 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1789104110.png', 'uploads/attendance/64_photo_1789104110.png'),
(87, 61, 61, 'PEEYUSH MEHRA', '2026-09-11', 'present', '23.3920324', '79.5363551', 0, NULL, '10:52:24', NULL, '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; V2518 Build/BP2A.250605.031.A3_V000L1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/61_sig_1789104144.png', 'uploads/attendance/61_photo_1789104144.png'),
(88, 66, 66, 'PRIYANKA GOUND', '2026-09-11', 'present', '23.3893601', '79.5352286', 0, NULL, '11:38:41', '12:00:44', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/152.0.7977.66 Mobile Safari/537.36 MobileApp', 'uploads/signatures/66_sig_1789106921.png', 'uploads/attendance/66_photo_1789106921.png'),
(89, 52, 52, 'JYOTI LODHI', '2026-09-12', 'present', '23.3924906', '79.538039', 0, NULL, '09:41:44', '12:09:15', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 14; RMX3933 Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/152.0.7977.66 Mobile Safari/537.36 MobileApp', 'uploads/signatures/52_sig_1789186304.png', 'uploads/attendance/52_photo_1789186304.png'),
(90, 60, 60, 'PARV JAIN', '2026-09-12', 'present', '23.3920353', '79.5363618', 0, NULL, '10:57:56', '12:08:23', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 16; 24108PCE2I Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 MobileApp', 'uploads/signatures/60_sig_1789190876.png', 'uploads/attendance/60_photo_1789190876.png'),
(91, 64, 64, 'SHRIKANT SAHU', '2026-09-12', 'present', '23.3923706', '79.5355514', 0, NULL, '10:58:39', '12:09:12', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 12; Redmi Note 9 Pro Max Build/SKQ1.211019.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.200 Mobile Safari/537.36 MobileApp', 'uploads/signatures/64_sig_1789190919.png', 'uploads/attendance/64_photo_1789190919.png');

-- --------------------------------------------------------

--
-- Table structure for table `business_settings`
--

CREATE TABLE `business_settings` (
  `id` int NOT NULL DEFAULT '1',
  `inst_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'CMS PRO',
  `inst_address` text COLLATE utf8mb4_general_ci,
  `inst_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `inst_email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `inst_logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_settings`
--

INSERT INTO `business_settings` (`id`, `inst_name`, `inst_address`, `inst_phone`, `inst_email`, `inst_logo`, `updated_at`) VALUES
(1, 'CMS PRO', 'Your Address Here', NULL, NULL, NULL, '2025-12-27 09:27:21');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL,
  `course_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `duration` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fees` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_code`, `duration`, `fees`, `image`) VALUES
(1, 'DCA (Diploma in Computer Application)', 'DCA01', '1 Year', '12000', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=1600'),
(2, 'PGDCA (Post Graduate Diploma)', 'PGDCA02', '1 Year', '13000', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=1600'),
(3, 'Tally Prime + GST Complete', 'TALLY03', '3 Months', '4500', 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1600'),
(4, 'Full Stack Web Development', 'WEB04', '6 Months', '15000', 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1600'),
(5, 'CPCT Preparation Batch', 'CPCT05', '2 Months', '2500', 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=1600'),
(6, 'BA(Bachelor of Arts)', 'BA01', '1 Year', '12000', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=1600');

-- --------------------------------------------------------

--
-- Table structure for table `document_requests`
--

CREATE TABLE `document_requests` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `document_type` enum('Certificate','Marksheet','ID Card','Report Card') COLLATE utf8mb4_general_ci NOT NULL,
  `fee_amount` decimal(10,2) DEFAULT '0.00',
  `fee_status` enum('Pending','Paid') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `admin_status` enum('Pending','Verified','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `applied_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_wallet`
--

CREATE TABLE `document_wallet` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` varchar(50) NOT NULL,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_wallet`
--

INSERT INTO `document_wallet` (`id`, `student_id`, `document_type`, `document_name`, `file_path`, `file_type`, `file_size`, `upload_date`, `status`) VALUES
(1, 41, 'Aadhaar Card', 'ADHAR', 'uploads/documents/DOC_41_1785691512_7907.png', 'png', '1.79 MB', '2026-08-02 17:25:12', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `ebooks`
--

CREATE TABLE `ebooks` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `course_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ebooks`
--

INSERT INTO `ebooks` (`id`, `title`, `course_name`, `file_path`, `uploaded_at`) VALUES
(4, 'PC PACAGE', 'DCA', 'EBOOK_1766768378_TC ACADAMY CENTER.pdf', '2025-12-26 16:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_interested` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Contacted') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int NOT NULL,
  `student_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','verified') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `batch_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `total_marks` int DEFAULT NULL,
  `passing_marks` int DEFAULT NULL,
  `exam_type` enum('Unit Test','Monthly','Mid-Term','Final') COLLATE utf8mb4_general_ci DEFAULT 'Unit Test'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `roll_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `course` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `subject_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_questions` int NOT NULL,
  `marks_obtained` int NOT NULL,
  `exam_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `student_id`, `roll_no`, `course`, `subject_name`, `total_questions`, `marks_obtained`, `exam_date`) VALUES
(1, 1, '123456', 'DCA', NULL, 1, 1, '2025-12-25 13:58:51'),
(2, 1, '123456', 'DCA', NULL, 1, 0, '2025-12-25 13:59:01'),
(3, 1, '123456', 'DCA', NULL, 1, 1, '2025-12-25 14:49:09'),
(4, 1, '123456', 'DCA', NULL, 2, 1, '2025-12-25 15:54:30'),
(5, 1, '123456', 'DCA', NULL, 2, 0, '2025-12-25 15:54:58'),
(6, 20, 'APP-2025-0001', 'DCA', NULL, 0, 0, '2026-03-11 15:13:07'),
(7, 41, 'e2072553449kna', 'DCA', NULL, 10, 8, '2026-08-02 23:07:55'),
(8, 52, 'jyoti', 'DCA', NULL, 0, 0, '2026-08-09 16:16:20'),
(9, 52, 'jyoti', 'DCA', 'General Exam', 0, 0, '2026-08-09 16:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `receipt_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('paid','pending') COLLATE utf8mb4_general_ci DEFAULT 'paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `student_id`, `amount_paid`, `payment_date`, `receipt_no`, `status`) VALUES
(12, 52, 1000.00, '2026-08-09', 'REC238100', 'paid'),
(14, 42, 2000.00, '2026-08-09', 'REC340747', 'paid'),
(15, 42, 4000.00, '2026-08-09', 'REC280008', 'paid'),
(16, 42, 4000.00, '2026-08-09', 'REC660696', 'paid'),
(17, 42, 4000.00, '2026-08-09', 'REC979043', 'paid'),
(18, 42, 2000.00, '2026-08-09', 'REC378621', 'paid'),
(19, 48, 4000.00, '2026-08-09', 'REC382012', 'paid'),
(20, 56, 2000.00, '2026-08-09', 'REC296441', 'paid'),
(21, 57, 1000.00, '2026-08-09', 'REC100760', 'paid'),
(22, 58, 6000.00, '2026-08-09', 'REC676200', 'paid'),
(23, 58, 6000.00, '2026-08-09', 'REC830599', 'paid'),
(24, 45, 5000.00, '2026-08-09', 'REC475752', 'paid'),
(25, 45, 5000.00, '2026-08-09', 'REC213065', 'paid'),
(26, 59, 2000.00, '2026-08-09', 'REC454578', 'paid'),
(27, 60, 400.00, '2026-08-09', 'REC316687', 'paid'),
(28, 62, 500.00, '2026-08-09', 'REC942582', 'paid'),
(29, 63, 500.00, '2026-08-09', 'REC994170', 'paid'),
(30, 64, 3500.00, '2026-08-13', 'REC542631', 'paid'),
(31, 61, 1000.00, '2026-08-19', 'REC156316', 'paid'),
(32, 60, 800.00, '2026-08-21', 'REC437580', 'paid'),
(33, 60, 200.00, '2026-08-21', 'REC623298', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `holiday_date` date NOT NULL,
  `description` text,
  `type` enum('Festival','National','Academy','Sunday') DEFAULT 'Festival',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

CREATE TABLE `homework` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homework`
--

INSERT INTO `homework` (`id`, `title`, `file_path`, `upload_date`) VALUES
(1, 'hindi', '1766597161_TC_ACADAMY_CENTER.pdf', '2025-12-24 17:26:01'),
(2, 'एम् एस एक्स्क्ल में बनाना हे ', 'HW_1786598756_ba2d5234_f65c_4cc2_817c_cab7e78d69d3.pdf', '2026-08-13 05:25:56');

-- --------------------------------------------------------

--
-- Table structure for table `lms_content`
--

CREATE TABLE `lms_content` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `video_link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pdf_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_doubts`
--

CREATE TABLE `lms_doubts` (
  `id` int NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `material_id` int NOT NULL,
  `question` text NOT NULL,
  `answer` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lms_doubts`
--

INSERT INTO `lms_doubts` (`id`, `student_id`, `material_id`, `question`, `answer`, `created_at`) VALUES
(1, 'Array', 101, 'hi', NULL, '2026-08-18 17:17:07'),
(2, 'Array', 101, 'hi', NULL, '2026-08-18 17:17:10'),
(3, 'Array', 101, 'hi', NULL, '2026-08-18 17:17:12'),
(4, 'Array', 101, 'hi', NULL, '2026-08-18 17:17:12'),
(5, 'Array', 101, 'hi', NULL, '2026-08-18 17:17:13'),
(6, 'Array', 101, 'hi', NULL, '2026-08-18 17:17:13'),
(7, 'Array', 101, 'hi', NULL, '2026-08-18 17:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `lms_materials`
--

CREATE TABLE `lms_materials` (
  `id` int NOT NULL,
  `course_code` varchar(50) DEFAULT 'DCA',
  `subject_name` varchar(100) NOT NULL,
  `chapter_title` varchar(255) NOT NULL,
  `content_type` enum('video','audio','pdf','text') NOT NULL,
  `media_url` text,
  `pdf_file` text,
  `text_notes` longtext,
  `duration_min` int DEFAULT '10',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_notes`
--

CREATE TABLE `lms_notes` (
  `id` int NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `material_id` int NOT NULL,
  `note_text` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_progress`
--

CREATE TABLE `lms_progress` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `material_id` int NOT NULL,
  `lecture_id` int NOT NULL,
  `status` enum('completed') DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `subject` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_marks` int DEFAULT NULL,
  `obtained_marks` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student_id`, `subject`, `subject_name`, `total_marks`, `obtained_marks`, `created_at`) VALUES
(16, 16, NULL, 'Fundamentals of IT', 100, 80, '2026-08-09 03:56:40'),
(17, 16, NULL, 'PC Package', 100, 85, '2026-08-09 03:56:40'),
(18, 16, NULL, 'Database Using MS Access', 100, 90, '2026-08-09 03:56:40'),
(19, 16, NULL, 'Programming with VB.Net', 100, 95, '2026-08-09 03:56:40'),
(20, 20, NULL, 'Fundamentals of IT', 100, 90, '2026-08-09 03:56:40'),
(24, 20, NULL, 'PC Package (Word, Excel, PPT)', 100, 90, '2026-08-09 03:56:40'),
(25, 20, NULL, 'MS Access / FoxPro', 100, 90, '2026-08-09 03:56:40'),
(26, 20, NULL, 'IT Trends', 100, 100, '2026-08-09 03:56:40'),
(27, 33, NULL, 'Fundamental of Computers', 100, 0, '2026-08-09 03:56:40');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int NOT NULL,
  `course` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `subject_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subject_id` int DEFAULT NULL,
  `question_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `option_a` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option_b` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option_c` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option_d` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `correct_option` char(1) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `course`, `subject_name`, `subject_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(1, 'DCA', NULL, 2, 'Windows ऑपरेटिंग सिस्टम में \"Control Panel\" का मुख्य कार्य क्या है?', 'सिस्टम की सेटिंग्स को कॉन्फ़िगर करना', 'इंटरनेट ब्राउज़िंग', 'दस्तावेज़ संपादित करना', 'गेम खेलना', 'A'),
(2, 'DCA', NULL, 2, 'MS Word में \"Mail Merge\" सुविधा का उपयोग किस लिए किया जाता है?', 'फाइल को पीडीएफ में बदलना', 'ईमेल डिलीट करना', 'एक ही पत्र कई प्राप्तकर्ताओं को भेजने के लिए', 'टेबल बनाना', 'C'),
(3, 'DCA', NULL, 2, 'MS Excel में \"VLOOKUP\" फ़ंक्शन का प्राथमिक उपयोग क्या है?', 'फॉन्ट बदलना', 'डेटा खोजने और लाने के लिए', 'ग्राफ बनाना', 'सिस्टम रीस्टार्ट', 'B'),
(4, 'DCA', NULL, 2, 'Windows में फाइल स्थायी रूप से डिलीट करने के लिए किस की-कॉम्बिनेशन का उपयोग किया जाता है?', 'Ctrl + Delete', 'Alt + Delete', 'Shift + Delete', 'Backspace + Delete', 'C'),
(5, 'DCA', NULL, 2, 'MS PowerPoint में \"Slide Master\" का क्या महत्व है?', 'वीडियो फाइल जोड़ना', 'पूरी प्रेजेंटेशन की डिज़ाइन नियंत्रित करने के लिए', 'स्लाइड का रंग बदलना', 'इंटरनेट कनेक्ट करना', 'B'),
(6, 'DCA', NULL, 2, 'MS Word में \"Format Painter\" का क्या कार्य है?', 'ईमेल भेजना', 'टेबल डिलीट करना', 'टेक्स्ट का फॉन्ट साइज बढ़ाना', 'फॉर्मेटिंग कॉपी करके अप्लाई करना', 'D'),
(7, 'DCA', NULL, 2, 'MS Excel में सेल में फॉर्मूला शुरू करने के लिए किस चिह्न का उपयोग किया जाता है?', '=', '+', '-', '*', 'A'),
(8, 'DCA', NULL, 2, 'Windows Task Manager खोलने की शॉर्टकट की क्या है?', 'Alt + F4', 'Ctrl + Shift + Esc', 'Windows + R', 'Ctrl + Alt + Delete', 'B'),
(9, 'DCA', NULL, 2, 'MS Word में \"Header and Footer\" का उपयोग क्यों किया जाता है?', 'पेज के शीर्ष/नीचे जानकारी डालने के लिए', 'डॉक्यूमेंट सुरक्षित करना', 'केवल पहले पेज पर लिखना', 'ग्राफ बनाना', 'A'),
(10, 'DCA', NULL, 2, 'MS Excel में \"AutoSum\" बटन का उपयोग क्या है?', 'नया डेटाबेस बनाना', 'फाइल सेव करना', 'टेक्स्ट बोल्ड करना', 'संख्याओं को जोड़ने के लिए', 'D'),
(11, 'DCA', NULL, 2, 'Windows में \"Recycle Bin\" का मुख्य उद्देश्य क्या है?', 'वायरस हटाना', 'डिलीट की गई फाइलों को स्टोर करना', 'इंटरनेट चलाना', 'कंप्यूटर स्पीड बढ़ाना', 'B'),
(12, 'DCA', NULL, 2, 'MS PowerPoint में प्रेजेंटेशन को स्लाइड शो मोड में चलाने की शॉर्टकट की क्या है?', 'F1', 'F10', 'Esc', 'F5', 'D'),
(13, 'DCA', NULL, 2, 'MS Word में पैराग्राफ को \"Justify\" करने का क्या अर्थ है?', 'टेक्स्ट का रंग बदलना', 'टेक्स्ट को दोनों किनारों पर बराबर करना', 'टेक्स्ट डिलीट करना', 'टेक्स्ट बीच में करना', 'B'),
(14, 'DCA', NULL, 2, 'MS Excel में शीट के नाम को बदलने के लिए क्या करना पड़ता है?', 'शीट टैब पर राइट क्लिक करके \"Rename\" चुनें', 'शीट को डिलीट करें', 'नया ग्राफ बनाएं', 'कीबोर्ड डिलीट दबाएं', 'A'),
(15, 'DCA', NULL, 2, 'Windows में डेस्कटॉप पर आइकन्स के आकार को बदलने के लिए क्या करना चाहिए?', 'सिस्टम शटडाउन करें', 'डेस्कटॉप पर राइट क्लिक करके \"View\" में जाएं', 'माउस डिस्कनेक्ट करें', 'कीबोर्ड लाइट बंद करें', 'B'),
(16, 'DCA', NULL, 2, 'MS Word में \"Watermark\" का उपयोग क्यों किया जाता है?', 'पेज नंबर देना', 'फोटो साफ करना', 'पेज का रंग बदलना', 'पृष्ठ के पीछे धुंधला लोगो डालने के लिए', 'D'),
(17, 'DCA', NULL, 2, 'MS Excel में एक पूरी कॉलम को सेलेक्ट करने की शॉर्टकट की क्या है?', 'Ctrl + Spacebar', 'Shift + Spacebar', 'Alt + Spacebar', 'Ctrl + A', 'A'),
(18, 'DCA', NULL, 2, 'MS PowerPoint में ट्रांज़िशन (Transition) क्या है?', 'टेबल टूल', 'ईमेल टूल', 'एक स्लाइड से दूसरी में जाने का इफ़ेक्ट', 'फोटो जोड़ने का तरीका', 'C'),
(19, 'DCA', NULL, 2, 'Windows में \"Device Manager\" का कार्य क्या है?', 'हार्डवेयर घटकों को प्रबंधित/अपडेट करना', 'सॉफ्टवेयर डाउनलोड', 'इंटरनेट गति', 'पावर ऑफ', 'A'),
(20, 'DCA', NULL, 2, 'MS Word में \"Hyperlink\" का उपयोग क्या है?', 'डॉक्यूमेंट सेव करना', 'दूसरे वेब पेज या फाइल को लिंक करना', 'पेज प्रिंट करना', 'टेक्स्ट कॉपी करना', 'B'),
(21, 'DCA', NULL, 2, 'MS Excel में \"Average\" फ़ंक्शन का उपयोग क्या है?', 'औसत (Mean) निकालने के लिए', 'योग करना', 'डेटा डिलीट', 'कॉलम डिलीट', 'A'),
(22, 'DCA', NULL, 2, 'Windows में \"File Explorer\" खोलने की शॉर्टकट की क्या है?', 'Windows + R', 'Windows + F', 'Windows + E', 'Windows + L', 'C'),
(23, 'DCA', NULL, 2, 'MS Word में \"Spelling and Grammar\" चेक करने की शॉर्टकट की क्या है?', 'F2', 'F7', 'F5', 'F12', 'B'),
(24, 'DCA', NULL, 2, 'MS Excel में \"Freeze Panes\" का क्या उपयोग है?', 'सेल रंग बदलना', 'पंक्तियों/स्तंभों को स्थिर रखने के लिए', 'शीट लॉक', 'ग्राफ ज़ूम', 'B'),
(25, 'DCA', NULL, 2, 'Windows ऑपरेटिंग सिस्टम किस कंपनी द्वारा विकसित किया गया है?', 'Apple', 'Google', 'Microsoft', 'IBM', 'C'),
(26, 'DCA', NULL, 2, 'MS PowerPoint में \"Custom Animation\" क्या है?', 'प्रेजेंटेशन शीर्षक', 'साउंड इफ़ेक्ट', 'स्लाइड पर मूव करने का इफ़ेक्ट', 'ग्राफ', 'C'),
(27, 'DCA', NULL, 2, 'MS Word में \"Page Break\" क्या करता है?', 'नए पेज पर कर्सर ले जाना', 'फाइल डिलीट', 'टेबल बनाना', 'फॉन्ट बदलना', 'A'),
(28, 'DCA', NULL, 2, 'MS Excel में \"Conditional Formatting\" क्या है?', 'शर्त के आधार पर सेल हाईलाइट करना', 'टेक्स्ट डिलीट', 'फाइल सेव', 'फॉर्मूला बनाना', 'A'),
(29, 'DCA', NULL, 2, 'Windows में \"Lock Screen\" को सक्रिय करने की शॉर्टकट की क्या है?', 'Windows + D', 'Windows + L', 'Windows + M', 'Windows + K', 'B'),
(30, 'DCA', NULL, 2, 'MS Word में \"Table of Contents\" कैसे बनाया जाता है?', 'Heading स्टाइल्स का उपयोग करके', 'टेक्स्ट टाइप', 'फोटो लगाना', 'पेज नंबर डालना', 'A'),
(31, 'DCA', NULL, 2, 'MS Excel में \"Absolute Reference\" के लिए किस प्रतीक का उपयोग किया जाता है?', '@', '#', '&', '$', 'D'),
(32, 'DCA', NULL, 2, 'MS PowerPoint में स्लाइड्स का लेआउट बदलने के लिए किस ऑप्शन का उपयोग करें?', 'Design', 'Transition', 'Layout', 'Animation', 'C'),
(33, 'DCA', NULL, 2, 'Windows में \"System Restore\" का उपयोग कब किया जाता है?', 'कंप्यूटर को पुरानी स्थिति में लाने के लिए', 'नेटवर्क ठीक करना', 'कीबोर्ड साफ करना', 'डिस्प्ले बदलना', 'A'),
(34, 'DCA', NULL, 2, 'MS Word में \"Document properties\" में क्या देखा जा सकता है?', 'फाइल का लेखक और शीर्षक', 'फाइल का नाम', 'फाइल का आकार', 'इंटरनेट जानकारी', 'A'),
(35, 'DCA', NULL, 2, 'MS Excel में \"Sort\" फीचर क्या करता है?', 'डेटा डिलीट करना', 'डेटा क्रमबद्ध करना', 'फॉर्मूला जोड़ना', 'ग्राफ बनाना', 'B'),
(36, 'DCA', NULL, 2, 'Windows में \"Command Prompt\" (CMD) का क्या उपयोग है?', 'वीडियो चलाना', 'फोटो एडिट', 'टाइपिंग', 'कमांड से कंप्यूटर नियंत्रित करना', 'D'),
(37, 'DCA', NULL, 2, 'MS Word में \"WordArt\" क्या है?', 'टेक्स्ट को स्टाइलिश रूप देने के लिए', 'पेज नंबर', 'एक फोटो', 'एक टेबल', 'A'),
(38, 'DCA', NULL, 2, 'MS Excel में \"Max\" फ़ंक्शन क्या करता है?', 'सबसे बड़ी संख्या ज्ञात करना', 'छोटी संख्या', 'योग करना', 'औसत', 'A'),
(39, 'DCA', NULL, 2, 'Windows में \"Screen Capture\" लेने के लिए किसका प्रयोग करें?', 'रिस्टार्ट', 'लॉक', 'Snipping Tool या Print Screen', 'शटडाउन', 'C'),
(40, 'DCA', NULL, 2, 'MS Word में \"Print Preview\" का क्या लाभ है?', 'प्रिंट से पहले डॉक्यूमेंट देखना', 'ईमेल करना', 'टेक्स्ट डिलीट', 'फॉन्ट चेंज', 'A'),
(41, 'DCA', NULL, 2, 'MS Excel में \"Pivot Table\" का उपयोग क्या है?', 'डेटा विश्लेषित करने के लिए', 'ग्राफ', 'टेक्स्ट एडिट', 'फॉर्मूला जोड़ने के लिए', 'A'),
(42, 'DCA', NULL, 2, 'Windows में \"Taskbar\" की स्थिति कहाँ बदली जा सकती है?', 'टास्कबार को लॉक हटाकर ड्रैग करके', 'सिस्टम में नहीं', 'कीबोर्ड से', 'माउस बिना', 'A'),
(43, 'DCA', NULL, 2, 'MS Word में \"Track Changes\" सुविधा का क्या काम है?', 'बदलावों को रिकॉर्ड करना', 'गति मापना', 'पेज डिलीट', 'ब्राउज़िंग', 'A'),
(44, 'DCA', NULL, 2, 'MS Excel में \"Count\" फ़ंक्शन क्या गिनता है?', 'सभी सेल्स', 'केवल संख्या वाली सेल्स', 'खाली सेल्स', 'फॉर्मूला', 'B'),
(45, 'DCA', NULL, 2, 'Windows में \"Settings\" एप खोलने के लिए किस शॉर्टकट का उपयोग करें?', 'Windows + S', 'Windows + P', 'Windows + D', 'Windows + I', 'D'),
(46, 'DCA', NULL, 2, 'MS Word में \"Find and Replace\" की शॉर्टकट क्या है?', 'Ctrl + F', 'Ctrl + H', 'Ctrl + R', 'Ctrl + D', 'B'),
(47, 'DCA', NULL, 2, 'MS Excel में \"Merge and Center\" क्या करता है?', 'सेल डिलीट', 'कॉलम छिपाना', 'फाइल सेव', 'सेल्स जोड़कर टेक्स्ट बीच में लाना', 'D'),
(48, 'DCA', NULL, 2, 'Windows में \"Accessibility\" सेटिंग्स का क्या उपयोग है?', 'इंटरनेट स्पीड', 'गेम सेटिंग', 'विशेष आवश्यकता के लिए सुविधाएँ', 'रंग बदलना', 'C'),
(49, 'DCA', NULL, 2, 'MS Word में \"Bulleted List\" क्या है?', 'पेज नंबर', 'प्रतीकों के साथ क्रमबद्ध करना', 'टेबल', 'ग्राफ', 'B'),
(50, 'DCA', NULL, 2, 'MS Excel में \"Save As\" का क्या अर्थ है?', 'फाइल प्रिंट', 'फाइल ईमेल', 'फाइल डिलीट', 'नए नाम/फॉर्मेट में सुरक्षित करना', 'D'),
(51, 'DCA', NULL, 3, 'Every page on the web has a unique address called:\nवेब पर मौजूद प्रत्येक पेज का एक अनूठा पता होता है, जिसे कहते हैं:', 'IP', 'URL', 'Hyperlink', 'Domain', 'B'),
(52, 'DCA', NULL, 3, 'What is the full form of WAN?\nWAN का पूर्ण रूप क्या है?', 'Wide Area Network', 'Wireless Area Network', 'World Area Network', 'Web Area Network', 'A'),
(53, 'DCA', NULL, 3, 'Who developed the C++ programming language?\nC++ प्रोग्रामिंग भाषा का विकास किसने किया?', 'Dennis Ritchie', 'Bjarne Stroustrup', 'James Gosling', 'Guido van Rossum', 'B'),
(54, 'DCA', NULL, 3, 'Which of the following is not a core feature of OOPs?\nनिम्नलिखित में से कौन सी OOPs की मुख्य विशेषता नहीं है?', 'Encapsulation', 'Inheritance', 'Polymorphism', 'Compilation', 'D'),
(55, 'DCA', NULL, 3, 'What is an instance of a class called?\nएक क्लास के इंस्टेंस (Instance) को क्या कहा जाता है?', 'Function', 'Object', 'Data type', 'Variable', 'B'),
(56, 'DCA', NULL, 3, 'Which access specifier makes class members accessible only within the same class?\nकौन सा एक्सेस स्पेसिफायर क्लास के मेंबर्स को केवल उसी क्लास के अंदर सुलभ बनाता है?', 'public', 'private', 'protected', 'internal', 'B'),
(57, 'DCA', NULL, 3, 'Which operator is used to define a member of a class outside the class?\nक्लास के बाहर किसी मेंबर को परिभाषित करने के लिए किस ऑपरेटर का उपयोग किया जाता है?', ':: (Scope Resolution)', '. (Dot)', '-> (Arrow)', ': (Colon)', 'A'),
(58, 'DCA', NULL, 3, 'What is a constructor in C++?\nC++ में कंस्ट्रक्टर क्या है?', 'A function to delete objects', 'A special function to initialize objects', 'A data type', 'A type of loop', 'B'),
(59, 'DCA', NULL, 3, 'A constructor has the same name as:\nकंस्ट्रक्टर का नाम किसके बिल्कुल समान होता है?', 'Object', 'Class', 'Function', 'Data Member', 'B'),
(60, 'DCA', NULL, 3, 'Which of the following cannot be overloaded?\nनिम्नलिखित में से किसे ओवरलोड नहीं किया जा सकता?', 'Member functions', 'Constructors', 'Destructors', 'Operators', 'C'),
(61, 'DCA', NULL, 3, 'What is the correct syntax to inherit a class B from class A publicly?\nक्लास A से क्लास B को पब्लिकली इनहेरिट करने का सही सिंटैक्स क्या है?', 'class B : public A {}', 'class B inherit A {}', 'class B extends A {}', 'class B :: public A {}', 'A'),
(62, 'DCA', NULL, 3, 'Which keyword is used to allocate memory dynamically in C++?\nC++ में डायनेमिक रूप से मेमोरी एलोकेट करने के लिए किस कीवर्ड का उपयोग किया जाता है?', 'alloc', 'malloc', 'new', 'create', 'C'),
(63, 'DCA', NULL, 3, 'Which keyword is used to free the dynamically allocated memory in C++?\nC++ में डायनेमिक रूप से एलोकेट की गई मेमोरी को फ्री करने के लिए किस कीवर्ड का उपयोग होता है?', 'free', 'delete', 'remove', 'drop', 'B'),
(64, 'DCA', NULL, 3, 'What is polymorphism in C++?\nC++ में पॉलिमॉर्फिज्म (Polymorphism) क्या है?', 'Single name, multiple forms', 'Multiple names, single form', 'Hiding data', 'Reusing code', 'A'),
(65, 'DCA', NULL, 3, 'Which function cannot be inherited but can access private members of a class?\nकौन सा फंक्शन इनहेरिट नहीं हो सकता लेकिन क्लास के प्राइवेट मेंबर्स को एक्सेस कर सकता है?', 'Virtual function', 'Friend function', 'Static function', 'Inline function', 'B'),
(66, 'DCA', NULL, 3, 'A pure virtual function is equated to which value?\nएक प्योर वर्चुअल फंक्शन (Pure Virtual Function) को किस वैल्यू के बराबर रखा जाता है?', '1', '-1', '0', 'NULL', 'C'),
(67, 'DCA', NULL, 3, 'A class that contains at least one pure virtual function is called:\nवह क्लास जिसमें कम से कम एक प्योर वर्चुअल फंक्शन हो, क्या कहलाती है?', 'Concrete class', 'Friend class', 'Abstract class', 'Derived class', 'C'),
(68, 'DCA', NULL, 3, 'What is the default access specifier for members of a class in C++?\nC++ में क्लास के मेंबर्स के लिए डिफॉल्ट एक्सेस स्पेसिफायर क्या होता है?', 'public', 'protected', 'private', 'global', 'C'),
(69, 'DCA', NULL, 3, 'Which of the following header files is required for input/output operations in C++?\nC++ में इनपुट/आउटपुट ऑपरेशन्स के लिए किस हेडर फ़ाइल की आवश्यकता होती है?', 'stdio.h', 'conio.h', 'iostream', 'stdlib.h', 'C'),
(70, 'DCA', NULL, 3, 'What is the use of \"cin\" in C++?\nC++ में \"cin\" का क्या उपयोग है?', 'To print output', 'To take input', 'To clear screen', 'To exit program', 'B'),
(71, 'DCA', NULL, 3, 'What is the use of \"cout\" in C++?\nC++ में \"cout\" का क्या उपयोग है?', 'To take input', 'To display output', 'To define a class', 'To delete an object', 'B'),
(72, 'DCA', NULL, 3, 'Wrapping up data and functions into a single unit is called:\nडेटा और फंक्शन्स को एक सिंगल यूनिट में लपेटना (बांधना) क्या कहलाता है?', 'Abstraction', 'Encapsulation', 'Inheritance', 'Polymorphism', 'B'),
(73, 'DCA', NULL, 3, 'Hiding internal details and showing only functionality is known as:\nआंतरिक विवरणों को छुपाना और केवल कार्यक्षमता दिखाना क्या कहलाता है?', 'Data Abstraction', 'Encapsulation', 'Inheritance', 'Nesting', 'A'),
(74, 'DCA', NULL, 3, 'How many types of polymorphism are there in C++?\nC++ में पॉलिमॉर्फिज्म कितने प्रकार का होता है?', '1', '2', '3', '4', 'B'),
(75, 'DCA', NULL, 3, 'Function overloading is an example of:\nफंक्शन ओवरलोडिंग किसका एक उदाहरण है?', 'Compile-time polymorphism', 'Run-time polymorphism', 'Inheritance', 'Encapsulation', 'A'),
(76, 'DCA', NULL, 3, 'Virtual functions are used to achieve:\nवर्चुअल फंक्शन्स का उपयोग क्या प्राप्त करने के लिए किया जाता है?', 'Compile-time polymorphism', 'Run-time polymorphism', 'Data hiding', 'Encapsulation', 'B'),
(77, 'DCA', NULL, 3, 'Which of the following features allows code reusability?\nनिम्नलिखित में से कौन सा फीचर कोड के पुनर्व्यवहार (Code Reusability) की अनुमति देता है?', 'Polymorphism', 'Encapsulation', 'Inheritance', 'Abstraction', 'C'),
(78, 'DCA', NULL, 3, 'A destructor can take how many arguments?\nएक डिस्ट्रक्टर (Destructor) कितने आर्गुमेंट्स ले सकता है?', '0', '1', '2', 'Any number', 'A'),
(79, 'DCA', NULL, 3, 'Which symbol is used prefix to define a destructor?\nडिस्ट्रक्टर को परिभाषित करने के लिए किस सिंबल का उपसर्ग (Prefix) लगाया जाता है?', '#', '&', '~ (Tilde)', '!', 'C'),
(80, 'DCA', NULL, 3, 'Which inheritance involves one derived class and more than one base class?\nकिस इनहेरिटेंस में एक डिराइव्ड क्लास और एक से अधिक base क्लास शामिल होती हैं?', 'Multiple Inheritance', 'Multilevel Inheritance', 'Hierarchical Inheritance', 'Single Inheritance', 'A'),
(81, 'DCA', NULL, 3, 'Which inheritance has a chain of classes like Class A -> Class B -> Class C?\nकिस इनहेरिटेंस में क्लासेज की एक चेन होती है जैसे क्लास A -> क्लास B -> क्लास C?', 'Multiple Inheritance', 'Multilevel Inheritance', 'Hierarchical Inheritance', 'Hybrid Inheritance', 'B'),
(82, 'DCA', NULL, 3, 'Which inheritance involves one base class and multiple derived classes?\nकिस इनहेरिटेंस में एक बेस क्लास और कई डिराइव्ड क्लासेज शामिल होती हैं?', 'Multiple Inheritance', 'Multilevel Inheritance', 'Hierarchical Inheritance', 'Single Inheritance', 'C'),
(83, 'DCA', NULL, 3, 'What is the storage size of a \'char\' data type in C++?\nC++ में \'char\' डेटा टाइप का स्टोरेज साइज क्या होता है?', '1 Byte', '2 Bytes', '4 Bytes', '8 Bytes', 'A'),
(84, 'DCA', NULL, 3, 'Which keyword is used to handle exceptions in C++?\nC++ में एक्सेप्शन को हैंडल करने के लिए किस कीवर्ड का उपयोग किया जाता है?', 'try', 'catch', 'throw', 'All of the above', 'D'),
(85, 'DCA', NULL, 3, 'The block that catches and handles an exception is called:\nवह ब्लॉक जो एक्सेप्शन को पकड़ता है और हैंडल करता है, कहलाता है:', 'try block', 'catch block', 'throw block', 'final block', 'B'),
(86, 'DCA', NULL, 3, 'Which keyword is used to explicitly throw an exception?\nएक्सेप्शन को स्पष्ट रूप से थ्रो (सेंड) करने के लिए किस कीवर्ड का उपयोग होता है?', 'try', 'catch', 'throw', 'raise', 'C'),
(87, 'DCA', NULL, 3, 'What is \'this\' pointer in C++?\nC++ में \'this\' पॉइंटर क्या है?', 'A pointer to the base class', 'A pointer pointing to the current object', 'A syntax error', 'A pointer to a global variable', 'B'),
(88, 'DCA', NULL, 3, 'Can a constructor be private in C++?\nक्या C++ में कंस्ट्रक्टर प्राइवेट हो सकता है?', 'Yes', 'No', 'Only inside structures', 'Depends on compiler', 'A'),
(89, 'DCA', NULL, 3, 'Inline functions are used to:\nइनलाइन फंक्शन्स (Inline Functions) का उपयोग किसलिए किया जाता है?', 'Save memory space', 'Reduce function call overhead and save time', 'Hide data', 'Increase security', 'B'),
(90, 'DCA', NULL, 3, 'Which of the following cannot be friend in C++?\nC++ में निम्नलिखित में से कौन \'फ्रेंड\' नहीं हो सकता?', 'A function', 'A class', 'An object', 'An operator', 'C'),
(91, 'DCA', NULL, 3, 'What is the extension of a C++ source code file?\nC++ सोर्स कोड फ़ाइल का एक्सटेंशन क्या होता है?', '.c', '.cpp', '.obj', '.exe', 'B'),
(92, 'DCA', NULL, 3, 'Which operator is used for input stream in C++?\nC++ में इनपुट स्ट्रीम के लिए किस ऑपरेटर (Extraction) का उपयोग होता है?', '<<', '>>', '<', '>', 'B'),
(93, 'DCA', NULL, 3, 'Which operator is used for output stream in C++?\nC++ में आउटपुट स्ट्रीम के लिए किस ऑपरेटर (Insertion) का उपयोग होता है?', '<<', '>>', '<', '>', 'A'),
(94, 'DCA', NULL, 3, 'Static data members are shared by:\nस्टैटिक डेटा मेंबर्स किसके द्वारा साझा (Share) किए जाते हैं?', 'Only one object', 'All objects of that class', 'No object', 'Global functions', 'B'),
(95, 'DCA', NULL, 3, 'What is a template in C++?\nC++ में टेम्पलेट (Template) क्या है?', 'A design layout', 'A feature to write generic functions and classes', 'A file extension', 'A debugging tool', 'B'),
(96, 'DCA', NULL, 3, 'Which keyword is used to define a template?\nटेम्पलेट को परिभाषित करने के लिए किस कीवर्ड का उपयोग किया जाता है?', 'temp', 'template', 'generic', 'class', 'B'),
(97, 'DCA', NULL, 3, 'Which of the following is stream class used for file writing in C++?\nC++ में फ़ाइल राइटिंग (लिखने) के लिए किस स्ट्रीम क्लास का उपयोग होता है?', 'ifstream', 'ofstream', 'fstream', 'iostream', 'B'),
(98, 'DCA', NULL, 3, 'Which stream class is used for file reading in C++?\nC++ में फ़ाइल रीडिंग (पढ़ने) के लिए किस स्ट्रीम क्लास का उपयोग होता है?', 'ifstream', 'ofstream', 'fstream', 'iostream', 'A'),
(99, 'DCA', NULL, 3, 'What is the role of \'endl\' in C++?\nC++ में \'endl\' की क्या भूमिका है?', 'To end the program', 'To insert a new line and flush the stream', 'To declare a variable', 'To exit a loop', 'B'),
(100, 'DCA', NULL, 3, 'Which user-defined data type can contain variables of different data types in C++?\nC++ में कौन सा यूजर-डिफाइंड डेटा टाइप विभिन्न डेटा टाइप्स के वेरिएबल्स रख सकता है?', 'Array', 'Structure / Class', 'Pointer', 'Function', 'B'),
(101, 'DCA', NULL, 3, 'What represents a real-world entity in programming?\nप्रोग्रामिंग में रियल-वर्ल्ड एंटिटी (वास्तविक दुनिया की वस्तु) का प्रतिनिधित्व कौन करता है?', 'Class', 'Object', 'Method', 'Operator', 'B'),
(102, 'DCA', NULL, 3, 'A class is a _________ data type.\nक्लास एक _________ डेटा टाइप है।', 'Primitive', 'User-defined', 'Built-in', 'System', 'B'),
(103, 'DCA', NULL, 3, 'Who developed the C++ programming language?\nC++ प्रोग्रामिंग भाषा का विकास किसने किया?', 'Dennis Ritchie', 'Bjarne Stroustrup', 'James Gosling', 'Guido van Rossum', 'B'),
(104, 'DCA', NULL, 3, 'Which of the following is not a core feature of OOPs?\nनिम्नलिखित में से कौन सी OOPs की मुख्य विशेषता नहीं है?', 'Encapsulation', 'Inheritance', 'Polymorphism', 'Compilation', 'D'),
(105, 'DCA', NULL, 3, 'What is an instance of a class called?\nएक क्लास के इंस्टेंस (Instance) को क्या कहा जाता है?', 'Function', 'Object', 'Data type', 'Variable', 'B'),
(106, 'DCA', NULL, 3, 'Which access specifier makes class members accessible only within the same class?\nकौन सा एक्सेस स्पेसिफायर क्लास के मेंबर्स को केवल उसी क्लास के अंदर सुलभ बनाता है?', 'public', 'private', 'protected', 'internal', 'B'),
(107, 'DCA', NULL, 3, 'Which operator is used to define a member of a class outside the class?\nक्लास के बाहर किसी मेंबर को परिभाषित करने के लिए किस ऑपरेटर का उपयोग किया जाता है?', ':: (Scope Resolution)', '. (Dot)', '-> (Arrow)', ': (Colon)', 'A'),
(108, 'DCA', NULL, 3, 'What is a constructor in C++?\nC++ में कंस्ट्रक्टर क्या है?', 'A function to delete objects', 'A special function to initialize objects', 'A data type', 'A type of loop', 'B'),
(109, 'DCA', NULL, 3, 'A constructor has the same name as:\nकंस्ट्रक्टर का नाम किसके बिल्कुल समान होता है?', 'Object', 'Class', 'Function', 'Data Member', 'B'),
(110, 'DCA', NULL, 3, 'Which of the following cannot be overloaded?\nनिम्नलिखित में से किसे ओवरलोड नहीं किया जा सकता?', 'Member functions', 'Constructors', 'Destructors', 'Operators', 'C'),
(111, 'DCA', NULL, 3, 'What is the correct syntax to inherit a class B from class A publicly?\nक्लास A से क्लास B को पब्लिकली इनहेरिट करने का सही सिंटैक्स क्या है?', 'class B : public A {}', 'class B inherit A {}', 'class B extends A {}', 'class B :: public A {}', 'A'),
(112, 'DCA', NULL, 3, 'Which keyword is used to allocate memory dynamically in C++?\nC++ में डायनेमिक रूप से मेमोरी एलोकेट करने के लिए किस कीवर्ड का उपयोग किया जाता है?', 'alloc', 'malloc', 'new', 'create', 'C'),
(113, 'DCA', NULL, 3, 'Which keyword is used to free the dynamically allocated memory in C++?\nC++ में डायनेमिक रूप से एलोकेट की गई मेमोरी को फ्री करने के लिए किस कीवर्ड का उपयोग होता है?', 'free', 'delete', 'remove', 'drop', 'B'),
(114, 'DCA', NULL, 3, 'What is polymorphism in C++?\nC++ में पॉलिमॉर्फिज्म (Polymorphism) क्या है?', 'Single name, multiple forms', 'Multiple names, single form', 'Hiding data', 'Reusing code', 'A'),
(115, 'DCA', NULL, 3, 'Which function cannot be inherited but can access private members of a class?\nकौन सा फंक्शन इनहेरिट नहीं हो सकता लेकिन क्लास के प्राइवेट मेंबर्स को एक्सेस कर सकता है?', 'Virtual function', 'Friend function', 'Static function', 'Inline function', 'B'),
(116, 'DCA', NULL, 3, 'A pure virtual function is equated to which value?\nएक प्योर वर्चुअल फंक्शन (Pure Virtual Function) को किस वैल्यू के बराबर रखा जाता है?', '1', '-1', '0', 'NULL', 'C'),
(117, 'DCA', NULL, 3, 'A class that contains at least one pure virtual function is called:\nवह क्लास जिसमें कम से कम एक प्योर वर्चुअल फंक्शन हो, क्या कहलाती है?', 'Concrete class', 'Friend class', 'Abstract class', 'Derived class', 'C'),
(118, 'DCA', NULL, 3, 'What is the default access specifier for members of a class in C++?\nC++ में क्लास के मेंबर्स के लिए डिफॉल्ट एक्सेस स्पेसिफायर क्या होता है?', 'public', 'protected', 'private', 'global', 'C'),
(119, 'DCA', NULL, 3, 'Which of the following header files is required for input/output operations in C++?\nC++ में इनपुट/आउटपुट ऑपरेशन्स के लिए किस हेडर फ़ाइल की आवश्यकता होती है?', 'stdio.h', 'conio.h', 'iostream', 'stdlib.h', 'C'),
(120, 'DCA', NULL, 3, 'What is the use of \"cin\" in C++?\nC++ में \"cin\" का क्या उपयोग है?', 'To print output', 'To take input', 'To clear screen', 'To exit program', 'B'),
(121, 'DCA', NULL, 3, 'What is the use of \"cout\" in C++?\nC++ में \"cout\" का क्या उपयोग है?', 'To take input', 'To display output', 'To define a class', 'To delete an object', 'B'),
(122, 'DCA', NULL, 3, 'Wrapping up data and functions into a single unit is called:\nडेटा और फंक्शन्स को एक सिंगल यूनिट में लपेटना (बांधना) क्या कहलाता है?', 'Abstraction', 'Encapsulation', 'Inheritance', 'Polymorphism', 'B'),
(123, 'DCA', NULL, 3, 'Hiding internal details and showing only functionality is known as:\nआंतरिक विवरणों को छुपाना और केवल कार्यक्षमता दिखाना क्या कहलाता है?', 'Data Abstraction', 'Encapsulation', 'Inheritance', 'Nesting', 'A'),
(124, 'DCA', NULL, 3, 'How many types of polymorphism are there in C++?\nC++ में पॉलिमॉर्फिज्म कितने प्रकार का होता है?', '1', '2', '3', '4', 'B'),
(125, 'DCA', NULL, 3, 'Function overloading is an example of:\nफंक्शन ओवरलोडिंग किसका एक उदाहरण है?', 'Compile-time polymorphism', 'Run-time polymorphism', 'Inheritance', 'Encapsulation', 'A'),
(126, 'DCA', NULL, 3, 'Virtual functions are used to achieve:\nवर्चुअल फंक्शन्स का उपयोग क्या प्राप्त करने के लिए किया जाता है?', 'Compile-time polymorphism', 'Run-time polymorphism', 'Data hiding', 'Encapsulation', 'B'),
(127, 'DCA', NULL, 3, 'Which of the following features allows code reusability?\nनिम्नलिखित में से कौन सा फीचर कोड के पुनर्व्यवहार (Code Reusability) की अनुमति देता है?', 'Polymorphism', 'Encapsulation', 'Inheritance', 'Abstraction', 'C'),
(128, 'DCA', NULL, 3, 'A destructor can take how many arguments?\nएक डिस्ट्रक्टर (Destructor) कितने आर्गुमेंट्स ले सकता है?', '0', '1', '2', 'Any number', 'A'),
(129, 'DCA', NULL, 3, 'Which symbol is used prefix to define a destructor?\nडिस्ट्रक्टर को परिभाषित करने के लिए किस सिंबल का उपसर्ग (Prefix) लगाया जाता है?', '#', '&', '~ (Tilde)', '!', 'C'),
(130, 'DCA', NULL, 3, 'Which inheritance involves one derived class and more than one base class?\nकिस इनहेरिटेंस में एक डिराइव्ड क्लास और एक से अधिक base क्लास शामिल होती हैं?', 'Multiple Inheritance', 'Multilevel Inheritance', 'Hierarchical Inheritance', 'Single Inheritance', 'A'),
(131, 'DCA', NULL, 3, 'Which inheritance has a chain of classes like Class A -> Class B -> Class C?\nकिस इनहेरिटेंस में क्लासेज की एक चेन होती है जैसे क्लास A -> क्लास B -> क्लास C?', 'Multiple Inheritance', 'Multilevel Inheritance', 'Hierarchical Inheritance', 'Hybrid Inheritance', 'B'),
(132, 'DCA', NULL, 3, 'Which inheritance involves one base class and multiple derived classes?\nकिस इनहेरिटेंस में एक बेस क्लास और कई डिराइव्ड क्लासेज शामिल होती हैं?', 'Multiple Inheritance', 'Multilevel Inheritance', 'Hierarchical Inheritance', 'Single Inheritance', 'C'),
(133, 'DCA', NULL, 3, 'What is the storage size of a \'char\' data type in C++?\nC++ में \'char\' डेटा टाइप का स्टोरेज साइज क्या होता है?', '1 Byte', '2 Bytes', '4 Bytes', '8 Bytes', 'A'),
(134, 'DCA', NULL, 3, 'Which keyword is used to handle exceptions in C++?\nC++ में एक्सेप्शन को हैंडल करने के लिए किस कीवर्ड का उपयोग किया जाता है?', 'try', 'catch', 'throw', 'All of the above', 'D'),
(135, 'DCA', NULL, 3, 'The block that catches and handles an exception is called:\nवह ब्लॉक जो एक्सेप्शन को पकड़ता है और हैंडल करता है, कहलाता है:', 'try block', 'catch block', 'throw block', 'final block', 'B'),
(136, 'DCA', NULL, 3, 'Which keyword is used to explicitly throw an exception?\nएक्सेप्शन को स्पष्ट रूप से थ्रो (सेंड) करने के लिए किस कीवर्ड का उपयोग होता है?', 'try', 'catch', 'throw', 'raise', 'C'),
(137, 'DCA', NULL, 3, 'What is \'this\' pointer in C++?\nC++ में \'this\' पॉइंटर क्या है?', 'A pointer to the base class', 'A pointer pointing to the current object', 'A syntax error', 'A pointer to a global variable', 'B'),
(138, 'DCA', NULL, 3, 'Can a constructor be private in C++?\nक्या C++ में कंस्ट्रक्टर प्राइवेट हो सकता है?', 'Yes', 'No', 'Only inside structures', 'Depends on compiler', 'A'),
(139, 'DCA', NULL, 3, 'Inline functions are used to:\nइनलाइन फंक्शन्स (Inline Functions) का उपयोग किसलिए किया जाता है?', 'Save memory space', 'Reduce function call overhead and save time', 'Hide data', 'Increase security', 'B'),
(140, 'DCA', NULL, 3, 'Which of the following cannot be friend in C++?\nC++ में निम्नलिखित में से कौन \'फ्रेंड\' नहीं हो सकता?', 'A function', 'A class', 'An object', 'An operator', 'C'),
(141, 'DCA', NULL, 3, 'What is the extension of a C++ source code file?\nC++ सोर्स कोड फ़ाइल का एक्सटेंशन क्या होता है?', '.c', '.cpp', '.obj', '.exe', 'B'),
(142, 'DCA', NULL, 3, 'Which operator is used for input stream in C++?\nC++ में इनपुट स्ट्रीम के लिए किस ऑपरेटर (Extraction) का उपयोग होता है?', '<<', '>>', '<', '>', 'B'),
(143, 'DCA', NULL, 3, 'Which operator is used for output stream in C++?\nC++ में आउटपुट स्ट्रीम के लिए किस ऑपरेटर (Insertion) का उपयोग होता है?', '<<', '>>', '<', '>', 'A'),
(144, 'DCA', NULL, 3, 'Static data members are shared by:\nस्टैटिक डेटा मेंबर्स किसके द्वारा साझा (Share) किए जाते हैं?', 'Only one object', 'All objects of that class', 'No object', 'Global functions', 'B'),
(145, 'DCA', NULL, 3, 'What is a template in C++?\nC++ में टेम्पलेट (Template) क्या है?', 'A design layout', 'A feature to write generic functions and classes', 'A file extension', 'A debugging tool', 'B'),
(146, 'DCA', NULL, 3, 'Which keyword is used to define a template?\nटेम्पलेट को परिभाषित करने के लिए किस कीवर्ड का उपयोग किया जाता है?', 'temp', 'template', 'generic', 'class', 'B'),
(147, 'DCA', NULL, 3, 'Which of the following is stream class used for file writing in C++?\nC++ में फ़ाइल राइटिंग (लिखने) के लिए किस स्ट्रीम क्लास का उपयोग होता है?', 'ifstream', 'ofstream', 'fstream', 'iostream', 'B'),
(148, 'DCA', NULL, 3, 'Which stream class is used for file reading in C++?\nC++ में फ़ाइल रीडिंग (पढ़ने) के लिए किस स्ट्रीम क्लास का उपयोग होता है?', 'ifstream', 'ofstream', 'fstream', 'iostream', 'A'),
(149, 'DCA', NULL, 3, 'What is the role of \'endl\' in C++?\nC++ में \'endl\' की क्या भूमिका है?', 'To end the program', 'To insert a new line and flush the stream', 'To declare a variable', 'To exit a loop', 'B'),
(150, 'DCA', NULL, 3, 'Which user-defined data type can contain variables of different data types in C++?\nC++ में कौन सा यूजर-डिफाइंड डेटा टाइप विभिन्न डेटा टाइप्स के वेरिएबल्स रख सकता है?', 'Array', 'Structure / Class', 'Pointer', 'Function', 'B'),
(151, 'DCA', NULL, 3, 'What represents a real-world entity in programming?\nप्रोग्रामिंग में रियल-वर्ल्ड एंटिटी (वास्तविक दुनिया की वस्तु) का प्रतिनिधित्व कौन करता है?', 'Class', 'Object', 'Method', 'Operator', 'B'),
(152, 'DCA', NULL, 3, 'A class is a _________ data type.\nक्लास एक _________ डेटा टाइप है।', 'Primitive', 'User-defined', 'Built-in', 'System', 'B'),
(153, 'DCA', NULL, 3, 'What is a database? / डेटाबेस क्या है?', 'Collection of data', 'Collection of files', 'Collection of software', 'None of these', 'A'),
(154, 'DCA', NULL, 3, 'DBMS stands for? / DBMS का पूर्ण रूप क्या है?', 'Data Base Management System', 'Data Bank Management System', 'Data Base Manual System', 'None', 'A'),
(155, 'DCA', NULL, 3, 'Which is an example of RDBMS? / RDBMS का उदाहरण कौन सा है?', 'MS Access', 'Oracle', 'MySQL', 'All of these', 'D'),
(156, 'DCA', NULL, 3, 'Which database object stores data? / कौन सा डेटाबेस ऑब्जेक्ट डेटा स्टोर करता है?', 'Query', 'Form', 'Table', 'Report', 'C'),
(157, 'DCA', NULL, 3, 'Primary key is used to? / प्राइमरी की का उपयोग क्यों होता है?', 'Delete data', 'Identify records uniquely', 'Sort data', 'Search data', 'B'),
(158, 'DCA', NULL, 3, 'A field that links two tables is? / दो टेबल को जोड़ने वाली फील्ड है?', 'Primary key', 'Foreign key', 'Candidate key', 'Unique key', 'B'),
(159, 'DCA', NULL, 3, 'What is a record in a table? / टेबल में रिकॉर्ड क्या है?', 'Column', 'Row', 'Field', 'Database', 'B'),
(160, 'DCA', NULL, 3, 'What is a field in a table? / टेबल में फील्ड क्या है?', 'Row', 'Column', 'Table', 'Form', 'B'),
(161, 'DCA', NULL, 3, 'Which data type is used for long text? / लंबे टेक्स्ट के लिए कौन सा डेटा टाइप है?', 'Number', 'Memo/Long Text', 'Currency', 'Date', 'B'),
(162, 'DCA', NULL, 3, 'MS Access is a? / MS Access क्या है?', 'Spreadsheet', 'Word Processor', 'RDBMS', 'Presentation tool', 'C'),
(163, 'DCA', NULL, 3, 'What is the default extension of Access 2016+? / Access 2016+ का डिफ़ॉल्ट एक्सटेंशन क्या है?', '.mdb', '.accdb', '.sql', '.db', 'B'),
(164, 'DCA', NULL, 3, 'Which view is used to design table? / टेबल डिजाइन करने के लिए कौन सा व्यू है?', 'Datasheet View', 'Design View', 'Form View', 'Print View', 'B'),
(165, 'DCA', NULL, 3, 'What is a query in Access? / एक्सेस में क्वेरी क्या है?', 'To enter data', 'To ask questions/filter data', 'To print report', 'To create table', 'B'),
(166, 'DCA', NULL, 3, 'Which command is used to get data? / डेटा प्राप्त करने के लिए कौन सा कमांड है?', 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'A'),
(167, 'DCA', NULL, 3, 'Which command adds new data? / नया डेटा जोड़ने के लिए कौन सा कमांड है?', 'INSERT', 'SELECT', 'DROP', 'ALTER', 'A'),
(168, 'DCA', NULL, 3, 'Which command modifies data? / डेटा अपडेट करने के लिए कौन सा कमांड है?', 'SELECT', 'UPDATE', 'INSERT', 'DELETE', 'B'),
(169, 'DCA', NULL, 3, 'Which command removes data? / डेटा हटाने के लिए कौन सा कमांड है?', 'SELECT', 'DELETE', 'UPDATE', 'INSERT', 'B'),
(170, 'DCA', NULL, 3, 'What is normalization? / नॉर्मलाइजेशन क्या है?', 'Increasing data', 'Reducing redundancy', 'Deleting data', 'Sorting data', 'B'),
(171, 'DCA', NULL, 3, 'What does 1NF stand for? / 1NF का अर्थ क्या है?', 'First Normal Form', 'First New Form', 'First Network Form', 'None', 'A'),
(172, 'DCA', NULL, 3, 'Data redundancy means? / डेटा रिडंडेंसी का क्या अर्थ है?', 'Data loss', 'Duplication of data', 'Data update', 'Data storage', 'B'),
(173, 'DCA', NULL, 3, 'What is a form? / फॉर्म क्या है?', 'Data storage', 'User interface for input', 'Report view', 'SQL code', 'B'),
(174, 'DCA', NULL, 3, 'What is a report? / रिपोर्ट क्या है?', 'Input data', 'Output/Print format', 'Table design', 'Database file', 'B'),
(175, 'DCA', NULL, 3, 'Which is a valid SQL command? / कौन सा मान्य SQL कमांड है?', 'ADD', 'APPEND', 'SELECT', 'VIEW', 'C'),
(176, 'DCA', NULL, 3, 'What is a composite key? / कंपोजिट की क्या है?', 'One column key', 'Multi-column key', 'No key', 'Empty key', 'B'),
(177, 'DCA', NULL, 3, 'Null value means? / नल वैल्यू का क्या अर्थ है?', 'Zero', 'Blank/No value', 'Space', 'None', 'B'),
(178, 'DCA', NULL, 3, 'Which operator is used for pattern matching? / पैटर्न मैचिंग के लिए कौन सा ऑपरेटर है?', 'BETWEEN', 'LIKE', 'IN', 'AND', 'B'),
(179, 'DCA', NULL, 3, 'Which operator is used for range? / रेंज के लिए कौन सा ऑपरेटर है?', 'LIKE', 'BETWEEN', 'IN', 'IS', 'B'),
(180, 'DCA', NULL, 3, 'What is DDL? / DDL क्या है?', 'Data Definition Language', 'Data Data Language', 'Define Data List', 'None', 'A'),
(181, 'DCA', NULL, 3, 'Which command is DDL? / कौन सा कमांड DDL है?', 'SELECT', 'INSERT', 'CREATE', 'UPDATE', 'C'),
(182, 'DCA', NULL, 3, 'Which is an aggregate function? / एग्रीगेट फंक्शन कौन सा है?', 'SUM', 'COUNT', 'AVG', 'All of these', 'D'),
(183, 'DCA', NULL, 3, 'SQL stands for? / SQL का फुल फॉर्म है?', 'Structured Query Language', 'Simple Query Language', 'System Query Language', 'None', 'A'),
(184, 'DCA', NULL, 3, 'What is a database object? / डेटाबेस ऑब्जेक्ट क्या है?', 'Table', 'Query', 'Form', 'All of these', 'D'),
(185, 'DCA', NULL, 3, 'What is data? / डेटा क्या है?', 'Information', 'Raw facts', 'Processed data', 'None', 'B'),
(186, 'DCA', NULL, 3, 'Which is not a database object? / कौन सा डेटाबेस ऑब्जेक्ट नहीं है?', 'Table', 'Query', 'Report', 'Excel sheet', 'D'),
(187, 'DCA', NULL, 3, 'Access is a type of? / एक्सेस किस प्रकार का सॉफ्टवेयर है?', 'System Software', 'Application Software', 'Utility Software', 'None', 'B'),
(188, 'DCA', NULL, 3, 'Which key is default? / कौन सा की डिफ़ॉल्ट है?', 'Primary', 'Foreign', 'Candidate', 'Unique', 'A'),
(189, 'DCA', NULL, 3, 'How many views in table design? / टेबल डिजाइन में कितने व्यू हैं?', '1', '2', '3', '4', 'B'),
(190, 'DCA', NULL, 3, 'Database security is for? / डेटाबेस सुरक्षा किसके लिए है?', 'Data privacy', 'Data loss', 'Data entry', 'None', 'A'),
(191, 'DCA', NULL, 3, 'What is a schema? / स्कीमा क्या है?', 'Table structure', 'Data inside table', 'Database name', 'None', 'A'),
(192, 'DCA', NULL, 3, 'What is a view? / व्यू क्या है?', 'Virtual table', 'Real table', 'Form', 'Report', 'A'),
(193, 'DCA', NULL, 3, 'Which is used for sorting? / सॉर्टिंग के लिए क्या उपयोग होता है?', 'ORDER BY', 'GROUP BY', 'HAVING', 'SELECT', 'A'),
(194, 'DCA', NULL, 3, 'Group by is used for? / ग्रुप बाय का उपयोग?', 'Grouping records', 'Sorting', 'Deleting', 'Updating', 'A'),
(195, 'DCA', NULL, 3, 'Having clause is used after? / हैविंग क्लॉज किसके बाद आता है?', 'SELECT', 'WHERE', 'GROUP BY', 'ORDER BY', 'C'),
(196, 'DCA', NULL, 3, 'What is DML? / DML क्या है?', 'Data Manipulation Language', 'Data Make Language', 'Data Management Language', 'None', 'A'),
(197, 'DCA', NULL, 3, 'Which is DML? / कौन सा DML है?', 'CREATE', 'DROP', 'INSERT', 'ALTER', 'C'),
(198, 'DCA', NULL, 3, 'What is a tuple? / टपल क्या है?', 'Table', 'Row', 'Column', 'Database', 'B'),
(199, 'DCA', NULL, 3, 'What is an attribute? / एट्रिब्यूट क्या है?', 'Column', 'Row', 'Table', 'None', 'A'),
(200, 'DCA', NULL, 3, 'Access stores data in? / एक्सेस डेटा कहाँ स्टोर करता है?', 'Rows/Columns', 'Text files', 'Images', 'None', 'A'),
(201, 'DCA', NULL, 3, 'Database size limit in Access? / एक्सेस में डेटाबेस सीमा?', '1 GB', '2 GB', '4 GB', 'No limit', 'B'),
(202, 'DCA', NULL, 3, 'Which is used for calculation? / गणना के लिए क्या उपयोग होता है?', 'Expression builder', 'Table', 'Form', 'None', 'A'),
(203, 'DCA', NULL, 4, 'Which of the following best describes the core concept of Encapsulation in C++ programming? / C++ प्रोग्रामिंग में एनकैप्सुलेशन (Encapsulation) की अवधारणा को कौन सा विकल्प सबसे बेहतर तरीके से समझाता है?', 'Combining data and functions into a single unit called class', 'Inheriting properties from a base class to a derived class', 'Creating multiple forms of a single function or operator', 'Hiding the internal complexity of code from the user', 'A'),
(204, 'DCA', NULL, 4, 'What is the primary difference between a constructor and a standard member function in C++? / C++ में कंस्ट्रक्टर और एक सामान्य सदस्य फंक्शन के बीच प्राथमिक अंतर क्या है?', 'Constructor has the same name as the class and no return type', 'Constructor is called only when explicitly invoked by the user', 'Standard member function is always declared in the private section', 'Constructor can return a value but member function cannot', 'A'),
(205, 'DCA', NULL, 4, 'In the context of Inheritance, what does a \"protected\" access specifier allow that \"private\" does not? / इनहेरिटेंस के संदर्भ में, प्रोटेक्टेड (protected) एक्सेस स्पेसिफायर वह क्या करने की अनुमति देता है जो प्राइवेट (private) नहीं देता?', 'It allows access to derived classes but keeps members hidden from outside', 'It makes members completely public to the entire program', 'It allows global access to all functions in the system', 'It prevents all access, including from the base class itself', 'A'),
(206, 'DCA', NULL, 4, 'What is the specific purpose of the \"virtual\" keyword in C++ when applied to a member function? / C++ में मेंबर फंक्शन पर \"virtual\" कीवर्ड का उपयोग करने का विशिष्ट उद्देश्य क्या है?', 'To enable dynamic binding and achieve Runtime Polymorphism', 'To restrict access to the function from other classes', 'To make the function execute faster than normal functions', 'To ensure that the function can only be called from the main block', 'A'),
(207, 'DCA', NULL, 4, 'How does Method Overloading differ from Method Overriding in an Object-Oriented environment? / ऑब्जेक्ट-ओरिएंटेड वातावरण में मेथड ओवरलोडिंग, मेथड ओवरराइडिंग से कैसे भिन्न है?', 'Overloading is compile-time polymorphism; Overriding is runtime polymorphism', 'Overloading occurs between different classes; Overriding within the same class', 'Overloading requires virtual functions; Overriding does not', 'There is no fundamental difference between the two', 'A'),
(208, 'DCA', NULL, 4, 'What exactly happens when you declare a function as \"inline\" in C++? / C++ में किसी फंक्शन को \"inline\" घोषित करने पर वास्तव में क्या होता है?', 'The compiler suggests replacing the function call with the function body to reduce overhead', 'The function is executed only when the program is compiled', 'The function is forced to be private and cannot be accessed by other classes', 'The function is stored in a separate memory segment to save space', 'A'),
(209, 'DCA', NULL, 4, 'What is the function of the \"Scope Resolution Operator (::)\" when used with a class name and a member function? / जब स्कोप रेजोल्यूशन ऑपरेटर (::) का उपयोग क्लास के नाम और मेंबर फंक्शन के साथ किया जाता है, तो इसका क्या कार्य होता है?', 'To define a member function outside the scope of the class definition', 'To call a private function from the main() method', 'To convert a standard function into a virtual function', 'To allocate memory dynamically for the class object', 'A'),
(210, 'DCA', NULL, 4, 'Why is a Destructor essential in C++ classes that utilize dynamic memory allocation? / डायनामिक मेमोरी एलोकेशन का उपयोग करने वाली C++ क्लास में डिस्ट्रक्टर क्यों आवश्यक है?', 'To deallocate the memory and prevent memory leaks when the object is destroyed', 'To initialize the object variables with default values during creation', 'To allow the object to be copied to another memory location', 'To define the rules for accessing private members of the class', 'A'),
(211, 'DCA', NULL, 4, 'What is the specific role of a Friend Function in C++ regarding class access? / C++ में फ्रेंड फंक्शन का क्लास एक्सेस के संदर्भ में विशिष्ट कार्य क्या है?', 'It can access the private and protected members of a class despite not being a member', 'It inherits all properties of the class without needing to be a derived class', 'It is a special type of constructor that initializes static members', 'It prevents other functions from accessing the private data of the class', 'A'),
(212, 'DCA', NULL, 4, 'What is the mechanism of \"Dynamic Binding\" (or Late Binding) in C++? / C++ में \"डायनामिक बाइंडिंग\" (या लेट बाइंडिंग) की कार्यप्रणाली क्या है?', 'The function to be called is determined at runtime, usually via virtual functions', 'The compiler determines the function call during the compilation phase', 'Memory is allocated for the object only when the program terminates', 'The data members are hidden and cannot be accessed from outside the class', 'A'),
(213, 'DCA', NULL, 4, 'When is a \"Copy Constructor\" automatically called by the C++ compiler? / C++ कंपाइलर द्वारा \"कॉपी कंस्ट्रक्टर\" अपने आप कब कॉल किया जाता है?', 'When an object is passed by value to a function or initialized with another object', 'When a new object of the class is created using the new operator', 'When a member function is called from the main() method', 'When the object goes out of scope and the program ends', 'A'),
(214, 'DCA', NULL, 4, 'What is the primary characteristic of an \"Abstract Class\" in C++? / C++ में एक \"एब्सट्रैक्ट क्लास\" की मुख्य विशेषता क्या है?', 'It contains at least one pure virtual function and cannot be instantiated', 'It has no data members and only public methods', 'It is a final class that cannot be inherited by any other class', 'It is a class where all methods are defined as inline', 'A'),
(215, 'DCA', NULL, 4, 'What does the \"this\" pointer represent inside a non-static member function? / नॉन-स्टैटिक मेंबर फंक्शन के अंदर \"this\" पॉइंटर क्या दर्शाता है?', 'It holds the memory address of the current object that invoked the function', 'It is a pointer to the parent class of the current object', 'It acts as a reference to the global variable in the program', 'It points to the memory location where the function code is stored', 'A'),
(216, 'DCA', NULL, 4, 'How does C++ handle exceptions using the \"try\", \"catch\", and \"throw\" mechanism? / C++ में \"try\", \"catch\", और \"throw\" तंत्र का उपयोग करके अपवाद (Exceptions) को कैसे हैंडल किया जाता है?', 'Code that might cause an error is placed in try, an error is triggered by throw, and handled in catch', 'Throw defines the error, catch executes the code, and try terminates the program', 'Try is used for initialization, throw for cleanup, and catch for output', 'Exceptions in C++ cannot be handled and will cause the program to crash', 'A'),
(217, 'DCA', NULL, 4, 'What is the difference between \"new\" and \"malloc\" for memory allocation in C++? / C++ में मेमोरी एलोकेशन के लिए \"new\" और \"malloc\" के बीच मुख्य अंतर क्या है?', 'New calls the constructor and returns the correct type, malloc does not', 'Malloc is faster than new because it does not use constructors', 'New is a C library function, while malloc is a C++ operator', 'There is no difference, both function identically', 'A'),
(218, 'DCA', NULL, 4, 'What is the function of a \"Static Member Variable\" in a class? / क्लास में \"स्टैटिक मेंबर वेरिएबल\" का कार्य क्या होता है?', 'It is shared among all objects of the class; only one copy exists in memory', 'It is created separately for every single object of the class', 'It is a private variable that cannot be accessed by any other class', 'It can only be accessed by the friend functions of that class', 'A'),
(219, 'DCA', NULL, 4, 'In C++, what does a \"Namespace\" provide for large programs? / C++ में बड़े प्रोग्राम के लिए \"नेमस्पेस\" क्या प्रदान करता है?', 'A way to organize code into logical groups to prevent name collisions', 'A mechanism to automatically compile the code without errors', 'A method to compress the size of the final executable file', 'A way to limit the amount of memory a program can use', 'A'),
(220, 'DCA', NULL, 4, 'Which of the following describes \"Multiple Inheritance\" in C++? / निम्नलिखित में से कौन सा C++ में \"मल्टीपल इनहेरिटेंस\" का वर्णन करता है?', 'A derived class can inherit properties from more than one base class', 'A base class can have multiple child classes simultaneously', 'One class inherits from another class which in turn inherits from another', 'A class can have multiple instances in the main program', 'A'),
(221, 'DCA', NULL, 4, 'What is the primary difference between a \"Struct\" and a \"Class\" in C++? / C++ में \"स्ट्रक्ट\" (Struct) और \"क्लास\" (Class) के बीच प्राथमिक अंतर क्या है?', 'Default access specifier in struct is public, whereas in class it is private', 'Class supports inheritance, but struct does not', 'Struct cannot have member functions, while class can', 'There is no difference between them', 'A'),
(222, 'DCA', NULL, 4, 'What is the purpose of the \"explicit\" keyword in a C++ constructor? / C++ कंस्ट्रक्टर में \"explicit\" कीवर्ड का उद्देश्य क्या है?', 'To prevent the compiler from performing implicit type conversions', 'To force the constructor to be public in the class', 'To make the constructor run only once during the program', 'To allow the constructor to return a value to the caller', 'A'),
(223, 'DCA', NULL, 4, 'What is a \"Pure Virtual Function\" in C++? / C++ में \"प्योर वर्चुअल फंक्शन\" क्या है?', 'A virtual function assigned to zero, which forces derived classes to override it', 'A function that executes faster because it is defined in the base class', 'A function that cannot be called even from the base class pointer', 'A virtual function that is only available in the private section', 'A'),
(224, 'DCA', NULL, 4, 'What is \"Operator Overloading\" in C++? / C++ में \"ऑपरेटर ओवरलोडिंग\" क्या है?', 'Defining new meanings for existing operators when used with user-defined types', 'Changing the operator precedence of built-in types like int and char', 'Creating completely new symbols for arithmetic operations', 'Replacing a standard function with a mathematical operator', 'A'),
(225, 'DCA', NULL, 4, 'What is the role of an \"Access Specifier\" (Public, Private, Protected)? / \"एक्सेस स्पेसिफायर\" (पब्लिक, प्राइवेट, प्रोटेक्टेड) की भूमिका क्या है?', 'To control the visibility and accessibility of class members', 'To determine the amount of memory allocated to a member', 'To specify the order in which functions are executed', 'To define the lifetime of an object in the program', 'A'),
(226, 'DCA', NULL, 4, 'What is \"Dynamic Memory Allocation\"? / \"डायनामिक मेमोरी एलोकेशन\" क्या है?', 'Allocating memory to variables during program runtime instead of compile time', 'Defining the size of all arrays at the beginning of the program', 'Allocating memory to functions only when they are called', 'Cleaning up the memory after the program has finished running', 'A'),
(227, 'DCA', NULL, 4, 'What does the \"Const\" keyword mean when used with a member function? / मेंबर फंक्शन के साथ \"Const\" कीवर्ड का उपयोग करने का क्या अर्थ है?', 'The function will not modify any data members of the class', 'The function is not allowed to call any other functions', 'The function returns a constant value that cannot be changed', 'The function can only be called from other constant functions', 'A'),
(228, 'DCA', NULL, 4, 'What is the \"Function Signature\" in C++? / C++ में \"फंक्शन सिग्नेचर\" क्या है?', 'The combination of function name and the number/types of its parameters', 'The return type of the function and its visibility specifier', 'The memory address where the function starts in the code', 'The total number of bytes the function occupies in memory', 'A'),
(229, 'DCA', NULL, 4, 'What is the result of applying the \"Sizeof\" operator to a class object? / किसी क्लास ऑब्जेक्ट पर \"Sizeof\" ऑपरेटर लागू करने का परिणाम क्या होता है?', 'It returns the total number of bytes occupied by all data members of the class', 'It returns the number of member functions defined in the class', 'It returns the memory address where the object is stored', 'It returns the number of times the object has been used in the program', 'A'),
(230, 'DCA', NULL, 4, 'In C++, what is a \"Reference Variable\"? / C++ में \"रेफरेंस वेरिएबल\" क्या है?', 'An alternative name or an alias for an existing variable', 'A special pointer that holds the address of a function', 'A variable that is permanently stored in the read-only memory', 'A constant value that cannot be changed after initialization', 'A'),
(231, 'DCA', NULL, 4, 'What does a \"Header File\" typically contain in C++? / C++ में \"हेडर फाइल\" में आमतौर पर क्या होता है?', 'Declarations of functions, classes, and constants for reuse', 'Compiled machine code for the entire program', 'The main entry point of the application execution', 'Binary data that cannot be read by the programmer', 'A'),
(232, 'DCA', NULL, 4, 'What is \"Type Casting\" in C++? / C++ में \"टाइप कास्टिंग\" क्या है?', 'The process of converting a variable from one data type to another', 'The process of deleting a data type from memory', 'The process of renaming a class or variable', 'The process of creating a new custom data type', 'A'),
(233, 'DCA', NULL, 4, 'Which statement correctly defines \"Polymorphism\"? / कौन सा कथन \"पॉलीमॉर्फिज्म\" को सही ढंग से परिभाषित करता है?', 'The ability of a message or function to be displayed in more than one form', 'The ability to hide internal details of the code from the user', 'The ability to reuse code by inheriting from a base class', 'The ability to prevent other classes from accessing private data', 'A');
INSERT INTO `questions` (`id`, `course`, `subject_name`, `subject_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(234, 'DCA', NULL, 4, 'What is the purpose of the \"std\" namespace in C++? / C++ में \"std\" नेमस्पेस का उद्देश्य क्या है?', 'It contains the standard C++ library functions and classes', 'It defines the syntax for the main function', 'It is a required keyword to start the compiler', 'It helps in optimizing the memory for arrays', 'A'),
(235, 'DCA', NULL, 4, 'What happens to a local object when its function finishes execution? / जब कोई फंक्शन अपनी एक्जीक्यूशन समाप्त करता है, तो लोकल ऑब्जेक्ट का क्या होता है?', 'The object is destroyed, and its destructor is automatically called', 'The object is moved to the global memory space', 'The object remains in memory until the program terminates', 'The object is saved in a temporary file on the disk', 'A'),
(236, 'DCA', NULL, 4, 'What is a \"Static Member Function\" in C++? / C++ में \"स्टैटिक मेंबर फंक्शन\" क्या है?', 'A function that can be called without creating an object of the class', 'A function that is private to all classes', 'A function that must be virtual', 'A function that only processes static variables', 'A'),
(237, 'DCA', NULL, 4, 'Why is \"Pass by Reference\" preferred over \"Pass by Value\" for large objects? / बड़े ऑब्जेक्ट्स के लिए \"पास बाय रेफरेंस\" को \"पास बाय वैल्यू\" से बेहतर क्यों माना जाता है?', 'It avoids the overhead of copying the entire object data', 'It makes the code execute faster by using pointers', 'It provides security to the class data', 'It is easier to write the code for pass by reference', 'A'),
(238, 'DCA', NULL, 4, 'What is the \"Base Class\" in an inheritance relationship? / इनहेरिटेंस संबंध में \"बेस क्लास\" क्या है?', 'The class from which other classes derive their properties', 'The class that is derived from another class', 'A class that contains only static members', 'A class that cannot be used for inheritance', 'A'),
(239, 'DCA', NULL, 4, 'What is the \"Derived Class\" in an inheritance relationship? / इनहेरिटेंस संबंध में \"डिराइव्ड क्लास\" क्या है?', 'A class that inherits properties from a base class', 'The class that defines the main structure of the program', 'A class that contains only private methods', 'A class that acts as the entry point for the program', 'A'),
(240, 'DCA', NULL, 4, 'How are \"Templates\" useful in C++ programming? / C++ प्रोग्रामिंग में \"टेम्प्लेट\" कैसे उपयोगी हैं?', 'They allow writing generic code that works with any data type', 'They help in organizing files in the directory', 'They are used to create graphical user interfaces', 'They automatically handle memory management', 'A'),
(241, 'DCA', NULL, 4, 'What is the difference between \"++i\" and \"i++\" operators? / \"++i\" और \"i++\" ऑपरेटरों के बीच क्या अंतर है?', '++i increments before use, i++ increments after use', 'i++ is faster than ++i in all cases', '++i is only for floats, i++ is only for integers', 'There is no difference in their output', 'A'),
(242, 'DCA', NULL, 4, 'What is the use of the \"fstream\" library in C++? / C++ में \"fstream\" लाइब्रेरी का क्या उपयोग है?', 'To perform file input and output operations', 'To manage computer memory', 'To display graphics on the screen', 'To compile the program code', 'A'),
(243, 'DCA', NULL, 4, 'What does the \"main()\" function do in a C++ program? / C++ प्रोग्राम में \"main()\" फंक्शन क्या करता है?', 'It is the starting point from where program execution begins', 'It is a function that contains all the private data of the class', 'It is used to declare all global variables', 'It is automatically called after the program ends', 'A'),
(244, 'DCA', NULL, 4, 'What is a \"Pointer\" in C++? / C++ में \"पॉइंटर\" क्या है?', 'A variable that stores the memory address of another variable', 'A variable that stores the actual data value', 'A function that executes other functions', 'A class that manages objects', 'A'),
(245, 'DCA', NULL, 4, 'What is \"Data Abstraction\"? / \"डेटा एब्सट्रैक्शन\" क्या है?', 'The practice of showing essential features while hiding implementation details', 'The practice of creating complex code structures', 'The process of renaming variables to make code harder to read', 'The process of compiling code into machine language', 'A'),
(246, 'DCA', NULL, 4, 'What happens if you try to access a private member from outside the class? / यदि आप क्लास के बाहर से प्राइवेट मेंबर को एक्सेस करने का प्रयास करते हैं तो क्या होता है?', 'The compiler generates an error', 'The program crashes immediately', 'The program runs but gives incorrect results', 'The compiler skips that line', 'A'),
(247, 'DCA', NULL, 4, 'What is the purpose of the \"new\" operator? / \"new\" ऑपरेटर का उद्देश्य क्या है?', 'To allocate memory for an object dynamically', 'To create a new class definition', 'To rename an existing variable', 'To terminate the execution of the program', 'A'),
(248, 'DCA', NULL, 4, 'What is a \"Constructor\" in C++? / C++ में \"कंस्ट्रक्टर\" क्या है?', 'A special member function called when an object is created', 'A function that deletes objects from memory', 'A variable that stores class state', 'A library function for math operations', 'A'),
(249, 'DCA', NULL, 4, 'What is \"Compile-time Polymorphism\"? / \"कंपाइल-टाइम पॉलीमॉर्फिज्म\" क्या है?', 'Method overloading or operator overloading determined during compilation', 'Polymorphism that is determined during program execution', 'A process that happens at the very end of the program', 'A feature that only works with virtual functions', 'A'),
(250, 'DCA', NULL, 4, 'Can we have multiple constructors in a single class? / क्या एक ही क्लास में कई कंस्ट्रक्टर हो सकते हैं?', 'Yes, this is called constructor overloading', 'No, only one is allowed', 'Only if they have different names', 'Only if the class is inherited', 'A'),
(251, 'DCA', NULL, 4, 'What is \"Method Overriding\"? / \"मेथड ओवरराइडिंग\" क्या है?', 'Providing a new implementation for a base class method in a derived class', 'Changing the name of a method in the derived class', 'Adding a new method to a class', 'Calling a function from a different program', 'A'),
(252, 'DCA', NULL, 4, 'What is the role of the \"protected\" specifier in inheritance? / इनहेरिटेंस में \"protected\" स्पेसिफायर की भूमिका क्या है?', 'Members are accessible within the class and in derived classes', 'Members are completely hidden from all classes', 'Members are public to all parts of the program', 'Members are private to the base class only', 'A'),
(253, 'DCA', NULL, 5, 'Which of the following best describes the process of effective communication in a professional environment? / पेशेवर वातावरण में प्रभावी संचार की प्रक्रिया का सबसे अच्छा वर्णन कौन सा है?', 'The exchange of information, ideas, and feelings between a sender and a receiver through a medium to achieve a mutual understanding', 'The process of strictly following the company hierarchy to deliver orders from top management to lower-level employees without feedback', 'A one-way transmission of data where the sender provides information and the receiver is not required to provide any response', 'The act of using complex technical vocabulary and jargon to impress colleagues and superiors during official meetings', 'A'),
(254, 'DCA', NULL, 5, 'How does \"Non-Verbal Communication\" play a critical role in shaping a person’s personality and professional image? / किसी व्यक्ति के व्यक्तित्व और पेशेवर छवि को आकार देने में \"गैर-मौखिक संचार\" (Non-Verbal Communication) कैसे महत्वपूर्ण भूमिका निभाता है?', 'Body language, facial expressions, and posture often convey more meaning and sincerity than the actual words spoken in a conversation', 'It serves no purpose as professional communication is entirely dependent on written documents and official emails sent to clients', 'It only involves the use of high-quality formal clothing and grooming standards to present oneself effectively to others', 'It is a form of communication that should be avoided in professional settings to maintain a strict and neutral personality', 'A'),
(255, 'DCA', NULL, 5, 'What is the most effective strategy to overcome \"Communication Barriers\" such as semantic or language differences? / \"संचार बाधाओं\" (Communication Barriers) जैसे कि भाषाई अंतर को दूर करने के लिए सबसे प्रभावी रणनीति क्या है?', 'Using simple, clear, and unambiguous language while ensuring active listening and seeking feedback to verify the receiver understood the message', 'Ignoring the language differences and expecting the receiver to adapt to the sender’s preferred style of speaking and vocabulary', 'Translating all communication into multiple regional languages regardless of the receiver’s actual proficiency in those languages', 'Relying solely on written reports and avoiding face-to-face interaction to prevent the risk of verbal misunderstandings', 'A'),
(256, 'DCA', NULL, 5, 'Which of the following is considered an essential trait of a \"Positive Personality\" in a workplace setting? / कार्यस्थल के माहौल में \"सकारात्मक व्यक्तित्व\" (Positive Personality) का एक अनिवार्य गुण किसे माना जाता है?', 'Maintaining an optimistic attitude, showing resilience during challenges, and actively supporting team members to achieve collective goals', 'Prioritizing individual success above all else and constantly criticizing the performance of others to improve one’s own standing', 'Avoiding all forms of social interaction and focusing exclusively on completing assigned tasks in isolation without any collaboration', 'Strictly adhering to traditional methods and refusing to accept any form of change or new technology in the work process', 'A'),
(257, 'DCA', NULL, 5, 'What does the concept of \"Active Listening\" imply in the communication process? / संचार प्रक्रिया में \"सक्रिय श्रवण\" (Active Listening) की अवधारणा का क्या अर्थ है?', 'The listener fully concentrates, understands, responds, and remembers what the speaker is saying without interrupting or jumping to conclusions', 'The listener waits for the speaker to pause so they can quickly insert their own opinion and change the direction of the topic', 'The listener performs other tasks like checking emails or taking notes while the speaker is talking to save time', 'The listener nods occasionally while planning what they want to say next instead of focusing on the speaker’s actual message', 'A'),
(258, 'DCA', NULL, 5, 'How can \"Assertiveness\" be distinguished from \"Aggressiveness\" in interpersonal communication? / पारस्परिक संचार में \"मुखरता\" (Assertiveness) को \"आक्रामकता\" (Aggressiveness) से कैसे अलग किया जा सकता है?', 'Assertiveness involves standing up for one’s rights with respect for others, whereas aggressiveness involves ignoring the rights and feelings of others', 'Assertiveness is a sign of weakness and lack of confidence, whereas aggressiveness is the only way to get things done in a competitive office', 'Assertiveness means shouting loudly to be heard, while aggressiveness involves staying silent and hoping others will guess your needs', 'There is no real difference, as both are synonyms for being loud and assertive during professional discussions and presentations', 'A'),
(259, 'DCA', NULL, 5, 'What is the role of \"Feedback\" in ensuring the success of a communication cycle? / एक संचार चक्र की सफलता सुनिश्चित करने में \"फीडबैक\" की क्या भूमिका है?', 'It confirms whether the message was received, understood, and interpreted by the receiver in the same way the sender intended', 'It is an optional part of communication that is only necessary if the sender is not confident about their message delivery', 'It serves as a tool to criticize the receiver for not paying attention to the details provided in the original message', 'It is the final stage that allows the sender to end the conversation and move on to the next task without further interaction', 'A'),
(260, 'DCA', NULL, 5, 'Which factor is most influential in building a \"Professional Image\" during the initial phase of an interview? / साक्षात्कार के प्रारंभिक चरण में \"पेशेवर छवि\" (Professional Image) बनाने में कौन सा कारक सबसे अधिक प्रभावशाली है?', 'The combination of appropriate formal attire, confident body language, punctuality, and the ability to articulate ideas clearly', 'The ability to speak constantly for long durations without giving the interviewer any chance to ask follow-up questions', 'Using highly sophisticated and rare words to demonstrate an extensive vocabulary and advanced language proficiency', 'Being extremely friendly and sharing personal life stories to build a rapport with the interviewer quickly', 'A'),
(261, 'DCA', NULL, 5, 'What is the importance of \"Emotional Intelligence\" in effective communication? / प्रभावी संचार में \"भावनात्मक बुद्धिमत्ता\" (Emotional Intelligence) का महत्व क्या है?', 'It enables an individual to recognize, understand, and manage their own emotions and influence the emotions of others positively', 'It means suppressing all feelings completely to act like a machine and avoid any personal bias in professional decisions', 'It is the ability to cry or express sadness openly in the office to make colleagues feel more sympathetic towards you', 'It relates to the ability to avoid working with people who have different temperaments or emotional backgrounds', 'A'),
(262, 'DCA', NULL, 5, 'How should one approach \"Conflict Management\" when there is a disagreement in a team? / टीम में असहमति होने पर \"संघर्ष प्रबंधन\" (Conflict Management) के प्रति क्या दृष्टिकोण होना चाहिए?', 'Focus on the problem rather than the person, encourage open dialogue, and seek a collaborative solution that satisfies all parties', 'Identify who is at fault and publicly blame them to ensure that such mistakes do not happen in the future', 'Avoid the conflict entirely by staying silent and accepting the majority opinion regardless of whether it is correct or not', 'Escalate the issue to the highest management immediately without trying to resolve it within the team structure', 'A'),
(263, 'DCA', NULL, 5, 'Which communication channel is best suited for complex or sensitive professional messages? / जटिल या संवेदनशील व्यावसायिक संदेशों के लिए कौन सा संचार चैनल सबसे उपयुक्त है?', 'Face-to-face communication or a video conference to ensure tone, intent, and clarity can be verified immediately', 'Sending a short instant message or a tweet to ensure the message is delivered as quickly as possible', 'Posting the information on a public social media platform to gather opinions from people outside the organization', 'Writing an anonymous note and leaving it on the desk of the concerned person to avoid direct confrontation', 'A'),
(264, 'DCA', NULL, 5, 'What is the \"7Cs of Communication\" principle mainly used for in business writing? / व्यावसायिक लेखन में \"संचार के 7Cs\" (7Cs of Communication) सिद्धांत का मुख्य उपयोग क्या है?', 'To ensure communication is Clear, Concise, Concrete, Correct, Coherent, Complete, and Courteous', 'To ensure that the letter or report is at least seven pages long to look professional and informative', 'To categorize different types of employees into seven distinct personality groups based on their communication styles', 'To provide a framework for creating seven different versions of the same document for different departments', 'A'),
(265, 'DCA', NULL, 5, 'Why is \"Self-Confidence\" a vital component of personality development? / व्यक्तित्व विकास में \"आत्मविश्वास\" (Self-Confidence) एक महत्वपूर्ण घटक क्यों है?', 'It allows individuals to take risks, face challenges, and communicate their ideas without fear of judgment', 'It helps individuals to dominate every conversation and ensure that they are the only ones speaking in a group', 'It ensures that the individual never makes a mistake because they believe they are always correct', 'It is not really important, as technical skills are the only thing required for professional growth in any career', 'A'),
(266, 'DCA', NULL, 5, 'How does \"Time Management\" contribute to the overall personality of a professional? / \"समय प्रबंधन\" (Time Management) एक पेशेवर के समग्र व्यक्तित्व में कैसे योगदान देता है?', 'It demonstrates discipline, reliability, and the ability to prioritize tasks, which are signs of a highly professional personality', 'It involves working for 18 hours a day to prove that the employee is more hardworking than others', 'It is simply about using a calendar app to record meetings and ignoring the actual deadline of projects', 'It has no relation to personality; it is just a tool used by managers to monitor employee activity', 'A'),
(267, 'DCA', NULL, 5, 'What is the significance of \"Adaptability\" in the modern workplace? / आधुनिक कार्यस्थल में \"अनुकूलनशीलता\" (Adaptability) का क्या महत्व है?', 'The ability to adjust to new situations, technologies, and changes in the work environment to remain effective', 'The tendency to reject all new changes and strictly stick to old, familiar ways of doing work', 'The skill of convincing others that the old way is better and resisting any form of organizational change', 'The practice of moving from one company to another every few months to gain more experience', 'A'),
(268, 'DCA', NULL, 5, 'Which of the following is an example of an \"Open-Ended Question\"? / निम्नलिखित में से कौन सा एक \"ओपन-एंडेड प्रश्न\" (Open-Ended Question) का उदाहरण है?', 'How would you describe your experience with this project and what challenges did you face?', 'Is this project completed or are you still working on it?', 'Did you finish the task I assigned to you yesterday?', 'Are you happy with the current results of our team?', 'A'),
(269, 'DCA', NULL, 5, 'What is the purpose of \"Body Language\" during a formal presentation? / एक औपचारिक प्रस्तुति (Presentation) के दौरान \"बॉडी लैंग्वेज\" का उद्देश्य क्या है?', 'To reinforce the spoken words, show confidence, and keep the audience engaged through eye contact and gestures', 'To hide the speaker’s nervousness by keeping hands in pockets and looking at the floor', 'To make the presentation look longer by incorporating complex dance-like movements', 'To show the audience that the speaker is not really interested in the topic being presented', 'A'),
(270, 'DCA', NULL, 5, 'How does \"Constructive Criticism\" help in professional growth? / \"रचनात्मक आलोचना\" (Constructive Criticism) पेशेवर विकास में कैसे मदद करती है?', 'It identifies areas for improvement and provides actionable feedback to enhance performance and skills', 'It is a way to discourage an employee and force them to leave their current position', 'It is used to highlight every single minor mistake of an employee to lower their morale', 'It should be ignored because one’s own method of working is always perfect and doesn’t need change', 'A'),
(271, 'DCA', NULL, 5, 'What is the role of \"Empathy\" in client relationship management? / क्लाइंट संबंध प्रबंधन में \"सहानुभूति\" (Empathy) की क्या भूमिका है?', 'To understand the client’s perspective and needs, which helps in building trust and long-term partnerships', 'To agree with everything the client says, even if it is harmful to the company’s goals', 'To provide free services to the client to ensure they never complain about the costs', 'To focus on selling more products to the client regardless of whether they need them or not', 'A'),
(272, 'DCA', NULL, 5, 'What does \"Grooming\" imply in the context of personality development? / व्यक्तित्व विकास के संदर्भ में \"ग्रूमिंग\" (Grooming) का क्या अर्थ है?', 'Maintaining a neat, clean, and professional appearance that reflects one’s self-respect and respect for others', 'Spending a lot of money on expensive branded clothes and luxury accessories every single day', 'Changing one’s physical appearance to look like a famous celebrity or public figure', 'Not caring about appearance at all because talent is the only thing that matters in a professional life', 'A'),
(273, 'DCA', NULL, 5, 'Why is \"Clarity\" crucial in written communication? / लिखित संचार में \"स्पष्टता\" (Clarity) क्यों महत्वपूर्ण है?', 'It eliminates confusion and ensures the reader understands the exact message without needing clarification', 'It allows the writer to use as many words as possible to fill up the space in the report', 'It helps the writer to hide the main point of the message in the middle of a long paragraph', 'It ensures that only the management can read the document, keeping it hidden from others', 'A'),
(274, 'DCA', NULL, 5, 'What is the best way to handle \"Public Speaking Anxiety\"? / \"सार्वजनिक बोलने की चिंता\" (Public Speaking Anxiety) को संभालने का सबसे अच्छा तरीका क्या है?', 'Thorough preparation, practicing in front of a mirror, and focusing on the message rather than the fear', 'Avoiding all public speaking opportunities for the rest of one’s career', 'Drinking caffeine before the speech to stay energized and talk faster to finish early', 'Memorizing the entire speech word-for-word so that there is no chance of making a mistake', 'A'),
(275, 'DCA', NULL, 5, 'What is the significance of \"Ethics\" in communication? / संचार में \"नैतिकता\" (Ethics) का क्या महत्व है?', 'It ensures honesty, transparency, and fairness in all professional interactions', 'It means only sharing information that makes the company look good, even if it is false', 'It involves keeping all information secret from team members to maintain control', 'It is an optional practice that does not affect professional reputation in the long run', 'A'),
(276, 'DCA', NULL, 5, 'How can \"Interpersonal Skills\" benefit a team leader? / \"पारस्परिक कौशल\" (Interpersonal Skills) एक टीम लीडर को कैसे लाभ पहुँचा सकते हैं?', 'They help in motivating team members, resolving conflicts, and creating a positive work environment', 'They allow the leader to manipulate team members to work extra hours without pay', 'They are only useful for socializing during office parties and not for actual work', 'They are a distraction because a leader should only focus on tasks and not on people', 'A'),
(277, 'DCA', NULL, 5, 'What is the importance of \"Punctuality\" in professional ethics? / व्यावसायिक नैतिकता में \"समयबद्धता\" (Punctuality) का क्या महत्व है?', 'It reflects reliability, respect for others’ time, and professional discipline', 'It is not important as long as the work is completed eventually, regardless of the deadline', 'It is only for junior employees and not for senior management', 'It is a sign that the employee does not have much work to do during the day', 'A'),
(278, 'DCA', NULL, 5, 'What is \"Horizontal Communication\"? / \"क्षैतिज संचार\" (Horizontal Communication) क्या है?', 'Communication between colleagues or departments at the same organizational level', 'Communication from the CEO to the junior staff members', 'Communication from the junior staff to the top management', 'Communication with external clients and stakeholders outside the company', 'A'),
(279, 'DCA', NULL, 5, 'How does \"Positive Attitude\" influence team morale? / \"सकारात्मक दृष्टिकोण\" (Positive Attitude) टीम के मनोबल को कैसे प्रभावित करता है?', 'It inspires confidence, encourages others, and creates an environment where everyone feels motivated to succeed', 'It has no effect because employees are only motivated by their salary and bonuses', 'It is irritating to other employees who prefer to be realistic and focused on problems', 'It makes the team complacent and less focused on meeting the targets and deadlines', 'A'),
(280, 'DCA', NULL, 5, 'What is the role of \"Vocabulary\" in effective communication? / प्रभावी संचार में \"शब्दावली\" (Vocabulary) की क्या भूमिका है?', 'It helps in expressing ideas accurately and precisely to ensure the message is understood', 'It is used only to show off and confuse people during meetings', 'It has no importance as long as you speak confidently, even if you don’t use the right words', 'It is a hindrance because simple language is always better regardless of the context', 'A'),
(281, 'DCA', NULL, 5, 'What does \"Visual Communication\" include in a professional setup? / एक पेशेवर सेटअप में \"दृश्य संचार\" (Visual Communication) में क्या शामिल है?', 'Charts, graphs, diagrams, and presentations that present data visually', 'Only the use of high-quality photographs of the office building', 'The act of looking at colleagues while they are speaking', 'It refers to the use of fancy fonts in email subject lines', 'A'),
(282, 'DCA', NULL, 5, 'How does \"Stress Management\" impact communication? / \"तनाव प्रबंधन\" (Stress Management) संचार को कैसे प्रभावित करता है?', 'It prevents emotional outbursts and keeps the communication calm, logical, and professional', 'It causes the person to shout at colleagues and make communication aggressive', 'It has no impact because stress is a part of professional life and cannot be managed', 'It leads to avoiding all communication because the person is too tired to talk', 'A'),
(283, 'DCA', NULL, 5, 'What is the importance of \"Reading Skills\" in personality development? / व्यक्तित्व विकास में \"पढ़ने के कौशल\" (Reading Skills) का क्या महत्व है?', 'They broaden knowledge, improve vocabulary, and provide new perspectives on various topics', 'They are only useful for students and not for working professionals', 'They are a waste of time as audiobooks can do the same job faster', 'They make the person quiet and less interested in team interactions', 'A'),
(284, 'DCA', NULL, 5, 'How can \"Group Discussion\" (GD) improve communication skills? / \"समूह चर्चा\" (Group Discussion) संचार कौशल को कैसे सुधार सकती है?', 'It enhances the ability to listen to others, present views clearly, and influence group opinion', 'It is just a way for everyone to talk at once to see who is the loudest', 'It is a useless activity that does not reflect actual work capability', 'It is only meant for people who want to become politicians', 'A'),
(285, 'DCA', NULL, 5, 'What is the role of \"Confidence\" in overcoming obstacles? / बाधाओं को दूर करने में \"आत्मविश्वास\" (Confidence) की क्या भूमिका है?', 'It helps in analyzing the situation calmly and taking the necessary steps to solve the problem', 'It makes the person ignore the problem and hope that it will solve itself', 'It is a hindrance because being nervous makes a person more careful', 'It has no role because only luck determines success or failure', 'A'),
(286, 'DCA', NULL, 5, 'Why is \"Active Participation\" in meetings important? / बैठकों में \"सक्रिय भागीदारी\" (Active Participation) क्यों महत्वपूर्ण है?', 'It shows interest, adds value to the discussion, and ensures that the team benefits from your insights', 'It is just a way to show off your presence to the manager', 'It is distracting and slows down the meeting’s progress', 'It is not necessary because the notes of the meeting can be read later', 'A'),
(287, 'DCA', NULL, 5, 'What is \"Upward Communication\"? / \"ऊर्ध्वगामी संचार\" (Upward Communication) क्या है?', 'Communication from the lower-level employees to the higher-level management', 'Communication from the CEO to the entire organization', 'Communication among peers in the same department', 'Communication with the clients and public', 'A'),
(288, 'DCA', NULL, 5, 'How does \"Networking\" enhance professional personality? / \"नेटवर्किंग\" (Networking) पेशेवर व्यक्तित्व को कैसे बढ़ाती है?', 'It creates new opportunities, builds a supportive professional circle, and facilitates knowledge sharing', 'It is only about attending parties and collecting visiting cards', 'It is a selfish practice that should be avoided in a collaborative environment', 'It is a waste of time that takes away from productive work hours', 'A'),
(289, 'DCA', NULL, 5, 'What is the significance of \"Tone of Voice\" in verbal communication? / मौखिक संचार में \"आवाज के स्वर\" (Tone of Voice) का क्या महत्व है?', 'It conveys emotions, intent, and attitude, which can change the meaning of words', 'It has no significance as the words themselves are all that matters', 'It should always be flat and monotonous to sound professional', 'It should be loud and aggressive to sound authoritative', 'A'),
(290, 'DCA', NULL, 5, 'How can \"Critical Thinking\" improve decision-making? / \"आलोचनात्मक सोच\" (Critical Thinking) निर्णय लेने की क्षमता को कैसे सुधार सकती है?', 'It involves evaluating information objectively, considering various alternatives, and making informed choices', 'It involves following the advice of the most experienced person without questioning', 'It involves making decisions based on feelings and intuition without analyzing facts', 'It is a process that makes decisions more complicated and slower than necessary', 'A'),
(291, 'DCA', NULL, 5, 'What is the role of \"Writing Skills\" in career advancement? / करियर में उन्नति में \"लेखन कौशल\" (Writing Skills) की क्या भूमिका है?', 'They enable clear documentation, professional emails, and impactful reports that demonstrate competence', 'They are only useful for writers and journalists', 'They are not important as all work is now done through verbal instructions', 'They are a secondary skill that does not affect promotion prospects', 'A'),
(292, 'DCA', NULL, 5, 'What does \"Body Language\" include besides posture? / मुद्रा (Posture) के अलावा \"बॉडी लैंग्वेज\" में क्या शामिल है?', 'Facial expressions, gestures, eye contact, and personal space', 'The clothes that the person is wearing', 'The accent that the person uses while speaking', 'The amount of time the person spends in the office', 'A'),
(293, 'DCA', NULL, 5, 'How can \"Humility\" impact leadership? / \"विनम्रता\" (Humility) नेतृत्व को कैसे प्रभावित करती है?', 'It fosters respect, encourages learning from others, and builds a culture of trust within the team', 'It is a sign of weakness that makes the leader lose control over the team', 'It prevents the leader from setting high standards for the team', 'It is not relevant to leadership as leaders should be tough and authoritative', 'A'),
(294, 'DCA', NULL, 5, 'What is \"Effective Listening\"? / \"प्रभावी श्रवण\" (Effective Listening) क्या है?', 'The ability to listen carefully, comprehend, and provide appropriate feedback', 'The ability to hear everything being said without caring about the meaning', 'The act of agreeing with everything to avoid conflict', 'The act of hearing but not processing the information', 'A'),
(295, 'DCA', NULL, 5, 'Why is \"Feedback\" important for personal improvement? / व्यक्तिगत सुधार के लिए \"फीडबैक\" क्यों महत्वपूर्ण है?', 'It provides an external perspective on one’s performance, highlighting strengths and weaknesses', 'It is a tool for others to discourage and pull you down', 'It is unnecessary if one is already confident about their work', 'It only causes stress and should be avoided at all costs', 'A'),
(296, 'DCA', NULL, 5, 'What is the impact of \"Negative Communication\" on a team? / टीम पर \"नकारात्मक संचार\" (Negative Communication) का क्या प्रभाव पड़ता है?', 'It creates confusion, demotivates team members, and disrupts the workflow', 'It has no impact as long as the work gets done', 'It helps in keeping team members alert and focused', 'It encourages competition among team members', 'A'),
(297, 'DCA', NULL, 5, 'What is \"Professionalism\"? / \"व्यावसायिकता\" (Professionalism) क्या है?', 'The conduct, behavior, and attitude exhibited by someone in a professional work environment', 'The ability to wear formal clothes to work every day', 'The skill of avoiding work whenever possible', 'The act of always agreeing with the manager', 'A'),
(298, 'DCA', NULL, 5, 'How does \"Confidence\" affect team performance? / \"आत्मविश्वास\" (Confidence) टीम के प्रदर्शन को कैसे प्रभावित करता है?', 'It promotes collective action, inspires trust, and keeps the team focused on goals', 'It makes team members arrogant and uncooperative', 'It leads to complacency and ignoring important details', 'It is not important as team performance is based only on skills', 'A'),
(299, 'DCA', NULL, 5, 'What is the significance of \"Global Communication\"? / \"वैश्विक संचार\" (Global Communication) का क्या महत्व है?', 'It allows businesses to connect, collaborate, and operate across different cultures and regions', 'It is only for large multinational companies and not for small businesses', 'It is a complex task that should be avoided to prevent cultural misunderstandings', 'It is limited to translating documents into foreign languages', 'A'),
(300, 'DCA', NULL, 5, 'What is \"Active Listening\" in a team context? / टीम के संदर्भ में \"सक्रिय श्रवण\" (Active Listening) क्या है?', 'Respecting different viewpoints and ensuring everyone feels heard and understood during team discussions', 'Waiting for one’s turn to speak without listening to others', 'Focusing only on the leader’s opinions and ignoring the team members', 'Hearing the conversation but doing other things simultaneously', 'A'),
(301, 'DCA', NULL, 5, 'How can \"Digital Literacy\" enhance professional communication? / \"डिजिटल साक्षरता\" (Digital Literacy) पेशेवर संचार को कैसे बढ़ा सकती है?', 'It ensures the effective use of digital tools for collaboration, information sharing, and project management', 'It is only about using social media platforms for personal use', 'It makes communication less personal and more technical', 'It is a skill that will be replaced by AI in the future', 'A'),
(302, 'DCA', NULL, 5, 'What is the ultimate goal of \"Personality Development\"? / \"व्यक्तित्व विकास\" का अंतिम लक्ष्य क्या है?', 'To cultivate a well-rounded personality that is effective, confident, and balanced in all life aspects', 'To become a celebrity or gain social fame', 'To make oneself look better than everyone else', 'To avoid any interaction with people who are not successful', 'A'),
(303, 'DCA', NULL, 6, 'What is the primary function of a Web Browser in the context of Internet technology? / इंटरनेट तकनीक के संदर्भ में वेब ब्राउज़र का प्राथमिक कार्य क्या है?', 'To act as a software application that retrieves, presents, and traverses information resources on the World Wide Web', 'To function as a hardware device that connects a computer directly to the global telecommunication network', 'To provide a secure platform for creating and hosting websites on a remote server for public access', 'To perform background server-side operations that manage databases and user authentication for web applications', 'A'),
(304, 'DCA', NULL, 6, 'How does the \"Domain Name System\" (DNS) bridge the gap between human-readable domain names and machine-readable IP addresses? / डोमेन नेम सिस्टम (DNS) मानव-पठनीय डोमेन नामों और मशीन-पठनीय IP पतों के बीच की दूरी को कैसे पाटता है?', 'It translates alphanumeric domain names into numeric IP addresses that computers use to identify each other on the network', 'It physically converts the internet signals into optical light pulses for faster transmission across long-distance fiber optic cables', 'It assigns a unique identity to every user connected to the internet to ensure secure communication between servers', 'It acts as a physical database that stores all website content locally on the user’s computer for faster loading speeds', 'A'),
(305, 'DCA', NULL, 6, 'What is the fundamental difference between \"HTTP\" and \"HTTPS\" in terms of data security? / डेटा सुरक्षा के मामले में \"HTTP\" और \"HTTPS\" के बीच मौलिक अंतर क्या है?', 'HTTPS uses an encryption protocol (SSL/TLS) to secure data transmission, whereas HTTP transmits data in plain text', 'HTTP is used for mobile devices while HTTPS is exclusively designed for desktop and laptop computer systems', 'HTTPS is a newer version of the internet that is only available in advanced countries with high-speed connections', 'HTTP provides faster page loading speeds than HTTPS because it does not have the overhead of encryption', 'A'),
(306, 'DCA', NULL, 6, 'Which language is considered the structural foundation (skeleton) of every webpage on the internet? / इंटरनेट पर हर वेबपेज की संरचनात्मक नींव (कंकाल) किस भाषा को माना जाता है?', 'HTML (HyperText Markup Language), which provides the basic document structure and formatting elements', 'CSS (Cascading Style Sheets), which is used to define the visual layout and design of the webpage', 'JavaScript, which handles all interactive elements and client-side logic on the page', 'PHP (Hypertext Preprocessor), which manages server-side processing and database interactions', 'A'),
(307, 'DCA', NULL, 6, 'What is the role of an \"ISP\" (Internet Service Provider) in providing connectivity to end-users? / एंड-यूज़र्स को कनेक्टिविटी प्रदान करने में \"ISP\" (इंटरनेट सेवा प्रदाता) की क्या भूमिका है?', 'To provide the necessary infrastructure and bandwidth for users to access the internet through various connection types', 'To design and build websites for clients and host them on secure servers around the world', 'To provide hardware components like routers and modems to users free of cost for their homes', 'To manage the internal networking of a private office and restrict access to specific websites', 'A'),
(308, 'DCA', NULL, 6, 'What are the main advantages of using \"CSS\" (Cascading Style Sheets) in web development? / वेब विकास में \"CSS\" (कैस्केडिंग स्टाइल शीट्स) का उपयोग करने के मुख्य लाभ क्या हैं?', 'It separates content from presentation, allowing for consistent design and easier maintenance of multiple pages', 'It replaces HTML entirely, making the web page lighter and faster for all mobile browsers', 'It automatically secures the website from hackers by encrypting the visual design elements', 'It provides a way to store all user data in a structured format without using any database software', 'A'),
(309, 'DCA', NULL, 6, 'Which of the following is a classic example of a \"Client-Side Scripting Language\"? / निम्नलिखित में से कौन सा \"क्लाइंट-साइड स्क्रिप्टिंग भाषा\" का एक उत्कृष्ट उदाहरण है?', 'JavaScript, which executes in the user’s browser to create dynamic and interactive content', 'PHP, which is processed on the web server before the result is sent to the user', 'SQL, which is used to manage and query data stored in relational databases', 'Python, which is typically used for backend server-side application development', 'A'),
(310, 'DCA', NULL, 6, 'What is the function of an \"IP Address\" for a device connected to the internet? / इंटरनेट से जुड़े डिवाइस के लिए \"IP एड्रेस\" का कार्य क्या है?', 'To provide a unique numerical label to each device, allowing it to be located and identified on the network', 'To determine the physical location of the user and block them from certain websites based on geography', 'To store the user’s browsing history and personal preferences for targeted advertisements', 'To act as a password that protects the device from unauthorized access by other computers', 'A'),
(311, 'DCA', NULL, 6, 'Why is \"Web Hosting\" necessary for a website to be live on the internet? / इंटरनेट पर वेबसाइट को लाइव करने के लिए \"वेब होस्टिंग\" क्यों आवश्यक है?', 'It provides a dedicated storage space on a web server where the website files are stored and made accessible globally', 'It allows the website to be downloaded on a user’s computer for offline viewing at any time', 'It ensures that the website creator is the owner of the domain name and no one else can use it', 'It provides a graphical interface for designers to edit the website content without any coding skills', 'A'),
(312, 'DCA', NULL, 6, 'What is a \"Search Engine\" and how does it organize information from the web? / \"सर्च इंजन\" क्या है और यह वेब से जानकारी को कैसे व्यवस्थित करता है?', 'It uses automated bots to crawl, index, and rank web pages to provide relevant results based on user queries', 'It is a human-managed directory where websites are listed alphabetically by their content', 'It is a software installed on a computer that stores a local copy of the entire internet', 'It is a hardware device that filters all incoming information to ensure only safe websites are shown', 'A'),
(313, 'DCA', NULL, 6, 'What is the primary purpose of an \"URL\" (Uniform Resource Locator)? / \"URL\" (यूनिफ़ॉर्म रिसोर्स लोकेटर) का प्राथमिक उद्देश्य क्या है?', 'To provide a unique web address that identifies the specific location of a resource on the internet', 'To act as a security key that allows users to log into private servers', 'To compress the size of a webpage to make it load faster on slow internet connections', 'To translate the content of a website into different languages for global users', 'A'),
(314, 'DCA', NULL, 6, 'How do \"Cookies\" help websites enhance user experience? / \"कुकीज़\" (Cookies) वेबसाइटों को उपयोगकर्ता अनुभव बेहतर बनाने में कैसे मदद करती हैं?', 'They store small pieces of data on the user’s device to remember preferences, login status, and browsing activities', 'They act as viruses that track all the keystrokes made by the user on their computer', 'They provide a direct communication channel between the user and the website owner', 'They increase the speed of the internet connection by cacheing data in the browser', 'A'),
(315, 'DCA', NULL, 6, 'What is \"Responsive Web Design\" and why is it important today? / \"रिस्पॉन्सिव वेब डिज़ाइन\" क्या है और यह आज के समय में क्यों महत्वपूर्ण है?', 'It allows a website to automatically adjust its layout and content to fit screens of all sizes, from mobile to desktop', 'It is a method of using only images on a website so it looks good on all devices', 'It is a technique where the website only loads on mobile devices and blocks desktop access', 'It is a way to make the website load faster by removing all text and using only colors', 'A'),
(316, 'DCA', NULL, 6, 'What is the role of an \"FTP\" (File Transfer Protocol) client? / \"FTP\" (फाइल ट्रांसफर प्रोटोकॉल) क्लाइंट की क्या भूमिका है?', 'To facilitate the transfer of files between a local computer and a remote web server', 'To create animations and videos for web pages using specialized tools', 'To manage the internal network security of an organization', 'To encrypt messages between two users to prevent data theft', 'A'),
(317, 'DCA', NULL, 6, 'How does a \"Web Server\" differ from a standard computer? / एक \"वेब सर्वर\" एक मानक कंप्यूटर से कैसे भिन्न है?', 'It is designed to serve content (like websites) to multiple clients via HTTP, running 24/7 with high reliability', 'It is just a computer that has a faster processor and more RAM than a regular laptop', 'It is a computer that only accepts files and does not send any data back', 'It is physically located only in large companies and cannot be accessed from home', 'A'),
(318, 'DCA', NULL, 6, 'What is the significance of the \"WWW\" (World Wide Web)? / \"WWW\" (वर्ल्ड वाइड वेब) का क्या महत्व है?', 'It is the system of interlinked hypertext documents that are accessed via the internet', 'It is the actual physical infrastructure of cables and satellites that make up the internet', 'It is a programming language that is used to create all websites globally', 'It is the name of the organization that regulates all internet activities worldwide', 'A'),
(319, 'DCA', NULL, 6, 'What is the difference between \"Static\" and \"Dynamic\" websites? / \"स्टैटिक\" और \"डायनामिक\" वेबसाइटों में क्या अंतर है?', 'Static websites display the same content to all visitors, while dynamic websites generate content based on user interaction/database', 'Static websites are built with HTML/CSS, while dynamic websites are built with only images', 'Dynamic websites are always offline, while static websites are always online', 'There is no difference, both are essentially the same', 'A'),
(320, 'DCA', NULL, 6, 'What is a \"Web Portal\"? / \"वेब पोर्टल\" क्या है?', 'A specially designed website that brings information from diverse sources into a uniform way, often including email and news', 'A type of search engine that only searches for images and videos', 'A private network used by a company to store internal employee documents', 'A physical device that acts as a router for a local home network', 'A'),
(321, 'DCA', NULL, 6, 'Which technology allows for real-time updates on a webpage without refreshing? / कौन सी तकनीक वेबपेज को रिफ्रेश किए बिना वास्तविक समय (real-time) में अपडेट करने की अनुमति देती है?', 'AJAX (Asynchronous JavaScript and XML), which updates parts of the page without reloading the entire content', 'HTML5, which is the latest standard for building web documents', 'CSS3, which adds advanced animations to the webpage', 'HTTP/2, which is a faster version of the transfer protocol', 'A'),
(322, 'DCA', NULL, 6, 'What is the purpose of an \"HTML Tag\"? / \"HTML टैग\" का उद्देश्य क्या है?', 'To define how the web browser should format and display the content inside it', 'To act as a security guard for the website against cyber attacks', 'To create a database connection to store user input', 'To style the webpage with colors and font sizes', 'A'),
(323, 'DCA', NULL, 6, 'What does \"SEO\" (Search Engine Optimization) aim to achieve? / \"SEO\" (सर्च इंजन ऑप्टिमाइज़ेशन) का उद्देश्य क्या हासिल करना है?', 'To improve the visibility and ranking of a website in search engine results for relevant queries', 'To prevent users from finding the website through search engines', 'To make the website run faster on slow internet connections', 'To add more features to the website using programming languages', 'A'),
(324, 'DCA', NULL, 6, 'What is the function of \"JavaScript\" in web development? / वेब विकास में \"JavaScript\" का कार्य क्या है?', 'To add interactivity, behavior, and dynamic functionality to a webpage', 'To act as a database to store user registration details', 'To design the layout of the page using boxes and grids', 'To provide hosting for the website on a server', 'A'),
(325, 'DCA', NULL, 6, 'How does a \"Firewall\" protect a network connected to the internet? / \"फ़ायरवॉल\" इंटरनेट से जुड़े नेटवर्क की रक्षा कैसे करता है?', 'It monitors and filters incoming and outgoing network traffic based on predefined security rules', 'It prevents all users from accessing the internet to keep the network safe', 'It encrypts every file on the computer to prevent theft', 'It acts as an antivirus software that deletes malicious files', 'A'),
(326, 'DCA', NULL, 6, 'What is the role of a \"Web Developer\"? / \"वेब डेवलपर\" की भूमिका क्या है?', 'To design, create, and maintain websites and web applications', 'To sell hardware devices to customers for internet connection', 'To manage the customer service team for an internet company', 'To write news articles for a blog website', 'A'),
(327, 'DCA', NULL, 6, 'What is a \"Hyperlink\"? / \"हाइपरलिंक\" क्या है?', 'A reference in a document that links to another document or a different part of the same document', 'A specific type of image that does not change when clicked', 'A security measure that prevents access to a website', 'A programming code that creates a database entry', 'A'),
(328, 'DCA', NULL, 6, 'What is \"Client-Side\" processing? / \"क्लाइंट-साइड\" प्रोसेसिंग क्या है?', 'When code is executed on the user’s computer (e.g., in a web browser)', 'When code is executed on the remote web server', 'When the database manages the request directly', 'When the internet service provider manages the content', 'A'),
(329, 'DCA', NULL, 6, 'What is \"Server-Side\" processing? / \"सर्वर-साइड\" प्रोसेसिंग क्या है?', 'When code is executed on the web server before the result is sent to the client browser', 'When the user’s browser handles all logic and database queries', 'When the internet router processes the user requests', 'When a website is only available on a local network', 'A'),
(330, 'DCA', NULL, 6, 'What is \"Cloud Computing\" in the context of the web? / वेब के संदर्भ में \"क्लाउड कंप्यूटिंग\" क्या है?', 'The delivery of computing services—including servers, storage, databases—over the internet', 'A technology that stores files only on a USB flash drive', 'A method of connecting two computers using a long wire', 'A software that runs only on one computer at a time', 'A'),
(331, 'DCA', NULL, 6, 'What is the purpose of an \"IP address\" range? / \"IP एड्रेस\" रेंज का उद्देश्य क्या है?', 'To define the network boundaries and allocate addresses to devices on a network', 'To make the internet faster for all users in a city', 'To block all websites that are not secure', 'To create a backup of the user’s data on the internet', 'A'),
(332, 'DCA', NULL, 6, 'What is \"Bandwidth\"? / \"बैंडविड्थ\" (Bandwidth) क्या है?', 'The maximum data transmission capacity of a network connection', 'The amount of time it takes for a web page to load', 'The number of people visiting a website at one time', 'The total cost of the internet service', 'A'),
(333, 'DCA', NULL, 6, 'What is the \"Internet\"? / \"इंटरनेट\" क्या है?', 'A global system of interconnected computer networks that communicate via standard protocols', 'A private network that is owned by one single company', 'A software that helps in organizing files on a computer', 'A digital library that contains only books', 'A'),
(334, 'DCA', NULL, 6, 'What is the function of \"HTTP\"? / \"HTTP\" का कार्य क्या है?', 'A protocol for the transfer of hypertext documents on the web', 'A protocol for sending emails across the world', 'A hardware device that connects to the server', 'A language used to create graphics on the screen', 'A'),
(335, 'DCA', NULL, 6, 'What is a \"Web Application\"? / \"वेब एप्लिकेशन\" क्या है?', 'Software that runs in a web browser, like an online banking system or social media', 'A game that is installed on the desktop', 'A file that stores text on the computer', 'A hardware device that scans documents', 'A'),
(336, 'DCA', NULL, 6, 'What is the role of \"HTML5\"? / \"HTML5\" की भूमिका क्या है?', 'The latest version of HTML, providing new features for multimedia and interactivity', 'A software used for video editing', 'A hardware component for servers', 'An ancient language used for programming', 'A'),
(337, 'DCA', NULL, 6, 'What is \"SSL\" (Secure Sockets Layer)? / \"SSL\" का क्या अर्थ है?', 'A security protocol for establishing an encrypted link between a web server and a browser', 'A language used to design the UI of a website', 'A tool used for marketing purposes', 'A database management system', 'A');
INSERT INTO `questions` (`id`, `course`, `subject_name`, `subject_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(338, 'DCA', NULL, 6, 'What is \"Intranet\"? / \"इंट्रानेट\" क्या है?', 'A private network accessible only to an organization’s staff', 'A global network available to everyone', 'A hardware component for the internet', 'A search engine for websites', 'A'),
(339, 'DCA', NULL, 6, 'What is \"Extranet\"? / \"एक्सट्रानेट\" क्या है?', 'An intranet that allows controlled access to authorized outsiders', 'A network that is entirely public', 'A private network that no one can access', 'A type of web browser', 'A'),
(340, 'DCA', NULL, 6, 'What is a \"Web Server\"? / \"वेब सर्वर\" क्या है?', 'A computer system that stores and delivers web content', 'A person who builds websites', 'A type of internet connection', 'A document editor', 'A'),
(341, 'DCA', NULL, 6, 'What is \"WLAN\" (Wireless Local Area Network)? / \"WLAN\" क्या है?', 'A network that allows devices to connect without physical cables', 'A cable network for home use', 'A global internet system', 'A satellite communication tool', 'A'),
(342, 'DCA', NULL, 6, 'What is \"TCP/IP\"? / \"TCP/IP\" क्या है?', 'The fundamental suite of communication protocols used to interconnect network devices on the internet', 'A language used for web design', 'A file format for images', 'A tool for searching information', 'A'),
(343, 'DCA', NULL, 6, 'What is a \"Domain Name\"? / \"डोमेन नेम\" क्या है?', 'A human-friendly address for a website (e.g., google.com)', 'A piece of code for the browser', 'A hardware device', 'An email address', 'A'),
(344, 'DCA', NULL, 6, 'What is \"Phishing\"? / \"फ़िशिंग\" (Phishing) क्या है?', 'A cybercrime where attackers masquerade as trusted entities to steal sensitive information', 'A legitimate marketing strategy', 'A tool to speed up the computer', 'A way to back up data', 'A'),
(345, 'DCA', NULL, 6, 'What is \"Caching\"? / \"कैशिंग\" (Caching) क्या है?', 'Storing copies of files in a temporary storage location to reduce latency and access time', 'Deleting data from the server', 'Organizing files in a folder', 'Creating a new website', 'A'),
(346, 'DCA', NULL, 6, 'What is a \"Web Browser Engine\"? / \"वेब ब्राउज़र इंजन\" क्या है?', 'The core software component that renders HTML and other web code into a visible page', 'A tool for editing images', 'A hardware component', 'A server management tool', 'A'),
(347, 'DCA', NULL, 6, 'What is the role of \"DNS\" in email delivery? / ईमेल वितरण में \"DNS\" की भूमिका क्या है?', 'It resolves the domain name of the email server to its IP address', 'It writes the email content', 'It sends the email to the user', 'It filters spam emails', 'A'),
(348, 'DCA', NULL, 6, 'What is \"Upload\"? / \"अपलोड\" क्या है?', 'Sending data from a local computer to a remote server', 'Receiving data from a server', 'Deleting a file', 'Searching for a file', 'A'),
(349, 'DCA', NULL, 6, 'What is \"Download\"? / \"डाउनलोड\" क्या है?', 'Receiving data from a remote server to a local computer', 'Sending data to a server', 'Creating a file', 'Moving a file', 'A'),
(350, 'DCA', NULL, 6, 'What is \"Latency\"? / \"लेटेंसी\" (Latency) क्या है?', 'The time delay between a user action and the response from the web application', 'The size of the data', 'The name of the server', 'The type of browser', 'A'),
(351, 'DCA', NULL, 6, 'What is a \"Web Spider\"? / \"वेब स्पाइडर\" क्या है?', 'A program that crawls the web to index pages for search engines', 'A web design tool', 'A virus that deletes files', 'A server hardware', 'A'),
(352, 'DCA', NULL, 6, 'What is \"Web 2.0\"? / \"वेब 2.0\" क्या है?', 'The second stage of development of the World Wide Web, characterized by user-generated content and usability', 'An older version of the web', 'A new programming language', 'A type of hardware', 'A'),
(403, 'DCA', NULL, 8, 'What is the correct syntax to declare a one-dimensional array of 10 integers in C? / C में 10 पूर्णांकों (integers) की एक-आयामी सरणी (array) घोषित करने के लिए सही सिंटैक्स क्या है?', 'int arr[10];', 'array int arr[10];', 'int arr{10};', 'int array(10);', 'A'),
(404, 'DCA', NULL, 8, 'Which of the following is the correct way to declare a pointer variable in C programming? / C प्रोग्रामिंग में पॉइंटर वेरिएबल घोषित करने का सही तरीका कौन सा है?', 'int *ptr;', 'ptr *int;', '*int ptr;', 'int ptr;', 'A'),
(405, 'DCA', NULL, 8, 'What will be the output of the code snippet: printf(\"%d\", 10 > 5 && 2 < 4);? / कोड स्निपेट printf(\"%d\", 10 > 5 && 2 < 4); का आउटपुट क्या होगा?', '1 (True)', '0 (False)', 'Error', '5', 'A'),
(406, 'DCA', NULL, 8, 'Which function is used to allocate a block of memory dynamically at runtime? / रनटाइम पर गतिशील रूप से मेमोरी के एक ब्लॉक को आवंटित करने के लिए किस फ़ंक्शन का उपयोग किया जाता है?', 'malloc()', 'calloc()', 'free()', 'realloc()', 'A'),
(407, 'DCA', NULL, 8, 'What is the purpose of the `#include <stdio.h>` preprocessor directive? / `#include <stdio.h>` प्रीप्रोसेसर निर्देश का उद्देश्य क्या है?', 'To include the Standard Input Output library for functions like printf and scanf', 'To include the math library for calculating square roots', 'To define the main function of the program', 'To declare all global variables used in the program', 'A'),
(408, 'DCA', NULL, 8, 'Which of the following is not a valid C data type? / निम्नलिखित में से कौन सा एक वैध C डेटा प्रकार नहीं है?', 'real', 'int', 'float', 'double', 'A'),
(409, 'DCA', NULL, 8, 'What does the \"continue\" statement do in a C loop? / C लूप में \"continue\" स्टेटमेंट क्या करता है?', 'It skips the remaining statements of the current iteration and jumps to the next iteration', 'It terminates the entire loop immediately', 'It restarts the loop from the first iteration', 'It exits the current function completely', 'A'),
(410, 'DCA', NULL, 8, 'What is the size of a `char` data type in standard C programming? / मानक C प्रोग्रामिंग में `char` डेटा प्रकार का आकार क्या है?', '1 byte', '2 bytes', '4 bytes', '8 bytes', 'A'),
(411, 'DCA', NULL, 8, 'Which operator is used to access the address of a variable? / किसी वेरिएबल के पते (address) को एक्सेस करने के लिए किस ऑपरेटर का उपयोग किया जाता है?', '& (Address-of operator)', '* (Dereference operator)', '% (Modulus operator)', '# (Preprocessor operator)', 'A'),
(412, 'DCA', NULL, 8, 'What is the default return value of the `main()` function in C? / C में `main()` फ़ंक्शन का डिफ़ॉल्ट रिटर्न मान क्या है?', 'int (0 indicates success)', 'void (no return)', 'char', 'float', 'A'),
(413, 'DCA', NULL, 8, 'Which loop structure is guaranteed to execute at least once? / कौन सा लूप स्ट्रक्चर कम से कम एक बार निष्पादित होने की गारंटी देता है?', 'do-while loop', 'while loop', 'for loop', 'nested loop', 'A'),
(414, 'DCA', NULL, 8, 'What is the output of the expression `5 / 2` in C? / C में `5 / 2` अभिव्यक्ति का आउटपुट क्या है?', '2 (Integer division truncates the decimal part)', '2.5', '3', '0', 'A'),
(415, 'DCA', NULL, 8, 'How can you create a user-defined data type in C using an existing data type? / मौजूदा डेटा प्रकार का उपयोग करके C में उपयोगकर्ता-परिभाषित डेटा प्रकार कैसे बना सकते हैं?', 'typedef', 'struct', 'union', 'enum', 'A'),
(416, 'DCA', NULL, 8, 'What is a \"Structure\" in C? / C में \"स्ट्रक्चर\" क्या है?', 'A user-defined data type that groups variables of different data types under a single name', 'A collection of similar data types stored in contiguous memory', 'A function that returns multiple values', 'A keyword used for memory allocation', 'A'),
(417, 'DCA', NULL, 8, 'What does `strlen()` function return? / `strlen()` फ़ंक्शन क्या लौटाता है?', 'The number of characters in a string excluding the null terminator', 'The size of the string in bytes including the null terminator', 'The memory address of the string', 'The first character of the string', 'A'),
(418, 'DCA', NULL, 8, 'Which keyword is used to prevent any changes to a variable value? / किसी वेरिएबल के मान में बदलाव को रोकने के लिए किस कीवर्ड का उपयोग किया जाता है?', 'const', 'static', 'volatile', 'register', 'A'),
(419, 'DCA', NULL, 8, 'What is the difference between `printf` and `puts` for printing strings? / स्ट्रिंग प्रिंट करने के लिए `printf` और `puts` में क्या अंतर है?', 'puts adds a newline character automatically, printf does not', 'printf is faster than puts', 'puts can print integers, printf cannot', 'There is no difference', 'A'),
(420, 'DCA', NULL, 8, 'What is a \"Recursive Function\"? / \"रिकर्सिव फ़ंक्शन\" क्या है?', 'A function that calls itself to solve a problem', 'A function that is called from another function', 'A function that returns no value', 'A function that is declared inside another function', 'A'),
(421, 'DCA', NULL, 8, 'Which header file is needed for `math.h` functions like `pow()` and `sqrt()`? / `pow()` और `sqrt()` जैसे `math.h` फ़ंक्शन के लिए कौन सी हेडर फ़ाइल की आवश्यकता है?', '<math.h>', '<stdio.h>', '<string.h>', '<stdlib.h>', 'A'),
(422, 'DCA', NULL, 8, 'What is an \"Infinite Loop\"? / \"इन्फिनिट लूप\" क्या है?', 'A loop that never terminates because the exit condition is always true', 'A loop that executes only once', 'A loop that contains no code', 'A loop that is empty', 'A'),
(423, 'DCA', NULL, 8, 'How do you represent a newline character in C? / C में न्यूलाइन कैरेक्टर को कैसे दर्शाते हैं?', '\\n', '/n', '\\t', '\\r', 'A'),
(424, 'DCA', NULL, 8, 'What is the use of `switch` statement in C? / C में `switch` स्टेटमेंट का उपयोग क्या है?', 'To execute one code block among many based on the value of a variable', 'To repeat a block of code multiple times', 'To declare a new data type', 'To allocate memory', 'A'),
(425, 'DCA', NULL, 8, 'Which operator is known as the \"ternary operator\" in C? / C में किस ऑपरेटर को \"टर्नरी ऑपरेटर\" के रूप में जाना जाता है?', '?:', '&&', '||', '!', 'A'),
(426, 'DCA', NULL, 8, 'What is a \"Union\" in C? / C में \"यूनियन\" क्या है?', 'A user-defined type where members share the same memory location', 'A collection of functions', 'A type that stores only floating-point numbers', 'A library function', 'A'),
(427, 'DCA', NULL, 8, 'Which of the following is a logical AND operator? / निम्नलिखित में से कौन सा एक तार्किक (logical) AND ऑपरेटर है?', '&&', '&', '||', '!', 'A'),
(428, 'DCA', NULL, 8, 'What is the range of a `signed int` (16-bit)? / `signed int` (16-bit) की रेंज क्या है?', '-32,768 to 32,767', '0 to 65,535', '-128 to 127', '0 to 32,767', 'A'),
(429, 'DCA', NULL, 8, 'How do you comment a single line in C? / C में सिंगल लाइन कमेंट कैसे करते हैं?', '// comment', '/* comment */', '# comment', '', 'A'),
(430, 'DCA', NULL, 8, 'What is the value of `EOF`? / `EOF` का मान क्या है?', '-1', '0', '1', 'NULL', 'A'),
(431, 'DCA', NULL, 8, 'Which function is used to close a file? / फ़ाइल को बंद करने के लिए किस फ़ंक्शन का उपयोग किया जाता है?', 'fclose()', 'close()', 'free()', 'delete()', 'A'),
(432, 'DCA', NULL, 8, 'What is the format specifier for a `float` number? / `float` संख्या के लिए फॉर्मेट स्पेसिफायर क्या है?', '%f', '%d', '%c', '%s', 'A'),
(433, 'DCA', NULL, 8, 'What is the purpose of `argc` and `argv` in `main()` function? / `main()` फ़ंक्शन में `argc` और `argv` का उद्देश्य क्या है?', 'To handle command-line arguments', 'To store file paths', 'To manage memory', 'To count local variables', 'A'),
(434, 'DCA', NULL, 8, 'What is the result of `!0` in C? / C में `!0` का परिणाम क्या है?', '1', '0', 'Error', 'True', 'A'),
(435, 'DCA', NULL, 8, 'Which storage class has the fastest access time? / किस स्टोरेज क्लास का एक्सेस टाइम सबसे तेज़ है?', 'register', 'auto', 'static', 'extern', 'A'),
(436, 'DCA', NULL, 8, 'What is the use of `break` statement? / `break` स्टेटमेंट का उपयोग क्या है?', 'To exit the nearest loop or switch block immediately', 'To skip the current iteration', 'To restart the loop', 'To terminate the program', 'A'),
(437, 'DCA', NULL, 8, 'Which of the following is used to read formatted input from the user? / उपयोगकर्ता से इनपुट पढ़ने के लिए किसका उपयोग किया जाता है?', 'scanf()', 'printf()', 'getch()', 'gets()', 'A'),
(438, 'DCA', NULL, 8, 'What is a \"string\" in C? / C में \"स्ट्रिंग\" क्या है?', 'An array of characters terminated by a null character (\\0)', 'A primitive data type', 'A pointer to a function', 'A library function', 'A'),
(439, 'DCA', NULL, 8, 'What is the purpose of `memset()` function? / `memset()` फ़ंक्शन का उद्देश्य क्या है?', 'To fill a block of memory with a specific value', 'To clear the screen', 'To open a file', 'To calculate the string length', 'A'),
(440, 'DCA', NULL, 8, 'Which operator is used to compare two values for equality? / दो मानों की समानता की तुलना करने के लिए किस ऑपरेटर का उपयोग किया जाता है?', '==', '=', '!=', '<=', 'A'),
(441, 'DCA', NULL, 8, 'What is a \"Function Prototype\"? / \"फ़ंक्शन प्रोटोटाइप\" क्या है?', 'A declaration of a function that specifies its name, return type, and parameters', 'The complete definition of the function', 'A call to a function', 'A variable inside a function', 'A'),
(442, 'DCA', NULL, 8, 'Which symbol is used to terminate a C statement? / C स्टेटमेंट को समाप्त करने के लिए किस प्रतीक का उपयोग किया जाता है?', ';', ':', '.', ',', 'A'),
(443, 'DCA', NULL, 8, 'What is the binary representation of 5? / 5 का बाइनरी प्रतिनिधित्व क्या है?', '101', '110', '100', '111', 'A'),
(444, 'DCA', NULL, 8, 'What is the use of `enum`? / `enum` का उपयोग क्या है?', 'To define a set of named integer constants', 'To create a floating-point variable', 'To open files', 'To allocate memory', 'A'),
(445, 'DCA', NULL, 8, 'What is a \"Global Variable\"? / \"ग्लोबल वेरिएबल\" क्या है?', 'A variable declared outside all functions, accessible throughout the program', 'A variable declared inside the main function', 'A variable declared inside a loop', 'A variable that stores only constant values', 'A'),
(446, 'DCA', NULL, 8, 'Which operator is used to shift bits to the left? / बिट्स को बाईं ओर शिफ्ट करने के लिए किस ऑपरेटर का उपयोग किया जाता है?', '<<', '>>', '&', '|', 'A'),
(447, 'DCA', NULL, 8, 'What is the purpose of the `volatile` keyword? / `volatile` कीवर्ड का उद्देश्य क्या है?', 'To tell the compiler that a variable’s value can change unexpectedly', 'To make a variable static', 'To store a variable in the register', 'To prevent variable access', 'A'),
(448, 'DCA', NULL, 8, 'What does `void` return type indicate? / `void` रिटर्न टाइप क्या इंगित करता है?', 'The function returns no value', 'The function returns a pointer', 'The function returns 0', 'The function returns an integer', 'A'),
(449, 'DCA', NULL, 8, 'What is the size of an `int` pointer on a 32-bit system? / 32-बिट सिस्टम पर `int` पॉइंटर का आकार क्या है?', '4 bytes', '2 bytes', '8 bytes', '1 byte', 'A'),
(450, 'DCA', NULL, 8, 'Which header file is required for `gets()` and `puts()`? / `gets()` और `puts()` के लिए कौन सी हेडर फ़ाइल की आवश्यकता है?', '<stdio.h>', '<conio.h>', '<string.h>', '<stdlib.h>', 'A'),
(451, 'DCA', NULL, 8, 'What is the first element index of an array in C? / C में सरणी (array) का पहला तत्व सूचकांक (index) क्या है?', '0', '1', '-1', 'NULL', 'A'),
(452, 'DCA', NULL, 8, 'Which function is used to convert a character to uppercase? / किसी अक्षर को अपरकेस में बदलने के लिए किस फ़ंक्शन का उपयोग किया जाता है?', 'toupper()', 'tolower()', 'isUpper()', 'strupr()', 'A'),
(453, 'DCA', NULL, 9, 'What is the primary goal of \"Cyber Security\"? / साइबर सुरक्षा का प्राथमिक लक्ष्य क्या है?', 'To protect computer systems, networks, and data from unauthorized access, attacks, and damage', 'To provide high-speed internet access to all users in a corporate network', 'To increase the number of advertisements displayed on a website for higher revenue', 'To replace all human employees with automated software to perform tasks efficiently', 'A'),
(454, 'DCA', NULL, 9, 'What does the \"CIA Triad\" represent in information security? / सूचना सुरक्षा में \"CIA Triad\" क्या दर्शाता है?', 'Confidentiality, Integrity, and Availability of data', 'Cyber, Intelligence, and Analysis of system threats', 'Communication, Internet, and Access management protocol', 'Computer, Infrastructure, and Application development tools', 'A'),
(455, 'DCA', NULL, 9, 'What is \"Phishing\" in the context of cyber attacks? / साइबर हमलों के संदर्भ में \"फ़िशिंग\" (Phishing) क्या है?', 'A fraudulent attempt to obtain sensitive information like usernames, passwords, and credit card details by disguising as a trustworthy entity', 'A method of repairing damaged computer files by using advanced recovery software tools', 'A technique used by web developers to make websites load faster on mobile devices', 'A type of legitimate communication between a company and its customers to inform them about new product launches', 'A'),
(456, 'DCA', NULL, 9, 'What is a \"Firewall\" and how does it protect a network? / \"फ़ायरवॉल\" क्या है और यह नेटवर्क की सुरक्षा कैसे करता है?', 'A security system that monitors and controls incoming and outgoing network traffic based on predefined security rules', 'A device that increases the physical speed of the internet connection in an office network', 'A software that organizes files in a computer to prevent accidental deletion', 'A backup tool that creates copies of all data stored on a computer to prevent data loss', 'A'),
(457, 'DCA', NULL, 9, 'Which of the following best describes \"Malware\"? / निम्नलिखित में से कौन सा \"मैलवेयर\" का सबसे अच्छा वर्णन करता है?', 'Malicious software designed to infiltrate, damage, or gain unauthorized access to a computer system', 'A software used for designing professional graphics and animations for websites', 'A security program that automatically deletes duplicate files from a computer', 'A legal operating system update provided by a manufacturer for system maintenance', 'A'),
(458, 'DCA', NULL, 9, 'What is \"Encryption\" and why is it important for data security? / \"एन्क्रिप्शन\" क्या है और यह डेटा सुरक्षा के लिए क्यों महत्वपूर्ण है?', 'The process of encoding information so that only authorized parties can access it, ensuring data confidentiality', 'The process of deleting old files to free up space on a computer hard drive', 'The method of increasing the resolution of images to make them look clearer', 'The process of identifying all users who have access to a specific computer system', 'A'),
(459, 'DCA', NULL, 9, 'What is \"Ransomware\" and how does it work? / \"रैनसमवेयर\" क्या है और यह कैसे काम करता है?', 'A type of malware that encrypts a victim’s files and demands payment for the decryption key', 'A tool used by hackers to gain unauthorized access to email accounts', 'A program that helps users recover lost passwords for their social media accounts', 'A virus that only affects mobile devices and not desktop computers', 'A'),
(460, 'DCA', NULL, 9, 'What is the \"Principle of Least Privilege\" (PoLP) in security? / सुरक्षा में \"न्यूनतम विशेषाधिकार का सिद्धांत\" (PoLP) क्या है?', 'Giving users only the minimum levels of access needed to perform their job functions', 'Giving every user full administrative access to all files in the system', 'Restricting all users from accessing any files on the network for security reasons', 'Giving users access to files based on their seniority in the company rather than job requirements', 'A'),
(461, 'DCA', NULL, 9, 'What is \"Two-Factor Authentication\" (2FA)? / \"टू-फैक्टर ऑथेंटिकेशन\" (2FA) क्या है?', 'A security process that requires two different forms of identification to access an account', 'A process where a user only needs to remember one password for all accounts', 'A feature that automatically resets the password if it is forgotten by the user', 'A system that checks if the internet connection is secure before logging in', 'A'),
(462, 'DCA', NULL, 9, 'What is a \"Denial-of-Service\" (DoS) attack? / \"डिनायल-ऑफ-सर्विस\" (DoS) हमला क्या है?', 'An attack meant to shut down a machine or network, making it inaccessible to its intended users', 'An attempt to steal sensitive data from a server without leaving any traces', 'A method of spreading viruses to all computers in a local area network', 'A legitimate way to test the strength of a firewall by overloading it', 'A'),
(463, 'DCA', NULL, 9, 'What is \"Social Engineering\" in cyber security? / साइबर सुरक्षा में \"सोशल इंजीनियरिंग\" क्या है?', 'Manipulating people into performing actions or divulging confidential information', 'The process of building social media apps for professional networking', 'A technique used to optimize websites for search engines', 'The practice of hiring hackers to improve system security', 'A'),
(464, 'DCA', NULL, 9, 'What does a \"Vulnerability Scanner\" do? / \"वल्नरेबिलिटी स्कैनर\" क्या करता है?', 'It identifies security weaknesses in a computer system, network, or application', 'It repairs damaged files on a hard drive automatically', 'It organizes all files in a computer to improve system performance', 'It provides internet connectivity to computers in a network', 'A'),
(465, 'DCA', NULL, 9, 'What is the role of an \"Antivirus\" program? / \"एंटीवायरस\" प्रोग्राम की भूमिका क्या है?', 'To detect, prevent, and remove malicious software from a computer system', 'To provide internet access to the user through a secure gateway', 'To encrypt all personal files of the user to prevent theft', 'To maintain the hardware components of the computer system', 'A'),
(466, 'DCA', NULL, 9, 'What is \"Zero-Day Exploit\"? / \"जीरो-डे एक्सप्लॉइट\" क्या है?', 'An attack that exploits a software vulnerability that is unknown to the vendor and has no patch', 'A virus that only attacks on a specific day of the year', 'A security update provided by a software company for a known bug', 'A method of cleaning a computer infected with malware', 'A'),
(467, 'DCA', NULL, 9, 'What is \"Biometric Authentication\"? / \"बायोमेट्रिक ऑथेंटिकेशन\" क्या है?', 'Using unique physical characteristics like fingerprints, iris scans, or face recognition to verify identity', 'Using a complex password that is updated every day', 'Using a security token that generates a new code every time', 'Using a smart card to enter a secure building', 'A'),
(468, 'DCA', NULL, 9, 'What is \"Spyware\"? / \"स्पायवेयर\" क्या है?', 'Malicious software that secretly gathers information about a person or organization', 'A legitimate tool used for monitoring office productivity', 'A program that automatically updates software', 'A software used for designing websites', 'A'),
(469, 'DCA', NULL, 9, 'What is the purpose of a \"Virtual Private Network\" (VPN)? / \"वर्चुअल प्राइवेट नेटवर्क\" (VPN) का उद्देश्य क्या है?', 'To create a secure and encrypted connection over a public network, protecting user privacy', 'To provide high-speed internet to remote locations', 'To block all websites on the internet for security', 'To manage internal files of a company in a secure way', 'A'),
(470, 'DCA', NULL, 9, 'What is \"Data Breach\"? / \"डेटा ब्रीच\" क्या है?', 'An incident where information is accessed or disclosed without authorization', 'A process of backing up data to a secure server', 'The act of deleting unnecessary files from a system', 'A security measure that encrypts all data', 'A'),
(471, 'DCA', NULL, 9, 'What is \"Password Hashing\"? / \"पासवर्ड हैशिंग\" क्या है?', 'The process of converting a password into a fixed-length string of characters to protect it', 'The act of guessing a password by trying all combinations', 'The process of saving a password in a text file', 'The act of sharing a password with colleagues', 'A'),
(472, 'DCA', NULL, 9, 'What is \"Botnet\"? / \"बॉटनेट\" क्या है?', 'A network of infected computers controlled remotely by a hacker for malicious purposes', 'A network of legitimate computers used for web hosting', 'A group of security professionals working to protect a network', 'A software used to manage internet traffic in an office', 'A'),
(473, 'DCA', NULL, 9, 'What is \"Man-in-the-Middle\" (MitM) attack? / \"मैन-इन-द-मिडल\" (MitM) हमला क्या है?', 'An attack where the attacker intercepts and potentially alters the communication between two parties', 'An attack where the attacker crashes the server', 'A method to gain unauthorized access to a database', 'A technique to steal physical hardware from an office', 'A'),
(474, 'DCA', NULL, 9, 'What is the significance of \"Cyber Law\"? / \"साइबर कानून\" (Cyber Law) का क्या महत्व है?', 'To provide legal frameworks and regulations for dealing with cybercrimes and protecting online rights', 'To teach people how to hack websites legally', 'To provide a platform for online shopping', 'To manage the internet infrastructure in a country', 'A'),
(475, 'DCA', NULL, 9, 'What is \"Public Key Infrastructure\" (PKI)? / \"पब्लिक की इंफ्रास्ट्रक्चर\" (PKI) क्या है?', 'A set of roles, policies, and procedures needed to manage digital certificates and public-key encryption', 'A physical key used to lock a computer room', 'A password used to access a secure server', 'A tool to track internet usage', 'A'),
(476, 'DCA', NULL, 9, 'What is \"Access Control\"? / \"एक्सेस कंट्रोल\" क्या है?', 'A security technique that regulates who can view or use resources in a computing environment', 'A method to increase the speed of a network', 'A system to monitor employee activity', 'A tool to delete files from a computer', 'A'),
(477, 'DCA', NULL, 9, 'What is \"Incident Response\"? / \"इंसिडेंट रिस्पॉन्स\" क्या है?', 'The organized approach to addressing and managing the aftermath of a security breach or cyber attack', 'The process of preventing all cyber attacks', 'The act of ignoring small security alerts', 'The process of creating backups for all files', 'A'),
(478, 'DCA', NULL, 9, 'What is \"Steganography\"? / \"स्टेग्नोग्राफ़ी\" क्या है?', 'The practice of hiding a secret message within another file, such as an image or audio file', 'The process of encrypting data', 'The act of deleting files to hide information', 'The practice of stealing passwords', 'A'),
(479, 'DCA', NULL, 9, 'What is \"Rootkit\"? / \"रूटकिट\" क्या है?', 'A collection of malicious software designed to provide unauthorized access to a computer while hiding its presence', 'A legitimate system maintenance tool', 'A program used for data recovery', 'A tool used to monitor internet traffic', 'A'),
(480, 'DCA', NULL, 9, 'What is \"DDoS\" attack? / \"DDoS\" हमला क्या है?', 'A type of DoS attack where multiple compromised systems attack a single target, overwhelming it with traffic', 'An attack performed by a single computer', 'A method to steal data from a database', 'A technique to update a website', 'A'),
(481, 'DCA', NULL, 9, 'What is \"Social Engineering\" target? / \"सोशल इंजीनियरिंग\" का लक्ष्य क्या है?', 'To manipulate human psychology to gain access to information or systems', 'To crash the server directly', 'To infect the hardware with a virus', 'To slow down the internet connection', 'A'),
(482, 'DCA', NULL, 9, 'What is \"Hashing Algorithm\"? / \"हैशिंग एल्गोरिदम\" क्या है?', 'A function that converts data into a fixed-size string, used for data integrity verification', 'A method to encrypt data', 'A tool to recover lost passwords', 'A way to delete files', 'A'),
(483, 'DCA', NULL, 9, 'What is \"Digital Forensics\"? / \"डिजिटल फॉरेंसिक\" क्या है?', 'The process of uncovering and interpreting electronic data for use in legal proceedings', 'The act of hacking a computer', 'The process of designing a secure network', 'The practice of backing up data', 'A'),
(484, 'DCA', NULL, 9, 'What is \"White Hat Hacker\"? / \"व्हाइट हैट हैकर\" क्या है?', 'A security professional who hacks systems to find and fix vulnerabilities, with permission', 'A criminal who hacks for personal gain', 'A hacker who works for the government', 'A developer who creates malware', 'A'),
(485, 'DCA', NULL, 9, 'What is \"Black Hat Hacker\"? / \"ब्लैक हैट हैकर\" क्या है?', 'A hacker who gains unauthorized access to systems with malicious intent', 'A hacker who helps companies improve security', 'A student who is learning to code', 'A professional security consultant', 'A'),
(486, 'DCA', NULL, 9, 'What is \"Grey Hat Hacker\"? / \"ग्रे हैट हैकर\" क्या है?', 'A hacker who may violate ethical standards but without malicious intent to cause harm', 'A hacker who always works legally', 'A hacker who is always a criminal', 'A person who never hacks systems', 'A'),
(487, 'DCA', NULL, 9, 'What is \"Security Audit\"? / \"सुरक्षा ऑडिट\" क्या है?', 'A systematic evaluation of the security of a computer system or network', 'The process of deleting files from a server', 'The act of updating software', 'The process of monitoring employees', 'A'),
(488, 'DCA', NULL, 9, 'What is \"Integrity\" in security? / सुरक्षा में \"अखंडता\" (Integrity) क्या है?', 'The assurance that data is accurate and has not been tampered with or modified unauthorized', 'The act of encrypting data', 'The process of backing up data', 'The act of hiding files', 'A'),
(489, 'DCA', NULL, 9, 'What is \"Availability\" in security? / सुरक्षा में \"उपलब्धता\" (Availability) क्या है?', 'The assurance that systems and data are accessible to authorized users when needed', 'The act of deleting files', 'The process of hacking a network', 'The act of encrypting data', 'A'),
(490, 'DCA', NULL, 9, 'What is \"Confidentiality\" in security? / सुरक्षा में \"गोपनीयता\" (Confidentiality) क्या है?', 'The assurance that sensitive information is only accessed by authorized people', 'The process of deleting data', 'The act of hacking a system', 'The process of backing up data', 'A'),
(491, 'DCA', NULL, 9, 'What is \"Session Hijacking\"? / \"सेशन हाइजैकिंग\" क्या है?', 'An attack where the attacker takes control of a legitimate user session', 'The act of crashing a website', 'A method of stealing files from a disk', 'The process of updating software', 'A'),
(492, 'DCA', NULL, 9, 'What is \"SQL Injection\"? / \"SQL इंजेक्शन\" क्या है?', 'An attack that inserts malicious SQL code into input fields to manipulate database queries', 'A method to crash the operating system', 'A technique to delete files from the server', 'A way to intercept email communication', 'A'),
(493, 'DCA', NULL, 9, 'What is \"Cross-Site Scripting\" (XSS)? / \"क्रॉस-साइट स्क्रिप्टिंग\" (XSS) क्या है?', 'An attack where malicious scripts are injected into trusted websites, which then execute in the user’s browser', 'An attack to steal hardware', 'A method to crash the server', 'A technique to encrypt files', 'A'),
(494, 'DCA', NULL, 9, 'What is \"Cookie Theft\"? / \"कुकी चोरी\" क्या है?', 'Stealing session cookies to gain unauthorized access to a user account', 'The act of deleting browsing history', 'A method to slow down the internet', 'The process of backing up cookies', 'A'),
(495, 'DCA', NULL, 9, 'What is \"Brute Force Attack\"? / \"ब्रूट फोर्स अटैक\" क्या है?', 'An attack that tries all possible combinations of passwords to gain unauthorized access', 'A method to crash the server', 'An attack that infects files with viruses', 'A technique to steal physical hardware', 'A'),
(496, 'DCA', NULL, 9, 'What is \"Backdoor\"? / \"बैकडोर\" क्या है?', 'A secret method of bypassing normal authentication in a computer system', 'A legitimate system feature', 'A tool used for monitoring networks', 'A software for data recovery', 'A'),
(497, 'DCA', NULL, 9, 'What is \"Trojan Horse\"? / \"ट्रोजन हॉर्स\" क्या है?', 'A type of malware that disguises itself as legitimate software to gain access to a system', 'A tool for managing files', 'A software update', 'A hardware component', 'A'),
(498, 'DCA', NULL, 9, 'What is \"Worm\"? / \"वॉर्म\" क्या है?', 'Malicious software that replicates itself to spread to other computers on a network', 'A tool for cleaning disks', 'A software update', 'A hardware component', 'A'),
(499, 'DCA', NULL, 9, 'What is \"Social Media Security\"? / \"सोशल मीडिया सुरक्षा\" क्या है?', 'Protecting social media accounts from unauthorized access and misuse', 'The act of hacking social media platforms', 'A tool to increase followers', 'The process of deleting accounts', 'A'),
(500, 'DCA', NULL, 9, 'What is \"Information Security Policy\"? / \"सूचना सुरक्षा नीति\" क्या है?', 'A set of rules and procedures designed to protect an organization’s information assets', 'A guide to using software', 'A list of employees', 'A file storage plan', 'A'),
(501, 'DCA', NULL, 9, 'What is \"Compliance\"? / \"अनुपालन\" (Compliance) क्या है?', 'Following the laws, regulations, and standards relevant to an organization’s operations', 'The act of breaking the law', 'The process of hacking systems', 'The act of deleting files', 'A'),
(502, 'DCA', NULL, 9, 'What is \"Threat Intelligence\"? / \"थ्रेट इंटेलिजेंस\" क्या है?', 'Information gathered about potential threats and cyber attackers to improve security', 'The act of spreading viruses', 'A tool to crash servers', 'The process of stealing data', 'A'),
(603, 'DCA', NULL, 2, 'Windows ऑपरेटिंग सिस्टम में \"Control Panel\" का मुख्य कार्य क्या है?', 'सिस्टम की सेटिंग्स को कॉन्फ़िगर और कस्टमाइज़ करना', 'इंटरनेट ब्राउज़िंग करना', 'दस्तावेज़ संपादित करना', 'गेम खेलना', 'A'),
(604, 'DCA', NULL, 2, 'MS Word में \"Mail Merge\" सुविधा का उपयोग किस लिए किया जाता है?', 'एक ही पत्र को कई प्राप्तकर्ताओं को अलग-अलग नाम/पते के साथ भेजने के लिए', 'ईमेल को डिलीट करने के लिए', 'फाइल को पीडीएफ में बदलने के लिए', 'टेबल बनाने के लिए', 'A'),
(605, 'DCA', NULL, 2, 'MS Excel में \"VLOOKUP\" फ़ंक्शन का प्राथमिक उपयोग क्या है?', 'एक तालिका में डेटा खोजने और उसे संबंधित मान के साथ लाने के लिए', 'फॉन्ट का आकार बदलने के लिए', 'ग्राफ बनाने के लिए', 'सिस्टम रीस्टार्ट करने के लिए', 'A'),
(606, 'DCA', NULL, 2, 'Windows में किसी फाइल को स्थायी रूप से (Permanently) डिलीट करने के लिए किस की-कॉम्बिनेशन का उपयोग किया जाता है?', 'Shift + Delete', 'Ctrl + Delete', 'Alt + Delete', 'Backspace + Delete', 'A'),
(607, 'DCA', NULL, 2, 'MS PowerPoint में \"Slide Master\" का क्या महत्व है?', 'पूरी प्रेजेंटेशन की डिज़ाइन और लेआउट को एक साथ नियंत्रित करने के लिए', 'सिर्फ एक स्लाइड का रंग बदलने के लिए', 'वीडियो फाइल जोड़ने के लिए', 'इंटरनेट कनेक्ट करने के लिए', 'A'),
(608, 'DCA', NULL, 2, 'MS Word में \"Format Painter\" का क्या कार्य है?', 'एक टेक्स्ट की फॉर्मेटिंग को कॉपी करके दूसरे टेक्स्ट पर अप्लाई करना', 'टेक्स्ट का फॉन्ट साइज बढ़ाना', 'टेबल को डिलीट करना', 'ईमेल भेजना', 'A'),
(609, 'DCA', NULL, 2, 'MS Excel में सेल (Cell) में फॉर्मूला शुरू करने के लिए किस चिह्न का उपयोग किया जाता है?', '=', '+', '-', '*', 'A'),
(610, 'DCA', NULL, 2, 'Windows Task Manager खोलने की शॉर्टकट की क्या है?', 'Ctrl + Shift + Esc', 'Alt + F4', 'Windows + R', 'Ctrl + Alt + Delete', 'A'),
(611, 'DCA', NULL, 2, 'MS Word में \"Header and Footer\" का उपयोग क्यों किया जाता है?', 'प्रत्येक पृष्ठ के शीर्ष और नीचे जानकारी (जैसे पेज नंबर) डालने के लिए', 'केवल पहली पेज पर लिखने के लिए', 'डॉक्यूमेंट को सुरक्षित करने के लिए', 'ग्राफ बनाने के लिए', 'A'),
(612, 'DCA', NULL, 2, 'MS Excel में \"AutoSum\" बटन का उपयोग क्या है?', 'चुने गए सेल्स की संख्याओं को तेज़ी से जोड़ने के लिए', 'टेक्स्ट को बोल्ड करने के लिए', 'फाइल सेव करने के लिए', 'नया डेटाबेस बनाने के लिए', 'A'),
(613, 'DCA', NULL, 2, 'Windows में \"Recycle Bin\" का मुख्य उद्देश्य क्या है?', 'डिलीट की गई फाइलों को अस्थायी रूप से स्टोर करना', 'वायरस हटाना', 'इंटरनेट चलाना', 'कंप्यूटर की स्पीड बढ़ाना', 'A'),
(614, 'DCA', NULL, 2, 'MS PowerPoint में प्रेजेंटेशन को स्लाइड शो मोड में चलाने की शॉर्टकट की क्या है?', 'F5', 'F1', 'F10', 'Esc', 'A'),
(615, 'DCA', NULL, 2, 'MS Word में पैराग्राफ को \"Justify\" करने का क्या अर्थ है?', 'टेक्स्ट को बाएं और दाएं दोनों किनारों पर बराबर व्यवस्थित करना', 'टेक्स्ट को केवल बीच में करना', 'टेक्स्ट को डिलीट करना', 'टेक्स्ट का रंग बदलना', 'A'),
(616, 'DCA', NULL, 2, 'MS Excel में \'Sheet\' के नाम को बदलने के लिए क्या करना पड़ता है?', 'शीट टैब पर राइट क्लिक करके \"Rename\" चुनें', 'शीट को डिलीट करें', 'नया ग्राफ बनाएं', 'कीबोर्ड से डिलीट दबाएं', 'A'),
(617, 'DCA', NULL, 2, 'Windows में \"Desktop\" पर आइकन्स के आकार को बदलने के लिए क्या करना चाहिए?', 'डेस्कटॉप पर राइट क्लिक करके \"View\" में जाएं', 'सिस्टम शटडाउन करें', 'माउस को डिस्कनेक्ट करें', 'कीबोर्ड की लाइट बंद करें', 'A'),
(618, 'DCA', NULL, 2, 'MS Word में \"Watermark\" का उपयोग क्यों किया जाता है?', 'पृष्ठ के पीछे धुंधला टेक्स्ट या लोगो डालने के लिए', 'फोटो साफ करने के लिए', 'पेज का रंग बदलने के लिए', 'पेज नंबर देने के लिए', 'A'),
(619, 'DCA', NULL, 2, 'MS Excel में एक पूरी कॉलम को सेलेक्ट करने की शॉर्टकट की क्या है?', 'Ctrl + Spacebar', 'Shift + Spacebar', 'Alt + Spacebar', 'Ctrl + A', 'A'),
(620, 'DCA', NULL, 2, 'MS PowerPoint में ट्रांज़िशन (Transition) क्या है?', 'एक स्लाइड से दूसरी स्लाइड में जाने का विज़ुअल इफ़ेक्ट', 'फोटो जोड़ने का तरीका', 'टेबल बनाने का टूल', 'ईमेल भेजने का टूल', 'A'),
(621, 'DCA', NULL, 2, 'Windows में \"Device Manager\" का कार्य क्या है?', 'हार्डवेयर घटकों (जैसे ड्राइवर्स) को प्रबंधित और अपडेट करना', 'सॉफ्टवेयर डाउनलोड करना', 'इंटरनेट की गति बढ़ाना', 'पावर ऑफ करना', 'A'),
(622, 'DCA', NULL, 2, 'MS Word में \"Hyperlink\" का उपयोग क्या है?', 'दस्तावेज़ में लिंक बनाकर दूसरे वेब पेज या फाइल को खोलना', 'पेज को प्रिंट करना', 'टेक्स्ट को कॉपी करना', 'डॉक्यूमेंट सेव करना', 'A'),
(623, 'DCA', NULL, 2, 'MS Excel में \"Average\" फ़ंक्शन का उपयोग क्या है?', 'चुने गए सेल्स की संख्याओं का औसत (Mean) निकालने के लिए', 'योग करने के लिए', 'डेटा डिलीट करने के लिए', 'कॉलम डिलीट करने के लिए', 'A'),
(624, 'DCA', NULL, 2, 'Windows में \"File Explorer\" खोलने की शॉर्टकट की क्या है?', 'Windows + E', 'Windows + R', 'Windows + F', 'Windows + L', 'A'),
(625, 'DCA', NULL, 2, 'MS Word में \"Spelling and Grammar\" चेक करने की शॉर्टकट की क्या है?', 'F7', 'F2', 'F5', 'F12', 'A'),
(626, 'DCA', NULL, 2, 'MS Excel में \"Freeze Panes\" का क्या उपयोग है?', 'पंक्तियों या स्तंभों को स्क्रॉल करते समय दृश्यमान बनाए रखने के लिए', 'शीट लॉक करने के लिए', 'ग्राफ को ज़ूम करने के लिए', 'सेल का रंग बदलने के लिए', 'A'),
(627, 'DCA', NULL, 2, 'Windows ऑपरेटिंग सिस्टम किस कंपनी द्वारा विकसित किया गया है?', 'Microsoft', 'Apple', 'Google', 'IBM', 'A'),
(628, 'DCA', NULL, 2, 'MS PowerPoint में \"Custom Animation\" क्या है?', 'स्लाइड पर टेक्स्ट या इमेज को मूव करने का इफ़ेक्ट', 'प्रेजेंटेशन का शीर्षक', 'साउंड इफ़ेक्ट', 'ग्राफ', 'A'),
(629, 'DCA', NULL, 2, 'MS Word में \"Page Break\" क्या करता है?', 'मौजूदा पेज को खत्म करके नए पेज पर कर्सर ले जाता है', 'फाइल डिलीट करता है', 'टेबल बनाता है', 'फॉन्ट बदलता है', 'A'),
(630, 'DCA', NULL, 2, 'MS Excel में \"Conditional Formatting\" क्या है?', 'किसी शर्त के आधार पर सेल को हाईलाइट करना', 'टेक्स्ट डिलीट करना', 'फाइल सेव करना', 'फॉर्मूला बनाना', 'A'),
(631, 'DCA', NULL, 2, 'Windows में \"Lock Screen\" को सक्रिय करने की शॉर्टकट की क्या है?', 'Windows + L', 'Windows + D', 'Windows + M', 'Windows + K', 'A'),
(632, 'DCA', NULL, 2, 'MS Word में \"Table of Contents\" कैसे बनाया जाता है?', 'Heading स्टाइल्स का उपयोग करके', 'टेक्स्ट टाइप करके', 'फोटो लगाकर', 'पेज नंबर डालकर', 'A'),
(633, 'DCA', NULL, 2, 'MS Excel में \"Absolute Reference\" के लिए किस प्रतीक का उपयोग किया जाता है?', '$ (जैसे $A$1)', '@', '#', '&', 'A'),
(634, 'DCA', NULL, 2, 'MS PowerPoint में स्लाइड्स का लेआउट बदलने के लिए किस ऑप्शन का उपयोग करें?', 'Layout', 'Design', 'Transition', 'Animation', 'A'),
(635, 'DCA', NULL, 2, 'Windows में \"System Restore\" का उपयोग कब किया जाता है?', 'कंप्यूटर को पुरानी स्थिति में वापस लाने के लिए', 'नेटवर्क ठीक करने के लिए', 'कीबोर्ड साफ करने के लिए', 'डिस्प्ले बदलने के लिए', 'A'),
(636, 'DCA', NULL, 2, 'MS Word में \"Document properties\" में क्या देखा जा सकता है?', 'फाइल का लेखक, शीर्षक और निर्माण तिथि', 'सिर्फ फाइल का नाम', 'सिर्फ फाइल का आकार', 'इंटरनेट की जानकारी', 'A'),
(637, 'DCA', NULL, 2, 'MS Excel में \"Sort\" फीचर क्या करता है?', 'डेटा को व्यवस्थित (क्रमबद्ध) करना', 'डेटा डिलीट करना', 'फॉर्मूला जोड़ना', 'ग्राफ बनाना', 'A'),
(638, 'DCA', NULL, 2, 'Windows में \"Command Prompt\" (CMD) का क्या उपयोग है?', 'टेक्स्ट आधारित कमांड से कंप्यूटर नियंत्रित करना', 'वीडियो चलाना', 'फोटो एडिट करना', 'टाइपिंग करना', 'A'),
(639, 'DCA', NULL, 2, 'MS Word में \"WordArt\" क्या है?', 'टेक्स्ट को स्टाइलिश और कलात्मक रूप देने का फीचर', 'पेज नंबर', 'एक फोटो', 'एक टेबल', 'A'),
(640, 'DCA', NULL, 2, 'MS Excel में \"Max\" फ़ंक्शन क्या करता है?', 'चुने गए डेटा में सबसे बड़ी संख्या ज्ञात करता है', 'सबसे छोटी संख्या ज्ञात करता है', 'योग करता है', 'औसत निकालता है', 'A'),
(641, 'DCA', NULL, 2, 'Windows में \"Screen Capture\" लेने के लिए किसका प्रयोग करें?', 'Snipping Tool या Print Screen', 'सिस्टम शटडाउन', 'रिस्टार्ट', 'लॉक', 'A'),
(642, 'DCA', NULL, 2, 'MS Word में \"Print Preview\" का क्या लाभ है?', 'प्रिंट निकालने से पहले डॉक्यूमेंट को स्क्रीन पर देखना', 'फाइल को ईमेल करना', 'टेक्स्ट को डिलीट करना', 'फॉन्ट चेंज करना', 'A'),
(643, 'DCA', NULL, 2, 'MS Excel में \"Pivot Table\" का उपयोग क्या है?', 'बड़े डेटा को संक्षिप्त और विश्लेषित करने के लिए', 'सिर्फ ग्राफ बनाने के लिए', 'टेक्स्ट एडिट करने के लिए', 'फॉर्मूला जोड़ने के लिए', 'A'),
(644, 'DCA', NULL, 2, 'Windows में \"Taskbar\" की स्थिति कहाँ बदली जा सकती है?', 'टास्कबार को लॉक हटाकर ड्रैग करके', 'सिस्टम में कहीं नहीं', 'कीबोर्ड से', 'माउस बिना', 'A'),
(645, 'DCA', NULL, 2, 'MS Word में \"Track Changes\" सुविधा का क्या काम है?', 'दस्तावेज़ में किए गए बदलावों को रिकॉर्ड करना', 'टाइपिंग की गति मापना', 'पेज डिलीट करना', 'इंटरनेट ब्राउज़िंग', 'A'),
(646, 'DCA', NULL, 2, 'MS Excel में \"Count\" फ़ंक्शन क्या गिनता है?', 'केवल उन सेल्स को जिनमें संख्याएँ होती हैं', 'सभी सेल्स को', 'केवल खाली सेल्स को', 'केवल फॉर्मूला को', 'A'),
(647, 'DCA', NULL, 2, 'Windows में \"Settings\" एप खोलने के लिए किस शॉर्टकट का उपयोग करें?', 'Windows + I', 'Windows + S', 'Windows + P', 'Windows + D', 'A'),
(648, 'DCA', NULL, 2, 'MS Word में \"Find and Replace\" की शॉर्टकट क्या है?', 'Ctrl + H', 'Ctrl + F', 'Ctrl + R', 'Ctrl + D', 'A'),
(649, 'DCA', NULL, 2, 'MS Excel में \"Merge and Center\" क्या करता है?', 'कई सेल्स को जोड़कर टेक्स्ट को बीच में लाना', 'सेल को डिलीट करना', 'कॉलम को छिपाना', 'फाइल सेव करना', 'A'),
(650, 'DCA', NULL, 2, 'Windows में \"Accessibility\" सेटिंग्स का क्या उपयोग है?', 'विशेष आवश्यकता वाले उपयोगकर्ताओं के लिए सुविधाएँ', 'इंटरनेट स्पीड', 'गेम सेटिंग', 'स्क्रीन का रंग बदलना', 'A'),
(651, 'DCA', NULL, 2, 'MS Word में \"Bulleted List\" क्या है?', 'आइटम्स को प्रतीकों के साथ क्रमबद्ध करना', 'पेज नंबर', 'एक टेबल', 'एक ग्राफ', 'A'),
(652, 'DCA', NULL, 2, 'MS Excel में \"Save As\" का क्या अर्थ है?', 'फाइल को नए नाम या फॉर्मेट में सुरक्षित करना', 'फाइल डिलीट करना', 'फाइल प्रिंट करना', 'फाइल ईमेल करना', 'A'),
(753, 'DCA', NULL, 1, 'What is the primary function of a \"box-sizing\" property in CSS? / सीएसएस (CSS) में \"box-sizing\" प्रॉपर्टी का मुख्य कार्य क्या है?', 'It changes the font color of the text. / यह टेक्स्ट का फॉन्ट रंग बदलता है।', 'It defines how the width and height of an element are calculated. / यह परिभाषित करता है कि किसी तत्व की चौड़ाई और ऊँचाई की गणना कैसे की जाती है।', 'It sets the background image of the element. / यह तत्व की बैकग्राउंड इमेज सेट करता है।', 'It aligns the text to the center. / यह टेक्स्ट को सेंटर में अलाइन करता है।', 'B'),
(754, 'DCA', NULL, 1, 'Which protocol is used for secure communication over the World Wide Web? / वर्ल्ड वाइड वेब पर सुरक्षित संचार के लिए किस प्रोटोकॉल का उपयोग किया जाता है?', 'HTTP', 'FTP', 'HTTPS', 'SMTP', 'C'),
(755, 'DCA', NULL, 1, 'Which of the following is considered as a volatile memory in a computer system? / कंप्यूटर सिस्टम में निम्नलिखित में से किसे वोलाटाइल (अस्थायी) मेमोरी माना जाता है?', 'Read Only Memory (ROM) / रीड ओनली मेमोरी', 'Random Access Memory (RAM) / रैंडम एक्सेस मेमोरी', 'Hard Disk Drive (HDD) / हार्ड डिस्क ड्राइव', 'Solid State Drive (SSD) / सॉलिड स्टेट ड्राइव', 'B'),
(756, 'DCA', NULL, 1, 'What is the full form of the term \"URL\" used in web browsing? / वेब ब्राउज़िंग में उपयोग किए जाने वाले शब्द \"URL\" का पूर्ण रूप क्या है?', 'Uniform Resource Locator / यूनिफ़ॉर्म रिसोर्स लोकेटर', 'Universal Resource Link / यूनिवर्सल रिसोर्स लिंक', 'Uniform Resource List / यूनिफ़ॉर्म रिसोर्स लिस्ट', 'Unified Resource Location / यूनिफ़ाइड रिसोर्स लोकेशन', 'A'),
(757, 'DCA', NULL, 1, 'Which device is responsible for connecting different networks and routing data packets? / विभिन्न नेटवर्क को जोड़ने और डेटा पैकेट को रूट करने के लिए कौन सा उपकरण जिम्मेदार है?', 'Hub / हब', 'Switch / स्विच', 'Router / राउटर', 'Bridge / ब्रिज', 'C'),
(758, 'DCA', NULL, 1, 'What is the purpose of a Firewall in a computer network? / कंप्यूटर नेटवर्क में फायरवॉल का उद्देश्य क्या है?', 'To increase the speed of the internet. / इंटरनेट की गति बढ़ाने के लिए।', 'To protect the network from unauthorized access. / नेटवर्क को अनधिकृत पहुंच से बचाने के लिए।', 'To store large amounts of data. / बड़ी मात्रा में डेटा स्टोर करने के लिए।', 'To create web pages. / वेब पेज बनाने के लिए।', 'B'),
(759, 'DCA', NULL, 1, 'Which file format is most commonly used for storing digital images? / डिजिटल छवियों को स्टोर करने के लिए सबसे अधिक उपयोग किया जाने वाला फाइल फॉर्मेट कौन सा है?', 'MP3', 'JPEG', 'AVI', 'DOCX', 'B'),
(760, 'DCA', NULL, 1, 'What is the function of an Operating System in a computer? / कंप्यूटर में ऑपरेटिंग सिस्टम का क्या कार्य है?', 'To perform mathematical calculations. / गणितीय गणना करना।', 'To manage hardware and software resources. / हार्डवेयर और सॉफ्टवेयर संसाधनों का प्रबंधन करना।', 'To design websites. / वेबसाइट डिजाइन करना।', 'To play high-end games. / हाई-एंड गेम खेलना।', 'B'),
(761, 'DCA', NULL, 1, 'Which keyboard shortcut is used to copy selected text? / चयनित टेक्स्ट को कॉपी करने के लिए किस कीबोर्ड शॉर्टकट का उपयोग किया जाता है?', 'Ctrl + C', 'Ctrl + V', 'Ctrl + X', 'Ctrl + Z', 'A'),
(762, 'DCA', NULL, 1, 'What is meant by the term \"Phishing\" in cyber security? / साइबर सुरक्षा में \"फिशिंग\" शब्द का क्या अर्थ है?', 'Deleting files from a system. / सिस्टम से फाइलें हटाना।', 'Attempting to steal sensitive information like passwords. / पासवर्ड जैसी संवेदनशील जानकारी चुराने का प्रयास करना।', 'Updating the system software. / सिस्टम सॉफ्टवेयर को अपडेट करना।', 'Creating backup of data. / डेटा का बैकअप बनाना।', 'B'),
(763, 'DCA', NULL, 1, 'Which of the following is an example of a non-volatile memory? / निम्नलिखित में से कौन सा नॉन-वोलाटाइल (स्थायी) मेमोरी का उदाहरण है?', 'RAM', 'Cache Memory', 'ROM', 'Registers', 'C'),
(764, 'DCA', NULL, 1, 'What is a \"Bug\" in computer software? / कंप्यूटर सॉफ्टवेयर में \"बग\" क्या है?', 'A new feature added by the developer. / डेवलपर द्वारा जोड़ा गया एक नया फीचर।', 'An error or flaw in the code causing unexpected results. / कोड में एक त्रुटि या खामी जिसके कारण अप्रत्याशित परिणाम मिलते हैं।', 'A type of storage device. / एक प्रकार का स्टोरेज डिवाइस।', 'A tool to increase processing speed. / प्रोसेसिंग गति बढ़ाने का एक टूल।', 'B'),
(765, 'DCA', NULL, 1, 'Which of these is a popular Web Browser? / इनमें से कौन सा एक लोकप्रिय वेब ब्राउज़र है?', 'MS Office', 'Adobe Photoshop', 'Google Chrome', 'MySQL', 'C'),
(766, 'DCA', NULL, 1, 'What is Cloud Computing? / क्लाउड कंप्यूटिंग क्या है?', 'Using local storage for all files. / सभी फाइलों के लिए लोकल स्टोरेज का उपयोग करना।', 'Storing and accessing data over the internet instead of a local drive. / स्थानीय ड्राइव के बजाय इंटरनेट पर डेटा स्टोर करना और एक्सेस करना।', 'Designing hardware components. / हार्डवेयर घटकों को डिजाइन करना।', 'A method to speed up the processor. / प्रोसेसर की गति बढ़ाने की एक विधि।', 'B'),
(767, 'DCA', NULL, 1, 'What is the unit used to measure processor speed? / प्रोसेसर की गति मापने के लिए किस इकाई का उपयोग किया जाता है?', 'Gigahertz (GHz) / गीगाहर्ट्ज़', 'Gigabytes (GB) / गीगाबाइट्स', 'Terabytes (TB) / टेराबाइट्स', 'Megabits (Mb) / मेगाबिट्स', 'A'),
(768, 'DCA', NULL, 1, 'What does HTML stand for? / HTML का पूर्ण रूप क्या है?', 'HyperText Markup Language / हाइपरटेक्स्ट मार्कअप लैंग्वेज', 'HighText Machine Language / हाईटेक्स्ट मशीन लैंग्वेज', 'HyperTool Markup Language / हाइपरटूल मार्कअप लैंग्वेज', 'HyperText Marking Language / हाइपरटेक्स्ट मार्किंग लैंग्वेज', 'A'),
(769, 'DCA', NULL, 1, 'Which is considered an Input Device? / किसे इनपुट डिवाइस माना जाता है?', 'Monitor / मॉनिटर', 'Printer / प्रिंटर', 'Keyboard / कीबोर्ड', 'Speaker / स्पीकर', 'C'),
(770, 'DCA', NULL, 1, 'What is the full form of ISP? / ISP का पूर्ण रूप क्या है?', 'Internet Service Provider / इंटरनेट सर्विस प्रोवाइडर', 'Internal System Processor / इंटरनल सिस्टम प्रोसेसर', 'Internet Security Protocol / इंटरनेट सिक्योरिटी प्रोटोकॉल', 'International Standard Protocol / इंटरनेशनल स्टैंडर्ड प्रोटोकॉल', 'A'),
(771, 'DCA', NULL, 1, 'Which protocol is standard for sending Emails? / ईमेल भेजने के लिए कौन सा प्रोटोकॉल मानक है?', 'HTTP', 'FTP', 'SMTP', 'TCP', 'C'),
(772, 'DCA', NULL, 1, 'What is the full form of WWW? / WWW का पूर्ण रूप क्या है?', 'World Wide Web / वर्ल्ड वाइड वेब', 'World Web Wide / वर्ल्ड वेब वाइड', 'Wide World Web / वाइड वर्ल्ड वेब', 'Web World Wide / वेब वर्ल्ड वाइड', 'A'),
(773, 'DCA', NULL, 1, 'Which tag in HTML is used for a line break? / HTML में लाइन ब्रेक के लिए किस टैग का उपयोग किया जाता है?', '<br>', '<b>', '<i>', '<p>', 'A'),
(774, 'DCA', NULL, 1, 'What is the shortcut to save a document in MS Word? / MS Word में दस्तावेज़ को सेव करने के लिए शॉर्टकट क्या है?', 'Ctrl + S', 'Ctrl + P', 'Ctrl + O', 'Ctrl + N', 'A'),
(775, 'DCA', NULL, 1, 'Which memory stores the BIOS? / BIOS को कौन सी मेमोरी स्टोर करती है?', 'RAM', 'ROM', 'Cache', 'Hard Disk', 'B'),
(776, 'DCA', NULL, 1, 'What is a \"Search Engine\"? / \"सर्च इंजन\" क्या है?', 'A type of Web Browser. / एक प्रकार का वेब ब्राउज़र।', 'A hardware device to connect to internet. / इंटरनेट से जुड़ने के लिए एक हार्डवेयर उपकरण।', 'A program to search web content. / वेब सामग्री खोजने के लिए एक प्रोग्राम।', 'A language for programming. / प्रोग्रामिंग के लिए एक भाषा।', 'C'),
(777, 'DCA', NULL, 1, 'Which key is used to delete text to the right of the cursor? / कर्सर के दाईं ओर के टेक्स्ट को मिटाने के लिए किस कुंजी का उपयोग किया जाता है?', 'Backspace / बैकस्पेस', 'Delete / डिलीट', 'Shift / शिफ्ट', 'Enter / एंटर', 'B'),
(778, 'DCA', NULL, 1, 'What is the full form of PDF? / PDF का पूर्ण रूप क्या है?', 'Portable Document Format / पोर्टेबल डॉक्यूमेंट फॉर्मेट', 'Printable Document File / प्रिंटेबल डॉक्यूमेंट फाइल', 'Public Document Format / पब्लिक डॉक्यूमेंट फॉर्मेट', 'Private Document File / प्राइवेट डॉक्यूमेंट फाइल', 'A'),
(779, 'DCA', NULL, 1, 'What is Multi-tasking? / मल्टी-टास्किंग क्या है?', 'Performing a single task. / एक कार्य करना।', 'Performing multiple tasks simultaneously. / एक साथ कई कार्य करना।', 'Shutting down the PC. / पीसी बंद करना।', 'Printing a document. / दस्तावेज़ प्रिंट करना।', 'B');
INSERT INTO `questions` (`id`, `course`, `subject_name`, `subject_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(780, 'DCA', NULL, 1, 'What is the purpose of the Control Panel? / कंट्रोल पैनल का क्या उद्देश्य है?', 'To configure system settings. / सिस्टम सेटिंग्स कॉन्फ़िगर करने के लिए।', 'To browse the internet. / इंटरनेट ब्राउज़ करने के लिए।', 'To edit images. / चित्र संपादित करने के लिए।', 'To play games. / गेम खेलने के लिए।', 'A'),
(781, 'DCA', NULL, 1, 'What is an Analog signal? / एनालॉग सिग्नल क्या है?', 'A continuous wave signal. / एक निरंतर तरंग सिग्नल।', 'Digital data in bits. / बिट्स में डिजिटल डेटा।', 'Text files only. / केवल टेक्स्ट फाइलें।', 'Empty data. / खाली डेटा।', 'A'),
(782, 'DCA', NULL, 1, 'Which device is used for video conferencing? / वीडियो कॉन्फ्रेंसिंग के लिए किस उपकरण का उपयोग किया जाता है?', 'Printer / प्रिंटर', 'Webcam / वेबकैम', 'Scanner / स्कैनर', 'Joystick / जॉयस्टिक', 'B'),
(783, 'DCA', NULL, 1, 'What is a domain name? / डोमेन नेम क्या है?', 'A computer name. / एक कंप्यूटर का नाम।', 'A unique address for a website. / वेबसाइट का एक अद्वितीय पता।', 'A username. / एक उपयोगकर्ता नाम।', 'A software type. / एक सॉफ्टवेयर प्रकार।', 'B'),
(784, 'DCA', NULL, 1, 'What is Bluetooth? / ब्लूटूथ क्या है?', 'A wireless communication technology. / एक वायरलेस संचार तकनीक।', 'A printing device. / एक प्रिंटिंग डिवाइस।', 'A scanner device. / एक स्कैनर डिवाइस।', 'An operating system. / एक ऑपरेटिंग सिस्टम।', 'A'),
(785, 'DCA', NULL, 1, 'What is the shortcut to Undo an action? / किसी क्रिया को पूर्ववत (Undo) करने के लिए शॉर्टकट क्या है?', 'Ctrl + Z', 'Ctrl + Y', 'Ctrl + U', 'Ctrl + A', 'A'),
(786, 'DCA', NULL, 1, 'Which is an Output Device? / आउटपुट डिवाइस कौन सा है?', 'Keyboard / कीबोर्ड', 'Mouse / माउस', 'Printer / प्रिंटर', 'Scanner / स्कैनर', 'C'),
(787, 'DCA', NULL, 1, 'What is a Spreadsheet? / स्प्रेडशीट क्या है?', 'A text editor. / एक टेक्स्ट एडिटर।', 'A calculation and data analysis tool. / गणना और डेटा विश्लेषण के लिए एक उपकरण।', 'An OS. / एक ओएस।', 'A browser. / एक ब्राउज़र।', 'B'),
(788, 'DCA', NULL, 1, 'What is a Local Area Network (LAN)? / लोकल एरिया नेटवर्क (LAN) क्या है?', 'Global network coverage. / वैश्विक नेटवर्क कवरेज।', 'Limited area coverage. / सीमित क्षेत्र कवरेज।', 'Satellite communication. / उपग्रह संचार।', 'None. / कोई नहीं।', 'B'),
(789, 'DCA', NULL, 1, 'What is a Modem? / मॉडेम क्या है?', 'A device that modulates/demodulates signals. / संकेतों को मॉड्युलेट/डीमॉड्युलेट करने वाला उपकरण।', 'A memory storage device. / एक मेमोरी स्टोरेज डिवाइस।', 'A monitor. / एक मॉनिटर।', 'A type of mouse. / एक प्रकार का माउस।', 'A'),
(790, 'DCA', NULL, 1, 'What is Wi-Fi? / वाई-फाई क्या है?', 'Wired internet connection. / वायर्ड इंटरनेट कनेक्शन।', 'Wireless internet connection. / वायरलेस इंटरनेट कनेक्शन।', 'A cable type. / एक केबल प्रकार।', 'A printer brand. / एक प्रिंटर ब्रांड।', 'B'),
(791, 'DCA', NULL, 1, 'What is the full form of SQL? / SQL का पूर्ण रूप क्या है?', 'Structured Query Language / स्ट्रक्चर्ड क्वेरी लैंग्वेज', 'Simple Query Language / सिंपल क्वेरी लैंग्वेज', 'Standard Query List / स्टैंडर्ड क्वेरी लिस्ट', 'System Query Language / सिस्टम क्वेरी लैंग्वेज', 'A'),
(792, 'DCA', NULL, 1, 'What is an Icon? / आइकॉन क्या है?', 'A small graphical representation of a program. / किसी प्रोग्राम का छोटा ग्राफिकल निरूपण।', 'A hardware part. / एक हार्डवेयर भाग।', 'A processor unit. / एक प्रोसेसर यूनिट।', 'A printer device. / एक प्रिंटर डिवाइस।', 'A'),
(793, 'DCA', NULL, 1, 'Which menu usually contains file options? / किस मेनू में आमतौर पर फाइल विकल्प होते हैं?', 'Edit', 'File', 'View', 'Tools', 'B'),
(794, 'DCA', NULL, 1, 'What is a password? / पासवर्ड क्या है?', 'A security key to protect access. / पहुंच सुरक्षित करने के लिए एक सुरक्षा कुंजी।', 'A common file name. / एक सामान्य फाइल नाम।', 'A folder type. / एक फोल्डर प्रकार।', 'A hardware device. / एक हार्डवेयर डिवाइस।', 'A'),
(795, 'DCA', NULL, 1, 'What is a Computer Virus? / कंप्यूटर वायरस क्या है?', 'A software that damages system files. / सिस्टम फाइलों को नुकसान पहुँचाने वाला सॉफ्टवेयर।', 'A system utility tool. / एक सिस्टम यूटिलिटी टूल।', 'A hardware component. / एक हार्डवेयर घटक।', 'A memory storage unit. / एक मेमोरी स्टोरेज यूनिट।', 'A'),
(796, 'DCA', NULL, 1, 'Which is a common Text Editor? / एक सामान्य टेक्स्ट एडिटर कौन सा है?', 'MS Paint', 'Notepad', 'MS Excel', 'Adobe Photoshop', 'B'),
(797, 'DCA', NULL, 1, 'What is an Operating System (OS)? / ऑपरेटिंग सिस्टम (OS) क्या है?', 'Software that manages computer hardware and services. / कंप्यूटर हार्डवेयर और सेवाओं का प्रबंधन करने वाला सॉफ्टवेयर।', 'A gaming device. / एक गेमिंग डिवाइस।', 'A web browser. / एक वेब ब्राउज़र।', 'A piece of hardware. / हार्डवेयर का एक टुकड़ा।', 'A'),
(798, 'DCA', NULL, 1, 'What does the \"Ctrl+A\" command do? / \"Ctrl+A\" कमांड क्या करती है?', 'Selects all text/items. / सभी टेक्स्ट/आइटम का चयन करती है।', 'Copies text. / टेक्स्ट कॉपी करती है।', 'Pastes text. / टेक्स्ट पेस्ट करती है।', 'Deletes text. / टेक्स्ट डिलीट करती है।', 'A'),
(799, 'DCA', NULL, 1, 'Which of these is a storage device? / इनमें से कौन सा स्टोरेज डिवाइस है?', 'Monitor', 'Hard Disk', 'Mouse', 'Microphone', 'B'),
(800, 'DCA', NULL, 1, 'What is the internet? / इंटरनेट क्या है?', 'A private network for one computer. / एक कंप्यूटर के लिए निजी नेटवर्क।', 'A global network of interconnected computers. / आपस में जुड़े कंप्यूटरों का एक वैश्विक नेटवर्क।', 'An application software. / एक एप्लीकेशन सॉफ्टवेयर।', 'A type of hardware. / एक प्रकार का हार्डवेयर।', 'B'),
(801, 'DCA', NULL, 1, 'What is a pixel? / पिक्सेल क्या है?', 'The smallest unit of a digital image. / एक डिजिटल छवि की सबसे छोटी इकाई।', 'A type of memory. / एक प्रकार की मेमोरी।', 'A network cable. / एक नेटवर्क केबल।', 'An operating system. / एक ऑपरेटिंग सिस्टम।', 'A'),
(802, 'DCA', NULL, 1, 'What is \"Backup\"? / \"बैकअप\" क्या है?', 'Deleting old files. / पुरानी फाइलें हटाना।', 'Making a copy of data for recovery. / रिकवरी के लिए डेटा की एक प्रति बनाना।', 'Scanning for viruses. / वायरस के लिए स्कैन करना।', 'Installing new software. / नया सॉफ्टवेयर इंस्टॉल करना।', 'B'),
(803, 'DCA', NULL, 7, 'What is the full form of Tally? / टैली का पूर्ण रूप क्या है?', 'Total Accounting Leading List Year / टोटल अकाउंटिंग लीडिंग लिस्ट ईयर', 'Transactions Allowed in Linear Line Yards / ट्रांजेक्शन अलाउड इन लीनियर लाइन यार्ड्स', 'Transactions Allowed in a Linear Line Yard / ट्रांजेक्शन अलाउड इन अ लीनियर लाइन यार्ड', 'None of the above / उपरोक्त में से कोई नहीं', 'C'),
(804, 'DCA', NULL, 7, 'Which key is used to shut down the Tally company? / टैली कंपनी को बंद (Shut Company) करने के लिए किस कुंजी का उपयोग किया जाता है?', 'Alt + F1', 'Ctrl + F1', 'Alt + F3', 'F1', 'A'),
(805, 'DCA', NULL, 7, 'What is a Ledger in Tally? / टैली में लेजर क्या है?', 'A record of an account head. / अकाउंट हेड का रिकॉर्ड।', 'A type of group. / एक प्रकार का ग्रुप।', 'A financial report. / एक वित्तीय रिपोर्ट।', 'A voucher type. / एक वाउचर प्रकार।', 'A'),
(806, 'DCA', NULL, 7, 'Which account group is \"Cash\"? / \"कैश\" (Cash) किस अकाउंट ग्रुप के अंतर्गत आता है?', 'Bank Account / बैंक अकाउंट', 'Cash-in-hand / कैश-इन-हैंड', 'Current Assets / करंट एसेट्स', 'Suspense Account / सस्पेंस अकाउंट', 'B'),
(807, 'DCA', NULL, 7, 'What is the shortcut for \"Voucher Entry\" in Tally? / टैली में \"वाउचर एंट्री\" के लिए शॉर्टकट क्या है?', 'V', 'E', 'A', 'D', 'A'),
(808, 'DCA', NULL, 7, 'What is a Balance Sheet? / बैलेंस शीट क्या है?', 'A list of all transactions. / सभी लेन-देन की सूची।', 'A statement showing financial position. / वित्तीय स्थिति दर्शाने वाला विवरण।', 'A record of cash expenses. / नकद खर्चों का रिकॉर्ड।', 'A list of employees. / कर्मचारियों की सूची।', 'B'),
(809, 'DCA', NULL, 7, 'What is Capital Account? / कैपिटल अकाउंट (Capital Account) क्या है?', 'Money invested by the owner. / मालिक द्वारा निवेशित धन।', 'Money spent on expenses. / खर्चों पर खर्च किया गया धन।', 'Income from sales. / बिक्री से आय।', 'Money borrowed from a bank. / बैंक से लिया गया ऋण।', 'A'),
(810, 'DCA', NULL, 7, 'Which key is used for Contra Voucher? / कॉन्ट्रा वाउचर (Contra Voucher) के लिए किस कुंजी का उपयोग किया जाता है?', 'F3', 'F4', 'F5', 'F6', 'B'),
(811, 'DCA', NULL, 7, 'What is the full form of GST? / GST का पूर्ण रूप क्या है?', 'Goods and Services Tax / गुड्स एंड सर्विसेज टैक्स', 'Good Sales Tax / गुड सेल्स टैक्स', 'Government Sales Tax / गवर्नमेंट सेल्स टैक्स', 'General Service Tax / जनरल सर्विस टैक्स', 'A'),
(812, 'DCA', NULL, 7, 'Which voucher is used for payment entries? / पेमेंट एंट्री के लिए किस वाउचर का उपयोग किया जाता है?', 'F4', 'F5', 'F6', 'F7', 'B'),
(813, 'DCA', NULL, 7, 'What is the group for \"Bank Account\"? / \"बैंक अकाउंट\" के लिए ग्रुप क्या है?', 'Bank Accounts / बैंक अकाउंट्स', 'Current Liabilities / करंट लायबिलिटीज', 'Direct Expenses / डायरेक्ट एक्सपेंसेस', 'Indirect Incomes / इनडायरेक्ट इनकम्स', 'A'),
(814, 'DCA', NULL, 7, 'What is Sales Voucher shortcut? / सेल्स वाउचर (Sales Voucher) का शॉर्टकट क्या है?', 'F8', 'F9', 'F7', 'F6', 'A'),
(815, 'DCA', NULL, 7, 'What is Purchase Voucher shortcut? / परचेज वाउचर (Purchase Voucher) का शॉर्टकट क्या है?', 'F8', 'F9', 'F10', 'F7', 'B'),
(816, 'DCA', NULL, 7, 'What is a Debit Note? / डेबिट नोट (Debit Note) क्या है?', 'Used for purchase return. / परचेज रिटर्न के लिए उपयोग किया जाता है।', 'Used for sales return. / सेल्स रिटर्न के लिए उपयोग किया जाता है।', 'Used for payments. / भुगतान के लिए उपयोग किया जाता है।', 'Used for receipts. / प्राप्तियों के लिए उपयोग किया जाता है।', 'A'),
(817, 'DCA', NULL, 7, 'What is a Credit Note? / क्रेडिट नोट (Credit Note) क्या है?', 'Used for purchase return. / परचेज रिटर्न के लिए उपयोग किया जाता है।', 'Used for sales return. / सेल्स रिटर्न के लिए उपयोग किया जाता है।', 'Used for cash deposit. / नकद जमा करने के लिए उपयोग किया जाता है।', 'Used for bank withdrawal. / बैंक से निकासी के लिए उपयोग किया जाता है।', 'B'),
(818, 'DCA', NULL, 7, 'Which of these is an Indirect Expense? / इनमें से कौन सा इनडायरेक्ट एक्सपेंस (Indirect Expense) है?', 'Purchase of raw material / कच्चे माल की खरीद', 'Salary paid to staff / स्टाफ को भुगतान किया गया वेतन', 'Sales of goods / माल की बिक्री', 'Capital investment / पूंजी निवेश', 'B'),
(819, 'DCA', NULL, 7, 'What is the primary unit of Tally? / टैली की प्राथमिक इकाई क्या है?', 'Company / कंपनी', 'Ledger / लेजर', 'Group / ग्रुप', 'Voucher / वाउचर', 'A'),
(820, 'DCA', NULL, 7, 'What is the meaning of \"Financial Year\"? / \"वित्तीय वर्ष\" (Financial Year) का क्या अर्थ है?', '1st Jan to 31st Dec / 1 जनवरी से 31 दिसंबर', '1st April to 31st March / 1 अप्रैल से 31 मार्च', '1st July to 30th June / 1 जुलाई से 30 जून', '1st May to 30th April / 1 मई से 30 अप्रैल', 'B'),
(821, 'DCA', NULL, 7, 'What is Trail Balance? / ट्रायल बैलेंस (Trial Balance) क्या है?', 'A list of all ledger balances. / सभी लेजर शेषों की सूची।', 'A list of employees. / कर्मचारियों की सूची।', 'A report of profit. / लाभ की रिपोर्ट।', 'A list of daily sales. / दैनिक बिक्री की सूची।', 'A'),
(822, 'DCA', NULL, 7, 'Which option is used to change the date in Tally? / टैली में तारीख बदलने के लिए किस विकल्प का उपयोग किया जाता है?', 'F2', 'F3', 'F4', 'F5', 'A'),
(823, 'DCA', NULL, 7, 'What is \"Outstanding Expenses\"? / \"आउटस्टैंडिंग एक्सपेंसेस\" क्या है?', 'Expenses paid in advance. / अग्रिम भुगतान किए गए खर्च।', 'Expenses due but not paid. / देय खर्च लेकिन भुगतान नहीं किया गया।', 'Income received. / प्राप्त आय।', 'Cash in hand. / हाथ में नकद।', 'B'),
(824, 'DCA', NULL, 7, 'What is \"Depreciation\"? / \"डेप्रिसिएशन\" (मूल्यह्रास) क्या है?', 'Value of an asset increasing. / संपत्ति का मूल्य बढ़ना।', 'Reduction in the value of an asset. / संपत्ति के मूल्य में कमी।', 'Profit from sales. / बिक्री से लाभ।', 'Loss from fire. / आग से नुकसान।', 'B'),
(825, 'DCA', NULL, 7, 'Which shortcut key is used for \"Gateway of Tally\"? / \"गेटवे ऑफ टैली\" के लिए किस शॉर्टकट कुंजी का उपयोग किया जाता है?', 'Esc', 'Enter', 'Alt', 'Ctrl', 'A'),
(826, 'DCA', NULL, 7, 'What is \"Receipt Voucher\"? / \"रिसिप्ट वाउचर\" क्या है?', 'For cash received. / नकद प्राप्त होने के लिए।', 'For cash paid. / नकद भुगतान करने के लिए।', 'For credit sales. / क्रेडिट बिक्री के लिए।', 'For bank payment. / बैंक भुगतान के लिए।', 'A'),
(827, 'DCA', NULL, 7, 'What is \"Journal Voucher\"? / \"जर्नल वाउचर\" क्या है?', 'For adjustment entries. / समायोजन प्रविष्टियों (Adjustment entries) के लिए।', 'For cash sales. / नकद बिक्री के लिए।', 'For bank entries. / बैंक प्रविष्टियों के लिए।', 'For inventory. / इन्वेंटरी के लिए।', 'A'),
(828, 'DCA', NULL, 7, 'What is Tally.ERP 9? / टैली.ईआरपी 9 क्या है?', 'Accounting software. / अकाउंटिंग सॉफ्टवेयर।', 'Operating system. / ऑपरेटिंग सिस्टम।', 'Web browser. / वेब ब्राउज़र।', 'Anti-virus. / एंटी-वायरस।', 'A'),
(829, 'DCA', NULL, 7, 'What is a \"Stock Group\"? / \"स्टॉक ग्रुप\" क्या है?', 'A group of items based on nature. / प्रकृति के आधार पर वस्तुओं का समूह।', 'A list of customers. / ग्राहकों की सूची।', 'A list of banks. / बैंकों की सूची।', 'A list of employees. / कर्मचारियों की सूची।', 'A'),
(830, 'DCA', NULL, 7, 'Which is a Current Asset? / इनमें से कौन सा करंट एसेट (Current Asset) है?', 'Building / बिल्डिंग', 'Cash at Bank / बैंक में नकद', 'Furniture / फर्नीचर', 'Machinery / मशीनरी', 'B'),
(831, 'DCA', NULL, 7, 'What is \"Sundry Debtors\"? / \"संड्री डेटर्स\" कौन होते हैं?', 'Persons to whom we owe money. / वे लोग जिन्हें हमें पैसा देना है।', 'Persons from whom we have to receive money. / वे लोग जिनसे हमें पैसा लेना है।', 'Bank employees. / बैंक कर्मचारी।', 'Government departments. / सरकारी विभाग।', 'B'),
(832, 'DCA', NULL, 7, 'What is \"Sundry Creditors\"? / \"संड्री क्रेडिटर्स\" कौन होते हैं?', 'Persons to whom we owe money. / वे लोग जिन्हें हमें पैसा देना है।', 'Persons from whom we receive money. / वे लोग जिनसे हम पैसा प्राप्त करते हैं।', 'Owners of the business. / व्यवसाय के मालिक।', 'Our customers. / हमारे ग्राहक।', 'A'),
(833, 'DCA', NULL, 7, 'What is the meaning of \"Credit\" in accounting? / अकाउंटिंग में \"क्रेडिट\" का क्या अर्थ है?', 'Money going out. / पैसा बाहर जाना।', 'Receiving money. / पैसा प्राप्त करना।', 'Adding value to an account. / किसी खाते में मूल्य जोड़ना।', 'None of the above. / उपरोक्त में से कोई नहीं।', 'A'),
(834, 'DCA', NULL, 7, 'What is the meaning of \"Debit\" in accounting? / अकाउंटिंग में \"डेबिट\" का क्या अर्थ है?', 'Money coming in. / पैसा अंदर आना।', 'Money going out. / पैसा बाहर जाना।', 'Loss in business. / व्यवसाय में नुकसान।', 'Profit in business. / व्यवसाय में लाभ।', 'A'),
(835, 'DCA', NULL, 7, 'What is Inventory Management? / इन्वेंटरी मैनेजमेंट क्या है?', 'Managing company staff. / कंपनी के स्टाफ का प्रबंधन।', 'Managing stock of goods. / माल के स्टॉक का प्रबंधन।', 'Managing bank accounts. / बैंक खातों का प्रबंधन।', 'Managing taxes. / करों का प्रबंधन।', 'B'),
(836, 'DCA', NULL, 7, 'What is \"Unit of Measure\" in Tally? / टैली में \"यूनिट ऑफ मेजर\" क्या है?', 'The measurement of stock items (e.g., kg, pcs). / स्टॉक आइटम की माप (जैसे किलो, पीस)।', 'The value of money. / पैसे का मूल्य।', 'The name of the company. / कंपनी का नाम।', 'The date of entry. / एंट्री की तारीख।', 'A'),
(837, 'DCA', NULL, 7, 'What is a \"Purchase Order\"? / \"परचेज ऑर्डर\" क्या है?', 'An order placed to buy goods. / माल खरीदने के लिए दिया गया ऑर्डर।', 'An order received to sell goods. / माल बेचने के लिए प्राप्त ऑर्डर।', 'A record of cash payment. / नकद भुगतान का रिकॉर्ड।', 'A record of salary. / वेतन का रिकॉर्ड।', 'A'),
(838, 'DCA', NULL, 7, 'What is a \"Sales Order\"? / \"सेल्स ऑर्डर\" क्या है?', 'An order received to sell goods. / माल बेचने के लिए प्राप्त ऑर्डर।', 'An order to buy goods. / माल खरीदने का ऑर्डर।', 'An expense record. / एक व्यय रिकॉर्ड।', 'A bank statement. / एक बैंक स्टेटमेंट।', 'A'),
(839, 'DCA', NULL, 7, 'What is VAT? / VAT (वैट) क्या है?', 'Value Added Tax / वैल्यू एडेड टैक्स', 'Very Added Tax / वेरी एडेड टैक्स', 'Value Addition Tax / वैल्यू एडिशन टैक्स', 'None / कोई नहीं', 'A'),
(840, 'DCA', NULL, 7, 'What is \"Cost Centre\"? / \"कॉस्ट सेंटर\" क्या है?', 'A way to track expenses. / खर्चों को ट्रैक करने का एक तरीका।', 'A way to track employees. / कर्मचारियों को ट्रैक करने का एक तरीका।', 'A way to track profits. / लाभ को ट्रैक करने का एक तरीका।', 'All of the above. / उपरोक्त सभी।', 'D'),
(841, 'DCA', NULL, 7, 'What is \"Godown\" in Tally? / टैली में \"गोदाम\" (Godown) क्या है?', 'A place to store goods. / माल रखने का स्थान।', 'A place to sit. / बैठने का स्थान।', 'A place to bank. / बैंक का स्थान।', 'None / कोई नहीं', 'A'),
(842, 'DCA', NULL, 7, 'Which option is used to display Balance Sheet? / बैलेंस शीट देखने के लिए किस विकल्प का उपयोग किया जाता है?', 'Display -> Balance Sheet', 'Accounts Info -> Balance Sheet', 'Reports -> Balance Sheet', 'None', 'A'),
(843, 'DCA', NULL, 7, 'What is \"Profit and Loss Account\"? / \"प्रॉफिट एंड लॉस अकाउंट\" क्या है?', 'Shows net profit or loss. / शुद्ध लाभ या हानि दिखाता है।', 'Shows assets. / संपत्ति दिखाता है।', 'Shows liabilities. / देनदारियां दिखाता है।', 'Shows cash balance. / नकद शेष दिखाता है।', 'A'),
(844, 'DCA', NULL, 7, 'What is \"Payroll\" in Tally? / टैली में \"पेरोल\" क्या है?', 'Managing employee salaries. / कर्मचारी वेतन का प्रबंधन।', 'Managing inventory. / इन्वेंटरी का प्रबंधन।', 'Managing bank loans. / बैंक ऋण का प्रबंधन।', 'Managing taxes. / करों का प्रबंधन।', 'A'),
(845, 'DCA', NULL, 7, 'What is \"Narration\" in a voucher? / वाउचर में \"नरेशन\" क्या है?', 'Short description of entry. / एंट्री का संक्षिप्त विवरण।', 'Date of entry. / एंट्री की तारीख।', 'Amount of entry. / एंट्री की राशि।', 'None / कोई नहीं', 'A'),
(846, 'DCA', NULL, 7, 'Which shortcut is used for \"Company Info\"? / \"कंपनी इंफो\" के लिए किस शॉर्टकट का उपयोग किया जाता है?', 'Alt + F3', 'F1', 'Ctrl + F3', 'Alt + F1', 'A'),
(847, 'DCA', NULL, 7, 'What is \"TDS\"? / \"TDS\" का पूर्ण रूप क्या है?', 'Tax Deducted at Source / टैक्स डिडक्टेड एट सोर्स', 'Total Deducted Salary / टोटल डिडक्टेड सैलरी', 'Tax Deposit System / टैक्स डिपॉजिट सिस्टम', 'None / कोई नहीं', 'A'),
(848, 'DCA', NULL, 7, 'What is a \"Bill of Material\"? / \"बिल ऑफ मटेरियल\" क्या है?', 'List of items required to make a product. / उत्पाद बनाने के लिए आवश्यक वस्तुओं की सूची।', 'List of customers. / ग्राहकों की सूची।', 'List of expenses. / खर्चों की सूची।', 'None / कोई नहीं', 'A'),
(849, 'DCA', NULL, 7, 'What is \"Multi-Currency\"? / \"मल्टी-करेंसी\" क्या है?', 'Working in different currencies. / विभिन्न मुद्राओं में काम करना।', 'Working with many banks. / कई बैंकों के साथ काम करना।', 'Working with many companies. / कई कंपनियों के साथ काम करना।', 'None / कोई नहीं', 'A'),
(850, 'DCA', NULL, 7, 'What is \"Backup\" in Tally? / टैली में \"बैकअप\" क्या है?', 'Keeping a copy of company data. / कंपनी डेटा की एक प्रति रखना।', 'Deleting company data. / कंपनी डेटा हटाना।', 'Opening company data. / कंपनी डेटा खोलना।', 'None / कोई नहीं', 'A'),
(851, 'DCA', NULL, 7, 'What is \"Restore\" in Tally? / टैली में \"रीस्टोर\" क्या है?', 'Recovering data from backup. / बैकअप से डेटा रिकवर करना।', 'Creating new company. / नई कंपनी बनाना।', 'Closing Tally. / टैली बंद करना।', 'None / कोई नहीं', 'A'),
(852, 'DCA', NULL, 7, 'What is the use of F11 key? / F11 कुंजी का क्या उपयोग है?', 'Company Features / कंपनी फीचर्स', 'Accounting Vouchers / अकाउंटिंग वाउचर', 'Gateway of Tally / गेटवे ऑफ टैली', 'None / कोई नहीं', 'A'),
(853, 'BA', 'Hindi Literature', 10, 'हिंदी साहित्य का प्रथम इतिहास ग्रंथ किस भाषा में लिखा गया था?', 'फ़्रेंच', 'संस्कृत', 'हिंदी', 'अंग्रेज़ी', 'A'),
(854, 'BA', 'Hindi Literature', 10, 'हिंदी नवजागरण का अग्रदूत किसे माना जाता है?', 'महावीर प्रसाद द्विवेदी', 'भारतेन्दु हरिश्चंद्र', 'रामचंद्र शुक्ल', 'प्रेमचंद', 'B'),
(855, 'BA', 'Hindi Literature', 10, 'रामचरितमानस किस भाषा में रची गई है?', 'ब्रज भाषा', 'मैथिली', 'अवधी', 'खड़ी बोली', 'C'),
(856, 'BA', 'Hindi Literature', 10, 'कबीरदास किस काव्यधारा के कवि हैं?', 'प्रेममार्गी', 'कृष्णभक्ति', 'रामभक्ति', 'ज्ञानमार्गी (संत काव्य)', 'D'),
(857, 'BA', 'Hindi Literature', 10, 'गोदान उपन्यास के लेखक कौन हैं?', 'मुंशी प्रेमचंद', 'जयशंकर प्रसाद', 'फणीश्वर नाथ रेणु', 'अज्ञेय', 'A'),
(858, 'BA', 'Hindi Literature', 10, 'हिंदी साहित्य के इतिहास का प्रथम काल किस नाम से जाना जाता है?', 'रीतिकाल', 'आदिकाल (वीरगाथा काल)', 'भक्तिकाल', 'आधुनिक काल', 'B'),
(859, 'BA', 'Hindi Literature', 10, 'पृथ्वीराज रासो के रचनाकार कौन हैं?', 'विद्यापति', 'नरपति नाल्ह', 'चंदबरदाई', 'जगनिक', 'C'),
(860, 'BA', 'Hindi Literature', 10, 'बीजक किसकी रचनाओं का संग्रह है?', 'सूरदास', 'तुलसीदास', 'मलिक मोहम्मद जायसी', 'कबीरदास', 'D'),
(861, 'BA', 'Hindi Literature', 10, 'पद्मावत महाकाव्य के रचयिता कौन हैं?', 'मलिक मोहम्मद जायसी', 'कबीर', 'सूरदास', 'रहीम', 'A'),
(862, 'BA', 'Hindi Literature', 10, 'सूरसागर के रचनाकार कौन हैं?', 'तुलसीदास', 'सूरदास', 'मीराबाई', 'रसखान', 'B'),
(863, 'BA', 'Hindi Literature', 10, 'रीतिकाल का मुख्य रस कौन सा है?', 'वीर रस', 'हास्य रस', 'शृंगार रस', 'करुण रस', 'C'),
(864, 'BA', 'Hindi Literature', 10, 'बिहारी सतसई किस काल की रचना है?', 'आदिकाल', 'भक्तिकाल', 'आधुनिक काल', 'रीतिकाल', 'D'),
(865, 'BA', 'Hindi Literature', 10, 'कामायनी महाकाव्य के रचयिता कौन हैं?', 'जयशंकर प्रसाद', 'सूर्यकांत त्रिपाठी निराला', 'सुमित्रानंदन पंत', 'महादेवी वर्मा', 'A'),
(866, 'BA', 'Hindi Literature', 10, 'प्रकृति के सुकुमार कवि किसे कहा जाता है?', 'जयशंकर प्रसाद', 'सुमित्रानंदन पंत', 'निराला', 'महादेवी वर्मा', 'B'),
(867, 'BA', 'Hindi Literature', 10, 'छायावाद के चार स्तंभों में कौन शामिल नहीं हैं?', 'जयशंकर प्रसाद', 'महादेवी वर्मा', 'मैथिलीशरण गुप्त', 'सूर्यकांत त्रिपाठी निराला', 'C'),
(868, 'BA', 'Hindi Literature', 10, 'उर्वशी काव्य नाटक के लिए किसे ज्ञानपीठ पुरस्कार मिला?', 'महादेवी वर्मा', 'अज्ञेय', 'हरिवंश राय बच्चन', 'रामधारी सिंह दिनकर', 'D'),
(869, 'BA', 'Hindi Literature', 10, 'हिंदी का पहला साप्ताहिक पत्र कौन सा था?', 'उदन्त मार्तण्ड', 'बंगदूत', 'बनारस अख़बार', 'प्रजा हितैषी', 'A'),
(870, 'BA', 'Hindi Literature', 10, 'अंधेर नगरी नाटक के लेखक कौन हैं?', 'जयशंकर प्रसाद', 'भारतेन्दु हरिश्चंद्र', 'मोहन राकेश', 'धर्मवीर भारती', 'B'),
(871, 'BA', 'Hindi Literature', 10, 'गबन उपन्यास के प्रमुख पात्र का नाम क्या है?', 'होरी', 'गोबर', 'रामानंद (रमानाथ)', 'माधव', 'C'),
(872, 'BA', 'Hindi Literature', 10, 'सरस्वती पत्रिका के प्रसिद्ध संपादक कौन थे?', 'भारतेन्दु', 'बालकृष्ण भट्ट', 'प्रताप नारायण मिश्र', 'महावीर प्रसाद द्विवेदी', 'D'),
(873, 'BA', 'Hindi Literature', 10, 'मैला आंचल उपन्यास के लेखक कौन हैं?', 'फणीश्वर नाथ रेणु', 'नागार्जुन', 'यशपाल', 'भीष्म साहनी', 'A'),
(874, 'BA', 'Hindi Literature', 10, 'आषाढ़ का एक दिन नाटक के रचनाकार कौन हैं?', 'भारतेन्दु', 'मोहन राकेश', 'लक्ष्मी नारायण मिश्र', 'उपेंद्रनाथ अश्‍क', 'B'),
(875, 'BA', 'Hindi Literature', 10, 'मधुशाला किसकी प्रसिद्ध काव्य कृति है?', 'जयशंकर प्रसाद', 'महादेवी वर्मा', 'हरिवंश राय बच्चन', 'दिनकर', 'C'),
(876, 'BA', 'Hindi Literature', 10, 'यामा काव्य संग्रह के लिए किसे ज्ञानपीठ पुरस्कार प्रदान किया गया?', 'सुभद्रा कुमारी चौहान', 'अमृता प्रीतम', 'कृष्णा सोबती', 'महादेवी वर्मा', 'D'),
(877, 'BA', 'Hindi Literature', 10, 'साकेत महाकाव्य के रचयिता कौन हैं?', 'मैथिलीशरण गुप्त', 'अयोध्या सिंह उपाध्याय हरिऔध', 'माखनलाल चतुर्वेदी', 'सोहनलाल द्विवेदी', 'A'),
(878, 'BA', 'Hindi Literature', 10, 'एक भारतीय आत्मा किस कवि को कहा जाता है?', 'रामधारी सिंह दिनकर', 'माखनलाल चतुर्वेदी', 'सुभद्रा कुमारी चौहान', 'बालकृष्ण शर्मा नवीन', 'B'),
(879, 'BA', 'Hindi Literature', 10, 'प्रियप्रवास किस बोली का प्रथम महाकाव्य माना जाता है?', 'अवधी', 'ब्रजभाषा', 'खड़ी बोली', 'मैथिली', 'C'),
(880, 'BA', 'Hindi Literature', 10, 'कुरुक्षेत्र काव्य कृति के लेखक कौन हैं?', 'जयशंकर प्रसाद', 'निराला', 'पंत', 'रामधारी सिंह दिनकर', 'D'),
(881, 'BA', 'Hindi Literature', 10, 'हिंदी साहित्य का इतिहास नामक ग्रंथ के लेखक कौन हैं?', 'आचार्य रामचंद्र शुक्ल', 'हज़ारी प्रसाद द्विवेदी', 'डॉ. नगेंद्र', 'रामकुमार वर्मा', 'A'),
(882, 'BA', 'Hindi Literature', 10, 'परिमल और अनामिका किसकी रचनाएं हैं?', 'सुमित्रानंदन पंत', 'सूर्यकांत त्रिपाठी निराला', 'महादेवी वर्मा', 'जयशंकर प्रसाद', 'B'),
(883, 'BA', 'Hindi Literature', 10, 'शेखर एक जीवनी उपन्यास के लेखक कौन हैं?', 'यशपाल', 'इलाचंद्र जोशी', 'सच्चिदानंद हीरानंद वात्स्यायन अज्ञेय', 'जैनेंद्र कुमार', 'C'),
(884, 'BA', 'Hindi Literature', 10, 'तमस उपन्यास किस ऐतिहासिक पृष्ठभूमि पर आधारित है?', '1857 की क्रांति', 'असहयोग आंदोलन', 'भारत छोड़ो आंदोलन', 'भारत-विभाजन', 'D'),
(885, 'BA', 'Hindi Literature', 10, 'राग दरबारी उपन्यास के रचनाकार कौन हैं?', 'श्रीलाल शुक्ल', 'हरिशंकर परसाई', 'शरद जोशी', 'रवींद्रनाथ त्यागी', 'A'),
(886, 'BA', 'Hindi Literature', 10, 'हिंदी की पहली कहानी किसे माना जाता है?', 'उसने कहा था', 'इन्दुमती', 'पंच परमेश्वर', 'आकाशदीप', 'B'),
(887, 'BA', 'Hindi Literature', 10, 'उसने कहा था कहानी के लेखक कौन हैं?', 'प्रेमचंद', 'जयशंकर प्रसाद', 'चंद्रधर शर्मा गुलेरी', 'जैनेंद्र', 'C'),
(888, 'BA', 'Hindi Literature', 10, 'चीफ़ की दावत कहानी के लेखक कौन हैं?', 'मोहन राकेश', 'अमरकांत', 'कमलेश्वर', 'भीष्म साहनी', 'D'),
(889, 'BA', 'Hindi Literature', 10, 'आधे अधूरे नाटक के लेखक कौन हैं?', 'मोहन राकेश', 'जगदीश चंद्र माथुर', 'सुरेंद्र वर्मा', 'धर्मवीर भारती', 'A'),
(890, 'BA', 'Hindi Literature', 10, 'अंधा युग काव्य नाटक के रचनाकार कौन हैं?', 'जयशंकर प्रसाद', 'धर्मवीर भारती', 'दुष्यंत कुमार', 'गिरिजाकुमार माथुर', 'B'),
(891, 'BA', 'Hindi Literature', 10, 'चिंतामणि किसके निबंधों का संग्रह है?', 'हज़ारी प्रसाद द्विवेदी', 'कुबेरनाथ राय', 'आचार्य रामचंद्र शुक्ल', 'विद्यानिवास मिश्र', 'C'),
(892, 'BA', 'Hindi Literature', 10, 'कुटज निबंध के लेखक कौन हैं?', 'रामचंद्र शुक्ल', 'महावीर प्रसाद द्विवेदी', 'विद्यानिवास मिश्र', 'हज़ारी प्रसाद द्विवेदी', 'D'),
(893, 'BA', 'Hindi Literature', 10, 'कबीर की भाषा को साधुक्कड़ी किसने कहा है?', 'आचार्य रामचंद्र शुक्ल', 'हज़ारी प्रसाद द्विवेदी', 'श्यामस सुंदर दास', 'रामकुमार वर्मा', 'A'),
(894, 'BA', 'Hindi Literature', 10, 'अष्टछाप का जहाज किसे कहा जाता है?', 'कुंभनदास', 'सूरदास', 'परमानंददास', 'कृष्णदास', 'B'),
(895, 'BA', 'Hindi Literature', 10, 'बरवै रामायण के रचयिता कौन हैं?', 'कबीरदास', 'सूरदास', 'तुलसीदास', 'नंददास', 'C'),
(896, 'BA', 'Hindi Literature', 10, 'रसखान किस संप्रदाय/भक्ति से जुड़े कवि थे?', 'रामभक्ति', 'जैन परंपरा', 'सूफ़ी साधना', 'कृष्णभक्ति', 'D'),
(897, 'BA', 'Hindi Literature', 10, 'भूषण किस रस के प्रमुख कवि हैं?', 'वीर रस', 'शृंगार रस', 'शांत रस', 'बीभत्स रस', 'A'),
(898, 'BA', 'Hindi Literature', 10, 'कठिन काव्य का प्रेत किस कवि को कहा जाता है?', 'घनानंद', 'केशवदास', 'पद्माकर', 'देव', 'B'),
(899, 'BA', 'Hindi Literature', 10, 'भारत भारती काव्य ग्रंथ के लेखक कौन हैं?', 'रामधारी सिंह दिनकर', 'माखनलाल चतुर्वेदी', 'मैथिलीशरण गुप्त', 'सोहनलाल द्विवेदी', 'C'),
(900, 'BA', 'Hindi Literature', 10, 'झांसी की रानी प्रसिद्ध कविता की रचयिता कौन हैं?', 'महादेवी वर्मा', 'मृदुला गर्ग', 'कृष्णा सोबती', 'सुभद्रा कुमारी चौहान', 'D'),
(901, 'BA', 'Hindi Literature', 10, 'चांद का मुंह टेढ़ा है काव्य संग्रह के लेखक कौन हैं?', 'गजानन माधव मुक्तिबोध', 'अज्ञेय', 'शमशेर बहादुर सिंह', 'भवानी प्रसाद मिश्र', 'A'),
(902, 'BA', 'Hindi Literature', 10, 'संसद से सड़क तक कविता संग्रह के कवि कौन हैं?', 'नागार्जुन', 'सुदामा पांडेय धूमिल', 'केदारनाथ सिंह', 'सर्वेश्वर दयाल सक्सेना', 'B'),
(903, 'BA', 'English Literature', 11, 'Who is known as the Father of English Poetry?', 'Geoffrey Chaucer', 'William Shakespeare', 'John Milton', 'Edmund Spenser', 'A'),
(904, 'BA', 'English Literature', 11, 'Which period is known as the Golden Age of English Literature?', 'Victorian Era', 'Elizabethan Era', 'Romantic Era', 'Puritan Age', 'B'),
(905, 'BA', 'English Literature', 11, 'Who wrote the play \"Hamlet\"?', 'Christopher Marlowe', 'Ben Jonson', 'William Shakespeare', 'John Dryden', 'C'),
(906, 'BA', 'English Literature', 11, 'In which year did the Romantic Revival begin with \"Lyrical Ballads\"?', '1789', '1837', '1901', '1798', 'D'),
(907, 'BA', 'English Literature', 11, 'Who is considered the author of the epic \"Paradise Lost\"?', 'John Milton', 'John Donne', 'Alexander Pope', 'Thomas Gray', 'A'),
(908, 'BA', 'English Literature', 11, 'Who wrote \"Pride and Prejudice\"?', 'Charlotte Brontë', 'Jane Austen', 'George Eliot', 'Virginia Woolf', 'B'),
(909, 'BA', 'English Literature', 11, 'Which poet is famously called the \"Poet of Nature\"?', 'John Keats', 'P. B. Shelley', 'William Wordsworth', 'Lord Byron', 'C'),
(910, 'BA', 'English Literature', 11, 'How many lines are there in a standard Shakespearean Sonnet?', '12', '16', '10', '14', 'D'),
(911, 'BA', 'English Literature', 11, 'Who is the author of \"Gitanjali\", which won the Nobel Prize in 1913?', 'Rabindranath Tagore', 'R. K. Narayan', 'Mulk Raj Anand', 'Raja Rao', 'A'),
(912, 'BA', 'English Literature', 11, 'Who wrote the famous novel \"David Copperfield\"?', 'Thomas Hardy', 'Charles Dickens', 'George Meredith', 'William Thackeray', 'B'),
(913, 'BA', 'English Literature', 11, 'Which poem opens with the line \"Beauty is truth, truth beauty\"?', 'Ode to a Nightingale', 'Ode to the West Wind', 'Ode on a Grecian Urn', 'To Autumn', 'C'),
(914, 'BA', 'English Literature', 11, 'Who wrote the dystopian novel \"1984\"?', 'Aldous Huxley', 'H. G. Wells', 'Ray Bradbury', 'George Orwell', 'D'),
(915, 'BA', 'English Literature', 11, 'Who invented the \"Spenserian Stanza\"?', 'Edmund Spenser', 'Philip Sidney', 'John Wyatt', 'Earl of Surrey', 'A'),
(916, 'BA', 'English Literature', 11, 'Who wrote the play \"Doctor Faustus\"?', 'William Shakespeare', 'Christopher Marlowe', 'Thomas Kyd', 'John Webster', 'B'),
(917, 'BA', 'English Literature', 11, 'Which novel is written by Indian English writer R. K. Narayan?', 'Untouchable', 'Kanthapura', 'The Guide', 'Train to Pakistan', 'C'),
(918, 'BA', 'English Literature', 11, 'Who wrote \"The Waste Land\", a landmark modern poem?', 'W. B. Yeats', 'W. H. Auden', 'Ezra Pound', 'T. S. Eliot', 'D'),
(919, 'BA', 'English Literature', 11, 'What is the rhyme scheme of a Petrarchan Sonnet octet?', 'ABBA ABBA', 'ABAB CDCD', 'AABB CCDD', 'ABCA ABCA', 'A'),
(920, 'BA', 'English Literature', 11, 'Who wrote the satirical work \"Gulliver\'s Travels\"?', 'Daniel Defoe', 'Jonathan Swift', 'Henry Fielding', 'Samuel Richardson', 'B'),
(921, 'BA', 'English Literature', 11, 'Which age in English Literature is also called the \"Age of Reason\"?', 'Elizabethan Age', 'Jacobean Age', 'Augustan Age (18th Century)', 'Victorian Age', 'C'),
(922, 'BA', 'English Literature', 11, 'Who wrote \"Tess of the d\'Urbervilles\"?', 'Charles Dickens', 'George Eliot', 'Emily Brontë', 'Thomas Hardy', 'D'),
(923, 'BA', 'English Literature', 11, 'Who is known as the Master of Dramatic Monologue in English poetry?', 'Robert Browning', 'Alfred Lord Tennyson', 'Matthew Arnold', 'D. G. Rossetti', 'A'),
(924, 'BA', 'English Literature', 11, 'Who wrote the tragic play \"Macbeth\"?', 'Ben Jonson', 'William Shakespeare', 'John Ford', 'Thomas Middleton', 'B'),
(925, 'BA', 'English Literature', 11, 'Which literary device uses \"like\" or \"as\" for explicit comparison?', 'Metaphor', 'Personification', 'Simile', 'Hyperbole', 'C'),
(926, 'BA', 'English Literature', 11, 'Who wrote the gothic novel \"Wuthering Heights\"?', 'Charlotte Brontë', 'Anne Brontë', 'Mary Shelley', 'Emily Brontë', 'D'),
(927, 'BA', 'English Literature', 11, 'Who coined the term \"Metaphysical Poets\"?', 'Samuel Johnson', 'John Dryden', 'T. S. Eliot', 'Matthew Arnold', 'A'),
(928, 'BA', 'English Literature', 11, 'Which movement is associated with poets like Wordsworth, Coleridge, and Keats?', 'Neoclassicism', 'Romanticism', 'Realism', 'Modernism', 'B'),
(929, 'BA', 'English Literature', 11, 'Who wrote \"Frankenstein\", a pioneering science-fiction novel?', 'Jane Austen', 'George Eliot', 'Mary Shelley', 'Charlotte Brontë', 'C'),
(930, 'BA', 'English Literature', 11, 'Which Shakespearean play features the character \"Shylock\"?', 'Othello', 'King Lear', 'Twelfth Night', 'The Merchant of Venice', 'D'),
(931, 'BA', 'English Literature', 11, 'Who wrote the famous essay \"Of Studies\"?', 'Francis Bacon', 'Charles Lamb', 'William Hazlitt', 'Joseph Addison', 'A'),
(932, 'BA', 'English Literature', 11, 'Which English poet went blind and wrote \"On His Blindness\"?', 'John Donne', 'John Milton', 'George Herbert', 'Andrew Marvell', 'B'),
(933, 'BA', 'English Literature', 11, 'Who wrote the poem \"Kubla Khan\"?', 'William Wordsworth', 'Robert Southey', 'Samuel Taylor Coleridge', 'Lord Byron', 'C'),
(934, 'BA', 'English Literature', 11, 'Who is the author of \"A Passage to India\"?', 'Graham Greene', 'George Orwell', 'Joseph Conrad', 'E. M. Forster', 'D'),
(935, 'BA', 'English Literature', 11, 'What is an \"Elegy\" in poetry?', 'A poem of lamentation and mourning', 'A song of celebration', 'A short 14-line love poem', 'A humorous light verse', 'A'),
(936, 'BA', 'English Literature', 11, 'Who wrote the famous Victorian poem \"The Charge of the Light Brigade\"?', 'Matthew Arnold', 'Alfred Lord Tennyson', 'Robert Browning', 'Christina Rossetti', 'B'),
(937, 'BA', 'English Literature', 11, 'Which novel by Mulk Raj Anand deals with the life of a sweeper boy named Bakha?', 'Coolie', 'Two Leaves and a Bud', 'Untouchable', 'The Big Heart', 'C'),
(938, 'BA', 'English Literature', 11, 'Who wrote the play \"Arms and the Man\"?', 'Oscar Wilde', 'John Galsworthy', 'J. M. Synge', 'George Bernard Shaw', 'D'),
(939, 'BA', 'English Literature', 11, 'Who is the author of \"Robinson Crusoe\"?', 'Daniel Defoe', 'Jonathan Swift', 'Samuel Richardson', 'Tobias Smollett', 'A'),
(940, 'BA', 'English Literature', 11, 'Which poet wrote \"Ode to the West Wind\"?', 'John Keats', 'P. B. Shelley', 'William Blake', 'Lord Byron', 'B'),
(941, 'BA', 'English Literature', 11, 'Who wrote the post-colonial novel \"Things Fall Apart\"?', 'Wole Soyinka', 'Ngugi wa Thiong\'o', 'Chinua Achebe', 'Derek Walcott', 'C'),
(942, 'BA', 'English Literature', 11, 'What does \"Catharsis\" mean in Greek Tragedy according to Aristotle?', 'Comedic relief', 'Fatal flaw', 'Dramatic irony', 'Purgation/Purification of emotions', 'D'),
(943, 'BA', 'English Literature', 11, 'Who wrote \"The Rape of the Lock\", a famous mock-heroic epic?', 'Alexander Pope', 'John Dryden', 'Jonathan Swift', 'Samuel Johnson', 'A'),
(944, 'BA', 'English Literature', 11, 'Which Shakespearean tragedy is based on jealousy and the villain Iago?', 'Hamlet', 'Othello', 'Macbeth', 'King Lear', 'B'),
(945, 'BA', 'English Literature', 11, 'Who wrote the Indian English epic novel \"Midnight\'s Children\"?', 'Amitav Ghosh', 'Anita Desai', 'Salman Rushdie', 'Vikram Seth', 'C'),
(946, 'BA', 'English Literature', 11, 'Which poem begins with \"Tyger! Tyger! burning bright\"?', 'To the Muses', 'The Lamb', 'Kubla Khan', 'The Tyger (William Blake)', 'D'),
(947, 'BA', 'English Literature', 11, 'Who wrote the famous tragicomedy play \"Waiting for Godot\"?', 'Samuel Beckett', 'Harold Pinter', 'Tom Stoppard', 'Eugene Ionesco', 'A'),
(948, 'BA', 'English Literature', 11, 'Who is considered the leader of the Lake Poets?', 'Coleridge', 'William Wordsworth', 'Robert Southey', 'William Blake', 'B'),
(949, 'BA', 'English Literature', 11, 'Which term is used for an exaggeration used for emphasis or effect?', 'Alliteration', 'Oxymoron', 'Hyperbole', 'Personification', 'C'),
(950, 'BA', 'English Literature', 11, 'Who wrote \"To The Lighthouse\" and was a key pioneer of \"Stream of Consciousness\"?', 'George Eliot', 'Jane Austen', 'Elizabeth Gaskell', 'Virginia Woolf', 'D'),
(951, 'BA', 'English Literature', 11, 'Who wrote the famous poem \"Dover Beach\"?', 'Matthew Arnold', 'Robert Browning', 'Tennyson', 'D. G. Rossetti', 'A'),
(952, 'BA', 'English Literature', 11, 'Which Indian English author wrote \"God of Small Things\"?', 'Jhumpa Lahiri', 'Arundhati Roy', 'Kiran Desai', 'Anita Desai', 'B'),
(953, 'BA', 'History', 13, 'इतिहास का पिता (Father of History) किसे कहा जाता है?', 'हेरोडोटस', 'थ्यूसिडाइड्स', 'प्लूटार्क', 'अरस्तू', 'A'),
(954, 'BA', 'History', 13, 'हड़प्पा सभ्यता की खोज किस वर्ष हुई थी?', '1905', '1921', '1935', '1942', 'B'),
(955, 'BA', 'History', 13, 'सिंधु घाटी सभ्यता का प्रमुख बंदरगाह कौन सा था?', 'कालीबंगा', 'रोपण', 'लोथल', 'मोहनजोदड़ो', 'C'),
(956, 'BA', 'History', 13, 'गायत्री मंत्र का उल्लेख किस वेद में मिलता है?', 'सामवेद', 'यजुर्वेद', 'अथर्ववेद', 'ऋग्वेद', 'D'),
(957, 'BA', 'History', 13, 'सत्यमेव जयते किस उपनिषद से लिया गया है?', 'मुंडकोपनिषद', 'कठोपनिषद', 'छान्दोग्य उपनिषद', 'केन उपनिषद', 'A'),
(958, 'BA', 'History', 13, 'जैन धर्म के 24वें तथा अंतिम तीर्थंकर कौन थे?', 'ऋषभदेव', 'भगवान महावीर', 'पार्श्वनाथ', 'अरिष्टनेमि', 'B'),
(959, 'BA', 'History', 13, 'महात्मा बुद्ध ने अपना प्रथम उपदेश (धर्मचक्रप्रवर्तन) कहां दिया था?', 'बोधगया', 'कुशीनगर', 'सारनाथ', 'लुम्बिनी', 'C'),
(960, 'BA', 'History', 13, 'पाटलिपुत्र नगर की स्थापना किस शासक ने की थी?', 'बिम्बिसार', 'अजातशत्रु', 'शिशुनाग', 'उदायिन (Udayin)', 'D'),
(961, 'BA', 'History', 13, 'भारत पर आक्रमण करने वाला प्रथम विदेशी शासक सिकंदर कहां का राजा था?', 'मिस्र', 'मकदुनिया (Macedonia)', 'सीरिया', 'बेबीलोन', 'B'),
(962, 'BA', 'History', 13, 'मौर्य साम्राज्य का संस्थापक कौन था?', 'चंद्रगुप्त मौर्य', 'बिंदुसार', 'अशोक', 'बृहद्रथ', 'A'),
(963, 'BA', 'History', 13, 'अशोक ने कलिंग युद्ध के बाद किस धर्म को अपनाया था?', 'जैन धर्म', 'हिंदू धर्म', 'बौद्ध धर्म', 'आजीविका धर्म', 'C'),
(964, 'BA', 'History', 13, 'इंडिका (Indica) पुस्तक के लेखक कौन थे?', 'फाहियान', 'ह्वेनसांग', 'इब्न बतूता', 'मेगास्थनीज', 'D'),
(965, 'BA', 'History', 13, 'गुप्त वंश का संस्थापक किसे माना जाता है?', 'श्रीगुप्त', 'चंद्रगुप्त प्रथम', 'समुद्रगुप्त', 'स्कंदगुप्त', 'A'),
(966, 'BA', 'History', 13, 'किस गुप्त शासक को \"भारत का नेपोलियन\" कहा जाता है?', 'चंद्रगुप्त द्वितीय', 'समुद्रगुप्त', 'कुमारगुप्त', 'भानुगुप्त', 'B'),
(967, 'BA', 'History', 13, 'चीनी यात्री ह्वेनसांग किस राजा के शासनकाल में भारत आया था?', 'चंद्रगुप्त मौर्य', 'समुद्रगुप्त', 'हर्षवर्धन', 'कनिष्क', 'C'),
(968, 'BA', 'History', 13, 'नालंदा विश्वविद्यालय की स्थापना किस गुप्त शासक ने की थी?', 'स्कंदगुप्त', 'चंद्रगुप्त प्रथम', 'समुद्रगुप्त', 'कुमारगुप्त प्रथम', 'D'),
(969, 'BA', 'History', 13, 'प्रसिद्ध खजुराहो के मंदिरों का निर्माण किस राजवंश के राजाओं ने करवाया था?', 'चंदेल शासक', 'परमार शासक', 'राष्ट्रकूट शासक', 'चोल शासक', 'A'),
(970, 'BA', 'History', 13, 'तराइन का प्रथम युद्ध (1191 ई.) किसके बीच लड़ा गया था?', 'अकबर और हेमू', 'पृथ्वीराज चौहान और मोहम्मद गोरी', 'बाबर और इब्राहिम लोदी', 'राणा सांगा और बाबर', 'B'),
(971, 'BA', 'History', 13, 'दिल्ली सल्तनत की स्थापना (गुलाम वंश) 1206 ई. में किसने की थी?', 'इल्तुतमिश', 'बलबन', 'कुतुबुद्दीन ऐबक', 'अलाउद्दीन खिलजी', 'C'),
(972, 'BA', 'History', 13, 'भारत की प्रथम महिला शासिका कौन थीं?', 'चांद बीबी', 'नूरजहां', 'रानी लक्ष्मीबाई', 'रजिया सुल्तान', 'D'),
(973, 'BA', 'History', 13, 'बाजार नियंत्रण प्रणाली (Price Control System) किस सुल्तान ने लागू की थी?', 'अलाउद्दीन खिलजी', 'मोहम्मद बिन तुगलक', 'फिरोजशाह तुगलक', 'इब्राहिम लोदी', 'A'),
(974, 'BA', 'History', 13, 'सांकेतिक मुद्रा (Token Currency) का प्रयोग किस तुगलक शासक ने किया था?', 'गयासुद्दीन तुगलक', 'मोहम्मद बिन तुगलक', 'फिरोजशाह तुगलक', 'नसीरुद्दीन महमूद', 'B'),
(975, 'BA', 'History', 13, 'प्रसिद्ध विजयनगर साम्राज्य की स्थापना किसने की थी?', 'कृष्णदेव राय', 'देवराय प्रथम', 'हरिहर और बुक्का', 'राम राय', 'C'),
(976, 'BA', 'History', 13, 'पानीपत का प्रथम युद्ध (1526 ई.) किसके बीच हुआ था?', 'अकबर और हेमू', 'बाबर और राणा सांगा', 'हुमायूं और शेरशाह', 'बाबर और इब्राहिम लोदी', 'D'),
(977, 'BA', 'History', 13, 'ग्रैंड ट्रंक रोड (GT Road) का पुनर्निर्माण किस शासक ने करवाया था?', 'शेरशाह सूरी', 'अकबर', 'जहांगीर', 'शाहजहां', 'A'),
(978, 'BA', 'History', 13, 'अकबर ने \"दीन-ए-इलाही\" धर्म की शुरुआत किस वर्ष की थी?', '1556', '1582', '1576', '1605', 'B'),
(979, 'BA', 'History', 13, 'सिख धर्म के स्वर्ण मंदिर (अमृतसर) की नींव और निर्माण कार्य किस सिख गुरु के समय शुरू हुआ?', 'गुरु नानक देव', 'गुरु गोबिंद सिंह', 'गुरु अर्जुन देव', 'गुरु तेग बहादुर', 'C'),
(980, 'BA', 'History', 13, 'जयपुर के प्रसिद्ध \"हवा महल\" और \"जंतर-मंतर\" का निर्माण किसने कराया था?', 'महाराणा प्रताप', 'राजा मानसिंह', 'राणा सांगा', 'सवाई जयसिंह द्वितीय', 'D'),
(981, 'BA', 'History', 13, 'मराठा साम्राज्य के संस्थापक छत्रपति शिवाजी महाराज का छत्रपति के रूप में राज्याभिषेक कहाँ हुआ था?', 'रायगढ़', 'सतारा', 'पुणे', 'बीजापुर', 'A'),
(982, 'BA', 'History', 13, 'प्लासी का प्रसिद्ध युद्ध किस वर्ष लड़ा गया था?', '1764', '1757', '1761', '1857', 'B'),
(983, 'BA', 'History', 13, 'बक्सर के युद्ध (1764 ई.) के समय बंगाल का गवर्नर कौन था या अंग्रेज़ी सेना का नेतृत्व किसने किया?', 'लॉर्ड क्लाइव', 'वारेन हेस्टिंग्स', 'हेक्टर मुनरो', 'लॉर्ड डलहौजी', 'C'),
(984, 'BA', 'History', 13, 'भारत में \"स्थायी बंदोबस्त\" (Permanent Settlement) की शुरुआत किसने की थी?', 'लॉर्ड कैनिंग', 'लॉर्ड कर्जन', 'लॉर्ड विलियम बेंटिक', 'लॉर्ड कॉर्नवालिस', 'D'),
(985, 'BA', 'History', 13, '1857 की क्रांति का शुभारंभ सर्वप्रथम किस स्थान से हुआ था?', 'मेरठ', 'झांसी', 'कानपुर', 'बैरकपुर', 'A'),
(986, 'BA', 'History', 13, '1857 के विद्रोह के समय भारत का गवर्नर जनरल कौन था?', 'लॉर्ड डलहौजी', 'लॉर्ड कैनिंग', 'लॉर्ड रिपन', 'लॉर्ड लिटन', 'B'),
(987, 'BA', 'History', 13, 'ब्रह्म समाज की स्थापना (1828 ई.) किसने की थी?', 'स्वामी दयानंद सरस्वती', 'स्वामी विवेकानंद', 'राजा राममोहन राय', 'ईश्वरचंद्र विद्यासागर', 'C'),
(988, 'BA', 'History', 13, '\"वेदों की ओर लौटो\" का नारा किसने दिया था?', 'रामकृष्ण परमहंस', 'बाल गंगाधर तिलक', 'ज्योतिबा फुले', 'स्वामी दयानंद सरस्वती', 'D'),
(989, 'BA', 'History', 13, 'भारतीय राष्ट्रीय कांग्रेस की स्थापना किस वर्ष हुई थी?', '1885', '1905', '1893', '1915', 'A'),
(990, 'BA', 'History', 13, 'बंगाल का विभाजन (1905 ई.) किस वायसराय के काल में हुआ था?', 'लॉर्ड मिंटो', 'लॉर्ड कर्जन', 'लॉर्ड चेम्सफोर्ड', 'लॉर्ड हार्डिंग', 'B'),
(991, 'BA', 'History', 13, 'मुस्लिम लीग की स्थापना (1906 ई.) कहाँ हुई थी?', 'कराची', 'लाहौर', 'ढाका', 'लखनऊ', 'C'),
(992, 'BA', 'History', 13, 'जलियांवाला बाग हत्याकांड किस वर्ष हुआ था?', '1911', '1917', '1920', '1919 (13 अप्रैल)', 'D'),
(993, 'BA', 'History', 13, 'महात्मा गांधी ने भारत में अपना पहला सत्याग्रह कहाँ से प्रारंभ किया था?', 'चंपारण (बिहार)', 'खेड़ा (गुजरात)', 'अहमदाबाद', 'दांडी', 'A'),
(994, 'BA', 'History', 13, 'चौरी-चौरा कांड के कारण गांधीजी ने किस आंदोलन को वापस ले लिया था?', 'सविनय अवज्ञा आंदोलन', 'असहयोग आंदोलन', 'भारत छोड़ो आंदोलन', 'खिलाफत आंदोलन', 'B'),
(995, 'BA', 'History', 13, 'स्वराज पार्टी की स्थापना (1923 ई.) किसने की थी?', 'जवाहरलाल नेहरू और पटेल', 'गांधीजी और टैगोर', 'चितरंजन दास और मोतीलाल नेहरू', 'लाला लाजपत राय', 'C'),
(996, 'BA', 'History', 13, 'साइमन कमीशन भारत किस वर्ष आया था?', '1925', '1930', '1942', '1928', 'D'),
(997, 'BA', 'History', 13, '\"करो या मरो\" का नारा गांधीजी ने किस आंदोलन के दौरान दिया था?', 'भारत छोड़ो आंदोलन (1942)', 'असहयोग आंदोलन', 'दांडी मार्च', 'व्यक्तिगत सत्याग्रह', 'A'),
(998, 'BA', 'History', 13, 'आजाद हिंद फौज (Forward Bloc/INA) के पुनर्गठन और नेतृत्व का श्रेय किसे जाता है?', 'भगत सिंह', 'नेताजी सुभाष चंद्र बोस', 'रासबिहारी बोस', 'चंद्रशेखर आजाद', 'B'),
(999, 'BA', 'History', 13, 'स्वतंत्र भारत के प्रथम गवर्नर जनरल कौन थे?', 'सी. राजगोपालाचारी', 'डॉ. राजेंद्र प्रसाद', 'लॉर्ड माउंटबेटन', 'जवाहरलाल नेहरू', 'C'),
(1000, 'BA', 'History', 13, 'स्वतंत्र भारत के प्रथम भारतीय गवर्नर जनरल कौन थे?', 'जवाहरलाल नेहरू', 'सर्दार पटेल', 'बी. आर. अंबेडकर', 'चक्रवर्ती राजगोपालाचारी (C. Rajagopalachari)', 'D'),
(1001, 'BA', 'History', 13, 'फ्रांसीसी क्रांति (French Revolution) किस वर्ष हुई थी?', '1789', '1776', '1917', '1848', 'A'),
(1002, 'BA', 'History', 13, 'रूसी क्रांति (Bolshevik Revolution) 1917 का नेता कौन था?', 'स्टालिन', 'लेनिन (Vladimir Lenin)', 'ट्रॉटस्की', 'जार निकोलस द्वितीय', 'B'),
(1003, 'BA', 'Political Science', 12, 'राजनीति विज्ञान का जनक (Father of Political Science) किसे माना जाता है?', 'सुकरात', 'अरस्तू (Aristotle)', 'प्लेटो', 'मैकियावेली', 'B'),
(1004, 'BA', 'Political Science', 12, 'प्रसिद्ध पुस्तक \"द रिपब्लिक\" (The Republic) के लेखक कौन हैं?', 'प्लेटो', 'अरस्तू', 'हॉब्स', 'जॉन लॉक', 'A'),
(1005, 'BA', 'Political Science', 12, '\"द प्रिंस\" (The Prince) नामक प्रसिद्ध राजनीतिक ग्रंथ किसने लिखा है?', 'बॉदा', 'निकोलस मैकियावेली', 'मॉन्टेस्क्यू', 'रूसो', 'B'),
(1006, 'BA', 'Political Science', 12, 'सामाजिक समझौता सिद्धांत (Social Contract Theory) के तीन प्रमुख विचारक कौन हैं?', 'प्लेटो, अरस्तू, सुकरात', 'लास्की, ग्रीन, हिगल', 'हॉब्स, लॉक, रूसो', 'मार्क्स, एंगल्स, लेनिन', 'C'),
(1007, 'BA', 'Political Science', 12, 'साम्यवाद (Communism) और \"दास कैपिटल\" के रचयिता कौन हैं?', 'कार्ल मार्क्स', 'फ्रेडरिक एंगल्स', 'लेनिन', 'स्टालिन', 'A'),
(1008, 'BA', 'Political Science', 12, 'शक्ति पृथक्करण का सिद्धांत (Theory of Separation of Powers) किसने प्रतिपादित किया?', 'जॉन लॉक', 'मॉन्टेस्क्यू', 'रूसो', 'जे. एस. मिल', 'B'),
(1009, 'BA', 'Political Science', 12, '\"राज्य का आधार शक्ति नहीं, इच्छा है\" यह कथन किसका है?', 'टी. एच. ग्रीन', 'कार्ल मार्क्स', 'बेंथम', 'लास्की', 'A'),
(1010, 'BA', 'Political Science', 12, 'उपयोगितावाद (Utilitarianism) का मुख्य समर्थक किसे माना जाता है?', 'प्लेटो', 'हेगल', 'जेरेमी बेंथम', 'आदर्शवाद', 'C'),
(1011, 'BA', 'Political Science', 12, 'भारतीय संविधान सभा के स्थायी अध्यक्ष कौन थे?', 'डॉ. बी. आर. अंबेडकर', 'डॉ. राजेंद्र प्रसाद', 'सच्चिदानंद सिन्हा', 'जवाहरलाल नेहरू', 'B'),
(1012, 'BA', 'Political Science', 12, 'भारतीय संविधान की प्रारूप समिति (Drafting Committee) के अध्यक्ष कौन थे?', 'डॉ. बी. आर. अंबेडकर', 'सर बी. एन. राव', 'के. एम. मुंशी', 'अल्लादि कृष्णास्वामी', 'A'),
(1013, 'BA', 'Political Science', 12, 'भारतीय संविधान को कब अंगीकृत (Adopt) किया गया था?', '15 अगस्त 1947', '26 जनवरी 1950', '26 नवंबर 1949', '26 अक्टूबर 1948', 'C'),
(1014, 'BA', 'Political Science', 12, 'भारत में मौलिक अधिकारों (Fundamental Rights) का उल्लेख संविधान के किस भाग में है?', 'भाग I', 'भाग II', 'भाग IV', 'भाग III', 'D'),
(1015, 'BA', 'Political Science', 12, 'संविधान के किस अनुच्छेद को डॉ. अंबेडकर ने \"संविधान की आत्मा और हृदय\" कहा था?', 'अनुच्छेद 32 (संवैधानिक उपचारों का अधिकार)', 'अनुच्छेद 14', 'अनुच्छेद 19', 'अनुच्छेद 21', 'A'),
(1016, 'BA', 'Political Science', 12, 'राज्य के नीति निदेशक तत्व (DPSP) किस देश के संविधान से लिए गए हैं?', 'अमेरिका', 'आयरलैंड', 'ब्रिटेन', 'कनाडा', 'B'),
(1017, 'BA', 'Political Science', 12, 'भारतीय संविधान में मौलिक कर्तव्यों (Fundamental Duties) को किस संशोधन द्वारा जोड़ा गया?', '44वां संशोधन', '86वां संशोधन', '42वां संशोधन (1976)', '73वां संशोधन', 'C'),
(1018, 'BA', 'Political Science', 12, 'भारत के राष्ट्रपति को पद एवं गोपनीयता की शपथ कौन दिलाता है?', 'उपराष्ट्रपति', 'प्रधानमंत्री', 'लोकसभा अध्यक्ष', 'भारत का मुख्य न्यायाधीश (CJI)', 'D'),
(1019, 'BA', 'Political Science', 12, 'भारत के राष्ट्रपति पर महाभियोग (Impeachment) चलाने का प्रावधान किस अनुच्छेद में है?', 'अनुच्छेद 61', 'अनुच्छेद 52', 'अनुच्छेद 72', 'अनुच्छेद 123', 'A'),
(1020, 'BA', 'Political Science', 12, 'भारत का पदेन सभापति (ex-officio Chairman) कौन होता है?', 'लोकसभा अध्यक्ष', 'भारत का उपराष्ट्रपति (राज्यसभा का)', 'प्रधानमंत्री', 'गृह मंत्री', 'B'),
(1021, 'BA', 'Political Science', 12, 'संसद के संयुक्त अधिवेशन (Joint Session) की अध्यक्षता कौन करता है?', 'राष्ट्रपति', 'उपराष्ट्रपति', 'लोकसभा अध्यक्ष (Speaker)', 'प्रधानमंत्री', 'C'),
(1022, 'BA', 'Political Science', 12, 'भारतीय संसद का निम्न सदन (Lower House) किसे कहा जाता है?', 'राज्यसभा', 'विधान परिषद', 'उच्चतम न्यायालय', 'लोकसभा', 'D'),
(1023, 'BA', 'Political Science', 12, 'लोकसभा का सदस्य बनने के लिए न्यूनतम आयु सीमा कितनी है?', '25 वर्ष', '30 वर्ष', '35 वर्ष', '21 वर्ष', 'A'),
(1024, 'BA', 'Political Science', 12, 'राज्यसभा का सदस्य बनने के लिए न्यूनतम आयु कितनी होनी चाहिए?', '25 वर्ष', '30 वर्ष', '35 वर्ष', '18 वर्ष', 'B'),
(1025, 'BA', 'Political Science', 12, 'राज्यसभा के सदस्यों का कार्यकाल कितने वर्षों का होता है?', '5 वर्ष', '4 वर्ष', '6 वर्ष', 'स्थायी (कोई सीमा नहीं)', 'C'),
(1026, 'BA', 'Political Science', 12, 'भारत में सर्वोच्च न्यायालय (Supreme Court) के न्यायाधीशों की सेवानिवृत्ति की आयु क्या है?', '60 वर्ष', '62 वर्ष', '70 वर्ष', '65 वर्ष', 'D'),
(1027, 'BA', 'Political Science', 12, 'भारत में न्यायिक पुनरावलोकन (Judicial Review) की अवधारणा किस देश से ली गई है?', 'संयुक्त राज्य अमेरिका (USA)', 'ब्रिटेन', 'फ़्रांस', 'ऑस्ट्रेलिया', 'A'),
(1028, 'BA', 'Political Science', 12, '73वां संविधान संशोधन (1992) किससे संबंधित है?', 'नगरपालिका', 'पंचायती राज व्यवस्था', 'दल-बदल कानून', 'शिक्षा का अधिकार', 'B'),
(1029, 'BA', 'Political Science', 12, 'भारत में त्रिस्तरीय पंचायती राज व्यवस्था की सिफारिश किस समिति ने की थी?', 'अशोक मेहता समिति', 'सार्करिया आयोग', 'बलवंत राय मेहता समिति', 'के. संथानम समिति', 'C'),
(1030, 'BA', 'Political Science', 12, 'पंचायती राज का गठन संविधान के किस भाग/अनुच्छेद में उल्लिखित है?', 'अनुच्छेद 356', 'अनुच्छेद 370', 'अनुच्छेद 280', 'अनुच्छेद 40 / 243', 'D'),
(1031, 'BA', 'Political Science', 12, 'भारत में मत देने (Voting) की न्यूनतम आयु 21 से घटाकर 18 वर्ष किस संशोधन द्वारा की गई?', '61वां संशोधन (1989)', '44वां संशोधन', '86वां संशोधन', '52वां संशोधन', 'A'),
(1032, 'BA', 'Political Science', 12, 'मुख्य निर्वाचन आयुक्त (Chief Election Commissioner) की नियुक्ति कौन करता है?', 'प्रधानमंत्री', 'भारत का राष्ट्रपति', 'संसद', 'मुख्य न्यायाधीश', 'B'),
(1033, 'BA', 'Political Science', 12, 'भारतीय संविधान में आपातकालीन प्रावधान (Emergency Provisions) किस देश से प्रेरित हैं?', 'अमेरिका', 'रूस', 'जर्मनी का वाइमर संविधान', 'कनाडा', 'C'),
(1034, 'BA', 'Political Science', 12, 'राष्ट्रीय आपातकाल (National Emergency) का उल्लेख किस अनुच्छेद में है?', 'अनुच्छेद 356', 'अनुच्छेद 360', 'अनुच्छेद 368', 'अनुच्छेद 352', 'D'),
(1035, 'BA', 'Political Science', 12, 'राज्यों में राष्ट्रपति शासन (President Rule) किस अनुच्छेद के तहत लगाया जाता है?', 'अनुच्छेद 356', 'अनुच्छेद 352', 'अनुच्छेद 360', 'अनुच्छेद 370', 'A'),
(1036, 'BA', 'Political Science', 12, 'वित्तीय आपातकाल (Financial Emergency) का उल्लेख किस अनुच्छेद में है?', 'अनुच्छेद 352', 'अनुच्छेद 360', 'अनुच्छेद 356', 'अनुच्छेद 368', 'B'),
(1037, 'BA', 'Political Science', 12, 'संविधान संशोधन प्रक्रिया (Constitutional Amendment) का प्रावधान किस अनुच्छेद में है?', 'अनुच्छेद 352', 'अनुच्छेद 356', 'अनुच्छेद 368', 'अनुच्छेद 370', 'C'),
(1038, 'BA', 'Political Science', 12, 'भारतीय संघ में किसी नए राज्य को शामिल करने की शक्ति किसके पास है?', 'राष्ट्रपति', 'प्रधानमंत्री', 'गृह मंत्रालय', 'संसद', 'D'),
(1039, 'BA', 'Political Science', 12, 'स्वतंत्र भारत के प्रथम गृह मंत्री और \"लौह पुरुष\" किसे कहा जाता है?', 'सर्दार वल्लभभाई पटेल', 'पंडित जवाहरलाल नेहरू', 'मौलाना अबुल कलाम आजाद', 'सी. राजगोपालाचारी', 'A'),
(1040, 'BA', 'Political Science', 12, 'द्विराष्ट्र सिद्धांत (Two-Nation Theory) का प्रतिपादन मुख्य रूप से किसने किया था?', 'सर सैयद अहमद खान', 'मोहम्मद अली जिन्ना', 'आगा खान', 'लॉर्ड मिंटो', 'B'),
(1041, 'BA', 'Political Science', 12, 'गुटनिरपेक्ष आंदोलन (NAM - Non-Aligned Movement) के संस्थापकों में भारत के नेता कौन थे?', 'महात्मा गांधी', 'लाल बहादुर शास्त्री', 'पंडित जवाहरलाल नेहरू', 'इंदिरा गांधी', 'C'),
(1042, 'BA', 'Political Science', 12, 'सार्क (SAARC) का मुख्यालय कहाँ स्थित है?', 'नई दिल्ली', 'कोलंबो', 'ढाका', 'काठमांडू (नेपाल)', 'D'),
(1043, 'BA', 'Political Science', 12, 'संयुक्त राष्ट्र संघ (UNO) की स्थापना किस तिथि को हुई थी?', '24 अक्टूबर 1945', '10 दिसंबर 1948', '15 अगस्त 1945', '26 जनवरी 1945', 'A'),
(1044, 'BA', 'Political Science', 12, 'संयुक्त राष्ट्र संघ (UN Security Council) में कितने स्थायी सदस्य देश हैं?', '10', '5 (USA, UK, Russia, China, France)', '15', '7', 'B');
INSERT INTO `questions` (`id`, `course`, `subject_name`, `subject_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(1045, 'BA', 'Political Science', 12, 'अंतर्राष्ट्रीय न्यायालय (International Court of Justice) का मुख्यालय कहाँ स्थित है?', 'जेनेवा', 'न्यूयॉर्क', 'द हेग (The Hague - नीदरलैंड)', 'पेरिस', 'C'),
(1046, 'BA', 'Political Science', 12, 'अंतर्राष्ट्रीय मानवाधिकार दिवस (Human Rights Day) कब मनाया जाता है?', '24 अक्टूबर', '5 जून', '1 दिसंबर', '10 दिसंबर', 'D'),
(1047, 'BA', 'Political Science', 12, 'भारत में \"राजनीतिक दलों को मान्यता\" कौन प्रदान करता है?', 'भारत का निर्वाचन आयोग (Election Commission)', 'राष्ट्रपति', 'संसद', 'सर्वोच्च न्यायालय', 'A'),
(1048, 'BA', 'Political Science', 12, 'संसद के दो सत्रों के बीच अधिकतम कितना समयांतराल हो सकता है?', '3 माह', '6 माह', '9 माह', '1 वर्ष', 'B'),
(1049, 'BA', 'Political Science', 12, 'लोकसभा में शून्य काल (Zero Hour) की अधिकतम अवधि सामान्यतः कितनी होती है?', '30 मिनट', '2 घंटे', '1 घंटा (12 से 1 बजे)', 'अनिश्चितकाल', 'C'),
(1050, 'BA', 'Political Science', 12, 'संविधान की कुंजी (Key to the Constitution) किसे कहा जाता है?', 'अनुच्छेद 32', 'मौलिक अधिकार', 'नीति निदेशक तत्व', 'प्रस्तावना (Preamble)', 'D'),
(1051, 'BA', 'Political Science', 12, 'लोक कल्याणकारी राज्य (Welfare State) की अवधारणा संविधान में कहाँ निहित है?', 'नीति निदेशक तत्वों में', 'प्रस्तावना में', 'मौलिक अधिकारों में', 'सातवीं अनुसूची में', 'A'),
(1052, 'BA', 'Political Science', 12, 'राजनीतिक शास्त्र में \"संप्रभुता\" (Sovereignty) शब्द का सबसे पहले प्रयोग किसने किया था?', 'मैकियावेली', 'जीन बॉदा (Jean Bodin)', 'ऑस्टिन', 'रूसो', 'B'),
(1053, 'BA', 'Sociology', 14, 'समाजशास्त्र (Sociology) का जनक किसे माना जाता है?', 'अगस्त कॉम्ट (Auguste Comte)', 'कार्ल मार्क्स', 'इमाइल दुर्खीम', 'मैक्स वेबर', 'A'),
(1054, 'BA', 'Sociology', 14, 'समाजशास्त्र शब्द की उत्पत्ति किस वर्ष मानी जाती है?', '1857', '1838', '1901', '1789', 'B'),
(1055, 'BA', 'Sociology', 14, 'समाजशास्त्र शब्द किन दो भाषाओं के शब्दों से मिलकर बना है?', 'ग्रीक और जर्मन', 'हिंदी और संस्कृत', 'लैटिन और ग्रीक', 'फ़्रेंच और अंग्रेजी', 'C'),
(1056, 'BA', 'Sociology', 14, '\"समाजशास्त्र सामाजिक संबंधों का जाल है\" यह प्रसिद्ध कथन किसका है?', 'मैकाइवर एवं पेज (MacIver & Page)', 'ओगबर्न', 'गिडिंग्स', 'जॉनसन', 'A'),
(1057, 'BA', 'Sociology', 14, 'प्रसिद्ध पुस्तक \"दास कैपिटल\" (Das Kapital) के लेखक कौन हैं?', 'मैक्स वेबर', 'कार्ल मार्क्स', 'इमाइल दुर्खीम', 'हेर्बर्ट स्पेंसर', 'B'),
(1058, 'BA', 'Sociology', 14, 'आत्महत्या का सिद्धांत (Theory of Suicide) किस समाजशास्त्री ने दिया था?', 'अगस्त कॉम्ट', 'कार्ल मार्क्स', 'इमाइल दुर्खीम', 'मैक्स वेबर', 'C'),
(1059, 'BA', 'Sociology', 14, '\"द प्रोटस्टेंट एथिक एंड द स्पिरिट ऑफ कैपिटलिज्म\" पुस्तक के लेखक कौन हैं?', 'मैक्स वेबर', 'इमाइल दुर्खीम', 'मार्क्स', 'स्पेंसर', 'A'),
(1060, 'BA', 'Sociology', 14, 'सामाजिक गतिशीलता (Social Mobility) की अवधारणा सर्वप्रथम किसने दी?', 'सोरोकिन (Pitirim Sorokin)', 'पार्संस', 'मर्टन', 'कूल', 'A'),
(1061, 'BA', 'Sociology', 14, 'मानव समाज का विकास तीन चरणों (Three Stages) में बताने वाले विचारक कौन हैं?', 'हेर्बर्ट स्पेंसर', 'अगस्त कॉम्ट', 'कार्ल मार्क्स', 'मैक्स वेबर', 'B'),
(1062, 'BA', 'Sociology', 14, 'जैविक सादृश्य (Organic Analogy) का सिद्धांत समाजशास्त्र में किसने प्रस्तुत किया?', 'इमाइल दुर्खीम', 'कार्ल मार्क्स', 'हेर्बर्ट स्पेंसर', 'मैकाइवर', 'C'),
(1063, 'BA', 'Sociology', 14, 'प्राथमिक समूह (Primary Group) की अवधारणा किसने प्रतिपादित की?', 'सी. एच. कूले (C.H. Cooley)', 'आर. के. मर्टन', 'जी. एच. मीड', 'टालकॉट पार्संस', 'A'),
(1064, 'BA', 'Sociology', 14, 'संदर्भ समूह (Reference Group) की अवधारणा सबसे पहले किसने दी?', 'एच. हाइमैन (H. Hyman)', 'मर्टन', 'कूले', 'बोटामोर', 'A'),
(1065, 'BA', 'Sociology', 14, 'जाति का आधार क्या होता है?', 'कर्म', 'जन्म', 'धन', 'शिक्षा', 'B'),
(1066, 'BA', 'Sociology', 14, 'वर्ग (Class) की मुख्य विशेषता क्या है?', 'अर्जित स्थिति (Achieved Status)', 'जन्म पर आधारित', 'धार्मिक आधार', 'स्थायी स्थिति', 'A'),
(1067, 'BA', 'Sociology', 14, 'संस्कृतिकरण (Sanskritization) की अवधारणा किस भारतीय समाजशास्त्री ने दी?', 'जी. एस. घुरिये', 'एम. एन. श्रीनिवास (M. N. Srinivas)', 'एस. सी. दुबे', 'डी. एन. मजूमदार', 'B'),
(1068, 'BA', 'Sociology', 14, 'संस्कृतिक विलंबना (Cultural Lag) का सिद्धांत किसने दिया?', 'डब्ल्यू. एफ. ओगबर्न (W. F. Ogburn)', 'सोरोकिन', 'मैकाइवर', 'सदरलैंड', 'A'),
(1069, 'BA', 'Sociology', 14, 'विवाह किस प्रकार की संस्था है?', 'राजनीतिक', 'आर्थिक', 'सामाजिक/सांस्कृतिक', 'धार्मिक', 'C'),
(1070, 'BA', 'Sociology', 14, 'एक समय में एक पुरुष द्वारा एक ही स्त्री से विवाह करना क्या कहलाता है?', 'बहुपति विवाह', 'एकविवाह (Monogamy)', 'बहुपत्नी विवाह', 'समूह विवाह', 'B'),
(1071, 'BA', 'Sociology', 14, 'नातेदारी (Kinship) को मुख्य रूप से कितने भागों में बांटा जाता है?', 'दो (रक्त एवं विवाह संबंधी)', 'चार', 'छह', 'आठ', 'A'),
(1072, 'BA', 'Sociology', 14, 'परिवार (Family) किस प्रकार के समूह का उदाहरण है?', 'द्वितीयक समूह', 'प्राथमिक समूह', 'अप्रत्यक्ष समूह', 'संदर्भ समूह', 'B'),
(1073, 'BA', 'Sociology', 14, 'प्रकट और अंतर्निहित प्रकार्य (Manifest and Latent Functions) का सिद्धांत किसने दिया?', 'आर. के. मर्टन (R. K. Merton)', 'टालकॉट पार्संस', 'दुर्खीम', 'मालोनाव्स्की', 'A'),
(1074, 'BA', 'Sociology', 14, 'नोकरशाही (Bureaucracy) का आदर्श प्रारूप (Ideal Type) किसने प्रस्तुत किया?', 'कार्ल मार्क्स', 'मैक्स वेबर', 'अगस्त कॉम्ट', 'गिडिंग्स', 'B'),
(1075, 'BA', 'Sociology', 14, 'वर्ग संघर्ष का सिद्धांत (Theory of Class Struggle) किसने दिया?', 'इमाइल दुर्खीम', 'मैक्स वेबर', 'कार्ल मार्क्स', 'हेर्बर्ट स्पेंसर', 'C'),
(1076, 'BA', 'Sociology', 14, 'यांत्रिक और सावयवी एकजुटता (Mechanical and Organic Solidarity) का विचार किसने दिया?', 'इमाइल दुर्खीम', 'अगस्त कॉम्ट', 'पार्संस', 'सोरोकिन', 'A'),
(1077, 'BA', 'Sociology', 14, 'भारतीय समाजशास्त्र का पिता (Father of Indian Sociology) किसे माना जाता है?', 'एम. एन. श्रीनिवास', 'जी. एस. घुरिये (G. S. Ghurye)', 'राधाकमल मुखर्जी', 'इरावती कर्वे', 'B'),
(1078, 'BA', 'Sociology', 14, '\"होमो हाइरार्फिकस\" (Homo Hierarchicus) पुस्तक के लेखक कौन हैं?', 'लुई ड्युमो (Louis Dumont)', 'एम. एन. श्रीनिवास', 'जी. एस. घुरिये', 'योगेंद्र सिंह', 'A'),
(1079, 'BA', 'Sociology', 14, 'भारत में जनजातियों को \"पिछड़े हिंदू\" (Backward Hindus) किसने कहा?', 'अमृतलाल ठक्कर', 'जी. एस. घुरिये', 'वेरियर एल्विन', 'एस. सी. दुबे', 'B'),
(1080, 'BA', 'Sociology', 14, 'प्रभु जाति (Dominant Caste) की अवधारणा किसने दी?', 'एम. एन. श्रीनिवास', 'इरावती कर्वे', 'योगेंद्र सिंह', 'डी. पी. मुखर्जी', 'A'),
(1081, 'BA', 'Sociology', 14, 'सामाजिक नियंत्रण (Social Control) शब्द का प्रयोग सर्वप्रथम किसने किया?', 'ई. ए. रॉस (E. A. Ross)', 'दुर्खीम', 'कूले', 'पार्संस', 'A'),
(1082, 'BA', 'Sociology', 14, 'श्वेत वसन अपराध (White-Collar Crime) की अवधारणा किसने दी?', 'सदरलैंड (Edwin Sutherland)', 'लोम्ब्रोसो', 'मर्टन', 'तार्दे', 'A'),
(1083, 'BA', 'Sociology', 14, 'सामाजिक परिवर्तन (Social Change) का चक्रिय सिद्धांत (Cyclical Theory) किसने दिया?', 'स्पेंग्लर और सोरोकिन', 'मार्क्स', 'स्पेंसर', 'ओगबर्न', 'A'),
(1084, 'BA', 'Sociology', 14, 'धर्म का सामाजिक सिद्धांत किसने दिया?', 'इमाइल दुर्खीम', 'टाइलर', 'मैक्स मूलर', 'फ्रेज़र', 'A'),
(1085, 'BA', 'Sociology', 14, 'टोने-टोटम (Totemism) का अध्ययन दुर्खीम ने किस जनजाति पर किया था?', 'अंरुटा जनजाति (ऑस्ट्रेलिया)', 'भील जनजाति', 'खासी जनजाति', 'टोडा जनजाति', 'A'),
(1086, 'BA', 'Sociology', 14, '\"द सोशल सिस्टम\" (The Social System) पुस्तक के लेखक कौन हैं?', 'टालकॉट पार्संस (Talcott Parsons)', 'मर्टन', 'दुर्खीम', 'मार्क्स', 'A'),
(1087, 'BA', 'Sociology', 14, 'अंतः समूह और बाह्य समूह (In-Group & Out-Group) का वर्गीकरण किसने किया?', 'डब्लू. जी. समनर (W. G. Sumner)', 'कूले', 'मैकाइवर', 'पार्संस', 'A'),
(1088, 'BA', 'Sociology', 14, 'समनर ने अपनी किस पुस्तक में \"Folkways\" (जनरीतियाँ) की अवधारणा दी?', 'Folkways (1906)', 'Sociology', 'Human Society', 'Social Mind', 'A'),
(1089, 'BA', 'Sociology', 14, 'भूमिका संघर्ष (Role Conflict) क्या है?', 'एक ही समय में दो परस्पर विरोधी भूमिकाओं का होना', 'दो व्यक्तियों के बीच लड़ाई', 'जातिगत संघर्ष', 'आर्थिक प्रतिस्पर्धा', 'A'),
(1090, 'BA', 'Sociology', 14, 'प्रदत्त स्थिति (Ascribed Status) का उदाहरण कौन सा है?', 'लिंग और जाति (जन्म आधारित)', 'डॉक्टर', 'शिक्षक', 'इंजीनियर', 'A'),
(1091, 'BA', 'Sociology', 14, 'अर्जित स्थिति (Achieved Status) का उदाहरण कौन सा है?', 'जन्म', 'जाति', 'व्यावसायिक पद/शिक्षा', 'लिंग', 'C'),
(1092, 'BA', 'Sociology', 14, 'शहरीकरण (Urbanization) का मुख्य कारण क्या है?', 'औद्योगीकरण और रोजगार के अवसर', 'कृषि का विकास', 'धार्मिक अनुष्ठान', 'संयुक्त परिवार', 'A'),
(1093, 'BA', 'Sociology', 14, 'पश्चिमीकरण (Westernization) की अवधारणा किसने दी?', 'एम. एन. श्रीनिवास', 'जी. एस. घुरिये', 'योगेंद्र सिंह', 'एस. सी. दुबे', 'A'),
(1094, 'BA', 'Sociology', 14, 'भारत में संयुक्त परिवार के विघटन का मुख्य कारण क्या है?', 'व्यक्तिवाद और औद्योगीकरण', 'धार्मिक अनुष्ठान', 'कृषि का प्रसार', 'संस्कृतशास्त्र', 'A'),
(1095, 'BA', 'Sociology', 14, 'नातेदारी व्यवस्था का विस्तृत अध्ययन करने वाली भारतीय महिला समाजशास्त्री कौन थीं?', 'इरावती कर्वे', 'लीला डुबे', 'शर्मिला रेगे', 'वीणा दास', 'A'),
(1096, 'BA', 'Sociology', 14, 'सामाजिक संरचना (Social Structure) शब्द का प्रयोग सबसे पहले किसने किया?', 'हेर्बर्ट स्पेंसर', 'दुर्खीम', 'मार्क्स', 'पार्संस', 'A'),
(1097, 'BA', 'Sociology', 14, 'आधुनिक भारत में सामाजिक परिवर्तन पुस्तक के लेखक कौन हैं?', 'एम. एन. श्रीनिवास', 'योगेंद्र सिंह', 'ए. आर. देसाई', 'रामकृष्ण मुखर्जी', 'A'),
(1098, 'BA', 'Sociology', 14, 'मार्क्सवादी दृष्टिकोण से भारतीय समाज का अध्ययन किसने किया?', 'ए. आर. देसाई (A. R. Desai)', 'एम. एन. श्रीनिवास', 'जी. एस. घुरिये', 'एस. सी. दुबे', 'A'),
(1099, 'BA', 'Sociology', 14, 'लिंग (Gender) समाजशास्त्र में क्या है?', 'एक सामाजिक और सांस्कृतिक निर्मिति', 'केवल जैविक अंतर', 'राजनीतिक दल', 'आर्थिक वर्ग', 'A'),
(1100, 'BA', 'Sociology', 14, 'सामाजिक प्रस्थिति और भूमिका (Status and Role) का सबसे पहले व्यवस्थित विश्लेषण किसने किया?', 'राल्फ लिनटन (Ralph Linton)', 'कूले', 'मर्टन', 'पार्संस', 'A'),
(1101, 'BA', 'Sociology', 14, 'विचलन (Deviance) समाजशास्त्र में किसे कहते हैं?', 'सामाजिक नियमों और मापदंडों का उल्लंघन करना', 'समाज सेवा करना', 'धार्मिक अनुष्ठान', 'शिक्षा प्राप्त करना', 'A'),
(1102, 'BA', 'Sociology', 14, 'अराजकता या नियमहीनता (Anomie) की अवधारणा किसने दी?', 'इमाइल दुर्खीम एवं मर्टन', 'कार्ल मार्क्स', 'मैक्स वेबर', 'समनर', 'A'),
(1103, 'BA', 'Foundation Course', 15, 'श्रीमद्भगवद्गीता के अनुसार निष्काम कर्म का क्या अर्थ है?', 'फल की इच्छा के बिना कर्म करना', 'कर्म न करना', 'केवल फल की चिंता करना', 'सकाम कर्म करना', 'A'),
(1104, 'BA', 'Foundation Course', 15, 'महात्मा गांधी के अनुसार \"सत्य ही ईश्वर है\" यह किस विचार पर आधारित है?', 'अहिंसा और नैतिक मूल्य', 'केवल राजनीतिक लाभ', 'धार्मिक संकीर्णता', 'भौतिकवाद', 'A'),
(1105, 'BA', 'Foundation Course', 15, 'स्वामी विवेकानंद के बचपन का नाम क्या था?', 'नरेंद्रनाथ दत्त', 'मूलशंकर', 'गदाधर', 'महेशदास', 'A'),
(1106, 'BA', 'Foundation Course', 15, 'विश्व पर्यावरण दिवस प्रतिवर्ष किस तिथि को मनाया जाता है?', '5 जून', '22 अप्रैल', '16 सितंबर', '1 दिसंबर', 'A'),
(1107, 'BA', 'Foundation Course', 15, 'रामचरितमानस में \"शबरी के बेर\" का प्रसंग किस नैतिक मूल्य को दर्शाता है?', 'प्रेम और भक्ति में भेदभाव का न होना', 'ऊंच-नीच का भाव', 'राजसी वैभव', 'अहंकार', 'A'),
(1108, 'BA', 'Foundation Course', 15, 'भारत के राष्ट्रीय ध्वज (तिरंगे) में चक्र किसका प्रतीक है?', 'गतिशीलता और प्रगति', 'शांति', 'त्याग', 'समृद्धि', 'A'),
(1109, 'BA', 'Foundation Course', 15, 'पर्यावरण संरक्षण के लिए प्रसिद्ध \"चिपको आंदोलन\" के प्रणेता कौन थे?', 'सुंदरलाल बहुगुणा', 'मेधा पाटकर', 'बाबा आमटे', 'सलीम अली', 'A'),
(1110, 'BA', 'Foundation Course', 15, 'डॉ. सर्वपल्ली राधाकृष्णन के जन्मदिवस (5 सितंबर) को किस रूप में मनाया जाता है?', 'शिक्षक दिवस', 'बाल दिवस', 'युवा दिवस', 'किसान दिवस', 'A'),
(1111, 'BA', 'Foundation Course', 15, 'हिंदी भाषा किस लिपि में लिखी जाती है?', 'देवनागरी', 'गुरुमुखी', 'रोमन', 'फारसी', 'A'),
(1112, 'BA', 'Foundation Course', 15, 'संविधान के किस अनुच्छेद के तहत हिंदी को संघ की राजभाषा घोषित किया गया है?', 'अनुच्छेद 343(1)', 'अनुच्छेद 356', 'अनुच्छेद 370', 'अनुच्छेद 32', 'A'),
(1113, 'BA', 'Foundation Course', 15, 'विश्व हिंदी दिवस कब मनाया जाता है?', '10 जनवरी', '14 सितंबर', '26 जनवरी', '15 अगस्त', 'A'),
(1114, 'BA', 'Foundation Course', 15, 'भारत में राष्ट्रीय हिंदी दिवस कब मनाया जाता है?', '14 सितंबर', '10 जनवरी', '2 अक्टूबर', '5 नवंबर', 'A'),
(1115, 'BA', 'Foundation Course', 15, 'स्वर वर्णों की कुल संख्या हिंदी भाषा में मुख्य रूप से कितनी मानी जाती है?', '11', '13', '33', '52', 'A'),
(1116, 'BA', 'Foundation Course', 15, 'हिंदी वर्णमाला में कुल कितने व्यजंन होते हैं (मूल रूप से)?', '33', '11', '25', '45', 'A'),
(1117, 'BA', 'Foundation Course', 15, 'संधि के मुख्य रूप से कितने भेद होते हैं?', 'तीन (स्वर, व्यंजन, विसर्ग)', 'दो', 'चार', 'पांच', 'A'),
(1118, 'BA', 'Foundation Course', 15, 'समानार्थी या एक जैसा अर्थ देने वाले शब्दों को क्या कहा जाता है?', 'पर्यायवाची शब्द', 'विलोम शब्द', 'तद्भव शब्द', 'अनेकार्थी शब्द', 'A'),
(1119, 'BA', 'Foundation Course', 15, 'विपरीत अर्थ व्यक्त करने वाले शब्दों को क्या कहते हैं?', 'विलोम शब्द', 'पर्यायवाची', 'तत्सम', 'देशज', 'A'),
(1120, 'BA', 'Foundation Course', 15, 'जो शब्द संस्कृत भाषा से बिना किसी परिवर्तन के हिंदी में प्रयोग होते हैं, उन्हें क्या कहते हैं?', 'तत्सम शब्द', 'तद्भव शब्द', 'देशज शब्द', 'विदेशज शब्द', 'A'),
(1121, 'BA', 'Foundation Course', 15, 'संस्कृत के वे शब्द जो थोड़ा परिवर्तित होकर हिंदी में प्रयुक्त होते हैं, कहलाते हैं:', 'तद्भव शब्द', 'तत्सम शब्द', 'विदेशी शब्द', 'संकर शब्द', 'A'),
(1122, 'BA', 'Foundation Course', 15, 'शब्द के प्रारंभ में जुड़कर उसके अर्थ में परिवर्तन करने वाले शब्दांश को क्या कहते हैं?', 'उपसर्ग', 'प्रत्यय', 'समास', 'कारक', 'A'),
(1123, 'BA', 'Foundation Course', 15, 'शब्द के अंत में जुड़कर नया अर्थ बनाने वाले शब्दांश को क्या कहते हैं?', 'प्रत्यय', 'उपसर्ग', 'संधि', 'अव्यय', 'A'),
(1124, 'BA', 'Foundation Course', 15, 'दो या दो से अधिक शब्दों के मेल से बने नए सार्थक शब्द को क्या कहते हैं?', 'समास', 'संधि', 'वाक्य', 'पद', 'A'),
(1125, 'BA', 'Foundation Course', 15, 'जिस समास में दोनों पद प्रधान होते हैं, उसे क्या कहते हैं?', 'द्वंद्व समास', 'द्विगु समास', 'बहुव्रीहि समास', 'तत्पुरुष समास', 'A'),
(1126, 'BA', 'Foundation Course', 15, 'जिस समास का पहला पद संख्यावाचक होता है, उसे क्या कहते हैं?', 'द्विगु समास', 'अव्ययीभाव समास', 'कर्मधारय समास', 'द्वंद्व समास', 'A'),
(1127, 'BA', 'Foundation Course', 15, 'शुद्ध वर्तनी का चयन कीजिए:', 'उज्ज्वल', 'उज्वल', 'उज्जवल', 'उजवल', 'A'),
(1128, 'BA', 'Foundation Course', 15, 'वाक्य के मुख्य रूप से कितने अंग होते हैं?', 'दो (उद्देश्य और विधेय)', 'तीन', 'चार', 'पाँच', 'A'),
(1129, 'BA', 'Foundation Course', 15, '\"आँखों का तारा होना\" मुहावरे का सही अर्थ क्या है?', 'अत्यधिक प्रिय होना', 'आँख में दर्द होना', 'धोखा देना', 'बहुत दूर होना', 'A'),
(1130, 'BA', 'Foundation Course', 15, '\"नाच न जाने आँगन टेढ़ा\" लोकोक्ति का सही अर्थ क्या है?', 'अपनी कमी छिपाने के लिए दूसरों में दोष निकालना', 'अच्छा नृत्य करना', 'घर का आँगन खराब होना', 'नृत्य प्रतियोगिता जीतना', 'A'),
(1131, 'BA', 'Foundation Course', 15, 'सम्प्रेषण (Communication) का शाब्दिक अर्थ क्या है?', 'विचारों का आदान-प्रदान करना', 'अकेले बात करना', 'पुस्तक पढ़ना', 'मौन रहना', 'A'),
(1132, 'BA', 'Foundation Course', 15, 'प्रभावी सम्प्रेषण में मुख्य बाधा क्या हो सकती है?', 'भाषा और शोर (Noise)', 'स्पष्टता', 'सहानुभूति', 'उचित माध्यम', 'A'),
(1133, 'BA', 'Foundation Course', 15, 'प्रशासनिक पत्राचार में अर्ध-सरकारी पत्र (Semi-Official Letter) का प्रयोग कब किया जाता है?', 'अधिकारियों के बीच व्यक्तिगत या ध्यानाकर्षण हेतु', 'सामान्य जनता के लिए', 'केवल निविदा हेतु', 'विज्ञापन हेतु', 'A'),
(1134, 'BA', 'Foundation Course', 15, 'संक्षेपण (Summarization) करते समय मूल अनुच्छेद का आकार लगभग कितना होना चाहिए?', 'एक-तिहाई (1/3)', 'आधा (1/2)', 'दो-तिहाई (2/3)', 'मूल के बराबर', 'A'),
(1135, 'BA', 'Foundation Course', 15, 'पल्लवन (Elaboration) का अर्थ क्या है?', 'विचार या भाव का विस्तार करना', 'संक्षिप्त करना', 'काटना-छांटना', 'अनुवाद करना', 'A'),
(1136, 'BA', 'Foundation Course', 15, 'अंग्रेजी शब्द \"Notification\" का हिंदी पारिभाषिक शब्द क्या है?', 'अधिसूचना', 'अनुस्मारक', 'परिपत्र', 'विज्ञापन', 'A'),
(1137, 'BA', 'Foundation Course', 15, 'अंग्रेजी शब्द \"Approval\" का सही हिंदी अर्थ क्या है?', 'अनुमोदन', 'अस्वीकृति', 'संसोधन', 'स्थानांतरण', 'A'),
(1138, 'BA', 'Foundation Course', 15, 'कबीरदास के अनुसार सर्वोत्तम नैतिक मूल्य क्या है?', 'सत्य और मानवता', 'धन-संपत्ति', 'अहंकार', 'दिखावा', 'A'),
(1139, 'BA', 'Foundation Course', 15, 'सच्चे मित्र की पहचान कब होती है?', 'विपत्ति के समय', 'उत्सव के समय', 'धन मिलने पर', 'रोजमर्रा के काम में', 'A'),
(1140, 'BA', 'Foundation Course', 15, 'श्रीमद्भगवद्गीता में कितने अध्याय हैं?', '18 अध्याय', '12 अध्याय', '24 अध्याय', '10 अध्याय', 'A'),
(1141, 'BA', 'Foundation Course', 15, 'पंचतंत्र के रचनाकार कौन हैं?', 'विष्णु शर्मा', 'बाणभट्ट', 'कालिदास', 'कौटिल्य', 'A'),
(1142, 'BA', 'Foundation Course', 15, 'पंचतंत्र की कहानियों का मुख्य उद्देश्य क्या है?', 'व्यवहारिक एवं नैतिक शिक्षा देना', 'केवल मनोरंजन', 'ऐतिहासिक ज्ञान', 'वैज्ञानिक खोजें', 'A'),
(1143, 'BA', 'Foundation Course', 15, 'रवींद्रनाथ टैगोर को उनकी किस कृति के लिए नोबेल पुरस्कार मिला था?', 'गीतांजलि', 'गोरा', 'काबूलीवाला', 'पोस्ट मास्टर', 'A'),
(1144, 'BA', 'Foundation Course', 15, 'हमारे राष्ट्रीय गान \"जन गण मन\" के रचयिता कौन हैं?', 'रवींद्रनाथ टैगोर', 'बंकिमचंद्र चटर्जी', 'माखनलाल चतुर्वेदी', 'इकबाल', 'A'),
(1145, 'BA', 'Foundation Course', 15, 'राष्ट्रीय गीत \"वंदे मातरम्\" किस ग्रन्थ/उपन्यास से लिया गया है?', 'आनंदमठ', 'गीतांजलि', 'गोदान', 'कपालकुंडला', 'A'),
(1146, 'BA', 'Foundation Course', 15, 'पर्यावरण प्रदूषण का मुख्य मानव निर्मित कारण क्या है?', 'अंधाधुंध औद्योगीकरण और वनों की कटाई', 'सूर्य का प्रकाश', 'प्राकृतिक वर्षा', 'ज्वालामुखी', 'A'),
(1147, 'BA', 'Foundation Course', 15, 'मानवाधिकार दिवस कब मनाया जाता है?', '10 दिसंबर', '24 अक्टूबर', '15 अगस्त', '1 मई', 'A'),
(1148, 'BA', 'Foundation Course', 15, 'नैतिकता (Ethics) का संबंध मुख्यतः किससे है?', 'उचित और अनुचित के व्यवहारिक ज्ञान से', 'केवल कानून से', 'शारीरिक बल से', 'आर्थिक लाभ से', 'A'),
(1149, 'BA', 'Foundation Course', 15, 'आत्मअनुशासन (Self-Discipline) का सबसे बड़ा लाभ क्या है?', 'व्यक्तिगत और सामाजिक विकास', 'दूसरों पर नियंत्रण', 'अहंकार में वृद्धि', 'समय की बर्बादी', 'A'),
(1150, 'BA', 'Foundation Course', 15, 'भारत में \"डिजिटल इंडिया\" अभियान का मुख्य उद्देश्य क्या है?', 'सरकारी सेवाओं को इलेक्ट्रॉनिक रूप से जनता तक पहुंचाना', 'कागज का उपयोग बढ़ाना', 'इंटरनेट बंद करना', 'केवल टीवी देखना', 'A'),
(1151, 'BA', 'Foundation Course', 15, 'कंप्यूटर और इंटरनेट के क्षेत्र में \"Cyber Ethics\" का अर्थ क्या है?', 'डिजिटल दुनिया में नैतिक और सही आचरण करना', 'हैकिंग करना', 'दूसरों का डेटा चुराना', 'वायरस फैलाना', 'A'),
(1152, 'BA', 'Foundation Course', 15, 'स्वच्छ भारत अभियान की औपचारिक शुरुआत किस वर्ष हुई थी?', '2014 (2 अक्टूबर)', '2019', '2015', '2012', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int NOT NULL,
  `roll_no` varchar(50) NOT NULL,
  `subject_id` int NOT NULL,
  `attempt_number` int NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `duration_text` varchar(50) DEFAULT '33 mins',
  `reference_code` varchar(100) NOT NULL,
  `score` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `roll_no`, `subject_id`, `attempt_number`, `start_time`, `end_time`, `duration_text`, `reference_code`, `score`) VALUES
(43, 'e2072553699gsa', 6, 1, '2026-06-16 11:04:21', '2026-06-16 11:22:11', '17 mins 50 secs', 'REF-e2072553699gsa-160626-112211', 0),
(44, 'e2072553699gsa', 7, 1, '2026-06-18 11:00:21', '2026-06-18 11:19:49', '19 mins 28 secs', 'REF-e2072553699gsa-180626-111949', 0),
(45, 'e2072553699gsa', 8, 1, '2026-06-19 10:54:10', '2026-06-19 11:14:08', '19 mins 58 secs', 'REF-e2072553699gsa-190626-111408', 0),
(46, 'e2072553699gsa', 9, 1, '2026-06-20 10:28:22', '2026-06-20 10:47:38', '19 mins 16 secs', 'REF-e2072553699gsa-200626-104738', 0),
(47, 'e2072553699gsa', 1, 1, '2026-06-20 10:59:09', '2026-06-20 11:16:28', '17 mins 19 secs', 'REF-e2072553699gsa-200626-111628', 0),
(48, 'e2072553699gsa', 2, 1, '2026-06-20 11:28:54', '2026-06-20 11:43:10', '14 mins 16 secs', 'REF-e2072553699gsa-200626-114310', 0),
(49, 'e2072553699gsa', 3, 1, '2026-06-20 12:00:36', '2026-06-20 12:13:49', '13 mins 13 secs', 'REF-e2072553699gsa-200626-121349', 0),
(50, 'e2072553699gsa', 4, 1, '2026-06-20 12:15:55', '2026-06-20 12:28:14', '12 mins 19 secs', 'REF-e2072553699gsa-200626-122814', 0),
(51, 'e2072553699gsa', 1, 2, '2026-09-07 11:54:18', '2026-09-07 12:20:40', '26 mins 22 secs', 'REF-e2072553699gsa-070926-122040', 0),
(59, 'e2072553567pri', 10, 1, '2026-09-08 09:12:05', '2026-09-08 09:14:20', '2 mins 15 secs', 'REF-e2072553567pri-080926-091420', 0),
(61, 'e2072553567pri', 11, 1, '2026-09-09 11:45:43', '2026-09-09 11:55:06', '9 mins 23 secs', 'REF-e2072553567prigsa-090926-115506', 0),
(62, 'e2072553567prigsa', 12, 1, '2026-09-11 11:41:08', '2026-09-11 11:54:24', '13 mins 16 secs', 'REF-e2072553567prigsa-110926-115424', 0);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int NOT NULL,
  `exam_id` int DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `marks_obtained` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `doc_type` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Pending','Verified','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `request_date` datetime NOT NULL,
  `admin_remark` text COLLATE utf8mb4_general_ci,
  `is_deleted` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`id`, `student_id`, `doc_type`, `status`, `request_date`, `admin_remark`, `is_deleted`) VALUES
(23, 20, 'Marksheet', 'Verified', '2025-12-28 20:06:53', NULL, 0),
(24, 20, 'ID Card', 'Verified', '2025-12-28 20:31:36', NULL, 0),
(25, 20, 'Certificate', 'Verified', '2025-12-29 01:43:38', NULL, 0),
(26, 20, 'Report Card', 'Verified', '2025-12-29 02:05:27', NULL, 0),
(27, 21, 'Certificate', 'Verified', '2026-03-24 00:42:03', NULL, 0),
(28, 23, 'Certificate', 'Rejected', '2026-04-22 14:19:43', NULL, 0),
(29, 23, 'Certificate', 'Verified', '2026-04-22 14:23:43', NULL, 0),
(30, 24, 'Certificate', 'Verified', '2026-04-22 14:40:28', NULL, 0),
(31, 25, 'Certificate', 'Verified', '2026-04-22 15:03:33', NULL, 0),
(32, 26, 'Certificate', 'Verified', '2026-04-22 15:14:38', NULL, 0),
(33, 27, 'Certificate', 'Verified', '2026-04-22 15:37:42', NULL, 0),
(34, 28, 'Certificate', 'Verified', '2026-04-22 15:43:50', NULL, 0),
(35, 29, 'Certificate', 'Verified', '2026-04-22 15:55:40', NULL, 0),
(36, 30, 'Certificate', 'Verified', '2026-04-22 16:07:02', NULL, 0),
(37, 31, 'Certificate', 'Verified', '2026-04-22 17:44:14', NULL, 0),
(38, 32, 'Certificate', 'Verified', '2026-04-23 08:25:29', NULL, 0),
(39, 33, 'Certificate', 'Verified', '2026-04-23 08:55:31', NULL, 0),
(40, 37, 'Certificate', 'Verified', '2026-05-22 13:41:22', NULL, 0),
(41, 38, 'Certificate', 'Rejected', '2026-05-22 13:50:51', NULL, 0),
(42, 33, 'Admit Card', 'Verified', '2026-06-08 15:40:00', NULL, 0),
(43, 36, 'Admit Card', 'Verified', '2026-06-09 07:36:06', NULL, 0),
(45, 41, 'Admit Card', 'Verified', '2026-06-09 09:47:45', NULL, 0),
(46, 42, 'Admit Card', 'Verified', '2026-06-09 10:21:23', NULL, 0),
(47, 43, 'Admit Card', 'Verified', '2026-06-09 11:04:40', NULL, 0),
(48, 44, 'Admit Card', 'Verified', '2026-06-09 11:21:03', NULL, 0),
(49, 40, 'Admit Card', 'Verified', '2026-06-09 11:33:48', NULL, 0),
(50, 45, 'Admit Card', 'Verified', '2026-06-09 12:04:43', NULL, 0),
(51, 39, 'Admit Card', 'Verified', '2026-06-09 12:11:03', NULL, 0),
(52, 46, 'Admit Card', 'Verified', '2026-06-09 12:44:53', NULL, 0),
(53, 33, 'ID Card', 'Rejected', '2026-06-09 13:29:52', NULL, 0),
(54, 47, 'Admit Card', 'Verified', '2026-06-09 13:58:38', NULL, 0),
(55, 48, 'Admit Card', 'Verified', '2026-06-09 14:09:19', NULL, 0),
(56, 49, 'Admit Card', 'Verified', '2026-06-09 21:55:30', NULL, 0),
(57, 51, 'Admit Card', '', '2026-07-10 13:53:41', NULL, 1),
(58, 51, 'Certificate', 'Verified', '2026-07-10 13:55:02', NULL, 0),
(59, 21, 'ID Card', 'Verified', '2026-07-18 11:04:27', NULL, 0),
(60, 41, 'Marksheet', 'Rejected', '2026-08-01 22:45:13', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `roll_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `enrollment_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_fee` decimal(10,2) DEFAULT '0.00',
  `semester` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'I',
  `batch_time` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course_fee` decimal(10,2) DEFAULT '10000.00',
  `admission_date` date DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'default.png',
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT '0',
  `session` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '2025',
  `is_verified` int DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'Not Verified',
  `last_login` datetime DEFAULT NULL,
  `current_viewing` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lms_access_expiry` date DEFAULT NULL,
  `app_locked` tinyint(1) DEFAULT '0',
  `batch_start_time` time DEFAULT '08:00:00',
  `batch_end_time` time DEFAULT '10:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `name`, `father_name`, `roll_no`, `enrollment_no`, `email`, `phone`, `course`, `total_fee`, `semester`, `batch_time`, `course_fee`, `admission_date`, `dob`, `gender`, `address`, `photo`, `password`, `mobile`, `is_deleted`, `session`, `is_verified`, `status`, `last_login`, `current_viewing`, `lms_access_expiry`, `app_locked`, `batch_start_time`, `batch_end_time`) VALUES
(21, 20, NULL, 'DEVISINGH GOUND', 'APP-2026-0021', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, NULL, '2000-01-01', 'Female', 'GRAM DHONDA', 'UPD_1776915643.png', NULL, NULL, 0, '2025-2026', 1, 'Active', NULL, NULL, '2027-03-24', 0, '08:00:00', '10:00:00'),
(23, 23, 'ABHISHEK VISHWAKARMA', 'ARVIND VISHWAKARMA', 'APP-2025-7283', NULL, NULL, NULL, 'TC VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2011-11-28', 'Male', 'WARD NO 09 TENDUKHEDA', 'UPD_1776847591.png', NULL, '9981016447', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(24, 24, 'LOKPAL GOUND', 'DEVISARAN THAKUR', 'APP-2025-7995', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2000-01-01', 'Male', 'GRAM BAHERIYA', 'UPD_1776849000.png', NULL, '8349264290', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(25, 25, 'SATYAM THAKUR', 'SOORAJ SINGH', 'APP-2025-8206', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2000-01-01', 'Male', 'GRAM BAHERIYA', 'UPD_1776850385.png', NULL, '7489251478', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(26, 26, 'SAHIL GOUND', 'MUKKAMD SINGH', 'APP-2025-5081', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2000-01-01', 'Male', 'GRAM IMLIDOL', 'UPD_1776850937.png', NULL, '8120754518', 0, '2025-2026', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(27, 27, 'KHUSHBOO KHAN', 'SHEKH SAJID', 'APP-2025-1543', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2000-01-01', 'Male', 'TENDUKHEDA', 'UPD_1776852440.png', NULL, '7452568912', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(28, 28, 'CHAHAT KHAN', 'ABDUL KALAM', 'APP-2025-3975', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2000-01-01', 'Female', 'TENDUKHEDA', 'UPD_1776852795.png', NULL, '7423568912', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(29, 29, 'SHAZIYA ALI', 'PARWEJ ALI', 'APP-2025-1099', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2000-01-01', 'Female', 'TENDUKHEDA', 'UPD_1776853520.png', NULL, '9301996776', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(30, 30, 'NAVED KHAN', 'SHAHEJAD KHAN', 'APP-2025-4154', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2000-01-01', 'Male', 'TENDUKHEDA', 'UPD_1776854172.png', NULL, '9056235689', 0, '2025-2026', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(31, 31, 'MATENDRA GOUND', 'DEVISARAN THAKUR', 'APP-2025-1617', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-22', '2000-01-01', 'Male', 'GRAM BAHERIYA', 'UPD_1776860027.png', NULL, '8546124578', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-22', 0, '08:00:00', '10:00:00'),
(32, 32, 'RAJKISHOR RAIKWAR', 'BABLOO RAIKWAR', 'rajkishor', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-23', '2000-01-01', 'Male', 'WARD NO 09 TENDUKHEDA', 'UPD_1776912901.png', NULL, '8305453858', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-23', 0, '08:00:00', '10:00:00'),
(33, 33, 'MOSHMI PORTE', 'NIJAM SINGH PORTE', 'e2072553566lka', NULL, NULL, '9302681738', 'DCA', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-23', '2000-01-01', 'Female', 'GRAM BANDHNA', 'STU_33_1780941338.jpeg', NULL, '7542154578', 0, '2025-2026', 1, 'Active', NULL, NULL, '2027-04-23', 0, '08:00:00', '10:00:00'),
(35, 35, 'SHIVANI RAIKWAR', 'NA', 'APP-2025-6348', NULL, NULL, NULL, 'TC ACADAMY VALANTIYAR', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-04-23', '2000-01-01', 'Female', 'WARD NO 09 TENDUKHEDA', 'UPD_1776930606.jpg', NULL, '8120751545', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-04-23', 0, '08:00:00', '10:00:00'),
(36, 36, 'MUSKAN PORTE', 'NIJAM SINGH PORTE', 'APP-2025-4092', NULL, NULL, '7489454141', 'DCA', 0.00, 'I', NULL, 10000.00, '2026-04-26', '2000-01-01', 'Female', 'GRAM BANDHNA', 'stu_36_1780970712.jpeg', NULL, '7489254141', 0, '2025-26', 1, 'Active', NULL, NULL, '2027-04-26', 0, '08:00:00', '10:00:00'),
(37, 37, 'LADLI GOUND', 'ANIL SINGH', 'APP-2025-4466', NULL, NULL, NULL, 'OLYMPAID', 0.00, 'I', NULL, 10000.00, '2026-05-22', '2016-06-03', 'Female', 'GRAM KACHHAR', '1779437430_ladli.jpeg', NULL, '7828993398', 0, '2025-26', 0, 'Active', NULL, NULL, '2027-05-22', 0, '08:00:00', '10:00:00'),
(38, 38, 'LALTA GOUND', 'RAJKUMAR GOUND', 'APP-2025-1624', NULL, NULL, '9770567969', 'OLYMPAID', 0.00, 'I', NULL, 10000.00, '2026-05-22', '2012-06-16', 'Female', 'GRAM KACHHAR', 'stu_38_1779438000.jpeg', NULL, '9770567969', 0, '2025-26', 0, 'Active', NULL, NULL, '2027-05-22', 0, '08:00:00', '10:00:00'),
(39, 39, 'RUPESH GOND', 'UTTAM SING', 'e2072553699gg', NULL, 'rupesh@gmail.com', '9407893713', 'PGDCA', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-06-09', '2000-01-01', 'Male', 'GRAM SARVAKUHI', 'UPD_1780972155.jpeg', 'e2072553600', '9407893713', 0, '2024-2025', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(40, 40, 'SHUBHAM YADAV', 'SANJESH YADAV', 'e2072553589vja', NULL, 'shubham@gmailcom', '8319274009', 'PGDCA', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-06-09', '2000-01-01', 'Male', 'GRAM JHAMARA', 'UPD_1780974706.jpg', 'e2072553589', '8319274009', 0, '2024-2025', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(41, 41, 'SATYAM THAKUR', 'NIRANJAN THAKUR', 'e2072553449kna', NULL, 'satyam@gmail.com', '8839282414', 'DCA', 0.00, 'I', NULL, 10000.00, '2026-06-09', '2000-01-01', 'Male', 'WARD NO 10 TARADEHI ROAD TENDUKHEDA', 'stu_41_1780978643.jpg', 'e2072553449', '8839282414', 0, '2025-26', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(42, 42, 'MANISH GOUND', 'SHOBHA SINGH', 'e2072553567jka', NULL, 'manish@gmail.com', NULL, 'DCA', 16000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-06-09', '2000-01-01', 'Male', 'GRAM KACHHAR', 'UPD_1780980624.jpeg', 'e2072553567', '7805994266', 0, '2024-2025', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(43, 43, 'PUSHPLATA GOUND', 'RAJKUMAR', 'e2072553478via', NULL, 'pusplata@gmail.com', '7000719606', 'PGDCA', 0.00, 'I', NULL, 10000.00, '2026-06-09', '2000-01-01', 'Female', 'GRAM ALOUNI', 'stu_43_1780983108.jpeg', 'e2072553478', '7000719606', 0, '2025-26', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(44, 44, 'SUMIT KEWAT', 'NAND LAL KEWAT', 'e2072553477eoa', NULL, 'sumit@gmail.com', '6267021566', 'PGDCA', 0.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-06-09', '2000-01-01', 'Male', 'WARD NO 01 TENDUKHEDA', 'UPD_1780992957.jpeg', 'e2072553477', '6267021566', 0, '2024-2025', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(45, 45, 'NIKHIL YADAV', 'MADHAV YADAV', 'e2072553460nsa', NULL, 'nikhil@gmail.com', '8358986726', 'DCA', 12000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-06-09', '2000-01-01', 'Male', 'GRAM KHAMARIYA KALAN', 'stu_45_1780986844.jpeg', 'e2072553460', '8358986726', 0, '2024-2025', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(46, 46, 'ASHUTOSH DUBEY', 'DEVENDRA DUBEY', 'e2072553450mba', NULL, 'ashutosh@gmal.com', '9685484638', 'DCA', 0.00, 'I', NULL, 10000.00, '2026-06-09', '2000-01-01', 'Male', 'GRAM BAGDARI', 'stu_46_1780988834.jpg', 'e2072553450', '9685484638', 0, '2025-26', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(47, 47, 'JANVEE PORTE', 'NANHE SINGH PORTE', 'e2072554083fka', NULL, 'janvee@gmail.com', '8817090541', 'DCA', 0.00, 'I', NULL, 10000.00, '2026-06-09', '2000-01-01', 'Female', 'GRAM BANDHNA SAILWADA', 'stu_47_1780993511.jpeg', 'e2072554083', '8817090541', 0, '2025-26', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(48, 48, 'PARMI GOUND', 'SATISH GOUND', 'e2072554083par', NULL, 'parmi@gmail.com', '9243499491', 'DCA', 12000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-06-09', '2000-01-01', 'Female', 'GRAM BADIPURA', 'UPD_1780994266.jpeg', 'e2072554000\r\n', '9243499491', 0, '2024-2025', 1, 'Active', NULL, NULL, '2027-06-09', 0, '08:00:00', '10:00:00'),
(49, 49, 'AKHLESH GOUND', 'KUSHAL SINGH', 'e2072553699gsa', NULL, NULL, '6263490585', 'DCA', 0.00, 'I', NULL, 10000.00, '2026-06-09', '2000-01-01', 'Male', 'GRAM BADIPURA', 'stu_49_1781022306.jpg', 'e2072553699', '6263490585', 0, '2025-26', 1, 'Active', NULL, NULL, '2027-06-14', 0, '08:00:00', '10:00:00'),
(51, 51, 'KAPIL KUMAR KEWAT', 'BHIKAM', 'APP-2025-6077', NULL, NULL, '9201226509', 'OLYMPAID', 0.00, 'I', NULL, 10000.00, '2026-07-10', '2008-10-21', 'Male', 'GRAM JHAROLI', 'stu_51_1783671789.jpg', NULL, '9201226509', 0, '2025-26', 1, 'Active', NULL, NULL, '2027-07-10', 0, '08:00:00', '10:00:00'),
(52, 52, 'JYOTI LODHI', 'RAM LODHI', 'jyoti', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '10:00 AM - 11:00 AM', 10000.00, '2026-08-06', '2000-01-01', 'Female', 'GRAM BANSI', 'UPD_1786038859.jpeg', NULL, '6232370752', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-08-06', 0, '09:50:00', '00:00:00'),
(53, 53, 'ADITYA SINGH', 'Kushal singh', 'APP-2025-2426', NULL, NULL, NULL, 'DCA', 0.00, 'I', NULL, 10000.00, '2026-08-07', '2005-02-01', 'Male', 'Tendukheda', '1786079141_1000047263.jpg', NULL, '7869131145', 0, '2025-26', 0, 'Active', NULL, NULL, '2027-08-07', 0, '08:00:00', '10:00:00'),
(55, 55, 'ANKIT PRAJAPATI', 'MUKESH PRAJAPATI', 'APP-2026-8747', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-08-09', '2008-07-12', 'Male', 'WARD NO 14 CHOURAI', 'UPD_1786294109.jpg', NULL, '9301878399', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-08-09', 0, '08:00:00', '10:00:00'),
(56, 56, 'KHUSHI TAMRKAR', 'RAJKUMAR TAMRKAR', 'APP-2026-6209', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '10:00 AM - 11:00 AM', 10000.00, '2026-08-09', '2008-12-04', 'Female', 'WARD NO 08 TENDUKHEDA', 'UPD_1786294087.jpg', NULL, '9301405397', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-08-09', 0, '08:00:00', '10:00:00'),
(57, 57, 'SANDEEP SINGH LODHI', 'PRAHLAD SINGH LODHI', 'APP-2026-6076', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-08-09', '2003-03-14', 'Male', 'GRAM KULUA DINARI', 'UPD_1786294060.jpg', NULL, '9109239629', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-08-09', 0, '08:00:00', '10:00:00'),
(58, 58, 'ANGAD PAL', 'SANTOSH PAL', 'APP-2026-3096', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-08-09', '2005-04-30', 'Male', 'GRAM PINDRAI', '1786292851_WhatsApp Image 2026-08-09 at 9.55.58 PM.jpeg', NULL, '9301370067', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-08-09', 0, '08:00:00', '10:00:00'),
(59, 59, 'RAVENDRA GOUND', 'HARIRAM GOUND', 'APP-2026-7231', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-08-09', '2008-10-08', 'Male', 'GRAM KACHHAR DHANGUR', '1786293624_51.png', NULL, '9331484565', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-08-09', 0, '08:00:00', '10:00:00'),
(60, 60, 'PARV JAIN', 'RAVINDRA JAIN', 'APP-2026-6678', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '10:00 AM - 11:00 AM', 10000.00, '2026-08-09', '2007-01-25', 'Male', 'WARD NO 11 TENDUKHEDA', 'UPD_1786294017.jpg', NULL, '8305511598', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-08-09', 0, '10:00:00', '00:00:00'),
(61, 61, 'PEEYUSH MEHRA', 'SHARDA MEHRA', 'APP-2026-7618', NULL, NULL, NULL, 'DCA', 0.00, 'I', NULL, 10000.00, '2026-08-09', '2008-06-13', 'Male', 'TENDUKHEDA', '1786294252_WhatsApp Image 2026-08-09 at 10.14.07 PM.jpeg', NULL, '7693017130', 0, '2025-2026', 0, 'Active', NULL, NULL, '2027-08-09', 0, '10:00:00', '00:00:00'),
(62, 62, 'JITENDRA CHAKRAVARTEE', 'VIJAY CHAKRAVARTEE', 'APP-2026-7203', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-08-09', '2007-07-02', 'Male', 'GRAM PINDRAI', 'UPD_1786294519.jpg', NULL, '7879615547', 0, '2024-2025', 0, 'Active', NULL, NULL, '2027-08-09', 0, '09:58:00', '00:00:00'),
(63, 63, 'NEELESH AHIRWAR', 'BARATI AHIRWAR', 'APP-2026-3090', NULL, NULL, NULL, 'DCA', 12000.00, 'I', '08:00 AM - 09:00 AM', 10000.00, '2026-08-09', '2007-06-28', 'Male', 'GRAM HARDUAA PANJHI', 'UPD_1786294797.jpg', NULL, '7694807575', 0, '2025-2026', 0, 'Active', NULL, NULL, '2027-08-09', 0, '09:58:00', '00:01:00'),
(64, 64, 'SHRIKANT SAHU', 'NA', '6265598921', NULL, NULL, '6265598921', 'PGDCA', 13000.00, 'I', NULL, 10000.00, '2026-08-13', '2000-01-01', 'Male', 'GRAM KUDPURA', 'stu_64_1786630100.jpeg', NULL, '6265598921', 0, '2025-2026', 0, 'Active', NULL, NULL, '2027-08-13', 0, '10:00:00', '00:00:00'),
(65, 65, 'ASHISH SEN', 'BABLOO SEN', 'APP-2026-1261', NULL, NULL, NULL, 'CPCT TYPING', 0.00, 'I', NULL, 10000.00, '2026-08-17', '2006-02-08', 'Male', 'WARD NO 08 VIDHYANAGAR TENDUKHEDA', '1786940750_PIC.jpg', NULL, '9174055212', 0, '2026-27', 0, 'Active', NULL, NULL, '2027-08-17', 0, '08:00:00', '10:00:00'),
(66, 66, 'PRIYANKA GOUND', 'NA', 'e2072553567pri', NULL, NULL, NULL, 'BA', 0.00, 'I', NULL, 10000.00, '2026-09-07', '2000-01-01', 'Female', 'GRAM KHAMKHEDA', '1788759893_WhatsApp Image 2026-09-07 at 11.14.00 AM.jpeg', '123456', '8120754577', 0, '2024-2025', 1, 'Active', NULL, NULL, '2027-09-07', 0, '08:00:00', '10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

CREATE TABLE `student_documents` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `doc_type` varchar(100) NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int NOT NULL,
  `subject_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `subject_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `course` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `semester` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `start_time` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `end_time` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_manual_active` int DEFAULT '0',
  `manual_start_date` date DEFAULT NULL,
  `manual_end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `subject_code`, `course`, `semester`, `exam_date`, `start_time`, `end_time`, `is_manual_active`, `manual_start_date`, `manual_end_date`) VALUES
(1, 'Information Technology Tools and Network Basics', NULL, 'DCA', 'I', '2026-06-10', 'Wednesday, 10 June 2026, 8:00 AM', 'Wednesday, 10 June 2026, 8:00 PM', 0, NULL, NULL),
(2, 'Windows and MS Office', NULL, 'DCA', 'I', '2026-06-11', 'Thursday, 11 June 2026, 8:00 AM', 'Thursday, 11 June 2026, 8:00 PM', 0, NULL, NULL),
(3, 'Database Concepts and Introduction to SQL', NULL, 'DCA', 'I', '2026-06-12', 'Friday, 12 June 2026, 8:00 AM', 'Friday, 12 June 2026, 8:00 PM', 0, NULL, NULL),
(4, 'Objects Oriented Programming With C++', NULL, 'DCA', 'I', '2026-06-13', 'Saturday, 13 June 2026, 8:00 AM', 'Saturday, 13 June 2026, 8:00 PM', 0, NULL, NULL),
(5, 'Communication Skills & Personality Development', NULL, 'DCA', 'I', '2026-06-15', 'Monday, 15 June 2026, 8:00 AM', 'Monday, 15 June 2026, 8:00 PM', 0, NULL, NULL),
(6, 'Introduction to Internet and Web Technology', NULL, 'DCA', 'II', '2026-06-16', 'Tuesday, 16 June 2026, 8:00 AM', 'Tuesday, 16 June 2026, 8:00 PM', 0, NULL, NULL),
(7, 'Introduction to Financial Accounting with Tally', NULL, 'DCA', 'II', '2026-06-18', 'Thursday, 18 June 2026, 8:00 AM', 'Thursday, 18 June 2026, 8:00 PM', 0, NULL, NULL),
(8, 'Programming and Problem Solving through Python', NULL, 'DCA', 'II', '2026-06-19', 'Friday, 19 June 2026, 8:00 AM', 'Friday, 19 June 2026, 8:00 PM', 0, NULL, NULL),
(9, 'Introduction to Cyber Security', NULL, 'DCA', 'II', '2026-06-20', 'Saturday, 20 June 2026, 8:00 AM', 'Saturday, 20 June 2026, 8:00 PM', 0, NULL, NULL),
(10, 'Hindi Literature (हिंदी साहित्य)', NULL, 'BA', 'I', '2026-09-07', '10:00 AM', '01:00 PM', 0, NULL, NULL),
(11, 'English Literature (अंग्रेजी साहित्य)', NULL, 'BA', 'I', '2026-09-09', '10:00 AM', '01:00 PM', 0, NULL, NULL),
(12, 'Political Science (राजनीति विज्ञान)', NULL, 'BA', 'I', '2026-09-11', '10:00 AM', '01:00 PM', 0, NULL, NULL),
(13, 'History (इतिहास)', NULL, 'BA', 'I', '2026-09-14', '10:00 AM', '01:00 PM', 0, NULL, NULL),
(14, 'Sociology (समाजशास्त्र)', NULL, 'BA', 'I', '2026-09-16', '10:00 AM', '01:00 PM', 0, NULL, NULL),
(15, 'Foundation Course (नैतिक मूल्य एवं भाषा)', NULL, 'BA', 'I', '2026-09-18', '10:00 AM', '01:00 PM', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('admin','teacher','student') COLLATE utf8mb4_general_ci DEFAULT 'student',
  `qr_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `email`, `mobile`, `password`, `role`, `qr_code`, `created_at`, `status`) VALUES
(1, 'Main Admin', NULL, 'admin@gmail.com', '8120751922', 'admin123', 'admin', NULL, '2025-12-24 16:24:56', 'Active'),
(20, 'REVA BAI GOUND', NULL, 'revathakurr269@gmail.com', '8305876036', '123123', 'student', NULL, '2026-03-23 18:49:34', 'Active'),
(23, 'ABHISHEK VISHWAKARMA', NULL, 'abhis@gmail.com', '9981016447', '123456', 'student', NULL, '2026-04-22 08:45:33', 'Active'),
(24, 'LOKPAL GOUND', NULL, 'lokpald@gmail.com', '8349264290', '123456', 'student', NULL, '2026-04-22 09:08:04', 'Active'),
(25, 'SATYAM THAKUR', NULL, 'satfg@gmail.com', '7489457812', '123456', 'student', NULL, '2026-04-22 09:32:08', 'Active'),
(26, 'SAHIL GOUND', NULL, 'sahil@gmail.com', '8145124578', '123456', 'student', NULL, '2026-04-22 09:41:17', 'Active'),
(27, 'KHUSHBOO KHAN', NULL, 'khusbo@gmail.com', '9098562356', '123456', 'student', NULL, '2026-04-22 10:06:23', 'Active'),
(28, 'CHAHAT KHAN', NULL, 'chahat@gmail.com', '7845124578', '123456', 'student', NULL, '2026-04-22 10:11:34', 'Active'),
(29, 'SHAZIYA ALI', NULL, 'saziyaali@gmail.com', '9301996776', '123456', 'student', NULL, '2026-04-22 10:24:06', 'Active'),
(30, 'NAVED KHAN', NULL, 'naved@gmail.com', '8945123456', '123456', 'student', NULL, '2026-04-22 10:35:12', 'Active'),
(31, 'MATENDRA GOUND', NULL, 'matendrath@gmail.com', '8245131619', '123456', 'student', NULL, '2026-04-22 12:12:52', 'Active'),
(32, 'RAJKISHOR RAIKWAR', NULL, 'rajkishor@gmail.com', '8305453858', '123456', 'student', NULL, '2026-04-23 02:54:10', 'Active'),
(33, 'MOSHMI PORTE', NULL, 'moshmig@gmail.com', '7542154578', 'e2072553576', 'student', NULL, '2026-04-23 03:24:41', 'Active'),
(35, 'SHIVANI RAIKWAR', NULL, 'sivanid@gmail.com', '8120754545', '123456', 'student', NULL, '2026-04-23 07:49:29', 'Active'),
(36, 'MUSKAN PORTE', NULL, 'muskan@gmail.com', NULL, '123456', 'student', NULL, '2026-04-26 05:01:20', 'Active'),
(37, 'LADLI GOUND', NULL, 'ladliss@gmail.com', NULL, '123123', 'student', NULL, '2026-05-22 08:10:30', 'Active'),
(38, 'LALTA GOUND', NULL, 'laltafgh@gmail.com', NULL, '123123', 'student', NULL, '2026-05-22 08:18:47', 'Active'),
(39, 'RUPESH GOND', NULL, 'rupesd@gmail.com', '9407893713', 'e2072553699', 'student', NULL, '2026-06-09 02:28:13', 'Active'),
(40, 'SHUBHAM YADAV', NULL, 'subhams@gmail.com', '8319274009', 'e2072553589', 'student', NULL, '2026-06-09 03:09:54', 'Active'),
(41, 'SATYAM THAKUR', NULL, 'satyam@gmail.com', NULL, 'e2072553449', 'student', NULL, '2026-06-09 04:16:27', 'Active'),
(42, 'MANISH GOUND', NULL, 'manish@gmail.com', '7805994266', 'e2072553567', 'student', NULL, '2026-06-09 04:49:06', 'Active'),
(43, 'PUSHPLATA GOUND', NULL, 'pusplata@gmail.com', '7000719606', 'e2072553478', 'student', NULL, '2026-06-09 05:30:59', 'Active'),
(44, 'SUMIT KEWAT', NULL, 'sumit@gmail.com', '6267021566', 'e2072553477', 'student', NULL, '2026-06-09 05:43:43', 'Active'),
(45, 'NIKHIL YADAV', NULL, 'nikhil@gmail.com', '8358986726', 'e2072553460', 'student', NULL, '2026-06-09 06:32:52', 'Active'),
(46, 'ASHUTOSH DUBEY', NULL, 'ashutosh@gmail.com', NULL, 'e2072553450', 'student', NULL, '2026-06-09 07:06:25', 'Active'),
(47, 'JANVEE PORTE', NULL, 'janvee@gmail.com', '8817090541', 'e2072554083', 'student', NULL, '2026-06-09 08:20:26', 'Active'),
(48, 'PARMI GOUND', NULL, 'parmi@gmail.com', '9243499491', 'e2072554000', 'student', NULL, '2026-06-09 08:35:59', 'Active'),
(49, 'AKHLESH GOUND', NULL, 'akhilesh@gmail.com', NULL, 'e2072553699', 'student', NULL, '2026-06-09 16:22:05', 'Active'),
(51, 'KAPIL KUMAR KEWAT', NULL, 'kapilw@gmail.com', NULL, '123123', 'student', NULL, '2026-07-10 08:19:27', 'Active'),
(52, 'JYOTI LODHI', NULL, 'jyoti@gmail.com', '6232370752', '123456', 'student', NULL, '2026-08-06 17:52:43', 'Active'),
(53, 'ADITYA SINGH', NULL, 'adityasingh39392@gmail.com', NULL, '123456', 'student', NULL, '2026-08-07 05:05:41', 'Active'),
(54, 'SHRIKANT SAHU', NULL, 'shrikantsahu6a65598921@gmail.com', '6265598921', '123123', 'student', NULL, '2026-08-07 07:04:02', 'Active'),
(55, 'ANKIT PRAJAPATI', NULL, 'ankitprajapati53236@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:11:09', 'Active'),
(56, 'KHUSHI TAMRKAR', NULL, 'khushi@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:16:24', 'Active'),
(57, 'SANDEEP SINGH LODHI', NULL, 'sandeep@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:22:43', 'Active'),
(58, 'ANGAD PAL', NULL, 'angad@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:27:31', 'Active'),
(59, 'RAVENDRA GOUND', NULL, 'ravendras@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:40:24', 'Active'),
(60, 'PARV JAIN', NULL, 'parv@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:45:49', 'Active'),
(61, 'PEEYUSH MEHRA', NULL, 'piyush@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:50:52', 'Active'),
(62, 'JITENDRA CHAKRAVARTEE', NULL, 'jitendra@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:54:18', 'Active'),
(63, 'NEELESH AHIRWAR', NULL, 'neelesh@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-09 16:58:50', 'Active'),
(64, 'SHRIKANT SAHU', NULL, 'shrikant@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-08-13 14:06:32', 'Active'),
(65, 'ASHISH SEN', NULL, 'ashishs@gmail.com', NULL, '123456', 'student', NULL, '2026-08-17 04:25:50', 'Active'),
(66, 'PRIYANKA GOUND', NULL, 'priyanka2@gmail.com', '<br /><b>Deprec', '123456', 'student', NULL, '2026-09-07 05:44:53', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `user_biometrics`
--

CREATE TABLE `user_biometrics` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `credential_id` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_logs`
--

CREATE TABLE `verification_logs` (
  `id` int NOT NULL,
  `roll_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `search_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `browser_info` text COLLATE utf8mb4_general_ci,
  `status` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_logs`
--

INSERT INTO `verification_logs` (`id`, `roll_no`, `search_time`, `ip_address`, `browser_info`, `status`) VALUES
(1, 'STU-2025-004', '2025-12-26 20:35:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(2, 'STU-2025-004', '2025-12-26 20:41:13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(3, 'STU-2025-004', '2025-12-26 20:44:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(4, 'STU-2025-004', '2025-12-26 21:03:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(5, 'STU-2025-004', '2025-12-26 21:15:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(6, 'STU-2025-004', '2025-12-26 22:50:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(7, 'STU-2025-355', '2025-12-27 08:51:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(8, 'STU-2025-004', '2025-12-27 08:53:38', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(9, 'APP-2025-4827', '2025-12-28 09:37:52', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(10, 'APP-2025-0001', '2025-12-28 15:02:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(11, 'APP-2025-0001', '2025-12-28 15:05:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(12, 'APP-2025-0001', '2025-12-28 15:05:24', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(13, 'APP-2025-0001', '2025-12-28 15:05:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(14, 'APP-2025-0001', '2025-12-28 15:07:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(15, 'APP-2025-0001', '2025-12-28 15:12:17', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(16, 'APP-2025-0001', '2025-12-28 15:13:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(17, 'APP-2025-0001', '2025-12-28 15:15:23', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(18, 'APP-2025-0001', '2025-12-28 15:15:31', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(19, 'APP-2025-0001', '2025-12-28 15:52:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(20, 'APP-2025-0001', '2025-12-28 15:52:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(21, 'APP-2025-0001', '2025-12-28 15:52:51', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(22, 'APP-2025-0001', '2025-12-28 15:53:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(23, 'APP-2025-0001', '2025-12-28 15:53:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(24, 'APP-2025-0001', '2025-12-28 15:53:18', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(25, 'APP-2025-0001', '2025-12-28 15:55:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(26, 'APP-2025-0001', '2025-12-28 15:57:07', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(27, 'APP-2025-0001', '2025-12-28 15:57:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(28, 'APP-2025-0001', '2025-12-28 15:57:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(29, 'APP-2025-0001', '2025-12-28 15:57:42', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(30, 'APP-2025-0001', '2025-12-28 15:59:01', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(31, 'APP-2025-0001', '2025-12-28 15:59:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(32, 'APP-2025-0001', '2025-12-28 16:01:35', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(33, 'APP-2025-0001', '2025-12-28 16:01:57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(34, 'APP-2025-0001', '2025-12-28 16:02:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(35, 'APP-2025-0001', '2025-12-28 16:06:10', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(36, 'APP-2025-0001', '2025-12-28 16:06:43', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(37, 'APP-2025-0001', '2025-12-28 16:06:56', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(38, 'APP-2025-0001', '2025-12-28 16:07:04', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(39, 'APP-2025-0001', '2025-12-28 16:08:16', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(40, 'APP-2025-0001', '2025-12-28 16:08:50', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(41, 'APP-2025-0001', '2025-12-28 16:08:58', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(42, 'APP-2025-0001', '2025-12-28 16:09:14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(43, 'APP-2025-0001', '2025-12-28 16:09:34', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(44, 'APP-2025-0001', '2025-12-28 16:09:49', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(45, 'APP-2025-0001', '2025-12-28 16:10:05', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(46, 'APP-2025-0001', '2025-12-28 16:11:48', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(47, 'APP-2025-0001', '2025-12-31 09:09:15', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'Success'),
(48, 'e2072553567pri', '2026-09-07 16:35:49', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Mobile Safari/537.36', 'Success'),
(49, 'e2072553567pri', '2026-09-07 16:35:56', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success'),
(50, 'e2072553699gsa', '2026-09-07 16:37:49', '10.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', 'Success'),
(51, 'APP-2025-0001', '2026-09-07 16:38:06', '10.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', 'Failed'),
(52, 'e2072553477eoa', '2026-09-07 16:38:15', '10.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', 'Success'),
(53, 'e2072553567pri', '2026-09-07 16:43:42', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success'),
(54, 'e2072553567pri', '2026-09-07 16:44:26', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success'),
(55, 'e2072553699gsa', '2026-09-07 16:45:08', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success'),
(56, 'e2072553699gsa', '2026-09-07 16:45:35', '10.0.0.1', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Mobile Safari/537.36', 'Success'),
(57, 'e2072553699gsa', '2026-09-07 16:45:44', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success'),
(58, 'e2072553699gsa', '2026-09-07 16:46:31', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success'),
(59, 'e2072553477eoa', '2026-09-07 16:47:17', '10.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', 'Success'),
(60, 'e2072553477eoa', '2026-09-07 16:47:38', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success'),
(61, 'e2072553477eoa', '2026-09-07 16:47:50', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success'),
(62, 'e2072553477eoa', '2026-09-07 17:09:29', '10.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0', 'Success'),
(63, 'e2072553567pri', '2026-09-09 06:41:21', '10.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'Success');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `video_url` text COLLATE utf8mb4_general_ci NOT NULL,
  `course_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Course name like ADCA, DCA or All',
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `video_url`, `course_name`, `description`, `created_at`) VALUES
(1, 'Introduction to Course', 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'All', NULL, '2025-12-26 15:19:33'),
(2, 'एम् एस वर्ड के SORTCUT केसे इस्तेमाल करते है ', 'https://youtu.be/b59WlQEKT6o?si=NyBPLiIAh-EEFFr8', 'DCA', NULL, '2025-12-26 15:28:26');

-- --------------------------------------------------------

--
-- Table structure for table `video_lectures`
--

CREATE TABLE `video_lectures` (
  `id` int NOT NULL,
  `course_id` int NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `description` text,
  `video_url` varchar(500) NOT NULL,
  `duration` varchar(50) DEFAULT '00:00',
  `chapter_name` varchar(100) DEFAULT 'General',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `video_lectures`
--

INSERT INTO `video_lectures` (`id`, `course_id`, `title`, `description`, `video_url`, `duration`, `chapter_name`, `sort_order`, `created_at`) VALUES
(1, 1, 'Introduction to the Course', 'Welcome! In this video, we cover the basics.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '05:20', 'Module 1: Basics', 1, '2026-08-02 17:15:45'),
(2, 1, 'Setting Up Environment', 'Learn how to install all required software.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '12:45', 'Module 1: Basics', 2, '2026-08-02 17:15:45'),
(3, 1, 'Deep Dive into Architecture', 'Understanding how the system works under the hood.', 'https://www.youtube.com/embed/dQw4w9WgXcQ', '20:15', 'Module 2: Advanced Concepts', 3, '2026-08-02 17:15:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `business_settings`
--
ALTER TABLE `business_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_wallet`
--
ALTER TABLE `document_wallet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ebooks`
--
ALTER TABLE `ebooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lms_content`
--
ALTER TABLE `lms_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lms_doubts`
--
ALTER TABLE `lms_doubts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lms_materials`
--
ALTER TABLE `lms_materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lms_notes`
--
ALTER TABLE `lms_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `std_mat_note` (`student_id`,`material_id`);

--
-- Indexes for table `lms_progress`
--
ALTER TABLE `lms_progress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roll_no` (`roll_no`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roll_no` (`roll_no`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_biometrics`
--
ALTER TABLE `user_biometrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `verification_logs`
--
ALTER TABLE `verification_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_lectures`
--
ALTER TABLE `video_lectures`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_wallet`
--
ALTER TABLE `document_wallet`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ebooks`
--
ALTER TABLE `ebooks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homework`
--
ALTER TABLE `homework`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lms_content`
--
ALTER TABLE `lms_content`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_doubts`
--
ALTER TABLE `lms_doubts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lms_materials`
--
ALTER TABLE `lms_materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_notes`
--
ALTER TABLE `lms_notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_progress`
--
ALTER TABLE `lms_progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1153;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `student_documents`
--
ALTER TABLE `student_documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `user_biometrics`
--
ALTER TABLE `user_biometrics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verification_logs`
--
ALTER TABLE `verification_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `video_lectures`
--
ALTER TABLE `video_lectures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`),
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_biometrics`
--
ALTER TABLE `user_biometrics`
  ADD CONSTRAINT `user_biometrics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
