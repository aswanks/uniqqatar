-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 11, 2021 at 10:02 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uniq_qatar_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'category1', 1, '2018-12-29 05:46:47', '0000-00-00 00:00:00'),
(2, 'category2', 1, '2018-12-29 05:46:47', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `contactuses`
--

CREATE TABLE `contactuses` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `msg` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contactuses`
--

INSERT INTO `contactuses` (`id`, `first_name`, `last_name`, `email`, `phone`, `msg`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Jomon', 'jkjk', 'jomontm@gmail.com', '484613031', 'adad', 1, '2019-03-28 18:49:12', '2019-03-28 11:49:12'),
(2, 'Robertvar', 'Robertvar', 'lvfeebback123@gmail.com', '167112384', 'We are often up to 50%-60% less than other VoIP providers. \r\n \r\nOur VoIP pricing method is really different. \r\n \r\nWe offer free SIP/VoIP trunks and just charge for minutes.  Don\'t worry, our service and quality is outstanding since 2009 (10 years)! \r\n \r\nTake a look at our pricing and contact us to set up your account. \r\nUsage is month to month, with no contract jail! \r\n \r\nhttps://legacyvoip.com/pricing-unlimited-sip-trunks-with-bundled-minutes-reduce-cost-now \r\n \r\nTry us out.  You really won’t regret it! \r\n \r\nBusiness VoIP for premises based PBXs \r\nCloud Hosted PBXs \r\nCloud Hosted Call Centers \r\n \r\nWe support all SIP-able PBXs and phones. \r\n \r\nThank you, \r\n \r\nBob Green \r\nLegacy VoIP', 1, '2019-04-09 15:41:15', '2019-04-09 08:41:15'),
(3, 'Jis', NULL, 'ryte2jiss@gmail.com', '55067379', 'I want to know more about UNIQs membership criteria', 1, '2019-04-16 13:00:22', '2019-04-16 06:00:22'),
(4, 'JamesEmign', 'JamesEmign', 'svetlanacol0sova@yandex.ua', '116288734', 'Hi uniqqatar.com \r\nGrow your bitcoins by 10% per 2 days. \r\nProfit comes to your btc wallet automatically. \r\n \r\nTry  http://bm-syst.xyz \r\nit takes 2 minutes only and let your btc works for you! \r\n \r\nGuaranteed by the blockchain technology!', 1, '2019-04-23 15:19:54', '2019-04-23 08:19:54'),
(5, 'CarlosZiste', 'CarlosZiste', 'geometricviral@gmail.com', '382687738', 'Are you one of the millions of people in the USA that are struggling to make ends meet? \r\n \r\nSo was I, until I found the Perfect TEAM that supports each other as if we all are Family. \r\n \r\nYou have heard the old saying \"No One Left Behind\" This is as close as it gets. The only way \r\nyou can not Succeed on our Team is to not put any effort into it or quit. \r\n \r\nI am personally placing new Team members under the members that join my TEAM. \r\n \r\nWe have the answer that has helped Thousands and Thousands of people get more take-home pay, \r\n \r\nWith One step, in our program, you will take home the monthly membership costs. While at the same time \r\nsecuring your financial needs. \r\n \r\nIf you can Follow a few EASY Steps, we will show you how to get started on the road to Financial Freedom. \r\n \r\nYou will find no affiliate links in my advertising, I work one on one with you. This way I can place you under the newest member. And I will do the same for you. \r\n \r\nYou have to take the first step. \r\n \r\nGo here Now!! \r\n \r\nWe look forward to working with you. \r\n \r\nScott Caile . \r\n954-348-8522 \r\nhttps://successteamnow.com/myecon', 1, '2019-04-24 12:21:46', '2019-04-24 05:21:46'),
(6, 'Bryantlob', 'Bryantlob', 'cgorillamail@gmail.com', '236218844', 'Hi, uniqqatar.com \r\n \r\nI\'ve been visiting your website a few times and decided to give you some positive feedback because I find it very useful. Well done. \r\n \r\nI was wondering if you as someone with experience of creating a useful website could help me out with my new site by giving some feedback about what I could improve? \r\n \r\nYou can find my site by searching for \"casino gorilla\" in Google (it\'s the gorilla themed online casino comparison). \r\n \r\nI would appreciate if you could check it out quickly and tell me what you think. \r\n \r\ncasinogorilla.com \r\n \r\nThank you for help and I wish you a great week!', 1, '2019-04-27 09:24:17', '2019-04-27 02:24:17'),
(7, 'Stevesaich', 'Stevesaich', 'sj430383@gmail.com', '174612744', 'Hello, \r\n \r\nWe (TEK-NEFT ),Hereby bring to your esteemed company \r\nattention regarding our available JP54/JET FUEL JPA1 with 1,000,000 \r\nbarrels and D2 GAS OIL 100,000 Metric Tons and Virgin D6 50,000,000 Gallons \r\nallocation which is currently on board in Rotterdam port / Huston port. \r\nKindly notify us your willingness to allocation this available products. \r\n \r\nYours sincerely \r\nALEXANDER ILYICH \r\nEmail: sales@tek-neft.ru', 1, '2019-05-01 22:13:27', '2019-05-01 15:13:27'),
(8, 'Davidric', 'Davidric', 'gunrussia@scryptmail.com', '238285835', '25 charging traumatic pistols shooting automatic fire! Modified Makarov pistols with a silencer! Combat Glock 17 original or with a silencer! And many other types of firearms without a license, without documents, without problems! \r\nDetailed video reviews of our products you can see on our website. \r\nhttp://Gunrussia.pw \r\nIf the site is unavailable or blocked, email us at - Gunrussia@secmail.pro   or  Gunrussia@elude.in \r\nAnd we will send you the address of the backup site!', 1, '2019-05-04 06:20:22', '2019-05-03 23:20:22'),
(9, 'Patricia', 'Hope', 'ptrhop@protonmail.com', '653113777', 'Hello,\r\n\r\nI\'ve been a reader of your blog for around 4 months and I would like first of all to say that I really enjoy it.\r\n\r\nI\'ve got recently some problems with my eyes which effects my reading ability.\r\n\r\nThereby I\'ve a favor to ask. Is it possible for you to add podcast audio version of your articles?\r\n\r\nIt would be very useful for people like me or others who like to listen to your content.\r\n\r\nI\'ve researched on that a bit and I found few free services that can help to do that.\r\n\r\nHere are the websites that I found that can add podcast to your site for free, maybe there are more but I found these two.\r\n\r\nhttps://websitevoice.com\r\n\r\nhttps://www.text2speech.org\r\n\r\nThanks!\r\n\r\nPatricia Hope', 1, '2019-05-04 08:30:10', '2019-05-04 01:30:10');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `tittle` varchar(255) NOT NULL,
  `contant` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `tittle`, `contant`, `address`, `start_date`, `start_time`, `end_date`, `end_time`, `image`, `status`, `created_at`, `updated_at`) VALUES
(14, 'UNIQ Members Family Meet 2019', 'UNIQ Members Family Meet 2019', 'Shalimar palace Restaurant', '2019-04-05', '05:30:00', NULL, NULL, NULL, 1, '2019-03-26 12:58:02', '2019-03-26 12:58:02'),
(15, 'Thyroid Cancer Conference', 'Thyroid Cancer Conference Under the patronage of H.E Sheikh Abdullah Bin Nasser Bin Khalifa Al-Thani.', 'The Ritz-Carlton Doha, Qatar', '2019-04-19', NULL, '2019-04-20', NULL, NULL, 1, '2019-04-04 13:50:34', '2019-04-04 13:50:34'),
(16, 'UNIQ -BLOOD DONATION CAMPAIGN.', '\'\'Give blood ,share life\'\'\r\n  UNIQ in cooperation with Hamad Medical Corporation Blood Bank Unit is committed to conduct,yet another blood donation campaign activity.\r\n  Let\'s join this campaign...', 'HMC BLOOD BANK, UNIQ', '2019-04-26', '12:02:30', '2019-04-26', '14:30:00', NULL, 1, '2019-04-17 02:46:09', '2019-04-17 02:46:09'),
(17, 'QPEM 2019 Mid-Year Symposium on Pediatric Pain.', 'Sidra Medicine Sidra Medicine\r\nAbout Us\r\nClinics & Services\r\nPatients & Visitors\r\nEducation\r\nResearch\r\nHealthcare Professionals\r\nMedia\r\nHow Can We Help?\r\n\r\nHome\r\n/\r\nEvents Calendar\r\n/\r\nEvent Details\r\nQPEM 2019 Mid-Year Symposium on Pediatric Pain\r\nSymposium\r\n\r\nThe first in a series of QPEM mid-year symposiums that will provide high quality, evidence based updates on narrowly focused topics relevant to Pediatric Emergency Medicine.\r\n\r\nLocation: Sidra Medicine Hospital \r\n\r\nOur multidisciplinary participants will have expanded knowledge and competence on the assessment and management of pediatric pain, and will be able to recognize and manage common and un-common presentations where pain may result from investigations and treatment.\r\n\r\nParticipants will have improved critical thinking around how to think about and address pain and anxiety in our everyday clinical practice settings, and will also have a better understanding and comfort with a variety of the pharmacologic and non pharmacologic interventions available for our pediatric patients.\r\n\r\nThis Activity is an Accredited Group Learning Activity Category 1 as defined by the Qatar Council for Health Practitioners - Accreditation Department (QCHP-AD) and is approved for a maximum of 8 hours for Category 1\r\n\r\nThis Activity was planned by and for the healthcare team, and learners will receive 8 hours Inter-professional Continuing Education (IPCE) credits for learning and change. \r\n\r\nThe Symposium program highlights, guidance on attendees and registration can be found here. \r\n\r\nCost:\r\n\r\nVaried\r\nEvent Dates\r\nApr 27, 2019 7:00am - 4:00pm\r\n\r\nRegistration Info:\r\nhttps://www.eventbrite.com/e/qpem-2019-mid-year-symposium-on-pediatric-pain-tickets-57968924614S', 'Sidra Medicine Hospital,\r\nAl Gharrafa Street,\r\nAr-Rayyan,Doha,Qatar\r\n  4003 3333', '2019-04-27', NULL, '2019-04-24', '07:00:00', NULL, 1, '2019-04-24 02:05:02', '2019-04-24 02:05:02'),
(18, 'test title', 'test description', 'test address', '2021-06-16', '07:03:53', '2021-06-16', '19:04:02', NULL, 1, '2021-06-16 07:08:24', '2021-06-16 07:08:24'),
(19, 'test title', 'test description', 'test address', '2021-06-16', '07:03:53', '2021-06-16', '19:04:02', NULL, 1, '2021-06-16 07:08:44', '2021-06-16 07:08:44'),
(20, 'test title', 'test description', 'test address', '2021-06-16', '07:03:53', '2021-06-16', '19:04:02', NULL, 1, '2021-06-16 07:10:26', '2021-06-16 07:10:26'),
(21, 'Nesciunt voluptatib', 'Omnis aut aliquam pe', 'Aut qui alias vero m', '2013-09-26', '01:15:00', '1997-07-08', '23:42:00', NULL, 1, '2021-06-16 14:41:37', '2021-06-16 09:11:37'),
(24, 'gj', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2021-06-18 04:58:19', '2021-06-18 04:58:19');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` int(11) NOT NULL,
  `tittle` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `vdo_url` text DEFAULT NULL,
  `gallery_type` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `tittle`, `image`, `category_id`, `vdo_url`, `gallery_type`, `status`, `created_at`, `updated_at`) VALUES
