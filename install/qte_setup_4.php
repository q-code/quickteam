<?php

// QuickTeam 3.0 build:20140608

session_start();

if ( !isset($_SESSION['qte_setup_lang']) ) $_SESSION['qte_setup_lang']='en';

include 'qte_lang_'.$_SESSION['qte_setup_lang'].'.php';
include '../bin/config.php'; if ( $qte_dbsystem=='sqlite' ) $qte_database = '../'.$qte_database;
include '../bin/class/qt_class_db.php';

$strAppl     = 'QuickTeam';
$strPrevUrl  = 'qte_setup_2.php';
$strNextUrl  = '../qte_login.php?dfltname=Admin';
$strPrevLabel= $L['Back'];
$strNextLabel= $L['Finish'];
$strMessage = '';
$error = '';

// CHECK DB VERSION (in case of update)

$oDB = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd,$qte_port,$qte_dsn);
if ( !empty($oDB->error) ) die ('<p><font color="red">Connection with database failed.<br />Please contact the webmaster for further information.</font></p><p>The webmaster must check that server is up and running, and that the settings in the config file are correct for the database.</p>');

$oDB->Query('SELECT setting FROM '.$qte_prefix.'qtesetting WHERE param="version"');
$row=$oDB->Getrow();
if ( $row['setting']=='1.5' || $row['setting']=='1.4' || $row['setting']=='1.3' || $row['setting']=='1.2' )
{
  switch($oDB->type)
  {
  case 'mysql':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtes2u CHANGE uid userid int NOT NULL default 0');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteindex CHANGE uid userid int NOT NULL default 0');
    break;
  case 'sqlsrv':
  case 'mssql':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtes2u ADD userid int NOT NULL default 0');
    $oDB->Query('UPDATE '.$qte_prefix.'qtes2u SET userid=uid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtes2u DROP COLUMN uid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteindex ADD userid int NOT NULL default 0');
    $oDB->Query('UPDATE '.$qte_prefix.'qteindex SET userid=uid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteindex DROP COLUMN uid');
    break;
  case 'pg':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtes2u RENAME uid TO userid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteindex RENAME uid TO userid');
    break;
  case 'sqlite':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtes2u ADD userid integer NOT NULL default 0');
    $oDB->Query('UPDATE '.$qte_prefix.'qtes2u SET userid=uid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteindex ADD userid integer NOT NULL default 0');
    $oDB->Query('UPDATE '.$qte_prefix.'qteindex SET userid=uid');
    break;
  case 'ibase':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtes2u ALTER COLUMN uid TO userid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteindex ALTER COLUMN uid TO userid');
    break;
  case 'db2':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtes2u ADD userid integer NOT NULL default 0');
    $oDB->Query('UPDATE '.$qte_prefix.'qtes2u SET userid=uid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtes2u DROP uid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteindex ADD userid integer NOT NULL default 0');
    $oDB->Query('UPDATE '.$qte_prefix.'qteindex SET userid=uid');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteindex DROP uid');
    break;
  default:
    die('unknown database type');
    break;
  }
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("show_stats", "M")'); // new in version 1.6
  $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="1.6" WHERE param="version"');
  $row['setting']='1.6';
  $strMessage .= '<p>Database upgraded to 1.6</p>';

}

// UPDAGRADE TO 1.7

if ( $row['setting']=='1.6' )
{
  switch($oDB->type)
  {
  case 'mysql':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY firstname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY midname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY lastname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY alias varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY x decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY y decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY z decimal(13,2)');
    break;
  case 'sqlsrv':
  case 'mssql':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN firstname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN midname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN lastname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN alias varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN x decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN y decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN z decimal(13,2)');
    break;
  case 'pg':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN firstname TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN midname TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN lastname TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN alias TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN x TYPE decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN y TYPE decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN z TYPE decimal(13,2)');
    break;
  case 'sqlite':
    // already text
    // already real
    break;
  case 'ibase':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN firstname TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN midname TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN lastname TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN alias TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN x TYPE decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN y TYPE decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN z TYPE decimal(13,2)');
    break;
  case 'db2':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN firstname SET DATA TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN midname SET DATA TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN lastname SET DATA TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN alias SET DATA TYPE varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN x SET DATA TYPE decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN y SET DATA TYPE decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ALTER COLUMN z SET DATA TYPE decimal(13,2)');
    break;
  case 'oci':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY firstname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY midname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY lastname varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY alias varchar(32)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY x decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY y decimal(13,10)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser MODIFY z decimal(13,2)');
    break;
  }
  $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="1.7" WHERE param="version"');
  $row['setting']='1.7';
  $strMessage .= '<p>Database upgraded to 1.7</p>';
}

