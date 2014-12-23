<?php

/**
* PHP versions 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license. If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @package    QuickTeam
* @author     Philippe Vandenberghe <info@qt-cute.org>
* @copyright  2014 The PHP Group
* @version    3.0 build:20141222
*/

session_start();
require 'bin/qte_init.php';
include Translate(APP.'_adm.php');

if ( sUser::Role()!='A' ) die($L['E_admin']);

// INITIALISE

$oVIP->selfurl = 'qte_adm_index.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_info'].'</span><br/>'.$L['Adm_status'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // check admin email and forum url
  if ( !QTismail($_SESSION[QT]['admin_email'],false) ) $error='Error';
  if ( strlen($_SESSION[QT]['site_url'])<8 ) $error='Error';
  if ( !empty($error) )
  {
    $strFile=GetLang().'sys_online_error.php';
    if ( file_exists($strFile) ) { $strMsg = include $strFile; } else { $strMsg = '<p>Missing admin e-mail or forum url...</p>'; }
    $oVIP->exiturl = 'qte_adm_site.php';
    $oVIP->exitname = $L['Adm_general'];
    $oHtml->PageMsgAdm(NULL,$strMsg);
  }

  if ( isset($_POST['offline']) ) {
  if ( $_POST['offline']=='1' || $_POST['offline']=='0' ) {
    $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_POST['offline'].'" WHERE param="board_offline"');
    $_SESSION[QT]['board_offline'] = $_POST['offline'];
  }}

  // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);
}

// --------
// HTML START
// --------

include APP.'_adm_inc_hd.php';

// BOARD OFFLINE

echo '<h2 class="subtitle">',$L['Adm_status'],'</h2>
<table class="t-data">
<tr class="t-data">
<td class="headfirst">',$L['Adm_status'],'</td>';
if ( $_SESSION[QT]['board_offline']=='0' )
{
  echo '<td style="width:100px;background-color:#AAFFAA;text-align:center">',$L['On_line'],'</td>';
}
else
{
  echo '<td style="width:100px;background-color:#FFAAAA;text-align:center">',$L['Off_line'],'</td>';
}
echo '<td style="text-align:right">
<form method="post" action="',$oVIP->selfurl,'">',$L['Change'],S,'
<select id="offline" name="offline" onchange="bEdited=true;" class="small">
<option value="0"',($_SESSION[QT]['board_offline']=='0' ? QSEL : ''),'>',$L['On_line'],'</option>
<option value="1"',($_SESSION[QT]['board_offline']=='1' ? QSEL : ''),'>',$L['Off_line'],'</option>
</select>&nbsp;<input class="small" type="submit" name="ok" value="',$L['Save'],'"/>
</form></td>
</tr>
</table>
';

// STATS

echo '<h2 class="subtitle">',L('Info'),'</h2>',PHP_EOL;

$oDB->Query('SELECT count(*) as countid FROM '.TABDOMAIN);
$row = $oDB->Getrow();
$intDomain = $row['countid'];

$oDB->Query('SELECT count(*) as countid FROM '.TABSECTION);
$row = $oDB->Getrow();
$intTeam = $row['countid'];

$oDB->Query('SELECT count(*) as countid FROM '.TABSECTION.' WHERE type="1"');
$row = $oDB->Getrow();
$intHidden = $row['countid'];

$oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE id>0');
$row = $oDB->Getrow();
$intUser = $row['countid'];

echo '<table class="t-data">',PHP_EOL;
echo '<tr class="t-data">';
echo '<td class="headfirst">',$L['Domains'],'/',$L['Sections'],'</td>';
echo '<td>',L('Domain',$intDomain),', ',L('Section',$intTeam),' <span class="small">(',$intHidden,S,$L['Hidden'],')</span>, <a href="qte_stats.php">',$L['Statistics'],'</a></td>';
echo '</tr>',PHP_EOL;
echo '<tr class="t-data">';
echo '<td class="headfirst">',$L['Users'],'</td>';

$oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE id>0');
$row = $oDB->Getrow();
$intUser = $row['countid'];
$oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE role="A"');
$row = $oDB->Getrow();
$intAdmin = $row['countid'];
$oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE role="M"');
$row = $oDB->Getrow();
$intMod = $row['countid'];

echo '<td>',L('User',$intUser),' <span class="small">(',L('Userrole_A',$intAdmin),', ',L('Userrole_M',$intMod),', ',L('User',($intUser-$intAdmin-$intMod)),')</span></td>';
echo '</tr>',PHP_EOL;
echo '</table>',PHP_EOL;

// PUBLIC ACCESS LEVEL

echo '<h2 class="subtitle">',$L['Public_access_level'],'</h2>',PHP_EOL;
echo '<table class="t-data">',PHP_EOL;
echo '<tr class="t-data">';
echo '<td class="headfirst">',$L['Visitors_can'],'</td>';
echo '<td>',$L['Pal'][$_SESSION[QT]['visitor_right']],' &middot; <a href="qte_adm_secu.php">',$L['Change'],'</a></td>';
echo '</tr>',PHP_EOL,'</table>',PHP_EOL;

// VERSIONS

$str='';
if ( file_exists('bin/phpinfo.php') ) $str .= ' &middot; <a href="bin/phpinfo.php">php info</a>';
if ( file_exists('qte_adm_const.php') ) $str .= ' &middot; <a href="qte_adm_const.php">php constants</a>';

echo '
<h2 class="subtitle">',$L['Version'],'</h2>
<table class="t-data">
<tr class="t-data">
<td class="headfirst">QuickTicket</td>
<td>',QTEVERSION,', <span class="small">database ',$_SESSION[QT]['version'],', sid ',QT,'</span></td>
</tr>
';
echo '<tr class="t-data">
<td class="headfirst">PHP</td>
<td>'.PHP_VERSION_ID.$str.'</td>
</tr>
</table>
';

// HTML END

include APP.'_adm_inc_ft.php';