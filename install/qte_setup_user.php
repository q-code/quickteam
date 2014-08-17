<?php

switch($oDB->type)
{

case 'pdo.mysql':
case 'mysql':
  $strQ='CREATE TABLE '.$qte_prefix.'qteuser (
  id int NOT NULL default 0,
  username varchar(32) NOT NULL UNIQUE,
  pwd varchar(40),
  role char(1) NOT NULL default "V",
  type char(1),
  status char(1) NOT NULL default "Z",
  children char(1) NOT NULL default "0",
  title varchar(8),
  firstname varchar(32),
  midname varchar(32),
  lastname varchar(32),
  alias varchar(32),
  birthdate varchar(8) NOT NULL default "0",
  nationality varchar(32),
  sexe varchar(32),
  picture varchar(255),
  address varchar(255),
  phones varchar(255),
  emails varchar(255),
  www varchar(255),
  privacy varchar(255),
  signature varchar(255),
  descr varchar(255),
  firstdate varchar(8) NOT NULL default "0",
  teamid1 varchar(32),
  teamid2 varchar(32),
  teamrole1 varchar(32),
  teamrole2 varchar(32),
  teamdate1 varchar(8) NOT NULL default "0",
  teamdate2 varchar(8) NOT NULL default "0",
  teamvalue1 decimal(16,2),
  teamvalue2 decimal(16,2),
  teamflag1 varchar(32),
  teamflag2 varchar(32),
  x decimal(9,6),
  y decimal(9,6),
  z decimal(9,2),
  ip varchar(24),
  secret_q varchar(255),
  secret_a varchar(255),
  PRIMARY KEY (id)
  )';
  break;
  
case 'pdo.sqlsrv':
case 'sqlsrv':
  $strQ='CREATE TABLE '.$qte_prefix.'qteuser (
  id int NOT NULL CONSTRAINT pk_'.$qte_prefix.'qteuser PRIMARY KEY,
  username varchar(32) NOT NULL CONSTRAINT uk_'.$qte_prefix.'qteuser UNIQUE,
  pwd varchar(40) NULL default NULL,
  role char(1) NOT NULL default "V",
  type char(1) NULL default NULL,
  status char(1) NOT NULL default "Z",
  children char(1) NOT NULL default "0",
  title varchar(8) NULL default NULL,
  firstname varchar(32) NULL default NULL,
  midname varchar(32) NULL default NULL,
  lastname varchar(32) NULL default NULL,
  alias varchar(32) NULL default NULL,
  birthdate varchar(8) NOT NULL default "0",
  nationality varchar(32) NULL default NULL,
  sexe varchar(32) NULL default NULL,
  picture varchar(255) NULL default NULL,
  address varchar(255) NULL default NULL,
  phones varchar(255) NULL default NULL,
  emails varchar(255) NULL default NULL,
  www varchar(255) NULL default NULL,
  privacy varchar(255) NULL default NULL,
  signature varchar(255) NULL default NULL,
  descr varchar(255) NULL default NULL,
  firstdate varchar(8) NOT NULL default "0",
  teamid1 varchar(32) NULL default NULL,
  teamid2 varchar(32) NULL default NULL,
  teamrole1 varchar(32) NULL default NULL,
  teamrole2 varchar(32) NULL default NULL,
  teamdate1 varchar(8) NOT NULL default "0",
  teamdate2 varchar(8) NOT NULL default "0",
  teamvalue1 numeric(16,2) NULL default NULL,
  teamvalue2 numeric(16,2) NULL default NULL,
  teamflag1 varchar(32) NULL default NULL,
  teamflag2 varchar(32) NULL default NULL,
  x decimal(9,6) NULL default NULL,
  y decimal(9,6) NULL default NULL,
  z decimal(9,2) NULL default NULL,
  ip varchar(24) NULL default NULL,
  secret_q varchar(255) NULL default NULL,
  secret_a varchar(255) NULL default NULL
  )';
  break;

