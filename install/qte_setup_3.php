<?php

// QTE 3.0 build:20140608

session_start();

if ( !isset($_SESSION['qte_setup_lang']) ) $_SESSION['qte_setup_lang']='en';

include 'qte_lang_'.$_SESSION['qte_setup_lang'].'.php';
include '../bin/config.php'; if ( $qte_dbsystem=='sqlite' ) $qte_database = '../'.$qte_database;
if ( isset($qte_install) ) { define('QT','qte'.substr($qte_install,-1)); } else { define('QT','qte'); }
include '../bin/class/qt_class_db.php';
include '../bin/qte_fn_base.php';

function QTismail($str)
{
  if ( !is_string($str) ) die('QTismail: arg #1 must be a string');

  if ( $str!=trim($str) ) return false;
  if ( $str!=strip_tags($str) ) return false;
  if ( !preg_match("/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i",$str) ) return false;
  return true;
}

$strAppl     = 'QuickTeam 3.0';
$strPrevUrl  = 'qte_setup_2.php';
$strNextUrl  = 'qte_setup_4.php';
$strPrevLabel= $L['Back'];
$strNextLabel= $L['Next'];

// Read admin_email setting

$oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd,$qte_port,$qte_dsn);
define('TABSETTING', $qte_prefix.'qtesetting');
GetParam(true,'param="admin_email"');
if ( !isset($_SESSION[QT]['admin_email']) ) $_SESSION[QT][admin_email]='';

// --------
// HTML START
// --------

include 'qte_setup_hd.php';

// Submitted

if ( !empty($_POST['admin_email']) )
{
  if ( QTismail($_POST['admin_email']) )
  {
    $_SESSION[QT]['admin_email'] = $_POST['admin_email'];
    $oDB->Query('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['admin_email'].'" WHERE param="admin_email"');
    if ( empty($oDB->error) )
    {
    echo '<div class="setup_ok">',$L['S_save'],'</div>';
    }
    else
    {
    echo '<div class="setup_err">',sprintf ($L['E_connect'],$qte_database,$qte_host),'</div>';
    }
  }
  else
  {
  echo '<div class="setup_err">Invalid e-mail</div>';
  }
}

// Form

echo '<h2>',$L['Board_email'],'</h2>
<form method="post" name="install" action="qte_setup_3.php">
<table>
<tr>
<td>',$L['Board_email'],' <input type="email" name="admin_email" value="',$_SESSION[QT]['admin_email'],'" size="34" maxlength="100"/> <input type="submit" name="ok" value="',$L['Save'],'"/></td>
<td style="width:40%"><div class="setup_help">',$L['Help_3'],'</div></td>
</tr>
</table>
</form>
';

include 'qte_setup_ft.php';