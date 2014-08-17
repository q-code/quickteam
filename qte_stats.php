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
 * @package    QuickTeam
 * @author     Philippe Vandenberghe <info@qt-cute.org>
 * @copyright  2014 The PHP Group
 * @version    3.0 build:20140608
 */

session_start();
require_once 'bin/qte_init.php';
if ( !cVIP::CanViewCalendar() ) { $oHtml->PageMsg(11); return; }
include 'bin/qt_lib_graph.php';

// INITIALISE

$year = date('Y'); if ( intval(date('n'))<2 ) $year--;
$s = -1;
QThttpvar('s year','int int',true,true,false);

$oVIP->selfurl = 'qte_stats.php';
$oVIP->selfname = $L['Statistics'];

if ( $s>=0 ) { $strWhere = ' INNER JOIN '.TABS2U.' s ON u.id=s.userid WHERE u.id>0 AND u.firstdate>"1" AND s.sid='.$s; } else { $strWhere=' WHERE u.id>0 AND u.firstdate>"1"'; }

// FIRST/LAST DATE

$oDB->Query('SELECT min(u.firstdate) as startdate, max(u.firstdate) as lastdate FROM '.TABUSER.' u '.$strWhere);
$row = $oDB->Getrow();
if ( empty($row['startdate']) ) $row['startdate']=strval($year-1).'0101';
if ( empty($row['lastdate']) )  $row['lastdate']=strval($year).'1231';

$strFirstdate = QTdatestr($row['startdate'],'$','',false);
$strLastdate  = QTdatestr($row['lastdate'],'$','',false);

$intStartyear  = intval(date('Y',strtotime($row['startdate'])));
$intStartmonth = intval(date('n',strtotime($row['startdate'])));
$intEndyear    = intval(date('Y'));
$intEndmonth   = intval(date('n'));

$arrAllYears = array();
for ($i=$intEndyear;$i>=$intStartyear;$i--)
{
  $arrAllYears[$i]=$i;
}

$arrYears = array($year,$year-1);

// Initialise

$arrA = array(); // Abscise
$arrU = array(); // Users
$arrUs = array();// Users sum

foreach($arrYears as $intYear)
{
  $arrU[$intYear] = array();
  for ($i=1;$i<=12;++$i)
  {
  $arrA[$i]=$L['dateMM'][$i];
  $arrU[$intYear][$i]=null;
  }
  $arrUs[$intYear]=0;
}

// COUNT Users

foreach($arrYears as $intYear) {
for ($intBt=1;$intBt<=12;++$intBt) {

  // check limits (startdate/enddate)

  if ( $intYear<$intStartyear ) continue;
  if ( $intYear==$intStartyear ) { if ( $intBt<$intStartmonth ) continue; }
  if ( $intYear>=$intEndyear ) { if ( $intBt>$intEndmonth ) continue; }

  // compute per blocktime

  $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' u '.$strWhere.' AND  u.firstdate LIKE "'.($intYear*100+$intBt).'%"' );
  $row = $oDB->Getrow();
  $arrU[$intYear][$intBt] = intval($row['countid']);
  $arrUs[$intYear] += $arrU[$intYear][$intBt]; // total

}}

// --------
// HTML START
// --------

$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_graph.css" />';

include 'qte_inc_hd.php';

// USERS

$oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE id>0' );
$row = $oDB->Getrow();

$intUsers = $row['countid'];

echo '<h1>',$L['Statistics'],'</h1>
<h2>',$L['General_site'],'</h2>
<table class="t-data" style="width:370px;">
<tr>
<td class="headfirst">',$L['Users'],'</td>
<td>',$intUsers,'</td>
</tr>
<tr>
<td class="headfirst">',$L['Section_start_date'],'</td>
<td>',$strFirstdate,'</td>
</tr>
<tr>
<td class="headfirst">',$L['Last_registration'],'</td>
<td>',$strLastdate,'</td>
</tr>
</table>
';

$arrSections = GetSectionTitles(sUser::Role());

echo '
<form method="get" action="',Href(),'">
<p class="right" style="margin:2px">',$L['Year'],' <select name="year" id="year" class="small">',QTasTag($arrAllYears,$year),'</select>
',(count($arrSections)>0 ? '<select class="small" name="s" id="s"><option value="-1">'.$L['In_all_sections'].'</option>'.Sectionlist($s).'</select>' : '&nbsp;'),'
<input type="submit" name="ok" id="submit" class="small" value="',$L['Ok'],'" />
</p>
</form>
<h2>',$L['Items_per_month'],S,($s>=0 ? '('.$arrSections[$s].')' : ''),'</h2>';

// Display table header

echo '<table class="t-data">
<tr>
<td class="headfirst" style="width:85px">&nbsp;</td>
';
for ($i=1;$i<=12;++$i) { echo '<td class="headfirst" style="text-align:center;">',$L['dateMM'][$i],'</td>'; }
echo '<td class="headfirst" style="text-align:center;"><b>',$L['Year'],'</b></td>
</tr>';

// Display users

foreach($arrYears as $intYear)
{
echo '<tr>';
echo '<td class="headfirst">',$intYear,'</td>';
for ($intBt=1;$intBt<=12;++$intBt)
{
echo '<td style="text-align:center;">',(isset($arrU[$intYear][$intBt]) ? $arrU[$intYear][$intBt] : '&middot;'),'</td>';
}
echo '<td style="text-align:center;padding:5px;"><b>',$arrUs[$intYear],'</b></td>';
echo '</tr>
';
}
echo '</table>
';

if ( file_exists('qte_stats_csv.php') ) echo '<p class="right" style="margin:2px"><a href="'.Href('qte_stats_csv.php').'?s='.$s.'&amp;year='.$year.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].'</a></p>';

// After values display, change the null values to zero to be able to make charts

foreach($arrYears as $intYear)
{
$arrU[$intYear] = QTarrayzero($arrU[$intYear]);
}

// GRAPH

if ( file_exists($_SESSION[QT]['skin_dir'].'/qte_graph.css') )
{
  echo '<h2>',$year,S,($s>=0 ? '('.$arrSections[$s].')' : ''),'</h2>',PHP_EOL;
  echo '<table class="layout">',PHP_EOL;
  echo '<tr>';
  echo '<td class="col1">';
  QTbarchart(QTarraymerge($arrA,$arrU[$year]),320,100,QTroof($arrU[$year]),2,true,$L['Items_per_month'],'','1');
  echo '</td>';
  echo '<td class="col2">';
  QTbarchart(QTarraymerge($arrA,QTpercent($arrU[$year])),350,100,100,2,'P',$L['Items_per_month'].' (%)','','1');
  echo '</td>';
  echo '</tr>',PHP_EOL;
  echo '<tr>';
  echo '<td class="col1">';
  QTbarchart(QTarraymerge($arrA,QTcumul($arrU[$year])),320,100,QTroof(QTcumul($arrU[$year])),2,true,$L['Items_per_month_cumul'],'','1');
  echo '</td>';
  echo '<td class="col2">';
  QTbarchart(QTarraymerge($arrA,QTcumul(QTpercent($arrU[$year],2))),350,100,100,2,'P',$L['Items_per_month_cumul'].' (%)','','1');
  echo '</td>';
  echo '</tr>',PHP_EOL;
  echo '</table>',PHP_EOL;
}
else
{
  echo '<p class="small">Graphs cannot be displayed because one of these files is missing: bin/qt_lib_graph.php, ',$_SESSION[QT]['skin_dir'].'/qte_graph.css</p>';
}

// HTML END

include 'qte_inc_ft.php';