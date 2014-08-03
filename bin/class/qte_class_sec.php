<?php

// QuickTeam 3.0 build:20140608

// CONSTRAINT:
// users can be outside section (i.e. new user not yet in section)
// users without section are stored in the garbage collector (section 0)

/*
 * CLASS cSection
 *
 * constructor cSection: argument can be null, a section id, a section arr, a cSection object
 * function MakeFromArray: used by the constructor
 *
 */

class cSection extends aQTcontainer
{

public $name;      // Section name (translation)
public $vorder = 255;
public $forder = 'status_i;fullname;phones;emails;picture';
public $modid = 1;
public $modname = '(Admin)';
public $options='';   // Several stats (no used)
public $stats='';   // Several stats (no used)
public $descr='';
public $members = 0;
public $membersZ = 0;

// options
public $d_order='';  // Teams member default sort order (not used)
public $d_logo='';   // Section picture named s_x.gif/.jpeg/.jpg/.png (where x is the section id)

// not used (version 14)
public $template='T';      // 'T'=table, 'P'=preview, 'C'=compactlist;
public $ontop='0';   // status members to be on top (0=none)

// --------

function __construct($aSection=null)
{
  if ( isset($aSection) )
  {
    if ( is_int($aSection) )
    {
      if ( $aSection<0 ) die('Wrong id in cSection');
      global $oDB;
      $oDB->Query('SELECT * FROM '.TABSECTION.' WHERE id='.$aSection);
      $row = $oDB->Getrow();
      if ( $row===False ) die('No section '.$aSection);
      $this->MakeFromArray($row);
    }
    elseif ( is_array($aSection) )
    {
      $this->MakeFromArray($aSection);
    }
    else
    {
      die('Invalid constructor parametre #1 for the class cSection');
    }
  }
  // Read options/stats

  $this->ReadOptions();
  $this->ReadStats();

}

// --------

private function MakeFromArray($arr)
{
  if ( !is_array($arr) ) die('Invalid argument for cSection->MakeFromArray');
  foreach ($arr as $strKey => $oValue) {
  switch ($strKey) {
    case 'domainid': $this->pid     = (int)$oValue; break;
    case 'id':       $this->id      = (int)$oValue; break;
    case 'title':    $this->title = $oValue; break;
    case 'type':     $this->type    = (int)$oValue; break;
    case 'status':   $this->status  = (int)$oValue; break;
    case 'vorder':   $this->vorder  = (int)$oValue; break;
    case 'forder':   $this->forder  = $oValue; if ( empty($this->forder) ) $this->forder = 'status_i;fullname;phones;emails;picture'; break;
    case 'modid':    $this->modid   = (int)$oValue; if ( $this->modid<1 ) $this->modid=1; break;
    case 'modname':  $this->modname = $oValue; break;
    case 'stats':    $this->stats   = $oValue; break;
    case 'options':  $this->options = $oValue; break;
    // not used (version 14)
    case 'template': $this->template = $oValue; break;
    case 'ontop':    $this->ontop    = $oValue; break;
  }}
  $this->name = ObjTrans('sec','s'.$this->id,$this->title);
  $this->descr = ObjTrans('secdesc','s'.$this->id,false);
}

// --------

public function ShowInfo($strClassImage='',$strClassName='',$strClassDesc='',$strPictureUrl='',$qte_root='')
{
  $str = (empty($this->d_logo) ? '' : AsImg($qte_root.'document/section/'.$this->d_logo,'logo',$this->name,$strClassImage,'',$strPictureUrl));
  if ( !is_null($strClassName) ) $str .= '<p class="'.$strClassName.'">'.$this->name.'</p>';
  if ( !is_null($strClassDesc) ) $str .= '<p class="'.$strClassDesc.'">'.$this->descr.'</p>';
  if ( ! empty($str) ) echo $str;
}

// --------

public function GetIcon()
{
	return $_SESSION[QT]['skin_dir'].'/ico_section_'.$this->type.'_'.$this->status.'.gif';
}

public function GetLogo($bIconIfNone=false)
{
	$str = '';
	if ( !empty($this->d_logo) && file_exists('document/section/'.$this->d_logo) ) $str = 'document/section/'.$this->d_logo;
	if ( empty($str) && $bIconIfNone ) $str = $this->GetIcon();
	return $str;
}

public static function UpdateStats($intS=-1,$arrValues=array())
{
  // Check

  if ( !is_int($intS) || $intS<0) die('cSection::UpdateStats, Wrong id');

  // Process (provided values are not recomputed)

  if ( !isset($arrValues['members']) )  $arrValues['members']  = cSection::CountItems($intS,null); //SectionCount('members',$intS);
  if ( !isset($arrValues['membersZ']) ) $arrValues['membersZ'] = cSection::CountItems($intS,'Z'); //SectionCount('membersZ',$intS);
  foreach($arrValues as $strKey=>$strValue) { $arrValues[$strKey]=$strKey.'='.$strValue; }

  // Save

  global $oDB;
  $oDB->Exec('UPDATE '.TABSECTION.' SET stats="'.implode(';',$arrValues).'" WHERE id='.$intS );

  // Return the stat line

  return implode(';',$arrValues);
}

// --------
// aQTcontainer implementations
// --------

public static function Create($title,$parentid)
{
  if ( $parentid<0 ) die('cSection->Create: Cannot create section without domainid');
  global $oDB, $error;
  $id = $oDB->Nextid(TABSECTION);
  $oDB->QueryErr('INSERT INTO '.TABSECTION.' (domainid,id,title,type,status,stats,options,vorder,forder,modid,modname,template,ontop) VALUES ('.$parentid.','.$id.',"'.addslashes($title).'","0","0","members=0","logo=0",0,NULL,0,"Admin","T","0")', $error);
  unset($_SESSION[QT]['sys_sections']);
  return $id;
}

public static function Drop($id)
{
  // Warning: if required use MoveItems() before Drop()
  if ( $id<1 ) die('cSection->Drop: Cannot delete domain 0');

  global $oDB, $error;
  $oDB->QueryErr('DELETE FROM '.TABSECTION.' WHERE id='.$id, $error);
  cLang::Delete(array('sec','secdesc'),'s'.$id);
  unset($_SESSION[QT]['sys_sections']);
  $_SESSION['L'] = array();
}

public static function MoveItems($id,$destination)
{
  if ( $id<0 || $destination<0 ) die('cSection->MoveItems: source and destination cannot be <0');
  if ( $id==$destination ) die('cSection->MoveItems: source and destination are the same');

  global $oDB;
  $oDB->Exec('UPDATE '.TABS2U." SET sid=$destination WHERE sid=$id");
}

public static function CountItems($id,$status='')
{
  // Count Users in Section $id. Use -1 to count users through all sections
  if ( !is_int($id) ) die('cSection::CountItems: argument #1 must be integer');
  global $oDB;
  if ( $id<0 )
  {
    $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.(empty($status) ? '' : ' WHERE status="'.$status.'"'));
  }
  else
  {
    if ( isset($status) )
      $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' u INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.sid='.$id.' AND u.status="'.$status.'"');
    else
      $oDB->Query('SELECT count(*) as countid FROM '.TABS2U.' WHERE sid='.$id);
  }
  $row = $oDB->Getrow();
  return (int)$row['countid'];
}

