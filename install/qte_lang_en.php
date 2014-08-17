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
$L['Database_host'] = 'Database host';
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
$L['E_connect'] = '<span class="bold">Problem to connect database [%s] on server [%s]</span><br /><br />Possible causes:<br />&raquo;&nbsp;Host is incorrect.<br />&raquo;&nbsp;Database name is incorrect.<br />&raquo;&nbsp;User login (or password) is incorrect.';
$L['S_save'] = 'Save successful...';
$L['E_save'] = '<span class="bold">Problem to write into /bin/ folder</span><br /><br />Possible causes:<br />&raquo;&nbsp;File /bin/config.php is missing.<br />&raquo;&nbsp;File /bin/config.php is read-only.';

$L['Default_setting'] = 'default settings inserted.';
$L['Default_domain'] = 'default domain inserted.';
$L['Default_section'] = 'default section inserted.';
$L['Default_user'] = 'default users inserted.';
$L['Default_status'] = 'default status inserted.';

$L['N_install'] = 'This ends the installation procedure.';
$L['S_install'] = 'Installation successful...';
$L['E_install'] = '<span class="bold">Problem to install the table [%s] into dabase [%s]</span><br /><br />Possible causes:<br />&raquo;&nbsp;Table already exists (delete existing table or use prefix).<br />&raquo;&nbsp;The user [%s] is not granted to create table.<br />';
$L['S_install_exit'] = 'Installation have been successfully completed.<br /><br />Don\'t forget to :<br />- Turn the board on-line<br />- <span class="bold">Change your admin password</span><br />- Delete the /install/ directory<br /><br />';

$L['Help_1'] = '<span class="bold">Database type</span>: The database type you are using.<br/><br/><span class="bold">Database host</span> (server name): If the database server is on the same server as the webserver, use "localhost". For SQLExpress use ".\SQLExpress".<br/><br/>For Oracle, SQLite and Firebird, the host must include the path to the database. Example:<br/>Oracle "//localhost:1521/mydb"<br/>SQLite "/opt/database/mydb.sq3"<br/>Firebird "/path/to/mydb.fdb"<br/><br/><span class="bold">Database name</span>: Type here the name of your database.<br/>For Oracle, SQLite or Firebird let this empty.<br/><br/><span class="bold">Table prefix</span>: If you have several boards in the same database, you can add a prefix to the tablename.<br/><br/><span class="bold">Database user</span>: User granted to perform update/delete actions in your database.<br/><br/><span class="bold">About database and logins</span>: Be sure that database or users are EXISTING. The script will just add tables in an existing database. It will not create database nor create database accounts.<br/>';
$L['Help_2'] = '<span class="bold">Database tables</span>: This will install the tables in your database. If you are making an update, you must skip this step.<br/>';
$L['Help_3'] = '<span class="bold">Board e-mail</span>: It\'s recommended to provide a contact e-mail address. This adress is visible in the page: General conditions.<br/>';