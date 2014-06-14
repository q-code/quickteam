<?php

// QuickTeam 3.0 build:20140608

class cLang
{

// --------

public static function Add($strType='',$strLang='en',$strId='',$strName='',$bCheck=false)
{
  // Check arguments

  if ( !is_string($strType) || empty($strType) ) die('cLang->Add: Argument #1 must be a string');
  if ( !is_string($strLang) || empty($strLang) ) die('cLang->Add: Argument #2 must be a string');
  if ( !is_string($strId) || empty($strId) ) die('cLang->Add: Argument #3 must be a string');
  if ( !is_string($strName) || empty($strName) ) die('cLang->Add: Argument #4 must be a string');

  // Add in objlang if not yet defined

  global $oDB;
  if ( $bCheck )
  {
  $oDB->Query('SELECT count(objid) AS countid FROM '.TABLANG.' WHERE objtype="'.$strType.'" AND objlang="'.strtolower($strLang).'" AND objid="'.$strId.'"');
  $row=$oDB->Getrow();
  if ( $row['countid']!=0 ) return False;
  }
  return $oDB->Query('INSERT INTO '.TABLANG.' (objtype,objlang,objid,objname) VALUES ("'.$strType.'","'.strtolower($strLang).'","'.$strId.'","'.addslashes(QTconv($strName,'3',QTE_CONVERT_AMP,false)).'")');
}

// --------

public static function Delete($strType='',$strId='')
{
  // Check arguments

  if ( is_array($strType) ) $strType = implode('" OR objtype="',$strType);
  if ( !is_string($strType) || empty($strType) ) die('cLang->Delete: Argument #1 must be a string');
  if ( !is_string($strId) || empty($strId) ) die('cLang->Delete: Argument #2 must be a string');

  // Process

  global $oDB;
  return $oDB->Query('DELETE FROM '.TABLANG.' WHERE (objtype="'.$strType.'") AND objid="'.$strId.'"');
}

// --------

// Return the object name (in this language)
// Can return an array of object names (in this language) when id=='*'

function GetName($strType='',$strLang='en',$strId='',$debug=false)
{
  // Check arguments

  if ( !is_string($strType) || empty($strType) ) die('cLang->GetName: Argument #1 must be a string');
  if ( !is_string($strLang) || empty($strLang) ) die('cLang->GetName: Argument #2 must be a string');
  if ( !is_string($strId) || empty($strId) ) die('cLang->GetName: Argument #3 must be a string');
  if ( $debug===true ) echo $strType,' | ',$strLang,' | ',$strId;

  // Read value

  global $oDB;

  if ( $strId=='*' )
  {
    $arr = array();
    $oDB->Query('SELECT objid,objname FROM '.TABLANG.' WHERE objtype="'.$strType.'" AND objlang="'.strtolower($strLang).'"');
    while($row=$oDB->Getrow())
    {
      if ( !empty($row['objname']) ) $arr[$row['objid']] = $row['objname'];
    }
      return $arr;
  }
  else
  {
    $oDB->Query('SELECT objname FROM '.TABLANG.' WHERE objtype="'.$strType.'" AND objlang="'.strtolower($strLang).'" AND objid="'.$strId.'"');
    $row=$oDB->Getrow();
    if ( !empty($row['objname']) ) return $row['objname'];
    return '';
  }
}

// --------

// Get an array with the object's translations (key is the iso-lang)
// In cas of list, the translations are ; separated

function GetTrans($strType='',$strId='')
{
  // Check arguments

  if ( !is_string($strType) || empty($strType) ) die('cLang->GetTrans: Argument #1 must be a string');
  if ( !is_string($strId) || empty($strId) ) die('cLang->GetTrans: Argument #2 must be a string');

  // Read values

  global $oDB;
  $arr = array();
  $oDB->Query('SELECT objlang,objname FROM '.TABLANG.' WHERE objtype="'.$strType.'" AND objid="'.$strId.'"');
  while($row=$oDB->Getrow())
  {
    $arr[$row['objlang']]=$row['objname'];
  }

  return $arr;
}

// --------

}