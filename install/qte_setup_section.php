<?php

// QuickTeam 3.0

switch($oDB->type)
{

case 'pdo.mysql':
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesection (
  id int,
  domainid int NOT NULL default 0,
  title varchar(32) NOT NULL default "untitled",
  type char(1) NOT NULL default "0",
  status char(1) NOT NULL default "0",
  stats varchar(255),
  options varchar(255),
  vorder int NOT NULL default 255,
  forder varchar(255),
  modid int NOT NULL default 0,
  modname varchar(32) NOT NULL default "Administrator",
  template char(1) NOT NULL default "T",
  ontop char(1) NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;

case 'pdo.sqlsrv':
case 'sqlsrv':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesection (
  id int NOT NULL CONSTRAINT pk_'.$qte_prefix.'qtesection PRIMARY KEY,
  domainid int NOT NULL default 0,
  title varchar(32) NOT NULL default "untitled",
  type char(1) NOT NULL default "0",
  status char(1) NOT NULL default "0",
  stats varchar(255) NULL default NULL,
  options varchar(255) NULL default NULL,
  vorder int NOT NULL default 255,
  forder varchar(255) NULL default NULL,
  modid int NOT NULL default 0,
  modname varchar(32) NOT NULL default "Administrator",
  template char(1) NOT NULL default "T",
  ontop char(1) NOT NULL default "0"
  )';
  break;

case 'pdo.pg':
case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesection (
  id integer,
  domainid integer NOT NULL default 0,
  title varchar(32) NOT NULL default "untitled",
  type char(1) NOT NULL default "0",
  status char(1) NOT NULL default "0",
  stats varchar(255),
  options varchar(255),
  vorder integer NOT NULL default 255,
  forder varchar(255),
  modid integer NOT NULL default 0,
  modname varchar(32) NOT NULL default "Administrator",
  template char(1) NOT NULL default "T",
  ontop char(1) NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;

case 'pdo.sqlite':
case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesection (
  id integer,
  domainid integer NOT NULL default 0,
  title text NOT NULL default "untitled",
  type text NOT NULL default "0",
  status text NOT NULL default "0",
  stats text,
  options text,
  vorder integer NOT NULL default 255,
  forder text,
  modid integer NOT NULL default 0,
  modname text NOT NULL default "Administrator",
  template text NOT NULL default "T",
  ontop text NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;
  
case 'pdo.ibase':
case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesection (
  id integer,
  domainid integer default 0,
  title varchar(32) default "untitled",
  type char(1) default "0",
  status char(1) default "0",
  stats varchar(255),
  options varchar(255),
  vorder integer default 255,
  forder varchar(255),
  modid integer default 0,
  modname varchar(32) default "Administrator",
  template char(1) default "T",
  ontop char(1) default "0",
  PRIMARY KEY (id)
  )';
  break;
  
case 'pdo.db2':
case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesection (
  id integer NOT NULL,
  domainid integer NOT NULL default 0,
  title varchar(32) NOT NULL default "untitled",
  type char(1) NOT NULL default "0",
  status char(1) NOT NULL default "0",
  stats varchar(255),
  options varchar(255),
  vorder integer NOT NULL default 255,
  forder varchar(255),
  modid integer NOT NULL default 0,
  modname varchar(32) NOT NULL default "Administrator",
  template char(1) NOT NULL default "T",
  ontop char(1) NOT NULL default "0",
  PRIMARY KEY (id)
  )';
  break;

case 'pdo.oci':
case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qtesection (
  id number(32),
  domainid number(32) default 0 NOT NULL,
  title varchar2(32) default "untitled" NOT NULL,
  type char(1) default "0" NOT NULL,
  status char(1) default "0" NOT NULL,
  stats varchar2(255),
  options varchar2(255),
  vorder number(32) default 255 NOT NULL,
  forder varchar2(255),
  modid integer default 0 NOT NULL,
  modname varchar2(32) default "Administrator" NOT NULL,
  template char(1) default "T" NOT NULL,
  ontop char(1) default "0" NOT NULL,
  CONSTRAINT pk_'.$qte_prefix.'qtesection PRIMARY KEY (id))';
  break;
  
default:
  die('Database type ['.$oDB->type.'] not supported... Must be mysql, sqlsrv, pg, ibase, db2, oci');
 
}

echo '<span style="color:blue">';
$b=$oDB->Exec($strQ);
echo '</span>';

if ( !empty($oDB->error) || $b===false )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qtesection',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}

$oDB->Exec( 'INSERT INTO '.$qte_prefix.'qtesection (id,domainid,title,type,status,stats,options,vorder,forder,modid,modname) VALUES (0,0,"Members collector","0","0","members=1","logo=none.gif",0,"status_i;lastname;firstname;phones;emails;picture",1,"Admin")' );
$oDB->Exec( 'INSERT INTO '.$qte_prefix.'qtesection (id,domainid,title,type,status,stats,options,vorder,forder,modid,modname) VALUES (1,1,"My team","0","0","members=0","logo=0",1,"status_i;lastname;firstname;phones;emails;picture",1,"Admin")' );
$oDB->Exec( 'INSERT INTO '.TABLANG.' (objtype,objlang,objid,objname) VALUES ("secdesc","en","s0","Collect members not yet in a team or removed from a team")' );