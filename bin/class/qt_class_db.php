<?php

/* ============
 * qt_db.php
 * ------------
 * version: 6.0 build:20140803
 * This is a library of public class
 * ------------
 * ============ */

class cDB
{

public $type; // server type
public $error = '';
public $debug = false;
public $pdo; // pdo object when using pdo.mysql

private $host; // server host name
private $db;   // database name
private $user;
private $pwd;
private $con;  // connection id
private $qry;  // query id or PDOstatement object

function __construct($type,$host,$db,$user,$pwd)
{
  $this->type = strtolower($type);
  $this->host = $host;
  $this->db = $db;
  $this->user = $user;
  $this->pwd = $pwd;
  
  // Use PDO or CONNECT
  if ( $this->type==='pdo.mysql')
  {
    try
    {
      $this->pdo = new PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->user, $this->pwd);
      return true;
    }
    catch (PDOException $e)
    {
      $this->error = $e->getMessage();
      $this->pdo = null;
      echo '<p class="small error">'.$this->error.'</p>';
      return false;
    }
  }
  else
  {
    return $this->Connect();
  }
}

/*
 * This function connects the database
 * @return boolean $is_connected Returns true if connection was successful otherwise false
 * @desc This function connects to the database which is set in the constructor
 */
public function Connect()
{
  // check type
  if ( !in_array($this->type,array('pdo.mysql','mysql','mysql4','sqlsrv','mssql','pg','ibase','sqlite','db2','oci')) )
  {
    die('db_type ['.$this->type.'] not supported. Must be "mysql","sqlsrv","mssql","pg","ibase","sqlite","db2" or "oci"');
  }
  
  switch($this->type)
  {
  case 'mysql4':
  case 'mysql': $this->con = mysql_connect($this->dsn,$this->user,$this->pwd); break;
  case 'pg': $this->con = pg_connect('host='.$this->dsn.' dbname='.$this->db.' user='.$this->user.' password='.$this->pwd); break;
  case 'ibase': $this->con = ibase_connect($this->host.':'.$this->db,$this->user,$this->pwd); break;
  case 'sqlite': $this->con = sqlite_open($this->db,0666,$e) or die($e); break;
  case 'sqlsrv':
  $arr=array('Database'=>$this->db,'UID'=>$this->user,'PWD'=>$this->pwd);
  // use windows authentication if no UID and no PWD
  if ( empty($this->user) && empty($this->pwd) ) $arr=array('Database'=>$this->db);
  $this->con = sqlsrv_connect($this->host,$arr);
  break;
  case 'mssql': $this->con = mssql_connect($this->host,$this->user,$this->pwd); break;
  case 'db2':   $this->con = db2_connect($this->host,$this->user,$this->pwd); break;
  case 'oci':   $this->con = oci_connect($this->user,$this->pwd,$this->db); break;
  default: die('db_type ['.$this->type.'] not supported.');
  }

  // check connection
  if ( !$this->con )
  {
    die( 'Wrong connection parameters! Cannot establish connection to host.');
  }

  // Selection database (if required)
  switch ($this->type)
  {
  case 'mysql4':
  case 'mysql': if ( mysql_select_db($this->db,$this->con) ) return true; break;
  case 'mssql': if ( mssql_select_db($this->db,$this->con) ) return true; break; // no select with sqlsrv
  case 'db2':   if ( db2_select_db($this->db,$this->con) ) return true; break;
  default: return true;
  }
  die('Wrong database parameters! Cannot select database.');
}

// ---------

function Disconnect()
{
  $this->pdo = null;
  switch($this->type)
  {
  case 'mysql4':
  case 'mysql': mysql_close($this->con); break;
  case 'mssql': mssql_close($this->con); break;
  case 'sqlsrv': sqlsrv_close($this->con); break;
  case 'pg': pg_close($this->con); break;
  case 'ibase': ibase_close($this->con); break;
  case 'sqlite': sqlite_close($this->con); break;
  case 'db2': db2_close($this->con); break;
  case 'oci': oci_close($this->con); break;
  }
  return true;
}

// ---------

