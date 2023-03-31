DROP VIEW IF EXISTS view_unittype; 

CREATE VIEW `view_unittype` AS
    SELECT 
        `org_unittype`.`Id` AS `Id`,
        `org_unittype`.`Name` AS `Name`,
        (CASE
            WHEN (`org_unittype`.`PosEnabled` = 2) THEN 'Ja'
            ELSE 'Nej'
        END) AS `PosEnabledText`,
        (CASE
            WHEN (`org_unittype`.`SubUnitEnabled` = 2) THEN 'Ja'
            ELSE 'Nej'
        END) AS `SubUnitEnabledText`,
        `org_unittype`.PosEnabled,
        `org_unittype`.SubUnitEnabled,
        `org_unittype`.`Description` AS `Description`,
        `org_unittype`.`Updater` AS `Updater`,
        `org_unittype`.`Updated` AS `Updated`,
        `org_unittype`.`UpdaterName` AS `UpdaterName`
    FROM
        `Org_UnitType`;