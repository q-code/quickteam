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
if ( !cVIP::CanViewCalendar() ) { $oHtml->PageMsg(11); return; }
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_calendar.css" />';

// ---------
// INITIALISE
// ---------

$s = -1;
$a = 1; if ( memGet('sys_domains')>200 ) $a = 0; // all sections at once. If too much members, default becomes this section only
$v = 'birthdate';

QThttpvar('s a','int int',true,true,false);

if ( $a==0 && $s<0 ) $a=1;

$intYear   = date('Y');
if ( isset($_GET['y']) ) $intYear = strip_tags($_GET['y']);
$intYearN  = $intYear;

$intMonth  = date('n');
if ( isset($_GET['m']) ) $intMonth = strip_tags($_GET['m']);
$intMonthN = $intMonth+1; if ( $intMonthN>12 ) { $intMonthN=1; ++$intYearN; }
$strMonth  = '0'.$intMonth; $strMonth = substr($strMonth,-2,2);
$strMonthN = '0'.$intMonthN; $strMonthN = substr($strMonthN,-2,2);
$arrWeekCss = array(1=>'monday','tuesday','wednesday','thursday','friday','saturday','sunday'); // system weekdays reference

$dToday = mktime(0,0,0,date('n'),date('j'),date('Y'));

if ( $intYear>2100 ) die('Invalid year');
if ( $intYear<1900 ) die('Invalid year');
if ( $intMonth>12 ) die('Invalid month');
if ( $intMonth<1 ) die('Invalid month');

$oVIP->selfurl = 'qte_calendar.php';
$oVIP->selfname = $L['Calendar'];

if ( QTE_WEEKSTART>1 )
{
  $L['dateDDD'] = ArraySwap($L['dateDDD'],intval(QTE_WEEKSTART)-1);
  $L['dateDD'] = ArraySwap($L['dateDD'],intval(QTE_WEEKSTART)-1);
  $L['dateD'] = ArraySwap($L['dateD'],intval(QTE_WEEKSTART)-1);
  $arrWeekCss = ArraySwap($arrWeekCss,intval(QTE_WEEKSTART)-1);
}

// --------
// LIST OF ITEMS
// --------

$arrEvents = array();
$arrEventsN = array();
$intEvents = 0;
$intEventsN = 0;

if ( $a==0 ) { $strInner='INNER JOIN '.TABS2U.' s ON u.id=s.userid WHERE s.sid='.$s.' AND'; } else { $strInner='WHERE'; }
switch($oDB->type)
{
// Select 2 months
case 'pdo.mysql': $oDB->Query('SELECT u.id,u.username,u.firstname,u.lastname,u.role,u.'.$v.' FROM '.TABUSER.' u '.$strInner.' (SUBSTRING(u.'.$v.',5,2)="'.$strMonth.'" OR SUBSTRING(u.'.$v.',5,2)="'.$strMonthN.'")'); break;
case 'pdo.ibase':
case 'ibase': $oDB->Query('SELECT u.id,u.username,u.firstname,u.lastname,u.role,u.'.$v.' FROM '.TABUSER.' u '.$strInner.' (SUBSTRING(u.'.$v.' FROM 5 FOR 2)="'.$strMonth.'" OR SUBSTRING(u.'.$v.' FROM 5 FOR 2)="'.$strMonthN.'")'); break;
case 'pdo.sqlite':
case 'sqlite':
case 'pdo.db2':
case 'db2':
case 'pdo.oci':
case 'oci': $oDB->Query('SELECT u.id,u.username,u.lastname,u.role,u.'.$v.' FROM '.TABUSER.' u '.$strInner.' (SUBSTR(u.'.v.',5,2)="'.$strMonth.'" OR SUBSTR(u.'.$v.',5,2)="'.$strMonthN.'")'); break;
default: $oDB->Query('SELECT u.id,u.username,u.firstname,u.lastname,u.role,u.'.$v.' FROM '.TABUSER.' u '.$strInner.' (SUBSTRING(u.'.$v.',5,2)="'.$strMonth.'" OR SUBSTRING(u.'.$v.',5,2)="'.$strMonthN.'")');
}
while($row=$oDB->Getrow())
{
  if ( strlen($row[$v])===8 )
  {
    $strM = substr($row[$v],4,2); $intM = intval($strM);
    $strD = substr($row[$v],6,2); $intD = intval($strD);
    if ( $strM==$strMonth ) { $arrEvents[$intD][]=$row; ++$intEvents; }
    if ( $strM==$strMonthN ) { $arrEventsN[$intD]=$row; ++$intEventsN; }
  }
}

