DROP VIEW IF EXISTS view_people_memberstate;

CREATE VIEW view_people_memberstate AS
    SELECT 
        People.Id AS Id,
        People.FirstNameEncrypt AS FirstNameEncrypt,
        People.LastNameEncrypt AS LastNameEncrypt,
        People.DateOfBirth AS DateOfBirth,
        People.DateOfDeath AS DateOfDeath,
        People.PreviousCongregation AS PreviousCongregation,
        People.MembershipNo AS MembershipNo,
        People.VisibleInCalendar AS VisibleInCalendar,
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
        People.KeyToExp AS KeyToExp,
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
                memberstate.Name
            FROM
                memberstate
            WHERE
                memberstate.Id = MemberStateId) AS MemberStateName,

        Homes.FamilyNameEncrypt AS FamilyNameEncrypt,
        Homes.PhoneEncrypt AS PhoneEncrypt,
        Homes.AddressEncrypt as AddressEncrypt,
        Homes.City

        FROM
        People
        LEFT OUTER JOIN Homes ON Homes.Id = People.HomeId