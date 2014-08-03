<?php

// QuickTeam 2.5

switch($oDB->type)
{

case 'pdo.mysql':
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtelang (
  objtype varchar(10),
  objlang varchar(2),
  objid varchar(24),
  objname varchar(4000),
  PRIMARY KEY (objtype,objlang,objid)
  )';
  break;

case 'mysql4':
  $strQ='CREATE TABLE '.$qte_prefix.'qtelang (
  objtype varchar(10),
  objlang varchar(2),
  objid varchar(24),
  objname text,
  PRIMARY KEY (objtype,objlang,objid)
  )';
  break;
  
case 'sqlsrv':
case 'mssql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtelang (
  objtype varchar(10) NOT NULL,
  objlang varchar(2) NOT NULL,
  objid varchar(24) NOT NULL,
  objname varchar(4000) NULL,
  CONSTRAINT pk_'.$qte_prefix.'qtelang PRIMARY KEY (objtype,objlang,objid)
  )';
  break;

case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qtelang (
  objtype varchar(10),
  objlang varchar(2),
  objid varchar(24),
  objname varchar(4000),
  PRIMARY KEY (objtype,objlang,objid)
  )';
  break;

case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qtelang (
  objtype text,
  objlang text,
  objid text,
  objname text,
  PRIMARY KEY (objtype,objlang,objid)
  )';
  break;

case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qtelang (
  objtype varchar(10),
  objlang varchar(2),
  objid varchar(24),
  objname varchar(4000),
  PRIMARY KEY (objtype,objlang,objid)
  )';
  break;

case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qtelang (
  objtype varchar(10),
  objlang varchar(2),
  objid varchar(24),
  objname varchar(4000),
  PRIMARY KEY (objtype,objlang,objid)
  )';
  break;

case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qtelang (
  objtype varchar2(10),
  objlang varchar2(2),
  objid varchar2(24),
  objname varchar2(4000),
  CONSTRAINT pk_'.$qte_prefix.'qtelang PRIMARY KEY (objtype,objlang,objid))';
  break;

default:
  die("Database type [{$oDB->type}] not supported... Must be mysql, sqlsrv, mssql, pg, sqlite, ibase, db2, oci");

}

echo '<span style="color:blue">';
$b=$oDB->Exec($strQ);
echo '</span>';

if ( !empty($oDB->error) || $b===false )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qtelang',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}