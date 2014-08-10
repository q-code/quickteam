<?php

// QuickTeam 3.0 build:20140608

class cDomain extends aQTcontainer
{

function __construct($aDom=null)
{
  if ( isset($aDom) )
  {
    if ( is_int($aDom) )
    {
      if ( $aDom<0 ) die('No domain '.$aDom);
      global $oDB;
      $oDB->Query('SELECT * FROM '.TABDOMAIN.' WHERE id='.$aDom);
      $row = $oDB->Getrow();
      if ( $row===False ) die('No domain '.$aDom);
      $this->MakeFromArray($row);
    }
    elseif ( is_array($aDom) )
    {
      $this->MakeFromArray($aDom);
    }
    else
    {
      die('Invalid constructor parameter #1 for the class cDomain');
    }
  }
}

// --------

private function MakeFromArray($arr)
{
  foreach ($arr as $strKey=>$oValue)
  {
    switch ($strKey)
    {
      case 'id': $this->id = (int)$oValue; break;
      case 'title': $this->title = $oValue; break;
    }
  }
}

// --------

function Rename($str='')
{
  if ( !is_string($str) || empty($str) ) die('cDomain->Rename: Argument #1 must be a string');

  global $oDB;
  $r = $oDB->Exec('UPDATE '.TABDOMAIN.' SET title="'.addslashes($str).'" WHERE id='.$this->id);
  if ( !$r ) return false;

  // Clear session to allow reload values
  memUnset('sys_domains');
  memUnset('sys_sections');
  return true;
}

// --------
// aQTcontainer implementations
// --------

public static function Create($title,$parentid)
{
  // parentid is no used here
  global $oDB, $error;
  $id = $oDB->Nextid(TABDOMAIN);
  $oDB->QueryErr('INSERT INTO '.TABDOMAIN.' (id,title,vorder) VALUES ('.$id.',"'.addslashes($title).'",0)', $error);

  // Clear session to allow reload values
  memUnset('sys_domains');
  return $id;
}

public static function Drop($id)
{
  if ( $id<1 ) die('cDomain->Drop: Cannot delete domain 0');
  global $oDB, $error;
  $oDB->QueryErr('UPDATE '.TABSECTION.' SET domainid=0 WHERE domainid='.$id, $error); // sections return to domain 0
  $oDB->QueryErr('DELETE FROM '.TABDOMAIN.' WHERE id='.$id, $error);
  cLang::Delete('domain','d'.$id);
  memUnset('sys_domains');
  memUnset('sys_sections');
  $_SESSION['L'] = array();
}

public static function MoveItems($id,$destination)
{
  if ( $id<0 || $destination<0 ) die('cDomain->MoveItems: source and destination cannot be <0');
  global $oDB, $error;
  $oDB->QueryErr('UPDATE '.TABSECTION.' SET domainid='.$destination.' WHERE domainid='.$id, $error);
}

public static function CountItems($id,$status)
{
  // Count Sections in domain $id
  if ( $id<0 ) die('cDomain->CountItems: id cannot be <0');
  global $oDB;
  $oDB->Query('SELECT count(*) as countid FROM '.TABSECTION.' WHERE domainid='.$id.(isset($status) ? ' AND status='.$status : ''));
  $row = $oDB->Getrow();
  return (int)$row['countid'];
}

// --------

}
