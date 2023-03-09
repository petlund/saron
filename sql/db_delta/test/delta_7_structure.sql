ALTER TABLE Changes 
ADD COLUMN ChangeType VARCHAR(45) NOT NULL AFTER Id,
ADD COLUMN User VARCHAR(45) NOT NULL AFTER ChangeType,
ADD COLUMN BusinessKey VARCHAR(1024) NOT NULL AFTER ChangeType,
CHANGE COLUMN Id Id INT NOT NULL AUTO_INCREMENT ;

ALTER TABLE Changes 
ADD COLUMN `BusinessKeyEncrypt` VARBINARY(2048) NOT NULL AFTER `BusinessKey`,
ADD COLUMN `DescriptionEncrypt` VARBINARY(8192) NOT NULL AFTER `Description`;


ALTER TABLE News 
CHANGE COLUMN id Id INT UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE Org_Version 
CHANGE COLUMN id Id INT UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `Changes` 
DROP COLUMN `Description`,
DROP COLUMN `BusinessKey`;

ALTER TABLE Org_Version 
DROP COLUMN `writer`;


ALTER TABLE People CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE People MODIFY `PreviousCongregation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;
ALTER TABLE People MODIFY `DateOfAnonymization` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;
ALTER TABLE People MODIFY `NextCongregation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;
ALTER TABLE People MODIFY `DateOfFriendshipStart` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;
ALTER TABLE People MODIFY `CongregationOfBaptism` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;
ALTER TABLE People MODIFY `UpdaterName` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;
ALTER TABLE People MODIFY `InserterName` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;

ALTER TABLE Homes CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE Homes MODIFY City varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;
ALTER TABLE Homes MODIFY Zip varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;
ALTER TABLE Homes MODIFY Country varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;
ALTER TABLE Homes MODIFY UpdaterName varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;
ALTER TABLE Homes MODIFY InserterName varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;

