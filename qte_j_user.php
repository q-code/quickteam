<?php // QuickTeam 3.0 build:20141222

// mandatory: id, the user id. Can start with 'user'.
// mandatory: dir, the photo repository.
// option: sep_name {NL|aaa} where NL means <br/>. Default is NL.
// option: link to add a link to the profile. Use '' to remove link.

include 'bin/class/qt_class_db.php';

if ( !isset($_POST['id']) ) { echo 'no id';  exit; }
if ( !isset($_POST['dir']) ) { echo 'no picture directory'; exit; }
if ( !isset($_POST['sep_name']) ) $_POST['sep_name']='NL';

$id = strip_tags(trim($_POST['id'])); if ( substr($id,0,4)=='user' ) $id=substr($id,4);
$dir = strip_tags($_POST['dir']);
$sep_name = strip_tags($_POST['sep_name']); if ( $sep_name==='NL' ) $sep_name='<br/>';

include 'bin/config.php';

// query

$oDBAJAX = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd);
if ( !empty($oDBAJAX->error) ) exit;
$oDBAJAX->Query('SELECT firstname,lastname,username,picture,status FROM '.$qte_prefix.'qteuser WHERE id='.$id);
$row = $oDBAJAX->GetRow();

$str = htmlentities(trim($row['firstname'])).$sep_name.htmlentities(trim($row['lastname']));
if ( $str == $sep_name ) $str = '('.htmlentities(trim($row['username'])).')';

// output the response

echo '<img class="userimage" src="'.( empty($row['picture']) ? 'admin/user.gif' : $dir.$row['picture'] ).'" alt="[i]"/>';
echo '<p class="username">'.$str.'</p>';
echo '<status '.$row['status'].'/>';
if ( !empty($_POST['link']) ) echo '<p class="userlink"><a href="qte_user.php?id='.$id.'">'.htmlentities(strip_tags($_POST['link'])).'</a></p>';