(6, 'vdo', NULL, 2, 'https://www.youtube.com/embed/VVYJ5QutqaA', 'Video', 1, '2019-01-05 10:01:45', '2019-01-03 10:40:44'),
(7, 'tittle', '1d8a5803.2-74348497.jpg', 1, NULL, 'Image', 1, '2019-01-05 04:14:09', '2019-01-05 04:14:09'),
(8, 'tittle', '1d8a5805.2-24429029.jpg', 1, NULL, 'Image', 1, '2019-01-05 04:16:37', '2019-01-05 04:16:37'),
(9, 'tittle', '1d8a5962.2-2598X589.jpg', 2, NULL, 'Image', 1, '2019-01-05 04:17:21', '2019-01-05 04:17:21'),
(10, 'tittle', '2.2-66392693.jpg', 2, NULL, 'Image', 1, '2019-01-05 04:17:49', '2019-01-05 04:17:49'),
(11, 'tittle', '7f7a1528.2-38002322.jpg', 1, NULL, 'Image', 1, '2019-01-05 04:18:10', '2019-01-05 04:18:10'),
(12, 'tittle', '7f7a1536.2-6544T099.jpg', 2, NULL, 'Image', 1, '2019-01-05 04:18:42', '2019-01-05 04:18:42'),
(13, 'Aurora Lets Begin', '1.2-84677943.jpg', NULL, NULL, 'Image', 1, '2019-01-18 17:05:52', '2019-01-18 17:05:52'),
(15, 'Aurora Lets Begin', '7f7a2067.2-863116V6.jpg', NULL, NULL, 'Image', 1, '2019-01-18 17:07:41', '2019-01-18 17:07:41'),
(16, 'MEGA EVENT', 'img-0608.2-886B1695.JPG', NULL, NULL, 'Image', 1, '2019-01-18 17:17:10', '2019-01-18 17:17:10'),
(17, NULL, 'whatsapp-image-2019-03-26-at-090943.2-94636704.jpeg', 1, NULL, 'Image', 1, '2019-03-26 14:47:25', '2019-03-26 14:47:25'),
(18, NULL, 'whatsapp-image-2019-03-26-at-092850.2-31I83106.jpeg', 1, NULL, 'Image', 1, '2019-03-26 14:47:41', '2019-03-26 14:47:41'),
(19, NULL, 'whatsapp-image-2019-03-26-at-091129.2-W6179993.jpeg', 1, NULL, 'Image', 1, '2019-03-26 14:47:56', '2019-03-26 14:47:56'),
(20, NULL, 'whatsapp-image-2019-03-26-at-091257.2-H0870989.jpeg', NULL, NULL, 'Image', 1, '2019-03-26 14:48:07', '2019-03-26 14:48:07'),
(21, NULL, 'whatsapp-image-2019-03-26-at-094000.2-63669574.jpeg', NULL, NULL, 'Image', 1, '2019-03-26 14:48:24', '2019-03-26 14:48:24'),
(22, NULL, 'whatsapp-image-2019-03-26-at-093237.2-D9168385.jpeg', NULL, NULL, 'Image', 1, '2019-03-26 14:48:33', '2019-03-26 14:48:33'),
(23, NULL, 'whatsapp-image-2019-03-26-at-093150.2-50062926.jpeg', NULL, NULL, 'Image', 1, '2019-03-26 14:48:50', '2019-03-26 14:48:50'),
(24, NULL, 'whatsapp-image-2019-03-26-at-093245.2-50248D65.jpeg', NULL, NULL, 'Image', 1, '2019-03-26 14:49:44', '2019-03-26 14:49:44'),
(25, 'Uniq Meet', 'whatsapp-image-2019-03-29-at-152612.2-157765A0.jpeg', 1, NULL, 'Image', 1, '2019-04-01 17:55:25', '2019-04-01 17:55:25'),
(26, 'UNIQ Mega Event', 'whatsapp-image-2019-03-29-at-152502.2-63348980.jpeg', 1, NULL, 'Image', 1, '2019-04-01 17:56:13', '2019-04-01 17:56:13'),
(27, 'Kerala Flood Relief Mission 2018', 'whatsapp-image-2019-04-03-at-175306.2-53305022.jpeg', 1, NULL, 'Image', 1, '2019-04-04 13:59:32', '2019-04-04 13:59:32'),
(28, 'Kerala Flood Relief Mission 2018', 'whatsapp-image-2019-04-03-at-175307.2-98711598.jpeg', 1, NULL, 'Image', 1, '2019-04-04 14:00:21', '2019-04-04 14:00:21'),
(29, NULL, 'mak4829.2-50963556.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:18:34', '2019-04-06 15:18:34'),
(30, NULL, 'mak4841.2-74293942.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:20:29', '2019-04-06 15:20:29'),
(31, NULL, NULL, NULL, NULL, 'Image', 1, '2019-04-06 15:20:37', '2019-04-06 15:20:37'),
(32, NULL, 'mak4846.2-48P10063.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:21:38', '2019-04-06 15:21:38'),
(33, NULL, 'mak4860.2-4H059943.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:23:32', '2019-04-06 15:23:32'),
(34, NULL, 'mak4878.2-33582M80.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:23:32', '2019-04-06 15:23:32'),
(35, NULL, 'mak5131.2-56U30350.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:25:04', '2019-04-06 15:25:04'),
(36, NULL, 'mak5141.2-994645S2.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:25:43', '2019-04-06 15:25:43'),
(37, NULL, NULL, NULL, NULL, 'Image', 1, '2019-04-06 15:26:00', '2019-04-06 15:26:00'),
(38, NULL, 'mak5158.2-260Q3628.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:26:25', '2019-04-06 15:26:25'),
(39, NULL, 'mak5158.2-7189176S.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:26:58', '2019-04-06 15:26:58'),
(40, NULL, 'mak5172.2-4184K495.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:28:44', '2019-04-06 15:28:44'),
(41, NULL, 'mak5212.2-46636166.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:32:22', '2019-04-06 15:32:22'),
(42, NULL, 'mak5222.2-79950410.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:32:53', '2019-04-06 15:32:53'),
(43, NULL, 'mak5245.2-67532124.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:33:43', '2019-04-06 15:33:43'),
(44, NULL, 'mak5295.2-3947764R.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:36:25', '2019-04-06 15:36:25'),
(45, NULL, 'mak5351.2-58J66959.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:36:56', '2019-04-06 15:36:56'),
(46, NULL, 'mak5367.2-1272185V.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:37:20', '2019-04-06 15:37:20'),
(47, NULL, 'mak5379.2-72666995.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:37:39', '2019-04-06 15:37:39'),
(48, NULL, 'mak5383.2-0379331P.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:38:01', '2019-04-06 15:38:01'),
(49, NULL, 'mak5399.2-90134932.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:38:24', '2019-04-06 15:38:24'),
(50, NULL, 'mak5424.2-6M267094.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:38:48', '2019-04-06 15:38:48'),
(51, NULL, 'mak5428.2-L8441119.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:39:18', '2019-04-06 15:39:18'),
(52, NULL, 'mak5444.2-342726O0.jpg', NULL, NULL, 'Image', 1, '2019-04-06 15:39:48', '2019-04-06 15:39:48'),
(53, NULL, 'mak5167.2-53707858.jpg', NULL, NULL, 'Image', 1, '2019-04-07 02:19:44', '2019-04-07 02:19:44'),
(54, NULL, 'img-20190414-wa0045.2-3I137369.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:21:23', '2019-04-17 02:21:23'),
(55, NULL, NULL, NULL, NULL, 'Image', 1, '2019-04-17 02:21:30', '2019-04-17 02:21:30'),
(56, NULL, 'img-20190413-wa0361.2-97376914.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:21:58', '2019-04-17 02:21:58'),
(57, NULL, 'img-20190413-wa0357.2-786T7798.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:22:11', '2019-04-17 02:22:11'),
(58, NULL, 'img-20190413-wa0355.2-31837M32.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:22:28', '2019-04-17 02:22:28'),
(59, NULL, 'img-20190413-wa0352.2-859641E0.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:22:44', '2019-04-17 02:22:44'),
(60, NULL, 'img-20190413-wa0339.2-95210107.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:23:00', '2019-04-17 02:23:00'),
(61, NULL, 'img-20190413-wa0326.2-57254883.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:23:26', '2019-04-17 02:23:26'),
(62, NULL, 'img-20190413-wa0318.2-4842Q908.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:23:45', '2019-04-17 02:23:45'),
(63, NULL, 'img-20190413-wa0309.2-0759860S.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:24:01', '2019-04-17 02:24:01'),
(64, NULL, 'img-20190413-wa0259.2-09480596.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:24:21', '2019-04-17 02:24:21'),
(65, NULL, 'img-20190413-225531.2-992J5606.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:24:46', '2019-04-17 02:24:46'),
(66, NULL, 'img-20190413-wa0378.2-803R5709.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:25:14', '2019-04-17 02:25:14'),
(67, NULL, 'img-20190413-194506.2-935981P0.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:25:30', '2019-04-17 02:25:30'),
(68, 'UNIQ family Get Together 2019  Selfie Contest  2th Winner', 'img-20190413-194432.2-9B773349.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:29:01', '2019-04-17 02:29:01'),
(69, 'UNIQ family Get Together 2019  Selfie Contest  1th Winner', 'img-20190413-194354.2-C6580870.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:29:26', '2019-04-17 02:29:26'),
(70, 'UNIQ,ICC,$ ISC INDIAN KARATE TOURNAMENT  [Appreciating Award]', 'img-20190413-wa0150.2-34515616.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:35:15', '2019-04-17 02:35:15'),
(71, 'UNIQ,ICC,$ ISC INDIAN KARATE TOURNAMENT  [Appreciating Award]', 'img-20190413-wa0149.2-64744810.jpg', NULL, NULL, 'Image', 1, '2019-04-17 02:35:42', '2019-04-17 02:35:42'),
(72, 'UNIQ with  honorable Indian Ambassador.Discuss on the matter of developing an opportunity for Diploma/GNM Nurses in Qatar for professional advancement.', 'img-20190423-wa0113.2-08008313.jpg', NULL, NULL, 'Image', 1, '2019-04-24 02:31:29', '2019-04-24 02:31:29'),
(73, 'UNIQ grabbed the opportunity to honor the renowned Indian athletes.', 'img-20190426-wa0106.2-495578A3.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:39:51', '2019-04-28 02:39:51'),
(74, NULL, 'img-20190426-wa0082.2-J6713624.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:40:56', '2019-04-28 02:40:56'),
(75, NULL, 'img-20190426-wa0105.2-67997886.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:41:46', '2019-04-28 02:41:46'),
(76, NULL, 'img-20190426-wa0055.2-19404770.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:42:13', '2019-04-28 02:42:13'),
(77, NULL, 'img-20190426-wa0052.2-4957777E.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:42:41', '2019-04-28 02:42:41'),
(78, NULL, 'img-20190426-wa0116.2-63147573.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:43:06', '2019-04-28 02:43:06'),
(79, NULL, 'img-20190426-wa0114.2-00433357.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:43:30', '2019-04-28 02:43:30'),
(80, NULL, 'img-20190426-wa0093.2-51666262.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:44:17', '2019-04-28 02:44:17'),
(81, NULL, 'img-20190426-wa0096.2-75198977.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:44:41', '2019-04-28 02:44:41'),
(82, NULL, 'img-20190426-wa0082.2-63504589.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:45:02', '2019-04-28 02:45:02'),
(83, NULL, 'img-20190426-wa0074.2-838333G2.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:45:33', '2019-04-28 02:45:33'),
(84, NULL, 'img-20190426-wa0060.2-6547H476.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:45:54', '2019-04-28 02:45:54'),
(85, NULL, 'img-20190426-wa0056.2-884413R2.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:46:16', '2019-04-28 02:46:16'),
(86, NULL, 'img-20190426-wa0059.2-4566L754.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:46:45', '2019-04-28 02:46:45'),
(87, NULL, 'img-20190426-wa0092.2-01414614.jpg', NULL, NULL, 'Image', 1, '2019-04-28 02:47:08', '2019-04-28 02:47:08');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `tittle` varchar(255) NOT NULL,
  `place` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `tittle`, `place`, `date`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
