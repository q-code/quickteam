<?php // QuickTeam 3.0 build:20141222

include 'bin/class/qt_class_db.php';
include 'bin/config.php';

if ( !isset($_POST['v']) ) { echo ' '; exit; }
if ( !isset($_POST['f']) ) $_POST['f']='name';
if ( get_magic_quotes_gpc() ) $_POST['v'] = stripslashes($_POST['v']);

if ( $_POST['v'])==='' ) { echo ' '; exit; }

if ( !isset($_POST['v'][3]) )
{
  echo isset($_POST['e1']) ? $_POST['e1'] : 'Minium 4 characters';
}
else
{
  $oDBAJAX = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd);
  if ( !empty($oDBAJAX->error) ) return;
  $oDBAJAX->Query('SELECT count(*) as countid FROM '.$qte_prefix.'qteuser WHERE '.$_POST['f'].'="'.htmlspecialchars(addslashes($_POST['v']),ENT_QUOTES).'"' );
  $row = $oDBAJAX->GetRow();
  if ( $row['countid']>0 )
  {
  if ( isset($_POST['e2']) ) { echo $_POST['e2']; } else { echo 'Already used'; }
  }
  else
  {
  echo ' ';
  }
}