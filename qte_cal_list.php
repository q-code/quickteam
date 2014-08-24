<?php

/**
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Team
 * @package    QuickTeam
 * @author     Philippe Vandenberghe <info@qt-cute.org>
 * @copyright  2014 The PHP Group
 * @version    3.0 build:20140608
 */

session_start();
require 'bin/qte_init.php';
if ( !sUser::CanView('V4') ) die(Error(11));
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_calendar.css" />';

// --------
// INITIALISE
// --------

$v = 'birthdate';
$intYear   = intval(date('Y'));
if ( isset($_GET['y']) ) $intYear = intval(strip_tags($_GET['y']));
$intYearN  = $intYear;
$intMonth  = intval(date('n'));
if ( isset($_GET['m']) ) $intMonth = intval(strip_tags($_GET['m']));
$intMonthN = $intMonth+1; if ( $intMonthN>12 ) { $intMonthN=1; ++$intYearN; }
$strMonth  = '0'.$intMonth; $strMonth = substr($strMonth,-2,2);
$strMonthN = '0'.$intMonthN; $strMonthN = substr($strMonthN,-2,2);

$arrEvents = array();
$arrEventsN = array();
$intEvents = 0;
$intEventsN = 0;

if ( $intYear>2100 ) die('Invalid year');
if ( $intYear<1900 ) die('Invalid year');
if ( $intMonth>12 ) die('Invalid month');
if ( $intMonth<1 ) die('Invalid month');

$oVIP->selfurl = 'qte_cal_list.php';
$oVIP->selfname = $L['Calendar'];
$oVIP->exiturl = 'qte_calendar.php?y='.$intYear.'&amp;m='.$intMonth;
$oVIP->exitname = '&laquo; '.$L['Calendar'];

// --------
// HTML START
// --------

include 'qte_inc_hd.php';

// USERS

switch($oDB->type)
{
// Select month
case 'pdo.mysql': $oDB->Query('SELECT id,username,title,firstname,midname,lastname,emails,alias,picture,'.$v.' FROM '.TABUSER.' WHERE SUBSTRING('.$v.',5,2)="'.$strMonth.'" OR SUBSTRING('.$v.',5,2)="'.$strMonthN.'"'); break;
case 'pdo.ibase':
case 'ibase': $oDB->Query('SELECT id,username,title,firstname,midname,lastname,emails,alias,picture,'.$v.' FROM '.TABUSER.' WHERE SUBSTRING('.$v.' FROM 5 FOR 2)="'.$strMonth.'" OR SUBSTRING('.$v.' FROM 5 FOR 2)="'.$strMonthN.'"'); break;
case 'pdo.sqlite':
case 'sqlite':
case 'pdo.db2':
case 'db2':
case 'pdo.oci':
case 'oci': $oDB->Query('SELECT id,username,title,firstname,midname,lastname,emails,alias,picture,'.$v.' FROM '.TABUSER.' WHERE SUBSTR('.$v.',5,2)="'.$strMonth.'" OR SUBSTR('.$v.',5,2)="'.$strMonthN.'"'); break;
default: $oDB->Query('SELECT id,username,title,firstname,midname,lastname,emails,alias,picture,'.$v.' FROM '.TABUSER.' WHERE SUBSTRING('.$v.',5,2)="'.$strMonth.'" OR SUBSTRING('.$v.',5,2)="'.$strMonthN.'"');
}
while($row=$oDB->Getrow())
{
  if ( strlen($row[$v])==8 )
  {
    $strM = substr($row[$v],4,2); $intM = intval($strM);
    $strD = substr($row[$v],6,2); $intD = intval($strD);
    if ( $strM==$strMonth ) { $arrEvents[$intD][]=$row; ++$intEvents; }
    if ( $strM==$strMonthN ) { $arrEventsN[$intD][]=$row; ++$intEventsN; }
  }
}
ksort($arrEvents);
ksort($arrEventsN);

echo '<h1>',$L['Birthdays_calendar'],' ',$L['dateMMM'][$intMonth],' ',$intYear,'</h1>',PHP_EOL;

