<?php

// QuickTeam 2.5

switch($oDB->type)
{

case 'mysql4':
  $strQ='CREATE TABLE '.$qte_prefix.'qtes2u (
  sid int NOT NULL default 0,
  userid int NOT NULL default 0,
  issuedate varchar(8) NOT NULL default "0",
  PRIMARY KEY (sid,userid)
  )';
  break;
  
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtes2u (
  sid int NOT NULL default 0,
  userid int NOT NULL default 0,
  issuedate varchar(8) NOT NULL default "0",
  PRIMARY KEY (sid,userid)
  )';
  break;

case 'sqlsrv':
case 'mssql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtes2u (
  sid int NOT NULL default 0,
  userid int NOT NULL default 0,
  issuedate varchar(8) NOT NULL default "0",
  CONSTRAINT '.$qte_prefix.'qtes2u_pk PRIMARY KEY (sid,userid)
  )';
  break;

case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qtes2u (
  sid int NOT NULL default 0,
  userid int NOT NULL default 0,
  issuedate varchar(8) NOT NULL default "0",
  PRIMARY KEY (sid,userid)
  )';
  break;

case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qtes2u (
  sid integer NOT NULL default 0,
  userid integer NOT NULL default 0,
  issuedate text NOT NULL default "0",
  PRIMARY KEY (sid,userid)
  )';
  break;
  
case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qtes2u (
  sid integer default 0,
  userid integer default 0,
  issuedate varchar(8) default "0",
  PRIMARY KEY (sid,userid)
  )';
  break;
  
case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qtes2u (
  sid integer NOT NULL default 0,
  userid integer NOT NULL default 0,
  issuedate varchar(8) NOT NULL default "0",
  PRIMARY KEY (sid,userid)
  )';
  break;

case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qtes2u (
  sid number(32) default 0 NOT NULL,
  userid number(32) default 0 NOT NULL,
  issuedate varchar2(8) default "0" NOT NULL, 
  CONSTRAINT pk_'.$qte_prefix.'qtes2u_pk PRIMARY KEY (sid,userid)
  )';
  break;
  
default:
  die('Database type ['.$oDB->type.'] not supported... Must be mysql, sqlsrv, mssql, pg, db2, sqlite, ibase, oci');
 
}

echo '<span style="color:blue">';
$b=$oDB->Query($strQ);
echo '</span>';

if ( !empty($oDB->error) || !$b )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qtes2u',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}

$oDB->Query( 'INSERT INTO '.TABS2U.' VALUES (0,1,"'.date('Ymd').'")' );