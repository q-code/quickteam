<?php

// QT re-usable component 1.2 build:20140825

// ========

// Memcache usage: This mechanism put in memory frequently used objects (i.e. list of domains, sections, statuses...)
// memcacheCreate() initialize and open connection (if MEMCACHE_HOST is defined). Can return false with a warning message.
// memGet()
// memSet() use memcache library OR session variables (i.e. when memcache is disable or when memcache daemon faill to respond)
// memcacheGet()
// memcacheSet() use memcache only
// Note: These functions return FALSE in case of failure (when memcache not enabled or daemon not responding). It's thus not recommanded to use metSet to store FALSE

// About memGet()
// As memcache has a timeout (or daemon may be not responding),
// memGet() is designed to REGENERATE the values when they cannot be fecthed from memory.
// Using memGet() to initialize memory is thus recommanded, for these frequently used objects values.
// BUT if you use memGet() with other keys, these may be regenerated to $default value ! (at your own risk)

function memcacheCreate(&$warning)
{
  $memcache=false;
  if ( MEMCACHE_HOST )
  {
    if ( class_exists('Memcache') )
    {
    $memcache = new Memcache;
    if ( !$memcache->connect(MEMCACHE_HOST,MEMCACHE_PORT) ) { $warning='Unable to contact memcache daemon ['.MEMCACHE_HOST.' port '.MEMCACHE_PORT.']. Turn this option to false in '.APP.'_init...'; $memcache=false; }
    }
    else
    {
    $warning='Memcache library not found. Turn this option to false in '.APP.'_init...';
    $memcache=false;
    }
  }
  
  return $memcache;
}
function memGet($key,$default=false)
{
  $obj = memcacheGet($key);
  if ( $obj===false )
  {
    // Check session if not in memcache
    if ( isset($_SESSION[QT][$key]) ) return $_SESSION[QT][$key];
    // Regenerate when not in memory
    $obj = cVIP::SysInit($key,$default);
    memSet($key,$obj);
  }
  return $obj;
}
function memSet($key,$obj,$timeout=300)
{
  if ( memcacheSet($key,$obj,$timeout)===false ) $_SESSION[QT][$key]=$obj;
}
function memcacheGet($key)
{
  global $memcache; return $memcache ? $memcache->get($key) : false;
}
function memcacheSet($key,$obj,$timeout=300)
{
  global $memcache; return $memcache ? $memcache->set($key,$obj,false,$timeout) : false;
}
function memcacheQuery($key,$sql,$timeout=300)
{
  // Caching a sql query a few minutes
  // Note this uses memcache only and NOT $_SESSION
  if ( empty($key) ) $key = md5(APP.$sql);

  // Get the cache from memcache
  if ( ($cache=memcacheGet($key))===false )
  {
    // If no cache response, runs the query to populate $cache
    $cache = false;
    global $oDB;
    if ( $oDB->Query($sql) )
    {
    $i = 0;
    while( $row=$oDB->Getrow() ) { $cache[$i]=$row; ++$i; }
    // Save $cache into the memcache. Attention if memcache daemon not running or not responding, this will failled (setCache just returns false)
    memcacheSet($key,$cache,$timeout);
    }
  }
  return $cache;
}
function memcacheQueryCount($key,$sql,$timeout=300,$field=false)
{
  // This returns a db count [int] from memcache, or put the value in cache if not yet cached.
  // When running the query, this function uses the first column (as int).
  // An other column can be fetched ($field). Note that if $field cannot be found, the first column is used.
  if ( empty($key) ) $key = md5(APP.$sql);
  $cache=memcacheGet($key);
  if ( $cache===false )
  {
    global $oDB;
    if ( $oDB->Query($sql) )
    {
      $arr = $oDB->Getrow();
      $cache = (int)reset($arr); // first column
      if ( $field && isset($arr[$field]) ) $cache = (int)$arr[$field];
      memcacheSet($key,$cache,$timeout);
    }
  }
  return $cache;
}
function memUnset($key)
{
   memcacheUnset($key); if ( isset($_SESSION[QT][$key]) ) unset($_SESSION[QT][$key]);
}
function memcacheUnset($key)
{
  global $memcache; return ($memcache) ? $memcache->delete($key) : false;
}

// ========

