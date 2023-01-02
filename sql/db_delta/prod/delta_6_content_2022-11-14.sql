
UPDATE `Org_UnitType` SET `PosEnabled`=PosEnabled +1,`SubUnitEnabled`=SubUnitEnabled + 1;

UPDATE `Org_Pos` SET OrgPosStatus_FK = 1 where OrgPosStatus_FK = 6;
UPDATE `Org_Pos` SET PrevOrgPosStatus_FK = 1 where PrevOrgPosStatus_FK = 6;

DELETE FROM `Org_PosStatus` WHERE (`Id` = '6');

Update Org_Pos set People_FK=-1, OrgSuperPos_FK=184 where People_FK = -72;
Update Org_Pos set People_FK=-1, OrgSuperPos_FK=553 where People_FK = -55;
Update Org_Pos set People_FK=-1, OrgSuperPos_FK=547 where People_FK = -51;
Update Org_Pos set PrevPeople_FK=-1, PrevOrgSuperPos_FK=184 where PrevPeople_FK = -72;
Update Org_Pos set PrevPeople_FK=-1, PrevOrgSuperPos_FK=553 where PrevPeople_FK = -55;
Update Org_Pos set PrevPeople_FK=-1, PrevOrgSuperPos_FK=547 where PrevPeople_FK = -51;

delete from MemberState;

INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('0', 'Sätts för en person som inte har tillräckliga uppgifter för att sätta en status', '-');
INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('1', 'Sätts för en person som endast är registrerad med namn och födelsedatum.', 'Registerförd');
INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('2', 'Sätts för en person som har ett datum för medlemskap start men inte för medlemskap slut', 'Medlem');
INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('3', 'Sätts för en "Registerförd" person som också har registrerade dopuppifter', 'Endast dopuppgift');
INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('4', 'Sätts för en person som har ett datum i fältet anonymiserad', 'Anonymiserad');
INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('5', 'Sätts för en person som har ett datum i fältet avliden', 'Avliden');
INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('6', 'Används inte längre. Ersatt av "Vänkontakt"', 'Medhjälpare');
INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('7', 'Sätts för en icke "Medlem" som har ett datum satt för vänkontakt. Årlig förnyelse behövs. (Sker med automatik om personen har ett eller flera uppdrag)', 'Vänkontakt');
INSERT INTO `MemberState` (`Id`, `Description`, `Name`) VALUES ('8', 'Sätts för person som både har datum för medlemskap start och slut', 'Avslutat medlemskap');

Update Org_Pos set People_FK=-1, OrgSuperPos_FK=184 where People_FK = -72;
Update Org_Pos set People_FK=-1, OrgSuperPos_FK=553 where People_FK = -55;
Update Org_Pos set People_FK=-1, OrgSuperPos_FK=547 where People_FK = -51;
Update Org_Pos set PrevPeople_FK=-1, PrevOrgSuperPos_FK=184 where PrevPeople_FK = -72;
Update Org_Pos set PrevPeople_FK=-1, PrevOrgSuperPos_FK=553 where PrevPeople_FK = -55;
Update Org_Pos set PrevPeople_FK=-1, PrevOrgSuperPos_FK=547 where PrevPeople_FK = -51;

Update People Set InserterName='Saron'; -- Default
Update People Set InserterName='Ulf Pettersson' WHERE Inserter=26 or Inserter=26;
Update People Set InserterName='Saron Demo' WHERE Inserter=44;
Update People Set InserterName='Peter Lundin' WHERE Inserter=16;
Update People Set InserterName='Mattias Josephson' WHERE Inserter=24;
Update People Set InserterName='Mattias Jonsson' WHERE Inserter=30;
Update People Set InserterName='Marie Lundin' WHERE Inserter=43;
Update People Set InserterName='Magnus Sjögren' WHERE Inserter=4;
Update People Set InserterName='Emma Arnlund' WHERE Inserter=38;
Update People Set InserterName='Emma Arnlund' WHERE Inserter=42;
Update People Set InserterName='Edvard Jarlert' WHERE Inserter=22;
Update People Set InserterName='Christian Delén' WHERE Inserter=5;
Update People Set InserterName='Björn Strömvall' WHERE Inserter=27;
Update People Set InserterName='Birgitta Svensson' WHERE Inserter=45;