function QueryErr($strSql,&$error,$bShowError=false)
{
  // same as Query but add the error in $error (passed by reference) and direct display is disabled
  if ( !$this->Query($strSql,$bShowError) ) { $error = $this->error; return false; }
  return true;
}
function Query($sql,$bShowError=true)
{
  if ( $this->debug || isset($_SESSION['QTdebugsql']) ) printf('<p class="small" style="margin:1px">SQL: %s</p>',$sql);

  switch($this->type)
  {
  case 'pdo.mysql': $this->qry = $this->pdo->query($sql); break; // warning this->qry is now a PDOstatement object
  case 'mysql': $this->qry = mysql_query($sql,$this->con); break;
  case 'mssql': $this->qry = mssql_query($sql,$this->con); break;
  case 'sqlsrv': $sql = str_replace('"',"'",$sql); $this->qry = sqlsrv_query($this->con,$sql); break;
  case 'pg': $sql = str_replace('"',"'",$sql); $this->qry = pg_query($this->con,$sql); break;
  case 'ibase': $this->qry = ibase_query($this->con,$sql); break;
  case 'sqlite':$this->qry = sqlite_query($this->con,$sql); break;
  case 'db2': $this->qry = db2_query($this->con,$sql); break;
  case 'oci': $sql = str_replace('"',"'",$sql); $this->qry = oci_parse($this->con,$sql); oci_execute($this->qry); break;
  case 'mysql4': $this->qry = mysql_query($sql,$this->con); break;
  default: die('db_type ['.$this->type.'] not supported.');
  }

  if ( isset($this->stats) ) $this->stats['num']++;
  if ( !$this->qry ) return $this->qtHalt($bShowError); // puts error message in $this->error, echos error message, and returns false
  return true; // success
}
function Exec($sql,$bShowError=true)
{
  if ( $this->debug || isset($_SESSION['QTdebugsql']) ) printf('<p class="small" style="margin:1px">SQL: %s</p>',$sql);
  if ( $this->type==='pdo.mysql' )
  {
      try
      {
        return $this->pdo->exec($sql); // Returns the number of affected rows. With CREATE TABLE, returns false if table exists
      }
      catch (PDOException $e)
      {
        $this->error = $e->getMessage();
        echo '<p class="small error">'.$this->error.'</p>';
        return false;
    }
  }
  // Non PDO uses defaut query method
  return $this->Query($sql,$bShowError);
}

// --------

function Nextid($table='',$field='id',$where='')
{
  if ( !is_string($table) || empty($table) ) die('cDB->Nextid: argument #1 must be a string');
  if ( !is_string($field) || empty($field) ) die('cDB->Nextid: argument #2 must be a string');
  if ( !is_string($where) ) die('cDB->Nextid: argument #3 must be a string');
  $this->Query('SELECT max('.$field.')+1 as newnum FROM '.$table.' '.$where);
  $row = $this->Getrow();
  $i = $row['newnum'];
  if ( empty($i) ) $i=1;
  return (int)$i;
}

// --------
// attention: sqlite can return fieldname including the prefix alias (p.id)

function Getrow()
{
  $row = false;
  switch ($this->type)
  {
  case 'pdo.mysql': $row = $this->qry->fetch(PDO::FETCH_ASSOC); break;
  case 'mysql': $row = mysql_fetch_assoc($this->qry); break; // php 5.0.3
  case 'sqlsrv':$row = sqlsrv_fetch_array($this->qry,SQLSRV_FETCH_ASSOC); break;
  case 'mssql':
    $row = mssql_fetch_assoc($this->qry);
    // this fix a known bug in mssql_fetch_assoc that add a space to empty string
    if ( is_array($row) )
    {
      foreach($row as $strKey=>$oValue)
      {
        if ( is_string($oValue) ) {
        if ( strlen($oValue)==1 ) {
        $row[$strKey] = trim($oValue);
        }}
      }
    }
    break;
  case 'pg': $row = pg_fetch_assoc($this->qry); break;// php 4.3.0
  case 'ibase': $row = ibase_fetch_assoc($this->qry); break;// php 4.3.0
  case 'sqlite':
    $row = sqlite_fetch_array($this->qry,SQLITE_ASSOC);// php 5.0
    if ( $row===false ) return false;
    $arr = array();
    foreach($row as $strKey=>$oValue)
    {
      if ( substr($strKey,1,1)=='.') $strKey = strtolower(substr($strKey,2));
      $arr[$strKey]=$oValue;
    }
    $row = $arr;
    break;
  case 'db2': $row = db2_fetch_assoc($this->qry); break; // php unknown version
  case 'oci':
    $row = oci_fetch_assoc($this->qry);
    if ( $row===false ) return false;
    $arr = array();
    foreach($row as $strKey=>$oValue)
    {
      $arr[strtolower($strKey)]=$oValue;
    }
    $row = $arr;
    break;
  case 'mysql4': $row = mysql_fetch_assoc($this->qry); break; // php 4.0.3
  default: die('db_type ['.$this->type.'] not supported.');
  }
  return $row;
}

// --------
// Puts error message in $this->error
// Echos error message (if $bShowDbError)
// Returns false (or stop if $bStop)

function Halt($bShowError=true,$bStop=false)
{
  switch($this->type)
  {
  case 'mysql4':
  case 'mysql':  $this->error .= 'Error '.mysql_errno().': '.mysql_error(); break;
  case 'sqlsrv': $err=end(sqlsrv_errors());  $this->error .= 'Error '.$err['message']; break;
  case 'mssql':  $this->error .= 'Error: '.mssql_get_last_message(); break;
  case 'pg':     $this->error .= 'Error: '.pg_last_error(); break;
  case 'ibase':  $this->error .= 'Error: '.ibase_errmsg(); break;
  case 'sqlite': $this->error .= 'Error: '.sqlite_last_error($this->con).': '.sqlite_error_string(sqlite_last_error($this->con)); break;
  case 'db2':    $this->error .= 'Error: '.db2_conn_errormsg(); break;
  case 'oci':    $e=oci_error(); $this->error .=  'Error: '.$e['message']; break;
  }
  if ( $bShowError && !empty($this->error) ) echo '<br/>'.$this->error;
  if ( $bStop ) exit;
  return false;
}

// --------

}