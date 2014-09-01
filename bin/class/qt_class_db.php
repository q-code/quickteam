<?php // version 6.0 build:20140817

class cDB
{

public $type; // server type
public $error = '';
public $debug = false;
public $pdo; // pdo object when using pdo.mysql
public $stats;

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
  if ( !empty($_SESSION['QTstatsql']) ) $this->StartStats();

  // Use PDO or CONNECT
  if ( substr($this->type,0,4)==='pdo.') return $this->PDOconnect();
  return $this->Connect();
}

// This function connects the database
// return boolean $is_connected Returns true if connection was successful otherwise false

private function PDOconnect()
{
  try
  {
    switch($this->type)
    {
    case 'pdo.mysql': $this->pdo = new PDO('mysql:host='.$this->host.';dbname='.$this->db, $this->user, $this->pwd); break;
    case 'pdo.sqlsrv': $this->pdo = new PDO('sqlsrv:Server='.$this->host.';Database='.$this->db, $this->user, $this->pwd); break;
    case 'pdo.pg': $this->pdo = new PDO('pgsql:host='.$this->host.';dnname='.$this->db, $this->user, $this->pwd); break;
    case 'pdo.sqlite': $this->pdo = new PDO('sqlite:'.$this->host, $this->user, $this->pwd); break;
    case 'pdo.ibase': $this->pdo = new PDO('firebird:dbname='.$this->host, $this->user, $this->pwd); break;
    case 'pdo.oci': $this->pdo = new PDO('oci:dbname='.$this->host, $this->user, $this->pwd); break;
    default: die('PDO interface ['.$this->type.'] is not supported');
    }
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return true;
  }
  catch (PDOException $e)
  {
    $this->error = 'Unable to connect: '.$e->getMessage();
    $this->pdo = null;
    echo '<p class="small error">'.$this->error.'</p>';
    return false;
  }
}

private function Connect()
{
  switch($this->type)
  {
  case 'mysql4':
  case 'mysql': $this->con = mysql_connect($this->host,$this->user,$this->pwd); break;
  case 'pg': $this->con = pg_connect('host='.$this->host.' dbname='.$this->db.' user='.$this->user.' password='.$this->pwd); break;
  case 'ibase': $this->con = ibase_connect($this->host.':'.$this->db,$this->user,$this->pwd); break;
  case 'sqlite': $this->con = sqlite_open($this->db,0666,$e) or die($e); break;
  case 'sqlsrv':
  $arr=array('Database'=>$this->db,'UID'=>$this->user,'PWD'=>$this->pwd);
  // use windows authentication if no UID and no PWD
  if ( empty($this->user) && empty($this->pwd) ) $arr=array('Database'=>$this->db);
  $this->con = sqlsrv_connect($this->host,$arr);
  break;
  case 'db2':   $this->con = db2_connect($this->host,$this->user,$this->pwd); break;
  case 'oci':   $this->con = oci_connect($this->user,$this->pwd,$this->db); break;
  default: die('db_type ['.$this->type.'] not supported. Must be mysql, sqlsrv, pg, ibase, sqlite, db2 or oci');
  }

  // check connection
  if ( !$this->con ) die( 'Wrong connection parameters! Cannot establish connection to host.');

  // Selection database (if required)
  switch ($this->type)
  {
  case 'mysql': if ( mysql_select_db($this->db,$this->con) ) return true; break;// no select with sqlsrv
  case 'db2':   if ( db2_select_db($this->db,$this->con) ) return true; break;
  default: return true;
  }
  die('Wrong database parameters! Cannot select database.');
}

// ---------

