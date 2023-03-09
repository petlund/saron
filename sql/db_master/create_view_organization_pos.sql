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
        LEFT JOIN `Org_Role` `SuperRole` ON `SuperRole`.`Id` = `SuperPos`.`OrgRole_FK`
        ;