// --------
// HTML START
// --------

$strStatuses='';
foreach(memGet('sys_statuses') as $key=>$arr) $strStatuses .= 'var status_'.$key.'="'.(isset($arr['statusname']) ? $arr['statusname'] : 'null' ).'"; var icon_'.$key.'="'.(isset($arr['icon']) ? $arr['icon'] : 'null' ).'";'.PHP_EOL;

$oHtml->scripts_jq[] = '
$(function() {
  $(".ajaxmouseover").mouseover(function() {
    if (lastid==this.id) return;
    $.post("qte_j_user.php",
      {id:this.id,dir:"'.QTE_DIR_PIC.'",sep_name:" "},
      function(data) { if ( data.length>0 ) document.getElementById("userinfo").innerHTML=addstatus(data); });
    lastid = this.id;
  });
});
';
$oHtml->scripts_end[] = '<script type="text/javascript">
var lastid = "0";
function addstatus(data)
{
var i = data.lastIndexOf("<status ");
if ( i<0 ) return data;
var status = data.substr(i+8,1);
'.$strStatuses.'
return data.replace("<status "+status+"/>", "<p class=\"small\"><img src=\"'.$_SESSION[QT]['skin_dir'].'/" + eval("icon_"+status) + "\" alt=\"" + status + "\" style=\"vertical-align:middle\" /> " + eval("status_"+status) + "</p>");
}
</script>
';

include 'qte_inc_hd.php';

echo '
<p id="sectiondesc">',$oVIP->selfname,'</p>
';

// PREPARE MAIN CALENDAR

$dCurrentDate = mktime(0,0,0,$intMonth,1,$intYear);
$dMainDate = $dCurrentDate;
$dFirstDay = mktime(0,0,0,$intMonth,1,$intYear);
if ( date('l',$dFirstDay)!='Monday' )
{
  $dFirstDay = strtotime('-1 week',$dFirstDay);
  $dFirstMonday = strtotime('next monday',$dFirstDay);
  // correction for php 4.2
  // find last monday
  for ($i=date('j',$dFirstDay);$i<32;++$i)
  {
    $dI = mktime(0,0,0,date('n',$dFirstDay),$i,date('Y',$dFirstDay));
    if ( !$dI )
    {
    if ( date('N',$dI)==1 ) $dFirstMonday = $dI;
    }
  }
  $dFirstDay = $dFirstMonday;
}
$intShiftWeek = intval(date('W',$dFirstDay)); if ( $intShiftWeek>53 ) $intShiftWeek==1;

// DISPLAY MAIN CALENDAR

echo '<table class="hidden" style="width:700px">',PHP_EOL,'<tr>',PHP_EOL;
echo '<td>';
if ( date('n',$dCurrentDate)>1 )
  echo '<a class="button" href="',$oVIP->selfurl,'?m=',(date('n',$dCurrentDate)-1),'">&lt;</a>';
  else
  echo '<a class="button disabled">&lt;</a>';
echo '<p id="thismonth">',$L['dateMMM'][date('n',$dCurrentDate)].' '.date('Y',$dCurrentDate),'</p>';
if ( date('n',$dCurrentDate)<12 )
  echo '<a class="button" href="',$oVIP->selfurl,'?m=',(date('n',$dCurrentDate)+1),'">&gt;</a>';
  else
  echo '<a class="button disabled">&gt;</a>';