case 'pdo.pg':
case 'pg':
  $strQ='CREATE TABLE '.$qte_prefix.'qteuser (
  id integer NOT NULL default 0,
  username varchar(32) NOT NULL UNIQUE,
  pwd varchar(40),
  role char(1) NOT NULL default "V",
  type char(1),
  status char(1) NOT NULL default "Z",
  children char(1) NOT NULL default "0",
  title varchar(8),
  firstname varchar(32),
  midname varchar(32),
  lastname varchar(32),
  alias varchar(32),
  birthdate varchar(8) NOT NULL default "0",
  nationality varchar(32),
  sexe varchar(32),
  picture varchar(255),
  address varchar(255),
  phones varchar(255),
  emails varchar(255),
  www varchar(255),
  privacy varchar(255),
  signature varchar(255),
  descr varchar(255),
  firstdate varchar(8) NOT NULL default "0",
  teamid1 varchar(32),
  teamid2 varchar(32),
  teamrole1 varchar(32),
  teamrole2 varchar(32),
  teamdate1 varchar(8) NOT NULL default "0",
  teamdate2 varchar(8) NOT NULL default "0",
  teamvalue1 numeric(16,2),
  teamvalue2 numeric(16,2),
  teamflag1 varchar(32),
  teamflag2 varchar(32),
  x decimal(9,6),
  y decimal(9,6),
  z decimal(9,2),
  ip varchar(24),
  secret_q varchar(255),
  secret_a varchar(255),
  PRIMARY KEY (id)
  )';
  break;
  
case 'pdo.sqlite':
case 'sqlite':
  $strQ='CREATE TABLE '.$qte_prefix.'qteuser (
  id integer,
  username text UNIQUE,
  pwd text,
  role text NOT NULL default "V",
  type text,
  status text NOT NULL  default "Z",
  children text NOT NULL default "0",
  title text,
  firstname text,
  midname text,
  lastname text,
  alias text,
  birthdate text NOT NULL default "0",
  nationality text,
  sexe text,
  picture text,
  address text,
  phones text,
  emails text,
  www text,
  privacy text,
  signature text,
  descr text,
  firstdate text NOT NULL default "0",
  teamid1 text,
  teamid2 text,
  teamrole1 text,
  teamrole2 text,
  teamdate1 text NOT NULL default "0",
  teamdate2 text NOT NULL default "0",
  teamvalue1 real,
  teamvalue2 real,
  teamflag1 text,
  teamflag2 text,
  x real,
  y real,
  z real,
  ip text,
  secret_q text,
  secret_a text,
  PRIMARY KEY (id)
  )';
  break;
  
case 'pdo.ibase':
case 'ibase':
  $strQ='CREATE TABLE '.$qte_prefix.'qteuser (
  id integer default 0,
  username varchar(32) NOT NULL UNIQUE,
  pwd varchar(40),
  role char(1) default "V",
  type char(1),
  status char(1) default "Z",
  children char(1) default "0",
  title varchar(8),
  firstname varchar(32),
  midname varchar(32),
  lastname varchar(32),
  alias varchar(32),
  birthdate varchar(8) default "0",
  nationality varchar(32),
  sexe varchar(32),
  picture varchar(255),
  address varchar(255),
  phones varchar(255),
  emails varchar(255),
  www varchar(255),
  privacy varchar(255),
  signature varchar(255),
  descr varchar(255),
  firstdate varchar(8) default "0",
  teamid1 varchar(32),
  teamid2 varchar(32),
  teamrole1 varchar(32),
  teamrole2 varchar(32),
  teamdate1 varchar(8) default "0",
  teamdate2 varchar(8) default "0",
  teamvalue1 numeric(16,2),
  teamvalue2 numeric(16,2),
  teamflag1 varchar(32),
  teamflag2 varchar(32),
  x decimal(9,6),
  y decimal(9,6),
  z decimal(9,2),
  ip varchar(24),
  secret_q varchar(255),
  secret_a varchar(255),
  PRIMARY KEY (id)
  )';
  break;
  
