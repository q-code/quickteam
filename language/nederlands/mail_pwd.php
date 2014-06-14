<?php

// QuickTeam

$strSubject = $_SESSION[QT]['site_name']." - Nieuwe watchwoord"; 

$strMessage = "
Gelieve te vinden hier na uw gebruikersnaam en wachtwoord om tot de team {$_SESSION[QT]['site_name']} toegang te hebben.
U kunt dit wachtwoord in de sectie Profiel veranderen.

Gebruikersnaam: %s
Wachtwoord: %s

Vriendelijke groeten,
Webmaster van {$_SESSION[QT]['site_name']}
{$_SESSION[QT]['site_url']}/qte_index.php
";