// UPDAGRADE TO 1.8

if ( $row['setting']=='1.7' )
{
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("upload", "1")');
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("uploadsize", "1000")');
  if ( isset($qte_install) ) { define('QT','qte'.substr($qte_install,-1)); } else { define('QT','qte'); }
  if ( isset($_SESSION[QT]['version']) ) $_SESSION[QT]['version']='1.8';
  $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="1.8" WHERE param="version"');
  $row['setting']='1.8';
  $strMessage .= '<p>Database upgraded to 1.8</p>';
}

// UPDAGRADE TO 1.9

if ( $row['setting']=='1.8' )
{
  $oDB->Query( 'UPDATE '.$qte_prefix.'qtesetting SET param="index_name" WHERE param="section_index"' );
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("login_qte_web", "0")');
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("login_qtf", "0")');
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("login_qti", "0")');
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("fields_c", "id,username,pwd,status,status_i,role,fullname,age,children,firstdate")'); // new in version 1.9
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("fields_u", "picture,address,phones,emails,emails_i,www,title,firstname,midname,lastname,alias,birthdate,nationality,sexe")'); // new in version 1.9
  $oDB->Query('INSERT INTO '.$qte_prefix.'qtesetting VALUES ("fields_t", "teamid1,teamid2,teamrole1,teamrole2,teamdate1,teamdate2,teamvalue1,teamvalue2,teamflag1,teamflag2,descr")'); // new in version 1.9

  switch($oDB->type)
  {

  case 'mysql4':
    $oDB->Query( 'CREATE TABLE '.$qte_prefix.'qtelang (
    objtype varchar(10),
    objlang varchar(2),
    objid varchar(24),
    objname text,
    PRIMARY KEY (objtype,objlang,objid)
    )');
    break;

  case 'mysql':
    $oDB->Query( 'CREATE TABLE '.$qte_prefix.'qtelang (
    objtype varchar(10),
    objlang varchar(2),
    objid varchar(24),
    objname varchar(4000),
    PRIMARY KEY (objtype,objlang,objid)
    )');
    break;

  case 'sqlsrv':
  case 'mssql':
    $oDB->Query( 'CREATE TABLE '.$qte_prefix.'qtelang (
    objtype varchar(10) NOT NULL,
    objlang varchar(2) NOT NULL,
    objid varchar(24) NOT NULL,
    objname varchar(4000) NULL,
    CONSTRAINT pk_'.$qte_prefix.'qtelang PRIMARY KEY (objtype,objlang,objid)
    )');
    break;

  case 'pg':
    $oDB->Query( 'CREATE TABLE '.$qte_prefix.'qtelang (
    objtype varchar(10),
    objlang varchar(2),
    objid varchar(24),
    objname varchar(4000),
    PRIMARY KEY (objtype,objlang,objid)
    )');
    break;

  case 'sqlite':
    $oDB->Query( 'CREATE TABLE '.$qte_prefix.'qtelang (
    objtype text,
    objlang text,
    objid text,
    objname text,
    PRIMARY KEY (objtype,objlang,objid)
    )' );
    break;

  case 'ibase':
    $oDB->Query( 'CREATE TABLE '.$qte_prefix.'qtelang (
    objtype varchar(10),
    objlang varchar(2),
    objid varchar(24),
    objname varchar(4000),
    PRIMARY KEY (objtype,objlang,objid)
    )' );
    break;

  case 'db2':
    $oDB->Query( 'CREATE TABLE '.$qte_prefix.'qtelang (
    objtype varchar(10),
    objlang varchar(2),
    objid varchar(24),
    objname varchar(4000),
    PRIMARY KEY (objtype,objlang,objid)
    )');
    break;

  case 'oci':
    $oDB->Query( 'CREATE TABLE '.$qte_prefix.'qtelang (
    objtype varchar2(10),
    objlang varchar2(2),
    objid varchar2(24),
    objname varchar2(4000),
    CONSTRAINT pk_'.$qte_prefix.'qtelang PRIMARY KEY (objtype,objlang,objid))');
    break;

  default:
    die("Database type [{$oDB->type}] not supported... Must be mysql, sqlsrv, mssql, pg, sqlite, ibase, db2, oci");

  }

  // transfers section descriptions

    $oDB2 = new cDB($qte_dbsystem,$qte_host,$qte_database,$qte_user,$qte_pwd,$qte_port,$qte_dsn);
    $oDB2->Query( 'SELECT id,descr FROM '.$qte_prefix.'qtesection ' );
    $arr = array();
    while($row=$oDB2->Getrow())
    {
      $arr[$row['id']] = $row['descr'];
    }
    foreach ($arr as $id=>$str)
    {
    $oDB2->Query( 'DELETE FROM '.$qte_prefix.'qtelang WHERE objtype="secdesc" AND objid="'.$id.'"' );
    $oDB2->Query( 'INSERT INTO '.$qte_prefix.'qtelang (objtype,objlang,objid,objname) VALUES ("secdesc","en","s'.$id.'","'.addslashes($str).'")' );
    }

  // transfers fields information

    $arrSetC = array('id','username','pwd','status','status_i','role');
    $arrSetU = array();
    $arrSetT = array();
    if ( file_exists('../bin/config_english.php') )
    {
      include '../bin/config_english.php';
      foreach ($qte_fields as $strKey=>$arrField)
      {
        if ( isset($arrField[0]) ) $oDB->Query( 'INSERT INTO '.$qte_prefix.'qtelang (objtype,objlang,objid,objname) VALUES ("field","en","'.$strKey.'","'.str_replace('&nbsp;',' ',$arrField[0]).'")' );
        if ( isset($arrField[1]) ) $oDB->Query( 'INSERT INTO '.$qte_prefix.'qtelang (objtype,objlang,objid,objname) VALUES ("ffield","en","'.$strKey.'","'.$arrField[1].'")' );
        if ( isset($arrField[2]) ) {
        if ( $arrField[2]) {
          if ( in_array($strKey,array('fullname','age','children','firstdate')) ) $arrSetC[] = $strKey;
          if ( in_array($strKey,array('picture','address','phones','emails','emails_i','www','title','firstname','midname','lastname','alias','birthdate','nationality','sexe')) ) $arrSetU[] = $strKey;
          if ( in_array($strKey,array('teamid1','teamid2','teamrole1','teamrole2','teamdate1','teamdate2','teamvalue1','teamvalue2','teamflag1','teamflag2','descr')) ) $arrSetT[] = $strKey;
        }}
      }
    }
    $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="'.implode(',',$arrSetC).'" WHERE param="fields_c"');
    $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="'.implode(',',$arrSetU).'" WHERE param="fields_u"');
    $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="'.implode(',',$arrSetT).'" WHERE param="fields_t"');
    if ( file_exists('../bin/config_francais.php') )
    {
      include '../bin/config_francais.php';
      foreach ($qte_fields as $strKey=>$arrField)
      {
        if ( isset($arrField[0]) ) $oDB->Query( 'INSERT INTO '.$qte_prefix.'qtelang (objtype,objlang,objid,objname) VALUES ("field","fr","'.$strKey.'","'.str_replace('&nbsp;',' ',$arrField[0]).'")' );
        if ( isset($arrField[1]) ) $oDB->Query( 'INSERT INTO '.$qte_prefix.'qtelang (objtype,objlang,objid,objname) VALUES ("ffield","fr","'.$strKey.'","'.$arrField[1].'")' );
      }
    }
    if ( file_exists('../bin/config_nederlands.php') )
    {
      include '../bin/config_nederlands.php';
      foreach ($qte_fields as $strKey=>$arrField)
      {
        if ( isset($arrField[0]) ) $oDB->Query( 'INSERT INTO '.$qte_prefix.'qtelang (objtype,objlang,objid,objname) VALUES ("field","nl","'.$strKey.'","'.str_replace('&nbsp;',' ',$arrField[0]).'")' );
        if ( isset($arrField[1]) ) $oDB->Query( 'INSERT INTO '.$qte_prefix.'qtelang (objtype,objlang,objid,objname) VALUES ("ffield","nl","'.$strKey.'","'.$arrField[1].'")' );
      }
    }

  // register version

  $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="1.9" WHERE param="version"');
  $row['setting']='1.9';
  $strMessage .= '<p>Database upgraded to 1.9</p>';
}