Update Homes Set InserterName='Saron'; -- Default
Update Homes Set InserterName='Ulf Pettersson' WHERE Inserter=26 or Inserter=26;
Update Homes Set InserterName='Saron Demo' WHERE Inserter=44;
Update Homes Set InserterName='Peter Lundin' WHERE Inserter=16;
Update Homes Set InserterName='Mattias Josephson' WHERE Inserter=24;
Update Homes Set InserterName='Mattias Jonsson' WHERE Inserter=30;
Update Homes Set InserterName='Marie Lundin' WHERE Inserter=43;
Update Homes Set InserterName='Magnus Sjögren' WHERE Inserter=4;
Update Homes Set InserterName='Emma Arnlund' WHERE Inserter=38;
Update Homes Set InserterName='Emma Arnlund' WHERE Inserter=42;
Update Homes Set InserterName='Edvard Jarlert' WHERE Inserter=22;
Update Homes Set InserterName='Christian Delén' WHERE Inserter=5;
Update Homes Set InserterName='Björn Strömvall' WHERE Inserter=27;
Update Homes Set InserterName='Birgitta Svensson' WHERE Inserter=45;

Update People Set UpdaterName='Saron'; -- Default
Update People Set UpdaterName='Ulf Pettersson' WHERE Updater=26 or Updater=26;
Update People Set UpdaterName='Saron Demo' WHERE Updater=44;
Update People Set UpdaterName='Peter Lundin' WHERE Updater=16;
Update People Set UpdaterName='Mattias Josephson' WHERE Updater=24;
Update People Set UpdaterName='Mattias Jonsson' WHERE Updater=30;
Update People Set UpdaterName='Marie Lundin' WHERE Updater=43;
Update People Set UpdaterName='Magnus Sjögren' WHERE Updater=4;
Update People Set UpdaterName='Emma Arnlund' WHERE Updater=38;
Update People Set UpdaterName='Emma Arnlund' WHERE Updater=42;
Update People Set UpdaterName='Edvard Jarlert' WHERE Updater=22;
Update People Set UpdaterName='Christian Delén' WHERE Updater=5;
Update People Set UpdaterName='Björn Strömvall' WHERE Updater=27;
Update People Set UpdaterName='Birgitta Svensson' WHERE Updater=45;

Update Homes Set UpdaterName='Saron'; -- Default
Update Homes Set UpdaterName='Ulf Pettersson' WHERE Updater=26 or Updater=26;
Update Homes Set UpdaterName='Saron Demo' WHERE Updater=44;
Update Homes Set UpdaterName='Peter Lundin' WHERE Updater=16;
Update Homes Set UpdaterName='Mattias Josephson' WHERE Updater=24;
Update Homes Set UpdaterName='Mattias Jonsson' WHERE Updater=30;
Update Homes Set UpdaterName='Marie Lundin' WHERE Updater=43;
Update Homes Set UpdaterName='Magnus Sjögren' WHERE Updater=4;
Update Homes Set UpdaterName='Emma Arnlund' WHERE Updater=38;
Update Homes Set UpdaterName='Emma Arnlund' WHERE Updater=42;
Update Homes Set UpdaterName='Edvard Jarlert' WHERE Updater=22;
Update Homes Set UpdaterName='Christian Delén' WHERE Updater=5;
Update Homes Set UpdaterName='Björn Strömvall' WHERE Updater=27;
Update Homes Set UpdaterName='Birgitta Svensson' WHERE Updater=45;

-- EXCEL Copy Memberview
-- ="Update People set DateOfAnonymization = '"&HÖGER(A4;10)&"' WHERE MembershipNo="&E4&";"

Update People set DateOfAnonymization = '2021-02-02' WHERE Id=464;
Update People set DateOfAnonymization = '2022-02-09' WHERE Id=454;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1212;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1211;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1138;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1135;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1121;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1136;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1064;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1060;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1251;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1057;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1199;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1200;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1198;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1252;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1104;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1148;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1056;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1044;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1171;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1038;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1204;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1174;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1026;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1127;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1172;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1153;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1024;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1025;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1002;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1151;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1161;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1146;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1006;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1079;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1125;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1115;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1145;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1100;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1075;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1076;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1126;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1123;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1144;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1101;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1074;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1034;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1162;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1048;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1092;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1124;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1122;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1129;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=908;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1080;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1128;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1031;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1045;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=897;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1130;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1117;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1112;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=887;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1073;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1113;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=844;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=902;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=998;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=901;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1107;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=920;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=884;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=987;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1120;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=917;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=916;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=867;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=915;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=958;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=905;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1119;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=861;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1106;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=986;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=992;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=995;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=796;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=918;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=856;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1180;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1142;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=956;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=858;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=865;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=869;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1017;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=828;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=696;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1143;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=781;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1007;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=721;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1168;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=968;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=848;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=580;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1096;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1041;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1093;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1088;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=1001;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=885;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=836;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=595;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=587;
Update People set DateOfAnonymization = '2019-02-24' WHERE MembershipNo=829;


Update People set DateOfFriendshipStart = '2022-02-10'
WHERE 
    DateOfDeath is null and
    DateOfAnonymization is null and
    ((DateOfMembershipStart is null and
    DateOfMembershipEnd is null) 
    or
    (DateOfMembershipStart is not null and
    DateOfMembershipEnd is not null)) and
    Id in (Select People_FK from Org_Pos where People_FK > 0 group by People_FK); 

