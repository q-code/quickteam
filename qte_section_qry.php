<?php // QTE 3.0 build:20140608

// Uri extra arguments

$v = '';
$w = '';
QThttpvar('v w','str str');

// ----------------
// QUERY DEFINITION
// ----------------

if ( !isset($strWhere) ) $strWhere = ' WHERE u.id>0';

switch($q)
{

case 'kwd':
	$infofield = 'ufield'; // replaces infofield by ufield
	$oVIP->selfname = $L['Search_by_key'];
	if ( empty($v) ) $error = $L['Keywords'].' '.Error(1);
	if ( strlen($v)>64 ) die('Invalid argument #v');
	$oSEC->descr = '"'.$v.'"'.($s>=0 ? ' '.L('In_section').' "'.$oVIP->sections[$s].'"' : '');

  $strFlds  = ' u.*,i.ufield,i.ukey';
  $strFrom  = ' FROM '.TABUSER.' u INNER JOIN '.TABINDEX.' i ON i.userid=u.id'.($s>=0 ? ' INNER JOIN '.TABS2U.' l ON l.userid = u.id' : '');
  $strWhere = ' WHERE u.id>0 AND UPPER(i.ukey) LIKE "%'.strtoupper($v).'%"'.($s>=0 ? ' AND l.sid='.$s : '');
  $strCount = 'SELECT count(DISTINCT u.id) as countid'.$strFrom.$strWhere;
  break;

case 'sta':
	$oVIP->selfname = $L['Search_by_status'];
	if ( empty($v) ) $error = $L['Status'].' '.Error(1);
	if ( strlen($v)>64 ) die('Invalid argument #v');
	$oSEC->descr = $L['Status'].' "'.cVIP::GetStatusName($v).'"'.($s>=0 ? ' '.L('In_section').' "'.$oVIP->sections[$s].'"' : '');

  $strFlds  = ' u.*';
  $strFrom  = ' FROM '.TABUSER.' u'.($s>=0 ? ' INNER JOIN '.TABS2U.' l ON l.userid = u.id' : '');
  $strWhere = ' WHERE u.id>0 AND u.status="'.$v.'"'.($s>=0 ? ' AND l.sid='.$s : '');
  $strCount = 'SELECT count(*) as countid'.$strFrom.$strWhere;
  break;

case 'age':
	$oVIP->selfname = $L['Search_by_age'];
	$str = ' < '.$v;
	if ($w=='u') $str = ' >= '.$v;
	if ($w=='e') $str = ' = '.$v;
	if ($w.$v==='=0' || $w==='0') $str = ' '.L('undefined');
	$oSEC->descr = L('Age').$str.($s>=0 ? ' '.L('In_section').' "'.$oVIP->sections[$s].'"' : '');

	$intDate = $v.'000';
	$intDate = (int)date('Ymd') - (int)$intDate;
  $strFlds  = ' u.*';
  $strFrom  = ' FROM '.TABUSER.' u '.($s>=0 ? ' INNER JOIN '.TABS2U.' l ON l.userid = u.id' : '');
  $strWhere = ' WHERE u.id>0'.($s>=0 ? ' AND l.sid='.$s : '');
  switch ($w)
  {
  	case 'l': $strWhere .= ' AND u.birthdate<>"0" AND "'.$intDate.'"<u.birthdate'; break;
  	case 'u':	$strWhere .= ' AND u.birthdate<>"0" AND "'.$intDate.'">=u.birthdate';	break;
  	case '0': $strWhere .= ' AND (u.birthdate="0" OR u.birthdate="")'; break; //undefined
  	default:
  		if ( $v=='0' )
  		{
  			$strWhere .= ' AND (u.birthdate="0" OR u.birthdate="")';
  		}
  		else
  		{
  			$strWhere .= ' AND u.birthdate<>"0" AND "'.$intDate.'">=u.birthdate AND "'.($intDate-10000).'"<u.birthdate';
  		}
  		break;
  }
  $strCount = 'SELECT count(*) as countid'.$strFrom.$strWhere;
  break;

case 'role':
	$oVIP->selfname = L('Search_result');
	$oSEC->descr = L('Role').' ';
	if ( $v=='S' ) { $oSEC->descr .= L('Userrole_A').' '.L('or').' '.L('Userrole_M'); } else { $oSEC->descr .= L('Userrole_'.$v); }

	$strFlds  = ' u.*,"'.L('Role').'" as ufield';
	$strFrom  = ' FROM '.TABUSER.' u'.($s>=0 ? ' INNER JOIN '.TABS2U.' l ON l.userid = u.id' : '');
	$strWhere = ' WHERE u.id>0'.($s>=0 ? ' AND l.sid='.$s : '');
  if ( $v=='S' ) { $strWhere .= ' AND (u.role="A" OR u.role="M")';} else { $strWhere .= ' AND u.role="'.$v.'"'; }
  $strCount = 'SELECT count(*) as countid'.$strFrom.$strWhere;
  break;

case 'uwt':
	$oVIP->selfname = L('Search_result');
	$oSEC->descr = L('Users_without_section');
	$strFlds  = ' u.*';
	$strFrom  = ' FROM '.TABUSER.' u LEFT JOIN '.TABS2U.' l ON l.userid=u.id';
	$strWhere = ' WHERE u.id>0 AND l.userid IS NULL';
	$strCount = 'SELECT count(*) as countid'.$strFrom.$strWhere;
	break;

case 'ui0':
	$oVIP->selfname = L('Search_result');
	$str = (isset($_SESSION[QT]['sys_sections'][0]) ? $_SESSION[QT]['sys_sections'][0] : 'team 0');
	$oSEC->descr = sprintf(L('Users_in_0_only'),$str);
  $strFlds  = ' u.*,sum(l.sid) as sumsid';
	$strFrom  = ' FROM '.TABUSER.' u INNER JOIN '.TABS2U.' l ON l.userid=u.id';
  $strWhere = ' GROUP BY u.id HAVING sum(l.sid)=0 AND u.id>0';
  $strCount = 'SELECT count(*) as countid FROM (SELECT sum(l.sid) as sumsid '.$strFrom.$strWhere.') as t1';
	break;

case 'bdm': // brithday on month $v

  $strYear = date('Y'); if ( isset($_GET['y']) ) $strYear = strip_tags($_GET['y']);
  $strMonth = ( isset($L['dateMMM'][(int)$v]) ? $L['dateMMM'][(int)$v] : 'unknown' );

	$oVIP->selfname = L('Birthdays_calendar').' '.$strMonth.' '.$strYear;

	$v = substr('0'.$v,-2);
  $strFlds  = ' u.id,u.username,u.firstname,u.lastname,u.emails';
  $strFrom  = ' FROM '.TABUSER.' u '.($s>=0 ? ' INNER JOIN '.TABS2U.' l ON l.userid = u.id' : '');
  $strWhere = ' WHERE u.id>0'.($s>=0 ? ' AND l.sid='.$s : '');

	switch($oDB->type)
	{
	// Select month
	case 'mysql4':
	case 'mysql':
	case 'sqlsrv':
	case 'mssql':
	case 'pg':    $strWhere .= ' AND SUBSTRING(u.birthdate,5,2)="'.$v.'"'; break;
	case 'ibase': $strWhere .= ' AND SUBSTRING(u.birthdate FROM 5 FOR 2)="'.$v.'"'; break;
	case 'sqlite':
	case 'db2':
	case 'oci':   $strWhere .= ' AND SUBSTR(u.birthdate,5,2)="'.$v.'"'; break;
	default: die('Unknown db type '.$oDB->type);
	}

  $strCount = 'SELECT count(*) as countid'.$strFrom.$strWhere;
  break;

default:

  die('Undefined query method: '.$q);
  break;
}

// stop if error

if ( !empty($error) ) $oHtml->PageMsg(NULL,$error);