// UPDAGRADE TO 2.0

if ( $row['setting']=='1.9' )
{
  if ( isset($qte_install) ) { define('QT','qte'.substr($qte_install,-1)); } else { define('QT','qte'); }
  if ( isset($_SESSION[QT]['version']) ) $_SESSION[QT]['version']='2.0';
  $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="2.0" WHERE param="version"');

  // no need to upgrade picture storage schema
  // no need to update document storage schema
  // update sectionlogo storage schema

  $b = false;
  if ( is_dir('../document') ) {
  if ( is_dir('../document/section') ) {
  if ( is_readable('../document/section') ) {
  if ( is_writable('../document/section') ) {
    $b=true;
  }}}}

  if ( $b )
  {
    $oDB->Query('SELECT id,picture FROM '.$qte_prefix.'qtesection');
    $arr = array();
    while($row=$oDB->Getrow())
    {
      if ( !empty($row['picture']) ) $arr[$row['id']]=$row['picture'];
    }
    foreach($arr as $strKey=>$strValue)
    {
      $strExt = strtolower(substr(strrchr($strValue,'.'),1));
      $strFile = 'document/section/'.$strKey.'.'.$strExt;
      if ( file_exists('../'.$strValue) )
      {
      copy('../'.$strValue,'../'.$strFile);
      $oDB->Query( 'UPDATE '.$qte_prefix.'qtesection SET picture="'.$strFile.'" WHERE id='.$strKey );
      }
    }
  }
  else
  {
    echo 'Warning: directory <b>document/section/</b> is not writable. Section logo cannot be copied into the new storage structure.';
  }
  // end update storage schema

  $row['setting']='2.0';
  $strMessage .= '<p>Database upgraded to 2.0</p>';
}