ALTER TABLE News CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE News MODIFY `information` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;
ALTER TABLE News MODIFY `writer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci DEFAULT NULL;

ALTER TABLE Statistics CHANGE COLUMN `year` `year` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE Statistics  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- add create views


DROP VIEW IF EXISTS view_news;

CREATE VIEW `view_news` AS
    SELECT 
        Id as Id,
        news_date as news_date,
        severity as severity,
        information as information,
        (CASE 
            WHEN severity= 0 THEN 'Meddelande'
            WHEN severity= 1 THEN 'Viktigt meddelande'
            WHEN severity= 2 THEN 'Varning'
            ELSE '-'
            END
        ) AS severityText,
        writer as writer        
    FROM
        News;


DROP VIEW IF EXISTS view_people_memberstate;

DROP VIEW IF EXISTS view_people;

CREATE VIEW view_people AS
    SELECT 
        People.Id AS Id,
        People.FirstNameEncrypt AS FirstNameEncrypt,
        People.LastNameEncrypt AS LastNameEncrypt,
        People.DateOfBirth AS DateOfBirth,
        IF(DateOfDeath is null, extract(YEAR FROM NOW()) - extract(YEAR FROM DateOfBirth), extract(YEAR FROM DateOfDeath) - extract(YEAR FROM DateOfBirth)) as Age,
        People.DateOfDeath AS DateOfDeath,
        People.PreviousCongregation AS PreviousCongregation,
        People.MembershipNo AS MembershipNo,
        People.VisibleInCalendar AS VisibleInCalendar,
        (CASE 
            WHEN People.VisibleInCalendar= 1 THEN 'Nej'
            WHEN People.VisibleInCalendar= 2 THEN 'Ja'
            ELSE '-'
            END) AS VisibleInCalendarText,
        People.DateOfMembershipStart AS DateOfMembershipStart,
        People.DateOfMembershipEnd AS DateOfMembershipEnd,
        People.DateOfAnonymization AS DateOfAnonymization,
        People.NextCongregation AS NextCongregation,
        People.DateOfBaptism AS DateOfBaptism,
        People.DateOfFriendshipStart AS DateOfFriendshipStart,
        People.BaptisterEncrypt AS BaptisterEncrypt,
        People.CongregationOfBaptism AS CongregationOfBaptism,
        People.CongregationOfBaptismThis AS CongregationOfBaptismThis,
        People.Gender AS Gender,
        (CASE 
            WHEN People.Gender= 1 THEN 'Nej'
            WHEN People.Gender= 2 THEN 'Ja'
            ELSE '-'
            END) AS GenderText,
        People.EmailEncrypt AS EmailEncrypt,
        People.MobileEncrypt AS MobileEncrypt,
        People.KeyToChurch AS KeyToChurch,
        (CASE 
            WHEN People.KeyToChurch= 1 THEN 'Nej'
            WHEN People.KeyToChurch= 2 THEN 'Ja'
            ELSE '-'
            END) AS KeyToChurchText,
        People.KeyToExp AS KeyToExp,
        (CASE 
            WHEN People.KeyToExp= 1 THEN 'Nej'
            WHEN People.KeyToExp= 2 THEN 'Ja'
            ELSE '-'
            END) AS KeyToExpText,
        People.CommentEncrypt AS CommentEncrypt,
        People.CommentKeyEncrypt AS CommentKeyEncrypt,
        People.HomeId AS HomeId,
        People.UpdaterName AS UpdaterName,
        People.Updater AS Updater,
        People.Updated AS Updated,
        People.Inserter AS Inserter,
        People.InserterName AS InserterName,
        People.Inserted AS Inserted,
        (CASE
            WHEN (People.DateOfAnonymization IS NOT NULL) THEN 4
            WHEN (People.DateOfDeath IS NOT NULL) THEN 5
            WHEN
                ((People.DateOfFriendshipStart > (NOW() - INTERVAL 400 DAY))
                    AND (((People.DateOfMembershipStart IS NULL)
                    AND (People.DateOfMembershipEnd IS NULL))
                    OR ((People.DateOfMembershipStart IS NOT NULL)
                    AND (People.DateOfMembershipEnd IS NOT NULL))))
            THEN
                7
            WHEN
                ((People.DateOfMembershipStart IS NOT NULL)
                    AND (People.DateOfMembershipEnd IS NULL))
            THEN
                2
            WHEN
                ((People.DateOfMembershipStart IS NOT NULL)
                    AND (People.DateOfMembershipEnd IS NOT NULL))
            THEN
                8
            WHEN (People.DateOfBaptism IS NOT NULL) THEN 3
            WHEN
                ((People.DateOfMembershipStart IS NULL)
                    AND (People.DateOfMembershipEnd IS NULL)
                    AND (People.DateOfBaptism IS NULL))
            THEN
                1
            ELSE 1
        END) AS MemberStateId,
        (SELECT 
                MemberState.Name
            FROM
                MemberState
            WHERE
                MemberState.Id = MemberStateId) AS MemberStateName,

        Homes.FamilyNameEncrypt AS FamilyNameEncrypt,
        Homes.PhoneEncrypt AS PhoneEncrypt,
        Homes.AddressEncrypt as AddressEncrypt,
        Homes.CoEncrypt as CoEncrypt,
        Homes.Zip,
        Homes.City,
        Homes.Country,
        Homes.Letter,
        (CASE 
            WHEN Homes.Letter = 2 THEN 'Ja'
            ELSE ''
            END) AS LetterText

        FROM
        People
        LEFT OUTER JOIN Homes ON Homes.Id = People.HomeId;


DROP VIEW IF EXISTS view_org_role_unittype;

CREATE VIEW `view_org_role_unittype` AS
    SELECT 
        `RUT`.`Id` AS `Id`,
        `Role`.`Name` AS `RoleName`,
        `Type`.`Name` AS `UnitTypeName`,
        `RUT`.`SortOrder` AS `SortOrder`,
        `RUT`.`Updated` AS `Updated`,
        `RUT`.`UpdaterName` AS `UpdaterName`
    FROM
        `Org_Role-UnitType` `RUT`
        JOIN `Org_Role` `Role` ON `RUT`.`OrgRole_FK` = `Role`.`Id`
        JOIN `Org_UnitType` `Type` ON `RUT`.`OrgUnitType_FK` = `Type`.`Id`;


DROP VIEW IF EXISTS view_organization_tree;

CREATE VIEW view_organization_tree AS
    with recursive Org_Tree_Root as (
        select 
                Org_Tree.Id AS Id,
        	Org_Tree.Prefix, 
        	Org_Tree.Name, 
        	Org_Tree.OrgUnitType_FK, 
                Org_Tree.ParentTreeNode_FK, 
                Org_Tree.Description, 
                Org_Tree.UpdaterName, 
                Org_Tree.Updated,
                concat(if((Org_Tree.Prefix is not null),
                concat(Org_Tree.Prefix,' '),''),Org_Tree.Name) AS LongName,
                cast(concat(if((Org_Tree.Prefix is not null), concat(Org_Tree.Prefix,' '),''),Org_Tree.Name) as char(5000) charset utf8mb4) AS Org_Path,
                0 AS Rel_Depth 
        from 
                Org_Tree 
        where 
                Org_Tree.ParentTreeNode_FK is null 
        union all 
        select 
                Org_Tree.Id AS Id,
        	Org_Tree.Prefix, 
        	Org_Tree.Name, 
        	Org_Tree.OrgUnitType_FK, 
                Org_Tree.ParentTreeNode_FK, 
                Org_Tree.Description, 
                Org_Tree.UpdaterName, 
                Org_Tree.Updated,
                concat(if((Org_Tree.Prefix is not null),
                concat(Org_Tree.Prefix,' '),''),Org_Tree.Name) AS LongName,
                concat(Org_Tree_Root.Org_Path,' / ', concat(if((Org_Tree.Prefix is not null), concat(Org_Tree.Prefix,' '),''),Org_Tree.Name)) AS Org_Path,
                (Org_Tree_Root.Rel_Depth + 1) AS Rel_Depth 
        from 
                Org_Tree_Root 
                inner join Org_Tree 
        where 
                Org_Tree.ParentTreeNode_FK = Org_Tree_Root.Id
    ) 
    Select * from Org_Tree_Root;


drop view if exists  view_organization_pos;

CREATE VIEW `view_organization_pos` AS
    SELECT 
        `Pos`.`Id` AS `Id`,
        `Pos`.`Comment` AS `Comment`,
        CONCAT(`Unit`.`Org_Path`, ': ', `Role`.`Name`) AS `PosKeyValue`,
        `Pcur`.`LastNameEncrypt` AS `LastNameEncrypt`,
        `Pcur`.`FirstNameEncrypt` AS `FirstNameEncrypt`,
        `Pcur`.`DateOfBirth` AS `DateOfBirthHidden`,
        `Pcur`.`MemberStateName` AS `MemberStateNameHidden`,
        `Func`.`Name` AS `FunctionName`,
        `SuperRole`.`Name` AS `SuperRoleName`,
        `PosStatus`.`Name` AS `OrgPosStatusName`,
        `Unit`.`LongName` AS `UnitName`,
        `Role`.`Name` AS `RoleName`
    FROM
        `Org_Pos` `Pos`
        JOIN `Org_Role` `Role` ON `Pos`.`OrgRole_FK` = `Role`.`Id`
        JOIN `Org_PosStatus` `PosStatus` ON `PosStatus`.`Id` = `Pos`.`OrgPosStatus_FK`
        JOIN view_organization_tree as Unit ON `Pos`.`OrgTree_FK` = `Unit`.`Id`
        LEFT JOIN `view_people` `Pcur` ON `Pos`.`People_FK` = `Pcur`.`Id`
        LEFT JOIN `Org_Tree` `Func` ON `Pos`.`Function_FK` = `Func`.`Id`
        LEFT JOIN `Org_Pos` `SuperPos` ON `Pos`.`OrgSuperPos_FK` = `SuperPos`.`Id`
        LEFT JOIN `Org_Role` `SuperRole` ON `SuperRole`.`Id` = `SuperPos`.`OrgRole_FK`;




