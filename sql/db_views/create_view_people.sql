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