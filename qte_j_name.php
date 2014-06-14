<?php // QuickTeam 3.0 build:20140608

if ( empty($_GET['term']) ) { echo json_encode(array(array('rItem'=>'','rInfo'=>'configuration error'))); return; }

$e0 = 'No result'; if ( isset($_GET['e0']) ) $e0 = $_GET['e0'];
$e1 = 'try other lettres'; if ( isset($_GET['e1']) ) $e1 = $_GET['e1'];
$strRole = ''; if ( isset($_GET['r']) ) $strRole = strtoupper($_GET['r']);
if ( $strRole=='A' ) $strRole = 'role="A" AND ';
if ( $strRole=='M' ) $strRole = '(role="A" OR role="M") AND ';

$arrValue = array();

include 'bin/class/qt_class_db.php';
include 'bin/config.php';

// query

$oDBAJAX = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd,$qte_port,$qte_dsn);
if ( !empty($oDBAJAX->error) ) return;

$oDBAJAX->Query('SELECT username,firstname,lastname FROM '.$qte_prefix.'qteuser WHERE '.$strRole.' (UPPER(username) like "%'.addslashes(strtoupper($_GET['term'])).'%" OR UPPER(lastname) like "%'.addslashes(strtoupper($_GET['term'])).'%" OR UPPER(firstname) like "%'.addslashes(strtoupper($_GET['term'])).'%")');

// format: result item + result info (as a json array with index "rItem","rInfo" )

$json = array();
while($row=$oDBAJAX->GetRow())
{
  $json[] =array('rItem'=>$row['firstname'].' '.$row['lastname'],'rInfo'=>$row['username']);
  if ( count($json)>=10 ) break;
}

// error handling
if ( empty($json) ) $json[]=array('rItem'=>'','rInfo'=>$e0.', '.$e1);

// response

echo json_encode($json);