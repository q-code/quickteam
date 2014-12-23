<?php

/**
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @package    QuickTeam
 * @author     Philippe Vandenberghe <info@qt-cute.org>
 * @copyright  2014 The PHP Group
 * @version    3.0 build:20141222
 */

session_start();
require 'bin/qte_init.php';
include 'bin/class/qt_class_smtp.php';

// ---------
// INITIALISE
// ---------

$a = ''; // mandatory action
$d = -1; // domain (or destination)
$s = -1; // section
$u = -1; // id (user id)
$v = ''; // value
$ids = ''; // list of comma separated id (converted to array)
$ok = ''; // submitted
QThttpvar('a d s u v ids ok','str int int int str str str');
if ( !empty($ids) ) $ids = explode(',',$ids);

include Translate(APP.'_adm.php');

$bCmdok = false;
$strMails = '';

$oVIP->selfurl  = 'qte_change.php';
$oVIP->selfname = 'QuickTeam command';

// --------
// EXECUTE COMMAND
// --------

switch($a)
{
// --------------
case 'deletesection':
// --------------

  if ( sUser::Role()!='A' ) die($L['R_admin']);
  if ( $s<1 ) die('Wrong id in '.$oVIP->selfurl);

  $oVIP->selfname = $L['Section'];
  $oVIP->exiturl  = 'qte_adm_sections.php';
  $oVIP->exitname = '&laquo; '.$L['Sections'];

  $oSEC = new cSection($s);

  // ask confirmation
  if ( empty($ok) )
  {
    // list content
    if ( $oSEC->members>0 )
    {
      // destination "d" is the list all sections (GetSections) except $s himself
      $strList = '<tr class="tr">
      <td class="headfirst">'.$L['Users'].'</td>
      <td>
      <select name="d" size="1" class="small">'.QTasTag(GetSections('A',-1,$s),'',array('format'=>$L['Move_to'].': %s')).'</select>
      </td>
      </tr>';
    }
    else
    {
      $strList = '';
    }

    $oHtml->PageMsgAdm
    (
    NULL,
    '<form method="get" action="'.Href().'">
    <table class="t-data">
    <tr class="tr">
    <td class="headfirst" style="width:150px">'.$L['Section'].'</td>
    <td>'.$oSEC->name.'</td>
    </tr>
    <tr class="tr">
    <td class="headfirst">'.$L['Containing'].'</td>
    <td>'.L('User',$oSEC->members).'</td>
    </tr>
    '.$strList.'
    <tr class="tr">
    <td class="headfirst">&nbsp;</td>
    <td>
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="s" value="'.$s.'"/>
    <input type="submit" name="ok" value="'.$L['Delete'].'"/>
    </td>
    </tr>
    </table>
    </form>',
    0,
    '600px'
    );
    exit;
  }

  // move members, delete translations, delete section and unset
  if ( $d<0 ) $d=0;
  cSection::MoveItems($s,$d); // Set $error in case of db failure
  if ( empty($error) ) cSection::Drop($s); // Set $error in case of db failure
  cSection::UpdateStats($d);

  // EXIT
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_delete'] : 'E|'.$error);
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'deletedomain':
// --------------

  if ( sUser::Role()!='A' ) die($L['R_admin']);
  if ( $s<1 ) die('Wrong id in '.$oVIP->selfurl); // note $s is the domainid and $d is the destination domainid

  $oVIP->selfname = $L['Domain'];
  $oVIP->exiturl  = 'qte_adm_sections.php';
  $oVIP->exitname = '&laquo; '.$L['Sections'];

  // ask destination
  if ( empty($ok) )
  {
    $arrDomains = memGet('sys_domains');
    $strTitle = (isset($arrDomains[$s]) ? $arrDomains[$s] : 'untitled');
    $arrTeams = GetSectionTitles(sUser::Role(),$s);

    // list the domain content
    if ( count($arrTeams)==0 )
    {
      $strDcont = '<span class="small">0 '.$L['Section'].'</span>';
    }
    else
    {
      $strDcont = '';
      foreach ($arrTeams as $intKey => $strValue)
      {
      $strDcont .= '<span class="small">'.$L['Section'].': '.$strValue.'</span><br />';
      }
    }

    // list of domain destination
    if ( count($arrTeams)>0 )
    {
      unset($arrDomains[$s]);
      $strDest = '<tr class="tr">
      <td class="headfirst">'.$L['Sections'].'</td>
      <td>
      <select name="d" size="1" class="small">'.QTasTag(QTtruncarray($arrDomains,25),'',array('format'=>$L['Move_to'].': %s')).'</select>
      </td>
      </tr>';
    }
    else
    {
      $strDest = '';
    }

    // form
    $oHtml->PageMsgAdm
    (
    NULL,
    '<form method="get" action="'.Href().'">
    <table class="t-data">
    <tr class="tr">
    <td class="headfirst" style="width:150px">'.$L['Title'].'</td>
    <td><b>'.$strTitle.'</b></td>
    </tr>
    <tr class="tr">
    <td class="headfirst">'.$L['Containing'].'</td>
    <td>'.$strDcont.'</td>
    </tr>'.PHP_EOL.$strDest.'
    <tr class="tr">
    <td class="headfirst">&nbsp;</td>
    <td>
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="s" value="'.$s.'"/>
    <input type="submit" name="ok" value="'.$L['Delete'].'"/></td>
    </tr>
    </table>
    </form>',
    0,
    '600px'
    );
    exit;
  }

  // delete domain $s and move content to destination $d

  require 'bin/class/qte_class_dom.php';
  if ( $d>-1 ) cDomain::MoveItems($s,$d); // Set $error in case of db failure
  if ( empty($error) ) cDomain::Drop($s); // Set $error in case of db failure

  // EXIT
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_delete'] : 'E|'.$error);
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'pwdreset':
// --------------

  if ( !sUser::IsStaff() ) die($L['R_staff']);
  if ( $u<0 ) die('Wrong id in '.$oVIP->selfurl);

  include Translate(APP.'_reg.php');

  $oVIP->selfname = $L['Reset_pwd'];
  $oVIP->exiturl  = Href('qte_user.php').'?id='.$u;
  $oVIP->exitname = '&laquo; '.$L['Profile'];

  $oItem = new cItem($u);

  // ask delay
  if ( empty($ok) )
  {
    $oHtml->PageMsgAdm
    (
    NULL,
    '<form method="get" action="'.Href().'">
    <table class="hidden">
    <tr>
    <td class="boxinfo">
    <p class="picture username">'.UserFirstLastName($oItem,'<br/>').'</p>'.UserPicture($oItem).'<p class="picture userstatus">'.$oItem->GetStatusIcon().' '.$oItem->GetStatusName().'</p>
    </td>
    <td class="boxform">
    <p style="text-align:right">'.$L['Reset_pwd_help'].'<br /><br />
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="u" value="'.$u.'"/>
    <input type="submit" name="ok" value="'.$L['Send'].'"/>&nbsp;&nbsp;
    <input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/></p>
    </td>
    </tr>
    </table>
    </form>',
    0,
    '500px'
    );
    exit;
  }

  // reset user
  $strNewpwd = 'qt'.rand(0,9).rand(0,9).rand(0,9).rand(0,9);
  $oDB->Exec('UPDATE '.TABUSER.' SET pwd="'.sha1($strNewpwd).'" WHERE id='.$u);

  // send email
  $strSubject = $_SESSION[QT]['site_name'].' - New password';
  $strFile = GetLang().'mail_pwd.php';
  if ( file_exists($strFile) ) include $strFile;
  if ( empty($strMessage) ) $strMessage = "Here are your login and password\nLogin: %s\nPassword: %s";
  $strMessage = sprintf($strMessage,$oItem->username,$strNewpwd);
  $strMessage = wordwrap($strMessage,70,"\r\n");
  QTmail($oItem->emails,$strSubject,$strMessage);
  $strEndmessage = str_replace("\n",'<br />',$strMessage);

  // parent warning if coppa (and if edited by the kid himself)
  if ( $oItem->coppa != '0' ) {
  if ( $_SESSION[QT]['register_coppa']=='1' ) {
    $oDB->Query('SELECT parentmail FROM '.TABCHILD.' WHERE id='.$u);
    $row = $oDB->Getrow();
    $strSubject = $_SESSION[QT]['site_name'].' - Profile updated';
    $strFile = GetLang().'mail_pwd_coppa.php';
    if ( file_exists($strFile) ) include $strFile;
    if ( empty($strMessage) )$strMessage = "Your children (login: %s) has modified his/her profile on the board {$_SESSION[QT]['site_name']}.";
    $strMessage = sprintf($strMessage,$oItem->username,$strNewpwd);
    $strMessage = wordwrap($strMessage,70,"\r\n");
    QTmail($row['parentmail'],$strSubject,$strMessage);
  }}

  // exit
  if ( $_SESSION[QT]['register_mode']!='direct' ) $strEndmessage='';
  $oHtml->PageMsg(NULL,$L['S_update'].'<br /><br />'.$strEndmessage);
  exit;
  break;

// --------------
case 'renamedoc':
// --------------

  if ( $u<0 ) die('Wrong id in '.$oVIP->selfurl);
  if ( !sUser::IsStaff() )
  {
    if ( $u!=sUser::Id() ) die('Wrong id in '.$oVIP->selfurl);
  }

  $oVIP->selfname = $L['Rename'];
  $oVIP->exiturl  = Href('qte_user.php').'?id='.$u.'&amp;tt=d';
  $oVIP->exitname = '&laquo; '.$L['Profile'];

  // ask confirmation
  if ( empty($ok) )
  {
    $oDB->Query('SELECT * FROM '.TABDOC.' WHERE docfile="'.$v.'"');
    $row=$oDB->Getrow();

    $oHtml->PageMsg
    (
    NULL,
    '<table class="hidden">
    <tr>
    <td></td>
    <td>
    <form method="get" action="'.Href().'">
    <p><span class="bold">'.$row['docname'].'</span> ('.QTdatestr($row['docdate']).')<p>
    <input type="text" name="name" id="name" size="40" maxlength="255" value="'.$row['docname'].'"/>
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="u" value="'.$u.'"/>
    <input type="hidden" name="v" value="'.$v.'"/>
    <input type="submit" name="ok" value="'.$L['Ok'].'"/>
    <input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </form>
    </td>
    </tr></table>',
    0,
    '500px'
    );
    exit;
  }

  // CHANGE

  $str = QTconv(trim(strip_tags($_GET['name'])),'3',QTE_CONVERT_AMP); if ( empty($str) ) $str='untitled';
  $oDB->Exec('UPDATE '.TABDOC.' SET docname="'.$str.'" WHERE docfile="'.$v.'"');

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'docs_del':
// --------------

  if ( $u<0 ) die('Wrong id in '.$oVIP->selfurl);
  if ( !sUser::IsStaff() ) if ( $u!=sUser::Id() ) die('Wrong id in '.$oVIP->selfurl);

  $oVIP->selfname = $L['Delete'];
  $oVIP->exiturl  = Href('qte_user.php').'?id='.$u.'&amp;tt=d';
  $oVIP->exitname = '&laquo; '.$L['Profile'];

  // ask confirmation
  if ( empty($ok) )
  {
    if ( empty($v) && !empty($_POST['t1_cb']) ) $v=$_POST['t1_cb']; // t1_cb can be an array of several ids
    if ( empty($v) ) die('Nothing selected');
    $ids = (is_array($v) ? $v : explode(',',$v));
    $arrDocs = array();
    $oDB->Query('SELECT * FROM '.TABDOC.' WHERE docfile IN ("'.implode('","',$ids).'")' );
    while ( $row=$oDB->Getrow() )
    {
    $arrDocs[] = $row['docname'].' <span class="small">('.$row['docfile'].')</span>';
    if ( count($arrDocs)==4 ) { $arrNames[4]='...';  break; }
    }

    $oHtml->PageMsgAdm
    (
    NULL,
    '<form method="post" action="'.Href().'">
    <table class="t-data">
    <tr>
    <td class="headfirst" style="width:150px;">'.L('Documents').'</td>
    <td>'.implode('<br/>',$arrDocs).'</td>
    </tr>
    <tr>
    <td class="headfirst">&nbsp;</td>
    <td>
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="u" value="'.$u.'"/>
    <input type="hidden" name="ids" value="'.implode(',',$ids).'"/>
    <input type="submit" name="ok" value="'.$L['Delete'].' ('.count($ids).')"/>
    <input type="button" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </td>
    </tr>
    </table>
    </form><br />',
    0,
    '600px'
    );
    exit;
  }

  // DELETE
  foreach($ids as $v)
  {
    $oDB->Query('SELECT * FROM '.TABDOC.' WHERE id='.$u.' AND docfile="'.$v.'"');
    while ($row=$oDB->Getrow())
    {
      $oDB->Exec('DELETE FROM '.TABDOC.' WHERE id='.$u.' AND docfile="'.$v.'"' );
      if (file_exists(QTE_DIR_DOC.$row['docpath'].$row['docfile'])) unlink(QTE_DIR_DOC.$row['docpath'].$row['docfile']);
    }
  }

  if ( empty($ids) ) die('Nothing selected');

  // End message
  $_SESSION['pagedialog'] = 'O|'.$L['S_delete'].'|'.count($ids);
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'userstatus':
// --------------

  if ( !sUser::IsStaff() ) die($L['R_staff']);

  $oVIP->selfname = $L['Change_status'];
  $oVIP->exiturl  = Href('qte_user.php').'?id='.$u;
  $oVIP->exitname = '&laquo; '.$L['Profile'];

  // ask confirmation
  if ( empty($ok) )
  {
    $oItem = new cItem($u);
    $arr = memGet('sys_statuses');
    $oHtml->PageMsgAdm
    (
    NULL,
    '<table class="hidden">
    <td class="boxinfo">
    <p class="picture username">'.UserFirstLastName($oItem,'<br/>').'</p>'.UserPicture($oItem).'<p class="picture userstatus">'.$oItem->GetStatusIcon().' '.$oItem->GetStatusName().'</p>
    </td>
    <td class="boxform">
    <form method="get" action="'.Href().'">
    <h2>'.$oItem->fullname.'<br />'.AsImg($_SESSION[QT]['skin_dir'].'/'.$arr[$oItem->status]['icon'],$oItem->status,$arr[$oItem->status]['statusname'],'ico i-status').' ('.$arr[$oItem->status]['statusname'].')</h2>
    <p>'.$L['Change_status'].' <select name="v" size="1">'.QTasTag($arr,$oItem->status).'</select></p>;
    <p><input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="u" value="'.$u.'"/>
    <input type="submit" name="ok" value="'.$L['Ok'].'"/>&nbsp;
    <input type="button" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/></p>
    </form>
    </td>
    </tr>
    </table>',
    0,
    '500px'
    );
    exit;
  }

  // CHANGE STATUS

  $oDB->Exec('UPDATE '.TABUSER.' SET status="'.$v.'" WHERE id='.$u);

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'userrole':
// --------------

  if ( !sUser::IsStaff() ) die($L['R_staff']);
  if ( $u<2 ) die('Wrong parameters: user 0 and 1 cannot be changed');
  include Translate(APP.'_reg.php');

  $oVIP->selfname = $L['Change_role'];
  $oVIP->exiturl  = Href('qte_user.php').'?id='.$u.'&amp;tt=s';
  $oVIP->exitname = '&laquo; '.$L['Profile'];

  // ask confirmation
  if ( empty($ok) )
  {
    $oItem = new cItem($u);
    $oHtml->PageMsgAdm
    (
    NULL,
    '<table class="hidden">
    <tr>
    <td class="boxinfo">
    '.UserPicture($oItem).'<p class="picture userstatus">'.$oItem->GetStatusIcon().' '.$oItem->GetStatusName().'</p>
    </td>
    <td class="boxform">
    <form method="get" action="'.Href().'">
    <h2 class="right">'.UserFirstLastName($oItem).'</h2>
    <p class="right">'.L('Role').' <select name="v" size="1">
    <option value="A"'.($oItem->role=='A' ? QSEL : '').(sUser::Role()!='A' ? ' disabled="disabled"' : '').'>'.L('Userrole_A').'</option>
    <option value="M"'.($oItem->role=='M' ? QSEL : '').'>'.L('Userrole_M').'</option>
    <option value="U"'.($oItem->role=='U' ? QSEL : '').'>'.L('Userrole_U').'</option>
    </select></p>
    <p class="right">
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="u" value="'.$u.'"/>
    <input type="submit" name="ok" value="'.$L['Ok'].'"/>&nbsp;
    <input type="button" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </form>
    </td>
    </tr>
    </table>',
    0,
    '500px'
    );
    exit;
  }

  //update role
  if ( sUser::Role()!='A' && $v=='A' ) die($L['R_admin']);
  $oDB->Exec('UPDATE '.TABUSER.' SET role="'.$v.'" WHERE id='.$u);
  if ( $v=='U' ) $oDB->Exec('UPDATE '.TABSECTION.' SET modid=1, modname="Administrator" WHERE modid='.$u);

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'deleteuser':
// --------------

  if ( !sUser::IsStaff() ) die($L['R_staff']);
  if ( $u<2 ) die('Wrong parameters: user 0 and 1 cannot be deleted');

  include Translate(APP.'_reg.php');

  $oVIP->selfname = $L['User_del'];
  if ( $v=='qte_adm_users' ) $oVIP->exiturl='qte_adm_users.php';

  $oItem = new cItem($u);

  // ask confirmation
  if ( empty($ok) )
  {
    $oHtml->PageMsgAdm
    (
    NULL,
    '<table class="hidden">
    <tr class="hidden">
    <td class="boxinfo">
    <p class="picture username">'.UserFirstLastName($oItem,'<br/>').'</p>'.UserPicture($oItem).'<p class="picture userstatus">'.$oItem->GetStatusIcon().' '.$oItem->GetStatusName().'</p>
    </td>
    <td class="boxform">
    <form method="get" action="'.Href().'">
    <h2 class="right">'.$oItem->username.'</h2>'.($oItem->username!=$oItem->fullname ? '<p class="bold"> ('.$oItem->fullname.')</p><br />' : '').'
    <p class="right">'.$L['User_del'].'</p>
    <p class="right">
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="v" value="'.$v.'"/>
    <input type="hidden" name="u" value="'.$u.'"/>
    <input type="submit" name="ok" value="'.$L['Delete'].'"/>
    <input type="button" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </p>
    </form>
    </td>
    </tr>
    </table>',
    0,
    '500px'
    );
    exit;
  }

  // delete user
  $oItem->Delete();

  // Unregister global sys (will be recomputed on next page)
  memUnset('sys_members');
  Unset($_SESSION[QT]['sys_states']);

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_delete'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'status_del':
// --------------

  if ( $v=='Z' ) die('Wrong id in '.$oVIP->selfurl);

  $oVIP->selfname = $L['Status'];
  $oVIP->exiturl  = 'qte_adm_statuses.php';
  $oVIP->exitname = '&laquo; '.$L['Statuses'];

  // ask confirmation
  if ( empty($ok) || !isset($_GET['to']) )
  {
    // list of status destination
    $strSdest = '';
    $arrStatuses = memGet('sys_statuses');
    foreach( $arrStatuses as $strKey=>$arrStatus )
    {
      if ( $strKey!=$v ) $strSdest .= '<option value="'.$strKey.'"/>'.$strKey.' - '.$arrStatus['statusname'].'</option>';
    }

    $oHtml->PageMsgAdm
    (
    NULL,
    '<form method="get" action="'.Href().'">
    <table  class="t-data">
    <tr>
    <td class="headfirst" style="width:150px;">'.$L['Status'].'</td>
    <td><b>'.$v.'&nbsp;&nbsp;'.AsImg($_SESSION[QT]['skin_dir'].'/'.$arrStatuses[$v]['icon'],'-',$arrStatuses[$v]['statusname'],'ico i-status').'&nbsp;&nbsp;'.$arrStatuses[$v]['statusname'].'</b></td>
    </tr>
    <tr>
    <td class="headfirst">'.$L['Description'].'</td>
    <td>'.$arrStatuses[$v]['statusdesc'].'</td>
    </tr>
    <tr>
    <td class="headfirst">'.$L['Move'].'</td>
    <td>'.$L['H_Status_move'].' <select name="to" size="1" class="small">'.$strSdest.'</select></td>
    </tr>
    <tr>
    <td class="headfirst">&nbsp;</td>
    <td>
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="v" value="'.$v.'"/>
    <input type="submit" name="ok" value="'.$L['Delete'].'"/>
    <input type="button" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </td>
    </tr>
    </table>
    </form><br />',
    0,
    '600px'
    );
    exit;
  }

  // delete status
  cVIP::StatusDelete($v,$_GET['to']);

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_delete'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'users_del':
// --------------

  if ( !sUser::IsStaff() ) die($L['R_staff']);

  $oVIP->selfname = $L['User_del'];
  $oVIP->exiturl  = 'qte_adm_users.php';
  $oVIP->exitname = '&laquo; '.$L['Users'];

  // ask confirmation
  if ( empty($ok) )
  {
  	if ( !isset($_POST['t1_cb']) ) die('Nothing selected');
    $ids = (is_array($_POST['t1_cb']) ? $_POST['t1_cb'] : explode(',',$_POST['t1_cb']));
    if ( in_array('0',$ids) || in_array('1',$ids) ) die('User 0 and 1 cannot be deleted');
    $arrNames = array();
    foreach($ids as $id)
    {
      $oItem = new cItem((int)$id);
      $arrNames[]=UserFirstLastName($oItem,' ',$oItem->username);
    	if ( count($arrNames)==4 ) { $arrNames[4]='...'; 	break; }
    }

    $oHtml->PageMsgAdm
  	(
  	NULL,
  	'<form method="post" action="'.Href().'">
    <table  class="t-data">
    <tr>
    <td class="headfirst" style="width:150px;">'.$L['Users'].'</td>
    <td>'.implode('<br/>',$arrNames).'</td>
    </tr>
    <tr>
    <td class="headfirst">&nbsp;</td>
    <td>
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="ids" value="'.implode(',',$ids).'"/>
  	<input type="submit" name="ok" value="'.$L['Delete'].' ('.count($ids).')"/>
    <input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </td>
    </tr>
    </table>
    </form><br />',
  	0,
  	'600px'
  	);
  	exit;
  }
  else
  {
  	if ( empty($ids) ) die('Nothing selected');
    if ( in_array('0',$ids) || in_array('1',$ids) ) die('User 0 and 1 cannot be deleted');
    // delete
    foreach($ids as $id) { $oItem = new cItem((int)$id); $oItem->Delete(); }
    // End message
    $_SESSION['pagedialog'] = 'O|'.$L['S_delete'].'|'.count($ids);
    $oHtml->Redirect($oVIP->exiturl);
  }
  break;

// --------------
case 'users_role':
// --------------

  $oVIP->selfname = L('Change_role');
  $oVIP->exiturl  = 'qte_adm_users.php';
  $oVIP->exitname = '&laquo; '.$L['Users'];

  // ask confirmation
  if ( empty($ok) )
  {
  	if ( !isset($_POST['t1_cb']) ) die('Nothing selected');
  	$ids = (is_array($_POST['t1_cb']) ? $_POST['t1_cb'] : explode(',',$_POST['t1_cb']));
  	if ( in_array('0',$ids) || in_array('1',$ids) ) die('User 0 and 1 cannot be updated');
  	$arrNames = array();
  	foreach($ids as $id)
  	{
      $oItem = new cItem((int)$id);
  		$arrNames[]=$oItem->fullname;
  		if ( count($arrNames)==4 ) { $arrNames[4]='...'; 	break; }
  	}

    $oHtml->PageMsgAdm
  	(
  	NULL,
  	'<form method="post" action="'.Href().'">
    <table  class="t-data">
    <tr><td class="headfirst" style="width:150px;">'.$L['Users'].'</td><td>'.implode('<br/>',$arrNames).'</td></tr>
  	<tr><td class="headfirst" style="width:150px;">'.$L['Role'].'</td><td><select name="v" size="1"><option value="A">'.$L['Userrole_A'].'</option><option value="M">'.$L['Userrole_M'].'</option><option value="U">'.$L['Userrole_U'].'</option></select></td>
    <tr><td class="headfirst">&nbsp;</td><td>
  	<input type="hidden" name="a" value="'.$a.'"/>
  	<input type="hidden" name="ids" value="'.implode(',',$ids).'"/>
    <input type="submit" name="ok" value="'.$L['Change'].' ('.count($ids).')"/>&nbsp;
    <input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </td>
    </tr>
    </table>
    </form><br />',
  	0,
  	'600px'
  	);
  	exit;
  }
  else
  {
  	if ( empty($ids) ) die('Nothing selected');
    if ( in_array('0',$ids) || in_array('1',$ids) ) die('User 0 and 1 cannot be updated');
  	// status (except admin and visitor)
    $oDB->Exec('UPDATE '.TABUSER.' SET role="'.strtoupper(substr($v,0,1)).'" WHERE id IN ('.implode(',',$ids).')' );
  	// change section coordinator if required
  	if ( $v=='U' ) $oDB->Exec('UPDATE '.TABSECTION.' SET moderator=1,moderatorname="Admin" WHERE moderator IN ('.implode(',',$ids).')');
  	// End message
  	$_SESSION['pagedialog'] = 'O|'.$L['S_update'].'|'.count($ids);
  }
  break;

// --------------
case 'users_status':
// --------------

  $oVIP->selfname = L('Change_status');
  $oVIP->exiturl  = 'qte_adm_users.php';
  $oVIP->exitname = '&laquo; '.$L['Users'];

  // ask confirmation
  if ( empty($ok) )
  {
  	if ( !isset($_POST['t1_cb']) ) die('Nothing selected');
  	$ids = (is_array($_POST['t1_cb']) ? $_POST['t1_cb'] : explode(',',$_POST['t1_cb']));
  	if ( in_array('0',$ids) ) die('User 0 cannot be updated');
  	$arrNames = array();
  	foreach($ids as $id)
  	{
  		$id=(int)$id;
  		$oItem = new cItem($id);
  		$arrNames[]=$oItem->fullname;
  		if ( count($arrNames)==4 ) { $arrNames[4]='...'; 	break; }
  	}

    $arrStatuses = memGet(sys_statuses);
    $strCol2 = '<select id="status" name="v" onchange="bEdited=true; SetStatusIcon(this.value);">'.QTasTag($arrStatuses).'</select> ';
    foreach($arrStatuses as $key=>$arr) $strCol2 .= AsImg($_SESSION[QT]['skin_dir'].'/'.$arr['icon'],'',$arr['statusname'],'ico i-status hiddenicon','display:none','','statusicon_'.$key);

    $oHtml->PageMsgAdm
  	(
  	NULL,
  	'<form method="post" action="'.Href().'">
    <table  class="t-data">
    <tr><td class="headfirst" style="width:150px;">'.$L['Users'].'</td><td>'.implode('<br/>',$arrNames).'</td></tr>
  	<tr><td class="headfirst" style="width:150px;">'.$L['Status'].'</td><td>'.$strCol2.'</td>
  	<tr><td class="headfirst">&nbsp;</td><td>
  	<input type="hidden" name="a" value="'.$a.'"/>
  	<input type="hidden" name="ids" value="'.implode(',',$ids).'"/>
    <input type="submit" name="ok" value="'.$L['Change'].' ('.count($ids).')"/>&nbsp;
    <input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </td>
    </tr>
    </table>
    </form><br/>
  	<script type="text/javascript">
  function SetStatusIcon(id)
  {
  var doc = document;
  var icons = doc.getElementsByClassName("hiddenicon");
  for(var i=0; i<icons.length; ++i) icons[i].style.display="none";
  if ( doc.getElementById("statusicon_"+id) ) doc.getElementById("statusicon_"+id).style.display="inline";
  }
  SetStatusIcon("A"); // make visible the current status (before using select)
  </script>',
  				0,
  				'600px'
  		);
  		exit;
  	}
  	else
  	{
  		if ( empty($ids) ) die('Nothing selected');
  		if ( in_array('0',$ids) ) die('User 0 cannot be updated');
  		// status
  	  foreach($ids as $id) { $id=(int)$id; $oDB->Exec('UPDATE '.TABUSER.' SET status="'.strtoupper(substr($v,0,1)).'" WHERE id IN ('.implode(',',$ids).')'); }

  		// End message
  		$_SESSION['pagedialog'] = 'O|'.$L['S_update'].'|'.count($arr);
  	}
  	break;

// --------------
case 'moveallmembers':
// --------------

  if ( $s<0 ) die('Wrong id in '.$oVIP->selfurl);

  $oVIP->selfname = $L['Members_moveall'];
  $oVIP->exiturl  = 'qte_adm_sections.php';
  $oVIP->exitname = '&laquo; '.$L['Sections'];
    if ( isset($_GET['exit2']) )
    {
    $oVIP->exiturl  = 'qte_adm_users_move.php?s='.$s;
    $oVIP->exitname = '&laquo; '.$L['User_man'];
    }
  $oSEC = new cSection($s);

  // ask confirmation
  if ( empty($ok) )
  {
    $oHtml->PageMsgAdm
    (
    NULL,
    '<form method="get" action="'.Href().'">
    <table  class="t-data">
    <tr>
    <td class="headfirst" style="width:150px;">'.$L['Section'].'</td>
    <td>'.$oSEC->name.'</td>
    </tr>
    <tr>
    <td class="headfirst">'.$L['Containing'].'</td>
    <td>'.L('User',$oSEC->members).'</td>
    </tr>
    <tr>
    <td class="headfirst">'.$L['Move_to'].'</td>
    <td><select name="d" size="1">'.Sectionlist(-1,$s).'</select></td>
    </tr>
    <tr>
    <td class="headfirst">&nbsp;</td>
    <td>
    <input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="s" value="'.$s.'"/>
    <input type="submit" name="ok" value="'.$L['Move'].'"/>&nbsp;<input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
    </td>
    </tr>
    </table>
    </form><br />',
    0,
    '600px'
    );
    exit;
  }

  // move items from section $s into destination $d
  cSection::MoveItems($s,$d);
  cSection::UpdateStats($s);
  cSection::UpdateStats($d);

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'userchild':
// --------------

  if ( !sUser::IsStaff() ) die($L['R_staff']);

  $oVIP->selfname = $L['Coppa_apply'];
  $oVIP->exiturl = Href('qte_user.php').'?id='.$u;
  $oVIP->exitname = '&laquo; '.$L['Profile'];
  $arrStatuses = memGet('sys_statuses');

  // ask confirmation
  if ( empty($ok) )
  {
    $oItem = new cItem($u);

    // System admin cannot be coppa child
    if ( $oItem->role=='A' ) $L['Coppa_child'] = array($L['Coppa_child'][0]) ;

    // form
    $oHtml->PageMsgAdm
    (
    NULL,
    '<table class="hidden">
    <tr class="hidden">
    <td class="hidden">'.AsImgBoxUser($oItem,'username').'</td>
    <td class="hidden">
    <form method="get" action="'.Href().'">
    <h2>'.$oItem->fullname.'</h2>
    <p>Username: '.$oItem->username.'<br/>'.$L['Status'].': '.$arrStatuses[$oItem->status]['statusname'].' '.AsImg($_SESSION[QT]['skin_dir'].'/'.$arrStatuses[$oItem->status]['icon'],$oItem->status,$arrStatuses[$oItem->status]['statusname']).'</p>
    <p style="text-align:right">'.$L['Coppa_status'].' <select name="v" size="1">'.QTasTag($L['Coppa_child'],$v).'</select><input type="hidden" name="a" value="'.$a.'"/><input type="hidden" name="u" value="'.$u.'"/></p>
    <p style="text-align:right">'.($oItem->role=='A' ? '<span class="error">System administrator must be major</span>' : '').'<input type="submit" name="ok" value="'.$L['Ok'].'"/><input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/></p>
    </form>
    </td>
    </tr>
    </table>',
    0,
    '550px'
    );
    exit;

  }

  // CHANGE CHILD STATUS

  $oDB->Exec('UPDATE '.TABUSER.' SET children="'.$v.'" WHERE id='.$u);
  if ( $v!=0 )
  {
    $oDB->Query('SELECT count(*) as countid FROM '.TABCHILD.' WHERE id='.$u);
    $row = $oDB->Getrow();
    if ( $row['countid']==0 ) $oDB->Exec('INSERT INTO '.TABCHILD.' (id,childdate) VALUES ('.$u.',"'.date('Ymd').'")' );
  }
  else
  {
    $oDB->Exec('DELETE FROM '.TABCHILD.' WHERE id='.$u);
  }

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'dropindex':
// --------------

  if ( !sUser::IsStaff() ) die($L['R_staff']);
  if ( $u<0 ) die('Wrong id');

  $oVIP->selfname = $L['Delete'].' index keys';
  $oVIP->exiturl = Href('qte_user.php').'?id='.$u.'&amp;tt=s';
  $oItem = new cItem($u);

  // ask confirmation
  if ( empty($ok) )
  {
    $arrIndex = array();
    $oDB->Query('SELECT ufield,ukey FROM '.TABINDEX.' WHERE userid='.$u);
    while( $row = $oDB->Getrow() )
    {
      $arrIndex[] = strtolower($row['ukey']).' ('.$row['ufield'].')';
    }
    $oHtml->PageMsgAdm
    (
    NULL,
    '<table class="hidden">
    <tr class="hidden">
    <td class="hidden" style="width:160px">'.AsImgBoxUser($oItem,'username').'</td>
    <td class="hidden">
    <form method="get" action="'.Href().'">
    <p style="margin-top:0">'.count($arrIndex).' index keys:</p><p class="small">'.implode('<br />',$arrIndex).'</p>
    <p><input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="u" value="'.$u.'"/>
    <input type="submit" name="ok" value="'.$L['Delete'].'"/>&nbsp;<input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/></p>
    </form>
    </td>
    </tr>
    </table>',
    0,
    '500px'
    );
    exit;
  }

  // drop index
  $oDB->Exec('DELETE FROM '.TABINDEX.' WHERE userid='.$u);

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_delete'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
case 'makeindex':
// --------------

  if ( !sUser::IsStaff() ) die($L['R_staff']);
  if ( $u<0 ) die('Wrong id');

  $oVIP->selfname = 'New index keys';
  $oVIP->exiturl = Href('qte_user.php').'?id='.$u.'&amp;tt=s';
  $oItem = new cItem($u);

  // new keywords
  $arrIndex = array_merge(GetFields('index_p'),GetFields('index_t'));
  $arrIndex = $oItem->GetKeywords($arrIndex);

  // ask confirmation
  if ( empty($ok) )
  {

    $str = '';
    $i = 0;
    foreach ($arrIndex as $strKey => $strValue)
    {
      if ( !empty($strValue) )
      {
      $str .= '('.$strKey.') '.strtolower(implode(', ',$strValue)).'<br />';
      $i = $i + count($strValue);
      }
    }
    $oHtml->PageMsgAdm
    (
    NULL,
    '<table class="hidden">
    <tr class="hidden">
    <td class="hidden" style="width:160px">'.AsImgBoxUser($oItem,'username').'</td>
    <td class="hidden">
    <form method="get" action="'.Href().'">
    <p style="margin-top:0">'.$i.' index keys:</p><p class="small">'.$str.'</p>
    <p><input type="hidden" name="a" value="'.$a.'"/>
    <input type="hidden" name="u" value="'.$u.'"/>
    <input type="submit" name="ok" value="'.$L['Save'].'"/>&nbsp;<input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/></p>
    </form>
    </td>
    </tr>
    </table>',
    0,
    '500px'
    );
    exit;
  }

  // save index
  $oItem->SaveKeywords($arrIndex);

  // EXIT
  $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
  $oHtml->Redirect($oVIP->exiturl);
  break;

// --------------
default:
// --------------

  echo 'Unknown action';
  break;

// --------------

}

// Exit
$_SESSION['pagedialog'] = 'E|'.'Command ['.$a.'] failled...';
$oHtml->Redirect($oVIP->exiturl);