(7, 'Breast Cancer Awareness Program', NULL, NULL, '<p>UNIQ- Social Educational division conducted first educational activity to raise the awareness on early detection and diagnosis of breast cancer.</p>', 'img-6010.2-9I776148.JPG', 1, '2019-01-18 10:00:13', '2019-01-18 17:00:13'),
(8, 'First General Body Meet', NULL, NULL, '<p>UNIQ held its first general body meet with the members. The development plan of 2018 was...</p>', '2.2-37475094.jpg', 1, '2019-01-18 09:40:43', '2019-01-18 16:40:43'),
(9, 'UNIQ participated in QIFF Football Tournament', NULL, NULL, '<p>UNIQ Cultural and Sports division organized and compete in the QIFF football tournament as UNIQ Kannoor team. Team UNIQ supported its members by providing a competitive platform for their extracurricular promotions...</p>', 'img-20181016-wa0241.2-33316356.jpg', 1, '2019-01-18 09:52:17', '2019-01-18 16:52:17'),
(11, 'Free Medical Camp With Diabetes awareness sessions in association with Qatar KMCC Calicut', 'KMCC hall Thumama.', '2019-01-02', '<p>Qatar KMCC Calicut health wing organized a&nbsp; Free Medical Camp With Diabetes awareness sessions, Blood pressure checkup, Blood sugar checkup etc in association with social &amp; educational wing of UNIQ UNITED NURSES OF INDIA -Qatar at KMCC hall Thumama.Dr.Noujas kattil (Associate specialist- Family Medicine) Delivered an awareness lecture on Diabetes,Life style,&nbsp; control and prevention of diabetes.Uniq members&nbsp; Mr.Sanu Gopi, Mr.Najad, Mr.Rekhin Lal, Mr.Semeer&nbsp; kk&nbsp;&nbsp;actively participated in the camp.The activities are cordinated by Mr.Nizar Cheruvath, Mr.Salih &amp; Mr.Ameer kusru from UNIQ&nbsp; Social and Educational wing.Team UNIQ would like to thank &amp; express our heartfelt gratitude to Dr.Noujas kattil and KMCC Calicut health wing for their immense support and cooperation.</p>', 'img-5782.2-04150928.JPG', 1, '2019-01-18 10:36:03', '2019-01-18 17:36:03'),
(12, 'UNIQ with T.P. Ramakrishnan Excise and labour minister.', 'Qatar', '2018-12-31', '<p>Productive discussion on the empowerment of nurses with labour minister in Doha.</p>', '20180627-225010.2-60895996.jpg', 1, '2019-01-18 10:38:11', '2019-01-18 17:38:11'),
(13, 'UNIQ Mega Show', 'Asian Town VIP Recreation Hall', '2018-06-22', '<p>AL Dhar Exchange presents, UNIQ Mega Event on 22/06/2018</p>', 'mege-event.2-53486438.jpeg', 1, '2019-04-01 17:42:43', '2019-04-01 17:42:43'),
(14, 'UNIQ Free CPD Event Winners', 'Qatar', '2019-03-29', '<p>Free CPD Event Winners by UNIQ on 29/03/2019</p>', 'cpd-event-winner.2-89652O12.jpeg', 1, '2019-04-01 17:47:35', '2019-04-01 17:47:35'),
(17, 'Notification from embassy of India', 'Indian cultural center Doha, Qatar', '2019-04-01', '<pre>\r\nFor Indian expatriates in Qatar, from 6th April, the Indian Cultural Center in Abu hamour offers an opportunity to attest documents and certificates. The Indian Embassy informed that applications will be accepted for attestation of ducuments and certificates from 9 am to 12 am on all Saturdays.</pre>', 'whatsapp-image-2019-04-01-at-220400.2-08766370.jpeg', 1, '2019-04-02 13:41:22', '2019-04-02 13:41:22'),
(18, 'UNIQ -BLOOD DONATION CAMPAIGN.', 'HMC BLOOD BANK', '2019-04-26', '<pre>\r\nHMC BLOOD BANK, UNIQ    &#39;&#39;Give blood ,share life&#39;&#39; UNIQ in cooperation with Hamad Medical Corporation Blood Bank Unit is committed to conduct,yet another blood donation campaign activity. Let&#39;s join this campaign on 04- 26-2019 at 2:30 PM.  </pre>', 'img-20190417-wa0192.2-9878668E.jpg', 1, '2019-04-17 20:07:39', '2019-04-18 03:07:39'),
(19, 'UNIQ grabbed the opportunity to honor the renowned Indian athletes.', 'Grand Qatar Palace', '2019-02-04', '<p>Be unique to serve lives.</p>', 'img-20190426-wa0106.2-57775767.jpg', 1, '2019-04-29 03:30:46', '2019-04-29 03:30:46'),
(20, 'Earum alias deserunt', 'Adipisicing quo porr', '2010-11-08', 'Error lorem quis eni', '2123209933.2-52367419.jpg', 1, '2021-06-18 14:59:51', '2021-06-18 09:29:51'),
(23, 'test', 'test', '2021-06-18', 'test', 'img-20210604-wa0004.2-80051837.jpg', 1, '2021-06-18 14:58:32', '2021-06-18 09:28:32'),
(25, 'Est autem reiciendi', 'Aliquid et aut in vo', '2008-09-18', 'Cupidatat magna inve', '1275639227.2-39V48582.jpg', 1, '2021-06-18 09:51:36', '2021-06-18 09:51:36');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `image` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `blood_grp` varchar(255) NOT NULL,
  `mob_no` varchar(255) NOT NULL,
  `alter_mob_no` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `whatsapp` varchar(255) NOT NULL,
  `ind_contact_no` varchar(255) DEFAULT NULL,
  `qid` varchar(255) NOT NULL,
  `qid_expiration` date DEFAULT NULL,
  `passport` varchar(255) NOT NULL,
  `address_qatar` text NOT NULL,
  `address_ind` text NOT NULL,
  `employer_pre` varchar(255) NOT NULL,
  `employer_current` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `reg_no` varchar(255) NOT NULL,
  `uniq_id` varchar(256) DEFAULT NULL,
  `registration_date` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `first_name`, `last_name`, `dob`, `image`, `gender`, `blood_grp`, `mob_no`, `alter_mob_no`, `email`, `whatsapp`, `ind_contact_no`, `qid`, `qid_expiration`, `passport`, `address_qatar`, `address_ind`, `employer_pre`, `employer_current`, `destination`, `reg_no`, `uniq_id`, `registration_date`, `status`, `created_at`, `updated_at`) VALUES
