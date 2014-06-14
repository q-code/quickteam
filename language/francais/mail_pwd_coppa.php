<?php

// QuickTeam

$strSubject = $_SESSION[QT]['site_name']." - Nouveau mot de passe"; 

$strMessage = "
Cher parent/tuteur,

Nous vous informons que votre enfant a changé son mot de passe sur le site {$_SESSION[QT]['site_name']}.

Utilisateur: %s
Mot de passe: %s

Salutations,
Le webmaster de {$_SESSION[QT]['site_name']}
{$_SESSION[QT]['site_url']}/qte_index.php

---- COPPA ----
Ce mail vous est adressé parce que votre enfant nous a indiqué être âgé(e) de moins de 13 ans et parce que ce site applique les règles de COPPA (Children's Online Privacy Protection Act).
Pour en savoir plus sur le COPPA, visitez cette page: http://www.ftc.gov/opa/1999/10/childfinal.htm
Veuillez également prendre connaissance du règlement de ce site : {$_SESSION[QT]['site_url']}/qte_privacy.php
----------------
";