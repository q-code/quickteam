<?php

$L['Ok'] = 'Ok';
$L['Save'] = 'Sauver';
$L['Done'] = 'Termin&eacute;';
$L['Back'] = '&lt;&nbsp;Pr&eacute;c&eacute;dent';
$L['Next'] = 'Suivant&nbsp;&gt;';
$L['Finish'] = 'Termin&eacute;';
$L['Restart'] = 'Red&eacute;marrer';
$L['Board_email'] = 'E-mail administrateur';
$L['User'] = 'Utilisateur';
$L['Password'] = 'Mot de passe';
$L['Installation'] = 'Installation';
$L['Install_db'] = 'Cr&eacute;ation des tables';
$L['Connection_db'] = 'Parm&egrave;tres de connexion &agrave; la base de donn&eacute;e (BDD)';
$L['Database_type'] = 'Type de BDD';
$L['Database_host'] = 'H&ocirc;te de la BDD';
$L['Database_name'] = 'Nom de la BDD';
$L['Database_user'] = 'Utilisateur BDD (user/password)';
$L['Table_prefix'] = 'Pr&eacute;fixe des tables';
$L['Htablecreator'] = 'Si l\'utilisateur BDD n\'a pas le droit de cr&eacute;er des tables, vous pouvez sp&eacute;cifier ici un autre login.';
$L['Create_tables'] = 'Cr&eacute;er les tables dans la base de donn&eacute;e [%s]';
$L['End_message'] = 'Vous pouvez acc&eacute;der au team en tant qu\'Admin';
$L['Upgrade'] = 'Als u een upgrade van versie 2.x maakt, hier zijn uw vorige parameters. Click Volgende.';
$L['Upgrade2'] = 'Als u een upgrade van versie 2.x maakt, u moet NIET tabel installeren. Ga naar de volgende stap.';
$L['Check_install'] = 'Contr&ocirc;ler l\'installation';
$L['S_connect'] = 'Connexion r&eacute;ussie...';
$L['E_connect'] = '<span class="bold">Probl&egrave;me de connexion &agrave; la base de donn&eacute;e [%s] sur le serveur [%s]</span><br /><br />Causes possibles :<br />&raquo;&nbsp;Le nom du serveur est incorrect.<br />&raquo;&nbsp;La nom de la base de donn&eacute;e est incorrect.<br />&raquo;&nbsp;Le login (ou mot de passe) est incorrect.';
$L['S_save'] = 'Savegarde r&eacute;ussie...';
$L['E_save'] = '<br /><br /><span class="bold">Probl&egrave;me pour &eacute;crire dans le r&eacute;pertoire /bin/</span><br /><br />Causes possibles :<br />&raquo;&nbsp;Le fichier /bin/config.php est absent.<br />&raquo;&nbsp;Le fichier /bin/config.php est read-only.<br />';

$L['Default_setting'] = 'param&egrave;tres par d&eacute;faut ins&eacute;r&eacute;s.';
$L['Default_domain'] = 'domaine par d&eacute;faut ins&eacute;r&eacute;.';
$L['Default_section'] = 'section par d&eacute;faut ins&eacute;r&eacute;e.';
$L['Default_user'] = 'utilisateurs par d&eacute;faut ins&eacute;r&eacute;s.';
$L['Default_status'] = 'statuts par d&eacute;faut ins&eacute;r&eacute;s.';

$L['N_install'] = 'Ici se termine la proc&eacute;dure d\'installation.';
$L['S_install'] = 'Installation termin&eacute;e...';
$L['E_install'] = '<span class="bold">Probl&egrave;me pour cr&eacute;er la table [%s] dans la base de donn&eacute;e [%s]</span><br /><br />Causes possibles :<br />&raquo;&nbsp;La table existe d&eacute;j&agrave; (effacez-la ou utilisez un pr&eacute;fixe).<br />&raquo;&nbsp;L\'utilisateur [%s] n\'a pas le droit de cr&eacute;er des tables dans la base de donn&eacute;e.<br />';
$L['S_install_exit'] = 'L\'installation s\'est correctement d&eacute;roul&eacute;e...<br /><br />N\'oubliez pas de :<br /><br />- Mettre le forum en-ligne (Panneau d\'administration)<br />- Changer le mot de passe de l\'administrateur<br />- Effacer le r&eacute;pertoire /install/.<br /><br />';

$L['Help_1'] = '<span class="bold">Type de base de donn&eacute;e</span>: Indiquez le type de base de donn&eacute;e que vous utilisez.<br/><br/><span class="bold">H&ocirc;te de la BDD</span> (nom du serveur): Si la BDD est sur le m&ecirc;me serveur que le serveur web, utilisez "localhost". Pour SQLExpress utilisez ".\SQLExpress".<br/><br/>Pour Oracle, SQLite et Firebird, le host doit inclure le chemin vers la BDD. Exemple:<br/>Oracle "//localhost:1521/mydb"<br/>SQLite "/opt/database/mydb.sq3"<br/>Firebird "/path/to/mydb.fdb"<br/><br/><span class="bold">Nom de la BDD</span>: Indiquez ici le nom de votre base de donn&eacute;e.<br/>Pour Oracle, SQLite et Firebird laissez ceci vide.<br/><br/><span class="bold">Pr&eacute;fixe des tables</span>: Si vous avez plusieurs applications dans la m&ecirc;me BDD, vous pouvez ajouter un pr&eacute;fixe au nom des tables.<br/><br/><span class="bold">Utilisateur BDD</span>: L\'utilisateur ayant le droit d\'ajouter/modifier/effacer dans la base de donn&eacute;e.<br/><br/><span class="bold">Pour la BDD et les logins</span>: Veillez a ce que la base de donn&eacute;e et les utilisateurs EXISTENT. Ce script ne fait qu\'ajouter les tables dans votre BDD. Il ne va pas cr&eacute;er la base de donn&eacute;e ni les accomptes BDD.<br/>';

$L['Help_2'] = '<span class="bold">Database tables</span>: Ceci va installer les tables dans votre base de donn&eacute;e. Si vous proc&eacute;dez  &agrave; un upgrade, veillez sauter cette &eacute;tape.<br/>';
$L['Help_3'] = '<span class="bold">E-mail administrateur</span>: Il est recommand&eacute; de donner une adresse de contact. Cette adresse est visible dans la page Conditions g&eacute;n&eacute;rales.<br/>';