case 'pdo.db2':
case 'db2':
  $strQ='CREATE TABLE '.$qte_prefix.'qteuser (
  id integer NOT NULL,
  username varchar(32) NOT NULL UNIQUE,
  pwd varchar(40),
  role char(1) NOT NULL default "V",
  type char(1),
  status char(1) NOT NULL default "Z",
  children char(1) NOT NULL default "0",
  title varchar(8),
  firstname varchar(32),
  midname varchar(32),
  lastname varchar(32),
  alias varchar(32),
  birthdate varchar(8) NOT NULL default "0",
  nationality varchar(32),
  sexe varchar(32),
  picture varchar(255),
  address varchar(255),
  phones varchar(255),
  emails varchar(255),
  www varchar(255),
  privacy varchar(255),
  signature varchar(255),
  descr varchar(255),
  firstdate varchar(8),
  teamid1 varchar(32),
  teamid2 varchar(32),
  teamrole1 varchar(32),
  teamrole2 varchar(32),
  teamdate1 varchar(8) NOT NULL default "0",
  teamdate2 varchar(8) NOT NULL default "0",
  teamvalue1 numeric(16,2),
  teamvalue2 numeric(16,2),
  teamflag1 varchar(32),
  teamflag2 varchar(32),  
  x numeric(9,6),
  y numeric(9,6),
  z numeric(9,2),
  ip varchar(24),
  secret_q varchar(255),
  secret_a varchar(255),
  PRIMARY KEY (id)
  )';
  break;

case 'pdo.oci':
case 'oci':
  $strQ='CREATE TABLE '.$qte_prefix.'qteuser (
  id number(32) default 0 NOT NULL,
  username varchar2(32) NOT NULL,
  pwd varchar2(40),
  role char(1),
  type char(1),
  status char(1) default "Z" NOT NULL,
  children char(1) default "0" NOT NULL,
  title varchar2(8),
  firstname varchar2(32),
  midname varchar2(32),
  lastname varchar2(32),
  alias varchar2(32),
  birthdate varchar2(8) default "0" NOT NULL,
  nationality varchar2(32),
  sexe varchar2(32),
  picture varchar2(255),
  address varchar2(255),
  phones varchar2(255),
  emails varchar2(255),
  www varchar2(255),
  privacy varchar2(255),
  signature varchar2(255),
  descr varchar2(255),
  firstdate varchar2(8) default "0" NOT NULL,
  teamid1 varchar2(32),
  teamid2 varchar2(32),
  teamrole1 varchar2(32),
  teamrole2 varchar2(32),
  teamdate1 varchar2(8) default "0" NOT NULL,
  teamdate2 varchar2(8) default "0" NOT NULL,
  teamvalue1 number(16,2),
  teamvalue2 number(16,2),
  teamflag1 varchar2(32),
  teamflag2 varchar2(32),
  x number(9,6) default 0 NOT NULL,
  y number(9,6) default 0 NOT NULL,
  z number(9,2) default 0 NOT NULL,
  ip varchar2(24),
  secret_q varchar2(255),
  secret_a varchar2(255),
  CONSTRAINT pk_'.$qte_prefix.'qteuser PRIMARY KEY (id),
  CONSTRAINT uk_'.$qte_prefix.'qteuser UNIQUE (username))';
  break;
  
default:
  die('Database type ['.$this->type.'] not supported... Must be mysql, sqlsrv, pg, sqlite, ibase, db2, oci');
}

echo '<span style="color:blue;">';
$b = $oDB->Exec($strQ);
echo '</span>';

if ( !empty($oDB->error) || $b===false )
{
  echo '<div class="setup_err">',sprintf ($L['E_install'],$qte_prefix.'qteuser',$qte_database,$qte_user),'</div>';
  echo '<br /><table cellspacing="0" class="button"><tr><td></td><td class="button" style="width:120px">&nbsp;<a href="qte_setup_1.php">',$L['Restart'],'</a>&nbsp;</td></tr></table>';
  exit;
}

$oDB->Exec( 'INSERT INTO '.$qte_prefix.'qteuser (id,username,pwd,role,children,status,firstdate) VALUES (0,"Visitor",null,"V","0","Z","'.date('Ymd').'")' );
$oDB->Exec( 'INSERT INTO '.$qte_prefix.'qteuser (id,username,pwd,role,children,status,firstdate) VALUES (1,"Admin","'.sha1('Admin').'","A","0","Z","'.date('Ymd').'")' );