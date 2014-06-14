<?php

// QuickTeam 3.0

$strSubject = $_SESSION[QT]['site_name']." - Welcome";

$strMessage = "
Welcome on the site {$_SESSION[QT]['site_name']}...

Please find here after your username and password to access the board {$_SESSION[QT]['site_name']}.
You can change this password in the Profile section.

Username: %s
Password: %s

Regards,
The webmaster of {$_SESSION[QT]['site_name']}
{$_SESSION[QT]['site_url']}/qte_index.php
";