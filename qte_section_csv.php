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
$oVIP->output='csv';
if ( !sUser::CanView('V2') ) die(Error(11));

// ---------
// INITIALISE
// ---------

$s = ''; // section $s can be '*' or [int] (after argument checking only [int] is allowed)
$q = '';
QThttpvar('s q','str str');
if ( $s==='*' || $s==='' ) $s=-1;
if ( !is_int($s) ) $s=(int)$s;
if ( $s<0 && empty($q) ) die('Missing argument $s or $q...');

// Check arguments

$size = strip_tags($_GET['size']);
$intCount = (int)$_GET['n'];

if ( empty($size) || $intCount <= $_SESSION[QT]['items_per_page'] ) $size='all';
if ( strlen($size)>6 || strlen($size)<2 ) die('Invalid argument');
if ( substr($size,0,1)!='p' && substr($size,0,1)!='m' && $size!='all') die('Invalid argument');
if ( substr($size,0,1)=='p' )
{
  $i = (int)substr($size,1);
  if ( empty($i) ) die('Invalid argument');
  if ( ($i-1) > $intCount/$_SESSION[QT]['items_per_page'] ) die('Invalid argument');
}
if ( substr($size,0,1)=='m' )
{
  if ( $size!='m1' && $size!='m2' && $size!='m5' && $size!='m10' ) die('Invalid argument');
}
if ( $intCount>1000 && $size=='all' ) die('Invalid argument');
if ( $intCount<=1000 && substr($size,0,1)=='m' ) die('Invalid argument');
if ( $intCount>1000 && substr($size,0,1)=='p' ) die('Invalid argument');

// apply page argument

if ( substr($size,0,1)=='p' ) $_GET['page'] = substr($size,1);

// Initialise

$oSEC = new cSection($s);
$oVIP->selfname = L('Section');
$oVIP->exitname = ObjTrans('index','i');

if ( $oSEC->type==1 && !sUser::IsStaff() )
{
  // exit
  $oHtml->PageMsg(NULL,$L['R_staff']);
}
if ( $oSEC->type==2 && sUser::Role()==='V' )
{
  // exit
  $oHtml->PageMsg(NULL,$L['R_user']);
}

$oVIP->selfurl = 'qte_section.php';
$oVIP->selfname = L('Section').': '.$oSEC->name;

$strFlds  = ' u.*';
$strFrom  = ' FROM '.TABUSER.' u INNER JOIN '.TABS2U.' l ON l.userid=u.id';
$strWhere = ' WHERE u.id>0';
$strGroup = 'all';
$strOrder = 'lastname';
$strDirec = 'ASC';
$intLimit = 0;
$intPage  = 1;
$strCSV = '';
$intLen = $_SESSION[QT]['items_per_page'];

// security check 1

if ( isset($_GET['group']) ) $strGroup = strip_tags($_GET['group']); if ( strlen($strGroup)>4 ) die('Invalid argument #group');
if ( isset($_GET['page']) ) { $intLimit = ((int)$_GET['page']-1)*$intLen; $intPage = (int)$_GET['page']; }
if ( isset($_GET['order']) ) $strOrder = strip_tags($_GET['order']);
if ( isset($_GET['dir']) ) $strDirec = strip_tags($_GET['dir']);

// apply argument

if ( $size=='all') { $intLimit=0; $intLen=$intCount; }
if ( $size=='m1' ) { $intLimit=0; $intLen=999; }
if ( $size=='m2' ) { $intLimit=1000; $intLen=1000; }
if ( $size=='m5' ) { $intLimit=0; $intLen=4999; }
if ( $size=='m10') { $intLimit=5000; $intLen=5000; }

// refine query

if ( $s>=0 )
{
  $oSEC = new cSection($s);

  if ( $oSEC->type==1 && !sUser::IsStaff() )
  {
    $oHtml->PageMsg(NULL,$L['R_staff']);
  }
  if ( $oSEC->type==2 && sUser::Role()==='V' )
  {
    $oHtml->PageMsg(NULL,$L['R_user']);
  }

  $oVIP->selfname = $oSEC->name;
}
elseif ( !empty($q) )
{
  $oSEC = new cSection(); // section is null in case of search query
}
else
{
  die('Missing argument $s or $q...');
}

// Initialize query

if ( !$_SESSION[QT]['show_Z'] ) $strWhere .= ' AND u.status<>"Z"';

if ( $s>=0 && empty($q) )
{
  $strWhere .= ' AND l.sid='.$s;
  switch ($strGroup)
  {
    case 'all': break;
    case '0': $strWhere = ' AND '.FirstCharCase('u.firstname','a-z').' AND '.FirstCharCase('u.lastname','a-z'); break;
    default:  $strWhere = ' AND ('.FirstCharCase('u.firstname','u').'="'.$strGroup.'" OR '.FirstCharCase('u.lastname','u').'="'.$strGroup.'")'; break;
  }
  $strCount = 'SELECT count(*) as countid'.$strFrom.$strWhere;
}
elseif ( !empty($q) )
{
  include 'qte_section_qry.php';
}
else
{
  die('Missing argument $s or $q...');
}

// --------
// HTML START
// --------

// Prepare fields

$arrFLD = GetFLDs($oSEC->forder.(!empty($_SESSION[QT]['infofield']) ? ';'.$_SESSION[QT]['infofield'] : ''));
$arrFLD['id']= new cFLD('id','ID');
unset($arrFLD['ufield']);

// check current order (if using default)

if ( !array_key_exists($strOrder,$arrFLD) )
{
  $strOrder='fullname'; if ( !array_key_exists($strOrder,$arrFLD) ) $strOrder='lastname';
  if ( !array_key_exists($strOrder,$arrFLD) ) $strOrder='username';
}

// ========
foreach($arrFLD as $strKey=>$oFLD)
{
$strCSV .= ToCsv($oFLD->name);
}
$strCSV .= "\r\n";
// ========

if ( substr($strOrder,0,2)!='u.' ) $strOrder = 'u.'.$strOrder;
$strOrder .= ' '.strtoupper($strDirec);
$strOrder = str_replace('u.fullname','u.lastname',$strOrder);
$strOrder = str_replace('u.status_i','u.status',$strOrder);
$strOrder = str_replace('u.age','u.birthdate',$strOrder);
// second order
if ( !strstr($strOrder,'lastname') ) $strOrder .= ',u.lastname';

$oDB->Query( LimitSQL($strFlds.$strFrom.$strWhere,$strOrder,$intLimit,$_SESSION[QT]['items_per_page']) );

$arrRow=array(); // rendered row. To remove duplicate in seach result

// ========
while($row=$oDB->Getrow())
{
	if ( in_array((int)$row['id'], $arrRow) ) continue; // this remove duplicate users in case of search result
	if ( empty($row['lastname']) ) $row['lastname']='('.L('unknown').')';

	$oItem = new cItem($row,true);
	$arrRow[] = $oItem->id;
	$arrRendered = cSection::RenderFieldsCSV($arrFLD,$oItem,$qte_root,false);

	$str = implode('',$arrRendered);
  $strCSV .= $str."\r\n";
}
// ========

// ----------
// OUPUT
// ----------

if ( isset($_GET['debug']) )
{
  echo $strCSV;
  exit;
}

if ( !headers_sent() )
{
  header('Content-Type: text/csv; charset='.QT_HTML_CHAR);
  header('Content-Disposition: attachment; filename="qte_'.date('YmdHi').'.csv"');
}

echo $strCSV;