<?php

// QuickTeam

$strSubject = $_SESSION[QT]['site_name']." - Nieuwe foto"; 

$strMessage = "
Beste ouder/beschermer,

Wij delen u mee dat uw kinderen (login: %s) zijn/haar foto op het team {$_SESSION[QT]['site_name']} heeft veranderd.

Vriendelijke groeten,
Webmaster van {$_SESSION[QT]['site_name']}
{$_SESSION[QT]['site_url']}/qte_index.php

---- COPPA ----
Deze e-mail is verzonden naar u omdat uw kinderen heeft verklaard dat hij/ze jonger is dan 13 jaar oud en dit team overeenkomstig het Akte is van de Bescherming van de Privacy van de Kinderen Online (COPPA). Om meer over COPPA te weten te komen, gelieve deze pagina te bezoeken http://www.ftc.gov/opa/1999/10/childfinal.htm. Gelieve ook de Privacy pagina van het Team te lezen: {$_SESSION[QT]['site_url']}/qte_privacy.php
---------------
";