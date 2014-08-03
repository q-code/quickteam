<?php

// This table contains additional information in case of Coppa rules are applied.
// [childdate] is the date when child registers. It may be missing ("0") when Coppa functions was disabled then re-activated (or in case of import)
// [parentdate] is the date of the parent's agreement (should be completed by a staff members).

switch($oDB->type)
{

case 'pdo.mysql':
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id int NOT NULL default 0,
  childdate varchar(8) NOT NULL default "0",
  parentmail varchar(255),
  parentdate varchar(8) NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;

case 'mysql4':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id int NOT NULL default 0,
  childdate varchar(8) NOT NULL default "0",
  parentmail varchar(255),
  parentdate varchar(8) NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;
  
case 'sqlsrv':
case 'mssql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id int NOT NULL CONSTRAINT pk_'.$qte_prefix.'qtechild PRIMARY KEY,
  childdate varchar(8) NOT NULL default "0",
  parentmail varchar(255) NULL default NULL,
  parentdate varchar(8) NOT NULL default "0"
  )';
  break;

case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id integer NOT NULL default 0,
  childdate varchar(8) NOT NULL default "0",
  parentmail varchar(255),
  parentdate varchar(8) NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;
  
case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id integer default 0,
  childdate varchar(8) default "0",
  parentmail varchar(255),  
  parentdate varchar(8) default "0",
  PRIMARY KEY (id)
  )';
  break;

case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id integer,
  childdate text NOT NULL default "0",
  parentmail text,  
  parentdate text NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;

case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id integer NOT NULL,
  childdate varchar(8) NOT NULL default "0",
  parentmail varchar(255),
  parentdate varchar(8) NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;

case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qtechild (
  id number(32) default 0 NOT NULL,
  childdate varchar2(8) default "0" NOT NULL,
  parentmail varchar2(255),
  parentdate varchar2(8) default "0" NOT NULL,
  CONSTRAINT pk_'.$qte_prefix.'qtechild PRIMARY KEY (id))';
  break;
  
default:
  die('Database type ['.$this->type.'] not supported... Must be mysql, sqlsrv, mssql, pg, ibase, db2, oci');
}

echo '<span style="color:blue;">';
$b=$oDB->Exec($strQ);
echo '</span>';

if ( !empty($oDB->error) || $b===false )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qtechild',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}