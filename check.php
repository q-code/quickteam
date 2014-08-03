<?php

// QuickTeam 3.0 build:20140608

// --------
// HTML start
// --------
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en" lang="en">
<head>
<title>QuickTeam installation checker</title>
<meta charset="utf-8" />
<meta name="description" content="QuickTeam" />
<meta name="keywords" content="quickteam,users management,qt-cute,OpenSource" />
<meta name="author" content="qt-cute.org" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5" />
<style type="text/css">
body {margin-top:6px;padding:0;color:#000000;background-color:#EEEEEE}
div,th,td,h1,h2,p,a,select,input,textarea,fieldset {font-family:Verdana, Arial, sans-serif}
h1 {margin-top:10px;margin-bottom:5px;font-size:14pt;font-weight:bold}
h2 {margin-top:10px;margin-bottom:5px;font-size:12pt;font-weight:bold}
div,th,td,p,a,select,input,textarea,fieldset {font-size:9pt;text-decoration:none}
div.banner {margin:0;padding:6px;background:#0C4C8C;background:linear-gradient(to bottom, #0C4C8C 20%, #156AC2)}
div.page {width:550px;margin:5px auto;background-color:#f2f2f2}
div.pagecontent {padding:5px 10px 10px 10px; border:1px solid #AAAAAA}
h1 {color:#999999}
p {margin:3px 0 0 0; color:#666666}
p.endcheck {margin:5px 0 0 0; padding:5px 7px; background-color:#dfdfdf; color:#444444; border-radius:5px}
span.ok {color:#00aa00; background-color:inherit}
span.nok {color:#ff0000; background-color:inherit}
p.footer {width:100%; text-align:right;font-size:8pt}
div.pagecontent a {display:inline-block; padding:7px; background-color:#4242FF; color:#ffffff}
div.pagecontent a:hover {background-color:#0000ff; text-decoration:none}
</style>
</head>
';

echo '<body>

<!-- PAGE CONTROL -->
<div class="page">
<!-- PAGE CONTROL -->

<!-- HEADER BANNER -->
<div class="banner">
<img src="admin/qte_logo.gif" width="150" height="50" style="border-width:0" alt="QuickTeam" title="QuickTeam" />
</div>
<!-- END HEADER BANNER -->

<!-- BODY MAIN -->
<div class="pagecontent">
<!-- BODY MAIN -->
';

// --------
// 1 CONFIG
// --------

echo '<p style="margin:0;text-align:right">QuickTeam v3.0 build:20140608</p>';

echo '<h1>Checking your configuration</h1>';

$error = '';

// 1 file exist

  echo '<p>Checking installed files... ';

  if ( !file_exists('bin/config.php') ) $error .= 'File <b>config.php</b> is not in the <b>bin</b> directory. Communication with database is impossible.<br />';
  if ( !file_exists('bin/qte_init.php') ) $error .= 'File <b>qte_init.php</b> is not in the <b>bin</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/qt_lib_sys.php') ) $error .= 'File <b>qt_lib_sys.php</b> is not in the <b>bin</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/qt_lib_txt.php') ) $error .= 'File <b>qt_lib_txt.php</b> is not in the <b>bin</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/qte_fn_base.php') ) $error .= 'File <b>qte_fn_base.php</b> is not in the <b>bin</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/qte_fn_html.php') ) $error .= 'File <b>qte_fn_html.php</b> is not in the <b>bin</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/class/qt_class_db.php') ) $error .= 'File <b>qt_class_db.php</b> is not in the <b>bin/class</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/class/qt_abstracts.php') ) $error .= 'File <b>qt_abstracts.php</b> is not in the <b>bin/class</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/class/qte_class_sec.php') ) $error .= 'File <b>qte_class_sec.php</b> is not in the <b>bin/class</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/class/qte_class_item.php') ) $error .= 'File <b>qte_class_item.php</b> is not in the <b>bin/class</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/class/qte_class_user.php') ) $error .= 'File <b>qte_class_user.php</b> is not in the <b>bin/class</b> directory. Application cannot start.<br />';
  if ( !file_exists('bin/class/qte_class_vip.php') ) $error .= 'File <b>qte_class_vip.php</b> is not in the <b>bin/class</b> directory. Application cannot start.<br />';

  if ( empty($error) )
  {
  echo '<span class="ok">Main files found.</span></p>';
  }
  else
  {
  die('<span class="nok">'.$error.'</span></p>');
  }

