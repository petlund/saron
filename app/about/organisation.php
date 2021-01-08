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
    <H1>Om <?php echo NameOfRegistry;?></H1>
    <H2>Organisation - Inledning</H2>
    <ul>    
        För att kunna ändra i organisationsinformationen krävs separat behörighet. Endast personer som är definierade i medlemsregistret kan kopplas till uppdrag. 
        För att lägga till personer eller redigera kontaktuppgifter behövs speciell behörighet.<br>
        Grunden för förtroende uppdrag är medlemskap. Ibland kan det passa med att låta icke medlemmar ha förtroendeuppdrag.
    </ul>    
        
        
    <H2>Organisation - Översik över menyer</H2>
    <ul>    
        <H3>Översikt av undermenyerna till Organisation</H3>
        <ul>
            <ul>
                <li>
                    <b>Organisationskalender förslag (pdf-fil)</b><br>
                    Ger en rapport överförändringsförslag till nåsta beslutsmöte.<br>
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
                    <b>Typer av organisationsenheter</b><br>
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
        <ul>    
            <ul>
                <li>
                </li>
                <li>
                </li>
                <ul> 
                    <li>
                    </li>
                    <li>
                    </li>
                </ul>
                <li>
                </li>
                <li>
                </li>    
                <ul>
                    <li>
                    </li>
                    <li>    
                    </li>
                </ul>
                <li>
                </li>
            </ul>
        </ul>
    </ul>
   </body>
</html>        