// UPDAGRADE 2.0 or 2.1 TO 2.2

if ( $row['setting']=='2.0' || $row['setting']=='2.1' )
{
  switch($oDB->type)
  {
  case 'sqlite':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtesection ADD stats text');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtesection ADD options text');
    break;
  case 'oci':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtesection ADD stats varchar2(255)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtesection ADD options varchar2(255)');
    break;
  default:
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtesection ADD stats varchar(255)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qtesection ADD options varchar(255)');
    break;
  }

  // update section options and stats

  $oDB->Query('SELECT id,members,picture FROM '.$qte_prefix.'qtesection');
  $arrOptions = array();
  $arrStats = array();
  while($row=$oDB->Getrow())
  {
    if ( empty($row['picture']) ) $row['picture']='0';
    if ( empty($row['members']) ) $row['members']='0';
    if ( substr($row['picture'],0,8)=='picture/' )
    {
      if ( is_dir('document/section') ) {
      if ( is_writable('document/section') ) {
      copy($row['picture'],'document/section/'.substr($row['picture'],8));
      $row['picture']=substr($row['picture'],8);
      }}
    }
    $row['picture'] = str_replace('document/section/','',$row['picture']);
    $arrOptions[$row['id']]='order=0;logo='.$row['picture'];
    $arrStats[$row['id']]='members='.$row['members'];
  }
  foreach($arrOptions as $strKey=>$strValue)
  {
  $oDB->Query( 'UPDATE '.$qte_prefix.'qtesection SET options="'.$arrOptions[$strKey].'",stats="'.$arrStats[$strKey].'" WHERE id='.$strKey );
  }

  $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="2.2" WHERE param="version"');
  $row['setting']='2.2';
  $strMessage .= '<p>Database upgraded to 2.2</p>';
}

