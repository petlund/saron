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
        <title><?php echo NameOfRegistry;?>  - GDPR</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!--   <link rel="stylesheet" type="text/css" href="/<?php //echo SARON_URI;?>app/css/saron.css" />
    -->
     </head>
    <Body>
    <div class="saron-about-text">
    <h1>Hantering av personuppgifter</h1>
    <h2>Grundläggande</h2>
    <ul>
        Medlemsregister lyder under personuppgiftslagen som bygger på EU-förordningen GDPR.
        <h3>Sammanfattning av de viktigaste punkterna</h3>
        <ul>
            <ul>
                <li>
                    <?php echo FullNameOfCongregation ?> är personuppgiftsansvarig för sitt medlemsregister.
                </li>
                <li>
                    Medlemsregistret är ett icke publikt. 
                </li>
                <li>
                    Medlemsregistret anger vilka som är församlingens medlemmar. 
                </li>
                <li>
                    Registret innehåller inte personnummer, endast födelsedatum. 
                </li>
                <li>
                    Registret omfattar information om knytning till religiös organisation. Sådan information kräver varsam hantering. 
                </li>
                <li>
                    Medlemmar som har skyddad identitet ska hanteras med extra varsamhet. Se nedan.
                </li>
                <li>
                    Församlingsledningen är skyldig att säkerställa att personuppiftsbiträdesavtal finns med driftleverantören av medlemsregistret. 
                    Se <a href="https://www.loopia.se/pdf/loopia_allmanna_villkor.pdf" target="_empty">personuppgiftbiträdesavtal - Bilaga A</a>
                </li>
            </lu>
        </ul>
    </ul>
    <h3>Mer om GDPR finns att läsa om på Datainspektionens hemsida</h3> 
        <ul>
            <ul>
                <li>
                    <a href="http://www.datainspektionen.se/dataskyddsreformen/fragor-ochsvar/" target="_blank">Frågor och svar</a>
                </li>
                <li>
                    <a href="http://www.datainspektionen.se/dataskyddsreformen/forberedelser/forberedelser-for-personuppgiftsansvariga/"  target="_blank">Personuppgiftsansvariga</a>
                </li>
                <li>
                    <a href="http://www.datainspektionen.se/dataskyddsreformen/forberedelser/forberedelser-for-personuppgiftsbitraden/" target="_blank">Personuppgiftsbiträde</a>
                </li>
            </ul>
        </ul>      
    </ul>      
    <h2>Krav på tjänsteutbud</h2>
    <ul>
        Tjänster som <?php echo FullNameOfCongregation ?> är skyldiga att tillhandahålla enligt GDPR. Nedan följer en uppräkning av tjänsterna.     Se även <a href="https://www.korskyrkan.se/dataskyddsforordningen/medlemsregister/" target="_empty">Beskrivning av Korskyrkans tillämpning av GDPR ur medlemmars synvinkel</a>
        <h3>Rättning av personuppgifter</h3> 
        <ul>
            Personuppgiftsansvarig är skyldig att skyndsamt rätta felaktiga uppgifter.    
        </ul>
        <h3>Redovisning av personuppgifter</h3> 
        <ul>
            Personuppgiftsansvarig är skyldig att skyndsamt redovisa vilka uppgifter som finns lagrade för en viss person.      
        <UL>
            <li>
            Personuppgifterna ska endast redovisas för den person de gäller för.    
            </li>
            <li>
            Det är viktig att säkerställa att uppgifterna lämnas till rätt person. Bäst är att lämna en papperskopia i handen till den det gäller.    
            </li>
            <li>
                Se <a href="/help.php">Hjälp för att ta reda på hur ett utdrag görs.</a>    
            </li>
        </UL>
        </ul>
        <h3>Portabilitet</h3>
        <UL>
            Vanligaste fallet är det som går under benämningen "flyttbetyg".
            Eftersom datamängd per individ är liten bör rutinen vara enligt "Redovisning av personuppgifter" ovan.
        </ul>
        <h3>Incidenthantering</h3>
            <UL>
                Personuppgiftsansvarig är skyldig att skyndsamt (inom 72 h) kontakta datainspektionen i händelse av säkerhetsincident där personuppgifter har eller kan ha kommit i orätta händer.    
            </ul>
        </li>
    </UL>
    </UL>
    <h2>Rutiner inom <?php echo FullNameOfCongregation ?>.</h2>
    <ul>
        <h3>Nya medlemmar</h3>
        <UL>
            I samband med att person blir medlem i församlingen ska medlemsuppgifter föras in i medlemsregistret.
            <UL>
                <li>
                    En lämplig rutin är att efter medlemsuppgifterna är inmatade skriva ut ett exemplar av översikten av medlemsuppgifterna.
                </li>
                <li>
                    Medlemmen får då om hen vill ge samtycke till att församlingen lagrar angivna uppgifter genom att skriva under pappret. 
                </li>
                <li>
                    Det underskrivna pappret sätts in i avsedd pärm - Samtyckespärmen.
                </li>
            </ul>
        </ul>    
    </ul>    
    <ul>    
        <h3>En medlem kan inte vara anonymiserad</h3> 
        <ul>
            <ul>
                <li>
                    En organisation har laglig rätt att föra register över sina medlemmar.
                </li>
                <li>
                    Se <a href="https://www.korskyrkan.se/dataskyddsforordningen/" target="_empty">Korskyrkans tillämpning av GDPR</a>
                </li>
            </ul>
        </ul>
        <h3>Medlemmar som avslutar sitt medlemskap</h3>
            <UL>
                I samband med avslutat medlemskap uppdateras medlemsregistret med ett slutdatum för medlemskapet
                <UL>
                    <li>
                        Om personen inte vill att det ska finnas några personidentifierande uppgifter kvar i registret så ska personuppgifterna anonymiseras.
                    </li>
                    <li>
                        Se <a href="help.php" target="_empty">Hjälp</a> för information om vilka handgrepp som behöver utföras.
                    </li>
                    <li>
                        Plocka även ur Samtycket ur "Samtyckespärmen" och förstör det.
                    </li>
                    <li>
                        En medlem som avslutar sitt medlemskap ska inte ha några nycklar till kyrkan.
                    </li>
                </ul>
          </li>
        </ul>    
    </ul>    
    <ul>    
        <h3>Årlig genomgång av registret</h3>
        <UL>
            <UL>
                <li>
                    Årligen ska registret gås egenom avseende före detta medlemmar. <br><b>Säkerställ att ingen före detta medlem har tillgång till medlemsregistret.</b>
                </li>
                <li>
                    Enklast är det att efter årsmötet göra en genomgång av regsitret och anonymisera före detta medlemmar.
                </li>
                <li>
                    Se <a href="help.php" target="_empty">Hjälp</a> för information om vilka handgrepp som behöver utföras.
                </li>
            </ul>    
        </ul>        
        <h3>Skyddad identitet</h3>
        <UL>
            <UL>
                <li>
                För en medlem som har någon form av skyddad identitet bör följande beaktas
                </li>
                    <UL>
                        <li>
                            Använd det namn som den blivande medlemmen önskar, även om det inte är det juridiska namnet.
                        </li>
                        <UL>
                            <li>
                                Det bör vara ett namn som kan användas i vardagligt tal. För att lagra en medlem behövs för och efternamn samt ett födelsedatum. Ett medlemsnummer ges som retur av systemet.
                            </li>
                        </ul>
                        <li>
                            Kontaktuppgifter är bra men inte nödvändiga 
                        </li>
                        <li>
                            Stäm av om personen ska vara synlig i utskrifter av medlemskalendern? 
                        </li>
                    </ul>
                <li>
                    Om en person har en skyddad identitet eller ej bör inte framgå av medlemsregistret.
                </li>
            </ul>        
        </ul>        
        <h3>Rapporter</h3>
        <ul>        
            Rapporter som skrivs ut omfattas av GDPR. Var därför aktsam med att skriva ut personidentifierande information.<br>
        </ul>        
    </div>
    </body>