<?php

// QuickTeam 3.0

switch($oDB->type)
{

case 'pdo.mysql':
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesetting (
  param varchar(24),
  setting varchar(255),
  PRIMARY KEY (param)
  )';
  break;
  
case 'pdo.sqlsrv':
case 'sqlsrv':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesetting (
  param varchar(24) CONSTRAINT pk_'.$qte_prefix.'qtesetting PRIMARY KEY,
  setting varchar(255)
  )';
  break;
  
case 'pdo.pg':
case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesetting (
  param varchar(24),
  setting varchar(255),
  PRIMARY KEY (param)
  )';
  break;

case 'pdo.sqlite':
case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesetting (
  param text,
  setting text,
  PRIMARY KEY (param)
  )';
  break;
  
case 'pdo.ibase':
case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesetting (
  param varchar(24),
  setting varchar(255),
  PRIMARY KEY (param)
  )';
  break;
  
case 'pdo.db2':
case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesetting (
  param varchar(24),
  setting varchar(255),
  PRIMARY KEY (param)
  )';
  break;

case 'pdo.oci':
case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesetting (
  param varchar2(24),
  setting varchar2(255),
  CONSTRAINT pk_'.$qte_prefix.'qtesetting PRIMARY KEY (param))';
  break;

default:
  die('Database type ['.$oDB->type.'] not supported... Must be mysql, sqlsrv, pg, db2, sqlite, oci');

}

echo '<span style="color:blue">';
$b=$oDB->Exec($strQ);
echo '</span>';

if ( !empty($oDB->error) || $b===false )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qtesetting',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}

$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("version", "3.0")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("board_offline", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("site_name", "QT-cute")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("site_url", "http://")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("home_name", "Home")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("home_url", "http://www.qt-cute.org")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("admin_email", "")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("admin_fax", "")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("admin_name", "")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("admin_addr", "")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("time_zone", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_time_zone", "0")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("home_menu", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("site_width", "780")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("register_safe", "text")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("smtp_password", "")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("smtp_username", "")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("smtp_host", "")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("use_smtp", "0")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_welcome", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("items_per_page", "20")');
$str='english';
if ( $_SESSION['qte_setup_lang']=='fr' ) $str='francais';
if ( $_SESSION['qte_setup_lang']=='nl' ) $str='nederlands';
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("language", "'.$str.'")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("userlang", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("section_descr", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_banner", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_legend", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("index_name", "Team index")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("skin_dir", "skin/default")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("picture", "gif,jpg,jpeg,png")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("picture_width", "150")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("picture_height", "150")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("picture_size", "20")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("upload", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("uploadsize", "1000")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("formatdate", "j F Y")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("formattime", "G:i")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_id", "0")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_Z", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("register_mode", "direct")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("login_qte_web", "0")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("login_qtf", "0")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("login_qti", "0")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("daylight", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("visitor_right", "4")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("member_right", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("register_coppa", "1")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_calendar", "U")');
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_stats", "M")'); // new in version 1.6
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("fields_c", "id,username,pwd,status,status_i,role,fullname,age,children,firstdate")'); // new in version 1.9
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("fields_u", "picture,address,phones,emails,www,title,firstname,lastname,birthdate")'); // new in version 1.9
$result=$oDB->Exec('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("fields_t", "teamid1,teamrole1,teamdate1,teamvalue1,teamflag1,descr")'); // new in version 1.9