echo '</td>',PHP_EOL;
echo '<td class="right" style="vertical-align:middle">',PHP_EOL;
echo '<form method="get" action="',Href(),'">',PHP_EOL;
echo '<input type="hidden" name="s" id="s" value="',$s,'"/>',PHP_EOL;
echo '<input type="hidden" name="y" id="y" value="',$intYear,'"/> ',PHP_EOL;
if ( $s>=0 )
{
echo $L['Show'],' <select name="a" onchange="document.getElementById(\'ok\').click();">';
echo '<option value="0"',($a==0 ? QSEL : ''),'>',$L['Calendar_show_this'],'</option>';
echo '<option value="1"',($a==1 ? QSEL : ''),'>',$L['Calendar_show_all'],'</option>';
echo '</select>';
}
echo ' ',$L['Month'],' <select name="m" onchange="document.getElementById(\'ok\').click();">';
for ($i=1;$i<13;++$i)
{
echo '<option',($i==date('n') ? ' class="bold"' : ''),' value="',$i,'"',($i==$intMonth ? QSEL : ''),'>',$L['dateMMM'][$i],'</option>',PHP_EOL;
}
echo '</select> <input type="submit" name="ok" id="ok" value="',$L['Ok'],'"/>
<script type="text/javascript">document.getElementById("ok").style.display="none";document.getElementById("ok").value="";</script>
</form>
</td>
</tr>
</table>
';

echo '<table class="t-data"  style="width:700px">',PHP_EOL;
echo '<tr class="t-data">',PHP_EOL;
echo '<th class="week date_first">&nbsp;</th>';
for ($i=1;$i<8;++$i)
{
  echo '<th class="date',($i==7 ? ' date_last' : ''),'" style="width:95px">',$L['dateDDD'][$i],'</th>',PHP_EOL;
}
echo '</tr>',PHP_EOL;

  $iShift=0;
  for ($intWeek=0;$intWeek<6;++$intWeek)
  {
    echo '<tr class="t-data">',PHP_EOL;
    echo '<td class="week">',$intShiftWeek,'</td>'; ++$intShiftWeek;
    for ($intDay=1;$intDay<8;++$intDay)
    {
      $d = strtotime("+$iShift days",$dFirstDay); ++$iShift;
      $intShiftYear = date('Y',$d);
      $intShiftMonth = date('n',$d);
      $intShiftDay = date('j',$d);

      // date number
      if ( date('n',$dCurrentDate)==date('n',$d) )
      {
        echo '<td class="date"',(date('z',$dToday)==date('z',$d) ? ' id="zone_today">' : '>');
        echo '<p class="datenumber">',$intShiftDay,'</p><p class="dateicon">&nbsp;';
        // date item
        if ( isset($arrEvents[$intShiftDay]) )
        {
          $intDayEvents = 0;
          Foreach ($arrEvents[$intShiftDay] as $intKey => $arrValues)
          {
            ++$intDayEvents;
            $intAge = 0;
            if ( strpos($_SESSION[QT]['fields_c'],'age')!==FALSE ) $intAge = $intShiftYear - intval(substr($arrValues[$v],0,4));
            if ( $intDayEvents<4 )
            {
              echo '<a class="ajaxmouseover small" id="user',$arrValues['id'],'" href="',Href('qte_user.php'),'?id=',$arrValues['id'],'" title="'.UserFirstLastName($arrValues,' ',$arrValues['username']).'">',(empty($arrValues['firstname']) ? '('.$arrValues['username'].')' : $arrValues['firstname']),'</a>',(empty($intAge) ? '' : ' ('.$intAge.')'),'<br />';
            }
            else
            {
              echo '<a class="ajaxmouseover small" id="user',$arrValues['id'],'" href="',Href('qte_user.php'),'?id=',$arrValues['id'],'"><img class="ico i-user" src="',$_SESSION[QT]['skin_dir'],'/ico_user_p_1.gif" alt="-" title="',$arrValues['username'],(empty($intAge) ? '' : ' ('.$intAge.')'),'"/></a> ';
            }
            if ( $intDayEvents>9 ) { echo '...'; break; }
          }
        }
      }
      else
      {
        echo '<td class="date_out">';
        echo '<p class="datenumber">',$intShiftDay,'</p><p class="ajaxmouseover">&nbsp;';
      }
      echo '</p></td>',PHP_EOL;
    }
    echo '</tr>',PHP_EOL;
    if ( $intShiftMonth>$intMonth && $intShiftYear==$intYear ) break;
  }

echo '</table>',PHP_EOL;

// DISPLAY SUBDATA

