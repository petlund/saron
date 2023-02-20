DROP VIEW IF EXISTS view_role;

CREATE VIEW `view_role` AS
    SELECT 
        Id as Id,
        Name as Name,
        (CASE 
            WHEN RoleType= 0 THEN 'Verksamhetsroll'
            ELSE 'Organisationsroll'
            END
        ) AS RoleTypeName,
        Description as Description,
        Updater as Updater,
        Updated as Updated,
        UpdaterName as UpdaterName
    FROM
        Org_Role