// --------
// IOptions, IStats implementations
// --------

public function ChangeOption($strKey,$strValue)
{
  QTargs('cSection->ChangeOption',array($strKey,$strValue));
  if ( $strKey==='' ) die ('cSection->ChangeOption: Missing key'); // $strKey can be ''

  $arr = QTarradd(QTexplode($this->options),$strKey,$strValue);
  $this->options = QTimplode($arr);
  $this->WriteOptions();
  return $arr;
}

public function ReadOptions()
{
  $arr = QTexplode($this->options);
  if ( isset($arr['order']) ) $this->d_order = $arr['order'];
  if ( isset($arr['logo']) ) $this->d_logo = $arr['logo'];
  return $arr;
}

public function WriteOptions()
{
  global $oDB;
  $oDB->Exec('UPDATE '.TABSECTION.' SET options="'.$this->options.'" WHERE id='.$this->id);
}

public function ChangeStat($strKey,$strValue)
{
  QTargs('cSection->ChangeStat',array($strKey,$strValue));
  if ( $strKey==='' ) die ('cSection->ChangeStat: Missing key'); // $strKey can be ''

  $arr = QTarradd(QTexplode($this->stats),$strKey,$strValue);
  $this->stats = QTimplode($arr);
  $this->WriteOptions();
  return $arr;
}

