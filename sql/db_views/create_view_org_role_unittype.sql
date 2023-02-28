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