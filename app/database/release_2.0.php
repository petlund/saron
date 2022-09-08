<?php
    require_once 'config.php';
    require_once SARON_ROOT . 'app/database/queries.php';
    require_once SARON_ROOT . 'app/database/db.php';
    require_once SARON_ROOT . 'app/entities/SaronUser.php';

    $db = new db();

//    updateHomes();
    updateAnonymDate();
    
updateAnonymDate(){
// copy anonymization date
// 'utf8' is currently an alias for the character set UTF8MB3, but will be an alias for UTF8MB4 in a future release. Please consider using UTF8MB4 in order to be unambiguous. 3719 'utf8' is currently an alias for the character set UTF8MB3, but will be an alias for UTF8MB4 in a future release. Please consider using UTF8MB4 in order to be unambiguous. Rows matched: 7  Changed: 7  Warnings: 2

Update People set DateOfAnonymization = UPPER(CONVERT(BINARY SUBSTR(AES_DECRYPT(People.FirstNameEncrypt, '-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC78X4y5USYg7xg
8y84xbe6V4oVz7RlwP5srlmZxNbI/75hGuClYnbu2NZWCXjTlcHQTcgak8FOx8I2
z7ty33voG07wniNdzmDoVbgzNq/AoG7IX5ZmMIeqPg5nvVBaYvrbZQSo2u54iJLw
ZbainijsDvVkpoBaQg1m99SPpiZ+l2uolJFhSIejTVar6cc9ta5QLHH6W9JMdaaf
1BZbAvPk7wzcSL44cr550NhCILBL8JXWwmu9nAoCevikyK043626est4k/adzT2H
+E9zmlaKQkUtQm0HU2M63/SL8+HNJU+Ukjx7DzvimzuiLSJ5AP9fbR63vDCPhhac
vXc4fU7DAgMBAAECggEABisj8Dm0KalROoFg0GU05YnP/21Ex34xG5IRYMmkHw69
yeQe8W6s5qP5TPOcop0sslCLO8wTsSk/R5tD1L5fC7wxuBYIpSCL77Q4in3GAPWD
DVKN1ibLnRvSTzdYds7+2sqS/5PH9e7Nm9RKzUWRpEXNahNI7S1z0ShbMqfKZac2
Owvl1uDsqADiSL6J/II54s9pNE/zW89n49pPAcBM+dwKbgA6Q8mih5w0xakAsM51
S57A7FagE2eeAhcWQ6RUAr/4FHciEuQAbchi2jIM61gVCvClTTqBbins3QPq+e7J
MTztICeu1eemdx9psQFiKY0JPRudesRaCWb9reqIKQKBgQDhJGG+K8ucKXFE2WUn
jz2Qo4wqiOQ2slXTixMUrh8OOU3Ei3/U57jHhovAHuugt8czpC1GADjFDJqj/Dmg
Jo1U2B/8y206EuPPboo8gi4qmMYjhcYKteR5n0fhK8f7CFkVHFO5LhW3A3Ti+K4k
M/v+jlIbum/xv0Jvd8ZcdcV8pwKBgQDVs+h0YY0pQMmWkVG3t30vwpvBOlhYoBpi
s2BiMF1TBo4WNJ6CuR0vVXz8Tdda0Y4tOhMeeA5c4Pdezzomz5pka1e7s42AMxH5
2RCw7Mrkuck07pMWSGk9jWOWTHDuSNAkF5ciprv9XQJcI/n1DkOVti9E6AL7SXDm
2Kx5B/WUhQKBgBQt4Jzc7Aj5Je/X1FsxWYFHR17gmiPU5CBrzLAD00DBHhlJr5Fz
m3o2svvrSY9EKZSVWIF3r57pvEqKZtfV3MEIBgXwbb/QxZ5n9/foax7iRpAUg2We
W/YSsqyZ8fisDN/5g/+LA+M9ahKV23XfOuzb8LwPf80ZKWk4QQ00eRjtAoGASQQH
UHxkNswkFIXiXdsbss3TWdoNhXpuiXqwW9wahtuegIfKAPAuwJV89tfB1Q30fg9x
qN9sSN+0pXg4bjpQji7+8usOVdcdNTitM0Y0rKGt1aAOMXbcPmOouxmzHDdUpoBI
61X8tTyqKcFBDjGpdeWhkPGU0zbslHNDura1eeECgYBwpSF0y6qxpW+apaOqETB4
t42B0pw2PR3bgaBivbvUPKDtKGxmsZJEcWKWcNac970rm6vjaQzOtXqJ7q0DzR0I
nyj1orGuUS4gUkBWRnvLJoZ0coKm6v+dffvixy4YqkM5yxmB17IzeLIyDI+y6NK5
2/U7A8Lof5JFtiMYBJ5G4Q==
-----END PRIVATE KEY-----
'), 13, 250)  USING utf8))

WHERE UPPER(CONVERT(BINARY SUBSTR(AES_DECRYPT(People.LastNameEncrypt, '-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC78X4y5USYg7xg
8y84xbe6V4oVz7RlwP5srlmZxNbI/75hGuClYnbu2NZWCXjTlcHQTcgak8FOx8I2
z7ty33voG07wniNdzmDoVbgzNq/AoG7IX5ZmMIeqPg5nvVBaYvrbZQSo2u54iJLw
ZbainijsDvVkpoBaQg1m99SPpiZ+l2uolJFhSIejTVar6cc9ta5QLHH6W9JMdaaf
1BZbAvPk7wzcSL44cr550NhCILBL8JXWwmu9nAoCevikyK043626est4k/adzT2H
+E9zmlaKQkUtQm0HU2M63/SL8+HNJU+Ukjx7DzvimzuiLSJ5AP9fbR63vDCPhhac
vXc4fU7DAgMBAAECggEABisj8Dm0KalROoFg0GU05YnP/21Ex34xG5IRYMmkHw69
yeQe8W6s5qP5TPOcop0sslCLO8wTsSk/R5tD1L5fC7wxuBYIpSCL77Q4in3GAPWD
DVKN1ibLnRvSTzdYds7+2sqS/5PH9e7Nm9RKzUWRpEXNahNI7S1z0ShbMqfKZac2
Owvl1uDsqADiSL6J/II54s9pNE/zW89n49pPAcBM+dwKbgA6Q8mih5w0xakAsM51
S57A7FagE2eeAhcWQ6RUAr/4FHciEuQAbchi2jIM61gVCvClTTqBbins3QPq+e7J
MTztICeu1eemdx9psQFiKY0JPRudesRaCWb9reqIKQKBgQDhJGG+K8ucKXFE2WUn
jz2Qo4wqiOQ2slXTixMUrh8OOU3Ei3/U57jHhovAHuugt8czpC1GADjFDJqj/Dmg
Jo1U2B/8y206EuPPboo8gi4qmMYjhcYKteR5n0fhK8f7CFkVHFO5LhW3A3Ti+K4k
M/v+jlIbum/xv0Jvd8ZcdcV8pwKBgQDVs+h0YY0pQMmWkVG3t30vwpvBOlhYoBpi
s2BiMF1TBo4WNJ6CuR0vVXz8Tdda0Y4tOhMeeA5c4Pdezzomz5pka1e7s42AMxH5
2RCw7Mrkuck07pMWSGk9jWOWTHDuSNAkF5ciprv9XQJcI/n1DkOVti9E6AL7SXDm
2Kx5B/WUhQKBgBQt4Jzc7Aj5Je/X1FsxWYFHR17gmiPU5CBrzLAD00DBHhlJr5Fz
m3o2svvrSY9EKZSVWIF3r57pvEqKZtfV3MEIBgXwbb/QxZ5n9/foax7iRpAUg2We
W/YSsqyZ8fisDN/5g/+LA+M9ahKV23XfOuzb8LwPf80ZKWk4QQ00eRjtAoGASQQH
UHxkNswkFIXiXdsbss3TWdoNhXpuiXqwW9wahtuegIfKAPAuwJV89tfB1Q30fg9x
qN9sSN+0pXg4bjpQji7+8usOVdcdNTitM0Y0rKGt1aAOMXbcPmOouxmzHDdUpoBI
61X8tTyqKcFBDjGpdeWhkPGU0zbslHNDura1eeECgYBwpSF0y6qxpW+apaOqETB4
t42B0pw2PR3bgaBivbvUPKDtKGxmsZJEcWKWcNac970rm6vjaQzOtXqJ7q0DzR0I
nyj1orGuUS4gUkBWRnvLJoZ0coKm6v+dffvixy4YqkM5yxmB17IzeLIyDI+y6NK5
2/U7A8Lof5JFtiMYBJ5G4Q==
-----END PRIVATE KEY-----
'), 13, 250)  USING utf8)) like '%_Anonymiserad_%'


    
}
    
function showAllAnonymous() {
    -- select all anomymized rows
Select UPPER(CONVERT(BINARY SUBSTR(AES_DECRYPT(People.FirstNameEncrypt, '-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC78X4y5USYg7xg
8y84xbe6V4oVz7RlwP5srlmZxNbI/75hGuClYnbu2NZWCXjTlcHQTcgak8FOx8I2
z7ty33voG07wniNdzmDoVbgzNq/AoG7IX5ZmMIeqPg5nvVBaYvrbZQSo2u54iJLw
ZbainijsDvVkpoBaQg1m99SPpiZ+l2uolJFhSIejTVar6cc9ta5QLHH6W9JMdaaf
1BZbAvPk7wzcSL44cr550NhCILBL8JXWwmu9nAoCevikyK043626est4k/adzT2H
+E9zmlaKQkUtQm0HU2M63/SL8+HNJU+Ukjx7DzvimzuiLSJ5AP9fbR63vDCPhhac
vXc4fU7DAgMBAAECggEABisj8Dm0KalROoFg0GU05YnP/21Ex34xG5IRYMmkHw69
yeQe8W6s5qP5TPOcop0sslCLO8wTsSk/R5tD1L5fC7wxuBYIpSCL77Q4in3GAPWD
DVKN1ibLnRvSTzdYds7+2sqS/5PH9e7Nm9RKzUWRpEXNahNI7S1z0ShbMqfKZac2
Owvl1uDsqADiSL6J/II54s9pNE/zW89n49pPAcBM+dwKbgA6Q8mih5w0xakAsM51
S57A7FagE2eeAhcWQ6RUAr/4FHciEuQAbchi2jIM61gVCvClTTqBbins3QPq+e7J
MTztICeu1eemdx9psQFiKY0JPRudesRaCWb9reqIKQKBgQDhJGG+K8ucKXFE2WUn
jz2Qo4wqiOQ2slXTixMUrh8OOU3Ei3/U57jHhovAHuugt8czpC1GADjFDJqj/Dmg
Jo1U2B/8y206EuPPboo8gi4qmMYjhcYKteR5n0fhK8f7CFkVHFO5LhW3A3Ti+K4k
M/v+jlIbum/xv0Jvd8ZcdcV8pwKBgQDVs+h0YY0pQMmWkVG3t30vwpvBOlhYoBpi
s2BiMF1TBo4WNJ6CuR0vVXz8Tdda0Y4tOhMeeA5c4Pdezzomz5pka1e7s42AMxH5
2RCw7Mrkuck07pMWSGk9jWOWTHDuSNAkF5ciprv9XQJcI/n1DkOVti9E6AL7SXDm
2Kx5B/WUhQKBgBQt4Jzc7Aj5Je/X1FsxWYFHR17gmiPU5CBrzLAD00DBHhlJr5Fz
m3o2svvrSY9EKZSVWIF3r57pvEqKZtfV3MEIBgXwbb/QxZ5n9/foax7iRpAUg2We
W/YSsqyZ8fisDN/5g/+LA+M9ahKV23XfOuzb8LwPf80ZKWk4QQ00eRjtAoGASQQH
UHxkNswkFIXiXdsbss3TWdoNhXpuiXqwW9wahtuegIfKAPAuwJV89tfB1Q30fg9x
qN9sSN+0pXg4bjpQji7+8usOVdcdNTitM0Y0rKGt1aAOMXbcPmOouxmzHDdUpoBI
61X8tTyqKcFBDjGpdeWhkPGU0zbslHNDura1eeECgYBwpSF0y6qxpW+apaOqETB4
t42B0pw2PR3bgaBivbvUPKDtKGxmsZJEcWKWcNac970rm6vjaQzOtXqJ7q0DzR0I
nyj1orGuUS4gUkBWRnvLJoZ0coKm6v+dffvixy4YqkM5yxmB17IzeLIyDI+y6NK5
2/U7A8Lof5JFtiMYBJ5G4Q==
-----END PRIVATE KEY-----
'), 13, 250)  USING utf8)) as ADATE, DateOfAnonymization
 from People
 WHERE UPPER(CONVERT(BINARY SUBSTR(AES_DECRYPT(People.LastNameEncrypt, '-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC78X4y5USYg7xg
8y84xbe6V4oVz7RlwP5srlmZxNbI/75hGuClYnbu2NZWCXjTlcHQTcgak8FOx8I2
z7ty33voG07wniNdzmDoVbgzNq/AoG7IX5ZmMIeqPg5nvVBaYvrbZQSo2u54iJLw
ZbainijsDvVkpoBaQg1m99SPpiZ+l2uolJFhSIejTVar6cc9ta5QLHH6W9JMdaaf
1BZbAvPk7wzcSL44cr550NhCILBL8JXWwmu9nAoCevikyK043626est4k/adzT2H
+E9zmlaKQkUtQm0HU2M63/SL8+HNJU+Ukjx7DzvimzuiLSJ5AP9fbR63vDCPhhac
vXc4fU7DAgMBAAECggEABisj8Dm0KalROoFg0GU05YnP/21Ex34xG5IRYMmkHw69
yeQe8W6s5qP5TPOcop0sslCLO8wTsSk/R5tD1L5fC7wxuBYIpSCL77Q4in3GAPWD
DVKN1ibLnRvSTzdYds7+2sqS/5PH9e7Nm9RKzUWRpEXNahNI7S1z0ShbMqfKZac2
Owvl1uDsqADiSL6J/II54s9pNE/zW89n49pPAcBM+dwKbgA6Q8mih5w0xakAsM51
S57A7FagE2eeAhcWQ6RUAr/4FHciEuQAbchi2jIM61gVCvClTTqBbins3QPq+e7J
MTztICeu1eemdx9psQFiKY0JPRudesRaCWb9reqIKQKBgQDhJGG+K8ucKXFE2WUn
jz2Qo4wqiOQ2slXTixMUrh8OOU3Ei3/U57jHhovAHuugt8czpC1GADjFDJqj/Dmg
Jo1U2B/8y206EuPPboo8gi4qmMYjhcYKteR5n0fhK8f7CFkVHFO5LhW3A3Ti+K4k
M/v+jlIbum/xv0Jvd8ZcdcV8pwKBgQDVs+h0YY0pQMmWkVG3t30vwpvBOlhYoBpi
s2BiMF1TBo4WNJ6CuR0vVXz8Tdda0Y4tOhMeeA5c4Pdezzomz5pka1e7s42AMxH5
2RCw7Mrkuck07pMWSGk9jWOWTHDuSNAkF5ciprv9XQJcI/n1DkOVti9E6AL7SXDm
2Kx5B/WUhQKBgBQt4Jzc7Aj5Je/X1FsxWYFHR17gmiPU5CBrzLAD00DBHhlJr5Fz
m3o2svvrSY9EKZSVWIF3r57pvEqKZtfV3MEIBgXwbb/QxZ5n9/foax7iRpAUg2We
W/YSsqyZ8fisDN/5g/+LA+M9ahKV23XfOuzb8LwPf80ZKWk4QQ00eRjtAoGASQQH
UHxkNswkFIXiXdsbss3TWdoNhXpuiXqwW9wahtuegIfKAPAuwJV89tfB1Q30fg9x
qN9sSN+0pXg4bjpQji7+8usOVdcdNTitM0Y0rKGt1aAOMXbcPmOouxmzHDdUpoBI
61X8tTyqKcFBDjGpdeWhkPGU0zbslHNDura1eeECgYBwpSF0y6qxpW+apaOqETB4
t42B0pw2PR3bgaBivbvUPKDtKGxmsZJEcWKWcNac970rm6vjaQzOtXqJ7q0DzR0I
nyj1orGuUS4gUkBWRnvLJoZ0coKm6v+dffvixy4YqkM5yxmB17IzeLIyDI+y6NK5
2/U7A8Lof5JFtiMYBJ5G4Q==
-----END PRIVATE KEY-----
'), 13, 250)  USING utf8)) like '%_Anonymiserad_%'
}    
    
    
function updateHomes(){    
    $sql = "Update Homes, " .
        "(WITH _People AS (" .
        "  SELECT *, " .
        "    row_number() OVER (PARTITION BY HomeId ORDER BY Inserted) as row_num " .
        " FROM saron.People " .
        ") " .
        "SELECT HomeId, Inserted, Inserter, InserterName " .
        "FROM _People " .
        "WHERE row_num = 1 and HomeId > 0 " .
        "order by HomeId) as min " .
        "set  " .
        "Homes.Inserted = min.Inserted, " .
        "Homes.Inserter = min.Inserter, " .
        "Homes.InserterName = min.InserterName " .
        "WHERE Homes.Id = min.HomeId ";

     echo $sql;

    $array = $db->sqlQuery($sql);

    echo '<pre>'; print_r($array); echo '</pre>';

    $sql2 = "select * from Homes";

    $listResult = $db->sqlQuery($sql2);
    if(!$listResult){
        
        exit();
    }
    foreach($listResult as $aRow){
        echo "<br>Id = " . $aRow["Id"] . 
                " updater = " . $aRow["Updater"] . 
                " updaterName = " . $aRow["UpdaterName"] . 
                " updated =  " . $aRow["Updated"] . 
                " inserter =  " . $aRow["Inserter"] . 
                " inserterName =  " . $aRow["InserterName"] . 
                " inserted: = " . $aRow["Inserted"]; 
    }
}