echo '<table class="t-item">',PHP_EOL;

if ( count($arrEvents)==0 )
{
  $bEmails=false;
  echo '<tr><td>',$L['None'],'</td>';
}
else
{
  $bEmails=true;
  foreach ($arrEvents as $intDay => $arrEvent) {
  foreach ($arrEvent as $intNum => $arrUser) {
  $oItem = new cItem($arrUser,true); // privatise
  echo '<tr>';
  echo '<td class="c-date">',$intDay,'</td>';
  echo '<td><a href="',Href('qte_user.php'),'?id=',$oItem->id,'">',UserFirstLastName($oItem,' ',$oItem->username),'</a>',(empty($oItem->age) ? '' : ' ('.$oItem->age.')'),'</td>';
  echo '<td>',QTdatestr($oItem->birthdate,'$','',true),'</td>';
  $arr = AsList($oItem->emails,$_SESSION[QT]['viewmode']=='c');
  foreach($arr as $intKey=>$strValue)
  {
  $arr[$intKey] = AsEmailImage(trim($strValue),'mail_'.$oItem->id.'_'.$intKey,true,QTE_JAVA_MAIL,array('class'=>'small'));
  }
  $str = implode(' ',$arr);
  echo '<td>',$str,'</td>';
  echo '<td class="c-picture">',( empty($oItem->picture) ? '&nbsp;' : AsImgPopup('usr_'.$oItem->id,QTE_DIR_PIC.$oItem->picture,'[i]') ),'</td>';
  echo '</tr>',PHP_EOL;
  }}
}
echo '</table>',PHP_EOL;

// link to mailing list
if ( $bEmails && sUser::IsStaff() ) echo '<p class="right" style="margin:5px 0"><a href="qte_email.php?q=bdm&amp;v=',$intMonth,'">',L('Emails'),'...</a></p>',PHP_EOL;

// Next MONTH

echo '<h1>',$L['Birthdays_calendar'],' ',$L['dateMMM'][$intMonthN],' ',$intYearN,'</h1>',PHP_EOL;

echo '<table class="t-item">',PHP_EOL;
if ( count($arrEventsN)==0 )
{
  $bEmails=false;
  echo '<tr><td>',$L['None'],'</td>';
}
else
{
  $bEmails=true;
  foreach ($arrEventsN as $intDay => $arrEvent) {
  foreach ($arrEvent as $intNum => $arrUser) {
    $oItem = new cItem($arrUser,true); // privatise
    echo '<tr>';
    echo '<td class="c-date">',$intDay,'</td>';
    echo '<td><a href="',Href('qte_user.php'),'?id=',$oItem->id,'">',UserFirstLastName($oItem),'</a>',(empty($oItem->age) ? '' : ' ('.$oItem->age.')'),'</td>';
    echo '<td>',QTdatestr($oItem->birthdate,'$','',true),'</td>';
    $arr = AsList($oItem->emails,$_SESSION[QT]['viewmode']=='c');
    foreach($arr as $intKey=>$strValue)
    {
    $arr[$intKey] = AsEmailImage(trim($strValue),'mail_'.$oItem->id.'_'.$intKey,true,QTE_JAVA_MAIL,array('class'=>'small'));
    }
    $str = implode(' ',$arr);
    echo '<td>',$str,'</td>';
    echo '<td class="c-picture">',AsImgPopup('usr_'.$oItem->id,QTE_DIR_PIC.$oItem->picture,'[i]'),'</td>';
    echo '</tr>',PHP_EOL;
  }}
}
echo '</table>',PHP_EOL;

// link to mailing list
if ( $bEmails && sUser::IsStaff() ) echo '<p class="right" style="margin:5px 0"><a href="qte_email.php?q=bdm&amp;v=',$intMonthN,'">',L('Emails'),'...</a></p>',PHP_EOL;

// HTML END

echo '<p><a href="',Href($oVIP->exiturl),'">',$oVIP->exitname,'</a></p>',PHP_EOL;

include 'qte_inc_ft.php';