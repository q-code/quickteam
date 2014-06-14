<?php

if ( empty($_GET['term']) ) { echo json_encode(array(array('rItem'=>'','rInfo'=>'configuration error'))); return; }

$e0 = 'No result'; if ( isset($_GET['e0']) ) $e0 = $_GET['e0'];
$e1 = 'try other lettres'; if ( isset($_GET['e1']) ) $e1 = $_GET['e1'];
$e2 = 'try in all teams'; if ( isset($_GET['e2']) ) $e2 = $_GET['e2'];

$s = -1; if ( isset($_GET['s']) ) $s = $_GET['s'];
if ( $s==='' || $s==='*' || $s==='-1' ) $s=-1;

include 'bin/class/qt_class_db.php';
include 'bin/config.php';

// query

$oDBAJAX = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd,$qte_port,$qte_dsn);
if ( !empty($oDBAJAX->error) ) return;

if ( $s>=0 )
{
	$oDBAJAX->Query('SELECT i.ufield,i.ukey FROM '.$qte_prefix.'qteindex i INNER JOIN '.$qte_prefix.'qtes2u s ON s.userid=i.userid WHERE s.userid>0 AND s.sid='.$s.' AND UPPER(i.ukey) LIKE "%'.addslashes(strtoupper($_GET['term'])).'%"');
}
else
{
	$oDBAJAX->Query('SELECT ufield,ukey FROM '.$qte_prefix.'qteindex WHERE userid>0 AND UPPER(ukey) LIKE "%'.addslashes(strtoupper($_GET['term'])).'%"');
}

$arr = array();
while($row=$oDBAJAX->GetRow())
{
	if ( array_key_exists($row['ukey'],$arr) )
	{
		if ( strpos($arr[$row['ukey']],$row['ufield'])===false )
		{ 
		$arr[$row['ukey']] .= ( strpos($row['ufield'],',')>0 ? ', ...' : ', '.$row['ufield'] );
		}
	}
	else
	{
		$arr[$row['ukey']] = $row['ufield'];
	}
  if ( count($arr)>8 ) break;
}

// format: result item + result info (as a json array with index "rItem","rInfo" )

$json = array();
if ( count($arr)==0 )
{
  $json[]=array('rItem'=>'','rInfo'=>$e0.', '.($s<0 ? $e1 : $e2));
}
else
{
  foreach($arr as $key=>$info) $json[]=array('rItem'=>$key,'rInfo'=>$info);
}

// response

echo json_encode($json);