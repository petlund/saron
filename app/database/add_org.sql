-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Värd: s579.loopia.se
-- Tid vid skapande: 01 nov 2020 kl 12:57
-- Serverversion: 10.3.25-MariaDB-log
-- PHP-version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `korskyrkan_se_db_2`
--

-- --------------------------------------------------------


--
-- Tabellstruktur `MemberState`
--

CREATE TABLE `MemberState` (
  `Id` int(11) NOT NULL,
  `Name` varchar(50) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT 'Status',
  `Description` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `FilterCreate` tinyint(4) NOT NULL DEFAULT 0,
  `FilterUpdate` tinyint(4) NOT NULL DEFAULT 0,
  `Updater` int(11) NOT NULL DEFAULT 0,
  `Updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `MemberState`
--

INSERT INTO `MemberState` (`Id`, `Name`, `Description`, `FilterCreate`, `FilterUpdate`, `Updater`, `Updated`) VALUES
(1, 'Ej medlem', 'Sätts för en person som saknar datom för medlemskap start och slut, samt saknar dopdatum', 0, 0, 16, '2020-10-27 13:23:55'),
(2, 'Medlem', 'Sätts för en person som har ett datum för medlemskap start men inte för medlemskap slut.', 1, 1, 16, '2020-10-27 13:24:02'),
(3, 'Dopregister', 'Sätts för en person som har ett datum för dop eller datum för start och slut av medlemskap', 0, 0, 16, '2020-10-27 13:24:09'),
(4, 'Anonymiserad', 'Sätts för en person som anonymiserats i medlemsregistret', 0, 0, 16, '2020-10-27 13:24:14'),
(5, 'Avliden', 'Sätts för en person som har ett datum i fältet Avliden', 0, 0, 16, '2020-10-27 13:24:18');

-- --------------------------------------------------------

--
-- Tabellstruktur `News`
--


CREATE TABLE `Org_Pos` (
  `Id` int(10) UNSIGNED NOT NULL,
  `People_FK` int(11) DEFAULT NULL,
  `PrevPeople_FK` int(11) DEFAULT NULL,
  `OrgPosStatus_FK` int(10) UNSIGNED NOT NULL,
  `OrgSuperPos_FK` int(10) UNSIGNED DEFAULT NULL,
  `OrgRole_FK` int(10) UNSIGNED NOT NULL,
  `OrgTree_FK` int(10) UNSIGNED NOT NULL,
  `Updater` int(11) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Comment` varchar(45) COLLATE utf8mb4_swedish_ci DEFAULT '50'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `Org_Pos`
--

INSERT INTO `Org_Pos` (`Id`, `People_FK`, `PrevPeople_FK`, `OrgPosStatus_FK`, `OrgSuperPos_FK`, `OrgRole_FK`, `OrgTree_FK`, `Updater`, `Updated`, `Comment`) VALUES
(183, NULL, NULL, 4, NULL, 51, 184, 1, '2020-11-01 11:55:07', ''),
(184, NULL, NULL, 4, NULL, 52, 186, 1, '2020-11-01 11:55:07', '2020-2022'),
(185, NULL, NULL, 4, NULL, 52, 186, 1, '2020-11-01 11:55:07', ''),
(186, NULL, NULL, 4, NULL, 52, 186, 1, '2020-11-01 11:55:07', ''),
(187, NULL, NULL, 4, NULL, 54, 186, 1, '2020-11-01 11:55:07', ''),
(188, NULL, NULL, 4, NULL, 53, 186, 1, '2020-11-01 11:55:07', ''),
(189, NULL, NULL, 4, NULL, 52, 186, 1, '2020-11-01 11:55:07', ''),
(190, NULL, NULL, 4, NULL, 55, 184, 1, '2020-11-01 11:55:07', ''),
(191, NULL, NULL, 4, NULL, 56, 199, 1, '2020-11-01 11:55:07', ''),
(192, NULL, NULL, 4, NULL, 57, 199, 1, '2020-11-01 11:55:07', ''),
(193, NULL, NULL, 4, NULL, 57, 199, 1, '2020-11-01 11:55:07', ''),
(194, NULL, NULL, 4, NULL, 57, 199, 1, '2020-11-01 11:55:07', ''),
(195, NULL, NULL, 4, NULL, 56, 200, 1, '2020-11-01 11:55:07', ''),
(196, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(197, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(198, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(199, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(200, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(201, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(202, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(203, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(204, NULL, NULL, 4, NULL, 57, 200, 1, '2020-11-01 11:55:07', ''),
(205, NULL, NULL, 4, NULL, 50, 201, 1, '2020-11-01 11:55:07', ''),
(206, NULL, NULL, 4, NULL, 59, 200, 1, '2020-11-01 11:55:07', ''),
(207, NULL, NULL, 4, NULL, 60, 201, 1, '2020-11-01 11:55:07', ''),
(208, NULL, NULL, 4, NULL, 59, 200, 1, '2020-11-01 11:55:07', ''),
(209, NULL, NULL, 4, NULL, 59, 200, 1, '2020-11-01 11:55:07', ''),
(210, NULL, NULL, 4, NULL, 60, 201, 1, '2020-11-01 11:55:07', ''),
(211, NULL, NULL, 4, NULL, 61, 201, 1, '2020-11-01 11:55:07', ''),
(212, NULL, NULL, 4, NULL, 58, 200, 1, '2020-11-01 11:55:07', ''),
(213, NULL, NULL, 4, NULL, 58, 200, 1, '2020-11-01 11:55:07', ''),
(214, NULL, NULL, 4, NULL, 58, 200, 1, '2020-11-01 11:55:07', ''),
(215, NULL, NULL, 4, NULL, 58, 200, 1, '2020-11-01 11:55:07', ''),
(216, NULL, NULL, 4, NULL, 58, 200, 1, '2020-11-01 11:55:07', ''),
(217, NULL, NULL, 4, NULL, 58, 200, 1, '2020-11-01 11:55:07', ''),
(218, NULL, NULL, 4, NULL, 58, 200, 1, '2020-11-01 11:55:07', ''),
(219, NULL, NULL, 4, NULL, 57, 202, 1, '2020-11-01 11:55:07', ''),
(220, NULL, NULL, 4, NULL, 58, 200, 1, '2020-11-01 11:55:07', ''),
(221, NULL, NULL, 4, NULL, 57, 202, 1, '2020-11-01 11:55:07', ''),
(222, NULL, NULL, 4, NULL, 57, 202, 1, '2020-11-01 11:55:07', ''),
(223, NULL, NULL, 4, NULL, 56, 203, 1, '2020-11-01 11:55:07', ''),
(224, NULL, NULL, 4, NULL, 62, 203, 1, '2020-11-01 11:55:07', ''),
(225, NULL, NULL, 4, NULL, 62, 203, 1, '2020-11-01 11:55:07', ''),
(226, NULL, NULL, 4, NULL, 62, 203, 1, '2020-11-01 11:55:07', ''),
(227, NULL, NULL, 4, NULL, 56, 204, 1, '2020-11-01 11:55:07', ''),
(228, NULL, NULL, 4, NULL, 62, 204, 1, '2020-11-01 11:55:07', ''),
(229, NULL, NULL, 4, NULL, 62, 204, 1, '2020-11-01 11:55:07', ''),
(230, NULL, NULL, 4, NULL, 62, 204, 1, '2020-11-01 11:55:07', ''),
(231, NULL, NULL, 4, NULL, 62, 204, 1, '2020-11-01 11:55:07', ''),
(232, NULL, NULL, 4, NULL, 62, 204, 1, '2020-11-01 11:55:07', ''),
(233, NULL, NULL, 4, NULL, 62, 204, 1, '2020-11-01 11:55:07', ''),
(234, NULL, NULL, 4, NULL, 56, 205, 1, '2020-11-01 11:55:07', ''),
(235, NULL, NULL, 4, NULL, 62, 205, 1, '2020-11-01 11:55:07', ''),
(236, NULL, NULL, 4, NULL, 50, 209, 1, '2020-11-01 11:55:07', ''),
(237, NULL, NULL, 4, NULL, 56, 207, 1, '2020-11-01 11:55:07', ''),
(238, NULL, NULL, 4, NULL, 50, 209, 1, '2020-11-01 11:55:07', ''),
(239, NULL, NULL, 4, NULL, 62, 207, 1, '2020-11-01 11:55:07', ''),
(240, NULL, NULL, 4, NULL, 62, 207, 1, '2020-11-01 11:55:07', ''),
(241, NULL, NULL, 4, NULL, 62, 207, 1, '2020-11-01 11:55:07', ''),
(242, NULL, NULL, 4, NULL, 56, 206, 1, '2020-11-01 11:55:07', ''),
(243, NULL, NULL, 4, NULL, 62, 207, 1, '2020-11-01 11:55:07', ''),
(244, NULL, NULL, 4, NULL, 62, 207, 1, '2020-11-01 11:55:07', ''),
(245, NULL, NULL, 4, NULL, 62, 207, 1, '2020-11-01 11:55:07', ''),
(246, NULL, NULL, 4, NULL, 62, 206, 1, '2020-11-01 11:55:07', 'Lägerchef'),
(247, NULL, NULL, 4, NULL, 62, 206, 1, '2020-11-01 11:55:07', ''),
(248, NULL, NULL, 4, NULL, 62, 206, 1, '2020-11-01 11:55:07', ''),
(249, NULL, NULL, 4, NULL, 56, 210, 1, '2020-11-01 11:55:07', ''),
(250, NULL, NULL, 4, NULL, 62, 210, 1, '2020-11-01 11:55:07', ''),
(251, NULL, NULL, 4, NULL, 62, 210, 1, '2020-11-01 11:55:07', ''),
(252, NULL, NULL, 4, NULL, 62, 210, 1, '2020-11-01 11:55:07', ''),
(253, NULL, NULL, 4, NULL, 62, 210, 1, '2020-11-01 11:55:07', ''),
(254, NULL, NULL, 4, NULL, 62, 210, 1, '2020-11-01 11:55:07', ''),
(255, NULL, NULL, 4, NULL, 62, 210, 1, '2020-11-01 11:55:07', ''),
(256, NULL, NULL, 4, NULL, 62, 210, 1, '2020-11-01 11:55:07', ''),
(257, NULL, NULL, 4, NULL, 62, 210, 1, '2020-11-01 11:55:07', ''),
(258, NULL, NULL, 4, NULL, 54, 211, 1, '2020-11-01 11:55:07', ''),
(259, NULL, NULL, 4, NULL, 56, 211, 1, '2020-11-01 11:55:07', ''),
(260, NULL, NULL, 4, NULL, 52, 211, 1, '2020-11-01 11:55:07', ''),
(261, NULL, NULL, 4, NULL, 52, 211, 1, '2020-11-01 11:55:07', ''),
(262, NULL, NULL, 4, NULL, 52, 211, 1, '2020-11-01 11:55:07', ''),
(263, NULL, NULL, 4, NULL, 52, 211, 1, '2020-11-01 11:55:07', ''),
(264, NULL, NULL, 4, NULL, 52, 211, 1, '2020-11-01 11:55:07', ''),
(265, NULL, NULL, 4, NULL, 52, 211, 1, '2020-11-01 11:55:07', ''),
(266, NULL, NULL, 4, NULL, 52, 211, 1, '2020-11-01 11:55:07', ''),
(267, NULL, NULL, 4, NULL, 52, 211, 1, '2020-11-01 11:55:07', ''),
(268, NULL, NULL, 4, NULL, 63, 212, 1, '2020-11-01 11:55:07', ''),
(269, NULL, NULL, 4, NULL, 63, 212, 1, '2020-11-01 11:55:07', ''),
(270, NULL, NULL, 4, NULL, 50, 208, 1, '2020-11-01 11:55:07', ''),
(271, NULL, NULL, 4, NULL, 63, 212, 1, '2020-11-01 11:55:07', ''),
(272, NULL, NULL, 4, NULL, 50, 208, 1, '2020-11-01 11:55:07', ''),
(273, NULL, NULL, 4, NULL, 63, 212, 1, '2020-11-01 11:55:07', ''),
(274, NULL, NULL, 4, NULL, 64, 191, 1, '2020-11-01 11:55:07', ''),
(275, NULL, NULL, 4, NULL, 65, 191, 1, '2020-11-01 11:55:07', ''),
(276, NULL, NULL, 4, NULL, 67, 213, 1, '2020-11-01 11:55:07', ''),
(277, NULL, NULL, 4, NULL, 66, 213, 1, '2020-11-01 11:55:07', ''),
(278, NULL, NULL, 4, NULL, 68, 213, 1, '2020-11-01 11:55:07', ''),
(279, NULL, NULL, 4, NULL, 68, 213, 1, '2020-11-01 11:55:07', ''),
(280, NULL, NULL, 4, NULL, 69, 213, 1, '2020-11-01 11:55:07', ''),
(281, NULL, NULL, 4, NULL, 69, 213, 1, '2020-11-01 11:55:07', ''),
(282, NULL, NULL, 4, NULL, 70, 188, 1, '2020-11-01 11:55:07', 'Stundentpastorsgruppen'),
(283, NULL, NULL, 4, NULL, 70, 188, 1, '2020-11-01 11:55:07', 'EFK Region Öst'),
(284, NULL, NULL, 4, NULL, 70, 188, 1, '2020-11-01 11:55:07', 'Bildas årsmöte'),
(285, NULL, NULL, 4, NULL, 70, 188, 1, '2020-11-01 11:55:07', 'Sjukhuskyrkans årsmöte'),
(286, NULL, NULL, 4, NULL, 70, 188, 1, '2020-11-01 11:55:07', 'Sjukhuskyrkans styrelse'),
(287, NULL, NULL, 4, NULL, 70, 188, 1, '2020-11-01 11:55:07', 'Cafebussens ledningsgrupp'),
(288, NULL, NULL, 4, NULL, 70, 188, 1, '2020-11-01 11:55:07', 'NKR Årsmöte'),
(290, NULL, NULL, 4, NULL, 72, 184, 1, '2020-11-01 11:55:07', ''),
(291, NULL, NULL, 4, NULL, 67, 214, 1, '2020-11-01 11:55:07', ''),
(292, NULL, NULL, 4, NULL, 69, 214, 1, '2020-11-01 11:55:07', ''),
(293, NULL, NULL, 4, NULL, 69, 214, 1, '2020-11-01 11:55:07', ''),
(294, NULL, NULL, 4, NULL, 68, 214, 1, '2020-11-01 11:55:07', ''),
(295, NULL, NULL, 4, NULL, 68, 214, 1, '2020-11-01 11:55:07', ''),
(296, NULL, NULL, 4, NULL, 66, 214, 1, '2020-11-01 11:55:07', ''),
(299, NULL, NULL, 4, NULL, 69, 215, 1, '2020-11-01 11:55:07', ''),
(300, NULL, NULL, 4, NULL, 69, 215, 1, '2020-11-01 11:55:07', ''),
(301, NULL, NULL, 4, NULL, 68, 215, 1, '2020-11-01 11:55:07', ''),
(302, NULL, NULL, 4, NULL, 68, 215, 1, '2020-11-01 11:55:07', ''),
(303, NULL, NULL, 4, NULL, 66, 215, 1, '2020-11-01 11:55:07', ''),
(304, NULL, NULL, 4, NULL, 67, 215, 1, '2020-11-01 11:55:07', ''),
(306, NULL, NULL, 4, NULL, 69, 216, 1, '2020-11-01 11:55:07', ''),
(307, NULL, NULL, 4, NULL, 69, 216, 1, '2020-11-01 11:55:07', ''),
(308, NULL, NULL, 4, NULL, 68, 216, 1, '2020-11-01 11:55:07', ''),
(309, NULL, NULL, 4, NULL, 68, 216, 1, '2020-11-01 11:55:07', ''),
(310, NULL, NULL, 4, NULL, 66, 216, 1, '2020-11-01 11:55:07', ''),
(311, NULL, NULL, 4, NULL, 67, 216, 1, '2020-11-01 11:55:07', ''),
(313, NULL, NULL, 4, NULL, 69, 217, 1, '2020-11-01 11:55:07', ''),
(314, NULL, NULL, 4, NULL, 69, 217, 1, '2020-11-01 11:55:07', ''),
(315, NULL, NULL, 4, NULL, 68, 217, 1, '2020-11-01 11:55:07', ''),
(316, NULL, NULL, 4, NULL, 68, 217, 1, '2020-11-01 11:55:07', ''),
(317, NULL, NULL, 4, NULL, 66, 217, 1, '2020-11-01 11:55:07', ''),
(318, NULL, NULL, 4, NULL, 67, 217, 1, '2020-11-01 11:55:07', ''),
(319, NULL, NULL, 4, NULL, 76, 198, 1, '2020-11-01 11:55:07', ''),
(320, NULL, NULL, 4, NULL, 77, 198, 1, '2020-11-01 11:55:07', ''),
(329, NULL, NULL, 4, NULL, 50, 222, 1, '2020-11-01 11:55:07', ''),
(330, NULL, NULL, 4, NULL, 50, 224, 1, '2020-11-01 11:55:07', ''),
(331, NULL, NULL, 4, NULL, 80, 224, 1, '2020-11-01 11:55:07', ''),
(332, NULL, NULL, 4, NULL, 50, 225, 1, '2020-11-01 11:55:07', ''),
(333, NULL, NULL, 4, NULL, 88, 184, 1, '2020-11-01 11:55:07', ''),
(334, NULL, NULL, 4, NULL, 88, 184, 1, '2020-11-01 11:55:07', ''),
(335, NULL, NULL, 4, NULL, 83, 195, 1, '2020-11-01 11:55:07', ''),
(336, NULL, NULL, 4, NULL, 56, 192, 1, '2020-11-01 11:55:07', ''),
(337, NULL, NULL, 4, NULL, 62, 192, 1, '2020-11-01 11:55:07', ''),
(338, NULL, NULL, 4, NULL, 62, 192, 1, '2020-11-01 11:55:07', ''),
(339, NULL, NULL, 4, NULL, 62, 192, 1, '2020-11-01 11:55:07', ''),
(340, NULL, NULL, 4, NULL, 62, 192, 1, '2020-11-01 11:55:07', ''),
(341, NULL, NULL, 4, NULL, 62, 192, 1, '2020-11-01 11:55:07', ''),
(342, NULL, NULL, 4, NULL, 62, 192, 1, '2020-11-01 11:55:07', ''),
(343, NULL, NULL, 4, NULL, 62, 192, 1, '2020-11-01 11:55:07', ''),
(344, NULL, NULL, 4, NULL, 62, 192, 1, '2020-11-01 11:55:07', ''),
(345, NULL, NULL, 4, NULL, 50, 228, 1, '2020-11-01 11:55:07', ''),
(346, NULL, NULL, 4, NULL, 56, 229, 1, '2020-11-01 11:55:07', ''),
(347, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(348, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(349, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(350, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(351, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(352, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(353, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(354, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(355, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(356, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(357, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(358, NULL, NULL, 4, NULL, 89, 229, 1, '2020-11-01 11:55:07', ''),
(359, NULL, NULL, 4, NULL, 59, 229, 1, '2020-11-01 11:55:07', ''),
(360, NULL, NULL, 4, NULL, 59, 229, 1, '2020-11-01 11:55:07', ''),
(361, NULL, NULL, 4, NULL, 59, 229, 1, '2020-11-01 11:55:07', ''),
(362, NULL, NULL, 4, NULL, 50, 227, 1, '2020-11-01 11:55:07', ''),
(363, NULL, NULL, 4, NULL, 50, 194, 1, '2020-11-01 11:55:07', ''),
(364, NULL, NULL, 4, NULL, 50, 226, 1, '2020-11-01 11:55:07', ''),
(365, NULL, NULL, 4, NULL, 62, 194, 1, '2020-11-01 11:55:07', ''),
(366, NULL, NULL, 4, NULL, 50, 223, 1, '2020-11-01 11:55:07', ''),
(367, NULL, NULL, 4, NULL, 81, 231, 1, '2020-11-01 11:55:07', ''),
(368, NULL, NULL, 4, NULL, 81, 231, 1, '2020-11-01 11:55:07', ''),
(369, NULL, NULL, 4, NULL, 82, 232, 1, '2020-11-01 11:55:07', ''),
(370, NULL, NULL, 4, NULL, 95, 232, 1, '2020-11-01 11:55:07', ''),
(371, NULL, NULL, 4, NULL, 91, 232, 1, '2020-11-01 11:55:07', ''),
(372, NULL, NULL, 4, NULL, 91, 232, 1, '2020-11-01 11:55:07', ''),
(373, NULL, NULL, 4, NULL, 94, 232, 1, '2020-11-01 11:55:07', ''),
(374, NULL, NULL, 4, NULL, 94, 232, 1, '2020-11-01 11:55:07', ''),
(375, NULL, NULL, 4, NULL, 93, 232, 1, '2020-11-01 11:55:07', ''),
(376, NULL, NULL, 4, NULL, 93, 232, 1, '2020-11-01 11:55:07', ''),
(377, NULL, NULL, 4, NULL, 92, 232, 1, '2020-11-01 11:55:07', ''),
(378, NULL, NULL, 4, NULL, 92, 232, 1, '2020-11-01 11:55:07', ''),
(379, NULL, NULL, 4, NULL, 82, 232, 1, '2020-11-01 11:55:07', ''),
(380, NULL, NULL, 4, NULL, 95, 232, 1, '2020-11-01 11:55:07', ''),
(381, NULL, NULL, 4, NULL, 50, 233, 1, '2020-11-01 11:55:07', ''),
(382, NULL, NULL, 4, NULL, 80, 233, 1, '2020-11-01 11:55:07', ''),
(383, NULL, NULL, 4, NULL, 80, 233, 1, '2020-11-01 11:55:07', ''),
(384, NULL, NULL, 4, NULL, 80, 233, 1, '2020-11-01 11:55:07', ''),
(385, NULL, NULL, 4, NULL, 76, 198, 1, '2020-11-01 11:55:07', ''),
(386, NULL, NULL, 4, NULL, 76, 198, 1, '2020-11-01 11:55:07', ''),
(387, NULL, NULL, 4, NULL, 76, 198, 1, '2020-11-01 11:55:07', ''),
(388, NULL, NULL, 4, NULL, 76, 198, 1, '2020-11-01 11:55:07', ''),
(389, NULL, NULL, 4, NULL, 75, 197, 1, '2020-11-01 11:55:07', ''),
(390, NULL, NULL, 4, NULL, 74, 197, 1, '2020-11-01 11:55:07', ''),
(391, NULL, NULL, 4, NULL, 71, 198, 1, '2020-11-01 11:55:07', ''),
(392, NULL, NULL, 4, NULL, 52, 234, 1, '2020-11-01 11:55:07', ''),
(393, NULL, NULL, 4, NULL, 52, 234, 1, '2020-11-01 11:55:07', ''),
(394, NULL, NULL, 4, NULL, 52, 234, 1, '2020-11-01 11:55:07', ''),
(395, NULL, NULL, 4, NULL, 52, 234, 1, '2020-11-01 11:55:07', ''),
(396, NULL, NULL, 4, NULL, 50, 235, 1, '2020-11-01 11:55:07', ''),
(397, NULL, NULL, 4, NULL, 80, 235, 1, '2020-11-01 11:55:07', ''),
(398, NULL, NULL, 4, NULL, 80, 235, 1, '2020-11-01 11:55:07', ''),
(399, NULL, NULL, 4, NULL, 80, 235, 1, '2020-11-01 11:55:07', ''),
(400, NULL, NULL, 4, NULL, 80, 235, 1, '2020-11-01 11:55:07', ''),
(401, NULL, NULL, 4, NULL, 80, 235, 1, '2020-11-01 11:55:07', ''),
(402, NULL, NULL, 4, NULL, 80, 235, 1, '2020-11-01 11:55:07', ''),
(403, NULL, NULL, 4, NULL, 80, 235, 1, '2020-11-01 11:55:07', ''),
(404, NULL, NULL, 4, NULL, 80, 235, 1, '2020-11-01 11:55:07', ''),
(405, NULL, NULL, 4, NULL, 99, 236, 1, '2020-11-01 11:55:07', ''),
(406, NULL, NULL, 4, NULL, 97, 236, 1, '2020-11-01 11:55:07', ''),
(407, NULL, NULL, 4, NULL, 100, 236, 1, '2020-11-01 11:55:07', ''),
(408, NULL, NULL, 4, NULL, 96, 236, 1, '2020-11-01 11:55:07', ''),
(409, NULL, NULL, 4, NULL, 50, 220, 1, '2020-11-01 11:55:07', ''),
(410, NULL, NULL, 4, NULL, 50, 220, 1, '2020-11-01 11:55:07', ''),
(411, NULL, NULL, 4, NULL, 50, 237, 1, '2020-11-01 11:55:07', ''),
(412, NULL, NULL, 4, NULL, 54, 219, 1, '2020-11-01 11:55:07', ''),
(413, NULL, NULL, 4, NULL, 54, 218, 1, '2020-11-01 11:55:07', ''),
(414, NULL, NULL, 4, NULL, 71, 218, 1, '2020-11-01 11:55:07', ''),
(415, NULL, NULL, 4, NULL, 112, 238, 1, '2020-11-01 11:55:07', '');

-- --------------------------------------------------------

--
-- Tabellstruktur `Org_PosStatus`
--

CREATE TABLE `Org_PosStatus` (
  `Id` int(10) UNSIGNED NOT NULL,
  `Name` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Description` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `SortOrder` int(10) UNSIGNED NOT NULL,
  `Updater` int(11) DEFAULT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `Org_PosStatus`
--

INSERT INTO `Org_PosStatus` (`Id`, `Name`, `Description`, `SortOrder`, `Updater`, `Updated`) VALUES
(1, 'Avstämd', 'Sätts på positioner där förslaget är avstämt med vederbörande', 20, 1, '2020-05-08 14:54:42'),
(2, 'Förslag', 'Sätts på en position där ett namnförslag finns.', 10, 1, '2020-10-03 19:03:36'),
(4, 'Vakant', 'Sätts på postioner där det saknas en person. Eventuell person kopplad till uppdraget kopplas från uppdraget', 30, 1, '2020-09-14 12:17:20'),
(5, 'Tillsätts ej', 'Sätts på positioner som inte ska tillsättas vid detta tillfälle. Eventuell person kopplad till uppdraget kopplas från uppdraget', 40, 1, '2020-09-14 12:17:12'),
(6, 'Funktionsansvar', 'Sätts på roller där det är en funktion som har ansvar. Funktionen anges som kommentar.', 50, 1, '2020-10-18 10:00:34');

-- --------------------------------------------------------

--
-- Tabellstruktur `Org_Role`
--

CREATE TABLE `Org_Role` (
  `Id` int(10) UNSIGNED NOT NULL,
  `Name` varchar(50) COLLATE utf8mb4_swedish_ci NOT NULL,
  `RoleType` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `Description` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Updater` int(11) DEFAULT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `Org_Role`
--

INSERT INTO `Org_Role` (`Id`, `Name`, `RoleType`, `Description`, `Updater`, `Updated`) VALUES
(50, 'Ansvarig', 0, '', 16, '2020-10-26 19:12:57'),
(51, 'Församlingsordförande', 1, '', 16, '2020-10-26 19:14:19'),
(52, 'Ledamot', 0, '', 16, '2020-10-27 19:31:11'),
(53, 'Suppleant', 0, '', 16, '2020-10-27 19:42:49'),
(54, 'Ordförande', 0, '', 16, '2020-10-27 19:43:12'),
(55, 'Församlingskassör', 1, '', 16, '2020-10-27 19:51:54'),
(56, 'Sammankallande', 0, '', 16, '2020-10-30 17:04:26'),
(57, 'Ledare', 0, '', 16, '2020-10-30 15:12:59'),
(58, 'Hjälpledare', 0, '', 16, '2020-10-30 15:13:21'),
(59, 'Extrta resurser', 0, '', 16, '2020-10-30 15:19:05'),
(60, 'Lovsång', 0, '', 16, '2020-10-30 15:21:15'),
(61, 'Sociala medier', 0, '', 16, '2020-10-30 15:22:25'),
(62, 'Medarbetare', 0, '', 16, '2020-10-30 15:39:59'),
(63, 'Tolk', 0, '', 16, '2020-10-30 15:57:11'),
(64, 'Samordnare', 0, '', 16, '2020-10-30 16:10:38'),
(65, 'Biträdande samordnare', 0, '', 16, '2020-10-30 16:11:26'),
(66, 'Ansvarig ljud', 0, '', 16, '2020-10-30 16:16:27'),
(67, 'Ansvarig bild', 0, '', 16, '2020-10-30 16:16:36'),
(68, 'Gruppledare', 0, '', 16, '2020-10-30 16:17:55'),
(69, 'Biträdande gruppledare', 0, '', 16, '2020-10-30 16:18:09'),
(70, 'Representant', 0, '', 16, '2020-10-30 16:40:08'),
(71, 'Sekreterare', 0, '', 16, '2020-10-30 16:47:42'),
(72, 'Församlingsföreståndare', 1, '', 16, '2020-10-30 16:48:41'),
(73, 'Vice ordförande', 0, '', 16, '2020-10-30 16:52:05'),
(74, 'Revisor', 0, '', 16, '2020-10-30 23:27:27'),
(75, 'Revisor Sammankallande', 0, '', 16, '2020-10-30 23:27:42'),
(76, 'Valberedare', 0, '', 16, '2020-10-30 23:28:27'),
(77, 'Valberedare sammankallande', 0, '', 16, '2020-10-30 23:28:42'),
(80, 'Medhjälpare', 0, 'Deltar i verksamheten och stödjer ansvariga', 16, '2020-10-31 12:45:55'),
(81, 'Nattvardsledare', 0, '', 16, '2020-10-31 12:46:36'),
(82, 'Nattvardstjänare (jan, jul))', 0, '', 16, '2020-10-31 17:21:06'),
(83, 'Huvudboförare', 0, '', 16, '2020-10-31 14:44:22'),
(84, 'Inteäktshantering', 0, '', 16, '2020-10-31 14:45:39'),
(85, 'Lönehantering', 0, '', 16, '2020-10-31 14:46:06'),
(86, 'Kassör', 0, '', 16, '2020-10-31 14:46:47'),
(87, 'Församlingsledningsrepresentant', 0, '', 16, '2020-10-31 14:47:48'),
(88, 'Firmatecknare', 1, '', 16, '2020-10-31 14:54:57'),
(89, 'Förebedjare', 0, '', 16, '2020-10-31 16:47:27'),
(91, 'Nattvardstjänare (feb, aug)', 0, '', 16, '2020-10-31 17:21:23'),
(92, 'Nattvardstjänare (mar, sep)', 0, '', 16, '2020-10-31 17:22:20'),
(93, 'Nattvardstjänare (apr, okt)', 0, '', 16, '2020-10-31 17:23:42'),
(94, 'Nattvardstjänare (maj, nov)', 0, '', 16, '2020-10-31 17:23:56'),
(95, 'Nattvardstjänare (jun, dec)', 0, '', 16, '2020-10-31 17:24:15'),
(96, 'Säkerhet (Larm, lås)', 0, 'Lås och larm', 16, '2020-10-31 18:02:55'),
(97, 'Brandskydd', 0, '', 16, '2020-10-31 17:54:41'),
(98, 'Värme och ventilation', 0, '', 16, '2020-10-31 17:55:20'),
(99, 'El', 0, '', 16, '2020-10-31 17:55:35'),
(100, 'IT', 0, 'Internet, Wifi, skrivare, Kopiator', 16, '2020-10-31 17:56:57'),
(101, 'Samordning ljudtekniker', 0, '', 16, '2020-11-01 09:58:47'),
(102, 'Ansvarig ljudteknik', 0, '', 16, '2020-10-31 18:04:08'),
(103, 'Samordning bildtekniker', 0, '', 16, '2020-10-31 18:05:03'),
(104, 'Ljus (scen)', 0, '', 16, '2020-10-31 18:05:24'),
(105, 'Belysning', 0, '', 16, '2020-10-31 18:05:40'),
(106, 'Servicegrupper', 0, '', 16, '2020-10-31 18:06:00'),
(107, 'Lägenheter (Jour, Underhåll))', 0, '', 16, '2020-11-01 09:58:12'),
(108, 'Parkering och post', 0, '', 16, '2020-10-31 18:07:02'),
(109, 'Serviceavtal fastiget', 0, 'Sotning, Hiss och värme', 16, '2020-11-01 10:00:56'),
(110, 'Fastighetsunderhåll', 0, '', 16, '2020-11-01 10:01:51'),
(111, 'Utomhusmiljö', 0, '', 16, '2020-11-01 10:03:38'),
(112, 'Anställd', 1, '', 16, '2020-11-01 10:20:52');

-- --------------------------------------------------------

--
-- Tabellstruktur `Org_Role-UnitType`
--

CREATE TABLE `Org_Role-UnitType` (
  `Id` int(10) UNSIGNED NOT NULL,
  `OrgRole_FK` int(10) UNSIGNED DEFAULT NULL,
  `OrgUnitType_FK` int(10) UNSIGNED DEFAULT NULL,
  `SortOrder` int(11) NOT NULL DEFAULT 1,
  `Updater` int(11) DEFAULT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `Org_Role-UnitType`
--

INSERT INTO `Org_Role-UnitType` (`Id`, `OrgRole_FK`, `OrgUnitType_FK`, `SortOrder`, `Updater`, `Updated`) VALUES
(111, 51, 49, 1, 16, '2020-10-26 19:15:29'),
(112, 52, 51, 3, 16, '2020-10-31 12:29:45'),
(113, 54, 51, 1, 16, '2020-10-27 19:43:43'),
(114, 53, 51, 5, 16, '2020-10-31 12:29:34'),
(115, 55, 49, 3, 16, '2020-10-31 12:28:49'),
(117, 56, 52, 1, 16, '2020-10-30 15:09:33'),
(118, 57, 52, 2, 16, '2020-10-31 12:30:38'),
(119, 58, 52, 3, 16, '2020-10-31 12:30:47'),
(120, 59, 52, 5, 16, '2020-10-31 12:30:53'),
(121, 60, 53, 1, 16, '2020-10-30 15:21:30'),
(122, 50, 53, 1, 16, '2020-10-30 15:22:41'),
(123, 61, 53, 1, 16, '2020-10-30 15:23:04'),
(124, 57, 53, 1, 16, '2020-10-30 15:28:50'),
(125, 56, 54, 1, 16, '2020-10-30 15:36:10'),
(126, 62, 54, 1, 16, '2020-10-30 15:40:14'),
(127, 56, 55, 1, 16, '2020-10-30 15:47:13'),
(128, 62, 55, 1, 16, '2020-10-30 15:47:33'),
(129, 50, 56, 1, 16, '2020-10-30 15:49:45'),
(130, 52, 57, 1, 16, '2020-10-30 15:57:23'),
(131, 54, 57, 1, 16, '2020-10-30 15:57:50'),
(132, 56, 57, 1, 16, '2020-10-30 15:58:33'),
(133, 63, 58, 1, 16, '2020-10-30 16:01:11'),
(136, 67, 59, 4, 16, '2020-10-31 18:43:02'),
(137, 66, 59, 3, 16, '2020-10-31 18:42:40'),
(138, 68, 59, 1, 16, '2020-10-30 16:18:19'),
(139, 69, 59, 2, 16, '2020-10-31 18:42:23'),
(140, 70, 60, 1, 16, '2020-10-30 16:40:27'),
(141, 54, 61, 1, 16, '2020-10-30 16:47:26'),
(142, 71, 61, 1, 16, '2020-10-30 16:47:56'),
(143, 72, 49, 5, 16, '2020-10-31 12:28:55'),
(144, 73, 61, 1, 16, '2020-10-30 16:52:26'),
(145, 65, 62, 2, 16, '2020-10-31 18:41:49'),
(146, 64, 62, 1, 16, '2020-10-30 23:21:03'),
(147, 74, 64, 1, 16, '2020-10-30 23:27:56'),
(148, 75, 64, 1, 16, '2020-10-30 23:28:11'),
(149, 76, 63, 2, 16, '2020-10-31 17:37:37'),
(150, 77, 63, 1, 16, '2020-10-31 17:37:44'),
(156, 50, 67, 1, 16, '2020-10-31 13:06:11'),
(157, 80, 56, 3, 16, '2020-10-31 17:07:21'),
(158, 83, 68, 1, 16, '2020-10-31 14:44:36'),
(159, 84, 68, 1, 16, '2020-10-31 14:45:51'),
(160, 85, 68, 1, 16, '2020-10-31 14:46:19'),
(161, 86, 68, 1, 16, '2020-10-31 14:47:00'),
(162, 87, 68, 1, 16, '2020-10-31 14:48:00'),
(163, 88, 49, 6, 16, '2020-10-31 14:57:36'),
(164, 89, 69, 2, 16, '2020-10-31 16:52:20'),
(165, 56, 69, 1, 16, '2020-10-31 16:48:56'),
(166, 59, 69, 4, 16, '2020-10-31 16:52:11'),
(167, 81, 70, 0, 16, '2020-10-31 17:20:03'),
(168, 82, 70, 1, 16, '2020-10-31 17:25:04'),
(170, 91, 70, 2, 16, '2020-10-31 17:19:56'),
(171, 92, 70, 3, 16, '2020-10-31 17:25:17'),
(172, 93, 70, 4, 16, '2020-10-31 17:25:25'),
(173, 94, 70, 5, 16, '2020-10-31 17:25:36'),
(174, 95, 70, 6, 16, '2020-10-31 17:25:47'),
(175, 71, 63, 3, 16, '2020-10-31 17:35:15'),
(176, 62, 67, 2, 16, '2020-10-31 17:43:28'),
(177, 96, 71, 0, 16, '2020-11-01 09:54:15'),
(179, 99, 71, 0, 16, '2020-11-01 09:54:28'),
(180, 100, 71, 0, 16, '2020-11-01 09:54:35'),
(181, 101, 71, 0, 16, '2020-11-01 09:54:42'),
(182, 98, 71, 0, 16, '2020-11-01 09:54:48'),
(183, 62, 59, 5, 16, '2020-10-31 18:44:19'),
(184, 97, 71, 0, 16, '2020-11-01 09:54:55'),
(186, 102, 71, 0, 16, '2020-11-01 09:55:56'),
(187, 103, 71, 0, 16, '2020-11-01 09:56:19'),
(188, 104, 71, 0, 16, '2020-11-01 09:56:37'),
(189, 105, 71, 0, 16, '2020-11-01 09:56:47'),
(190, 107, 71, 0, 16, '2020-11-01 09:58:20'),
(191, 106, 71, 0, 16, '2020-11-01 09:59:04'),
(192, 108, 71, 0, 16, '2020-11-01 09:59:28'),
(193, 109, 71, 0, 16, '2020-11-01 10:01:12'),
(194, 110, 71, 0, 16, '2020-11-01 10:02:58'),
(195, 111, 71, 0, 16, '2020-11-01 10:04:11'),
(196, 112, 72, 0, 16, '2020-11-01 10:21:03');

-- --------------------------------------------------------

--
-- Tabellstruktur `Org_Tree`
--

CREATE TABLE `Org_Tree` (
  `Id` int(10) UNSIGNED NOT NULL,
  `Name` varchar(100) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Description` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `ParentTreeNode_FK` int(10) UNSIGNED DEFAULT NULL,
  `OrgUnitType_FK` int(10) UNSIGNED DEFAULT NULL,
  `Updater` int(11) DEFAULT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `Org_Tree`
--

INSERT INTO `Org_Tree` (`Id`, `Name`, `Description`, `ParentTreeNode_FK`, `OrgUnitType_FK`, `Updater`, `Updated`) VALUES
(184, ' 1. Ledning', '', NULL, 49, 16, '2020-11-01 11:38:59'),
(185, ' 5. Gudstjänstplanering och information', '', NULL, 50, 16, '2020-10-31 12:52:18'),
(186, ' 1.3 Församlingsledning', '', 184, 51, 16, '2020-11-01 11:33:24'),
(188, ' 2. Församlingens representanter', '', NULL, 60, 16, '2020-10-30 16:37:20'),
(189, ' 3. Barn och ungdom', '', NULL, 50, 13, '2020-10-30 14:59:18'),
(190, ' 4. Diakoni', '', NULL, 50, 13, '2020-10-30 14:59:59'),
(191, ' 6. Servicegrupper', '', NULL, 62, 16, '2020-10-30 23:21:53'),
(192, ' 7. Internationellt arbete', '', NULL, 55, 13, '2020-10-31 16:42:25'),
(193, ' 8. Själavård och förbön', '', NULL, 50, 13, '2020-10-30 15:03:06'),
(194, ' 9. Smågrupper', '', NULL, 67, 13, '2020-10-31 17:40:29'),
(195, '10. Ekonomi', '', NULL, 68, 16, '2020-10-31 14:48:56'),
(196, '11. Förvaltning', '', NULL, 50, 13, '2020-10-30 15:04:48'),
(197, '12. Revisionskommitté', '', NULL, 64, 16, '2020-10-30 23:29:42'),
(198, '13. Valberedning', '', NULL, 63, 16, '2020-10-30 23:29:29'),
(199, 'Kom och lek', 'Barngrupp 0-4 år', 189, 52, 16, '2020-11-01 11:37:38'),
(200, 'Äventyret', 'Barngrupp 1-12 år', 189, 52, 16, '2020-11-01 11:37:25'),
(201, 'Mötesplats', '', 189, 53, 16, '2020-11-01 11:37:32'),
(202, 'Arabiska ungdomsledare', '', 189, 53, 16, '2020-11-01 11:37:52'),
(203, 'Hembesök', '', 190, 54, 13, '2020-10-30 15:41:09'),
(204, 'Hembesök arabiska gruppen', '', 190, 54, 13, '2020-10-30 15:41:47'),
(205, 'Sjukhuskyrkans mötesvärdar', '', 190, 54, 13, '2020-10-30 15:42:23'),
(206, 'Gemenskapshelgskommitté', '', 185, 55, 16, '2020-10-30 15:51:36'),
(207, 'Gudstjänstplaneringsgruppen', '', 185, 55, 16, '2020-11-01 11:40:59'),
(208, 'Korskyrkan Gospel', '', 185, 56, 16, '2020-10-30 15:52:03'),
(209, 'Kör/Sångrupp', '', 185, 56, 16, '2020-10-30 15:52:33'),
(210, 'Information', '', 185, 55, 13, '2020-10-30 15:55:04'),
(211, 'Arabiska rådet', '', 185, 57, 16, '2020-11-01 11:41:12'),
(212, 'Tolkning Arabiska', '', 185, 58, 16, '2020-10-30 16:00:39'),
(213, 'Servicegrupp A', '', 191, 59, 16, '2020-10-30 16:12:09'),
(214, 'Servicegrupp B', '', 191, 59, 16, '2020-10-30 16:12:55'),
(215, 'Servicegrupp C', '', 191, 59, 16, '2020-10-30 16:12:45'),
(216, 'Servicegrupp D', '', 191, 59, 16, '2020-10-30 16:13:09'),
(217, 'Servicegrupp E', '', 191, 59, 16, '2020-10-30 16:13:28'),
(218, ' 1.3 Årsmötespresidium', '', 184, 61, 16, '2020-11-01 11:33:55'),
(219, ' 1.2 Presidium för året', '', 184, 61, 16, '2020-11-01 11:33:44'),
(220, 'Städledning vår och höst', '', 191, 56, 16, '2020-10-31 12:36:03'),
(222, 'Dop', '', 185, 67, 16, '2020-10-31 13:05:20'),
(223, 'Nattvard', '', 185, 67, 16, '2020-10-31 13:16:07'),
(224, 'Dopgrav', '', 222, 56, 16, '2020-10-31 13:08:30'),
(225, 'Dopdräkter', '', 222, 56, 16, '2020-10-31 13:10:04'),
(226, 'Musikinstrument', '', 185, 56, 16, '2020-10-31 16:34:56'),
(227, 'Minnes och högtdsgåvor', '', 185, 56, 16, '2020-10-31 16:35:36'),
(228, 'Själavård och andlig vägledning', '', 193, 56, 13, '2020-10-31 16:46:17'),
(229, 'Förebedjare', '', 193, 69, 13, '2020-10-31 16:48:01'),
(230, 'Arabiska förebedjare', '', 193, 69, 13, '2020-10-31 16:55:51'),
(231, 'Nattvardsledare', '', 223, 70, 13, '2020-10-31 17:02:59'),
(232, 'Nattvardstjänare', '', 223, 70, 13, '2020-10-31 17:04:53'),
(233, 'Nattvardsbord', '', 223, 56, 13, '2020-10-31 17:31:09'),
(234, 'Vasaparken möten', 'Fastighetsbolag', 196, 51, 13, '2020-10-31 17:47:30'),
(235, 'Fastighetsråd', '', 196, 56, 13, '2020-10-31 17:49:00'),
(236, 'Övriga roller', 'Ex. säkerhet, värme, ljud', 196, 71, 13, '2020-10-31 17:58:22'),
(237, 'Smågrupp 1', '', 194, 56, 16, '2020-11-01 10:08:06'),
(238, ' 1.4 Anställda', '', 184, 72, 16, '2020-11-01 11:33:04');

-- --------------------------------------------------------

--
-- Tabellstruktur `Org_UnitType`
--

CREATE TABLE `Org_UnitType` (
  `Id` int(10) UNSIGNED NOT NULL,
  `Name` varchar(50) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '',
  `Description` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `PosEnabled` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `SubUnitEnabled` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `Updater` int(11) DEFAULT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `Org_UnitType`
--

INSERT INTO `Org_UnitType` (`Id`, `Name`, `Description`, `PosEnabled`, `SubUnitEnabled`, `Updater`, `Updated`) VALUES
(49, 'Organisation', 'Moderorganisation', 1, 1, 16, '2020-10-27 12:48:57'),
(50, 'Verksamhetsområde', 'Ett verksamhetsområde omfattar en del av verksamheten med ett gemensamt tema', 0, 1, 16, '2020-10-30 23:22:27'),
(51, 'Styrelse', '', 1, 0, 16, '2020-10-27 12:49:14'),
(52, 'Barngrupp', '', 1, 0, 16, '2020-10-30 15:08:34'),
(53, 'Ungdomsgrupp', '', 1, 0, 16, '2020-10-30 15:20:21'),
(54, 'Diakonal funktion', '', 1, 0, 16, '2020-10-30 15:35:37'),
(55, 'Planeringsfunktion', '', 1, 0, 16, '2020-10-30 15:46:40'),
(56, 'Verksamhet', '', 1, 0, 16, '2020-10-30 15:49:08'),
(57, 'Råd', '', 1, 0, 16, '2020-10-30 15:56:12'),
(58, 'Tolkning', '', 1, 0, 16, '2020-10-30 15:56:36'),
(59, 'Servicegrupp', '', 1, 0, 16, '2020-10-30 16:06:34'),
(60, 'Representanter', '', 1, 0, 16, '2020-10-30 16:36:34'),
(61, 'Presidium', '', 1, 0, 16, '2020-10-30 16:46:44'),
(62, 'Servicegrupper', '', 1, 1, 16, '2020-10-30 23:20:28'),
(63, 'Valberedning', '', 1, 0, 16, '2020-10-30 23:25:31'),
(64, 'Revisorer', '', 1, 0, 16, '2020-10-30 23:26:26'),
(67, 'Samordnad verksamhetsgren', '', 1, 1, 16, '2020-10-31 13:05:55'),
(68, 'Ekonomifunktion', '', 1, 0, 16, '2020-10-31 14:43:29'),
(69, 'Förbön', '', 1, 0, 16, '2020-10-31 16:44:38'),
(70, 'Nattvard', '', 1, 0, 16, '2020-10-31 17:01:54'),
(71, 'Övriga förvaltningsroller', '', 1, 0, 16, '2020-10-31 17:53:25'),
(72, 'Anställda', '', 1, 0, 16, '2020-11-01 10:20:26');

-- --------------------------------------------------------

--
-- Tabellstruktur `Org_Version`
--

CREATE TABLE `Org_Version` (
  `id` int(10) UNSIGNED NOT NULL,
  `decision_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `information` varchar(512) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `writer` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------



--
-- Index för tabell `MemberState`
--
ALTER TABLE `MemberState`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name_UNIQUE` (`Name`);


--
-- Index för tabell `Org_Pos`
--
ALTER TABLE `Org_Pos`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `People_FK_idxfk` (`People_FK`),
  ADD KEY `PrevPeople_FK_idxfk` (`PrevPeople_FK`),
  ADD KEY `OrgRole_FK_idxfk_1` (`OrgRole_FK`),
  ADD KEY `OrgPosStatus_FK` (`OrgPosStatus_FK`),
  ADD KEY `OrgTree_FK` (`OrgTree_FK`),
  ADD KEY `org_pos_ibfk_8` (`OrgSuperPos_FK`);

--
-- Index för tabell `Org_PosStatus`
--
ALTER TABLE `Org_PosStatus`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Index för tabell `Org_Role`
--
ALTER TABLE `Org_Role`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Index för tabell `Org_Role-UnitType`
--
ALTER TABLE `Org_Role-UnitType`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `OrgRole_FK` (`OrgRole_FK`,`OrgUnitType_FK`),
  ADD KEY `OrgRole_FK_idxfk` (`OrgRole_FK`),
  ADD KEY `org_role_unittype_ibfk_2` (`OrgUnitType_FK`);

--
-- Index för tabell `Org_Tree`
--
ALTER TABLE `Org_Tree`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name` (`Name`),
  ADD KEY `ParentTreeNode_FK_idxfk` (`ParentTreeNode_FK`),
  ADD KEY `OrgUnitType_FK` (`OrgUnitType_FK`);

--
-- Index för tabell `Org_UnitType`
--
ALTER TABLE `Org_UnitType`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Index för tabell `Org_Version`
--
ALTER TABLE `Org_Version`
  ADD PRIMARY KEY (`id`);


--
-- AUTO_INCREMENT för dumpade tabeller
--


--
-- AUTO_INCREMENT för tabell `Org_Pos`
--
ALTER TABLE `Org_Pos`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=416;

--
-- AUTO_INCREMENT för tabell `Org_PosStatus`
--
ALTER TABLE `Org_PosStatus`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT för tabell `Org_Role`
--
ALTER TABLE `Org_Role`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT för tabell `Org_Role-UnitType`
--
ALTER TABLE `Org_Role-UnitType`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT för tabell `Org_Tree`
--
ALTER TABLE `Org_Tree`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- AUTO_INCREMENT för tabell `Org_UnitType`
--
ALTER TABLE `Org_UnitType`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT för tabell `Org_Version`
--
ALTER TABLE `Org_Version`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;


--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `Org_Pos`
--
ALTER TABLE `Org_Pos`
  ADD CONSTRAINT `org_pos_ibfk_4` FOREIGN KEY (`OrgRole_FK`) REFERENCES `Org_Role` (`Id`),
  ADD CONSTRAINT `org_pos_ibfk_6` FOREIGN KEY (`OrgPosStatus_FK`) REFERENCES `Org_PosStatus` (`Id`),
  ADD CONSTRAINT `org_pos_ibfk_7` FOREIGN KEY (`OrgTree_FK`) REFERENCES `Org_Tree` (`Id`),
  ADD CONSTRAINT `org_pos_ibfk_8` FOREIGN KEY (`OrgSuperPos_FK`) REFERENCES `Org_Pos` (`Id`);

--
-- Restriktioner för tabell `Org_Role-UnitType`
--
ALTER TABLE `Org_Role-UnitType`
  ADD CONSTRAINT `org_role-unittype_ibfk_1` FOREIGN KEY (`OrgRole_FK`) REFERENCES `Org_Role` (`Id`),
  ADD CONSTRAINT `org_role_unittype_ibfk_2` FOREIGN KEY (`OrgUnitType_FK`) REFERENCES `Org_UnitType` (`Id`);

--
-- Restriktioner för tabell `Org_Tree`
--
ALTER TABLE `Org_Tree`
  ADD CONSTRAINT `org_tree_ibfk_1` FOREIGN KEY (`ParentTreeNode_FK`) REFERENCES `Org_Tree` (`Id`),
  ADD CONSTRAINT `org_tree_ibfk_2` FOREIGN KEY (`OrgUnitType_FK`) REFERENCES `Org_UnitType` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
