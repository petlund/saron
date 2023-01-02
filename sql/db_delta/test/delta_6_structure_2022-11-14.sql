
ALTER TABLE `Org_Pos` 
ADD COLUMN `PrevOrgSuperPos_FK` INT UNSIGNED NULL DEFAULT NULL AFTER `OrgSuperPos_FK`;

CREATE TABLE `Changes` (
  `Id` int NOT NULL,
  `Description` varchar(4096) NOT NULL,
  `Inserter` int NOT NULL,
  `Inserted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `InserterName` varchar(45) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;


ALTER TABLE `People`  
ADD COLUMN `DateOfFriendshipStart` VARCHAR(45) NULL AFTER `DateOfBaptism`,
ADD COLUMN `DateOfAnonymization` VARCHAR(45) NULL AFTER `DateOfMembershipEnd`,
ADD COLUMN `InserterName` VARCHAR(45) NULL AFTER `Inserted`,
ADD COLUMN `UpdaterName` VARCHAR(45) NULL AFTER `Updated`;

ALTER TABLE `Homes` 
ADD COLUMN `Updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `Letter`,
ADD COLUMN `Updater` INT NULL AFTER `Updated`,
ADD COLUMN `UpdaterName` VARCHAR(45) NULL AFTER `Updater`,
ADD COLUMN `Inserted` DATETIME NULL DEFAULT CURRENT_TIMESTAMP AFTER `UpdaterName`,
ADD COLUMN `Inserter` INT NULL AFTER `Inserted`,
ADD COLUMN `InserterName` VARCHAR(45) NULL AFTER `Inserter`;


CREATE 
VIEW `view_people_memberstate` AS
    SELECT 
        `People`.`Id` AS `Id`,
        `People`.`FirstNameEncrypt` AS `FirstNameEncrypt`,
        `People`.`LastNameEncrypt` AS `LastNameEncrypt`,
        `People`.`DateOfBirth` AS `DateOfBirth`,
        `People`.`DateOfDeath` AS `DateOfDeath`,
        `People`.`PreviousCongregation` AS `PreviousCongregation`,
        `People`.`MembershipNo` AS `MembershipNo`,
        `People`.`VisibleInCalendar` AS `VisibleInCalendar`,
        `People`.`DateOfMembershipStart` AS `DateOfMembershipStart`,
        `People`.`DateOfMembershipEnd` AS `DateOfMembershipEnd`,
        `People`.`DateOfAnonymization` AS `DateOfAnonymization`,
        `People`.`NextCongregation` AS `NextCongregation`,
        `People`.`DateOfBaptism` AS `DateOfBaptism`,
        `People`.`DateOfFriendshipStart` AS `DateOfFriendshipStart`,
        `People`.`BaptisterEncrypt` AS `BaptisterEncrypt`,
        `People`.`CongregationOfBaptism` AS `CongregationOfBaptism`,
        `People`.`CongregationOfBaptismThis` AS `CongregationOfBaptismThis`,
        `People`.`Gender` AS `Gender`,
        `People`.`EmailEncrypt` AS `EmailEncrypt`,
        `People`.`MobileEncrypt` AS `MobileEncrypt`,
        `People`.`KeyToChurch` AS `KeyToChurch`,
        `People`.`KeyToExp` AS `KeyToExp`,
        `People`.`CommentEncrypt` AS `CommentEncrypt`,
        `People`.`CommentKeyEncrypt` AS `CommentKeyEncrypt`,
        `People`.`HomeId` AS `HomeId`,
        `People`.`UpdaterName` AS `UpdaterName`,
        `People`.`Updater` AS `Updater`,
        `People`.`Updated` AS `Updated`,
        `People`.`Inserter` AS `Inserter`,
        `People`.`InserterName` AS `InserterName`,
        `People`.`Inserted` AS `Inserted`,
        (CASE
            WHEN (`People`.`DateOfAnonymization` IS NOT NULL) THEN 4
            WHEN (`People`.`DateOfDeath` IS NOT NULL) THEN 5
            WHEN
                ((`People`.`DateOfFriendshipStart` > (NOW() - INTERVAL 400 DAY))
                    AND (((`People`.`DateOfMembershipStart` IS NULL)
                    AND (`People`.`DateOfMembershipEnd` IS NULL))
                    OR ((`People`.`DateOfMembershipStart` IS NOT NULL)
                    AND (`People`.`DateOfMembershipEnd` IS NOT NULL))))
            THEN
                7
            WHEN
                ((`People`.`DateOfMembershipStart` IS NOT NULL)
                    AND (`People`.`DateOfMembershipEnd` IS NULL))
            THEN
                2
            WHEN
                ((`People`.`DateOfMembershipStart` IS NOT NULL)
                    AND (`People`.`DateOfMembershipEnd` IS NOT NULL))
            THEN
                8
            WHEN (`People`.`DateOfBaptism` IS NOT NULL) THEN 3
            WHEN
                ((`People`.`DateOfMembershipStart` IS NULL)
                    AND (`People`.`DateOfMembershipEnd` IS NULL)
                    AND (`People`.`DateOfBaptism` IS NULL))
            THEN
                1
            ELSE 1
        END) AS `MemberStateId`,
        (SELECT 
                `MemberState`.`Name`
            FROM
                `MemberState`
            WHERE
                (`MemberState`.`Id` = `MemberStateId`)) AS `MemberStateName`
    FROM
        `People`;

Drop table `MemberState`;
CREATE TABLE `MemberState` (
  `Id` int NOT NULL,
  `Name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT 'Status',
  `Description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `DateOfFriendshipStart` tinyint NOT NULL DEFAULT '0',
  `DateOfBaptism` tinyint NOT NULL DEFAULT '0',
  `DateOfMembershipStart` tinyint NOT NULL DEFAULT '0',
  `DateOfMembershipEnd` tinyint NOT NULL DEFAULT '0',
  `HasEngagement` tinyint NOT NULL DEFAULT '0',
  `DateOfAnonymization` tinyint NOT NULL DEFAULT '0',
  `DateOfDeath` tinyint NOT NULL DEFAULT '0',
  `Updater` int NOT NULL DEFAULT '0',
  `UpdaterName` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT '-',
  `Updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Inserted` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name_UNIQUE` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;
