<?php

// QuickTeam 3.0 build:20140608

class cItem
{

// --------

public $id = -1;
public $username = ''; // this is the login BUT also the main displayed name. It's recommended to use the fullname (John Smith)
public $pwd = '';
public $role = 'V';
public $type = '';
public $status = 'Z';   // status must exist !
public $coppa = '0'; // is children (0=No, 1=Yes with parent agree, 2=Yes without parent agree)

public $title = '';    // M. Mr, Mme ...
public $firstname = '';
public $midname = '';
public $lastname = '';
public $alias = '';
public $fullname = ''; // computed info
public $birthdate = '0';
public $age = 0;      // computed info
public $nationality = '';
public $sexe = '';
public $picture = '';
public $secret_q = '';
public $secret_a = '';

public $address = '';
public $phones = '';
public $emails = '';
public $www = '';
public $privacy = '';
public $descr = '';
public $firstdate = '0';

public $teamid1 = '';
public $teamid2 = '';
public $teamrole1 = '';
public $teamrole2 = '';
public $teamdate1 = '0';
public $teamdate2 = '0';
public $teamvalue1 = '';
public $teamvalue2 = '';
public $teamflag1 = '';
public $teamflag2 = '';

public $x;
public $y;
public $z;

public $ufield = ''; // used when searching: contains the fields on which search was performed

// --------
// attention: sqlite can return fieldname including the prefix alias (p.id)

function __construct($aUser=null,$bPrivate=false)
{
  if ( isset($aUser) )
  {
    if ( is_int($aUser) )
    {
      if ( $aUser<0 ) die('Wrong id in cItem');
      global $oDB;
      $oDB->Query('SELECT * FROM '.TABUSER.' WHERE id='.$aUser);
      $row = $oDB->Getrow();
      if ( $row===False ) die('No user '.$aUser);
      $this->MakeFromArray($row,$bPrivate);
    }
    elseif ( is_array($aUser) )
    {
      $this->MakeFromArray($aUser,$bPrivate);
    }
    else
    {
      die('Invalid constructor parametre #1 for the class cItem: '.$aUser);
    }
  }

  // computed information
  if ( !empty($this->birthdate) ) $this->SetAge();
  $this->SetFullname();
}

// --------

public function Create($intSection=-1)
{
  $oDB->Query('INSERT INTO '.TABUSER.' (id,role,username,lastname,pwd,emails,children,status,firstdate) VALUES ('.$id.',"U","'.htmlspecialchars($strTitle,ENT_QUOTES).'","'.htmlspecialchars($strTitle,ENT_QUOTES).'","'.sha1($strNewpwd).'","'.$strMail.'","0","'.$_POST['status'].'","'.date('Ymd').'")');
}

// --------

private function MakeFromArray($arr,$bPrivate=false)
{
  if ( !is_array($arr) ) die('Invalid argument for cItem->MakeFromArray');
  foreach ($arr as $key => $value)
  {
    if  ( !is_null($value) ) {
    switch ($key) {
    case 'id':         $this->id         = (int)$value; break;
    case 'children':   $this->coppa      = (int)$value; break;
    default: $this->$key = $value; break;
    }}
  }
  // private information
  if ( $bPrivate && !empty($this->privacy) ) $this->SetPrivate();
}

// --------

function SetPrivate()
{
  global $oVIP;
  foreach (array('address','phones','emails','descr') as $strKey)
  {
  if ( $oVIP->IsPrivate($this->privacy,$strKey,$this->id) ) $this->$strKey='';
  }
  // coordinates
  if ( $oVIP->IsPrivate($this->privacy,'coord',$this->id) ) { $this->x=null; $this->y=null; }
}

// --------

function SetAge()
{
  if ( isset($_SESSION[QT]['fields_u']) ) {
  if ( strstr($_SESSION[QT]['fields_u'],'birthdate') ) {
  if ( !empty($this->birthdate) ) {
  if ( isset($_SESSION[QT]['fields_c']) ) {
  if ( strstr($_SESSION[QT]['fields_c'],'age') ) {
    $this->age = date('Y') - intval(substr(strval($this->birthdate),0,4)) - 1;
    if ( date('md') >= intval(substr(strval($this->birthdate),4,4)) ) $this->age++;
    if ( $this->age<0 ) $this->age=0;
  }}}}}
}

// --------

public function SetFullname()
{
  $this->fullname = cItem::MakeFullname($this->username,$this->lastname,$this->midname,$this->firstname,$this->title,$this->alias);
}

// --------

public function GetStatusIcon($class='ico i-status')
{
	return cVIP::GetStatusIcon($this->status,$class);
}
public function GetStatusName()
{
	return cVIP::GetStatusName($this->status);
}
// --------

public static function MakeFullname($username='visitor',$lastname='',$midname='',$firstname='',$title='',$alias='')
{
  $strDisabled='';
  if ( isset($_SESSION[QT]['fields_u']) )
  {
    if ( !strstr($_SESSION[QT]['fields_u'],'title') ) $strDisabled .= 'title,';
    if ( !strstr($_SESSION[QT]['fields_u'],'firstname') ) $strDisabled .= 'firstname,';
    if ( !strstr($_SESSION[QT]['fields_u'],'lastname') ) $strDisabled .= 'lastname,';
    if ( !strstr($_SESSION[QT]['fields_u'],'midname') ) $strDisabled .= 'midname,';
    if ( !strstr($_SESSION[QT]['fields_u'],'alias') ) $strDisabled .= 'alias,';
  }
  $str = $username;
  if ( !empty($lastname) && !strstr($strDisabled,'lastname') )
  {
      $str = $lastname;
      if ( !empty($midname) && !strstr($strDisabled,'midname') ) $str = $midname.' '.$str;
      if ( !empty($firstname) && !strstr($strDisabled,'firstname') ) $str = $firstname.' '.$str;
      if ( !empty($title) && !strstr($strDisabled,'title') ) $str = $title.' '.$str;
      if ( !empty($alias) && !strstr($strDisabled,'alias') ) $str .= ' "'.$alias.'"';
  }
  return $str;
}

// --------

function Delete($bStat=true)
{
  QTargs('cItem->Delete',array($bStat),array('boo'));
  if ( $this->id<2 ) die('cItem->Delete: Wrong id cannot delete user 0 and 1');

  global $oDB;
  $oDB->Query('UPDATE '.TABSECTION.' SET modid=1, modname="Administrator" WHERE modid='.$this->id);
  $oDB->Query('DELETE FROM '.TABS2U.' WHERE userid='.$this->id);
  $oDB->Query('DELETE FROM '.TABUSER.' WHERE id='.$this->id);
  $oDB->Query('DELETE FROM '.TABCHILD.' WHERE id='.$this->id);
  $oDB->Query('DELETE FROM '.TABINDEX.' WHERE userid='.$this->id);

  // remove images

  if ( !empty($this->picture) ) {
  if ( file_exists(QTE_DIR_PIC.$this->picture) ) {
    unlink(QTE_DIR_PIC.$this->picture);
  }}

  // update stats

  if ( $bStat && isset($_SESSION[QT]['sys_sections']) )
  {
  foreach($_SESSION[QT]['sys_sections'] as $intId=>$arr) cSection::UpdateStats($intId);
  }
}

// --------

public static function InSection($intSection=-1,$strAction='add',$id=null)
{
  if ( !is_numeric($intSection) ) die('Wrong id in Item->InSection');
  if ( !is_string($strAction) ) die('Wrong id in Item->InSection');
  if ( $intSection<0 ) die('Wrong id in Item->InSection');
  if ( !isset($id) ) $id = $this->id;
  if ( $id<0)  die('Wrong id in Item->InSection');
  if ( is_string($id) ) $id = (int)$id;

  global $oDB;
  switch ($strAction)
  {
  case 'add':
    $oDB->Query('SELECT count(*) as countid FROM '.TABS2U.' WHERE sid='.$intSection.' AND userid='.$id);
    $row = $oDB->Getrow();
    if ( $row['countid']==0 )
    {
    $oDB->Query('INSERT INTO '.TABS2U.' (sid,userid,issuedate) VALUES ('.$intSection.','.$id.',"'.date('Ymd').'")');
    }
    else
    {
    return false;
    }
    break;
  case 'rem':
    $oDB->Query('DELETE FROM '.TABS2U.' WHERE sid='.$intSection.' AND userid='.$id);
      // add in the garbage collector if necessary
      if ( cSection::CountItems($id)==0 ) { $oDB->Query('INSERT INTO '.TABS2U.' (sid,userid,issuedate) VALUES (0,'.$id.',"'.date('Ymd').'")'); return false; }
    break;
  }
  return true;
}

// --------

function GetKeywords($arrFields)
{
  $arrKeys = array();
  foreach ($arrFields as $strField)
  {
    $arrKeys[$strField] = array();
    $str = strtoupper(strip_tags($this->$strField));
    $str = QTconv($str,'-4');
    $str = strtr($str,'(){}+-=;,:.@/?!"','                ');
    $str = str_replace('\'',' ',$str);
    $str = str_replace('  ',' ',$str);
    $arr = explode(' ',$str);
    foreach ($arr as $str)
    {
     if ( strlen($str)>=2 ) $arrKeys[$strField][] = $str;
    }
    $arrKeys[$strField] = array_unique($arrKeys[$strField]);
  }
  return $arrKeys;
}

// --------

function SaveKeywords($arrKeys)
{
  global $oDB;
  $arrFields = array_keys($arrKeys);
  foreach ($arrFields as $strField)
  {
    $oDB->query('DELETE FROM '.TABINDEX.' WHERE userid='.$this->id.' AND ufield="'.$strField.'"');
    foreach ($arrKeys[$strField] as $strValue)
    {
    $oDB->query('INSERT INTO '.TABINDEX.' (userid,ufield,ukey) VALUES ('.$this->id.',"'.$strField.'","'.$strValue.'")');
    }
  }
}

// --------

public static function SetCoord($id,$coord)
{
  // Coordinates must be a string 'y,x'.
  // '0,0' can be use to remove a coordinates.
  // z is not used here
  if ( empty($coord) ) $coord='0,0';
  $y=null;
  $x=null;
  $coord = explode(',',$coord);
  if ( isset($coord[0]) ) $y = (float)$coord[0];
  if ( isset($coord[1]) ) $x = (float)$coord[1];
  if ( EmptyFloat($y) && EmptyFloat($x) ) { $y=null; $x=null; }
  global $oDB;
  $oDB->Query('UPDATE '.TABUSER.' SET y='.(isset($y) ? $y : 'NULL').',x='.(isset($x) ? $x : 'NULL').' WHERE id='.$id);
}

}