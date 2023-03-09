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

