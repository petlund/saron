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


-- create view view_org_role_unittype <== file
-- CREATE VIEW view_organization_tree AS <== file
-- CREATE VIEW view_organization_pos  <== file

