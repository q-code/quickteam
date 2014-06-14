<?php

// QuickTeam 3.0 build:20140608

switch($oDB->type)
{

case 'mysql4':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedomain (
  id int,
  title varchar(64) NOT NULL default "untitled",
  vorder int NOT NULL default 0,
  PRIMARY KEY (id)
  )';
  break;

case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedomain (
  id int,
  title varchar(64) NOT NULL default "untitled",
  vorder int NOT NULL default 0,
  PRIMARY KEY (id)
  )';
  break;

case 'sqlsrv':
case 'mssql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedomain (
  id int NOT NULL CONSTRAINT pk_'.$qte_prefix.'qtedomain PRIMARY KEY,
  title varchar(64) NOT NULL default "untitled",
  vorder int NOT NULL default 0
  )';
  break;

case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedomain (
  id integer,
  title varchar(64) NOT NULL default "untitled",
  vorder integer NOT NULL default 0,
  PRIMARY KEY (id)
  )';
  break;

case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedomain (
  id integer,
  title text NOT NULL default "untitled",
  vorder integer NOT NULL default 0,
  PRIMARY KEY (id)
  )';
  break;

case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedomain (
  id integer,
  title varchar(64) default "untitled",
  vorder integer default 0,
  PRIMARY KEY (id)
  )';
  break;

case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedomain (
  id integer NOT NULL,
  title varchar(64) NOT NULL default "untitled",
  vorder integer NOT NULL default 0,
  PRIMARY KEY (id)
  )';
  break;

case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedomain (
  id number(32),
  title varchar2(64) default "untitled" NOT NULL,
  vorder number(32) default 0 NOT NULL,
  CONSTRAINT pk_'.$qte_prefix.'qtedomain PRIMARY KEY (id))';
  break;

default:
  die('Database type ['.$this->type.'] not supported... Must be mysql, sqlsrv, mssql, pg, db2, oci');

}

echo '<span style="color:blue">';
$b=$oDB->Query($strQ);
echo '</span>';

if ( !empty($oDB->error) || !$b )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qtedomain',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}

$result=$oDB->Query( 'INSERT INTO '.$qte_prefix.'qtedomain (id,title,vorder) VALUES (0,"Administration domain",255)' );
$result=$oDB->Query( 'INSERT INTO '.$qte_prefix.'qtedomain (id,title,vorder) VALUES (1,"My teams",0)' );