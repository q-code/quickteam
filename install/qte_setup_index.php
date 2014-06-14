<?php

// QuickTeam 2.5

switch($oDB->type)
{

case 'mysql4':
  $strQ='CREATE TABLE '.$qte_prefix.'qteindex (
  userid int NOT NULL default 0,
  ufield varchar(32) NOT NULL default "undefined",
  ukey varchar(32) NOT NULL default "undefined"
  )';
  break;
  
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qteindex (
  userid int NOT NULL default 0,
  ufield varchar(32) NOT NULL default "undefined",
  ukey varchar(32) NOT NULL default "undefined"
  )';
  break;

case 'sqlsrv':
case 'mssql':
  $strQ='CREATE TABLE '.$qte_prefix.'qteindex (
  userid int NOT NULL default 0,
  ufield varchar(32) NOT NULL default "undefined",
  ukey varchar(32) NOT NULL default "undefined"
  )';
  break;
  
case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qteindex (
  userid integer,
  ufield varchar(32) NOT NULL default "undefined",
  ukey varchar(32) NOT NULL default "undefined"
  )';
  break;

case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qteindex (
  userid integer,
  ufield text NOT NULL default "undefined",
  ukey text NOT NULL default "undefined"
  )';
  break;
  
case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qteindex (
  userid integer,
  ufield varchar(32) default "undefined",
  ukey varchar(32) default "undefined"
  )';
  break;
  
case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qteindex (
  userid integer NOT NULL,
  ufield varchar(32) NOT NULL default "undefined",
  ukey varchar(32) NOT NULL default "undefined"
  )';
  break;
  
case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qteindex (
  userid number(32),
  ufield varchar2(32) default "undefined" NOT NULL,
  ukey varchar2(32) default "undefined" NOT NULL
  )';
  break;

default:
  die('Database type ['.$this->type.'] not supported... Must be mysql, sqlsrv, mssql, pg, db2, ibase, oci');

}

echo '<span style="color:blue">';
$b=$oDB->Query($strQ);
echo '</span>';

if ( !empty($oDB->error) || !$b )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qteindex',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}

$result=$oDB->Query( 'INSERT INTO '.$qte_prefix.'qteindex (userid,ufield,ukey) VALUES (0,"username","Admin")' );