public function ReadStats()
{
  $arr = ReadValues($this->stats);
  if ( isset($arr['members']) ) $this->members = intval($arr['members']);
  if ( isset($arr['membersZ']) ) $this->membersZ = intval($arr['membersZ']);
  return $arr;
}

public function WriteStats()
{
  global $oDB;
  $oDB->Exec('UPDATE '.TABSECTION.' SET stats="'.$this->stats.'" WHERE id='.$this->id);
}

// --------
// Renderer
// --------

static function RenderFields($arrFLD,$oItem,$qte_root='',$bAllowLinkToUser=true,$bMap=false)
{
	if ( !is_array($arrFLD) || !is_a($oItem,'cItem') ) die('Invalid argument for cSection::RenderFields');
  if ( empty($oItem->id) ) die('Invalid argument for cSection::RenderFields, missing user id');

  // render

	$arrRender=array();
	foreach ($arrFLD as $key=>$aField)
	{
		$str='&nbsp;'; // default if unknown field
    switch($key)
    {
    	case 'status_i':
        if ( !empty($oItem->status) && !empty($_SESSION[QT]['sys_statuses'][$oItem->status]) )
        {
        $status = $_SESSION[QT]['sys_statuses'][$oItem->status];
        $str = AsImg($qte_root.$_SESSION[QT]['skin_dir'].'/'.$status['icon'],$oItem->status,$status['statusname'],'ico i-status','',($bAllowLinkToUser ? $qte_root.'qte_user.php?id='.$oItem->id : ''));
        }
        break;
    	case 'status':
        if ( !empty($oItem->status) ) $str = $oItem->status;
        if ( !empty($_SESSION[QT]['sys_statuses'][$oItem->status]['statusname']) ) $str = $_SESSION[QT]['sys_statuses'][$oItem->status]['statusname'];
        break;
      case 'role':
        $str = 'V';
        if ( !empty($oItem->role) ) $str = L('Userrole_'.strtoupper($oItem->role));
        break;
      case 'username':
      case 'firstname':
      case 'lastname':
      case 'fullname':
        if ( $bAllowLinkToUser ) { $str = '<a href="'.$qte_root.Href('qte_user.php').'?id='.$oItem->id.'" title="'.$oItem->fullname.'">'.$oItem->$key.'</a>'; } else { $str = $oItem->$key; }
        break;
      case 'children':
        if ( empty($oItem->coppa) ) $oItem->coppa='0';
        $str = AsFormat(($oItem->coppa=='0' ? L('N') : L('Y')),$aField->format);
        break;
      case 'picture':
        if ( empty($oItem->coppa) ) $oItem->coppa='0';
        $str = ( empty($oItem->picture) ? '&nbsp;' : AsImgPopup('usr_'.$oItem->id,$qte_root.QTE_DIR_PIC.$oItem->picture,'&nbsp;'));
        break;
      case 'emails_i':
        if ( !empty($oItem->emails) )
        {
          $arr = AsList($oItem->emails,false,',',3); // 4th entry is '...'
          if ( !empty($arr[0]) ) $str = AsEmailImage(trim($arr[0]),'mail-i-'.$oItem->id,'a',true,QTE_JAVA_MAIL,array('class'=>'small'));
          if ( !empty($arr[1]) ) $str .= ' '.AsEmailImage(trim($arr[1]),'mail-i-'.$oItem->id,'b',true,QTE_JAVA_MAIL,array('class'=>'small'));
          if ( !empty($arr[2]) ) $str .= ' '.AsEmailImage(trim($arr[2]),'mail-i-'.$oItem->id,'c',true,QTE_JAVA_MAIL,array('class'=>'small'));
          if ( !empty($arr[3]) ) $str .= ' '.$arr[3];
        }
        break;
      case 'emails':
      	if ( !empty($oItem->emails) )
        {
      	  $arr = AsList($oItem->emails,false,',',2); // 3d entry is '...'
          if ( !empty($arr[0]) ) $str = AsEmailText(trim($arr[0]),'mail-t-'.$oItem->id,'a',true,QTE_JAVA_MAIL,array('class'=>'small'));
          if ( !empty($arr[1]) ) $str .= '<br/>'.AsEmailText(trim($arr[1]),'mail-t-'.$oItem->id,'b',true,QTE_JAVA_MAIL,array('class'=>'small'));
          if ( !empty($arr[2]) ) $str .= ' '.$arr[2];
        }
        break;
      case 'birthdate':
      case 'teamdate1':
      case 'teamdate2':
      case 'registration': if ( !empty($oItem->$key) ) { if ( QTisvaliddate($oItem->$key,true,false,false) ) $str = QTdatestr($oItem->$key,'$','',false); } break;
      case 'address':
      case 'phones': if ( !empty($oItem->$key) ) { $str = ($_SESSION[QT]['viewmode']=='C' ? implode(' ',AsList($oItem->$key,true)) : implode('<br />',AsList($oItem->$key,false))); } break;
      case 'coord': $str = $strLatLon; break;
      case 'ufield': $str = (empty($oItem->ufield) ? '' : ObjTrans('field',$oItem->ufield)); break;
      default: $str = AsFormat(AsList((string)$oItem->$key),$aField->format);
    }
    $arrRender[$key]=$str.PHP_EOL;
	}
  return $arrRender;
}

