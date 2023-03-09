/* 
 * Author:  peter
 * Created: 7 mars 2023
 */

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Värd: s579.loopia.se
-- Tid vid skapande: 07 mars 2023 kl 21:08
-- Serverversion: 10.3.36-MariaDB-log
-- PHP-version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Tabellstruktur `Changes`
--

CREATE TABLE `Changes` (
  `Id` int(11) NOT NULL,
  `ChangeType` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL,
  `BusinessKeyEncrypt` varbinary(2048) NOT NULL,
  `User` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL,
  `DescriptionEncrypt` varbinary(8192) NOT NULL,
  `Inserter` int(11) NOT NULL,
  `Inserted` datetime NOT NULL DEFAULT current_timestamp(),
  `InserterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `Homes`
--

CREATE TABLE `Homes` (
  `Id` bigint(20) UNSIGNED NOT NULL,
  `FamilyNameEncrypt` varbinary(100) DEFAULT '',
  `AddressEncrypt` varbinary(100) DEFAULT '',
  `CoEncrypt` varbinary(100) DEFAULT '',
  `City` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Zip` varchar(20) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Country` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `PhoneEncrypt` varbinary(100) DEFAULT '',
  `Letter` tinyint(4) NOT NULL DEFAULT 0,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Updater` int(11) DEFAULT NULL,
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Inserted` datetime DEFAULT current_timestamp(),
  `Inserter` int(11) DEFAULT NULL,
  `InserterName` varchar(45) COLLATE utf8mb4_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `MemberState`
--

CREATE TABLE `MemberState` (
  `Id` int(11) NOT NULL,
  `Name` varchar(50) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT 'Status',
  `Description` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `DateOfFriendshipStart` tinyint(4) NOT NULL DEFAULT 0,
  `DateOfBaptism` tinyint(4) NOT NULL DEFAULT 0,
  `DateOfMembershipStart` tinyint(4) NOT NULL DEFAULT 0,
  `DateOfMembershipEnd` tinyint(4) NOT NULL DEFAULT 0,
  `HasEngagement` tinyint(4) NOT NULL DEFAULT 0,
  `DateOfAnonymization` tinyint(4) NOT NULL DEFAULT 0,
  `DateOfDeath` tinyint(4) NOT NULL DEFAULT 0,
  `Updater` int(11) NOT NULL DEFAULT 0,
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-',
  `Updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Inserted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `MemberState`
--

INSERT INTO `MemberState` (`Id`, `Name`, `Description`, `DateOfFriendshipStart`, `DateOfBaptism`, `DateOfMembershipStart`, `DateOfMembershipEnd`, `HasEngagement`, `DateOfAnonymization`, `DateOfDeath`, `Updater`, `UpdaterName`, `Updated`, `Inserted`) VALUES
(0, '-', 'Sätts för en person som inte har tillräckliga uppgifter för att sätta en status', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0),
(1, 'Registerförd', 'Sätts för en person som endast är registrerad med namn och födelsedatum.', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0),
(2, 'Medlem', 'Sätts för en person som har ett datum för medlemskap start men inte för medlemskap slut', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0),
(3, 'Endast dopuppgift', 'Sätts för en \"Registerförd\" person som också har registrerade dopuppifter', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0),
(4, 'Anonymiserad', 'Sätts för en person som har ett datum i fältet anonymiserad', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0),
(5, 'Avliden', 'Sätts för en person som har ett datum i fältet avliden', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0),
(6, 'Medhjälpare', 'Används inte längre. Ersatt av \"Vänkontakt\"', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0),
(7, 'Vänkontakt', 'Sätts för en icke \"Medlem\" som har ett datum satt för vänkontakt. Årlig förnyelse behövs. (Sker med automatik om personen har ett eller flera uppdrag)', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0),
(8, 'Avslutat medlemskap', 'Sätts för person som både har datum för medlemskap start och slut', 0, 0, 0, 0, 0, 0, 0, 0, '-', '2022-11-14 20:18:13', 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `News`
--

CREATE TABLE `News` (
  `Id` int(10) UNSIGNED NOT NULL,
  `news_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `information` varchar(512) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `writer` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `severity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `Org_Pos`
--

CREATE TABLE `Org_Pos` (
  `Id` int(10) UNSIGNED NOT NULL,
  `People_FK` int(11) DEFAULT NULL,
  `PrevPeople_FK` int(11) DEFAULT NULL,
  `Function_FK` int(11) DEFAULT NULL,
  `PrevFunction_FK` int(11) DEFAULT NULL,
  `OrgPosStatus_FK` int(10) UNSIGNED NOT NULL,
  `PrevOrgPosStatus_FK` int(11) DEFAULT NULL,
  `OrgSuperPos_FK` int(10) UNSIGNED DEFAULT NULL,
  `PrevOrgSuperPos_FK` int(10) UNSIGNED DEFAULT NULL,
  `OrgRole_FK` int(10) UNSIGNED NOT NULL,
  `OrgTree_FK` int(10) UNSIGNED NOT NULL,
  `Updater` int(11) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-',
  `Comment` varchar(45) COLLATE utf8mb4_swedish_ci DEFAULT '50'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;


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
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `Org_PosStatus`
--

INSERT INTO `Org_PosStatus` (`Id`, `Name`, `Description`, `SortOrder`, `Updater`, `Updated`, `UpdaterName`) VALUES
(1, 'Avstämd', 'Sätts på positioner där förslaget är avstämt med vederbörande', 20, 16, '2020-12-29 16:22:07', 'Peter Lundin'),
(2, 'Förslag', 'Sätts på en position där ett namnförslag finns.', 10, 1, '2020-10-03 17:03:36', '-'),
(4, 'Vakant', 'Sätts på postioner där det saknas en person. Eventuell person kopplad till uppdraget kopplas från uppdraget', 30, 1, '2020-09-14 10:17:20', '-'),
(5, 'Tillsätts ej', 'Sätts på positioner som inte ska tillsättas vid detta tillfälle. Eventuell person kopplad till uppdraget kopplas från uppdraget', 40, 1, '2020-09-14 10:17:12', '-');

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
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;


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
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;


-- --------------------------------------------------------

--
-- Tabellstruktur `Org_Tree`
--

CREATE TABLE `Org_Tree` (
  `Id` int(10) UNSIGNED NOT NULL,
  `Prefix` varchar(10) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Name` varchar(100) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Description` varchar(255) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `ParentTreeNode_FK` int(10) UNSIGNED DEFAULT NULL,
  `OrgUnitType_FK` int(10) UNSIGNED DEFAULT NULL,
  `Updater` int(11) DEFAULT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;


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
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;


-- --------------------------------------------------------

--
-- Tabellstruktur `Org_Version`
--

CREATE TABLE `Org_Version` (
  `Id` int(10) UNSIGNED NOT NULL,
  `decision_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `information` varchar(512) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-',
  `Updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;


-- --------------------------------------------------------

--
-- Tabellstruktur `People`
--

CREATE TABLE `People` (
  `Id` bigint(20) UNSIGNED NOT NULL,
  `FirstNameEncrypt` varbinary(100) NOT NULL DEFAULT '',
  `LastNameEncrypt` varbinary(100) NOT NULL DEFAULT '',
  `DateOfBirth` date NOT NULL,
  `DateOfDeath` date DEFAULT NULL,
  `PreviousCongregation` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `MembershipNo` int(8) DEFAULT 0,
  `VisibleInCalendar` tinyint(1) NOT NULL DEFAULT 2,
  `DateOfMembershipStart` date DEFAULT NULL,
  `DateOfMembershipEnd` date DEFAULT NULL,
  `DateOfAnonymization` varchar(45) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `NextCongregation` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `DateOfBaptism` date DEFAULT NULL,
  `DateOfFriendshipStart` varchar(45) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `BaptisterEncrypt` varbinary(100) DEFAULT NULL,
  `CongregationOfBaptism` varchar(50) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `CongregationOfBaptismThis` tinyint(1) NOT NULL DEFAULT 0,
  `Gender` tinyint(1) NOT NULL DEFAULT 0,
  `EmailEncrypt` varbinary(100) DEFAULT '',
  `MobileEncrypt` varbinary(100) DEFAULT '',
  `KeyToChurch` tinyint(1) DEFAULT 0,
  `KeyToExp` tinyint(1) DEFAULT 0,
  `CommentEncrypt` varbinary(350) DEFAULT '',
  `CommentKeyEncrypt` varbinary(350) DEFAULT NULL,
  `HomeId` int(10) DEFAULT NULL,
  `Updater` int(10) NOT NULL DEFAULT 1,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `UpdaterName` varchar(45) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Inserter` int(10) NOT NULL DEFAULT 1,
  `Inserted` timestamp NOT NULL DEFAULT current_timestamp(),
  `InserterName` varchar(45) COLLATE utf8mb4_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `SaronUser`
--

CREATE TABLE `SaronUser` (
  `Id` int(11) NOT NULL,
  `AccessTicket` varchar(100) COLLATE utf8mb4_swedish_ci NOT NULL,
  `Editor` int(11) NOT NULL,
  `Org_Editor` int(11) NOT NULL,
  `WP_ID` int(11) NOT NULL,
  `Time_Stamp` datetime NOT NULL DEFAULT current_timestamp(),
  `UserDisplayName` varchar(45) COLLATE utf8mb4_swedish_ci NOT NULL,
  `Last_Activity` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `Statistics`
--

CREATE TABLE `Statistics` (
  `year` date NOT NULL DEFAULT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `number_of_members` int(11) NOT NULL,
  `number_of_new_members` int(11) NOT NULL,
  `number_of_finnished_members` int(11) NOT NULL,
  `number_of_dead` int(11) NOT NULL,
  `number_of_baptist_people` int(11) NOT NULL,
  `average_age` double NOT NULL,
  `average_membership_time` double NOT NULL,
  `diff` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Index för tabell `Changes`
--
ALTER TABLE `Changes`
  ADD PRIMARY KEY (`Id`);

--
-- Index för tabell `Homes`
--
ALTER TABLE `Homes`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Id` (`Id`);

--
-- Index för tabell `MemberState`
--
ALTER TABLE `MemberState`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Name_UNIQUE` (`Name`);

--
-- Index för tabell `News`
--
ALTER TABLE `News`
  ADD PRIMARY KEY (`Id`);

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
  ADD PRIMARY KEY (`Id`);

--
-- Index för tabell `People`
--
ALTER TABLE `People`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Id` (`Id`),
  ADD UNIQUE KEY `MemberShipNo` (`MembershipNo`);

--
-- Index för tabell `SaronUser`
--
ALTER TABLE `SaronUser`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Id_UNIQUE` (`AccessTicket`);

--
-- Index för tabell `Statistics`
--
ALTER TABLE `Statistics`
  ADD PRIMARY KEY (`year`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `Changes`
--
ALTER TABLE `Changes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `Homes`
--
ALTER TABLE `Homes`
  MODIFY `Id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `News`
--
ALTER TABLE `News`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `Org_Pos`
--
ALTER TABLE `Org_Pos`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `Org_PosStatus`
--
ALTER TABLE `Org_PosStatus`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `Org_Role`
--
ALTER TABLE `Org_Role`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `Org_Role-UnitType`
--
ALTER TABLE `Org_Role-UnitType`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `Org_Tree`
--
ALTER TABLE `Org_Tree`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `Org_UnitType`
--
ALTER TABLE `Org_UnitType`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `Org_Version`
--
ALTER TABLE `Org_Version`
  MODIFY `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `People`
--
ALTER TABLE `People`
  MODIFY `Id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT för tabell `SaronUser`
--
ALTER TABLE `SaronUser`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

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