echo '<table class="hidden">',PHP_EOL,'<tr class="hidden">',PHP_EOL;
echo '<td class="hidden" style="width:220px">',PHP_EOL;

  // PREPARE NEXT MONTH

  $dCurrentDate = mktime(0,0,0,$intMonthN,1,$intYearN);
  $dFirstDay = mktime(0,0,0,$intMonthN,1,$intYearN);
  if ( date("l",$dFirstDay)!='Monday' )
  {
    $dFirstDay = strtotime('-1 week',$dFirstDay);
    $dFirstMonday = strtotime('next monday',$dFirstDay);
    // correction for php 4.2
    // find last monday
    for ($i=date('j',$dFirstDay);$i<32;++$i)
    {
      $dI = mktime(0,0,0,date('n',$dFirstDay),$i,date('Y',$dFirstDay));
      if ( !$dI )
      {
      if ( date('N',$dI)==1 ) $dFirstMonday = $dI;
      }
    }
    $dFirstDay = $dFirstMonday;
  }

  // DISPLAY NEXT MONTH

  echo '<h2>',$L['dateMMM'][date('n',$dCurrentDate)],'</h2>';
  echo '<table class="t-data"  style="width:200px">',PHP_EOL;
  echo '<tr class="t-data">',PHP_EOL;
  for ($intDay=1;$intDay<8;++$intDay)
  {
  echo '<th class="date_next">',$L['dateD'][$intDay],'</th>',PHP_EOL;
  }
  echo '</tr>',PHP_EOL;

    $iShift=0;
    for ($intWeek=0;$intWeek<6;++$intWeek)
    {
      echo '<tr class="t-data">',PHP_EOL;
      for ($intDay=1;$intDay<8;++$intDay)
      {
        $d = strtotime("+$iShift days",$dFirstDay); ++$iShift;
        $intShiftYear = date('Y',$d);
        $intShiftMonth = date('n',$d);
        $intShiftDay = date('j',$d);
        // date number
        if ( date('n',$dCurrentDate)==date('n',$d) )
        {
          echo '<td class="date_next"',(date('z',$dToday)==date('z',$d) ? ' id="zone_today"' : ''),'>';
          if ( isset($arrEventsN[$intShiftDay]) )
          {
            echo '<a class="date_next" href="',Href('qte_calendar.php'),'?m=',$intMonthN,'">',$intShiftDay,'</a> ';
          }
          else
          {
            echo $intShiftDay;
          }
        }
        else
        {
          echo '<td class="date_out_next">';
          echo $intShiftDay;
        }
        echo '</td>',PHP_EOL;
      }
      echo '</tr>',PHP_EOL;
      if ( $intShiftMonth>$intMonthN && $intShiftYear==$intYearN ) break;
    }

  echo '</table>',PHP_EOL;

echo '</td>',PHP_EOL;
echo '<td class="hidden" style="width:220px">',PHP_EOL;

  // DISPLAY STATS

  echo '<h2>',$L['Total'],'</h2>',PHP_EOL;
  echo '<p><span class="bold">',$L['dateMMM'][date('n',$dMainDate)],'</span><br/>',($intEvents>0 ? '<a href="'.Href('qte_cal_list.php').'?y='.$intYear.'&amp;m='.$intMonth.'">' : ''),L('User',$intEvents),($intEvents>0 ? '</a>' : ''),'</p>',PHP_EOL;
  echo '<p><span class="bold">',$L['dateMMM'][date('n',$dCurrentDate)],'</span><br/>',($intEventsN>0 ? '<a href="'.Href('qte_cal_list.php').'?y='.$intYearN.'&amp;m='.$intMonthN.'">' : ''),L('User',$intEventsN),($intEventsN>0 ? '</a>' : ''),'</p>',PHP_EOL;

echo '</td>',PHP_EOL;
echo '<td class="hidden" style="width:220px">',PHP_EOL;

  // DISPLAY Preview

  echo '<h2>',$L['Information'],'</h2>';
  echo '<script type="text/javascript"></script><noscript>Your browser does not support JavaScript</noscript>';
  echo '<div style="width:250px" id="userinfo"></div>';

echo '</td>',PHP_EOL;

echo '<td class="hidden">&nbsp;</td>',PHP_EOL;

echo '</tr>',PHP_EOL,'</table>',PHP_EOL;

if ( isset($_SESSION[QT]['section']) ) {
if ( $_SESSION[QT]['section']>=0 ) {
  echo '<p class="tablecommand"><a class="tablecommand" href="',Href('qte_section.php'),'?s=',$_SESSION[QT]['section'],'" title="Tabular view">Tabular view</a></p>';
}}

// --------
// HTML END
// --------

include 'qte_inc_ft.php';