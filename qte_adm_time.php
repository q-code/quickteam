<?php

/**
* PHP version 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license. If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @package    QuickTeam
* @author     Philippe Vandenberghe <info@qt-cute.org>
* @copyright  2013 The PHP Group
* @version    3.0 build:20140608
*/

session_start();
require 'bin/qte_init.php';
include Translate(APP.'_adm.php');
include Translate(APP.'_zone.php');

if ( sUser::Role()!='A' ) die(Error(13));

// INITIALISE

$oVIP->selfurl = 'qte_adm_time.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_settings'].'</span><br/>Application time';
$oVIP->exiturl = 'qte_adm_region.php';
$oVIP->exitname = $L['Adm_settings'].' '.$L['Adm_region'];

if ( PHP_VERSION_ID<50200 )
{
  $oHtml->PageMsgAdm(PHP_VERSION_ID,'Sorry...<br/>Your webhost must support PHP 5.2 or next to allow application time change.');
  exit;
}

// Default time zone setting

if ( !isset($_SESSION[QT]['defaulttimezone']) ) $_SESSION[QT]['defaulttimezone']=date_default_timezone_get();

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  $strTZI = strip_tags(trim($_POST['tzi']));
  if ( !in_array($strTZI,DateTimeZone::listIdentifiers()) ) $error='Unknown time zone identifier ['.$strTZI.']';

  // Save change. Attention, it can be a empty string (i.e. No change in the timezone)

  if ( empty($error) )
  {
  $_SESSION[QT]['defaulttimezone']=$strTZI;
  $oDB->Exec('DELETE FROM '.TABSETTING.' WHERE param="defaulttimezone"');
  $oDB->Exec('INSERT INTO '.TABSETTING.' VALUES ("defaulttimezone", "'.$_SESSION[QT]['defaulttimezone'].'", "1")');
  }

  // Exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);
}

// --------
// HTML START
// --------

include APP.'_adm_inc_hd.php';

echo '<form method="post" action="',$oVIP->selfurl,'">
<h2 class="subtitle">Application time zone</h2>
<table class="t-data">
';
if ( $_SESSION[QT]['defaulttimezone']!='' )
{
date_default_timezone_set($_SESSION[QT]['defaulttimezone']); // restore application timezone
}
$oDT = new DateTime();
echo '<tr class="t-data">
<td class="headfirst" style="width:150px;">Time</td>
<td style="width:225px;">',$oDT->format('H:i:s'),'</td>
<td><span class="help">',$oDT->format(DATE_ATOM),'</span></td>
</tr>
<tr class="t-data">
<td class="headfirst" style="width:150px;">Identifier</td>
<td style="width:225px;"><input type="text" id="tzi" name="tzi" size="32" value="',$oDT->getTimezone()->getName(),'"/></td>
<td><span class="help">Time zone identifier</td>
</tr>
</table>
';
echo '<p style="margin:0 0 5px 0;text-align:center"><input type="submit" name="ok" value="',$L['Save'],'"/></p>
</form>
';

$arrGroup = array('AFRICA'=>'Africa','ANTARCTICA'=>'Antarctica','ARCTIC'=>'Arctic','AMERICA'=>'America','ASIA'=>'Asia','ATLANTIC'=>'Atlantic','AUSTRALIA'=>'Australia','EUROPE'=>'Europe','INDIAN'=>'Indian','PACIFIC'=>'Pacific','OTHERS'=>'Universal & others');
$strGroup='EUROPE';
$arrTZI = array();
if ( isset($_GET['group']) )
{
  $strGroup = strtoupper(strip_tags(trim($_GET['group'])));
  if ( !array_key_exists($strGroup,$arrGroup) ) $strGroup='ALL';
}
switch($strGroup)
{
case 'ALL':
  $arrTZI = DateTimeZone::listIdentifiers();
  break;
case 'OTHERS':
  $arrTZI = DateTimeZone::listIdentifiers();
  foreach ($arrTZI as $i=>$str) {
  foreach ($arrGroup as $s=>$strName) {
  if ( $s==strtoupper(substr($str,0,strlen($s))) ) unset($arrTZI[$i]);
  }}
  break;
default:
  foreach (DateTimeZone::listIdentifiers() as $str)
  {
  if ( $strGroup==strtoupper(substr($str,0,strlen($strGroup))) ) $arrTZI[]=$str;
  }
  break;
}

echo '
<h2 class="subtitle">Identifiers</h2>
<table class="t-data" style="border-spacing:10px">
<tr>
<tr class="t-data">
<td class="blanko right bold">Search by zone</td>
<td class="blanko bold">Time zone identifiers</td>
</tr>
<tr class="t-data">
<td class="blanko right">
';
foreach ($arrGroup as $strKey=>$strValue) echo '<a href="qte_adm_time.php?group=',$strKey,'">',$strValue,'</a><br/>';
echo '<br/><a href="qte_adm_time.php?group=ALL">Show all</a>';
echo '</td>
<td class="blanko"><div class="scrollmessage small">',implode('<br/>',$arrTZI),'</div></td>
</tr>
</table>
';

echo '<p>&laquo; <a href="',$oVIP->exiturl,'">',$oVIP->exitname,'</a></p>';

// HTML END

include APP.'_adm_inc_ft.php';