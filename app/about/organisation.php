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
        <title>Om <?php echo NameOfRegistry;?></title>
        <meta charset="UTF-8">
    </head>
    <Body>
    <div class="saron-about-text">
    <H1>Om <?php echo NameOfRegistry;?> - Organisation och bemanning</H1>
    <H2>Organisation - Inledning</H2>
    <ul>    
        För att kunna ändra i organisationsinformationen krävs separat behörighet. Endast personer som är definierade i medlemsregistret kan kopplas till uppdrag. 
        För att lägga till personer eller redigera kontaktuppgifter behövs speciell behörighet.<br>
        Grunden för förtroende uppdrag är medlemskap. Ibland kan det passa med att låta icke medlemmar ha förtroendeuppdrag. Även dessa personer behöver då registreras i
        medlemsregistret som icke medlemmar.
    </ul>    
        
        
    <H2>Organisation - Översik över menyer</H2>
    <ul>    
        <H3>Översikt av undermenyerna till Organisation</H3>
        <ul>
            <ul>
                <li>
                    <b>Organisationskalender förslag (pdf-fil)</b><br>
                    Ger en rapport över förändringsförslag till nåsta beslutsmöte.<br>
                </li>
                <li>
                    <b>Ansvar per person</b><br>
                    Här listas ansvar per person. Vakanta ansvar kan kopplas till person. Alla medlemmar finns med i listan plus de personer som har uppdrag.<br>
                </li>            
                <li>
                    <b>Organisation & Bemanning</b><br>
                    Här byggs och justeras organisationsträdet av de organisatoriska enheter och roller som definierats. Personer och funktioner kan kopplas till vakanta uppdrag.
                    <br>De röda och gula färmarkeringarna anger antalet vakanta positioner samt positioner som endast har ett förslag kopplat till sig. Personen är inte vidtalad än.<br>
                </li>            
                <li>
                    <b>Bemanningsstatus</b><br>
                    Här redovisas vilka bemanningasstatus som finns i systemet samt vad statusarna står för. <br>
                </li>            
                <li>
                    <b>Organisationsroller</b><br>
                    Här definieras vilka roller som ska finnas i organisationsträdet samt till vilka typer organisatoriska enheter rollerna är kopplade till. <br>                   
                </li>            
                <li>
                    <b>Organisationsenheter</b><br>
                    Här defineras vilka typer av organisatoriska enheter som ska finnas i organisationsträdet. I samband med att en gren i det organisatoriska trädet skapas namnsätts den angivna typen av organisatorisk enhet.
                    Exempelvis sätts namnet Församlingsledning medan typen av organisatorisk enhet är styrelse.<br>
                </li>            
                <li>
                    <b>Medlemsstatus</b><br>
                    Här redovisas vilka medlemsstatus som finns och som kan kopplas till en persson. Status kopplas till person givet angivande av olika datum. 
                    <br>Datum för: medlemskap start, medlemskap slut, Dopdatum, Dödsdatum.
                    <br>samt om personen har uppdrag eller är anonymiserad.<br>
                </li>            
                <li>
                    <b>Beslut om organisation</b><br>
                    Efter att ett beslut om organisationsuppdatareing tagits i årsmöte, församlingsledningsmöte eller av församlingsledningen. Skapa en ny version av organisationen. Befintlig organisation skrivs över med det nya försalget (Gäller endast bemanning i dagsläget.)
                <ul>
                    <li>
                        Beslutad organisattion redovisas i rapporten Organisationskalender beslutad (pdf). Den finns under menyn Rapporter. 
                    </li>               
                </ul>
                </li>            
            </ul>
        </ul>
        <H2>Fördjupad beskrivning av redigeringsmöjligheterna</H2>
        <H3>Organisationskalender förslag (pdf-fil)</H3>
        <ul>    
            <ul>
                <li>
                    Kalendern generes alltid med den aktuella infomrmationen i medlemsregistret.
                </li>
                <li>
                    I rapportens högra kant skrivs status för angivna förslag ut. Se rubriken Bemanningsstatus för ytterligare information om respektive status.
                </li>
                <li>
                    Rapporten är bland annat tänkt att utgör underlag för församlingens årsmöte.
                </li>
            </ul>
        </ul>
        <h3>Ansvar per person</h3>
        <ul>    
            <ul>
                <li>
                    Listan ger en sökbar och sorteringsbar funktion till stöd för att hitta personer som kan föreslås uppdrg i församlingen. 
                </li>
                <ul> 
                    <li>
                        Alla kolumner är sorteringsbara
                    </li>
                    <li>
                        Sökning görs i kolumnerna: Namn, Mail, Mobil, Bostadsort samtidigt
                    </li>
                </ul>
                <li>
                Klickar man på  <img src="/<?php echo SARON_URI;?>app/images/pos.png" title="Inga uppdrag"/> eller <img src="/<?php echo SARON_URI;?>app/images/haspos.png" title="Uppdragslista"/> längst till vänster så öppnar sig en undertabell med innehåll motsvarande kolumnen uppdragsöversikt. Där kan justeringar göras av vilka
                uppdrag en given person ska ha.
                </li>
                <ul>
                    <li>
                        Klicka på [ + Tilldela personen ett vakant uppdrag] om du vill koppla ytterligare ett uppdrag till personen
                    </li>    
                    <ul>
                        <li>
                            Välj en "Position" ur listan. Endast vakanta postioner visas i listan
                        </li>
                        <li>    
                            Ange om det är ett "Förslag" från din sida eller om det är "Avstämt" med den aktuella personen.
                        </li>
                        <li>    
                            Kommentarer som skrivs syns i rapporterna. Kommentaren ska därför relatera till uppdraget inte till personen.
                        </li>
                    </ul>
                    <li>
                        Klicka på <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/> om du ändra status eller frikoppla en person från ett uppdrag.
                    </li>    
                    <ul>
                        <li>    
                            Ange om det är ett "Förslag" från din sida eller om det är "Avstämt" med den aktuella personen.<BR>Om status sätts till "Vakant" frikopplas personen från uppdraget.
                        </li>
                        <li>    
                            Kommentarer som skrivs syns i rapporterna. Kommentaren ska därför relatera till uppdraget inte till personen.
                        </li>
                </ul>
            </ul>
        </ul>
        <h3>Organisation & Bemanning</h3>
        <ul>    
            <div>Detta är den mest komplexa tabellen av alla. Den är central för att bygga organisationsträdet och bemanna det.<br>
            Under menyerna "Organisationsenheter" och "Organisationsroller" sätter du regelverket för hur organisationsträdet kan byggas.</div>

            <h4>Organisatorisk enhet</h4>
            <ul>
                <li>
                    Skapa
                </li>
                <ul> 
                    <li>
                    </li>
                    <li>
                    </li>
                    <li>
                    </li>
                    <li>    
                    </li>
                </ul>
                <li>
                    Uppdatera
                </li>
                <li>
                    Ta bort
                </li>
                <li>
                    Flytta
                </li>
            </ul>
                
            <h4>Positioner att bemanna</h4>
            <ul>
                <li>
                    ...
                </li>
                <ul> 
                    <li>
                    </li>
                    <li>
                    </li>
                    <li>
                    </li>
                    <li>    
                    </li>
                </ul>
                <li>
                    ...
                </li>
                <li>
                    ...
                </li>
            </ul>
            <h4>Bemanna positioner</h4>
            <ul>
                <li>
                    Lägg till
                </li>
                <ul> 
                    <li>
                    </li>
                    <li>
                    </li>
                    <li>
                    </li>
                    <li>    
                    </li>
                </ul>
                <li>
                    Uppdatera
                </li>
                <li>
                    Ta bort
                </li>
            </ul>
        </ul>
        <h3>Bemanningsstatus</h3>
        <ul>    
            Tabellen redovisar vilka status en position i organisationen kan ha.
            <ul>
                <li>
                    Vill du uppdatera beskrivning klickar du på  <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/> 
                </li>
            </ul>
        </ul>
        <H3>Organisationsroller</H3>
        <ul>    
            Här definieras organisationsroller och kopplas till de typer av organisationsenheter som de ingår i.<br>
            Kommentar: Det kan vara bra att koll igenom vilka roller som finns innan ny skapas.     
            <ul>
                <li>
                    Lägg till ny roll genom att klicka på [ + Lägg till ny roll]
                </li>
                <ul>
                    <li>
                        Ange benämning. Den måste vara unik för hela systemet.
                    </li>
                    <li>
                        Ange beskrivning. Det gör det lättare för exempelvis valberedning och hitta lämpliga personer.
                    </li>
                    <li>
                        Ange Typ, det vill säga om det är en "Organisationsroll" eller "Verksamhetsroll"
                    </li>
                    <ul> 
                        <li>
                            En verksamhetsroll är kopplad till den verksamhet den befinner sig i.
                        </li>
                        <li>
                            En Organisationsroll placeras förslagsvis i en Organisatorisk enhet som kallas "Organisation". Den bör ligga högt upp i organisationsträdet.<br>
                            Lämpliga organisationsroller är Försalingsordförande, Församlingskassör. Dessa roller kan finnas i många verksamheter.
                        </li>
                    </ul>
                </ul>
                <li>
                    Uppdatering av roll görs genom att klicka på <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/>
                </li>
                <ul> 
                    <li>
                        Benämningen kan ändras till något nytt, men som fortfarande är unikt i systemet. Ändringen får genomslag på alla pltaser den används.
                    </li>
                    <li>
                        Beskrivningen kan ändras.                        
                    </li>
                    <li>
                        Även Typ kan ändras men der bör göras med mycket stor eftertanke.
                    </li>
                </ul>
                <li>
                    Borttag av roll är endast möjlig om rollen inte är kopplad till någon typ av organisatorisk enhet. Om <img src="/<?php echo SARON_URI;?>app/images/delete.png" title="Radera"/> är synlig kan rollen tas bort.
                </li>
                <li>
                    Koppling av roll till en typ av organisatorisk enhet görs genom att klicka på <img src="/<?php echo SARON_URI;?>app/images/pos.png" title="Inga roller"/> eller <img src="/<?php echo SARON_URI;?>app/images/haspos.png" title="Roller"/> 
                </li>
                <ul> 
                    <li>
                        Lägg till en ny koppling genom att klicka på [ + Lägg till en ny koppling till en roll.]
                    </li>
                    <li>
                        Ange sorteringsordning. Sorteringsordningen påverkar listordningen i rapporter.
                    </li>
                    <li>
                        Ta bort en koppling genom att klicka på <img src="/<?php echo SARON_URI;?>app/images/delete.png" title="Radera"/><br>
                        Rollern finnsar. Det är bara kopplingen som försvinner.
                    </li>
                </ul>
            </ul>
        </ul>
        <H3>Organisationsenheter </H3>
        <ul>    
            Listan innehåller de typer av organisationsenheter som definerats. Till varje typ av organisationsenhet kan en uppsättning roller kopplas. 
            <ul>
                <li>
                    Lägg till ny enhetstyp genom att klicka på [ + Lägg till en ny typ av organisatorisk enhet.]
                </li>
                <ul>
                    <li>
                        Ange benämning. Den måste vara unik för hela systemet.
                    </li>
                    <li>
                        Ange om den organisatorsika enhetstypen ska kunna ha bemanning.
                    </li>
                    <li>
                        Ange om den organisatorsika enhetstypen ska kunna ha underenheter. 
                    </li>
                    OBS: Om någon av de två ovanstående valen ändras från ja till nej så förloras den visuella kopplingen till Bemanningen eller Underenheterna. Det är inte rekommenderbart att göra så.
                    <li>
                        Ange beskrivning. Det gör det lättare för exempelvis valberedning och hitta lämpliga personer.
                    </li>
                </ul>
                <li>
                    Uppdatering av organisatorisk enhetstyp görs genom att klicka på <img src="/<?php echo SARON_URI;?>app/images/edit.png" title="ändra"/>
                </li>
                <ul> 
                    <li>
                        Benämningen kan ändras till något nytt, men som fortfarande är unikt i systemet. Ändringen får genomslag på alla pltaser den används.
                    </li>
                    <li>
                        Ange om den organisatorsika enhetstypen ska kunna ha bemanning.
                    </li>
                    <li>
                        Ange om den organisatorsika enhetstypen ska kunna ha underenheter. 
                    </li>
                    OBS: Om någon av de två ovanstående valen ändras från ja till nej så förloras den visuella kopplingen till Bemanningen eller Underenheterna. Det är inte rekommenderbart att göra så.
                    <li>
                        Uppdatera beskrivning.
                    </li>
                </ul>
                <li>
                    Borttag av roll är endast möjlig om organisatorisk enhetstyp inte är kopplad till någon roll. Om <img src="/<?php echo SARON_URI;?>app/images/delete.png" title="Radera"/> är synlig kan den organisatorisk enhetstyp tas bort.
                </li>
                <li>
                    Koppling av en typ av organisatorisk enhet till en roll görs genom att klicka på <img src="/<?php echo SARON_URI;?>app/images/haschild.png" title="Organisation"/>
                    eller <img src="/<?php echo SARON_URI;?>app/images/child.png" title="Organisation"/> 
                </li>
                <ul> 
                    <li>
                        Lägg till en ny koppling genom att klicka på [ + Koppla roll till typ av organisatorisk enhet.]
                    </li>
                    <li>
                        Ta bort en koppling genom att klicka på <img src="/<?php echo SARON_URI;?>app/images/delete.png" title="Radera"/><br>
                        Den organisatoriska enheten finns kvar. Det är bara kopplingen som försvinner.
                    </li>
                </ul>
            </ul>
        </ul>
        <H3>Medlemsstatus</H3>
        Tabellen beskriver vilka medlemsstatus som finns. 
        <ul>    
            <ul>
                <li>
                    Beskrivningstexten kan uppdateras
                </li>
            </ul>
        </ul>
        <H3>Beslut om organisation</H3>
        <ul>
            I samband med beslut om förändrad organisation. Görs en ny version av organisationen.
            <ul>
                <li>
                En ny versison av organisationen skapas genom att man klickar på knappen [+ Skapa ny version av organisationen] längt upp till höger.
                </li>
                <ul> 
                    <li>
                        Datum för beslutstillfället fylls i
                    </li>
                    <li>
                        En beskrivning av beslutsmötet läggs till. Ex Församlingens årsmöte
                    </li>                    
                    <li>
                        Följande kommer då att hända
                    </li>
                <ul> 
                    <li>
                        Samtliga personnamn som har status "Avstämd" eller "Funktionsansvar" som ligger under "Förslag" kommer att kopieras till "Senast beslutad". Se även menyn "Organisation & Bemanning"
                    </li>
                    <li>
                        När en ny version är skapad, är alla personer i beslutad organisation lika med dem i föreslagen organiation. Då försvinner alla status "Ny" i rapporten "Organisationskalender förslag (pdf-fil)"
                    </li>
                    <li>
                        "Organisationskalender beslutad (pdf)" uppdateras omedelbar i enlighet med förslaget.
                    </li>
                </ul>
                </ul>
            </ul>
        </ul>
    </ul>
   </body>
</html>        
