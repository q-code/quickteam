<?php

// QuickTeam

$strSubject = $_SESSION[QT]['site_name']." - Profile mis à jour"; 

$strMessage = "
Cher parent/tuteur,

Nous vous informons que votre enfant (nom d'utilisateur: %s) a changé son profil sur le site {$_SESSION[QT]['site_name']}. Vous pouvez contrôler ces informations dans la page Profil.

Pour accéder au site web vous aurez besoin de son nom d'utilisatuer et mot de passe qui vous ont été communiqués dans un mail précédent.

Salutations,
Le webmaster de {$_SESSION[QT]['site_name']}
{$_SESSION[QT]['site_url']}/qte_index.php

---- COPPA ----
Ce mail vous est adressé parce que votre enfant nous a indiqué être âgé(e) de moins de 13 ans et parce que ce site applique les règles de COPPA (Children's Online Privacy Protection Act).
Pour en savoir plus sur le COPPA, visitez cette page: http://www.ftc.gov/opa/1999/10/childfinal.htm
Veuillez également prendre connaissance du règlement de ce site : {$_SESSION[QT]['site_url']}/qte_privacy.php
----------------
";