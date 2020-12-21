<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once 'config.php'; 
require_once SARON_ROOT . "app/access/SaronCookie.php";
require_once SARON_ROOT . "menu.php";


   
    ?>
    <!DOCTYPE html>
<html>
    <head>
        <title><?php echo NameOfRegistry;?>  - Hjälp</title>
        <meta charset="UTF-8">
<!--        <meta name="viewport" content="width=device-width, initial-scale=1.0">
-->
</head>
    <Body>
    <div class="saron-about-text">
    <h1>Hjälp - Beskrivning av handgrepp</h1>
    Se även <a href="gdpr.php">Hantering av personuppgifter</a>
    <h2>Övergripande - Alla användare</h2>
    <ul>
        <h3>Huvudmenyn</h3>
        <ul>
            Huvudmenyn finns alltid överst på sidan
            <ul>
                <li>
                    <b>Hem:</b> leder till första sidan.
                    <ul>
                        <li>
                            På första sidan kan de som har uppdateringsmöjligheter. Skriva meddelanden till alla som har möjlighet att logga in. 
                        </li>
                    </ul>
                </li>
                <li>
                    <b>Register:</b> Här finns funktioner för att söka i och uppdatera registret.
                </li>
                <li>
                    <b>Rapporter: </b> Här finns olika typer av rapporter. En del görs i form av PDF.    
                </li>
                <li>
                    <b>Om: </b>Här finns beskrivning av registret och viktiga rutiner.  
                </li>
                <li>
                    <b>Logga ut: </b>Det är viktigt att alltid logga ut när du är färdig.
                    <ul>
                        <li>
                            Det finns en automatisk utloggningsfunktion som loggar ut efter fem minuter. Beroende på hur du lämnar <?php echo NameOfRegistry;?> kommer den automatiska utloggningen att fungera eller inte.
                        </li>
                    </ul>
                    
                </li>
            </ul>
        </ul>
        <H3>Söka i registret</H3>
        <ul>
            Sökning kan göras där tabeller med personinformation visas.
            Sökfunktionen omfattar tre filterfunktioner:
            <ul>
                <li>
                    <b>Grupp:</b> Filter för olika typer av grupperingar.
                </li>
                <li>
                    <b>Söksträng:</b> Fritextsökning (endast ett ord kan anges) sökning sker på alla relevanta fält i databasen, ej datum och ja/nej fält.
                </li>
                <li>
                    <b>Sortering i tabellerna: </b>Tabellerna går att sortera genom att klicka på en kolumnrubrik.
                    Visad tabellängd kan justeras i botten på tabellen. Där kan man även hoppa mellan sidor. Kortare tabeller ger snabbare sidväxlingar.
                 </li>
                    <ul>
                        <li>
                            När [Grupp] är satt till Senast ändrade personer går det inte att sortera i tabellerna. Sorteringsordningen är satt av filtret.
                        </li>
                    </ul>                 
            </ul>                 
            Anmärkning: Sökfunktionerna är bara synliga där de är användbara.
        </ul>
        <H3>Ta fram ett samtyckesunderlag</H3>
        <ul>
            I samband med att uppgifter om en ny person läggs in i medlemsregistret 
            bör ett samtyckesunderlag skapas. Detta ska göras i samförstånd med den aktuella personen.
            <ul>
                <li>
                    Välj menyn [Register][Registeröversikt]
                </li>
                <li>
                    Sök fram den aktuella personen.
                </li>
                <li>
                    Klicka på <img src="/<?php echo SARON_URI;?>app/images/pdf.png" title="Skapa personakt PDF"/> för aktuell person. 
                </li>
                <li>
                    Skriv ut och skriv under och sätt in i Samtyckespärmen.
                </li>
            </ul>
        </ul>    
    </ul>
    <H2>Uppdatera medlemsregistret - Särskild behörighet</H2>
    <ul>
        Uppdateringar kan endast göras i de tabeller som finns under menyn [Register].
        <H3>Lägg till personuppgifter om en ny person</H3>
        <ul>
            Använd menyn [Register][Personuppgifter]. Längst upp till höger finns en knapp [+ Ny person].
            <ul>
                <li>
                    Om personen bor i ett hem som redan finns inlagt i registret, leta upp det i drop-down-listen.
                </li>
                <li>
                    Efter att individen lagts till kan medlems- och dopuppgifter kompletteras.
                </li>
                <li>
                    Efter att de nya personuppgifterna lags till skiftar filtret [Grupp] automatiskt till "senast ändrade personer" för att det ska bli lättare att hitta de nya personuppgifterna. 
                </li>
            </ul>
        </ul>
    <H3>Uppdatera personuppgifter som är kopplade till ett hem</H3>
    <ul>
        Uppgifterna är gemensamma för alla som bor i hemmet.<br>
        Använd menyn [Register][Personuppgifter] samt ikonen <img src="/<?php echo SARON_URI;?>app/images/home.png" title="Uppdatera hem"/> till vänster.
        <ul>
            <li>
                Klicka på <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/> till höger.
            </li>
            <li>
            Uppdatera uppgifterna i formuläret. 
            </li>
                <ul>
                    <li>
                        Med brevutskick menas att adressen kommer med bland adressettiketter, när sådana skrivs ut.
                    </li>
                </ul>
            <li>
            Tryck på spara om du vill [Spara] om du vill spara eller på [Avbryt] om du inte vill spara.
            </li>
            <li>
            Stäng raden med <img src="/<?php echo SARON_URI;?>app/images/cross.png" title="ändra"/> till vänster.    
            </li>
        </ul>
    </ul>
    <H3>Uppdatera personuppgifter som är kopplade till medlemskap</H3>
    <ul>
        Använd menyn [Register][Personuppgifter] samt ikonen <img src="/<?php echo SARON_URI . SARON_IMAGES_URI;?>member.png" title="Uppdatera hem"/> till vänster.
        <ul>
            <li>
                Klicka på <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/> till höger.
            </li>
            <li>
            Uppdatera uppgifterna i formuläret.
            </li>
                <ul>
                    <li>
                        OBS: Använd ingen värderande information i kommentarsfältet.
                    </li>
                </ul>
            <li>
            Tryck på spara om du vill [Spara] om du vill spara eller på [Avbryt] om du inte vill spara.
            </li>
            <li>
            Stäng raden med <img src="/<?php echo SARON_URI;?>app/images/cross.png" title="ändra"/> till vänster.    
            </li>
        </ul>
        Alternativt använder du menyn [Register][- Medlemsuppgifter] och <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/> till höger.                
    </ul>
    <H3>Uppdatera personuppgifter som är kopplade till dop</H3>
    <ul>
        Använd menyn [Register][Personuppgifter] samt ikonen <img src="/<?php echo SARON_URI;?>app/images/baptist.png" title="Uppdatera hem"/> till vänster.
        <ul>
            <li>
                Klicka på <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/> till höger.
            </li>
            <li>
            Uppdatera uppgifterna i formuläret.
            </li>
                <ul>
                    <li>
                        OBS: Använd ingen värderande information i kommentarsfältet.
                    </li>
                </ul>
            <li>
            Tryck på spara om du vill [Spara] om du vill spara eller på [Avbryt] om du inte vill spara.
            </li>
            <li>
            Stäng raden med <img src="/<?php echo SARON_URI;?>app/images/cross.png" title="ändra"/> till vänster.    
            </li>
        </ul>
        Alternativt använder du menyn [Register][- Dopuppgifter] och <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/> till höger.                
    </ul>
    <H3>Uppdatera personuppgifter som är kopplade till nyckelinnehav</H3>
    <ul>
        Använd menyn [Register][- Nyckelinnehav].
        <ul>
            <li>
                Klicka på <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/> till höger.
            </li>
            <li>
            Uppdatera uppgifterna i formuläret.
            </li>
                <ul>
                    <li>
                        OBS: Använd ingen värderande information i kommentarsfältet.
                    </li>
                </ul>
            <li>
            Tryck på spara om du vill [Spara] om du vill spara eller på [Avbryt] om du inte vill spara.
            </li>
        </ul>
    </ul>
    <H3>Radera/anonymisera personuppgifter</H3>
    <ul>
        Använd menyn [Register][Registeröversikt].
        <ul>
            <li>
                Finns behov av att skapa ett samtyckesavtal innan anonymisering? Gäller för dem som gått med i församlingen innan 2018.
            </li>            
            <li>
                Klicka på <img src="/<?php echo SARON_URI;?>app/images/delete.png" title="Radera"/> till höger.
            </li>            
            <li>
                Tänk efter och svara sedan på frågan 
            </li>
        </ul>
    </ul>
    <H2>Livscykel för personuppgifter</H2>
    Med livscykel menas vilka tillstånd (Status) personuppgifter kan beskrivas med i registret.
    
    <table class="saronHtmlTable">
        <tr class="saronHtmlTable head"><th>Händelse</th><th>Status</th><th>Automatiska uppdateringar</th></tr>
        <tr class="saronHtmlTable row odd">
            <td class="saronHtmlTable col">En ny person läggs in i registret utan medlems och dopuppgifter</td>
            <td class="saronHtmlTable col">Ej Medlem</td>
            <td class="saronHtmlTable col">-Inga andra uppdateringar</td>
        </tr>
        <tr class="saronHtmlTable row even">
            <td class="saronHtmlTable col">- En ny person läggs till i registret med dopuppgifter utan uppgifter om pågående medlemskap.<br>
                - Dopuppgifter kopplas till en person utan uppgifter om pågående medlemskap.<br>                
                - En medlem får ett datum för avslut av medlemskap.</td>
            <td class="saronHtmlTable col">Dopregister</td>
            <td class="saronHtmlTable col">- Personuppgifterna kommer inte att visas i adresskalender.<br>
                - Personen kommer att finnas med i listning för Anonymisering kommande år.<br>
                - Personuppgifterna kommer att redovisas i dopregistret oavsett om det finns dopuppgifter eller ej.
            </td>
        </tr>
        <tr class="saronHtmlTable row odd">
            <td class="saronHtmlTable col">- En ny person läggs till registret med medlemsuppgifter.<br>
            - En person uppdateras med ett datum för start av medlemskap. </td>
            <td class="saronHtmlTable col">Medlem</td>
            <td class="saronHtmlTable col">- Personuppgifterna kommer att visas i adresskalendern om synlighet i adresskalender är satt till Ja</td>
        </tr>
        <tr class="saronHtmlTable row even">
            <td class="saronHtmlTable col">- En person får ett datum i fältet Avliden</td>
            <td class="saronHtmlTable col">Avliden</td>
            <td class="saronHtmlTable col">- Personen kopplas bort från sina kontakt- och adressuppgifter.<br> 
                - Personen kommer inte att tillhöra något hem längre.<br>
                - Personen kommer inte att finnas med bland de som behöver anonymiseras.<br>
                - Personuppgifter kommer inte att visas i adresskalender.
            </td>
        </tr>
        <tr class="saronHtmlTable row odd">
            <td class="saronHtmlTable col">- En person raderas/anonymiseras</td>
            <td class="saronHtmlTable col">Anonymiserad</td>
            <td class="saronHtmlTable col">- Personen kopplas bort från sina kontakt och adressuppgifter. Personen kommer inte att tillhöra något hem längre.<br>
                - Personen kommer inte att finnas med bland de som behöver anonymiseras.<br>
                - Endast registrerade datum och uppgift om kön kommer att finnas kvar.
            </td>
        </tr>
    </table>
    </div>
    </body>
</html>        
        