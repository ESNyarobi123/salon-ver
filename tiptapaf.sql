-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 18, 2026 at 08:54 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tiptapaf`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `description`, `type`, `properties`, `created_at`, `updated_at`) VALUES
(1, NULL, 'New WhatsApp order #4 from 165515876151525', 'order_created', '{\"order_id\":4,\"source\":\"whatsapp\"}', '2026-01-03 23:26:48', '2026-01-03 23:26:48'),
(2, NULL, 'New WhatsApp order #5 from 165515876151525', 'order_created', '{\"order_id\":5,\"source\":\"whatsapp\"}', '2026-01-03 23:33:48', '2026-01-03 23:33:48'),
(3, NULL, 'New WhatsApp order #6 from 165515876151525', 'order_created', '{\"order_id\":6,\"source\":\"whatsapp\"}', '2026-01-03 23:38:00', '2026-01-03 23:38:00'),
(4, NULL, 'New WhatsApp order #7 from 165515876151525', 'order_created', '{\"order_id\":7,\"source\":\"whatsapp\"}', '2026-01-03 23:57:52', '2026-01-03 23:57:52'),
(5, NULL, 'New WhatsApp order #8 from 165515876151525', 'order_created', '{\"order_id\":8,\"source\":\"whatsapp\"}', '2026-01-04 00:19:59', '2026-01-04 00:19:59'),
(6, NULL, 'New WhatsApp order #9 from 165515876151525', 'order_created', '{\"order_id\":9,\"source\":\"whatsapp\"}', '2026-01-04 10:42:20', '2026-01-04 10:42:20'),
(7, NULL, 'New WhatsApp order #10 from 165515876151525', 'order_created', '{\"order_id\":10,\"source\":\"whatsapp\"}', '2026-01-06 06:27:38', '2026-01-06 06:27:38'),
(8, NULL, 'New WhatsApp order #11 from 165515876151525', 'order_created', '{\"order_id\":11,\"source\":\"whatsapp\"}', '2026-01-06 08:03:07', '2026-01-06 08:03:07'),
(9, NULL, 'New WhatsApp order #12 from 165515876151525', 'order_created', '{\"order_id\":12,\"source\":\"whatsapp\"}', '2026-01-10 13:16:20', '2026-01-10 13:16:20'),
(10, NULL, 'New WhatsApp order #13 from 165515876151525', 'order_created', '{\"order_id\":13,\"source\":\"whatsapp\"}', '2026-01-11 06:56:21', '2026-01-11 06:56:21'),
(11, NULL, 'Quick payment initiated: Tsh 30,000 from 255678165524', 'quick_payment', '{\"payment_id\":9,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-11 21:19:22', '2026-01-11 21:19:22'),
(12, NULL, 'Quick payment initiated: Tsh 1,000 from 255678165524', 'quick_payment', '{\"payment_id\":10,\"source\":\"whatsapp\",\"description\":\"Tip for ENJO\"}', '2026-01-11 21:21:05', '2026-01-11 21:21:05'),
(13, NULL, 'New WhatsApp text order #14 from 165515876151525: \"mango juice glass 5 na french fries 1\" (Unmatched)', 'order_created', '{\"order_id\":14,\"source\":\"whatsapp_text_unmatched\"}', '2026-01-11 22:22:38', '2026-01-11 22:22:38'),
(14, NULL, 'New WhatsApp text order #15 from 165515876151525: \"mango juice glass 5 na french fries 1\" (Unmatched)', 'order_created', '{\"order_id\":15,\"source\":\"whatsapp_text_unmatched\"}', '2026-01-11 22:26:59', '2026-01-11 22:26:59'),
(15, NULL, 'New WhatsApp text order #16 from 165515876151525: \"mango juice glass 5 na french fries 1\" (Unmatched)', 'order_created', '{\"order_id\":16,\"source\":\"whatsapp_text_unmatched\"}', '2026-01-11 22:30:30', '2026-01-11 22:30:30'),
(16, NULL, 'Quick payment initiated: Tsh 5,000 from 255753228505', 'quick_payment', '{\"payment_id\":11,\"source\":\"whatsapp\",\"description\":\"Tip for ENJO\"}', '2026-01-12 02:32:12', '2026-01-12 02:32:12'),
(17, NULL, 'Quick payment initiated: Tsh 8,000 from 255753228505', 'quick_payment', '{\"payment_id\":12,\"source\":\"whatsapp\",\"description\":\"Tip for ENJO\"}', '2026-01-12 02:34:01', '2026-01-12 02:34:01'),
(18, NULL, 'Quick payment initiated: Tsh 1,000 from 255678165524', 'quick_payment', '{\"payment_id\":13,\"source\":\"whatsapp\",\"description\":\"Tip for ENJO\"}', '2026-01-12 06:15:57', '2026-01-12 06:15:57'),
(19, NULL, 'Quick payment initiated: Tsh 5,000 from 255678165524', 'quick_payment', '{\"payment_id\":14,\"source\":\"whatsapp\",\"description\":\"Tip for ENJO\"}', '2026-01-12 07:18:59', '2026-01-12 07:18:59'),
(20, NULL, 'Quick payment initiated: Tsh 8,009 from 255679166524', 'quick_payment', '{\"payment_id\":15,\"source\":\"whatsapp\",\"description\":\"Tip for ENJO\"}', '2026-01-12 07:19:41', '2026-01-12 07:19:41'),
(21, NULL, 'Quick payment initiated: Tsh 7,000 from 255753228505', 'quick_payment', '{\"payment_id\":16,\"source\":\"whatsapp\",\"description\":\"Tip for JANET RICHARD\"}', '2026-01-12 07:45:38', '2026-01-12 07:45:38'),
(22, NULL, 'Quick payment initiated: Tsh 7,000 from 255753228505', 'quick_payment', '{\"payment_id\":17,\"source\":\"whatsapp\",\"description\":\"Tip for JANET RICHARD\"}', '2026-01-12 07:48:04', '2026-01-12 07:48:04'),
(23, NULL, 'New WhatsApp text order #17 from 165515876151525: \"Mango juice glass 5\" (Unmatched)', 'order_created', '{\"order_id\":17,\"source\":\"whatsapp_text_unmatched\"}', '2026-01-12 07:51:51', '2026-01-12 07:51:51'),
(24, NULL, 'Quick payment initiated: Tsh 5,000 from 255759296502', 'quick_payment', '{\"payment_id\":18,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-12 07:52:22', '2026-01-12 07:52:22'),
(25, NULL, 'Quick payment initiated: Tsh 500 from 255678165524', 'quick_payment', '{\"payment_id\":19,\"source\":\"whatsapp\",\"description\":\"Tip for MSUME ABDLAH\"}', '2026-01-12 08:02:02', '2026-01-12 08:02:02'),
(26, NULL, 'New WhatsApp text order #18 from 165515876151525: \"Sama\" (Unmatched)', 'order_created', '{\"order_id\":18,\"source\":\"whatsapp_text_unmatched\"}', '2026-01-12 08:54:05', '2026-01-12 08:54:05'),
(27, NULL, 'Quick payment initiated: Tsh 5,000 from 255678165524', 'quick_payment', '{\"payment_id\":20,\"source\":\"whatsapp\",\"description\":\"Tip for JANET RICHARD\"}', '2026-01-12 09:00:36', '2026-01-12 09:00:36'),
(28, NULL, 'Quick payment initiated: Tsh 500 from 255753228505', 'quick_payment', '{\"payment_id\":21,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-12 09:54:05', '2026-01-12 09:54:05'),
(29, NULL, 'Quick payment initiated: Tsh 6,000 from 255753228505', 'quick_payment', '{\"payment_id\":22,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-01-12 09:55:06', '2026-01-12 09:55:06'),
(30, NULL, 'Quick payment initiated: Tsh 500 from 255786116010', 'quick_payment', '{\"payment_id\":23,\"source\":\"whatsapp\",\"description\":\"Tip for JANET RICHARD\"}', '2026-01-12 10:09:39', '2026-01-12 10:09:39'),
(31, NULL, 'Quick payment initiated: Tsh 2,000 from 255628042409', 'quick_payment', '{\"payment_id\":24,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-12 11:38:17', '2026-01-12 11:38:17'),
(32, NULL, 'New WhatsApp text order #19 from 49727282356351: \"2\" (Unmatched)', 'order_created', '{\"order_id\":19,\"source\":\"whatsapp_text_unmatched\"}', '2026-01-12 12:27:25', '2026-01-12 12:27:25'),
(33, NULL, 'Quick payment initiated: Tsh 1,000 from 255786116010', 'quick_payment', '{\"payment_id\":25,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-01-12 12:31:23', '2026-01-12 12:31:23'),
(34, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165524', 'quick_payment', '{\"payment_id\":26,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-13 19:17:13', '2026-01-13 19:17:13'),
(35, NULL, 'Quick payment initiated: Tsh 2,000 from 0750599412', 'quick_payment', '{\"payment_id\":27,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-13 19:19:14', '2026-01-13 19:19:14'),
(36, NULL, 'Quick payment initiated: Tsh 2,000 from 0750599412', 'quick_payment', '{\"payment_id\":28,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-13 19:27:18', '2026-01-13 19:27:18'),
(37, NULL, 'Quick payment initiated: Tsh 2,000 from 0750599412', 'quick_payment', '{\"payment_id\":29,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-13 19:45:46', '2026-01-13 19:45:46'),
(38, NULL, 'New WhatsApp text order #20 from 165515876151525: \"Mango juice glass 5\" (Unmatched)', 'order_created', '{\"order_id\":20,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":12}', '2026-01-13 19:51:40', '2026-01-13 19:51:40'),
(39, NULL, 'Quick payment initiated: Tsh 2,000 from 0750599412', 'quick_payment', '{\"payment_id\":30,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-13 19:56:53', '2026-01-13 19:56:53'),
(40, NULL, 'Quick payment initiated: Tsh 2,000 from 0678165524', 'quick_payment', '{\"payment_id\":31,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-13 19:58:28', '2026-01-13 19:58:28'),
(41, NULL, 'New WhatsApp text order #21 from 165515876151525: \"Juice za embe\" (Unmatched)', 'order_created', '{\"order_id\":21,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":12}', '2026-01-13 19:59:52', '2026-01-13 19:59:52'),
(42, NULL, 'Quick payment initiated: Tsh 4,000 from 0678165524', 'quick_payment', '{\"payment_id\":32,\"source\":\"whatsapp\",\"description\":\"Tip for JANET RICHARD\"}', '2026-01-13 20:00:37', '2026-01-13 20:00:37'),
(43, NULL, 'Quick payment initiated: Tsh 4,000 from 0678165524', 'quick_payment', '{\"payment_id\":34,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-14 07:30:05', '2026-01-14 07:30:05'),
(44, NULL, 'Quick payment initiated: Tsh 300 from 0753228505', 'quick_payment', '{\"payment_id\":35,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-01-14 07:33:25', '2026-01-14 07:33:25'),
(45, NULL, 'Quick payment completed: Tsh 300', 'payment_success', '{\"payment_id\":35,\"amount\":\"300.00\",\"phone\":\"0753228505\"}', '2026-01-14 07:33:46', '2026-01-14 07:33:46'),
(46, NULL, 'Quick payment initiated: Tsh 100 from 0753228505', 'quick_payment', '{\"payment_id\":36,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-14 07:40:40', '2026-01-14 07:40:40'),
(47, NULL, 'New WhatsApp text order #22 from 165515876151525: \"MANGO JUICE GLASS 5\" (Unmatched)', 'order_created', '{\"order_id\":22,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":16}', '2026-01-14 16:25:03', '2026-01-14 16:25:03'),
(48, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165543', 'quick_payment', '{\"payment_id\":37,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-15 03:12:44', '2026-01-15 03:12:44'),
(49, NULL, 'New WhatsApp text order #23 from 25022580134024: \"2\" (Unmatched)', 'order_created', '{\"order_id\":23,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":null}', '2026-01-15 04:44:16', '2026-01-15 04:44:16'),
(50, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165524', 'quick_payment', '{\"payment_id\":38,\"source\":\"whatsapp\",\"description\":\"Tip for MSUME ABDLAH\"}', '2026-01-16 06:11:05', '2026-01-16 06:11:05'),
(51, NULL, 'Quick payment initiated: Tsh 2,000 from 0678165534', 'quick_payment', '{\"payment_id\":39,\"source\":\"whatsapp\",\"description\":\"Tip for MSUME ABDLAH\"}', '2026-01-16 06:19:04', '2026-01-16 06:19:04'),
(52, NULL, 'New WhatsApp text order #24 from 165515876151525: \"3\" (Unmatched)', 'order_created', '{\"order_id\":24,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":null}', '2026-01-16 06:44:02', '2026-01-16 06:44:02'),
(53, NULL, 'Quick payment initiated: Tsh 10,000 from 0678165543', 'quick_payment', '{\"payment_id\":40,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-16 06:44:29', '2026-01-16 06:44:29'),
(54, NULL, 'Quick payment initiated: Tsh 5,000 from 0678154332', 'quick_payment', '{\"payment_id\":41,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-16 06:48:19', '2026-01-16 06:48:19'),
(55, NULL, 'Quick payment initiated: Tsh 5,000 from 0678454321', 'quick_payment', '{\"payment_id\":42,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-01-16 06:48:42', '2026-01-16 06:48:42'),
(56, NULL, 'Quick payment initiated: Tsh 300 from 0753228505', 'quick_payment', '{\"payment_id\":43,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-01-16 07:05:06', '2026-01-16 07:05:06'),
(57, NULL, 'Quick payment completed: Tsh 300', 'payment_success', '{\"payment_id\":43,\"amount\":\"300.00\",\"phone\":\"0753228505\"}', '2026-01-16 07:05:38', '2026-01-16 07:05:38'),
(58, NULL, 'Quick payment initiated: Tsh 400 from 0753228505', 'quick_payment', '{\"payment_id\":44,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-16 07:07:06', '2026-01-16 07:07:06'),
(59, NULL, 'Quick payment initiated: Tsh 500 from 0786116010', 'quick_payment', '{\"payment_id\":45,\"source\":\"whatsapp\",\"description\":\"Tip for MACHA JOHN\"}', '2026-01-16 10:03:50', '2026-01-16 10:03:50'),
(60, NULL, 'Quick payment initiated: Tsh 6,999 from 0678165543', 'quick_payment', '{\"payment_id\":46,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-01-16 14:55:13', '2026-01-16 14:55:13'),
(61, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165545', 'quick_payment', '{\"payment_id\":47,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-16 15:44:31', '2026-01-16 15:44:31'),
(62, NULL, 'Quick payment initiated: Tsh 5,000 from 0678654321', 'quick_payment', '{\"payment_id\":48,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-01-16 15:45:18', '2026-01-16 15:45:18'),
(63, NULL, 'Quick payment initiated: Tsh 300 from 0753228505', 'quick_payment', '{\"payment_id\":49,\"source\":\"whatsapp\",\"description\":\"Tip  SIRIEL CHARLSE\"}', '2026-01-16 16:14:19', '2026-01-16 16:14:19'),
(64, NULL, 'Quick payment completed: Tsh 300', 'payment_success', '{\"payment_id\":49,\"amount\":\"300.00\",\"phone\":\"0753228505\"}', '2026-01-16 16:14:40', '2026-01-16 16:14:40'),
(65, NULL, 'Quick payment initiated: Tsh 200 from 0753228505', 'quick_payment', '{\"payment_id\":50,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-16 16:15:30', '2026-01-16 16:15:30'),
(66, NULL, 'Quick payment completed: Tsh 200', 'payment_success', '{\"payment_id\":50,\"amount\":\"200.00\",\"phone\":\"0753228505\"}', '2026-01-16 16:15:51', '2026-01-16 16:15:51'),
(67, NULL, 'New WhatsApp text order #25 from 165515876151525: \"Soda 2 na milija ok\" (Unmatched)', 'order_created', '{\"order_id\":25,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":15}', '2026-01-16 18:53:53', '2026-01-16 18:53:53'),
(68, NULL, 'Quick payment initiated: Tsh 100 from 0753228505', 'quick_payment', '{\"payment_id\":51,\"source\":\"whatsapp\",\"description\":\"Tip  ERICK SALEHE\"}', '2026-01-17 06:19:27', '2026-01-17 06:19:27'),
(69, NULL, 'Quick payment initiated: Tsh 300 from 0753228505', 'quick_payment', '{\"payment_id\":52,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-17 06:20:41', '2026-01-17 06:20:41'),
(70, NULL, 'Quick payment completed: Tsh 300', 'payment_success', '{\"payment_id\":52,\"amount\":\"300.00\",\"phone\":\"0753228505\"}', '2026-01-17 06:21:02', '2026-01-17 06:21:02'),
(71, NULL, 'Quick payment initiated: Tsh 5,000 from 0766141849', 'quick_payment', '{\"payment_id\":53,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-18 04:28:43', '2026-01-18 04:28:43'),
(72, NULL, 'Quick payment initiated: Tsh 5,000 from 0678165524', 'quick_payment', '{\"payment_id\":54,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-18 04:30:43', '2026-01-18 04:30:43'),
(73, NULL, 'Quick payment initiated: Tsh 500 from 0753228505', 'quick_payment', '{\"payment_id\":55,\"source\":\"whatsapp\",\"description\":\"Tip  SIRIEL CHARLSE\"}', '2026-01-18 07:09:37', '2026-01-18 07:09:37'),
(74, NULL, 'Quick payment initiated: Tsh 1,000 from 0753228505', 'quick_payment', '{\"payment_id\":56,\"source\":\"whatsapp\",\"description\":\"Tip  SIRIEL CHARLSE\"}', '2026-01-21 10:59:57', '2026-01-21 10:59:57'),
(75, NULL, 'New WhatsApp text order #28 from 165515876151525: \"Nataka soda 2 za pepsi\" (Unmatched)', 'order_created', '{\"order_id\":28,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":12}', '2026-01-23 07:59:49', '2026-01-23 07:59:49'),
(76, NULL, 'Quick payment initiated: Tsh 40,000 from 0678165524', 'quick_payment', '{\"payment_id\":57,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-01-23 08:00:28', '2026-01-23 08:00:28'),
(77, NULL, 'New WhatsApp text order #29 from 165515876151525: \"Nataka soda chupa 2\" (Unmatched)', 'order_created', '{\"order_id\":29,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":12}', '2026-02-01 10:02:21', '2026-02-01 10:02:21'),
(78, NULL, 'Quick payment initiated: Tsh 5,000 from 0750599412', 'quick_payment', '{\"payment_id\":58,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-02-01 10:03:30', '2026-02-01 10:03:30'),
(79, NULL, 'Quick payment initiated: Tsh 2,000 from 0678165524', 'quick_payment', '{\"payment_id\":59,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-02-01 10:04:39', '2026-02-01 10:04:39'),
(80, NULL, 'Quick payment initiated: Tsh 100,000 from 0624762514', 'quick_payment', '{\"payment_id\":60,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-02-01 10:05:32', '2026-02-01 10:05:32'),
(81, NULL, 'Quick payment initiated: Tsh 5,000 from 0753228505', 'quick_payment', '{\"payment_id\":61,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-02-12 16:22:10', '2026-02-12 16:22:10'),
(82, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165524', 'quick_payment', '{\"payment_id\":62,\"source\":\"whatsapp\",\"description\":\"Tip  SIRIEL CHARLSE\"}', '2026-02-13 08:39:58', '2026-02-13 08:39:58'),
(83, NULL, 'Quick payment initiated: Tsh 2,000 from 0678165524', 'quick_payment', '{\"payment_id\":63,\"source\":\"whatsapp\",\"description\":\"Tip  SIRIEL CHARLSE\"}', '2026-02-14 03:22:58', '2026-02-14 03:22:58'),
(84, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165524', 'quick_payment', '{\"payment_id\":64,\"source\":\"whatsapp\",\"description\":\"Tip  SIRIEL CHARLSE\"}', '2026-02-14 03:30:39', '2026-02-14 03:30:39'),
(85, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165524', 'quick_payment', '{\"payment_id\":65,\"source\":\"whatsapp\",\"description\":\"Tip  SIRIEL CHARLSE\"}', '2026-02-14 03:37:11', '2026-02-14 03:37:11'),
(86, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165524', 'quick_payment', '{\"payment_id\":66,\"source\":\"whatsapp\",\"description\":\"Tip  SIRIEL CHARLSE\"}', '2026-02-14 03:41:28', '2026-02-14 03:41:28'),
(87, NULL, 'Demo: Quick payment completed: Tsh 1,000', 'payment_success', '{\"payment_id\":67,\"amount\":1000,\"phone\":\"0678165524\"}', '2026-02-14 03:57:04', '2026-02-14 03:57:04'),
(88, NULL, 'Demo: Quick payment completed: Tsh 2,000', 'payment_success', '{\"payment_id\":68,\"amount\":2000,\"phone\":\"0678165524\"}', '2026-02-14 18:44:38', '2026-02-14 18:44:38'),
(89, NULL, 'New WhatsApp text order #30 from 165515876151525: \"wali samaki\" (Unmatched)', 'order_created', '{\"order_id\":30,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":13}', '2026-02-14 18:45:13', '2026-02-14 18:45:13'),
(90, NULL, 'Demo: Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":69,\"amount\":500,\"phone\":\"0753228505\"}', '2026-02-15 05:38:57', '2026-02-15 05:38:57'),
(91, NULL, 'Demo: Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":70,\"amount\":500,\"phone\":\"0753228505\"}', '2026-02-15 05:47:40', '2026-02-15 05:47:40'),
(92, NULL, 'Demo: Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":71,\"amount\":500,\"phone\":\"0753228505\"}', '2026-02-15 05:50:03', '2026-02-15 05:50:03'),
(93, NULL, 'Demo: Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":72,\"amount\":500,\"phone\":\"0753228505\"}', '2026-02-15 05:56:31', '2026-02-15 05:56:31'),
(94, NULL, 'Demo: Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":73,\"amount\":500,\"phone\":\"0753228505\"}', '2026-02-15 06:41:52', '2026-02-15 06:41:52'),
(95, NULL, 'Quick payment initiated: Tsh 1,000 from 0678165524', 'quick_payment', '{\"payment_id\":74,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-02-15 08:09:36', '2026-02-15 08:09:36'),
(96, NULL, 'Quick payment initiated: Tsh 500 from 0753228505', 'quick_payment', '{\"payment_id\":75,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-02-15 08:16:32', '2026-02-15 08:16:32'),
(97, NULL, 'Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":75,\"amount\":\"500.00\",\"phone\":\"0753228505\"}', '2026-02-15 08:17:53', '2026-02-15 08:17:53'),
(98, NULL, 'Quick payment initiated: Tsh 500 from 0753228505', 'quick_payment', '{\"payment_id\":76,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-02-15 08:53:41', '2026-02-15 08:53:41'),
(99, NULL, 'Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":76,\"amount\":\"500.00\",\"phone\":\"0753228505\"}', '2026-02-15 08:54:12', '2026-02-15 08:54:12'),
(100, NULL, 'New WhatsApp text order #31 from 155276086894839: \"3\" (Unmatched)', 'order_created', '{\"order_id\":31,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":13}', '2026-02-19 11:34:26', '2026-02-19 11:34:26'),
(101, NULL, 'Quick payment initiated: Tsh 500 from 0753228505', 'quick_payment', '{\"payment_id\":77,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-02-19 11:40:11', '2026-02-19 11:40:11'),
(102, NULL, 'Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":77,\"amount\":\"500.00\",\"phone\":\"0753228505\"}', '2026-02-19 11:40:44', '2026-02-19 11:40:44'),
(103, NULL, 'Quick payment initiated: Tsh 500 from 0753228505', 'quick_payment', '{\"payment_id\":78,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-02-19 11:47:54', '2026-02-19 11:47:54'),
(104, NULL, 'Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":78,\"amount\":\"500.00\",\"phone\":\"0753228505\"}', '2026-02-19 11:48:36', '2026-02-19 11:48:36'),
(105, NULL, 'Quick payment initiated: Tsh 500 from 0753228505', 'quick_payment', '{\"payment_id\":79,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-02-20 05:21:22', '2026-02-20 05:21:22'),
(106, NULL, 'Quick payment completed: Tsh 500', 'payment_success', '{\"payment_id\":79,\"amount\":\"500.00\",\"phone\":\"0753228505\"}', '2026-02-20 05:21:44', '2026-02-20 05:21:44'),
(107, NULL, 'New WhatsApp text order #32 from 136648931176650: \"✅\" (Unmatched)', 'order_created', '{\"order_id\":32,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":13}', '2026-02-20 06:06:04', '2026-02-20 06:06:04'),
(108, NULL, 'Quick payment initiated: Tsh 500 from 0712345678', 'quick_payment', '{\"payment_id\":80,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-02-20 06:09:37', '2026-02-20 06:09:37'),
(109, NULL, 'Quick payment initiated: Tsh 1,000 from 0715598080', 'quick_payment', '{\"payment_id\":81,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-02-20 06:11:56', '2026-02-20 06:11:56'),
(110, NULL, 'New WhatsApp text order #33 from 165515876151525: \"Chips 2, Soda 1\" (Unmatched)', 'order_created', '{\"order_id\":33,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":13}', '2026-02-21 19:43:05', '2026-02-21 19:43:05'),
(111, NULL, 'New WhatsApp text order #34 from 165515876151525: \"soda 2\" (Unmatched)', 'order_created', '{\"order_id\":34,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":13}', '2026-02-21 20:05:54', '2026-02-21 20:05:54'),
(112, NULL, 'Quick payment initiated: Tsh 10,000 from 0678165524', 'quick_payment', '{\"payment_id\":82,\"source\":\"whatsapp\",\"description\":\"Bill Payment\"}', '2026-02-21 20:07:29', '2026-02-21 20:07:29'),
(113, NULL, 'Quick payment initiated: Tsh 300 from 0753228505', 'quick_payment', '{\"payment_id\":83,\"source\":\"whatsapp\",\"description\":\"Tip for SIRIEL CHARLSE\"}', '2026-02-24 08:38:51', '2026-02-24 08:38:51'),
(114, NULL, 'Quick payment completed: Tsh 300', 'payment_success', '{\"payment_id\":83,\"amount\":\"300.00\",\"phone\":\"0753228505\"}', '2026-02-24 08:39:12', '2026-02-24 08:39:12'),
(115, NULL, 'New WhatsApp text order #35 from 165515876151525: \"Maji, chips na kuku\" (Unmatched)', 'order_created', '{\"order_id\":35,\"source\":\"whatsapp_text_unmatched\",\"waiter_id\":22}', '2026-02-26 09:38:45', '2026-02-26 09:38:45'),
(116, NULL, 'New WhatsApp text order #36 from 165515876151525: \"Maji x 2\nMango x 3\nchips x 2\"', 'order_created', '{\"order_id\":36,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:49:37', '2026-02-26 11:49:37'),
(117, NULL, 'New WhatsApp text order #37 from 165515876151525: \"Maji x 2\nMango x 3\"', 'order_created', '{\"order_id\":37,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:54:41', '2026-02-26 11:54:41'),
(118, NULL, 'New WhatsApp text order #38 from 165515876151525: \"Maji x 2\nMango x 3\nchips x 2\"', 'order_created', '{\"order_id\":38,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:54:57', '2026-02-26 11:54:57'),
(119, NULL, 'New WhatsApp text order #39 from 165515876151525: \"Maji x 2\nMango x 3\nchips x 2\"', 'order_created', '{\"order_id\":39,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:55:39', '2026-02-26 11:55:39'),
(120, NULL, 'New WhatsApp text order #40 from 165515876151525: \"Maji x 2\nMango x 3\"', 'order_created', '{\"order_id\":40,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:55:46', '2026-02-26 11:55:46'),
(121, NULL, 'New WhatsApp text order #41 from 165515876151525: \"Maji x 2\nMango x 3\nchips x 2\nMaji x 2\nMango x 3\nchips x 2\"', 'order_created', '{\"order_id\":41,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:55:58', '2026-02-26 11:55:58'),
(122, NULL, 'New WhatsApp text order #42 from 165515876151525: \"Maji x 2\nMango x 3\"', 'order_created', '{\"order_id\":42,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:56:06', '2026-02-26 11:56:06'),
(123, NULL, 'New WhatsApp text order #43 from 165515876151525: \"Maji x 2\nMango x 3\nchips x 2\"', 'order_created', '{\"order_id\":43,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:56:12', '2026-02-26 11:56:12'),
(124, NULL, 'New WhatsApp text order #44 from 165515876151525: \"Maji x 2\nMango x 3\"', 'order_created', '{\"order_id\":44,\"source\":\"whatsapp_text\",\"waiter_id\":22}', '2026-02-26 11:56:18', '2026-02-26 11:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(64) NOT NULL,
  `subject_type` varchar(64) NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_activity_logs`
