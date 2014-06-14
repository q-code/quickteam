<?php

// QuickTeam

$strSubject = $_SESSION[QT]['site_name']." - New password"; 

$strMessage = "
Please find here after your username and password to access the board {$_SESSION[QT]['site_name']}.
You can change this password in the Profile section.

Username: %s
Password: %s

Regards,
The webmaster of {$_SESSION[QT]['site_name']}
{$_SESSION[QT]['site_url']}/qte_index.php
";