class cLang
{
  public static function Add($strType='',$strLang='en',$strId='',$strName='',$bCheck=false)
  {
    QTargs( 'cLang::Add',array($strType,$strLang,$strId,$strName,$bCheck),array('str','str','str','str','boo') );
    QTargs( 'cLang::Add',array($strType,$strLang,$strId,$strName),'empty' );

    // Process
    $prefix = strtoupper(substr(constant('QT'),0,3));
    if ( !defined($prefix.'_CONVERT_AMP') ) define($prefix.'_CONVERT_AMP',false);

    global $oDB;
    if ( $bCheck )
    {
    $oDB->Query('SELECT count(objid) AS countid FROM '.TABLANG.' WHERE objtype="'.$strType.'" AND objlang="'.strtolower($strLang).'" AND objid="'.$strId.'"');
    $row=$oDB->Getrow();
    if ( $row['countid']!=0 ) return False;
    }
    return $oDB->Exec('INSERT INTO '.TABLANG.' (objtype,objlang,objid,objname) VALUES ("'.$strType.'","'.strtolower($strLang).'","'.$strId.'","'.addslashes(QTconv($strName,'3',constant($prefix.'_CONVERT_AMP'),false)).'")');
  }
  public static function Delete($strType='',$strId='')
  {
    if ( is_array($strType) ) $strType = implode('" OR objtype="',$strType);
    QTargs( 'cLang::Delete',array($strType,$strId) );
    QTargs( 'cLang::Delete',array($strType,$strId),'empty' );

    // Process

    global $oDB;
    return $oDB->Exec('DELETE FROM '.TABLANG.' WHERE (objtype="'.$strType.'") AND objid="'.$strId.'"');
  }
  public static function Get($strType='',$strLang='en',$strId='*')
  {
    // Return the object name (translated)
    // Can return an array of object names (in this language) when $strId is '*'
    // Can return an array of object translation when $strLang is '*'

    QTargs('cLang::Get',array($strType,$strLang,$strId));
    QTargs('cLang::Get',array($strType,$strLang,$strId),'empty');
    if ( $strId==='*' && $strLang==='*' ) Die('cLang::Get: Arg 2 and 3 cannot be *');

    // Process

    global $oDB;
    if ( $strId==='*' )
    {
      $arr = array();
      $oDB->Query('SELECT objid,objname FROM '.TABLANG.' WHERE objtype="'.$strType.'" AND objlang="'.strtolower($strLang).'"');
      while($row=$oDB->Getrow())
      {
        if ( !empty($row['objname']) ) $arr[$row['objid']]=$row['objname'];
      }
      return $arr;
    }
    elseif ( $strLang==='*' )
    {
      $arr = array();
      $oDB->Query('SELECT objlang,objname FROM '.TABLANG.' WHERE objtype="'.$strType.'" AND objid="'.$strId.'"');
      while($row=$oDB->Getrow())
      {
        $arr[$row['objlang']]=$row['objname'];
      }
      return $arr;
    }
    else
    {
      $oDB->Query('SELECT objname FROM '.TABLANG.' WHERE objtype="'.$strType.'" AND objlang="'.strtolower($strLang).'" AND objid="'.$strId.'"');
      $row=$oDB->Getrow();
      return (empty($row['objname']) ? '' : $row['objname']);
    }
  }
}

// ========

class cStats
{
  // This uses dynamic properties
  // Any properties can be created (i.e. to create a property 'items' just set $oStats->items=0)
  // The properties are stored in a session variable (i.e. $_SESSION[QT]['sys_stat_items'])
  // It's USELESS to create several object cStats (there is only one storage per session)
  // NOTE: when properties is not defined, __get generate an error
  function __get($prop)
  {
    if ( !isset($this->$prop) && isset($_SESSION[QT]['sys_stat_'.$prop]) ) $this->$prop = $_SESSION[QT]['sys_stat_'.$prop];
    if ( isset($this->$prop) ) return $this->$prop;
    echo 'cStats: undefined properties '.$prop;
  }
  function __set($prop,$value)
  {
    $this->$prop = $value;
    $_SESSION[QT]['sys_stat_'.$prop] = $this->$prop;
  }
  public function RemoveProperty($prop)
  {
    if ( isset($_SESSION[QT]['sys_stat_'.$prop]) ) unset($_SESSION[QT]['sys_stat_'.$prop]);
    if ( isset($this->$prop) ) unset($this->$prop);
  }
}

// ========

class cMsg
{
  // This class handles a message comming from a previous page (session variable 'pagedialog')
  // The message is displayed thanks to a jquery function from *_page_header.
  // The session variable must be destroyed after display width cMsg::Reset()
  // cMsg::getType()
  // cMsg::getFulltext()
  // cMsg::getItems()
  // cMsg::getText()
  // The class can also be instanciated for backward compatibility (see after)

  public static function getType()
  {
    if ( !empty($_SESSION['pagedialog']) )
    {
    $arr = explode('|',$_SESSION['pagedialog']);
    return strtolower(substr($arr[0],0,1));
    }
    return 'o'; //i=info, e=error, w=warning, o=ok (default)
  }
  public static function getFulltext()
  {
    if ( !empty($_SESSION['pagedialog']) )
    {
    $arr = explode('|',$_SESSION['pagedialog']);
    $i = (isset($arr[2]) ? (int)$arr[2] : 0);
    if ( isset($arr[1]) ) return str_replace('"','',$arr[1]).($i>1 ? ' ('.$i.')' : '');
    }
    return '';
  }
  public static function getItems()
  {
    if ( !empty($_SESSION['pagedialog']) )
    {
    $arr = explode('|',$_SESSION['pagedialog']);
    if ( isset($arr[2]) ) return (int)$arr[2];
    }
    return 0;
  }
  public static function getText()
  {
    if ( !empty($_SESSION['pagedialog']) )
    {
    $str = cMsg::getFulltext();
    return (isset($str{65}) ? substr($str,0,60).'...' : $str);
    }
    return '';
  }
  public static function Reset()
  {
    $_SESSION['pagedialog']=null;
  }

  // For backward compatiblity, the non-static class

  public $text; // '' means nothing (no display)
  public $fulltext;
  public $type; // i=info, e=error, w=warning, o=ok (default)
  public $items; // affected items
  public function __construct()
  {
    if ( !empty($_SESSION['pagedialog']) ) $this->FromString($_SESSION['pagedialog']);
  }
  public function Clear()
  {
    $_SESSION['pagedialog']=null;
  }
  public function FromString($arr)
  {
    if ( is_string($arr) ) $arr=explode('|',$arr);
    if ( is_array($arr) )
    {
      if ( isset($arr[0]) ) $this->type = strtolower(substr($arr[0],0,1));
      if ( isset($arr[1]) ) $this->fulltext = str_replace('"','',$arr[1]);
      if ( isset($arr[2]) ) $this->items = (int)$arr[2];
      if ( $this->items>1 ) $this->fulltext .= ' ('.$this->items.')';
      $this->text = QTtrunc($this->fulltext,64);
    }
    return $this->text;
  }
}