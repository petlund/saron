CREATE VIEW `view_org_role_unittype` AS
    SELECT 
        `RUT`.`Id` AS `Id`,
        `Role`.`Name` AS `RoleName`,
        `Type`.`Name` AS `UnitTypeName`,
        `RUT`.`SortOrder` AS `SortOrder`,
        `RUT`.`Updated` AS `Updated`,
        `RUT`.`UpdaterName` AS `UpdaterName`
    FROM
        `org_role-unittype` `RUT`
        JOIN `org_role` `Role` ON `RUT`.`OrgRole_FK` = `Role`.`Id`
        JOIN `org_unittype` `Type` ON `RUT`.`OrgUnitType_FK` = `Type`.`Id`