<?php

$L['Ok'] = 'Ok';
$L['Save'] = 'Bewaar';
$L['Done'] = 'Gemaakt';
$L['Back'] = '&lt;&nbsp;Vorige';
$L['Next'] = 'Volgende&nbsp;&gt;';
$L['Finish'] = 'Eind';
$L['Restart'] = 'Nieuw begin';
$L['Board_email'] = 'Systeem beheer e-mail';
$L['User'] = 'Gebruiker';
$L['Password'] = 'Watchwoord';
$L['Installation'] = 'Installatie';
$L['Install_db'] = 'Installatie van tabels';
$L['Connection_db'] = 'Parameters van aansluiting aan de database';
$L['Database_type'] = 'Database type';
$L['Database_host'] = 'Database host';
$L['Database_name'] = 'Database naam';
$L['Database_user'] = 'Gebruiker (user/wachtwoord)';
$L['Table_prefix'] = 'Tabel prefixe';
$L['Create_tables'] = 'Tabels in uw database [%s] make';
$L['Htablecreator'] = 'Als database gebruiker geen recht heeft om de tabel te make, u can here een andere login/wachtwoord geven.';
$L['Upgrade'] = 'Als u een upgrade van versie 2.x maakt, hier zijn uw vorige parameters. Click Volgende.';
$L['Upgrade2'] = 'Als u een upgrade van versie 2.x maakt, u moet NIET tabel installeren. Ga naar de volgende stap.';
$L['End_message'] = 'U kunt de team als Admin bereiken';
$L['Check_install'] = 'Installatie controleren';
$L['S_connect'] = 'Aansluiting succesvol...';
$L['E_connect'] = '<span class="bold">Probleem met aansluiting aan de database [%s] op server [%s]</span><br /><br />Mogelige reden :<br />&raquo;&nbsp;De naam van de host is verkeerd.<br />&raquo;&nbsp;De naam van de database is verkeerd.<br />&raquo;&nbsp;De login (of wachtwoord) is verkeerd.';
$L['S_save'] = 'Save succesvol...';
$L['E_save'] = '<br /><br /><span class="bold">Probleem om in de map /bin/ te schrijven</span><br /><br />Mogelige reden :<br />&raquo;&nbsp;Het bestand /bin/config.php is afwezig.<br />&raquo;&nbsp;Het bestand /bin/config.php is read-only.<br /><br />';

$L['Default_setting'] = 'parameters toegevoegd.';
$L['Default_domain'] = 'domain toegevoegd.';
$L['Default_section'] = 'sectie toegevoegd.';
$L['Default_user'] = 'gebruikers toegevoegd.';
$L['Default_status'] = 'statuten toegevoegd.';

$L['N_install'] = 'Installatie process be&euml;indigd';
$L['S_install'] = 'Installatie succesvol...';
$L['E_install'] = '<span class="bold">Probleem om de tabel [%s] in de database [%s] te maken</span><br /><br />Mogelige reden :<br />&raquo;&nbsp;De tabel bestaat al (u can dit uitwissen of een prefixe gebruiken).<br />&raquo;&nbsp;De gebruiker [%s] heeft geen recht om tabel te maken.<br /><br />';
$L['S_install_exit'] = 'Installatie is succesvol....<br /><br />Vergeet niet:<br />- Systeem on-line zetten (Administratie sectie)<br />- Administrator wachtword veranderen<br />- De map /install/ uitwissen<br /><br />';

$L['Help_1'] = '<span class="bold">Database type</span>: De type van uw database.<br/><br/><span class="bold">Database host</span> (server naam): Als de webserver en de database op de zelfde server staan, gebruik "localhost". Met SQLExpress gebruik ".\SQLExpress".<br/><br/>Met Oracle, SQLite en Firebird, moet  de database samen met de host zijn. Bvb.:<br/>Oracle "//localhost:1521/mydb"<br/>SQLite "/opt/database/mydb.sq3"<br/>Firebird "/path/to/mydb.fdb"<br/><br/><span class="bold">Database naam</span>: Geef hier de naam van uw database. Met Oracle, SQLite en Firebird laat het leeg.<br/><br/><span class="bold">Tabel prefixe</span>: Als u hebt meerdere applicaties op de zelfde database, u can een prefixe voor de tabellen geven.<br/><br/><span class="bold">Gebruiker</span>: Gebruiker die in uw database update/delete/insert acties can maken.<br/><br/><span class="bold">Wat betref database en logins</span>: De database en de gebruiker MOET bestaan. Dit installatie zal alleen tabellen maken in de bestaande database. Dit zal niet de database of de database user-account creeren.<br/>';
$L['Help_2'] = 'Als u een upgrade van versie 1.x maakt, u moet NIET tabel installeren. Ga naar de volgende stap.<br/>';
$L['Help_3'] = '<span class="bold">Contact e-mail</span>: Het is noodzakelijk om een contact e-mail te geven. Dit is zichtbaar in de pagina: Gebruiksvoorwaarden.<br/>';