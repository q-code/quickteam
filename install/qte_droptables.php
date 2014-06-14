<?php

// QuickTeam 3.0 build:20140608

session_start();

// --------
// INITIALISE
// --------

include '../bin/class/qt_class_db.php';
include '../bin/config.php'; if ( $qte_dbsystem=='sqlite' ) $qte_database = '../'.$qte_database;

define('TABDOMAIN', $qte_prefix.'qtedomain');
define('TABSECTION', $qte_prefix.'qtesection');
define('TABUSER', $qte_prefix.'qteuser');
define('TABCHILD', $qte_prefix.'qtechild');
define('TABS2U', $qte_prefix.'qtes2u');
define('TABSTATUS', $qte_prefix.'qtestatus');
define('TABSETTING', $qte_prefix.'qtesetting');
define('TABINDEX', $qte_prefix.'qteindex');
define('TABDOC', $qte_prefix.'qtedoc');
define('TABLANG', $qte_prefix.'qtelang');

$strAppl  = 'QuickTeam 3.0';
include 'qte_lang_en.php';

// --------
// HTML START
// --------

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en" lang="en">
<head>
<title>Uninstalling ',$strAppl,'</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<link rel="stylesheet" href="../admin/qte_main.css"/>
</head>

<body>

<!-- PAGE CONTROL -->
<div class="qte_page">
<table class="qte_page" width="750"  style="margin:5px">
<tr class="qte_page">
<td class="qte_page">

<!-- HEADER BANNER -->
<div class="banner">
<div class="banner_in">
<img src="qte_logo.gif" width="175" height="50" style="border-width:0" alt="QuickTeam" title="QuickTeam"/>
</div>
</div>

<!-- BODY MAIN -->
<table width="100%"  style="border-style:solid;border-color:#AAAAAA;border-width:1px;">
<tr class="body">
<td class="body">
';

echo '<h2>1. Opening database connection...</h2>';
$oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd,$qte_port,$qte_dsn);
if ( !empty($oDB->error) ) die ('<p style="color:red">Connection with database failed.<br />Check that server is up and running.<br />Check that the settings in the file <b>bin/config.php</b> are correct for your database.</p>');

echo '<p>done</p>';

// SUBMITTED

if ( isset($_GET['a']) )
{
  switch ($_GET['a'])
  {
  case 'Drop ALL tables':
    echo ' Dropping Lang...'; $oDB->Query('DROP TABLE '.TABLANG); echo 'done.<br />';
    echo ' Dropping Team...'; $oDB->Query('DROP TABLE '.TABSECTION); echo 'done.<br />';
    echo ' Dropping Domain...'; $oDB->Query('DROP TABLE '.TABDOMAIN); echo 'done.<br />';
    echo ' Dropping User...'; $oDB->Query('DROP TABLE '.TABUSER); echo 'done.<br />';
    echo ' Dropping Child...'; $oDB->Query('DROP TABLE '.TABCHILD); echo 'done.<br />';
    echo ' Dropping S2U...'; $oDB->Query('DROP TABLE '.TABS2U); echo 'done.<br />';
    echo ' Dropping Status...'; $oDB->Query('DROP TABLE '.TABSTATUS); echo 'done.<br />';
    echo ' Dropping Setting...'; $oDB->Query('DROP TABLE '.TABSETTING); echo 'done.<br />';
    echo ' Dropping Index...'; $oDB->Query('DROP TABLE '.TABINDEX); echo 'done.<br />';
    echo ' Dropping Doc...'; $oDB->Query('DROP TABLE '.TABDOC); echo 'done.<br />';
    break;
  case 'Drop table Lang':
    echo ' Dropping Lang...';   $oDB->Query('DROP TABLE '.TABLANG);   echo 'done.<br />';
    break;
  case 'Drop table Team':
    echo ' Dropping Team...';   $oDB->Query('DROP TABLE '.TABSECTION);   echo 'done.<br />';
    break;
  case 'Drop table Domain':
    echo ' Dropping Domain...';  $oDB->Query('DROP TABLE '.TABDOMAIN);  echo 'done.<br />';
    break;
  case 'Drop table User':
    echo ' Dropping User...';    $oDB->Query('DROP TABLE '.TABUSER);    echo 'done.<br />';
    break;
  case 'Drop table Child':
    echo ' Dropping Child...';    $oDB->Query('DROP TABLE '.TABCHILD);    echo 'done.<br />';
    break;
  case 'Drop table S2U':
    echo ' Dropping S2U...';  $oDB->Query('DROP TABLE '.TABS2U);  echo 'done.<br />';
    break;
  case 'Drop table Status':
    echo ' Dropping Status...';  $oDB->Query('DROP TABLE '.TABSTATUS);  echo 'done.<br />';
    break;
  case 'Drop table Setting':
    echo ' Dropping Setting...'; $oDB->Query('DROP TABLE '.TABSETTING); echo 'done.<br />';
    break;
  case 'Drop table Index':
    echo ' Dropping Index...'; $oDB->Query('DROP TABLE '.TABINDEX); echo 'done.<br />';
    break;
  case 'Drop table Doc':
    echo ' Dropping Doc...'; $oDB->Query('DROP TABLE '.TABDOC); echo 'done.<br />';
    break;
  case 'Add table Lang':
    include 'qte_setup_lang.php'; echo $_GET['a'],' done'; break;
  case 'Add table Team':
    include 'qte_setup_section.php'; echo $_GET['a'],' done'; break;
  case 'Add table Domain':
    include 'qte_setup_domain.php'; echo $_GET['a'],' done'; break;
  case 'Add table User':
    include 'qte_setup_user.php'; echo $_GET['a'],' done'; break;
  case 'Add table Child':
    include 'qte_setup_child.php'; echo $_GET['a'],' done'; break;
  case 'Add table S2U':
    include 'qte_setup_s2u.php'; echo $_GET['a'],' done'; break;
  case 'Add table Status':
    include 'qte_setup_status.php'; echo $_GET['a'],' done'; break;
  case 'Add table Setting':
    include 'qte_setup_setting.php'; echo $_GET['a'],' done'; break;
  case 'Add table Index':
    include 'qte_setup_index.php'; echo $_GET['a'],' done'; break;
  case 'Add table Doc':
    include 'qte_setup_doc.php'; echo $_GET['a'],' done'; break;
  }
}

