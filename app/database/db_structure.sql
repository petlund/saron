CREATE TABLE `Homes` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `FamilyNameEncrypt` varbinary(100) DEFAULT '',
  `AddressEncrypt` varbinary(100) DEFAULT '',
  `CoEncrypt` varbinary(100) DEFAULT '',
  `City` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `Zip` varchar(20) COLLATE utf8_swedish_ci DEFAULT NULL,
  `Country` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `PhoneEncrypt` varbinary(100) DEFAULT '',
  `Letter` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=432 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `People` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `FirstNameEncrypt` varbinary(100) NOT NULL DEFAULT '',
  `LastNameEncrypt` varbinary(100) NOT NULL DEFAULT '',
  `DateOfBirth` date NOT NULL,
  `DateOfDeath` date DEFAULT NULL,
  `PreviousCongregation` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `MembershipNo` int(8) DEFAULT '0',
  `VisibleInCalendar` tinyint(1) NOT NULL DEFAULT '2',
  `DateOfMembershipStart` date DEFAULT NULL,
  `DateOfMembershipEnd` date DEFAULT NULL,
  `NextCongregation` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `DateOfBaptism` date DEFAULT NULL,
  `BaptisterEncrypt` varbinary(100) DEFAULT NULL,
  `CongregationOfBaptism` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `CongregationOfBaptismThis` tinyint(1) NOT NULL DEFAULT '0',
  `Gender` tinyint(1) NOT NULL DEFAULT '0',
  `EmailEncrypt` varbinary(100) DEFAULT '',
  `MobileEncrypt` varbinary(100) DEFAULT '',
  `KeyToChurch` tinyint(1) DEFAULT '0',
  `KeyToExp` tinyint(1) DEFAULT '0',
  `CommentEncrypt` varbinary(350) DEFAULT '',
  `HomeId` int(10) DEFAULT NULL,
  `Updater` int(10) NOT NULL DEFAULT '1',
  `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Inserter` int(10) NOT NULL DEFAULT '1',
  `Inserted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CommentKeyEncrypt` varbinary(350) DEFAULT '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Id` (`Id`),
  UNIQUE KEY `MemberShipNo` (`MembershipNo`)
) ENGINE=InnoDB AUTO_INCREMENT=3006 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `Statistics` (
  `year` date NOT NULL DEFAULT '0000-00-00',
  `number_of_members` int(11) NOT NULL,
  `number_of_new_members` int(11) NOT NULL,
  `number_of_finnished_members` int(11) NOT NULL,
  `number_of_dead` int(11) NOT NULL,
  `number_of_baptist_people` int(11) NOT NULL,
  `average_age` double NOT NULL,
  `average_membership_time` double NOT NULL,
  `diff` int(11) NOT NULL,
  PRIMARY KEY (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


CREATE TABLE `News` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `information` varchar(512) COLLATE utf8_swedish_ci DEFAULT NULL,
  `writer` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;