// 2 config is correct

  echo '<p>Checking config.php... ';

  include 'bin/config.php';

  if ( !isset($qte_dbsystem) ) $error .= 'Variable <b>$qte_dbsystem</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( !isset($qte_host) ) $error .= 'Variable <b>$qte_host</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( !isset($qte_database) ) $error .= 'Variable <b>$qte_database</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( !isset($qte_prefix) ) $error .= 'Variable <b>$qte_prefix</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( !isset($qte_user) ) $error .= 'Variable <b>$qte_user</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( !isset($qte_pwd) ) $error .= 'Variable <b>$qte_pwd</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( !isset($qte_port) ) $error .= 'Variable <b>$qte_port</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( !isset($qte_dsn) ) $error .= 'Variable <b>$qte_dsn</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';

  if ( !empty($error) )  die('<span class="nok">'.$error.'</span>');

  // check db type
  if ( !in_array($qte_dbsystem,array('pdo.mysql','mysql4','mysql','sqlsrv','mssql','pg','ibase','sqlite','db2','oci')) ) die('Unknown db type '.$qte_dbsystem);
  // check other values
  if ( empty($qte_host) ) $error .= 'Variable <b>$qte_host</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( empty($qte_database) ) $error .= 'Variable <b>$qte_database</b> is not defined in the file <b>bin/config.php</b>. Communication with database is impossible.<br />';
  if ( !empty($error) ) die($error);

  if ( empty($error) )
  {
  echo '<span class="ok">Done.</span></p>';
  }
  else
  {
  die('<span class="nok">'.$error.'</span></p>');
  }

// 3 test db connection

  echo '<p>Connecting to database (connection type: ',$qte_dbsystem,')... ';

  include 'bin/class/qt_class_db.php';

  $oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd);

  if ( empty($oDB->error) )
  {
  echo '<span class="ok">Done.</span></p>';
  }
  else
  {
  die('<span class="nok">Connection with database failed.<br />Check that server is up and running.<br />Check that the settings in the file <b>bin/config.php</b> are correct for your database.</span></p>');
  }

// end CONFIG tests

  echo '<p class="endcheck">Configuration tests completed successfully.</p>';

// --------
// 2 DATABASE
// --------

$error = '';

echo '
<h1>Checking your database design</h1>
';

// 1 setting table

  echo '<p>Checking setting table... ';

  $oDB->Query('SELECT setting FROM '.$qte_prefix.'qtesetting WHERE param="version"');
  if ( !empty($oDB->error) ) die("<br /><font color=red>Problem with table ".$qte_prefix."qtesetting</font>");
  $row = $oDB->Getrow();
  $strVersion = $row['setting'];

  echo '<span class="ok">Table [',$qte_prefix,'qtesetting] exists. Version is ',$strVersion,'.</span>';
  if ( !in_array(substr($strVersion,0,3),array('2.4','2.5','3.0')) ) die('<span class="nok">But data in this table refers to an incompatible version (must be version 2.5).</span></p>');
  echo '</p>';

// 2 domain table

  echo '<p>Checking domain table... ';

  $oDB->Query('SELECT count(id) as countid FROM '.$qte_prefix.'qtedomain');
  if ( !empty($oDB->error) ) die("<br /><font color=red>Problem with table ".$qte_prefix."qtedomain</font>");
  $row = $oDB->Getrow();
  $intCount = $row['countid'];
  echo '<span class="ok">Table [',$qte_prefix,'qtedomain] exists. ',$intCount,' domains found.</span></p>';

// 3 section table

  echo '<p>Checking section table...';

  $oDB->Query('SELECT count(id) as countid FROM '.$qte_prefix.'qtesection');
  if ( !empty($oDB->error) ) die("<br /><font color=red>Problem with table ".$qte_prefix."qtesection</font>");
  $row = $oDB->Getrow();
  $intCount = $row['countid'];
  echo '<span class="ok">Table [',$qte_prefix,'qtesection] exists. ',$intCount,' sections found.</span></p>';

