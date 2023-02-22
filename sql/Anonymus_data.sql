select 
	concat(CONVERT(SUBSTR(AES_DECRYPT(LastNameEncrypt,@hash),1, 15) using utf8), "son") as LastName, 
	if(Gender=1, 
		concat(CONVERT(SUBSTR(AES_DECRYPT(FirstNameEncrypt,@hash),1, 15) using utf8), "e"),
		concat(CONVERT(SUBSTR(AES_DECRYPT(FirstNameEncrypt,@hash),1, 15) using utf8), "a")
        ) as LastName 
    from People 
    where DateOfAnonymization is null
;


update People Set 
	LastNameEncrypt = AES_ENCRYPT(concat(CONVERT(SUBSTR(AES_DECRYPT(LastNameEncrypt,@hash),1, 15) using utf8), "son"), @hash), 
	FirstNameEncrypt = AES_ENCRYPT(if(Gender=1, 
		concat(CONVERT(SUBSTR(AES_DECRYPT(FirstNameEncrypt,@hash),1, 15) using utf8), "e"),
		concat(CONVERT(SUBSTR(AES_DECRYPT(FirstNameEncrypt,@hash),1, 15) using utf8), "a")
        ), @hash),
	EmailEncrypt = AES_ENCRYPT(concat(CONVERT(SUBSTR(AES_DECRYPT(LastNameEncrypt,@hash),1, 15) using utf8), "@mail.com"), @hash) 
    where DateOfAnonymization is null
;


update Homes Set 
	FamilyNameEncrypt = AES_ENCRYPT(concat(CONVERT(SUBSTR(AES_DECRYPT(FamilyNameEncrypt,@hash),1, 15) using utf8), "son"), @hash) 
;