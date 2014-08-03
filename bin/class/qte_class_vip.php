<?php // QuickTeam 3.0 build:20140608

/**
 * VIP means Visitor In Page: This class includes info on the curent user and the curent page,
 * The class also provides major lists or global stats used in most of the pages
 * Here we extend the basic user's construction methods (cAuthenticate) to add support of Web/quickticket/quicktalk logins
 * Here we extend the basic Login method (cAuthenticate::Login) to return extra user's info
 */

class cVIP extends cSYS
{
public $coockieconfirm = false; // Will be set to TRUE when login is performed via coockie.
public $fullname ='';
public $picture = '';

public $domains = array();  // list of domains (translated) visible for the curent user (sUser::Role())
public $sections = array(); // list of sectionstitles (translated) visible for the curent user (sUser::Role())
public $types = array();    // list of types
public $statuses = array(); // list of statuses
public $states = array();   // other info
public $css = array();
public $output = 'screen'; // output media (screen,print)
public $members = 0;      // count members

// --------

function __construct()
{

  parent::__construct();

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

function SetSys()
{
  if ( !isset($_SESSION[QT]['sys_domains']) ) $_SESSION[QT]['sys_domains'] = GetDomains(sUser::Role());
  if ( !isset($_SESSION[QT]['sys_sections']) ) $_SESSION[QT]['sys_sections'] = QTarrget(GetSections(sUser::Role()));
  if ( !isset($_SESSION[QT]['sys_statuses']) ) $_SESSION[QT]['sys_statuses'] = cVIP::GetStatuses();
  if ( !isset($_SESSION[QT]['sys_members']) ) $_SESSION[QT]['sys_members'] = cVIP::SysCount('members');
  if ( !isset($_SESSION[QT]['sys_states']) ) $_SESSION[QT]['sys_states'] = cVIP::SysCount('states');

  $this->domains = $_SESSION[QT]['sys_domains'];
  $this->sections = $_SESSION[QT]['sys_sections'];
  $this->statuses = $_SESSION[QT]['sys_statuses'];
  $this->members = $_SESSION[QT]['sys_members'];
  $this->states = $_SESSION[QT]['sys_states'];
}

// --------

public static function GetStatusName($str='A')
{
	if ( !isset($_SESSION[QT]['sys_statuses']) ) $_SESSION[QT]['sys_statuses'] = cVIP::GetStatuses();
	if ( empty($_SESSION[QT]['sys_statuses'][$str]['statusname']) ) return 'unkown status';
	return $_SESSION[QT]['sys_statuses'][$str]['statusname'];
}
public static function GetStatusIconFile($str='A')
{
	if ( !isset($_SESSION[QT]['sys_statuses']) ) $_SESSION[QT]['sys_statuses'] = cVIP::GetStatuses();
	if ( empty($_SESSION[QT]['sys_statuses'][$str]['icon']) ) return 'status_0.gif';
	return $_SESSION[QT]['sys_statuses'][$str]['icon'];
}
public static function GetStatusIcon($str='A',$class='ico i-status')
{
	if ( !is_string($str) || !is_string($class) ) die('Wrong argument in cVIP::GetStatusIcon');
	if ( !isset($_SESSION[QT]['sys_statuses']) ) $_SESSION[QT]['sys_statuses'] = cVIP::GetStatuses();
	return '<img '.(empty($class) ? '' : 'class="'.$class.'"').' src="'.$_SESSION[QT]['skin_dir'].'/'.cVIP::GetStatusIconFile($str).'"/>';
}
// --------

public static function GetStatuses()
{
  $ar = array();

  global $oDB;  $oDB->Query('SELECT * FROM '.TABSTATUS.' ORDER BY id' );
  while($row=$oDB->Getrow())
  {
    $ar[$row['id']]['statusname'] = ucfirst(str_replace('_',' ',$row['name']));
    $ar[$row['id']]['statusdesc'] = '';
    $ar[$row['id']]['name'] = $row['name'];
    $ar[$row['id']]['icon'] = $row['icon'];
    $ar[$row['id']]['color'] = $row['color'];
  }

  // find translations

  $arL = cLang::Get('status',QTiso(),'*');
  foreach ($arL as $id=>$str)
  {
    if ( !empty($str) ) $ar[$id]['statusname'] = $str;
  }
  $arL = cLang::Get('statusdesc',QTiso(),'*');
  foreach ($arL as $id=>$str)
  {
    if ( !empty($str) ) $ar[$id]['statusdesc'] = $str;
  }

  return $ar;
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

  if ( isset($_SESSION[QT]['sys_statuses']) ) unset($_SESSION[QT]['sys_statuses']);
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

  if ( isset($_SESSION[QT]['sys_statuses']) ) unset($_SESSION[QT]['sys_statuses']);
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

  if ( isset($_SESSION[QT]['sys_statuses']) ) unset($_SESSION[QT]['sys_statuses']);
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
    $ar = array();
      $oDB->Query('SELECT max(id) as countid FROM '.TABUSER); // where close is not used
      $row = $oDB->Getrow();
    $ar['newuserid'] = intval($row['countid']);
      $oDB->Query('SELECT username,firstname,lastname FROM '.TABUSER.' WHERE id='.$row['countid'] );
      $row = $oDB->Getrow();
    $ar['newusername'] =  $row['firstname'].' '.$row['lastname']; if ( strlen($ar['newusername'])<3 ) $ar['newusername'] = $row['username'];
    return $ar;
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

}