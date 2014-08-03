<?php

/**
* qt_lib_sys.php
* ------------
* version: 4.5 build:2014021
* This is a library of public functions
* ------------
* APPCST
* array_prefix_keys
* Error
* Href
* GetLang
* Translate
* L
* QTcheckL
* QTiso
* QTargs
* QTasTag
* QTargimplode
* QTargexplode
* QTarradd
* QTexplode
* QTimplode
* QTexplodeUri
* QTimplodeUri
* QTuritoform
*/

function APPCST($s)
{
  // Return the constant with prefix APP
  if ( !defined('APP') ) die('Undefined constant APP');
  if ( !defined(strtoupper(APP.$s)) ) die('Undefined constant '.APP.$s);
  return constant(strtoupper(APP.$s));
}

// --------

function array_prefix_keys($str,$arrSource)
{
  // add the prefix $str to the keys in an array.
  if ( empty($str) || !is_string($str) ) die('array_prefix_keys: arg #1 must be a string');
  if ( !is_array($arrSource) ) die('array_prefix_keys: arg #2 must be an array');
  $arr = array();
  foreach($arrSource as $key=>$value) $arr[$str.$key]=$value;
  return $arr;
}

// --------

function Error($i=0)
{
  include Translate('@_error.php');
  if ( isset($e[$i]) ) return $e[$i];
  return 'Error '.$i;
}

// --------

function Href($str='')
{
  // When urlrewriting is active, the url can be displayed in html format (they will be converted by the server's rewrite rule).
  // This function transforms a php url into a html like url (the url can have arguments): 'qnm_login.php' is displayed as 'login.html'.
  // Note: Don't worry, server's rewriting has NO effect when the url is in php format (i.e. when this function is not used or when QTx_URLREWRITE is FALSE)
  if ( empty($str) ) { global $oVIP; $str=$oVIP->selfurl; }
  if ( empty($str) ) $str=$_SERVER['PHP_SELF'];
  if ( APPCST('_URLREWRITE') && substr($str,0,4)==APP && strstr($str,'.php') )
  {
    $str = substr($str,4);
    $str = str_replace('.php','.html',$str);
  }
  return $str;
}

// ---------

function GetLang($str='')
{
  if ( empty($str) ) $str = GetSetting('language','english');
  return 'language/'.$str.'/';
}

// --------

function Translate($strFile,$at=true)
{
  if ( $at ) $strFile = str_replace('@',APP,$strFile);
  if ( file_exists(GetLang().$strFile) ) Return GetLang().$strFile;
  Return 'language/english/'.$strFile;
}

// --------

function L($key='',$int=false,$bInclude=true)
{
  // Returns the corresponding word or the lowercase version of the word (to request the lowercase version of a word, just pass his key in lowercase):
  // Examples for a french language file:
  // L('Password') returns 'Mot de passe'
  // L('password') returns 'mot de passe'
  // Note: if a lowercase version is nevertheless defined the the language file (key in lowercase), that word will be returned.

  // Also searches the plural word if necessary: i.e. when $int>1 (plural version of a word can be define in the language file by $key+'s')
  // In case of $int is set (<>false) and $bInclude is true, the $int value is added before the word. ($int can be negative or 0)
  // L('Domain',0) returns '0 Domaine' // <2 thus no plural word
  // L('domain',1) returns '1 domaine' // <2 thus no plural word, but with lowercase
  // L('Domain',2) returns '2 Domaines'
  // Note: when the plural version (key+'s') is not defined in the language file,
  // the function still works and returns the singular word (with the value in front if requested).

  // Fallback:
  // If the requested word (key) is not defined in the language file, the function returns the key itself (without '_')
  // L('Unknown_key') returns 'Unknown key'
  // In addition a key like 'E_aaa' (used to describe an error code) will be converted to 'Error aaa' when not defined in the language file.

  // Debug:
  // If you define a session variable 'QTdebuglang' set to '1', the function will show in red the key not defined in the language file.

  global $L;
  if ( isset($L[$key]) )
  {
    $str = $L[$key];
    if ( $int!==false ) { if ( $int>1 && isset($L[$key.'s']) ) $str = $L[$key.'s']; }
  }
  elseif ( isset($L[ucfirst($key)]) )
  {
    $str = strtolower($L[ucfirst($key)]);
    if ( $int!==false ) { if ( $int>1 && isset($L[ucfirst($key.'s')]) ) $str = strtolower($L[ucfirst($key.'s')]); }
  }
  else
  {
  $str = str_replace('_',' ',$key); // When word is missing, returns the key code without _
  if ( substr($key,0,2)=='E_' ) $str = 'error: '.substr($str,2);
  if ( isset($_SESSION['QTdebuglang']) && $_SESSION['QTdebuglang']==='1' ) $str = '<span style="color:red">'.$str.'</span>';
  }
  return ($int!==false && $bInclude ? $int.' ' : '').$str; // When $int<>false (and $bInclude is true) the value is merged with the word
}

// --------

function QTcheckL($arr)
{
  if ( is_string($arr) ) $arr=explode(';',$arr);
  if ( !is_array($arr) ) die('QTcheckL: arg #1 be an array');
  foreach($arr as $str) if ( !isset($_SESSION['L'][$str]) ) $_SESSION['L'][$str] = cLang::Get($str,QTiso(),'*');
}

// --------

