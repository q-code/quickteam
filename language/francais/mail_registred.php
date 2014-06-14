<?php

// QuickTeam 2.5

$strSubject = $_SESSION[QT]['site_name']." - Bienvenue"; 

$strMessage = "
Bienvenue sur {$_SESSION[QT]['site_name']}...

Veuillez trouver ci-après votre nom d'utilisateur et mot de passe pour le site web {$_SESSION[QT]['site_name']}.
Vous pouvez changer ce mot de passe dans votre page Profil.

Utilisateur: %s
Mot de passe: %s

Salutations,
Le webmaster de {$_SESSION[QT]['site_name']}
{$_SESSION[QT]['site_url']}/qte_index.php
";