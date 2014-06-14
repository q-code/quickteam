<?php

$L['Ok'] = 'Ok';
$L['Save'] = 'Save';
$L['Done'] = 'Done';
$L['Back'] = '&lt;&nbsp;Back';
$L['Next'] = 'Next&nbsp;&gt;';
$L['Finish'] = 'Finish';
$L['Restart'] = 'Restart';
$L['Board_email'] = 'Board e-mail';
$L['User'] = 'User';
$L['Password'] = 'Password';
$L['Installation'] = 'Installation';
$L['Install_db'] = 'Creation of the tables';
$L['Connection_db'] = 'Connection parametres for the database';
$L['Database_type'] = 'Database type';
$L['Database_host'] = 'Database host (host/port/dsn)';
$L['Database_name'] = 'Database name';
$L['Database_user'] = 'Database login (user/password)';
$L['Table_prefix'] = 'Table prefix';
$L['Create_tables'] = 'Create tables into database [%s]';
$L['Htablecreator'] = 'If the database user is not granted to create table, you can enter here an alternate login.';
$L['End_message'] = 'You can access the board as Admin.';
$L['Upgrade'] = 'If you upgrade from version 2.x, your previous settings are displayed here. Continue to the next step.';
$L['Upgrade2'] = 'If you upgrade from version 2.x, you do NOT have to install the tables. Continue to the next step.';
$L['Check_install'] = 'Check installation';
$L['S_connect'] = 'Connection successful...';
$L['E_connect'] = '<b>Problem to connect database [%s] on server [%s]</b><br /><br />Possible causes:<br />&raquo;&nbsp;Host is incorrect.<br />&raquo;&nbsp;Database name is incorrect.<br />&raquo;&nbsp;User login (or password) is incorrect.';
$L['S_save'] = 'Save successful...';
$L['E_save'] = '<b>Problem to write into /bin/ folder</b><br /><br />Possible causes:<br />&raquo;&nbsp;File /bin/config.php is missing.<br />&raquo;&nbsp;File /bin/config.php is read-only.';

$L['Default_setting'] = 'default settings inserted.';
$L['Default_domain'] = 'default domain inserted.';
$L['Default_section'] = 'default section inserted.';
$L['Default_user'] = 'default users inserted.';
$L['Default_status'] = 'default status inserted.';

$L['N_install'] = 'This ends the installation procedure.';
$L['S_install'] = 'Installation successful...';
$L['E_install'] = '<b>Problem to install the table [%s] into dabase [%s]</b><br /><br />Possible causes:<br />&raquo;&nbsp;Table already exists (delete existing table or use prefix).<br />&raquo;&nbsp;The user [%s] is not granted to create table.<br />';
$L['S_install_exit'] = 'Installation have been successfully completed.<br /><br />Don\'t forget to :<br />- Turn the board on-line<br />- <b>Change your admin password</b><br />- Delete the /install/ directory<br /><br />';

$L['Help_1'] = '<b>Database type</b>: The database type you are using.<br/><br/><b>Database host</b> (server name): If the database server is on the same server as the webserver, use "localhost". Let the port empty unless you are using PostgreSQL (port 5432). Let the DSN empty unless you are using ODBC connection.<br/><br/><b>Database name</b>: Type here the name of your database. For Oracle Express use "//localhost/XE".<br/><br/><b>Table prefix</b>: If you have several boards in the same database, you can add a prefix to the tablename.<br/><br/><b>Database user</b>: User granted to perform update/delete actions in your database. The second administrator is not mandatory.<br/><br/><b>About database and logins</b>: Be sure that database or users are EXISTING. The script will just add tables in an existing database. It will not create database nor create database accounts.<br/>';
$L['Help_2'] = '<b>Database tables</b>: This will install the tables in your database. If you are making an update, you must skip this step.<br/>';
$L['Help_3'] = '<b>Board e-mail</b>: It\'s recommended to provide a contact e-mail address. This adress is visible in the page: General conditions.<br/>';