// Tables do drop

echo '<h2>2. Drop the tables</h2>';

echo '<form action="qte_droptables.php" method="get"><p>';
echo '<input type="submit" name="a" value="Drop ALL tables"/> from the database ',$qte_database,'<br /><br />';
echo '<input type="submit" name="a" value="Drop table Lang"/> ',TABLANG,'<br />';
echo '<input type="submit" name="a" value="Drop table Status"/> ',TABSTATUS,'<br />';
echo '<input type="submit" name="a" value="Drop table S2U"/> ',TABS2U,'<br />';
echo '<input type="submit" name="a" value="Drop table User"/> ',TABUSER,'<br />';
echo '<input type="submit" name="a" value="Drop table Child"/> ',TABCHILD,'<br />';
echo '<input type="submit" name="a" value="Drop table Team"/> ',TABSECTION,'<br />';
echo '<input type="submit" name="a" value="Drop table Domain"/> ',TABDOMAIN,'<br />';
echo '<input type="submit" name="a" value="Drop table Setting"/> ',TABSETTING,'<br />';
echo '<input type="submit" name="a" value="Drop table Index"/> ',TABINDEX,'<br />';
echo '<input type="submit" name="a" value="Drop table Doc"/> ',TABDOC,'<br /><br />';
echo '</p></form>';

// Tables do add

echo '<h2>3. Add tables</h2>';

echo '<form action="qte_droptables.php" method="get"><p>';
echo '<input type="submit" name="a" value="Add table Lang"/> ',TABLANG,'<br />';
echo '<input type="submit" name="a" value="Add table Status"/> ',TABSTATUS,'<br />';
echo '<input type="submit" name="a" value="Add table S2U"/> ',TABS2U,'<br />';
echo '<input type="submit" name="a" value="Add table User"/> ',TABUSER,'<br />';
echo '<input type="submit" name="a" value="Add table Child"/> ',TABCHILD,'<br />';
echo '<input type="submit" name="a" value="Add table Team"/> ',TABSECTION,'<br />';
echo '<input type="submit" name="a" value="Add table Domain"/> ',TABDOMAIN,'<br />';
echo '<input type="submit" name="a" value="Add table Setting"/> ',TABSETTING,'<br />';
echo '<input type="submit" name="a" value="Add table Index"/> ',TABINDEX,'<br />';
echo '<input type="submit" name="a" value="Add table Doc"/> ',TABDOC,'<br />';
echo '</p></form>';

echo '<p><a href="qte_setup.php">install &raquo;</a></p>';

// --------
// HTML END
// --------

echo '
<!-- END BODY MAIN -->
</td>
</tr>
</table>

<div class="footer_copy">
<span class="footer_copy">powered by <a href="http://www.qt-cute.org" class="footer_copy">QT-cute</a></span>
</div>

<!-- END PAGE CONTROL -->
</td>
</tr>
</table>
</div>

</body>
</html>';