public function Disconnect()
{
  $this->pdo = null;
  switch($this->type)
  {
  case 'mysql': mysql_close($this->con); break;
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

public function QueryErr($strSql,&$error,$bShowError=false)
{
  // same as Query but add the error in $error (passed by reference) and direct display is disabled
  if ( !$this->Query($strSql,$bShowError) ) { $error = $this->error; return false; }
  return true;
}
public function Query($sql,$bShowError=true)
{
  if ( $this->debug || isset($_SESSION['QTdebugsql']) ) printf('<p class="small" style="margin:1px">SQL: %s</p>',$sql);

  switch($this->type)
  {
  case 'pdo.mysql': $this->qry = $this->pdo->query($sql); break; // warning this->qry is now a PDOstatement object
  case 'mysql': $this->qry = mysql_query($sql,$this->con); break;
  case 'sqlsrv': $sql = str_replace('"',"'",$sql); $this->qry = sqlsrv_query($this->con,$sql); break;
  case 'pg': $sql = str_replace('"',"'",$sql); $this->qry = pg_query($this->con,$sql); break;
  case 'ibase': $this->qry = ibase_query($this->con,$sql); break;
  case 'sqlite':$this->qry = sqlite_query($this->con,$sql); break;
  case 'db2': $this->qry = db2_query($this->con,$sql); break;
  case 'oci': $sql = str_replace('"',"'",$sql); $this->qry = oci_parse($this->con,$sql); oci_execute($this->qry); break;
  case 'pdo.sqlsrv':
  case 'pdo.pg':
  case 'pdo.ibase':
  case 'pdo.sqlite':
  case 'pdo.db2':
  case 'pdo.oci': $this->qry = $this->pdo->query($sql); break; // warning this->qry is now a PDOstatement object
  default: die('db_type ['.$this->type.'] not supported.');
  }

  if ( isset($this->stats) ) { ++$this->stats['num']; $this->stats['end']=(float)vsprintf('%d.%06d', gettimeofday()); }
  if ( !$this->qry ) return $this->Halt($bShowError); // puts error message in $this->error, echos error message, and returns false
  return true; // success
}
public function ExecErr($strSql,&$error,$bShowError=true)
{
  // same as Query but add the error in $error (passed by reference) and direct display is disabled
  $n = $this->Exec($strSql,$bShowError);
  if ( $n===false ) { $error = $this->error; return false; }
  return true;
}
public function Exec($sql,$bShowError=true)
{
  if ( $this->debug || isset($_SESSION['QTdebugsql']) ) printf('<p class="small" style="margin:1px">SQL: %s</p>',$sql);
  if ( isset($this->stats) ) ++$this->stats['num'];
  if ( substr($this->type,0,4)==='pdo.' )
  {
      try
      {
        return $this->pdo->exec($sql); // Returns the number of affected rows. With CREATE TABLE, returns false if table exists
        /* !!!! if error notification works, remove this
        $n =  $this->pdo->exec($sql); // Returns the number of affected rows. With CREATE TABLE, returns false if table exists
        if ( $n===false )
        {
          $this->error = 'Unable to execute: '.$sql;
          if ( $bShowError ) echo '<p class="small error">'.$this->error.'</p>';
          return false;
        }
        return $n;
        */
      }
      catch (PDOException $e)
      {
        $this->error = $e->getMessage();
        if ( $bShowError ) echo '<p class="small error">'.$this->error.'</p>';
        return false;
    }
  }
  // Non PDO uses defaut query method
  return $this->Query($sql,$bShowError);
}

// --------

public function Nextid($table='',$field='id',$where='')
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

public function Getrow()
{
  $row = false;
  switch ($this->type)
  {
  case 'pdo.mysql': $row = $this->qry->fetch(PDO::FETCH_ASSOC); break;
  case 'mysql': $row = mysql_fetch_assoc($this->qry); break; // php 5.0.3
  case 'sqlsrv':$row = sqlsrv_fetch_array($this->qry,SQLSRV_FETCH_ASSOC); break;
  case 'mssql':
    $row = mssql_fetch_assoc($this->qry); if ($row===false) return false;
    // this fix a known bug in mssql_fetch_assoc that add a space to empty string
    foreach($row as $key=>$val) if ( is_string($val) && strlen($val)===1 ) $row[$key] = trim($val);
    break;
  case 'pg': $row = pg_fetch_assoc($this->qry); break;// php 4.3.0
  case 'ibase': $row = ibase_fetch_assoc($this->qry); break;// php 4.3.0
  case 'sqlite':
    $row = sqlite_fetch_array($this->qry,SQLITE_ASSOC);// php 5.0
    if ( $row===false ) return false;
    $arr = array();
    foreach($row as $key=>$val)
    {
      if ( substr($key,1,1)==='.') $key = strtolower(substr($key,2));
      $arr[$key]=$val;
    }
    $row = $arr;
    break;
  case 'db2': $row = db2_fetch_assoc($this->qry); break; // php unknown version
  case 'oci':
    $row = oci_fetch_assoc($this->qry); if ( $row===false ) return false;
    $arr = array();
    foreach($row as $key=>$val) $arr[strtolower($key)]=$val;
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

private function Halt($bShowError=true,$bStop=false)
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

public function StartStats()
{
  $t = (float)vsprintf('%d.%06d', gettimeofday());
  $this->stats=array( 'num'=>0, 'start'=>$t, 'pagestart'=>$t );
}

}