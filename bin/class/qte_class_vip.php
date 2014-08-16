<?php // QuickTeam 3.0 build:20140608

/**
 * VIP means Visitor In Page: This class includes info on the curent user and the curent page,
 * The class also provides major lists or global stats used in most of the pages
 * Here we extend the basic user's construction methods (cAuthenticate) to add support of Web/quickticket/quicktalk logins
 * Here we extend the basic Login method (cAuthenticate::Login) to return extra user's info
 */

class cVIP
{

public $selfurl;
public $selfname;
public $selfuri;
public $exiturl;
public $exitname;
public $exituri;

public $coockieconfirm = false; // Will be set to TRUE when login is performed via coockie.
public $fullname ='';
public $picture = '';

public $sections = array(); // list of sectionstitles (translated) visible for the curent user (sUser::Role())
public $states = array();   // other info
public $css = array();
public $output = 'screen'; // output media (screen,print)
public $members = 0;      // count members

// --------

function __construct()
{

  $this->selfurl = APP.'_index.php';
  $this->selfname = '';
  $this->selfuri = '';  // URL parameters
  $this->exiturl = APP.'_index.php';
  $this->exitname = 'Back';
  $this->exituri = '';

  // Coockie login check if not yet logged in
  if ( !sUser::Auth() && isset($_COOKIE[QT.'_cookname']) && isset($_COOKIE[QT.'_cookpass']) )
  {
    global $oDB;
    $oDB->Query('SELECT * FROM '.TABUSER.' WHERE username="'.$_COOKIE[QT.'_cookname'].'" AND pwd="'.$_COOKIE[QT.'_cookpass'].'"');
    if ( $row = $oDB->Getrow() )
    {
      $_SESSION[QT.'_usr_auth'] = true;
      if ( isset($row['id']) )        $_SESSION[QT.'_usr_id'] = (int)$row['id'];
      if ( isset($row['username']) )  $_SESSION[QT.'_usr_name'] = $row['username'];
      if ( isset($row['firstname']) ) $this->fullname = (empty($row['firstname']) ? '' : $row['firstname']);
      if ( isset($row['lastname']) )  $this->fullname .= (empty($row['lastname']) ? $_SESSION[QT.'_usr_name'] : $row['lastname']);
      if ( isset($row['role']) )      $_SESSION[QT.'_usr_role'] = substr($row['role'],0,1);
      $this->coockieconfirm=true;
    }
  }
  // Web Team Login check
  if ( !sUser::Auth() ) {
  if ( isset($_SESSION[QT]['login_qte_web']) ) {
  if ( !empty($_SESSION[QT]['login_qte_web']) ) {
    $this->LoginTeam('qte');
  }}}

  // QuickTicket login check
  if ( !sUser::Auth() ) {
  if ( isset($_SESSION[QT]['login_qti']) ) {
  if ( !empty($_SESSION[QT]['login_qti']) ) {

    $this->LoginTeam($_SESSION[QT]['login_qti']);
  }}}
  // QuickTalk login check
  if ( !sUser::Auth() ) {
  if ( isset($_SESSION[QT]['login_qtf']) ) {
  if ( !empty($_SESSION[QT]['login_qtf']) ) {

    $this->LoginTeam($_SESSION[QT]['login_qtf']);
  }}}
}

// --------

static function PageCode($str,$prefixsize=4)
{
  // Returns the PageCode: the php-file without prefix and without .php
  // If several points exist in the pagecode, only the first part is returned
  // This is use as code in the html class style
  $arr = explode('.',substr($str,$prefixsize));
  return $arr[0];
}

// --------

function GetTypes()
{
  global $L;
  return array('H'=>array('name'=>'Human','color'=>''),'O'=>array('name'=>'Other','color'=>''));
}

// --------

function IsPrivate($strFields,$strField,$id)
{
  // Check the privacy setting. $strField is in the list of private fields ($strFields)
  // Returns true/false if curent user can see the private info
  if ( sUser::IsStaff() ) return false;
  if ( sUser::Id()==$id ) return false;
  if ( strstr($strFields,$strField) ) return true;
  return false;
}

// --------

// DOLOGINTEAM check weblogin/teamlogin and return extra info (fullname and coppa)
function LoginTeam($sid='qte')
{
  if ( isset($_SESSION[$sid.'_usr_auth']) && ( isset($_SESSION[$sid.'_usr_name']) || isset($_SESSION[$sid.'_usr_username']) ) ) {
  if ( $_SESSION[$sid.'_usr_auth']=='yes' ) {

    // check coherence: name must exist
    global $oDB;
    if ( isset($_SESSION[$sid.'_usr_name']) ) { $str = $_SESSION[$sid.'_usr_name']; } else { $str=$_SESSION[$sid.'_usr_username']; }
    $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE username="'.$str.'"');

    if ( $row = $oDB->Getrow() ) {
    if ( isset($row['countid']) ) {
    if ( $row['countid']==1 ) {
      $_SESSION[$sid.'_usr_auth'] = true;
      // Get user info
      $oDB->Query('SELECT id,username,role,children,status FROM '.TABUSER.' WHERE username="'.$str.'"');
      $row = $oDB->Getrow();
      $_SESSION[$sid.'_usr_id'] = (int)$row['id'];
      $_SESSION[$sid.'_usr_name'] = $row['username'];
      $_SESSION[$sid.'_usr_role'] = $row['role'];
      // Register VIP info in session
      $this->Register();
      return array('fullname'=>$str,'coppa'=>intval($row['children']),'status'=>$row['status']);

    }}}

  }}

  return array('fullname'=>'','coppa'=>0,'status'=>'Z');
}

// --------

public static function CanViewCalendar()
{
  if ( !isset($_SESSION[QT]['show_calendar']) ) return true;
  if ( $_SESSION[QT]['show_calendar']=='V' ) return true;
  if ( $_SESSION[QT]['show_calendar']=='U' && sUser::Role()!='V' ) return true;
  return sUser::IsStaff();
}

// --------

public static function CanViewStats()
{
  if ( !isset($_SESSION[QT]['show_stats']) ) return true;
  if ( $_SESSION[QT]['show_stats']=='V' ) return true;
  if ( $_SESSION[QT]['show_stats']=='U' && sUser::Role()!='V' ) return true;
  return sUser::IsStaff();
}

// --------

public static function GetStatusName($str='A')
{
  $arr = memGet('sys_statuses');
  if ( empty($arr[$str]['statusname']) ) return 'unkown status';
  return $arr[$str]['statusname'];
}
public static function GetStatusIconFile($str='A')
{
  $arr = memGet('sys_statuses');
	if ( empty($arr[$str]['icon']) ) return 'status_0.gif';
	return $arr[$str]['icon'];
}
public static function GetStatusIcon($str='A',$class='ico i-status')
{
	if ( !is_string($str) || !is_string($class) ) die('Wrong argument in cVIP::GetStatusIcon');
	return '<img '.(empty($class) ? '' : 'class="'.$class.'"').' src="'.$_SESSION[QT]['skin_dir'].'/'.cVIP::GetStatusIconFile($str).'"/>';
}
// --------

public static function GetStatuses()
{
  $arr = array();

  global $oDB; $oDB->Query('SELECT * FROM '.TABSTATUS.' ORDER BY id' );
  while($row=$oDB->Getrow())
  {
    $arr[$row['id']]['statusname'] = ucfirst(str_replace('_',' ',$row['name']));
    $arr[$row['id']]['statusdesc'] = '';
    $arr[$row['id']]['name'] = $row['name'];
    $arr[$row['id']]['icon'] = $row['icon'];
    $arr[$row['id']]['color'] = $row['color'];
  }

  // find translations

  $arrL = cLang::Get('status',QTiso(),'*');
  foreach ($arrL as $id=>$str)
  {
    if ( !empty($str) ) $arr[$id]['statusname'] = $str;
  }
  $arrL = cLang::Get('statusdesc',QTiso(),'*');
  foreach ($arrL as $id=>$str)
  {
    if ( !empty($str) ) $arr[$id]['statusdesc'] = $str;
  }

  return $arr;
}

// --------

public static function StatusAdd($id='',$name='',$icon='',$color='')
{
  QTargs('cVIP->StatusAdd',array($id,$name,$icon,$color));
  QTargs('cVIP->StatusAdd',array($id,$name),'empty');

  // Process

  global $oDB;
  $error = '';

  $id = strtoupper(substr(trim($id),0,1));
  $name = QTconv($name,'3',QTE_CONVERT_AMP);

  // unique id and name

  $oDB->Query('SELECT count(*) AS countid FROM '.TABSTATUS.' WHERE id="'.$id.'"');
  $row=$oDB->Getrow();
  if ( $row['countid']>0 ) $error = "Status id [$id] already used";
  $oDB->Query('SELECT count(*) AS countid FROM '.TABSTATUS.' WHERE name="'.addslashes($name).'"');
  $row=$oDB->Getrow();
  if ( $row['countid']>0 ) $error = "Status name [$name] already used";

  // Save

  if ( empty($error) )
  {
    $oDB->QueryErr('INSERT INTO '.TABSTATUS.' (id,name,color,icon) VALUES ("'.$id.'","'.addslashes($name).'","'.$color.'","'.$icon.'")', $error);
  }

  // Exit

  memUnset('sys_statuses');
  return $error;
}

// --------

public static function StatusDelete($id='',$to='A')
{
  QTargs('cVIP->StatusDelete',array($id,$to) );
  QTargs('cVIP->StatusDelete',array($id,$to),'empty');
  $id = strtoupper(substr(trim($id),0,1));
  $to = strtoupper(substr(trim($to),0,1));
  if ( $id=='A' || $id=='A' ) die('cVIP->StatusDelete: Argument #1 cannot be A nor Z');
  if ( $id==$to ) die('cVIP->StatusDelete: Argument #1 equal #2');

  // Process - status id > to and delete id

  global $oDB;

  $oDB->Exec('UPDATE '.TABUSER.' SET status="'.$to.'" WHERE status="'.$id.'"' );
  $oDB->Exec('DELETE FROM '.TABSTATUS.' WHERE id="'.$id.'"' );
  $oDB->Exec('DELETE FROM '.TABLANG.' WHERE (objtype="status" OR objtype="statusdesc") AND objid="'.$id.'"' );

  // Exit

  memUnset('sys_statuses');
}

// --------

public static function StatusChangeId($id='',$to='')
{
  QTargs('cVIP->StatusChangeId',array($id,$to));
  QTargs('cVIP->StatusChangeId',array($id,$to),'empty');
  $id = strtoupper(substr(trim($id),0,1));
  $to = strtoupper(substr(trim($id),0,1));
  if ( $id=='A' || $id=='Z' ) die('cVIP->StatusChangeId: Argument #1 cannot be A nor Z');
  if ( $to=='A' || $to=='Z' ) die('cVIP->StatusChangeId: Argument #2 cannot be A nor Z');

  // Process

  global $oDB;
  $error = '';

  // Unique name

  if ( empty($error) )
  {
  $oDB->Query('SELECT count(*) AS countid FROM '.TABSTATUS.' WHERE status="'.$to.'"');
  $row=$oDB->Getrow();
  if ( $row['countid']>0 ) $error = "Status id [$id] already used";
  }

  // Save changes

  if ( empty($error) )
  {
  $oDB->Exec('UPDATE '.TABUSER.' SET status="'.$to.'" WHERE status="'.$id.'"');
  $oDB->Exec('UPDATE '.TABSTATUS.' SET id="'.$to.'" WHERE id="'.$id.'"');
  }

  // Exit

  memUnset('sys_statuses');
  return $error;
}

// --------

public static function SysCount($strObject='members',$strWhere='')
{
  global $oDB;
  switch($strObject)
  {
  case 'members':
    $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE id>0'.$strWhere);
    $row = $oDB->Getrow();
    return intval($row['countid']);
    break;
  case 'states':
    $arr = array();
      $oDB->Query('SELECT max(id) as countid FROM '.TABUSER); // where close is not used
      $row = $oDB->Getrow();
    $arr['newuserid'] = intval($row['countid']);
      $oDB->Query('SELECT username,firstname,lastname FROM '.TABUSER.' WHERE id='.$row['countid'] );
      $row = $oDB->Getrow();
    $arr['newusername'] =  $row['firstname'].' '.$row['lastname']; if ( strlen($arr['newusername'])<3 ) $arr['newusername'] = $row['username'];
    return $arr;
    break;
  case 'brithdays':
    switch($oDB->type)
    {
    // Select month
    case 'pdo.mysql':
    case 'mysql': $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTRING(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTRING(birthdate,5,4)="'.Date('md').'"'); break;
    case 'pg':    $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTRING(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTRING(birthdate,5,4)="'.Date('md').'"'); break;
    case 'ibase': $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTRING(birthdate FROM 5 FOR 4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTRING(birthdate FROM 5 FOR 4)="'.Date('md').'"'); break;
    case 'sqlite':$oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTR(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTR(birthdate,5,4)="'.Date('md').'"'); break;
    case 'db2':   $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTR(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTR(birthdate,5,4)="'.Date('md').'"'); break;
    case 'oci':   $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTR(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTR(birthdate,5,4)="'.Date('md').'"'); break;
    default:      $oDB->Query('SELECT id,username,firstname,lastname FROM '.TABUSER.' WHERE SUBSTRING(birthdate,5,4)="'.substr(DateAdd(Date('Ymd'),+1,'day'),4,4).'" OR SUBSTRING(birthdate,5,4)="'.Date('md').'"'); break;
    }
    $arr = array();
    while($row=$oDB->Getrow())
    {
      if ( empty($row['lastname']) ) $row['lastname']='('.$row['username'].')';
      $arr[] = '<a class="small" href="'.Href('qte_user.php').'?id='.$row['id'].'">'.(empty($row['firstname']) ? '' : $row['firstname'].' ').$row['lastname'].'</a>';
      if ( isset($arr[4]) ) break; // max 5 users
    }
    return $arr;
    break;
  default:
  	die('SysCount: Invalid argument');
  }
}

// --------

public function Logout()
{
  // Remove session info (and cookie)
  $_SESSION=array();
  session_destroy();
  if ( isset($_COOKIE[QT.'_cookname']) ) setcookie(QT.'_cookname', '', time()+60*60*24*100, '/');
  if ( isset($_COOKIE[QT.'_cookpass']) ) setcookie(QT.'_cookpass', '', time()+60*60*24*100, '/');
  if ( isset($_COOKIE[QT.'_cooklang']) ) setcookie(QT.'_cooklang', '', time()+60*60*24*100, '/');
}

// --------

public static function SysInit($key,$default=false)
{
  switch($key)
  {
  case 'sys_domains': $obj = GetDomains(); break;
  case 'sys_sections': $obj = GetSections('A'); break; // attention this get ALL sections
  case 'sys_statuses': $obj = cVIP::GetStatuses(); break;
  case 'sys_members': $obj = cVIP::SysCount('members'); break;
  case 'sys_states': $obj = cVIP::SysCount('states'); break;
  case 'sys_birthdays': $obj = cVIP::SysCount('birthdays'); break;
  default: $obj = $default;
  }
  return $obj;
}

}