static function RenderFieldsCSV($arrFLD,$oItem,$qte_root='',$bMap=false)
{
	if ( !is_array($arrFLD) || !is_a($oItem,'cItem') ) die('Invalid argument for cSection::RenderFields');
	if ( empty($oItem->id) ) die('Invalid argument for cSection::RenderFields, missing user id');

	// render

	$arrRender=array();
	foreach ($arrFLD as $key=>$aField)
	{
		$str=''; // default if unknown field
		switch($key)
		{
			case 'status_i':
			case 'status':
				if ( !empty($oItem->status) ) $str = $oItem->status;
				if ( !empty($_SESSION[QT]['sys_statuses'][$oItem->status]['statusname']) ) $str = $_SESSION[QT]['sys_statuses'][$oItem->status]['statusname'];
				break;
			case 'role':
				$str = 'V';
				if ( !empty($oItem->role) ) $str = L('Userrole_'.strtoupper($oItem->role));
				break;
			case 'username':
			case 'firstname':
			case 'lastname':
			case 'fullname':
				$str = $oItem->$key;
				break;
			case 'children':
				if ( empty($oItem->coppa) ) $oItem->coppa='0';
				$str = AsFormat(($oItem->coppa=='0' ? L('N') : L('Y')),$aField->format);
				break;
			case 'picture':
				if ( empty($oItem->coppa) ) $oItem->coppa='0';
				$str = ( empty($oItem->picture) ? L('N') : L('Y') );
				break;
			case 'emails_i':
			case 'emails':
				if ( !empty($oItem->emails) )
				{
					$arr = AsList($oItem->emails,false,',',2); // 3d entry is '...'
					$str = implode(' ',$arr);
				}
				break;
			case 'birthdate':
			case 'teamdate1':
			case 'teamdate2':
			case 'registration': if ( !empty($oItem->$key) ) { if ( QTisvaliddate($oItem->$key,true,false,false) ) $str = QTdatestr($oItem->$key,'$','',false); } break;
			case 'address':
			case 'phones': if ( !empty($oItem->$key) ) $str = implode(' ',AsList($oItem->$key,true)); break;
			case 'coord': $str = $strLatLon; break;
			case 'age':
			case 'id': $str = (int)$oItem->$key; break;
			default: $str = AsFormat(AsList((string)$oItem->$key),$aField->format);
		}
		$arrRender[$key]= ToCsv($str);
	}
	return $arrRender;
}

// --------

}
