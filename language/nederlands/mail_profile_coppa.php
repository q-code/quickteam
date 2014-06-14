<?php

// QuickTeam

$strSubject = $_SESSION[QT]['site_name']." - Profiel verandering"; 

$strMessage = "
Beste ouder/beschermer,

Wij delen u mee dat uw kinderen (gebruikersnaam: %s) zijn/haar profiel op de website {$_SESSION[QT]['site_name']} heeft veranderd.
U kunt deze informatie in de pagina Profiel herzien.

Om tot de website toegang te hebben zult u zijn/haar gebruikersnaam en wachtwoord nodig hebben (verzendt naar u in een vorige post).

Vriendelijke groeten,
Webmaster van {$_SESSION[QT]['site_name']}
{$_SESSION[QT]['site_url']}/qte_index.php

---- COPPA ----
Deze e-mail is verzonden naar u omdat uw kinderen heeft verklaard dat hij/ze jonger is dan 13 jaar oud en dit team overeenkomstig het Akte is van de Bescherming van de Privacy van de Kinderen Online (COPPA). Om meer over COPPA te weten te komen, gelieve deze pagina te bezoeken http://www.ftc.gov/opa/1999/10/childfinal.htm. Gelieve ook de Privacy pagina van het Team te lezen: {$_SESSION[QT]['site_url']}/qte_privacy.php
---------------
";