<?php

// QuickTeam 3.0 build:20140608

switch($oDB->type)
{

case 'pdo.mysql':
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedoc (
  id int NOT NULL default 0,
  docdate varchar(8) NOT NULL default "0",
  docname varchar(255),
  docfile varchar(255),
  docpath varchar(255)
  )';
  break;

case 'mysql4':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedoc (
  id int NOT NULL default 0,
  docdate varchar(8) NOT NULL default "0",
  docname varchar(255),
  docfile varchar(255),
  docpath varchar(255)
  )';
  break;

case 'sqlsrv':
case 'mssql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedoc (
  id int NOT NULL default 0,
  docdate varchar(8) NOT NULL default "0",
  docname varchar(255),
  docfile varchar(255),
  docpath varchar(255)
  )';
  break;

case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedoc (
  id integer NOT NULL default 0,
  docdate varchar(8) NOT NULL default "0",
  docname varchar(255),
  docfile varchar(255),
  docpath varchar(255)
  )';
  break;

case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedoc (
  id integer default 0,
  docdate varchar(8) default "0",
  docname varchar(255),
  docfile varchar(255),
  docpath varchar(255)
  )';
  break;

case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedoc (
  id integer,
  docdate varchar(8) NOT NULL default "0",
  docname varchar(255),
  docfile varchar(255),
  docpath varchar(255)
  )';
  break;

case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id integer NOT NULL default 0,
  docdate varchar(8) NOT NULL default "0",
  docname varchar(255),
  docfile varchar(255),
  docpath varchar(255)
  )';
  break;

case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qtedoc (
  id number(32) default 0 NOT NULL,
  docdate varchar2(8) default "0" NOT NULL,
  docname varchar2(255),
  docfile varchar2(255),
  docpath varchar2(255)
  )';
  break;

default:
  die('Database type ['.$this->type.'] not supported... Must be mysql, sqlsrv, mssql, pg, ibase, db2, oci');
}

echo '<span style="color:blue;">';
$b=$oDB->Exec($strQ);
echo '</span>';

if ( !empty($oDB->error) || $b===false )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qtedoc',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}