// 4 status table

  echo '<p>Checking status table...';

  $oDB->Query('SELECT count(id) as countid FROM '.$qte_prefix.'qtestatus');
  if ( !empty($oDB->error) ) die("<br /><font color=red>Problem with table ".$qte_prefix."qtestatus</font>");
  $row = $oDB->Getrow();
  $intCount = $row['countid'];
  echo '<span class="ok">Table [',$qte_prefix,'qtestatus] exists. ',$intCount,' statuses found.</span></p>';

// 5 user table

  echo '<p>Checking user table... ';

  $oDB->Query('SELECT count(id) as countid FROM '.$qte_prefix.'qteuser');
  if ( !empty($oDB->error) ) die("<br /><font color=red>Problem with table ".$qte_prefix."qteuser</font>");
  $row = $oDB->Getrow();
  $intCount = $row['countid'];
  echo '<span class="ok">Table [',$qte_prefix,'qteuser] exists. ',$intCount,' users found.</span></p>';

// end DATABASE tests

  echo '<p class="endcheck">Database tests completed successfully.</p>';

// --------
// 3 LANGUAGE AND SKIN
// --------

$error = '';

echo '
<h1>Checking language and skin options</h1>
';

  echo '<p>Files... ';

  $oDB->Query('SELECT setting FROM '.$qte_prefix.'qtesetting WHERE param="language"');
  $row = $oDB->Getrow();
  $str = $row['setting'];
  if ( empty($str) ) $error .= 'Setting <b>language</b> is not defined in the setting table. Application can only work with english.<br />';
  if ( !file_exists("language/$str/qte_main.php") ) $error .= "File <b>qte_main.php</b> is not in the <b>language/xxxx</b> directory.<br />";
  if ( !file_exists("language/$str/qte_adm.php") ) $error .= "File <b>qte_adm.php</b> is not in the <b>language/xxxx</b> directory.<br />";
  if ( !file_exists("language/$str/qte_reg.php") ) $error .= "File <b>qte_reg.php</b> is not in the <b>language/xxxx</b> directory.<br />";
  if ( !file_exists("language/$str/qte_zone.php") ) $error .= "File <b>qte_zone.php</b> is not in the <b>language/xxxx</b> directory.<br />";
  if ( $str!='english' )
  {
  if ( !file_exists("language/english/qte_main.php") ) $error .= "File <b>qte_main.php</b> is not in the <b>language/english</b> directory. English language is mandatory.<br />";
  if ( !file_exists("language/english/qte_adm.php") )  $error .= "File <b>qte_adm.php</b> is not in the <b>language/english</b> directory. English language is mandatory.<br />";
  if ( !file_exists("language/english/qte_reg.php") )  $error .= "File <b>qte_reg.php</b> is not in the <b>language/english</b> directory. English language is mandatory.<br />";
  if ( !file_exists("language/english/qte_zone.php") ) $error .= "File <b>qte_zone.php</b> is not in the <b>language/english</b> directory. English language is mandatory.<br />";
  }

  $oDB->Query('SELECT setting FROM '.$qte_prefix.'qtesetting WHERE param="skin_dir"');
  $row = $oDB->Getrow();
  $str = $row['setting']; if ( substr($str,0,5)!='skin/' ) $str = 'skin/'.$str;

  if ( empty($str) ) $error .= 'Setting <b>skin</b> is not defined in the setting table. Application will not display correctly.<br />';
  if ( !file_exists("$str/qte_main.css") ) $error .= "File <b>qte_main.css</b> is not in the <b>skin/xxxx</b> directory.<br />";
  if ( !file_exists("skin/default/qte_main.css") ) $error .= "File <b>qte_main.css</b> is not in the <b>skin/default</b> directory. Default skin is mandatory.<br />";

  if ( empty($error) )
  {
  echo '<span class="ok">Ok.</span>';
  }
  else
  {
  echo '<span class="nok">',$error,'</span>';
  }

  echo '</p>';

// end LANGUAGE AND SKIN tests

  echo '<p class="endcheck">Language and skin files tested.</p>';

// --------
// 4 ADMINISTRATION TIPS
// --------

$error = '';

echo '
<h1>Administration tips</h1>
';