(9, 'Jomon', 'Michael', '1989-05-30', 'whatsapp-image-2018-02-13-at-95821-pm.2-15345856.jpeg', 'male', 'B +ve', '9744527293', '97445272+3', 'jomontm@gmail.com', '9744527293', NULL, '68464', NULL, '54548981', 'Aspire', 'Kerala', 'zxcv', 'dsgasd', 'sdag', 'dgsd', 'UNIQ_60cdadcc24f76', '2019-03-28', 3, '2021-06-21 05:27:41', '2021-06-19 10:29:47'),
(10, 'Jose', 'Blaise', '1985-04-03', '15545483909981613745756.2-83V65925.jpg', 'male', 'B positive', '30954171', '50737680', 'joseblaise@gmail.com', '30954171', NULL, '28535664218', NULL, 'K5076118', 'Najma plaza 4\r\nDoha qatar', 'Alumkal h\r\nSouth chellanam po \r\nCochin 682008', 'Medical trust hospital ernakulam', 'Al zaeem polyclinic', 'Staff nurse', '1234', 'UNIQ_60cdadc6323be', '2019-04-06', 3, '2021-06-21 05:27:52', '2021-06-19 10:29:48'),
(11, 'Jayalakshmy', 'Prajith', '1973-05-05', '68e40e1e-39ee-4b9c-8b23-401be96de591.2-4600N616.jpeg', 'female', 'Oneg', '66071449', '33049965', 'jayalekshmy4@gmail.com', '66071449', NULL, '27335621064', NULL, 'L4964751', 'Hmc', 'Changanacherry', 'Hmc', 'Hmc', 'Staff nurse', '17905', 'UNIQ_60cdadbdde7f8', '2019-04-09', 3, '2021-06-21 05:30:01', '2021-06-19 10:29:48'),
(12, 'Johnson', 'Mathew', '1987-05-16', 'image.2-0D379886.jpg', 'male', 'B+', '+97474032177', '+97450130288', 'meetjo7@gmail.com', '+97474032177', '+918547153691', '28735653821', NULL, 'P6213403', 'Nakheel landscapes', 'Kandamkulathu House \r\nKumplampoika PO\r\nPathanamthitta (Dist)\r\nKearala', 'Bathes Hospital New Delhi', 'Nakheel Landscapes', 'Staff Nurse', 'N16868', 'UNIQ_60cdadb629ce3', '2019-04-23', 3, '2021-06-21 05:30:43', '2021-06-19 10:29:48'),
(13, 'Preeja', 'Prasad', '1990-07-23', 'a77d6f31-c12e-4eb8-8cc5-a6824a45bbfa.2-41473938.jpeg', 'female', 'O+', '+97450130288', '+97474032177', 'preejaprasad06@gmail.com', '+97450130288', '+918547153691', '29035641433', NULL, 'K8313677', 'Al emadi hospital', 'Kandamkulathu (House)\r\nKumplampoika PO\r\nPathanamthitta\r\nKearala 689661', 'King Khalid Hospit \r\nSaudi Arabia', 'Al emadi hospital', 'Staff Nurse', 'TN39785', 'UNIQ_60cdadaee85f4', '2019-04-23', 3, '2021-06-21 05:32:59', '2021-06-19 10:29:48'),
(14, 'SHEMI', 'ASHIM', '1986-05-10', 'img-20190327-170854.2-0735G473.jpg', 'female', 'B positive', '30165045', '55619487', 'ashimshemi@gmail.com', '30165045', '9995111048', '28635624057', NULL, 'J0287368', 'Villa No-8 , IBN jareer street, Hilal\r\nDoha, Qatar', 'Kalathoor Padeetathil, Edakulangara PO\r\nKarunagappally, Kollam, kerala', 'Upasana Hospital, Kollam , Kerala', 'Naseem Al Rabeeh Medical Centre , Doha , Qatar', 'Registered Nurse/ Infection control Link Nurse', '60214', 'UNIQ_60cdada7439e1', '2019-04-23', 3, '2021-06-21 05:33:12', '2021-06-19 10:29:48'),
(15, 'Jincy', 'Thomas', '1979-03-04', 'img-20190331-wa0076.2-14890633.jpg', 'female', 'B+ve', '55172985', '55172985', 'jincyj79@gmail.com', '55172985', NULL, '27935612098', NULL, 'K3841298', 'Jain Mathew, zajel telecommunications, doha, Qatar, 1120', 'Pathiyil, punnakkunnu, parappa, kerala', 'Al hayat medical center', 'HMC', 'Staff nurse', '2795', 'UNIQ_60cdada26ef23', '2019-04-23', 3, '2021-06-21 05:33:22', '2021-06-19 10:29:48'),
(16, 'MARIA JERALDIN', 'ARULAPPAN', '1982-01-03', 'img-20190420-2153062.2-81302639.jpg', 'female', 'O+', '33053325', '66997694', 'mariajeraldin82@gmail.com', '33053325', NULL, '28235633405', NULL, 'J532007', 'Villa no -23, Zone - 52, Street - 959,Al Luqta, Qatar', '#92,balaramapuram,villivakkam,chennai -49', 'Manipal Hospital, Bangalore', 'HMC', 'SN', 'RN-56087', 'UNIQ_60cdad9d1ba02', '2019-04-23', 3, '2021-06-21 05:34:38', '2021-06-19 10:29:48'),
(17, 'Linu', 'Thomas', '2015-05-21', 'img-20181219-170903.2-Q4273861.jpg', 'female', 'A+vr', '77115916', '70415916', 'linuthomas151@gmail.com', '77115916', '919447046267', '28835674222', NULL, 'P7312919', 'Linu Thomas , Thumama signal , villa no.17', 'Linu Thomas , pulimoottil house , koonathara P.O, Shornur', 'Madonna hospital', 'Atlas medical center', 'Staff nurse', 'N24822', 'UNIQ_60cdad967de22', '2019-04-23', 3, '2021-06-21 05:38:11', '2021-06-19 10:29:48'),
(18, 'Jeetu Mariam', 'Jacob', '1984-08-01', '20190424-062122.2-25777638.jpg', 'female', 'O positive', '00974 74434494', '00974 77470848', 'jeetushino1@gmail.com', '00974 74434494', '00919946930405', '28435629388', NULL, 'R2430791', 'Ezdan A2\r\nMusherib \r\nNear manai round about \r\nDoha Qatar', 'Shino Paul\r\nTharakkandathil house\r\nPampampallam P.O\r\nPslakkad,Kerala,India', 'HMC', 'Hamad Medical corporation', 'Staff nurse', 'XXVIII-25445', 'UNIQ_60cdad8fe8912', '2019-04-24', 3, '2021-06-21 05:38:25', '2021-06-19 10:29:48'),
(19, 'Saramma', 'Philip', '1978-02-13', 'img-20181123-173826.2-949514T4.jpg', 'female', 'O positive', '66446012', '66721242', 'saraphilip78@yahoo.co.in', '66446012', '04692774559', '27835603839', NULL, 'J1141143', 'Madhinat khalifa south, Doha-Qatar', 'Padinjarepurackal, kumplampoika, Pathanamthitta,, Kerala', 'India', 'HMC', 'Staff Nurse', 'Nurse-29690, Midwife-29566', 'UNIQ_60cdad87d26bc', '2019-04-24', 3, '2021-06-21 05:38:37', '2021-06-19 10:29:48'),
(20, 'SHINY', 'ANTO', '1973-03-13', 'untitled-1.2-32399028.JPG', 'female', 'AB+', '66143553', '+97466048306', 'shinyanto2000@yahoo.co.in', '66143553', NULL, '27335618451', NULL, 'R5424245', '3 NORTH I,HMC DOHA QATAR\r\nPO 3050', 'MALIAKAL HOUSE ,\r\nKIZHAKKUMMURY PO \r\nTHRISSUR  DT\r\nKERALA-680571\r\nINDIA', 'FORTIS HOSPITAL \r\nNEW DELHI', 'HMC', 'CHARGE NURSE', 'NURSE-15639,MIDWIFE-15550', 'UNIQ_60cdad81161e3', '2019-04-24', 3, '2021-06-21 05:38:48', '2021-06-19 10:29:48'),
(21, 'Sijimol', 'Mathew', '1976-04-15', 'imag2004-1.2-2320Q968.jpg', 'female', 'B +ve', '55390939', '55436743', 'sijimolbaiju@gmail.com', '55390939', '00914985242391', '27635605189', NULL, 'H7738919', 'AL Rayyan phc ,Doha ,Qatar', 'Kattathel (H),thattummel p.o, Kannur (St) ,Kerala', 'Primary health care Corp', 'Primary health care corporation', 'Staff nurse', 'N4075', 'UNIQ_60cdad78868fb', '2019-04-25', 2, '2021-06-21 05:38:57', '2021-06-19 02:47:02'),
(22, 'Shyny', 'Mol', '2019-05-29', '3.2-5962963C.jpg', 'female', 'A positive', '31064548', '31064548', 'shynymolsuseelan95@gmail.com', '+918593873965', NULL, '28835673516', NULL, 'J2927602', 'Madeena khalifa south, al manhal street  ,doha ,qatar', 'Ottapunnavila,odayam,varkala p.o,tvm,kerala,india', 'Anathapuri hospitals and research  institute. Chakai ,tvm,kerala,india', 'Faster medical care', 'Staff nurse', 'N25413', 'UNIQ_60cdad705ce47', '2019-05-01', 3, '2021-06-21 05:39:07', '2021-06-19 10:29:48'),
(23, 'aru', 'vinu', '1993-04-28', 'swa-20160707-073227.2-2388124R.jpg', 'female', 'b+ve', '8999980898', '994712355', 'raju@gmail.com', '8999980898', 'qwe123', '12131', NULL, 'qewr123', 'sdfgh dfghjk', 'qwqee eee', 'qwwqw', 'qwqw', 'nurse', 'nurse123', 'UNIQ_60cdad6ae63b0', '2019-05-14', 3, '2021-06-21 05:39:25', '2021-06-19 10:29:48'),
(24, 'Betssy', 'Sam', '1983-12-17', '20210410-123507.2-7491126E.jpg', 'female', 'O-ve', '66906107', '50135222', 'betssy2011@gmail.com', '66906107', '+919860262049', '28335642318', NULL, 'K1619430', 'Al Wakra', 'Kausar sugar, 2 nd floor, opposite Bethel Church, Ambarnath - west', 'India', 'HMC', 'Staff Nurse', 'N12997', 'UNIQ_60cdad5ae21d1', '2021-04-10', 0, '2021-06-21 05:39:37', '2021-04-10 04:08:57'),
(25, 'Manju', 'Varghese', '1976-02-19', 'inbound5032356639114263632.2-30908641.jpg', 'female', 'O+ve', '55091805', '55091805', 'binumanju2011@gmail.com', '55091805', NULL, '27635611794', NULL, 'N27783296', 'Pobox 40751', 'Keekattil  manjuvilla.moolapadave chengannur', 'Kuwait', 'Phcc', 'Staff nurse', 'N921', 'UNIQ_60cdad4fd0bbb', '2021-05-13', 0, '2021-06-21 05:39:46', '2021-06-18 13:34:56'),
(26, 'test', 'test', '1987-10-06', '485901925.2-U4195555.jpg', 'other', 'Aliqua Itaque repre', '1234', 'Laboris fugiat bland', 'test@uniq.com', '1234', 'Autem ea fugiat pla', '1234', '2021-06-19', 'Vero eveniet atque', 'Dolores rerum dolore', 'Voluptas sunt rerum', 'test', 'test', 'test', 'Sunt ut ad aut adip', 'UNIQ_60cdace304b72', '2021-06-15', 1, '2021-06-21 05:59:36', '2021-06-21 00:22:08'),
(27, 'TaShya', 'Burton', '2001-08-22', '2123209933.2-137S8600.jpg', 'male', 'Ullam labore ut quid', 'Consectetur volupta', 'Iure atque et optio', 'wajoqiwo@mailinator.com', 'Architecto sit quo', 'Aperiam voluptatem', 'Et magni qui quibusd', '2018-05-23', 'Elit incidunt est', 'Qui dolore eos error', 'Natus excepturi accu', 'Est repudiandae iust', 'Accusamus perferendi', 'Ad accusamus et qui', 'Aute ut laboriosam', 'UNIQ_60d02ab03b2a0', '2021-06-21', 0, '2021-06-21 00:29:12', '2021-06-21 00:29:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 1,
  `remember_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `user_type`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admi', '123', 'admin@uni.com', '$2y$10$Dx/LTnLf2xg8ZxXg3XfSretmOiGs2b5DSK/othZxpPdecFrIb8.A2', 1, 1, '3WIyQ74KhSHL61GV74fxv81Uq4i7KBim0bVnLHYq5Uh0cnlgddc2ykNZ9EZM', '2021-05-22 08:30:25', '2018-12-22 03:54:10'),
(2, 'Anas', 'Hussain', 'anas@uniq.com', '$2y$10$pKMomoWajJq.Gr0XdAPfHeO36MaeshzRfhLR1ayQExtL9ZjI/JHVO', 1, 1, '2n5UCahmr5ttd3wKhT8kjuMllyVnIB3nEmnvJ2LSi6XKHwTDTY3b7e06Yetu', '2021-06-18 10:33:58', '2021-06-16 04:19:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contactuses`
--
ALTER TABLE `contactuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contactuses`
--
ALTER TABLE `contactuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