// UPDAGRADE 2.2, 2.3, 2.4 TO 2.5

if ( $row['setting']=='2.2' || $row['setting']=='2.3' || $row['setting']=='2.4' )
{
  $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="2.5" WHERE param="version"');
  switch($oDB->type)
  {
  case 'sqlite':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ADD secret_q text');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ADD secret_a text');
    break;
  case 'oci':
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ADD secret_q varchar2(255)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ADD secret_a varchar2(255)');
    break;
  default:
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ADD secret_q varchar(255)');
    $oDB->Query('ALTER TABLE '.$qte_prefix.'qteuser ADD secret_a varchar(255)');
    break;
  }
  $row['setting']='2.5';
  $strMessage .= '<p>Database upgraded to 2.5</p>';
}

// UPDAGRADE 2.5 TO 3.0

if ( $row['setting']=='2.5' )
{
  $oDB->Query('UPDATE '.$qte_prefix.'qtesetting SET setting="3.0" WHERE param="version"');
  switch($oDB->type)
  {
  case 'pg':
  case 'ibase':
    $oDB->Query('UPDATE '.$qte_prefix.'qteuser SET picture=REPLACE(picture, "picture/", "") WHERE SUBSTRING(picture FROM 1 FOR 8)="picture/"');
    break;
  case 'db2':
  case 'oci':
  case 'sqlite':
    $oDB->Query('UPDATE '.$qte_prefix.'qteuser SET picture=REPLACE(picture, "picture/", "") WHERE SUBSTR(picture,1,8)="picture/"');
    break;
  default:
    $oDB->Query('UPDATE '.$qte_prefix.'qteuser SET picture=REPLACE(picture, "picture/", "") WHERE LEFT(picture,8)="picture/"');
    break;
  }
  $row['setting']='3.0';
  $strMessage .= '<p>Database upgraded to 3.0</p>';
}

// --------
// HTML START
// --------

include 'qte_setup_hd.php';

echo $strMessage;

if ( isset($_SESSION['qteInstalled']) )
{
echo '<p>Database 3.0 in place.</p>';
echo '<p>',$L['S_install_exit'],'</p>';
echo '<div style="width:350px; padding:10px; border-style:solid; border-color:#FF0000; border-width:1px; background-color:#EEEEEE">',$L['End_message'],'<br />',$L['User'],': <b>Admin</b><br />',$L['Password'],': <b>Admin</b><br /></div><br />';
}
else
{
echo $L['N_install'];
}

// document folders

if ( !is_dir('document') )
{
  $error .= '<font color=red>Directory <b>document</b> not found.</font><br />Please create this directory and make it writeable (chmod 777) if you want to allow uploads<br />';
}
else
{
  if ( !is_readable('document') ) $error .= '<font color=red>Directory <b>document</b> is not readable.</font><br />Change permissions (chmod 777) if you want to allow uploads<br />';
  if ( !is_writable('document') ) $error .= '<font color=red>Directory <b>document</b> is not writable.</font><br />Change permissions (chmod 777) if you want to allow uploads<br />';
}

if ( empty($error) )
{
  $iY = intval(date('Y'));
  for ($i=$iY;$i<=$iY+5;$i++)
  {
    if ( !is_dir('document/'.$i) )
    {
      if ( mkdir('document/'.$i) )
      {
        for ($j=1;$j<=12;$j++)
        {
        mkdir('document/'.$i.'/'.($i*100+$j));
        }
      }
    }
  }
}

echo '<p><a href="../check.php">',$L['Check_install'],'</a></p>';

// --------
// HTML END
// --------

include 'qte_setup_ft.php';

unset($_SESSION);
session_destroy();