function QTiso($str='')
{
  if ( empty($str) ) $str = GetSetting('language','english');
  switch(strtolower($str))
  {
  case 'english': return 'en'; break;
  case 'francais': return 'fr'; break;
  case 'nederlands': return 'nl'; break;
  case 'italiano': return 'it'; break;
  case 'espanol': return 'es'; break;
  default: include 'bin/'.APP.'_lang.php'; $arr=array_flip(QTarrget($arrLang,2)); if ( isset($arr[$str]) ) return $arr[$str]; break;
  }
  return 'en';
}

// --------

function QTarradd($arr,$strKey,$strValue=null)
{
  // Add (or remove) a key+value to the array.
  // When $strValue is null, the key is not set (or removed if existing)
  if ( !is_array($arr) ) die('QTarradd: arg #1 must be an array');
  if ( !is_string($strKey) ) die('QTarradd: arg #2 must be a string');
  if ( isset($arr[$strKey]) ) unset($arr[$strKey]);
  if ( is_null($strValue) ) return $arr;
  $arr[$strKey] = $strValue;
  return $arr;
}

// --------

function QTarrget($arr,$key='title')
{
  // Converts an array of arrays into a simple array where the values are the [$key]element of each array (indexes are preserved).
  // When the [$key]element doesn't existing, the result will include a NULL.
  // If on element of $arr is not an array, it REMAINS in the result. $key can be integer or string.
  if ( !is_array($arr) ) die('QTarrget: arg #1 must be an array');
  foreach($arr as $k=>$a) {
  if ( is_array($a) ) {
    if ( isset($a[$key]) ) { $arr[$k]=$a[$key]; } else { $arr[$k]=null; }
  }}
  return $arr;
}

// --------

function QTexplode($str,$sep=';',$function='')
{
  // From a string "key1=value1;key2=value2" returns an array of key=>value.
  // When $str is empty or when there is no "=" the function returns an empty array
  // When duplicate keys exist, the last value overwrites previous values
  // A $function can be applied to each value (ex "urldecode" or "strtolower")

  if ( empty($str) ) return array();
  if ( !empty($function) && !function_exists($function) ) die('QTexplode: requested function ['.$function.'] is unknown');
  $arr = explode($sep,$str);
  $arrArgs = array();
  foreach($arr as $str)
  {
    if ( strstr($str,'=') )
    {
    $arrPart = explode('=',$str);
    $arrArgs[$arrPart[0]]= (empty($function) ? $arrPart[1] : $function($arrPart[1]));
    }
  }
  return $arrArgs;
}

// --------

function QTimplode($arr,$sep=';',$function='')
{
  // Build a string "key1=value1;key2=value2" from the array. Returns '' when the array is empty.
  // A $function can be applied to each value (ex "urlencode" or "strtolower")

  if ( !is_array($arr) ) die('QTimplode: arg #1 must be an array');
  if ( !is_string($sep) ) die('QTimplode: arg #2 must be a string');
  if ( !empty($function) && !function_exists($function) ) die('QTimplode: requested function ['.$function.'] is unknown');

  if ( count($arr)==0 ) return '';
  $str = '';
  foreach($arr as $key=>$value)
  {
  if ( !empty($function) ) $value = $function($value);
  $str .= ($str==='' ? '' : $sep).$key.'='.$value;
  }
  return $str;
}

// --------

function QTexplodeUri($str,$urldecode=true)
{
  // Same as QTexplode() but for url arguments (separated by & or by &amp;)
  // If $str is empty, the current URI is used. If URI contains full URL, the ? right part is used.
  // By default each argument is urldecoded.

  if ( empty($str) )
  {
    $arr = parse_url($_SERVER['REQUEST_URI']);
    if ( !isset($arr['query']) ) return array();
    $str = $arr['query'];
    if ( empty($str) ) return array();
  }
  else
  {
    // drop url part to keep uri part
    if ( strstr($str,'?') )
    {
    $arr = explode('?',$str); if ( empty($arr[1]) ) return array();
    $str = $arr[1];
    }
  }
  $str = str_replace('&amp;','&',$str);
  return QTexplode($str,'&',($urldecode ? 'urldecode' : ''));
}

// --------

function QTimplodeUri($arr,$urlencode=true,$sep='&amp;')
{
  // Same as QTimplode() but for url arguments. By default each argument is urlencoded.
  return QTimplode($arr,$sep,($urlencode ? 'urlencode' : ''));
}

// --------

function QTuritoform($uri,$bDropNullString=true,$reject=array())
{
  // Convert an uri to a serie of hidden-input
  // $uri can be an array or an url string
  // $reject is the list of arguments that must be dropped (can be an array of a csv-string)

  if ( is_string($uri) ) $uri = QTexplodeUri($uri);
  if ( !is_array($uri) ) die('QTuritoform: invalid argument #1');

  // $reject to remove parametres from the URI (can be csv)
  if ( is_string($reject) ) $reject=explode(',',$reject);
  if ( !is_array($reject) ) die('QTuritoform: invalid argument #3');
  
  $str = '';
  foreach($uri as $key=>$value)
  {
    if ( !empty($key) && !in_array($key,$reject) )
    {
    if ( $bDropNullString && $value=='' ) continue;
    $str .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
    }
  }
  return $str;
}

// --------

function QTargexplode($str='') { return QTexplodeUri($str,false); } // Reference to the old function
function QTargimplode($arr,$sep='&amp;') { return QTimplodeUri($arr,false,$sep); } // Reference to the old function