--

INSERT INTO `admin_activity_logs` (`id`, `user_id`, `action`, `subject_type`, `subject_id`, `old_values`, `new_values`, `meta`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'restaurant.toggle_status', 'restaurant', 2, '{\"is_active\":1,\"name\":\"SAMAKI SAMAKI\"}', '{\"is_active\":false,\"name\":\"SAMAKI SAMAKI\"}', NULL, '41.220.128.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 05:14:16', '2026-02-25 05:14:16'),
(2, 1, 'restaurant.toggle_status', 'restaurant', 2, '{\"is_active\":0,\"name\":\"SAMAKI SAMAKI\"}', '{\"is_active\":true,\"name\":\"SAMAKI SAMAKI\"}', NULL, '41.220.128.10', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 05:15:38', '2026-02-25 05:15:38');

-- --------------------------------------------------------

--
-- Table structure for table `admin_sent_notifications`
--

CREATE TABLE `admin_sent_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target` varchar(32) NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bots`
--

CREATE TABLE `bots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'inactive',
  `last_ping` timestamp NULL DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-order_portal_token:fhlBSIJFHhxeXAzWHkOkm7HoBx0sWJnmavvzrJ9FXT586WmDHSXfX3cl2aFCuqqm', 'a:2:{s:13:\"restaurant_id\";i:2;s:7:\"user_id\";i:23;}', 1774969991),
('laravel-cache-order_portal_token:QtW5tyAF7etQQC80BxjqsJ7uf8tMMmvBl44dfeDUOW6dN5iwSK7mMtaBBACzTU4q', 'a:2:{s:13:\"restaurant_id\";i:2;s:7:\"user_id\";i:23;}', 1775903647),
('laravel-cache-order_portal_token:SrmkFzDtQbaSTpwC5kn4wwvQ3krwAj3WFi54uYojGMJuC1IIXZ6AYTNvhCKoWgA5', 'a:2:{s:13:\"restaurant_id\";i:2;s:7:\"user_id\";i:22;}', 1774708675),
('laravel-cache-order_portal_token:VEGfb5UwadMw0jgF9DnQCS6Umcp7WCDIDwVmZrLnCbiWoOiLiZwGG4rduFcIaynE', 'a:2:{s:13:\"restaurant_id\";i:2;s:7:\"user_id\";i:22;}', 1774708830),
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:21:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:18:\"manage_restaurants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:19:\"manage_system_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:16:\"view_all_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:22:\"manage_system_settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"manage_menu\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"manage_waiters\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:11:\"view_orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:2;i:1;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"update_orders\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:13:\"view_payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:16:\"confirm_payments\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:13:\"view_feedback\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:23:\"view_reports_restaurant\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:20:\"update_orders_status\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:9:\"view_tips\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:21:\"api_restaurant_search\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:12:\"api_get_menu\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:16:\"api_create_order\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:18:\"api_create_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:17:\"api_check_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:19:\"api_submit_feedback\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:14:\"api_submit_tip\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:4;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"manager\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:6:\"waiter\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"bot_service\";s:1:\"c\";s:3:\"web\";}}}', 1773664059);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `restaurant_id`, `name`, `image`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 2, 'Juice', NULL, 0, '2026-01-03 20:58:15', '2026-01-03 20:58:15'),
(2, 2, 'Chakula', NULL, 0, '2026-01-03 21:11:26', '2026-01-03 21:11:26'),
(3, 2, 'meet', NULL, 0, '2026-01-03 22:54:20', '2026-01-03 22:54:20'),
(4, 3, 'AAA', NULL, 0, '2026-01-03 23:05:23', '2026-01-03 23:05:23'),
(5, 3, 'BBB', NULL, 0, '2026-01-03 23:05:32', '2026-01-03 23:05:32'),
(6, 6, 'Shawarma', NULL, 0, '2026-01-17 07:30:09', '2026-01-17 07:30:09');

-- --------------------------------------------------------

--
-- Table structure for table `customer_requests`
--

CREATE TABLE `customer_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `table_number` varchar(255) DEFAULT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `waiter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_requests`
--

INSERT INTO `customer_requests` (`id`, `restaurant_id`, `table_number`, `table_id`, `waiter_id`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, '2', NULL, NULL, 'call_waiter', 'completed', '2026-01-06 10:06:56', '2026-01-08 03:42:30'),
(2, 2, '2', NULL, NULL, 'request_bill', 'completed', '2026-01-06 10:07:30', '2026-01-07 08:21:26'),
(3, 5, '7', NULL, NULL, 'call_waiter', 'completed', '2026-01-11 06:42:31', '2026-01-13 02:26:13'),
(4, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-01-12 23:43:59', '2026-02-12 21:46:42'),
(5, 2, NULL, NULL, 12, 'call_waiter', 'completed', '2026-01-13 04:35:31', '2026-02-12 21:46:36'),
(6, 2, '1', NULL, 13, 'call_waiter', 'completed', '2026-01-13 06:09:46', '2026-02-12 21:46:43'),
(7, 2, NULL, NULL, 12, 'call_waiter', 'completed', '2026-01-13 20:00:56', '2026-02-12 21:46:44'),
(8, 2, NULL, NULL, 15, 'call_waiter', 'completed', '2026-01-14 07:33:06', '2026-02-12 21:46:45'),
(9, 2, '1', NULL, 13, 'call_waiter', 'completed', '2026-01-14 07:38:44', '2026-02-12 21:46:50'),
(10, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-01-16 07:02:28', '2026-02-12 20:17:22'),
(11, 2, NULL, NULL, 15, 'call_waiter', 'completed', '2026-01-16 18:54:06', '2026-02-12 20:17:20'),
(12, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-01-18 03:54:45', '2026-02-12 21:46:49'),
(13, 2, NULL, NULL, 12, 'call_waiter', 'completed', '2026-02-01 10:02:43', '2026-02-12 20:17:26'),
(14, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 07:03:32', '2026-02-13 07:03:46'),
(15, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 07:03:58', '2026-02-13 08:37:28'),
(16, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 08:36:18', '2026-02-13 08:37:25'),
(17, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 08:36:50', '2026-02-13 08:37:26'),
(18, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 08:37:52', '2026-02-13 08:38:23'),
(19, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 08:38:31', '2026-02-13 08:55:38'),
(20, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 08:38:52', '2026-02-13 08:55:38'),
(21, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 08:57:34', '2026-02-13 08:57:59'),
(22, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 08:58:11', '2026-02-13 09:09:51'),
(23, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 09:00:45', '2026-02-13 09:22:19'),
(24, 2, NULL, NULL, 12, 'call_waiter', 'completed', '2026-02-13 09:24:06', '2026-02-13 09:24:44'),
(25, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 09:25:56', '2026-02-13 12:22:12'),
(26, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 19:52:05', '2026-02-14 04:09:33'),
(27, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-13 19:53:13', '2026-02-14 04:09:32'),
(28, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-14 04:06:00', '2026-02-14 05:09:14'),
(29, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-14 04:06:51', '2026-02-14 05:09:14'),
(30, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-14 04:07:41', '2026-02-14 05:09:11'),
(31, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-14 18:44:00', '2026-02-14 18:44:19'),
(32, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 04:55:00', '2026-02-15 04:56:11'),
(33, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 04:56:52', '2026-02-15 04:58:10'),
(34, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 05:02:59', '2026-02-15 05:16:53'),
(35, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 05:13:11', '2026-02-15 05:16:45'),
(36, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 05:15:08', '2026-02-15 05:16:49'),
(37, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 05:30:28', '2026-02-15 05:50:37'),
(38, 5, NULL, NULL, 11, 'call_waiter', 'completed', '2026-02-15 07:35:34', '2026-02-15 08:01:16'),
(39, 5, NULL, NULL, 11, 'call_waiter', 'completed', '2026-02-15 07:44:06', '2026-02-15 08:01:14'),
(40, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 08:21:17', '2026-02-15 08:22:26'),
(41, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 08:58:29', '2026-02-15 15:30:50'),
(42, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-15 09:00:26', '2026-02-15 15:30:53'),
(43, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-19 11:30:42', '2026-02-20 05:25:48'),
(44, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-19 16:09:07', '2026-02-20 05:25:46'),
(45, 2, NULL, NULL, 13, 'call_waiter', 'completed', '2026-02-20 06:05:42', '2026-02-20 06:59:27'),
(46, 2, 'table_1', NULL, 13, 'call_waiter', 'completed', '2026-02-21 20:06:07', '2026-02-21 20:07:55'),
(47, 2, 'table_1', NULL, 13, 'call_waiter', 'completed', '2026-02-21 20:13:06', '2026-02-22 12:50:05'),
(48, 2, 'table_1', NULL, NULL, 'call_waiter', 'completed', '2026-02-23 19:04:57', '2026-02-23 19:24:30'),
(49, 2, 'table_1', NULL, 22, 'call_waiter', 'completed', '2026-02-23 19:24:22', '2026-02-23 19:24:33'),
(50, 2, 'table_1', NULL, 22, 'call_waiter', 'completed', '2026-02-23 20:43:56', '2026-02-23 20:45:35'),
(51, 2, 'table_2', NULL, 22, 'call_waiter', 'completed', '2026-02-24 07:43:55', '2026-02-24 10:44:00'),
(52, 2, 'table_1', NULL, 13, 'call_waiter', 'completed', '2026-02-25 01:11:55', '2026-02-25 03:59:58'),
(53, 2, 'table_1', NULL, 13, 'call_waiter', 'completed', '2026-02-25 01:12:42', '2026-02-25 04:00:01'),
(54, 2, 'table_2', NULL, 23, 'call_waiter', 'completed', '2026-02-25 04:02:07', '2026-02-25 04:03:18'),
(55, 2, 'Mawenzi', 12, 23, 'call_waiter', 'completed', '2026-02-25 04:15:01', '2026-02-25 04:38:23'),
(56, 2, 'Uhuru', 13, 23, 'call_waiter', 'completed', '2026-02-25 06:03:41', '2026-02-26 05:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `waiter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `restaurant_id`, `order_id`, `waiter_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL, 2, 'Asante', '2026-01-06 07:17:16', '2026-01-06 07:17:16'),
(2, 2, NULL, NULL, 5, 'Nawapenda', '2026-01-06 08:05:32', '2026-01-06 08:05:32'),
(3, 2, NULL, NULL, 5, 'Vyakula vitamu', '2026-01-07 18:45:36', '2026-01-07 18:45:36'),
(4, 5, NULL, NULL, 5, 'The food was nice', '2026-01-10 10:13:50', '2026-01-10 10:13:50'),
(5, 2, NULL, NULL, 2, 'ONGEZENI UTAMU', '2026-01-10 13:17:00', '2026-01-10 13:17:00'),
(6, 2, NULL, NULL, 1, NULL, '2026-01-11 21:20:17', '2026-01-11 21:20:17'),
(7, 2, NULL, NULL, 5, 'The food was nice', '2026-01-12 02:35:28', '2026-01-12 02:35:28'),
(8, 5, NULL, NULL, 2, 'He was fast', '2026-01-12 02:55:50', '2026-01-12 02:55:50'),
(9, 2, NULL, NULL, 1, NULL, '2026-01-12 06:50:42', '2026-01-12 06:50:42'),
(10, 2, NULL, NULL, 5, NULL, '2026-01-12 07:18:33', '2026-01-12 07:18:33'),
(11, 2, NULL, NULL, 4, NULL, '2026-01-12 07:44:25', '2026-01-12 07:44:25'),
(12, 2, NULL, NULL, 5, NULL, '2026-01-12 08:02:46', '2026-01-12 08:02:46'),
(13, 2, NULL, NULL, 5, 'Nimependa', '2026-01-12 08:55:32', '2026-01-12 08:55:32'),
(14, 2, NULL, NULL, 5, 'Mambo', '2026-01-12 09:23:24', '2026-01-12 09:23:24'),
(15, 2, NULL, 12, 5, 'She is very nice', '2026-01-12 09:53:30', '2026-01-12 09:53:30'),
(16, 2, NULL, 12, 5, 'Great service', '2026-01-12 10:09:09', '2026-01-12 10:09:09'),
(17, 2, NULL, 13, 5, 'Well done on great service Charles , definitely coming back again?', '2026-01-12 12:29:02', '2026-01-12 12:29:02'),
(18, 2, NULL, 12, 1, 'Amazing', '2026-01-12 22:51:33', '2026-01-12 22:51:33'),
(19, 2, NULL, 13, 5, NULL, '2026-01-12 22:52:54', '2026-01-12 22:52:54'),
(20, 2, NULL, 13, 5, 'she is quick', '2026-01-13 06:08:09', '2026-01-13 06:08:09'),
(21, 2, NULL, 12, 5, 'Kazi nzuri', '2026-01-13 20:00:16', '2026-01-13 20:00:16'),
(22, 2, NULL, 12, 5, 'On time', '2026-01-14 07:31:06', '2026-01-14 07:31:06'),
(23, 5, NULL, 11, 5, 'Excellent', '2026-01-14 09:37:25', '2026-01-14 09:37:25'),
(24, 2, NULL, 16, 5, NULL, '2026-01-14 16:25:45', '2026-01-14 16:25:45'),
(25, 2, NULL, 13, 5, 'Mambo', '2026-01-16 06:47:49', '2026-01-16 06:47:49'),
(26, 2, NULL, 13, 5, NULL, '2026-01-16 07:02:21', '2026-01-16 07:02:21'),
(27, 2, NULL, 13, 5, 'Great service', '2026-01-16 07:06:32', '2026-01-16 07:06:32'),
(28, 2, NULL, 12, 5, 'Excellent service', '2026-01-16 10:03:11', '2026-01-16 10:03:11'),
(29, 2, NULL, 13, 1, NULL, '2026-01-16 15:34:57', '2026-01-16 15:34:57'),
(30, 2, NULL, 13, 5, NULL, '2026-01-16 15:45:36', '2026-01-16 15:45:36'),
(31, 5, NULL, 11, 5, 'He was fast', '2026-01-16 16:12:00', '2026-01-16 16:12:00'),
(32, 2, NULL, 13, 4, 'Wonderful service', '2026-01-17 06:22:19', '2026-01-17 06:22:19'),
(33, 2, NULL, 13, 5, 'Awesome', '2026-01-18 03:54:23', '2026-01-18 03:54:23'),
(34, 2, NULL, 13, 5, NULL, '2026-02-12 16:21:21', '2026-02-12 16:21:21'),
(35, 2, NULL, 13, 5, 'Nakupenda', '2026-02-13 12:22:59', '2026-02-13 12:22:59'),
(36, 2, NULL, 12, 5, 'SO AMAZING', '2026-02-13 12:27:02', '2026-02-13 12:27:02'),
(37, 2, NULL, 13, 5, 'The waiter was extremely professional, friendly, and attentive. She made sure our table was comfortable and checked on us regularly without being intrusive. Great customer service!', '2026-02-15 08:26:17', '2026-02-15 08:26:17'),
(38, 2, NULL, 13, 4, 'The waiter was quick, organized, and handled our order perfectly. The food arrived on time and everything was correct. Very efficient service.', '2026-02-15 08:26:40', '2026-02-15 08:26:40'),
(39, 2, NULL, 13, 2, 'Comment', '2026-02-19 11:51:20', '2026-02-19 11:51:20'),
(40, 2, NULL, 13, 5, NULL, '2026-02-21 20:07:03', '2026-02-21 20:07:03'),
(41, 2, NULL, 23, 4, 'Good service', '2026-02-25 04:01:45', '2026-02-25 04:01:45');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `preparation_time` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `category_id`, `name`, `description`, `price`, `image`, `is_available`, `preparation_time`, `created_at`, `updated_at`) VALUES
(4, 3, 4, 'KKK', 'GGG', 1000.00, NULL, 1, 3, '2026-01-03 23:05:49', '2026-01-03 23:05:49'),
(5, 3, 5, 'UUU', 'III', 3000.00, NULL, 1, 4, '2026-01-03 23:06:10', '2026-01-03 23:06:10'),
(6, 6, 6, 'Chicken Shawarma', NULL, 10000.00, NULL, 1, 20, '2026-01-17 07:30:42', '2026-01-17 07:30:42'),
(13, 2, 2, 'Chips Mayai', NULL, 10000.00, 'menu/HGuZFIyDkwBpF9fvVi5M4BZh56c7jJnEpInyTbk4.jpg', 1, 22, '2026-03-17 17:50:03', '2026-03-17 17:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_03_124225_create_permission_tables', 1),
(5, '2026_01_03_124448_create_personal_access_tokens_table', 1),
(6, '2026_01_03_125042_create_restaurants_table', 1),
(7, '2026_01_03_125128_create_categories_table', 1),
(8, '2026_01_03_125131_create_menu_items_table', 1),
(9, '2026_01_03_125136_create_orders_table', 1),
(10, '2026_01_03_125139_create_order_items_table', 1),
(11, '2026_01_03_125145_create_payments_table', 1),
(12, '2026_01_03_125148_create_feedback_table', 1),
(13, '2026_01_03_125152_add_restaurant_id_to_users_table', 1),
(14, '2026_01_03_125153_create_tips_table', 1),
(15, '2026_01_03_152722_add_waiter_id_to_orders_table', 2),
(16, '2026_01_03_153938_add_zenopay_api_key_to_restaurants_table', 3),
(17, '2026_01_03_154415_add_payment_reference_to_orders_table', 4),
(18, '2026_01_03_160415_create_customer_requests_table', 5),
(19, '2026_01_03_161451_add_preparation_time_to_menu_items_table', 6),
(20, '2026_01_03_181944_create_withdrawals_table', 7),
(21, '2026_01_03_181946_create_bots_table', 7),
(22, '2026_01_03_183106_create_settings_table', 8),
(23, '2026_01_03_183304_create_activities_table', 9),
(24, '2026_01_03_192854_add_customer_phone_to_orders_table', 10),
(25, '2026_01_03_221919_create_tables_table', 11),
(26, '2026_01_04_001925_add_default_to_order_items_total', 12),
(27, '2026_01_06_102041_add_customer_name_to_orders_table', 13),
(28, '2026_01_06_111201_make_waiter_id_nullable_in_tips_table', 14),
(29, '2026_01_08_014044_add_kitchen_token_to_restaurants_table', 15),
(30, '2026_01_08_014831_add_kds_columns_to_orders_and_items_tables', 15),
(31, '2026_01_11_220628_add_menu_image_to_restaurants_table', 16),
(32, '2026_01_11_222426_add_quick_payment_fields_to_payments_table', 16),
(33, '2026_01_12_011239_update_order_items_for_text_orders', 17),
(34, '2026_01_12_120945_add_waiter_id_to_feedback_table', 18),
(35, '2026_01_13_004700_replace_zenopay_with_selcom_in_restaurants_table', 19),
(36, '2026_01_13_014000_add_service_tags_to_restaurants_users_tables', 20),
(37, '2026_01_13_021126_add_table_id_and_waiter_id_to_customer_requests_table', 21),
(38, '2026_02_13_120000_add_waiter_id_to_payments_table', 21),
(39, '2026_02_14_100000_add_payment_id_and_nullable_order_id_to_tips_table', 22),
(40, '2026_02_21_205102_add_waiter_id_to_tables_table', 23),
(41, '2026_02_21_211143_add_support_phone_to_restaurants_table', 23),
(42, '2026_02_22_150410_add_global_waiter_number_and_waiter_profile_to_users_table', 24),
(43, '2026_02_22_151442_add_employment_type_and_linked_until_to_users_table', 24),
(44, '2026_02_22_190545_create_waiter_restaurant_assignments_table', 25),
(45, '2026_02_22_193913_add_profile_photo_path_to_users_table', 26),
(46, '2026_02_22_204421_create_waiter_salary_payments_table', 27),
(47, '2026_02_23_111947_create_notifications_table', 28),
(48, '2026_02_23_212736_create_admin_activity_logs_table', 29),
(49, '2026_02_23_213847_create_admin_sent_notifications_table', 30),
(50, '2026_02_23_214848_add_waiter_online_status_to_users_table', 31),
(51, '2026_02_26_114031_create_order_portal_passwords_table', 32);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 7),
(2, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 10),
(2, 'App\\Models\\User', 19),
(2, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 13),
(3, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 15),
(3, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(3, 'App\\Models\\User', 20),
(3, 'App\\Models\\User', 22),
(3, 'App\\Models\\User', 23),
(3, 'App\\Models\\User', 24),
(4, 'App\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('6918f4d5-fe3e-4d28-a472-42cf88b04d36', 'App\\Notifications\\SalaryPaymentConfirmed', 'App\\Models\\User', 23, '{\"type\":\"salary_payment_confirmed\",\"period_month\":\"2026-02\",\"period_label\":\"FEB 2026\",\"message\":\"Malipo yako ya FEB 2026 yamethibitishwa \\u2013 angalia Salary Slip.\",\"url\":\"https:\\/\\/tiptapafrica.co.tz\\/waiter\\/salary-slip\\/2026-02\"}', '2026-02-24 15:44:05', '2026-02-24 15:26:43', '2026-02-24 15:44:05'),
('7df38076-f957-4033-af92-cef9a75731e1', 'App\\Notifications\\SalaryPaymentConfirmed', 'App\\Models\\User', 23, '{\"type\":\"salary_payment_confirmed\",\"period_month\":\"2026-02\",\"period_label\":\"FEB 2026\",\"message\":\"Malipo yako ya FEB 2026 yamethibitishwa \\u2013 angalia Salary Slip.\",\"url\":\"https:\\/\\/tiptapafrica.co.tz\\/waiter\\/salary-slip\\/2026-02\"}', '2026-02-24 15:44:05', '2026-02-24 15:28:07', '2026-02-24 15:44:05'),
('d5357b96-3536-411b-9491-70af57bb8ff8', 'App\\Notifications\\SalaryPaymentConfirmed', 'App\\Models\\User', 12, '{\"type\":\"salary_payment_confirmed\",\"period_month\":\"2026-03\",\"period_label\":\"MAR 2026\",\"message\":\"Malipo yako ya MAR 2026 yamethibitishwa \\u2013 angalia Salary Slip.\",\"url\":\"https:\\/\\/tiptapafrica.co.tz\\/waiter\\/salary-slip\\/2026-03\"}', NULL, '2026-03-12 13:38:08', '2026-03-12 13:38:08'),
('d7907c01-8548-4a51-be05-b7077e4b9a1e', 'App\\Notifications\\SalaryPaymentConfirmed', 'App\\Models\\User', 22, '{\"type\":\"salary_payment_confirmed\",\"period_month\":\"2026-02\",\"period_label\":\"FEB 2026\",\"message\":\"Malipo yako ya FEB 2026 yamethibitishwa \\u2013 angalia Salary Slip.\",\"url\":\"https:\\/\\/tiptapafrica.co.tz\\/waiter\\/salary-slip\\/2026-02\"}', '2026-02-24 15:21:14', '2026-02-24 09:50:32', '2026-02-24 15:21:14'),
('edc5264d-2b7c-42bd-aa8c-e06af54cd357', 'App\\Notifications\\SalaryPaymentConfirmed', 'App\\Models\\User', 24, '{\"type\":\"salary_payment_confirmed\",\"period_month\":\"2026-03\",\"period_label\":\"MAR 2026\",\"message\":\"Malipo yako ya MAR 2026 yamethibitishwa \\u2013 angalia Salary Slip.\",\"url\":\"https:\\/\\/tiptapafrica.co.tz\\/waiter\\/salary-slip\\/2026-03\"}', NULL, '2026-03-12 22:04:23', '2026-03-12 22:04:23'),
('feb3c0c5-65ae-4c35-b458-ce20244b769c', 'App\\Notifications\\SalaryPaymentConfirmed', 'App\\Models\\User', 23, '{\"type\":\"salary_payment_confirmed\",\"period_month\":\"2026-03\",\"period_label\":\"MAR 2026\",\"message\":\"Malipo yako ya MAR 2026 yamethibitishwa \\u2013 angalia Salary Slip.\",\"url\":\"https:\\/\\/tiptapafrica.co.tz\\/waiter\\/salary-slip\\/2026-03\"}', '2026-03-02 12:37:02', '2026-03-02 12:35:17', '2026-03-02 12:37:02');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `table_number` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `is_vip` tinyint(1) NOT NULL DEFAULT 0,
  `payment_reference` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `waiter_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `restaurant_id`, `table_number`, `customer_phone`, `customer_name`, `status`, `is_vip`, `payment_reference`, `total_amount`, `notes`, `created_at`, `updated_at`, `waiter_id`) VALUES
(4, 3, '3', '165515876151525', NULL, 'pending', 0, NULL, 3000.00, NULL, '2026-01-03 23:26:48', '2026-01-03 23:26:48', NULL),
(5, 3, '3', '165515876151525', NULL, 'pending', 0, NULL, 3000.00, NULL, '2026-01-03 23:33:48', '2026-01-03 23:33:48', NULL),
(7, 2, '1', '165515876151525', NULL, 'paid', 0, NULL, 40000.00, NULL, '2026-01-03 23:57:52', '2026-01-03 23:58:51', NULL),
(9, 2, '1', '165515876151525', NULL, 'paid', 0, NULL, 10000.00, NULL, '2026-01-04 10:42:20', '2026-01-04 10:45:17', NULL),
(10, 2, '2', '165515876151525', NULL, 'paid', 0, NULL, 28000.00, NULL, '2026-01-06 06:27:38', '2026-01-06 06:28:17', NULL),
(11, 2, '1', '165515876151525', NULL, 'paid', 0, NULL, 10000.00, NULL, '2026-01-06 08:03:07', '2026-01-06 08:05:06', NULL),
(12, 2, '1', '165515876151525', NULL, 'ready', 0, NULL, 28000.00, NULL, '2026-01-10 13:16:20', '2026-02-15 07:03:22', 15),
(26, 6, 'Mawenzi', '0753228505', NULL, 'served', 0, NULL, 10000.00, NULL, '2026-01-17 07:34:33', '2026-01-17 07:36:35', NULL),
(27, 6, 'Mawenzi', NULL, NULL, 'ready', 0, NULL, 20000.00, NULL, '2026-01-17 07:39:18', '2026-01-17 07:42:25', NULL),
(29, 2, NULL, '165515876151525', 'SKY SOFTWARE DEVELOPER', 'paid', 0, NULL, 0.00, 'Order from text: Nataka soda chupa 2', '2026-02-01 10:02:21', '2026-02-27 06:14:34', 12),
(35, 2, 'table_11', '165515876151525', 'SKY SOFTWARE DEVELOPER', 'paid', 0, 'ORD-35-1772112211', 25000.00, 'Order from text: Maji, chips na kuku', '2026-02-26 09:38:45', '2026-02-26 10:26:38', 22),
(36, 2, 'table_13', '165515876151525', 'SKY SOFTWARE DEVELOPER', 'paid', 0, NULL, 25000.00, NULL, '2026-02-26 11:49:37', '2026-02-26 11:51:46', 22),
(37, 2, 'table_13', '165515876151525', 'SKY SOFTWARE DEVELOPER', 'paid', 0, NULL, 25000.00, NULL, '2026-02-26 11:54:41', '2026-02-26 14:48:17', 22),
(38, 2, 'table_13', '165515876151525', 'SKY SOFTWARE DEVELOPER', 'paid', 0, 'ORD-38-1772128189', 25000.00, NULL, '2026-02-26 11:54:57', '2026-02-27 06:14:09', 22),
(40, 2, 'table_11', '165515876151525', 'SKY SOFTWARE DEVELOPER', 'paid', 0, NULL, 25000.00, NULL, '2026-02-26 11:55:46', '2026-02-28 02:18:16', 22),
(44, 2, 'table_11', '165515876151525', 'SKY SOFTWARE DEVELOPER', 'paid', 0, NULL, 25000.00, NULL, '2026-02-26 11:56:18', '2026-02-26 14:45:52', 22),
(46, 2, 'front', '', '', 'paid', 0, NULL, 40000.00, NULL, '2026-02-27 06:18:01', '2026-02-27 06:27:23', 16),
(47, 2, 'front', '', '', 'paid', 0, 'ORD-47-1772256022', 25000.00, NULL, '2026-02-28 02:18:48', '2026-02-28 02:23:34', 22),
(48, 2, 'Uhuru', '', '', 'paid', 0, NULL, 40000.00, NULL, '2026-02-28 03:08:10', '2026-03-01 12:01:57', 22),
(49, 2, 'Mawenzi', '', '', 'paid', 0, NULL, 25000.00, NULL, '2026-03-01 11:59:14', '2026-03-01 12:03:27', 22),
(50, 2, 'Uhuru', '', '', 'ready', 0, NULL, 65000.00, NULL, '2026-03-01 12:08:08', '2026-03-01 12:16:51', 22),
(51, 2, 'Mawenzi', '', '', 'paid', 0, NULL, 30000.00, NULL, '2026-03-01 12:13:33', '2026-03-01 12:15:35', 23),
(52, 2, 'Mawenzi', '', '', 'ready', 0, NULL, 100000.00, NULL, '2026-03-01 12:17:46', '2026-03-01 12:20:01', 23),
(53, 2, 'Mawenzi', '', '', 'paid', 0, NULL, 65000.00, NULL, '2026-03-01 12:28:03', '2026-03-01 12:32:05', 23),
(54, 2, 'Uhuru', '', '', 'paid', 0, 'ORD-54-1773325469', 55000.00, NULL, '2026-03-01 13:27:15', '2026-03-12 11:25:04', 23),
(55, 2, 'Mawenzi', '', '', 'paid', 0, NULL, 25000.00, NULL, '2026-03-12 07:34:44', '2026-03-12 07:36:01', 23),
(56, 2, 'Mawenzi', '', '', 'ready', 0, NULL, 25000.00, NULL, '2026-03-12 11:22:33', '2026-03-13 11:44:56', 23),
(57, 2, 'Uhuru', '', '', 'ready', 0, NULL, 10000.00, NULL, '2026-03-12 11:44:45', '2026-03-13 11:44:31', 23),
(58, 2, 'Mawenzi', '', '', 'paid', 0, NULL, 25000.00, NULL, '2026-03-12 11:48:16', '2026-03-13 11:47:08', 23),
(59, 2, 'Mawenzi', '', '', 'paid', 0, NULL, 5000.00, NULL, '2026-03-12 13:53:09', '2026-03-13 03:13:43', 23),
(60, 2, 'Mawenzi', '', '', 'ready', 0, NULL, 10000.00, NULL, '2026-03-13 11:45:51', '2026-03-13 11:46:40', 23),
(61, 2, 'Mawenzi', '', '', 'ready', 0, NULL, 10000.00, NULL, '2026-03-15 10:05:58', '2026-03-15 10:08:37', 23),
(62, 2, 'Uhuru', '', '', 'ready', 0, NULL, 10000.00, NULL, '2026-03-15 10:09:55', '2026-03-15 10:10:46', 23),
(63, 2, 'Mawenzi', '', '', 'ready', 0, NULL, 5000.00, NULL, '2026-03-15 10:12:53', '2026-03-15 10:14:08', 23),
(64, 2, 'Uhuru', '', '', 'ready', 0, NULL, 25000.00, NULL, '2026-03-15 10:18:06', '2026-03-15 10:19:19', 23),
(65, 2, 'Mawenzi', '0678165524', 'Kelvin', 'ready', 0, NULL, 60000.00, NULL, '2026-03-15 19:58:56', '2026-03-15 20:14:11', NULL),
(66, 2, 'Mawenzi', '0653494745', 'Kelvin', 'ready', 0, NULL, 40000.00, NULL, '2026-03-15 20:33:20', '2026-03-15 20:33:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `name`, `quantity`, `price`, `total`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, 5, NULL, 1, 3000.00, 3000.00, 'pending', NULL, '2026-01-03 23:26:48', '2026-01-03 23:26:48'),
(2, 5, 5, NULL, 1, 3000.00, 3000.00, 'pending', NULL, '2026-01-03 23:33:48', '2026-01-03 23:33:48'),
(24, 26, 6, 'Chicken Shawarma', 1, 10000.00, 10000.00, 'pending', NULL, '2026-01-17 07:34:33', '2026-01-17 07:34:33'),
(25, 27, 6, 'Chicken Shawarma', 2, 10000.00, 20000.00, 'pending', NULL, '2026-01-17 07:39:18', '2026-01-17 07:39:18'),
(27, 29, NULL, 'Nataka soda chupa 2', 1, 0.00, 0.00, 'pending', NULL, '2026-02-01 10:02:21', '2026-02-01 10:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `order_portal_passwords`
--

CREATE TABLE `order_portal_passwords` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `password` varchar(255) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `revoked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_portal_passwords`
--

INSERT INTO `order_portal_passwords` (`id`, `restaurant_id`, `user_id`, `password`, `generated_at`, `revoked_at`, `created_at`, `updated_at`) VALUES
(1, 2, 22, '$2y$12$9H7zyujSnaVg4WU39UICpehfdPvvPHhShUi.9Rub.39FVHsuOko6C', '2026-02-26 11:16:58', NULL, '2026-02-26 09:22:35', '2026-02-26 11:16:58'),
(3, 2, 12, '$2y$12$vJrTfRBCpf33jIW5pun1ne2AnN/BEj0Cwligso/JooN0kt.ukwmXS', '2026-02-27 06:10:50', NULL, '2026-02-27 06:10:50', '2026-02-27 06:10:50'),
(4, 2, 13, '$2y$12$8e/sVMf0ZIBqQdEw0RvR9OCtwKK2LAOiHKTn5eIZq8TBm4WRsH7bG', '2026-02-27 06:11:12', NULL, '2026-02-27 06:11:13', '2026-02-27 06:11:13'),
(5, 2, 16, '$2y$12$YeyXx6XSxJR2sIjorz2PS.GEbB2OVQsQIfzC1WFUR71AE3PqhEqCG', '2026-02-27 06:17:25', NULL, '2026-02-27 06:17:25', '2026-02-27 06:17:25'),
(6, 2, 23, '$2y$12$/ogrhRNeOC4Cth9RLrmwV.8DI.Dmfmroq2kU8QXIsLelUZtgUqPlS', '2026-03-12 07:33:17', NULL, '2026-02-27 06:33:57', '2026-03-12 07:33:17'),
(7, 2, 15, '$2y$12$xh8MZITS4T2XWiETSh0cXukIvofg6eet8lHOyStcltWijjUqrRyTK', '2026-03-15 15:56:18', NULL, '2026-03-15 15:56:19', '2026-03-15 15:56:19');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `waiter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_phone` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL DEFAULT 'order',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `transaction_reference` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `restaurant_id`, `waiter_id`, `customer_phone`, `amount`, `method`, `payment_type`, `status`, `transaction_reference`, `description`, `created_at`, `updated_at`) VALUES
(1, 4, 3, NULL, NULL, 3000.00, 'ussd', 'order', 'pending', 'BOT-6959B3E7B9ECD', NULL, '2026-01-03 23:27:19', '2026-03-15 15:59:04'),
(2, 5, 3, NULL, NULL, 3000.00, 'ussd', 'order', 'pending', 'BOT-6959B585ACD3F', NULL, '2026-01-03 23:34:13', '2026-03-15 15:59:04'),
(4, 7, 2, NULL, NULL, 40000.00, 'ussd', 'order', 'paid', 'BOT-7-1767488304', NULL, '2026-01-03 23:58:27', '2026-03-15 15:59:04'),
(6, 9, 2, NULL, NULL, 10000.00, 'ussd', 'order', 'paid', 'BOT-9-1767527089', NULL, '2026-01-04 10:44:55', '2026-03-15 15:59:04'),
(7, 10, 2, NULL, NULL, 28000.00, 'ussd', 'order', 'paid', 'BOT-10-1767691678', NULL, '2026-01-06 06:28:03', '2026-03-15 15:59:04'),
(8, 11, 2, NULL, NULL, 10000.00, 'ussd', 'order', 'paid', 'BOT-11-1767697436', NULL, '2026-01-06 08:04:01', '2026-03-15 15:59:04'),
(9, NULL, 2, NULL, '255678165524', 30000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768177158', 'Bill Payment', '2026-01-11 21:19:22', '2026-01-11 21:19:38'),
(10, NULL, 2, NULL, '255678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768177261', 'Tip for ENJO', '2026-01-11 21:21:05', '2026-01-11 21:21:05'),
(11, NULL, 2, NULL, '255753228505', 5000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768195928', 'Tip for ENJO', '2026-01-12 02:32:12', '2026-01-12 02:32:51'),
(12, NULL, 2, NULL, '255753228505', 8000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768196037', 'Tip for ENJO', '2026-01-12 02:34:01', '2026-01-12 02:34:01'),
(13, NULL, 2, NULL, '255678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768209352', 'Tip for ENJO', '2026-01-12 06:15:57', '2026-01-12 06:15:57'),
(14, NULL, 2, NULL, '255678165524', 5000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768213134', 'Tip for ENJO', '2026-01-12 07:18:59', '2026-01-12 07:19:13'),
(15, NULL, 2, NULL, '255679166524', 8009.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768213176', 'Tip for ENJO', '2026-01-12 07:19:41', '2026-01-12 07:19:52'),
(16, NULL, 2, NULL, '255753228505', 7000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768214734', 'Tip for JANET RICHARD', '2026-01-12 07:45:38', '2026-01-12 07:45:50'),
(17, NULL, 2, NULL, '255753228505', 7000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768214880', 'Tip for JANET RICHARD', '2026-01-12 07:48:04', '2026-01-12 07:48:16'),
(18, NULL, 2, NULL, '255759296502', 5000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768215137', 'Bill Payment', '2026-01-12 07:52:21', '2026-01-12 07:52:33'),
(19, NULL, 2, NULL, '255678165524', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768215717', 'Tip for MSUME ABDLAH', '2026-01-12 08:02:02', '2026-01-12 08:02:14'),
(20, NULL, 2, NULL, '255678165524', 5000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768219232', 'Tip for JANET RICHARD', '2026-01-12 09:00:36', '2026-01-12 09:00:48'),
(21, NULL, 2, NULL, '255753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768222440', 'Bill Payment', '2026-01-12 09:54:05', '2026-01-12 09:54:18'),
(22, NULL, 2, NULL, '255753228505', 6000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768222501', 'Tip for SIRIEL CHARLSE', '2026-01-12 09:55:06', '2026-01-12 09:55:18'),
(23, NULL, 2, NULL, '255786116010', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768223376', 'Tip for JANET RICHARD', '2026-01-12 10:09:39', '2026-01-12 10:09:51'),
(24, NULL, 2, NULL, '255628042409', 2000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768228693', 'Bill Payment', '2026-01-12 11:38:17', '2026-01-12 11:38:29'),
(25, NULL, 2, NULL, '255786116010', 1000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768231878', 'Tip for SIRIEL CHARLSE', '2026-01-12 12:31:23', '2026-01-12 12:31:34'),
(26, NULL, 2, NULL, '0678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768342632', 'Bill Payment', '2026-01-13 19:17:13', '2026-01-13 19:17:13'),
(27, NULL, 2, NULL, '0750599412', 2000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768342754', 'Bill Payment', '2026-01-13 19:19:14', '2026-01-13 19:19:14'),
(28, NULL, 2, NULL, '0750599412', 2000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768343237', 'Bill Payment', '2026-01-13 19:27:18', '2026-01-13 19:27:18'),
(29, NULL, 2, NULL, '0750599412', 2000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768344344', 'Bill Payment', '2026-01-13 19:45:46', '2026-01-13 19:45:46'),
(30, NULL, 2, NULL, '0750599412', 2000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768345011', 'Bill Payment', '2026-01-13 19:56:53', '2026-01-13 19:56:53'),
(31, NULL, 2, NULL, '0678165524', 2000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768345107', 'Bill Payment', '2026-01-13 19:58:28', '2026-01-13 19:58:28'),
(32, NULL, 2, NULL, '0678165524', 4000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768345235', 'Tip for JANET RICHARD', '2026-01-13 20:00:36', '2026-01-13 20:00:36'),
(34, NULL, 2, NULL, '0678165524', 4000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768386602', 'Bill Payment', '2026-01-14 07:30:05', '2026-01-14 07:30:05'),
(35, NULL, 2, NULL, '0753228505', 300.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768386802', 'Tip for SIRIEL CHARLSE', '2026-01-14 07:33:25', '2026-01-14 07:33:46'),
(36, NULL, 2, NULL, '0753228505', 100.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768387239', 'Bill Payment', '2026-01-14 07:40:40', '2026-01-14 07:40:40'),
(37, NULL, 2, NULL, '0678165543', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768457561', 'Bill Payment', '2026-01-15 03:12:44', '2026-01-15 03:12:44'),
(38, NULL, 2, NULL, '0678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768554662', 'Tip for MSUME ABDLAH', '2026-01-16 06:11:05', '2026-01-16 06:11:05'),
(39, NULL, 2, NULL, '0678165534', 2000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768555142', 'Tip for MSUME ABDLAH', '2026-01-16 06:19:04', '2026-01-16 06:19:04'),
(40, NULL, 2, NULL, '0678165543', 10000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768556667', 'Bill Payment', '2026-01-16 06:44:29', '2026-01-16 06:44:29'),
(41, NULL, 2, NULL, '0678154332', 5000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768556896', 'Bill Payment', '2026-01-16 06:48:19', '2026-01-16 06:48:19'),
(42, NULL, 2, NULL, '0678454321', 5000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768556920', 'Tip for SIRIEL CHARLSE', '2026-01-16 06:48:42', '2026-01-16 06:48:42'),
(43, NULL, 2, NULL, '0753228505', 300.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768557904', 'Tip for SIRIEL CHARLSE', '2026-01-16 07:05:06', '2026-01-16 07:05:37'),
(44, NULL, 2, NULL, '0753228505', 400.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768558024', 'Bill Payment', '2026-01-16 07:07:06', '2026-01-16 07:07:06'),
(45, NULL, 2, NULL, '0786116010', 500.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768568628', 'Tip for MACHA JOHN', '2026-01-16 10:03:50', '2026-01-16 10:03:50'),
(46, NULL, 2, NULL, '0678165543', 6999.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768586104', 'Tip for SIRIEL CHARLSE', '2026-01-16 14:55:13', '2026-01-16 14:55:13'),
(47, NULL, 2, NULL, '0678165545', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768589063', 'Bill Payment', '2026-01-16 15:44:31', '2026-01-16 15:44:31'),
(48, NULL, 2, NULL, '0678654321', 5000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768589113', 'Tip for SIRIEL CHARLSE', '2026-01-16 15:45:18', '2026-01-16 15:45:18'),
(49, NULL, 2, NULL, '0753228505', 300.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768590857', 'Tip  SIRIEL CHARLSE', '2026-01-16 16:14:19', '2026-01-16 16:14:40'),
(50, NULL, 2, NULL, '0753228505', 200.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768590928', 'Bill Payment', '2026-01-16 16:15:30', '2026-01-16 16:15:51'),
(51, NULL, 2, NULL, '0753228505', 100.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768641565', 'Tip  ERICK SALEHE', '2026-01-17 06:19:27', '2026-01-17 06:19:27'),
(52, NULL, 2, NULL, '0753228505', 300.00, 'ussd', 'quick', 'paid', 'QUICK-2-1768641639', 'Bill Payment', '2026-01-17 06:20:41', '2026-01-17 06:21:02'),
(53, NULL, 2, NULL, '0766141849', 5000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768721321', 'Bill Payment', '2026-01-18 04:28:43', '2026-01-18 04:28:43'),
(54, NULL, 2, NULL, '0678165524', 5000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768721441', 'Bill Payment', '2026-01-18 04:30:43', '2026-01-18 04:30:43'),
(55, NULL, 2, NULL, '0753228505', 500.00, 'ussd', 'quick', 'pending', 'QUICK-2-1768730974', 'Tip  SIRIEL CHARLSE', '2026-01-18 07:09:37', '2026-01-18 07:09:37'),
(56, NULL, 2, NULL, '0753228505', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1769003995', 'Tip  SIRIEL CHARLSE', '2026-01-21 10:59:57', '2026-01-21 10:59:57'),
(57, NULL, 2, NULL, '0678165524', 40000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1769166025', 'Bill Payment', '2026-01-23 08:00:28', '2026-01-23 08:00:28'),
(58, NULL, 2, NULL, '0750599412', 5000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1769951006', 'Bill Payment', '2026-02-01 10:03:30', '2026-02-01 10:03:30'),
(59, NULL, 2, NULL, '0678165524', 2000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1769951075', 'Bill Payment', '2026-02-01 10:04:39', '2026-02-01 10:04:39'),
(60, NULL, 2, NULL, '0624762514', 100000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1769951130', 'Bill Payment', '2026-02-01 10:05:32', '2026-02-01 10:05:32'),
(61, NULL, 2, NULL, '0753228505', 5000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1770924127', 'Bill Payment', '2026-02-12 16:22:10', '2026-02-12 16:22:10'),
(62, NULL, 2, NULL, '0678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1770982793', 'Tip  SIRIEL CHARLSE', '2026-02-13 08:39:58', '2026-02-13 08:39:58'),
(63, NULL, 2, 13, '0678165524', 2000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1771050176', 'Tip  SIRIEL CHARLSE', '2026-02-14 03:22:58', '2026-02-14 03:22:58'),
(64, NULL, 2, 13, '0678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1771050636', 'Tip  SIRIEL CHARLSE', '2026-02-14 03:30:39', '2026-02-14 03:30:39'),
(65, NULL, 2, 13, '0678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1771051029', 'Tip  SIRIEL CHARLSE', '2026-02-14 03:37:11', '2026-02-14 03:37:11'),
(66, NULL, 2, 13, '0678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1771051286', 'Tip  SIRIEL CHARLSE', '2026-02-14 03:41:28', '2026-02-14 03:41:28'),
(67, NULL, 2, 13, '0678165524', 1000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771052223', 'Tip  SIRIEL CHARLSE', '2026-02-14 03:57:03', '2026-02-14 03:57:03'),
(68, NULL, 2, 13, '0678165524', 2000.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771105478', 'Tip for SIRIEL CHARLSE', '2026-02-14 18:44:38', '2026-02-14 18:44:38'),
(69, NULL, 2, 13, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771144737', 'Tip for SIRIEL CHARLSE', '2026-02-15 05:38:57', '2026-02-15 05:38:57'),
(70, NULL, 2, 13, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771145260', 'Tip for SIRIEL CHARLSE', '2026-02-15 05:47:40', '2026-02-15 05:47:40'),
(71, NULL, 2, 13, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771145403', 'Tip for SIRIEL CHARLSE', '2026-02-15 05:50:03', '2026-02-15 05:50:03'),
(72, NULL, 5, 11, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-5-1771145791', 'Tip for John Cena', '2026-02-15 05:56:31', '2026-02-15 05:56:31'),
(73, NULL, 5, 11, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-5-1771148512', 'Tip for John Cena', '2026-02-15 06:41:52', '2026-02-15 06:41:52'),
(74, NULL, 2, 13, '0678165524', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1771153771', 'Tip for SIRIEL CHARLSE', '2026-02-15 08:09:36', '2026-02-15 08:09:36'),
(75, NULL, 2, 13, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771154190', 'Tip for SIRIEL CHARLSE', '2026-02-15 08:16:32', '2026-02-15 08:17:53'),
(76, NULL, 2, 13, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771156419', 'Tip for SIRIEL CHARLSE', '2026-02-15 08:53:41', '2026-02-15 08:54:12'),
(77, NULL, 2, NULL, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771512008', 'Bill Payment', '2026-02-19 11:40:11', '2026-02-19 11:40:44'),
(78, NULL, 2, 13, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771512472', 'Tip for SIRIEL CHARLSE', '2026-02-19 11:47:54', '2026-02-19 11:48:36'),
(79, NULL, 2, 13, '0753228505', 500.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771575676', 'Tip for SIRIEL CHARLSE', '2026-02-20 05:21:22', '2026-02-20 05:21:44'),
(80, NULL, 2, 13, '0712345678', 500.00, 'ussd', 'quick', 'pending', 'QUICK-2-1771578570', 'Tip for SIRIEL CHARLSE', '2026-02-20 06:09:37', '2026-02-20 06:09:37'),
(81, NULL, 2, 13, '0715598080', 1000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1771578707', 'Tip for SIRIEL CHARLSE', '2026-02-20 06:11:56', '2026-02-20 06:11:56'),
(82, NULL, 2, NULL, '0678165524', 10000.00, 'ussd', 'quick', 'pending', 'QUICK-2-1771715247', 'Bill Payment', '2026-02-21 20:07:29', '2026-02-21 20:07:29'),
(83, NULL, 2, 13, '0753228505', 300.00, 'ussd', 'quick', 'paid', 'QUICK-2-1771933127', 'Tip for SIRIEL CHARLSE', '2026-02-24 08:38:51', '2026-02-24 08:39:12'),
(84, 35, 2, NULL, '0678165524', 25000.00, 'ussd', 'order', 'pending', 'ORD-35-1772112211', NULL, '2026-02-26 10:23:32', '2026-02-26 10:23:32'),
(85, 38, 2, NULL, '0753228505', 25000.00, 'ussd', 'order', 'pending', 'ORD-38-1772128189', NULL, '2026-02-26 14:49:53', '2026-02-26 14:49:53'),
(86, 47, 2, NULL, '0753228505', 25000.00, 'ussd', 'order', 'pending', 'ORD-47-1772256022', NULL, '2026-02-28 02:20:25', '2026-02-28 02:20:25'),
(87, 54, 2, NULL, '0715199834', 55000.00, 'ussd', 'order', 'pending', 'ORD-54-1773325469', NULL, '2026-03-12 11:24:32', '2026-03-12 11:24:32');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage_restaurants', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(2, 'manage_system_users', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(3, 'view_all_reports', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(4, 'manage_system_settings', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(5, 'manage_menu', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(6, 'manage_waiters', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(7, 'view_orders', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(8, 'update_orders', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(9, 'view_payments', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(10, 'confirm_payments', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(11, 'view_feedback', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(12, 'view_reports_restaurant', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(13, 'update_orders_status', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(14, 'view_tips', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(15, 'api_restaurant_search', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(16, 'api_get_menu', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(17, 'api_create_order', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(18, 'api_create_payment', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(19, 'api_check_payment', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(20, 'api_submit_feedback', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(21, 'api_submit_tip', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(2, 'App\\Models\\User', 6, 'WhatsAppBotToken', '44e727ad0ca01cca8bb72a333856f34ecfc37605aa42e96889d0e1a6b8811b46', '[\"*\"]', '2026-03-10 09:48:37', NULL, '2026-01-07 06:04:19', '2026-03-10 09:48:37'),
(36, 'App\\Models\\User', 13, 'api-login', 'a9eb594faf303ee5c9f85f4c1e5bbd5892e98ac37ab24fb99f8308c0c9e31505', '[\"*\"]', '2026-02-24 09:19:40', NULL, '2026-02-20 05:25:29', '2026-02-24 09:19:40'),
(46, 'App\\Models\\User', 22, 'api-login', '21246a093f4e5d0b06381afdbe52ce334c0ec4f2aed7d378b742e01f26fc4666', '[\"*\"]', '2026-02-26 15:02:11', NULL, '2026-02-26 10:36:28', '2026-02-26 15:02:11'),
(48, 'App\\Models\\User', 4, 'payroll-ai', '540b7a2dbdf28b076cea1ec8ec60d0dd998200ff990677283e3f095fca1d238c', '[\"*\"]', NULL, NULL, '2026-03-12 13:34:31', '2026-03-12 13:34:31'),
(49, 'App\\Models\\User', 4, 'payroll-ai', '9c00853089d0c81a1dd56651d55c1c42d5391fe001eb6b3ffcb397d9b1b2e3d9', '[\"*\"]', NULL, NULL, '2026-03-12 13:37:40', '2026-03-12 13:37:40'),
(50, 'App\\Models\\User', 4, 'payroll-ai', '834eef47bd5f8d7076f8d1f5e7132ef45e6b683c70b89c36159584c72218233d', '[\"*\"]', NULL, NULL, '2026-03-12 13:38:09', '2026-03-12 13:38:09'),
(51, 'App\\Models\\User', 4, 'payroll-ai', '7abb96ee3d52366a0475dbe8b1620f22b1d706ba569b4d11ce09dc224bcdf4c2', '[\"*\"]', NULL, NULL, '2026-03-12 13:39:12', '2026-03-12 13:39:12'),
(53, 'App\\Models\\User', 23, 'api-login', 'c29d3ef1e8a618f3f60531b78c48f1b182dc682258b756eee63e0ed900b9c070', '[\"*\"]', '2026-03-13 17:38:14', NULL, '2026-03-13 11:37:27', '2026-03-13 17:38:14');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `tag_prefix` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `support_phone` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `selcom_vendor_id` varchar(255) DEFAULT NULL,
  `selcom_api_key` varchar(255) DEFAULT NULL,
  `selcom_api_secret` varchar(255) DEFAULT NULL,
  `selcom_is_live` tinyint(1) NOT NULL DEFAULT 0,
  `menu_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kitchen_token` varchar(255) DEFAULT NULL,
  `kitchen_token_generated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `tag_prefix`, `location`, `phone`, `support_phone`, `logo`, `selcom_vendor_id`, `selcom_api_key`, `selcom_api_secret`, `selcom_is_live`, `menu_image`, `is_active`, `created_at`, `updated_at`, `kitchen_token`, `kitchen_token_generated_at`) VALUES
(1, 'TAPTAP Demo Grill', 'TAP', 'Dar es Salaam', '0700000000', NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-01-03 10:23:14', '2026-01-12 21:05:37', NULL, NULL),
(2, 'SAMAKI SAMAKI', 'SAM', 'Masaki,Dar es salaam', '0678165524', '0678455524', NULL, 'TILL60917564', 'MOBIAD-BAE4439D874CAFF7', '8PE3412A-7J3F0K7F-2A254AF-0P636D54', 1, 'menu_images/r2emtIIiEh0j8EjmOAY3MYVEOVj8LF9ExSEMnoMx.jpg', 1, '2026-01-03 11:43:48', '2026-03-17 17:50:15', '8mWAWpLWkKz8URRmDnn6uLc97MVl4cms', '2026-01-14 02:20:38'),
(3, 'KISINIA', 'KIS', 'tra mwenge', '0719-738-852', '0678165524', NULL, 'admin@taptap.com', NULL, 'password', 0, NULL, 1, '2026-01-03 23:04:43', '2026-02-21 19:32:18', NULL, NULL),
(4, 'SKY HOME', 'SKY', 'Mwenge,TRA', '0719738852', NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-01-04 10:30:24', '2026-01-12 21:05:37', NULL, NULL),
(5, 'TipTap Grill', 'TIP', 'Sinza, Dar es Salaam', '+255753228505', NULL, NULL, 'TILL60917564', 'MOBIAD-BAE4439D874CAFF7', '8PE3412A-7J3F0K7F-2A254AF-0P636D54', 0, NULL, 1, '2026-01-07 05:17:35', '2026-02-15 06:35:31', 'TfcjNeGko7FceshbY5FsS4z4EExGJCmB', '2026-02-15 06:35:31'),
(6, 'shawarma', 'SHA', 'Kinondoni, Dar es Salaam', '0753228505', NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-01-17 07:12:55', '2026-01-17 07:35:05', 'J41WB8G9hyt1SeW26HQMD3bQl5cHhH0E', '2026-01-17 07:35:05'),
(7, 'ibrahim', 'IBR', 'samaki', '2556787898788', NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, '2026-01-25 10:46:14', '2026-01-25 10:46:14', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(2, 'manager', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(3, 'waiter', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13'),
(4, 'bot_service', 'web', '2026-01-03 10:23:13', '2026-01-03 10:23:13');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 2),
(6, 2),
(7, 2),
(7, 3),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 3),
(14, 3),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(19, 4),
(20, 4),
(21, 4);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('3XAKZZqh8EXS2mx5ButiNnOyv76rIWo7WNkSRWGY', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:148.0) Gecko/20100101 Firefox/148.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidkVrVGJ6UFY0WDE0VEEwb2psU2hVMjFkM1gzVVhPcUFvVEZ1Nk0wUiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1774345541);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `group`, `created_at`, `updated_at`) VALUES
(1, 'system_name', 'TIPTAP', 'general', '2026-01-12 06:48:41', '2026-01-12 06:48:41'),
(2, 'support_email', 'support@tiptap.com', 'general', '2026-01-12 06:48:41', '2026-01-12 06:48:41'),
(3, 'commission_rate', '5', 'general', '2026-01-12 06:48:41', '2026-01-12 06:48:41'),
(4, 'min_withdrawal', '50000', 'general', '2026-01-12 06:48:42', '2026-01-12 06:48:42'),
(5, 'whatsapp_bot_number', '+255 752570026', 'general', '2026-01-12 06:48:42', '2026-03-10 09:48:06'),
(6, 'webhook_secret', NULL, 'general', '2026-01-12 06:48:42', '2026-01-12 06:48:42'),
(7, 'demo_push', '0', 'payments', '2026-02-14 03:55:25', '2026-02-15 08:08:59');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `waiter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `table_tag` varchar(20) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 4,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `restaurant_id`, `waiter_id`, `name`, `table_tag`, `qr_code`, `capacity`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 3, NULL, '1', 'KIS-T01', 'http://localhost:8000/menu/3?table=3', 7, 1, '2026-01-03 23:06:25', '2026-01-12 21:05:37'),
(4, 3, NULL, '2', 'KIS-T02', 'http://localhost:8000/menu/3?table=4', 6, 1, '2026-01-03 23:06:35', '2026-01-12 21:05:37'),
(5, 4, NULL, '1', 'SKY-T01', 'http://localhost:8000/menu/4?table=5', 4, 1, '2026-01-04 10:32:14', '2026-01-12 21:05:37'),
(7, 5, NULL, 'Mlangoni', 'TIP-T01', 'http://localhost:8000/menu/5?table=7', 4, 1, '2026-01-07 08:24:24', '2026-01-12 21:05:37'),
(10, 6, NULL, 'Mawenzi', 'SHA-T01', 'https://wa.me/255794321510?text=START_6_T10', 4, 1, '2026-01-17 07:33:22', '2026-01-17 07:33:22'),
(11, 2, NULL, 'front', 'SAM-T03', 'https://wa.me/255794321510?text=START_2_T11', 4, 1, '2026-02-25 04:11:25', '2026-02-25 04:11:25'),
(12, 2, NULL, 'Mawenzi', 'SAM-T04', 'https://wa.me/255794321510?text=START_2_T12', 6, 1, '2026-02-25 04:12:08', '2026-02-25 04:12:08'),
(13, 2, NULL, 'Uhuru', 'SAM-T05', 'https://wa.me/255794321510?text=START_2_T13', 6, 1, '2026-02-25 04:12:18', '2026-02-25 04:12:18');

-- --------------------------------------------------------

--
-- Table structure for table `tips`
--

CREATE TABLE `tips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `waiter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tips`
--

INSERT INTO `tips` (`id`, `restaurant_id`, `waiter_id`, `order_id`, `payment_id`, `amount`, `created_at`, `updated_at`) VALUES
(2, 2, 13, NULL, 67, 1000.00, '2026-02-14 03:57:03', '2026-02-14 03:57:03'),
(3, 2, 13, NULL, 68, 2000.00, '2026-02-14 18:44:38', '2026-02-14 18:44:38'),
(4, 2, 13, NULL, 69, 500.00, '2026-02-15 05:38:57', '2026-02-15 05:38:57'),
(5, 2, 13, NULL, 70, 500.00, '2026-02-15 05:47:40', '2026-02-15 05:47:40'),
(6, 2, 13, NULL, 71, 500.00, '2026-02-15 05:50:03', '2026-02-15 05:50:03'),
(7, 5, 11, NULL, 72, 500.00, '2026-02-15 05:56:31', '2026-02-15 05:56:31'),
(8, 5, 11, NULL, 73, 500.00, '2026-02-15 06:41:52', '2026-02-15 06:41:52'),
(9, 2, 13, NULL, 75, 500.00, '2026-02-15 08:17:53', '2026-02-15 08:17:53'),
(10, 2, 13, NULL, 76, 500.00, '2026-02-15 08:54:12', '2026-02-15 08:54:12'),
(11, 2, 13, NULL, 78, 500.00, '2026-02-19 11:48:36', '2026-02-19 11:48:36'),
(12, 2, 13, NULL, 79, 500.00, '2026-02-20 05:21:44', '2026-02-20 05:21:44'),
(13, 2, 13, NULL, 83, 300.00, '2026-02-24 08:39:12', '2026-02-24 08:39:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `profile_photo_path` varchar(500) DEFAULT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT 1,
  `last_online_at` timestamp NULL DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `global_waiter_number` varchar(20) DEFAULT NULL,
  `waiter_code` varchar(20) DEFAULT NULL,
  `employment_type` varchar(20) DEFAULT NULL COMMENT 'permanent or temporary (show-time)',
  `linked_until` date DEFAULT NULL COMMENT 'For temporary: link ends on this date',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `location`, `profile_photo_path`, `is_online`, `last_online_at`, `email`, `global_waiter_number`, `waiter_code`, `employment_type`, `linked_until`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `restaurant_id`) VALUES
(1, 'Super Admin', NULL, NULL, NULL, 1, NULL, 'admin@taptap.com', NULL, NULL, NULL, NULL, '2026-01-03 10:23:14', '$2y$12$uhgeFbU9hlv4hjA56Upm5OPT9Yu9TFBj7s8G/TSr5gjbgtQD8jx7G', 'joqB4DfvcpRiBJsgbN66eCPaBQphmcLRFk97chdEZD6bux7fbQ8kdk5pRk3w', '2026-01-03 10:23:14', '2026-01-03 10:23:14', NULL),
(2, 'Manager One', NULL, NULL, NULL, 1, NULL, 'manager@taptap.com', NULL, NULL, NULL, NULL, '2026-01-03 10:23:14', '$2y$12$uhgeFbU9hlv4hjA56Upm5OPT9Yu9TFBj7s8G/TSr5gjbgtQD8jx7G', 'jQRjcA5xsB', '2026-01-03 10:23:14', '2026-01-03 10:23:14', 1),
(3, 'Waiter One', NULL, NULL, NULL, 1, NULL, 'waiter@taptap.com', NULL, 'TAP-W01', NULL, NULL, '2026-01-03 10:23:14', '$2y$12$uhgeFbU9hlv4hjA56Upm5OPT9Yu9TFBj7s8G/TSr5gjbgtQD8jx7G', 'o2bDIhDpc2', '2026-01-03 10:23:14', '2026-01-12 21:05:37', 1),
(4, 'ERICK SALEHE', NULL, NULL, NULL, 1, NULL, 'ezekielsalehe11@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$idjCXd3K1FsDbDMR7.bm5OKsAhtUfjN8P69UkCuq/Z3HXUjh3xgU2', 'i6lt5ljOlFgHvGUG9jqDRbJkSbsHUO9QJK7QUa7vnbQF9I11IAfj53XGoM5j', '2026-01-03 11:43:49', '2026-01-03 11:43:49', 2),
(6, 'WhatsApp Bot Service', NULL, NULL, NULL, 1, NULL, 'bot@taptap.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$GuxRxdixXp/AAKk8sNFFG.D/0hvcgxLvjxGTQAXIwShnx89KGl.QS', NULL, '2026-01-03 22:42:17', '2026-01-03 22:42:17', NULL),
(7, 'ELVIN', NULL, NULL, NULL, 1, NULL, 'ezekielsalehe22@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$ml002unPTacJk2HxcprO.uVYXHsVXj4x9WVz9JlzqR5il.SJnSfGG', 'BBvEHLFu6RgNxfNLgbjoP7OZZKpaWq5VLoxwljyOFG0EUd78QOR7Og0w0wWg', '2026-01-03 23:04:43', '2026-01-03 23:04:43', 3),
(8, 'JOHN', NULL, NULL, NULL, 1, NULL, 'ezekielsalehe33@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$hY.AgqcNp2KsW8uBLDw3UOIC0KoyflyKXO1ZnL6CWtyJHBssJZMPi', NULL, '2026-01-04 10:30:24', '2026-01-04 10:30:24', 4),
(9, 'ELVIN', NULL, NULL, NULL, 1, NULL, 'elvin@gmail.com', NULL, 'SKY-W01', NULL, NULL, NULL, '$2y$12$pIjv/2lzZzZOuP5ww5bQrepomk/dzNb2kOL//WFQaT2qY6FTFJrra', NULL, '2026-01-04 10:32:01', '2026-01-12 21:05:37', 4),
(10, 'John Cena', NULL, NULL, NULL, 1, NULL, 'kminja@tiptapafrica.co.tz', NULL, NULL, NULL, NULL, NULL, '$2y$12$7zcZTPp82XELJHpWNdH6UOxA0z1yWel0KqXrwFY9YZGcgz4mziZ56', NULL, '2026-01-07 05:17:36', '2026-01-07 05:17:36', 5),
(11, 'John Cena', NULL, NULL, NULL, 1, NULL, 'jcena@tiptapafrica.co.tz', NULL, 'TIP-W01', NULL, NULL, NULL, '$2y$12$1fmLVirTDLiDQEj5NwVFmuHvA/amZIRfehzMUSs9fVFB6eoovSigy', NULL, '2026-01-08 03:45:40', '2026-01-12 21:05:37', 5),
(12, 'JANET RICHARD', NULL, NULL, NULL, 1, NULL, 'janet@gmail.com', NULL, 'SAM-W01', NULL, NULL, NULL, '$2y$12$Y66./IpbVNLtkcB2BD7p0eGZRav5WuQcD3r18yyR4EVAH40xCQDZa', NULL, '2026-01-12 07:40:18', '2026-01-12 21:05:37', 2),
(13, 'SIRIEL CHARLSE', NULL, NULL, NULL, 1, NULL, 'siriel@gmail.com', NULL, 'SAM-W02', NULL, NULL, NULL, '$2y$12$ypO9jvxNqo0eiIJZGyhs3elduEesgVPumKJEHm8HkhdcHDowVbomu', 'lCC9cZxfwcz05gv6Bcmg6ahrwGdVEA62nmD3ixzJ4NwEvvORn8HIuif5vjfa', '2026-01-12 07:40:54', '2026-01-12 21:05:37', 2),
(14, 'MALAKI GODFREY', NULL, NULL, NULL, 1, NULL, 'malaki@gmail.com', NULL, 'SAM-W03', NULL, NULL, NULL, '$2y$12$JwGIh2DWwwnTe4gLtduJPeCvBBu11KyKryTjkeaLl3JuAljs4.kHW', NULL, '2026-01-12 07:41:21', '2026-01-12 21:05:37', 2),
(15, 'MACHA JOHN', NULL, NULL, NULL, 1, NULL, 'macha@gmail.com', NULL, 'SAM-W04', NULL, NULL, NULL, '$2y$12$Q8mTfZvvRKT/32g7ZZOPuuVcDX0H6ywEn8lDW/ZDHUbk4QsqBRtRK', NULL, '2026-01-12 07:41:50', '2026-01-12 21:05:37', 2),
(16, 'MSUME ABDLAH', NULL, NULL, NULL, 1, NULL, 'msume@gmail.com', NULL, 'SAM-W05', NULL, NULL, NULL, '$2y$12$MrTBH6/l8h86B/zwrhSmtOVuB5X1kioW9OPetYU6f9LpR7tJIP8aa', NULL, '2026-01-12 07:42:19', '2026-01-12 21:05:37', 2),
(17, 'ERICK SALEHE', NULL, NULL, NULL, 1, NULL, 'erick@gmail.com', NULL, 'SAM-W06', NULL, NULL, NULL, '$2y$12$lOGjp4kMzDa4DZxTI8.QQux4ALoA5/zH6sgG8OJEC7mdXK.X8Mjy.', NULL, '2026-01-12 20:45:20', '2026-01-12 21:05:37', 2),
(19, 'John Doe', NULL, NULL, NULL, 1, NULL, 'jdoe@shawarma.co.tz', NULL, NULL, NULL, NULL, NULL, '$2y$12$CWpAHzt4zzaENDcChIk.nu.j6pf6R1f1Mw41R17iOooLS7Qg9B8bu', NULL, '2026-01-17 07:12:56', '2026-01-17 07:12:56', 6),
(20, 'Feisal Salum', NULL, NULL, NULL, 1, NULL, 'fsalum@shawarma.co.tz', NULL, 'SHA-W01', NULL, NULL, NULL, '$2y$12$GmqKeSUGHlAxFvXvP15c5uuC7wcEmAKj5tOqycYcAZumyqUsN0Xri', NULL, '2026-01-17 07:21:09', '2026-01-17 07:21:09', 6),
(21, 'ibrahim ashi', NULL, NULL, NULL, 1, NULL, 'doniaparoma@gmail.com', NULL, NULL, NULL, NULL, NULL, '$2y$12$XiWXYuDqHUqBVOmafVzjIupokEmekxPyqB7p2SSNqPO4WT7o0RwR6', NULL, '2026-01-25 10:46:15', '2026-01-25 10:46:15', 7),
(22, 'ERICK SALEHE', '+255 691 111 111', 'Masaki,Dar es salaam', 'profile/HsTrV6OvtQHv1wJXs2CdYJq0xez3lrvytYIswnOW.png', 1, NULL, 'ezekielsalehe99@gmail.com', 'TIPTAP-W-00001', 'SAM-W07', 'permanent', NULL, NULL, '$2y$12$FVFfHOE5/inEQwUvXvwoYengsB8ku5jYpC8KPqMd3iq1yRe6vCcmu', 'GECcBYOGRDr13i9pbKmdltUtB6NHnI7HnsQkHqzCAF0qdGCfZave8eFQk6LU', '2026-02-22 12:47:39', '2026-02-23 20:45:29', 2),
(23, 'kelvin minja', '0753228505', 'Masaki,Dar es salaam', NULL, 1, NULL, 'kelvinminjait@gmail.com', 'TIPTAP-W-00002', 'SAM-W08', 'permanent', NULL, NULL, '$2y$12$DKvrBP1RUkjMK30Rv6ZUFuTZJvBvorf/AR8SUud7WUU.Zbcri67ye', NULL, '2026-02-24 10:27:21', '2026-02-27 06:33:33', 2),
(24, 'Anna Doe', '0753228505', 'Masaki,Dar es salaam', NULL, 1, NULL, 'adoe@gmail.com', 'TIPTAP-W-00003', 'SAM-W09', 'permanent', NULL, NULL, '$2y$12$YOj3QtU4GoFcPEaqakT5G.5wZemk.p1.r0x7YR7FxWugM9hNq2OW.', NULL, '2026-03-12 07:45:04', '2026-03-12 14:14:13', 2);

-- --------------------------------------------------------

--
-- Table structure for table `waiter_restaurant_assignments`
--

CREATE TABLE `waiter_restaurant_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `linked_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `unlinked_at` timestamp NULL DEFAULT NULL,
  `employment_type` varchar(255) DEFAULT NULL,
  `linked_until` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `waiter_restaurant_assignments`
--

INSERT INTO `waiter_restaurant_assignments` (`id`, `user_id`, `restaurant_id`, `linked_at`, `unlinked_at`, `employment_type`, `linked_until`, `created_at`, `updated_at`) VALUES
(1, 22, 2, '2026-02-22 12:48:54', '2026-02-22 16:21:20', NULL, NULL, '2026-02-22 16:21:20', '2026-02-22 16:21:20'),
(2, 22, 2, '2026-02-22 19:34:05', '2026-02-22 16:34:05', 'permanent', NULL, '2026-02-22 16:23:39', '2026-02-22 16:34:05'),
(3, 12, 2, '2026-01-12 21:05:37', NULL, NULL, NULL, '2026-02-22 16:27:16', '2026-02-22 16:27:16'),
(4, 13, 2, '2026-01-12 21:05:37', NULL, NULL, NULL, '2026-02-22 16:27:16', '2026-02-22 16:27:16'),
(5, 14, 2, '2026-01-12 21:05:37', NULL, NULL, NULL, '2026-02-22 16:27:16', '2026-02-22 16:27:16'),
(6, 15, 2, '2026-01-12 21:05:37', NULL, NULL, NULL, '2026-02-22 16:27:16', '2026-02-22 16:27:16'),
(7, 16, 2, '2026-01-12 21:05:37', NULL, NULL, NULL, '2026-02-22 16:27:16', '2026-02-22 16:27:16'),
(8, 17, 2, '2026-01-12 21:05:37', NULL, NULL, NULL, '2026-02-22 16:27:16', '2026-02-22 16:27:16'),
(9, 22, 2, '2026-02-23 22:21:15', '2026-02-23 19:21:15', 'permanent', NULL, '2026-02-22 17:05:23', '2026-02-23 19:21:15'),
(10, 22, 2, '2026-02-23 23:44:19', '2026-02-23 20:44:19', 'permanent', NULL, '2026-02-23 19:23:18', '2026-02-23 20:44:19'),
(11, 22, 2, '2026-02-23 20:45:29', NULL, 'permanent', NULL, '2026-02-23 20:45:29', '2026-02-23 20:45:29'),
(12, 23, 2, '2026-02-25 07:21:33', '2026-02-25 04:21:33', 'permanent', NULL, '2026-02-24 10:29:46', '2026-02-25 04:21:33'),
(13, 23, 2, '2026-02-25 04:24:14', NULL, 'permanent', NULL, '2026-02-25 04:24:14', '2026-02-25 04:24:14'),
(14, 24, 2, '2026-03-12 10:50:09', '2026-03-12 07:50:09', 'permanent', NULL, '2026-03-12 07:48:52', '2026-03-12 07:50:09'),
(15, 24, 2, '2026-03-12 14:28:37', '2026-03-12 11:28:37', 'permanent', NULL, '2026-03-12 07:52:08', '2026-03-12 11:28:37'),
(16, 24, 2, '2026-03-12 11:30:52', NULL, 'permanent', NULL, '2026-03-12 11:30:52', '2026-03-12 11:30:52');

-- --------------------------------------------------------

--
-- Table structure for table `waiter_salary_payments`
--

CREATE TABLE `waiter_salary_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `period_month` varchar(7) NOT NULL,
  `basic_salary` decimal(12,0) NOT NULL DEFAULT 0,
  `allowances` decimal(12,0) NOT NULL DEFAULT 0,
  `paye` decimal(12,0) NOT NULL DEFAULT 0,
  `nssf` decimal(12,0) NOT NULL DEFAULT 0,
  `net_pay` decimal(12,0) NOT NULL DEFAULT 0,
  `paid_at` timestamp NULL DEFAULT NULL,
  `confirmed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `waiter_salary_payments`
--

INSERT INTO `waiter_salary_payments` (`id`, `restaurant_id`, `user_id`, `period_month`, `basic_salary`, `allowances`, `paye`, `nssf`, `net_pay`, `paid_at`, `confirmed_by`, `created_at`, `updated_at`) VALUES
(1, 2, 22, '2026-02', 100000, 7000, 5000, 8900, 93100, '2026-02-24 09:50:31', 4, '2026-02-22 17:53:11', '2026-02-24 09:50:31'),
(2, 2, 13, '2026-02', 100000, 7000, 5000, 8900, 93100, '2026-02-22 18:05:31', 4, '2026-02-22 18:05:31', '2026-02-22 18:05:31'),
(3, 2, 23, '2026-02', 500000, 50000, 50000, 150000, 350000, '2026-02-24 15:28:07', 4, '2026-02-24 15:26:43', '2026-02-24 15:28:07'),
(4, 2, 23, '2026-03', 500000, 60000, 50000, 100000, 410000, '2026-03-02 12:35:17', 4, '2026-03-02 12:35:17', '2026-03-02 12:35:17'),
(5, 2, 12, '2026-03', 0, 0, 0, 0, 0, '2026-03-12 13:38:08', 4, '2026-03-12 13:38:08', '2026-03-12 13:38:08'),
(6, 2, 24, '2026-03', 80000, 6700, 9000, 9000, 68700, '2026-03-12 22:04:23', 4, '2026-03-12 22:04:23', '2026-03-12 22:04:23');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_details` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_user_id_foreign` (`user_id`);

--
-- Indexes for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `admin_sent_notifications`
--
ALTER TABLE `admin_sent_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_sent_notifications_user_id_foreign` (`user_id`),
  ADD KEY `admin_sent_notifications_restaurant_id_foreign` (`restaurant_id`);

--
-- Indexes for table `bots`
--
ALTER TABLE `bots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_restaurant_id_foreign` (`restaurant_id`);

--
-- Indexes for table `customer_requests`
--
ALTER TABLE `customer_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_requests_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `customer_requests_table_id_foreign` (`table_id`),
  ADD KEY `customer_requests_waiter_id_foreign` (`waiter_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `feedback_order_id_foreign` (`order_id`),
  ADD KEY `feedback_waiter_id_foreign` (`waiter_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `menu_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `orders_waiter_id_foreign` (`waiter_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_menu_item_id_foreign` (`menu_item_id`);

--
-- Indexes for table `order_portal_passwords`
--
ALTER TABLE `order_portal_passwords`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_portal_restaurant_user_unique` (`restaurant_id`,`user_id`),
  ADD KEY `order_portal_passwords_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`),
  ADD KEY `payments_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `payments_waiter_id_foreign` (`waiter_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurants_kitchen_token_unique` (`kitchen_token`),
  ADD UNIQUE KEY `restaurants_tag_prefix_unique` (`tag_prefix`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tables_table_tag_unique` (`table_tag`),
  ADD KEY `tables_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `tables_waiter_id_foreign` (`waiter_id`);

--
-- Indexes for table `tips`
--
ALTER TABLE `tips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tips_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `tips_waiter_id_foreign` (`waiter_id`),
  ADD KEY `tips_order_id_foreign` (`order_id`),
  ADD KEY `tips_payment_id_foreign` (`payment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_waiter_code_unique` (`waiter_code`),
  ADD UNIQUE KEY `users_global_waiter_number_unique` (`global_waiter_number`),
  ADD KEY `users_restaurant_id_foreign` (`restaurant_id`);

--
-- Indexes for table `waiter_restaurant_assignments`
--
ALTER TABLE `waiter_restaurant_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `waiter_restaurant_assignments_user_id_foreign` (`user_id`),
  ADD KEY `waiter_restaurant_assignments_restaurant_id_unlinked_at_index` (`restaurant_id`,`unlinked_at`);

--
-- Indexes for table `waiter_salary_payments`
--
ALTER TABLE `waiter_salary_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `waiter_salary_payments_restaurant_id_user_id_period_month_unique` (`restaurant_id`,`user_id`,`period_month`),
  ADD KEY `waiter_salary_payments_user_id_foreign` (`user_id`),
  ADD KEY `waiter_salary_payments_confirmed_by_foreign` (`confirmed_by`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawals_restaurant_id_foreign` (`restaurant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_sent_notifications`
--
ALTER TABLE `admin_sent_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bots`
--
ALTER TABLE `bots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer_requests`
--
ALTER TABLE `customer_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `order_portal_passwords`
--
ALTER TABLE `order_portal_passwords`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `waiter_restaurant_assignments`
--
ALTER TABLE `waiter_restaurant_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `waiter_salary_payments`
--
ALTER TABLE `waiter_salary_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD CONSTRAINT `admin_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_sent_notifications`
--
ALTER TABLE `admin_sent_notifications`
  ADD CONSTRAINT `admin_sent_notifications_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `admin_sent_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_requests`
--
ALTER TABLE `customer_requests`
  ADD CONSTRAINT `customer_requests_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_requests_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_requests_waiter_id_foreign` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `feedback_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_waiter_id_foreign` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_items_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_waiter_id_foreign` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_portal_passwords`
--
ALTER TABLE `order_portal_passwords`
  ADD CONSTRAINT `order_portal_passwords_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_portal_passwords_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_waiter_id_foreign` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tables`
--
ALTER TABLE `tables`
  ADD CONSTRAINT `tables_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tables_waiter_id_foreign` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tips`
--
ALTER TABLE `tips`
  ADD CONSTRAINT `tips_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tips_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tips_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tips_waiter_id_foreign` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `waiter_restaurant_assignments`
--
ALTER TABLE `waiter_restaurant_assignments`
  ADD CONSTRAINT `waiter_restaurant_assignments_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `waiter_restaurant_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `waiter_salary_payments`
--
ALTER TABLE `waiter_salary_payments`
  ADD CONSTRAINT `waiter_salary_payments_confirmed_by_foreign` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `waiter_salary_payments_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `waiter_salary_payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
