<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/wp-authenticate.php";
require_once SARON_ROOT . "menu.php";

 
    /*** REQUIRE USER AUTHENTICATION ***/

    isLoggedIn();
    
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Om <?php echo NameOfRegistry;?></title>
        <meta charset="UTF-8">
    </head>
    <Body>
    <div class="saron-about-text">
    <H1>Om <?php echo NameOfRegistry;?></H1>
    <H2>Allmänt</H2>
    <ul>    
        <H3>Personuppgiftslagen - GDPR</H3>
        <ul>    
            <?php echo NameOfRegistry;?> är <?php echo FullNameOfCongregation;?>s medlemsregister och lyder under personuppgiftslagen.  
            Se <a href="gdpr.php">Hantering av personuppgifter</a>
        </ul>    

        <h3>Primär användargrupp</h3>
        <ul>
            Primär användargrupp är församlingsledning/styrelse.<Br>
        </ul>
        <h3>Behörighet</h3>
        <ul>
            Det finns två behörighetsnivåer:
            <ul>
                <li>
                    Uppdaterare - Endast ett fåtal bör ha denna behörighet.
                </li>
                <li>
                    Läsanvändare - Hela församlingslednngen samt medlemmar med förtroendeuppdrag där de behöver medlemsuppgifter.
                </li>            
            </ul>
        </ul>
        <H3>Registrets huvuduppgifter</H3>
        <ul>    
            Registrets huvuduppgift är att hålla förteckning över församlingens medlemmar.
            Registret är endast till för att:
            <ul>
                <li>
                    Registrera medlemsuppgifter
                </li>
                <li>
                    Registrera dopuppgifter på samtliga personer som döpts i församlingens regi eller som är medlemmar i församlingen
                </li>
                <ul> 
                    <li>
                        Dopuppgifter registreras för att hålla den informationen som tjänst till den döpte 
                    </li>
                    <li>
                        Efter medlemskapets slut bygger lagringen av dopuppgifterna på samtycke. Det kan därför vara bra att i samband med medlemsintagning dokumentera samtycke. Se 
                    </li>
                </ul>
                <li>
                    Registrera kontaktuppgifter på medlemmar för skapande av adresskalender
                </li>
                <li>
                    Systemet kan skapa en adresskalender i pdf-format. Filen innehåller alla som satt som "synlig i adresskalendern". Filen skrivs ut och delas sedan ut till aktuella medlemmar. 
                </li>    
                <ul>
                    <li>
                        Omslag till adresskalendern skapas separat. Underlag finns i församlingsledningens gemensamma lagringstjänst i katalogen Medlemsregister.
                    </li>
                    <li>    
                        <b>OBS: PDF-filen ska inte mailas ut!</b> 
                    </li>
                </ul>
                <li>
                    Generera statistik på gruppnivå för egen analys och till samarbetsorganisationer som vi vill dela informationen med
                </li>
            </ul>
            Registrets uppgifter får inte användas i andra syften.<br><br>
            <?php echo FullNameOfCongregation ?> är inte skyldig att lämna ut registrets uppgifter till någon förutom till den uppgifterna gäller. Se även: tjänster enligt GDPR.        
        </ul>
    </ul>
    <h2>Informationsuppgifter i <?php echo NameOfRegistry;?></h2>
        <ul>
            <h3>Identitet i registret</h3>
            <ul>
            Identiteten i registret består av nedanstående tre uppgifter. Uppgifterna måste vara unika för en person. För att att kunna lagra en person i registret behövs en fullständig identitet. Principen är att använda de namn som används i vardagliga kontakter.
            <ul>
                <li>
                    Förnamn 
                </li>
                <li>
                    Efternamn
                </li>
                <li>
                    Födelsedatum 
                </li>
                    <ul>
                        <li>
                            I händelse av att en medlem har skyddad identitet kan fingerade uppgifter användas. Detta görs i samråd med aktuell medlem.
                        </li>
                    </ul>
            </ul>
        </ul>
        <h3>Medlemsuppgifter</h3>
        <ul>
            Medlemsuppgifter för de som är medlemmar
            <ul>
                <li>
                    Startdatum för medlemskap
                </li>
                <li>
                    Slutdatum för medlemskap
                </li>
                <li>
                    Medlemsnummer
                </li>
                <li>
                    Eventuell tidigare församlingstillhörighet 
                </li>
                <li>
                    Eventuell efterkommande församlingstillhörighet.
                </li>
            </ul>
        </ul>

        <h3>Dopuppgifter</h3>
       <ul>
       Dopuppgifter ligger till grund för dopregistret för de som ger samtycke
        <ul>
            <li>
                Dopdatum
            </li>
            <li>
                Dopförsamling
            </li>
            <li>
                Dopförrättare
            </li>
        </ul>
    </ul>
    <h3>Kontaktuppgifter</h3>
    <ul>
        Kontaktuppgifterna används bland annat i adresskalendern    
        <ul>
            <li>
                Hemadress
            </li>
            <li>
                Telefon
            </li>
            <li>
                Mobiltelefon
            </li>
            <li>
                Mailadress
            </li>
            <li>
                Utskicksväg: [-, Brev] (- hanteras som mail om mailadress existerar.    
             </li>
        </ul>
    </ul>
    <h3>Övriga uppgifter</h3>
    <ul>
        <ul>
            <li>
                Kommentar i fritext (Noteringarna ska vara icke värderande om individen.)
            </li>
            <li>
                Kön [-, Kvinna, Man]<Br>
             </li>
            <li>
               Synlighet i adresskalender [-, Nej, Ja] (- hanteras som Nej)
            </li>
            <li>
                Nyckelinnehav till kyrka samt kyrkans expedition [-, Nej, Ja]        
            </li>
        </ul>
    </ul>  
</ul>
    <h2>Teknisk informationssäkerhet</h3>
    <ul>
    <h3>Förvaltning</h3>
    <ul>
        Säkerhetsnivån på systemet är anpassad för informationens skyddsvärde och att systemet används i enlighet med dess huvuduppgift av avsedd användargrupp.<BR>
        Tekniskt sett så behövs en förvaltning som ser till att systemet hålls uppdaterat vad det gäller programvaror och att backuper tas. Om detta inte görs kommer systemets säkerhetsnivå att sjunka.<br>
        Den tekniska plattformen medger att registret är tillgängligt via internet för behöriga användare. Den tekniska plattformen medger sökning bland medlemsuppgifterna samt uppdatering av dessa.
    </ul>
    <h3>Behörighet</h3>
    <ul>
        Behörighet sätts upp via Korskyrkans hemsida. Anvisning finns i den gemensamma lagringsfunktionen för FL. Se katalog Medlemsregister. 
    </ul>
     </ul>
    <H2>Git repository</H2>
    Se <a href="https://github.com/petlund/saron/" target="_blank">git repository</a>
    </div>
   </body>
</html>        
