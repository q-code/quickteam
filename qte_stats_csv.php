<?php

// QuickTeam 3.0 build:20140608

session_start();
require 'bin/qte_init.php';
if ( !sUser::CanView('V4') ) { $oHtml->PageMsg(11); return; }

// --------
// INITIALISE
// --------

$strCSV = '';

$year = date('Y'); if ( intval(date('n'))<2 ) $year--;
$s = -1;
QThttpvar('s year','int int',true,true,false);
if ( $s>=0 ) { $strWhere = ' INNER JOIN '.TABS2U.' s ON u.id=s.userid WHERE u.id>0 AND u.firstdate>"1" AND s.sid='.$s; } else { $strWhere=' WHERE u.id>0 AND u.firstdate>"1"'; }

// ------
// COUNT Users
// ------

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

$arrYears = array($year-1,$year);

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

// Table header

$strCSV .= '"'.$L['Year'].'";';
for ($i=1;$i<=12;++$i) $strCSV .= '"'.$L['dateMM'][$i].'";';
$strCSV .= '"'.$L['Total'].'"'.PHP_EOL;

// -----
foreach($arrYears as $y) {
// -----


// Table body

  $strCSV .= '"'.$y.'";';
  for ($intBt=1;$intBt<=12;++$intBt) { $strCSV .= (isset($arrU[$y][$intBt]) ? $arrU[$y][$intBt] : '0').';'; }
  $strCSV .= $arrUs[$y].PHP_EOL;


// -----
}
// -----

// ------
// Export
// ------

if ( !headers_sent() )
{
  header('Content-Type: text/csv; charset='.QT_HTML_CHAR);
  header('Content-Disposition: attachment; filename="stat_'.$y.'.csv"');
}

echo $strCSV;