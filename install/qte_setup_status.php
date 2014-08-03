<?php

// QuickTeam 2.5

switch($oDB->type)
{

case 'pdo.mysql':
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtestatus (
  id char(1),
  name varchar(32),
  icon varchar(32),
  color varchar(32),
  PRIMARY KEY (id)
  )';
  break;

case 'mysql4':
  $strQ='CREATE TABLE '.$qte_prefix.'qtestatus (
  id char(1),
  name varchar(32),
  icon varchar(32),
  color varchar(32),
  PRIMARY KEY (id)
  )';
  break;
  
case 'sqlsrv':
case 'mssql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtestatus (
  id char(1) NOT NULL CONSTRAINT pk_'.$qte_prefix.'qtestatus PRIMARY KEY,
  name varchar(32) NULL default NULL,
  icon varchar(32) NULL default NULL,
  color varchar(32) NULL default NULL
  )';
  break;
  
case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qtestatus (
  id char(1),
  name varchar(32),
  icon varchar(32),
  color varchar(32),
  PRIMARY KEY (id)
  )';
  break;
  
case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qtestatus (
  id text,
  name text,
  icon text,
  color text,
  PRIMARY KEY (id)
  )';
  break;
  
case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qtestatus (
  id char(1),
  name varchar(32),
  icon varchar(32),
  color varchar(32),
  PRIMARY KEY (id)
  )';
  break;
  
case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qtestatus (
  id char(1) NOT NULL,
  name varchar(32),
  icon varchar(32),
  color varchar(32),
  PRIMARY KEY (id)
  )';
  break;

case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qtestatus (
  id char(1),
  name varchar2(32),
  icon varchar2(32),
  color varchar2(32),
  CONSTRAINT pk_'.$qte_prefix.'qtestatus PRIMARY KEY (id))';
  break;

default:
  die("Database type [{$this->type}] not supported... Must be mysql, mssql, pg, ibase, db2, oci");

}

echo '<span style="color:blue">';
$b=$oDB->Exec($strQ);
echo '</span>';

if ( !empty($oDB->error) || $b===false )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qtestatus',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}

// add default values

$oDB->Exec('INSERT INTO '.$qte_prefix.'qtestatus (id,name,color,icon) VALUES ("A","Candidate","","status_star.gif")');
$oDB->Exec('INSERT INTO '.$qte_prefix.'qtestatus (id,name,color,icon) VALUES ("B","Member","","status_body_blue.gif")');
$oDB->Exec('INSERT INTO '.$qte_prefix.'qtestatus (id,name,color,icon) VALUES ("C","Retired","","status_body_grey.gif")');
$oDB->Exec('INSERT INTO '.$qte_prefix.'qtestatus (id,name,color,icon) VALUES ("Z","Not member","","status_no.gif")');