// 1 admin email

  echo '<p>Email setting... ';

  $oDB->Query('SELECT setting FROM '.$qte_prefix.'qtesetting WHERE param="admin_email"');
  $row = $oDB->Getrow();
  $strMail = $row['setting'];
  if ( empty($strMail) )
  {
  $error .= 'Administrator e-mail is not yet defined. It\'s mandatory to define it!';
  }
  else
  {
  if ( !preg_match("/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i",$strMail) ) $error .= 'Administrator e-mail format seams incorrect. Please check it';
  }

  if ( !empty($error) ) echo '<span class="nok">'.$error.'</span></p>';
  echo '<span class="ok">Done.</span></p>';
  $error = '';

// 2 admin password

  echo '<p>Security check... ';

  $oDB->Query('SELECT pwd FROM '.$qte_prefix.'qteuser WHERE id=1');
  $row = $oDB->Getrow();
  $strPwd = $row['pwd'];
  If ( $strPwd==sha1('Admin') ) $error .= 'Administrator password is still the initial password. It\'s recommended to change it !<br />';

  if ( empty($error) )
  {
  echo '<span class="ok">Done.</span></p>';
  }
  else
  {
  echo '<span class="nok">',$error,'</span></p>';
  }
  $error = '';

// 3 site url

  echo '<p>Site url... ';

  $oDB->Query('SELECT setting FROM '.$qte_prefix.'qtesetting WHERE param="site_url"');
  $row = $oDB->Getrow();
  $strText = trim($row['setting']);
  if ( substr($strText,0,7)!="http://" && substr($strText,0,8)!="https://" )
  {
    $error .= 'Site url is not yet defined (or not starting by http://). It\'s mandatory to define it !<br />';
  }
  else
  {
    $strURL = ( empty($_SERVER['SERVER_HTTPS']) ? 'http://' : 'https://' ).$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $strURL = substr($strURL,0,-10);
    if ( $strURL!=$strText ) $error .= 'Site url seams to be different that the current url. Please check it<br />';
  }

  if ( empty($error) )
  {
  echo '<span class="ok">Done.</span></p>';
  }
  else
  {
  echo '<span class="nok">',$error,'</span></p>';
  }
  $error = '';

// 4 avatar/upload folder permission

  echo '<p>Folder permissions... ';

  if ( !is_dir('picture') )
  {
    $error .= 'Directory <b>picture</b> not found.<br />Please create this directory and make it writeable (chmod 777) if you want to allow picture.<br />';
  }
  else
  {
    if ( !is_readable('picture') ) $error .= 'Directory <b>picture</b> is not readable. Change permissions (chmod 777) if you want to allow picture.<br />';
    if ( !is_writable('picture') ) $error .= 'Directory <b>picture</b> is not writable. Change permissions (chmod 777) if you want to allow picture.<br />';
  }

  if ( !is_dir('document') )
  {
    $error .= '>Directory <b>document</b> not found.<br />Please create this directory and make it writeable (chmod 777) if you want to allow uploads<br />';
  }
  else
  {
    if ( !is_readable('document') ) $error .= 'Directory <b>document</b> is not readable. Change permissions (chmod 777) if you want to allow uploads<br />';
    if ( !is_writable('document') ) $error .= 'Directory <b>document</b> is not writable. Change permissions (chmod 777) if you want to allow uploads<br />';
  }

  if ( !empty($error) ) echo '<span class="nok">',$error,'</span></p>';
  echo '<span class="ok">Done.</span></p>';
  $error = '';

echo '<p class="endcheck">Administration tips completed.</p>';

// --------
// 5 END
// --------

echo '
<h1>Result</h1>
';
echo '<p class="endcheck">The checker did not found blocking issues in your configuration.<br />';

  $oDB->Query('SELECT setting FROM '.$qte_prefix.'qtesetting WHERE param="board_offline"');
  $row = $oDB->Getrow();
  $strOff = $row['setting'];
  if ( $strOff=='1' ) echo 'Your board seams well installed, but is currently off-line.<br />Log as Administrator and go to the Administration panel to turn your board on-line.<br />';

echo '</p>
<br/>
<p><a href="qte_index.php">Go to QuickTeam</a></p>';

// --------
// HTML END
// --------

echo '
<!-- END BODY MAIN -->
</div>
</table>

<p class="footer">powered by <a href="http://www.qt-cute.org" class="footer_copy">QT-cute</a></p>

<!-- END PAGE CONTROL -->
</div>

</body>
</html>';