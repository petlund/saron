ALTER TABLE `saron2`.`emberState` 
ADD COLUMN `UpdaterName` VARCHAR(45) NOT NULL DEFAULT '-' AFTER `Updated`;

ALTER TABLE `saron2`.`Org_Pos` 
ADD COLUMN `UpdaterName` VARCHAR(45) NOT NULL DEFAULT '-' AFTER `Updated`;

ALTER TABLE `saron2`.`Org_PosStatus` 
ADD COLUMN `UpdaterName` VARCHAR(45) NOT NULL DEFAULT '-' AFTER `Updated`;

ALTER TABLE `saron2`.`Org_Role` 
ADD COLUMN `UpdaterName` VARCHAR(45) NOT NULL DEFAULT '-' AFTER `Updated`;

ALTER TABLE `saron2`.`Org_Role-UnitType` 
ADD COLUMN `UpdaterName` VARCHAR(45) NOT NULL DEFAULT '-' AFTER `Updated`;

ALTER TABLE `saron2`.`Org_Tree` 
ADD COLUMN `UpdaterName` VARCHAR(45) NOT NULL DEFAULT '-' AFTER `Updated`;

ALTER TABLE `saron2`.`Org_UnitType` 
ADD COLUMN `UpdaterName` VARCHAR(45) NOT NULL DEFAULT '-' AFTER `Updated`;

ALTER TABLE `saron2`.`Org_Version` 
ADD COLUMN `UpdaterName` VARCHAR(45) NOT NULL DEFAULT '-' AFTER `writer`;
