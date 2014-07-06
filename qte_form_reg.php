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
 * @version    3.0 build:20140608
 */

session_start();
require_once 'bin/qte_init.php';
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_profile.css" />';

// ---------
// INITIALISE
// ---------

include GetLang().'qte_reg.php';

$oVIP->selfurl = 'qte_form_reg.php';
$oVIP->selfname = $L['Register'];
if ( $_SESSION[QT]['register_mode']=='backoffice' ) $oVIP->selfname .= ' ('.L('request').')';

$strChild = '0';
if ( isset($_GET['c']) ) $strChild = substr($_GET['c'],0,1);
if ( isset($_POST['child']) ) $strChild = substr($_POST['child'],0,1);

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // pre-check code
  if ( empty($error) )
  {
    if ( $_SESSION[QT]['register_safe']!='none' )
    {
    if ( trim($_POST['code'])=='' )  $error = $L['Type_code'];
    if ( strlen($_POST['code'])!=6 ) $error = $L['Type_code'];
    }
  }

  // check name
  if ( empty($error) )
  {
    if ( get_magic_quotes_gpc() ) $_POST['title'] = stripslashes($_POST['title']);
    $_POST['title'] = QTconv($_POST['title'],'U');
    if ( !QTislogin($_POST['title']) ) $error=$L['Username'].' '.$L['E_invalid'];
    if ( empty($error) )
    {
    $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE username="'.$_POST['title'].'"');
    $row = $oDB->Getrow();
    if ($row['countid']!=0) $error=$L['Username'].' '.$L['E_already_used'];
    }
  }

  // check mail
  if ( empty($error) )
  {
    $_POST['mail'] = trim($_POST['mail']);
    if (!QTismail($_POST['mail'])) $error=$L['Email'].' '.$L['E_invalid'];
    /* check email is unique // NOT USED
    if ( empty($error) )
    {
    $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE emails="'.$_POST['mail'].'"');
    $row = $oDB->Getrow();
    if ($row['countid']!=0) $error=$L['Email'].S.$L['E_already_used'];
    }
    */
  }

  // check parentmail
  if ( empty($error) ) {
  if ( $_SESSION[QT]['register_coppa']=='1' && $strChild!='0' ) {
    $_POST['parentmail'] = trim($_POST['parentmail']);
    if ( !QTismail($_POST['parentmail']) ) $error=$L['Parent_mail'].' '.$L['E_invalid'];
  }}
  if ( !isset($_POST['parentmail']) ) $_POST['parentmail'] = '';


  // check password
  if ( empty($error) && $_SESSION[QT]['register_mode']=='direct' )
  {
    if ( get_magic_quotes_gpc() ) $_POST['pwd'] = stripslashes($_POST['pwd']);
    $_POST['pwd'] = QTconv($_POST['pwd'],'U');
    if ( !QTispassword($_POST['pwd']) ) $error = $L['Password'].' '.$L['E_invalid'];

    if ( get_magic_quotes_gpc() ) $_POST['conpwd'] = stripslashes($_POST['conpwd']);
    $_POST['conpwd'] = QTconv($_POST['conpwd'],'U');
    if ( !QTispassword($_POST['conpwd']) ) $error = $L['Password'].' '.$L['E_invalid'];
  }
  if ( empty($error) && $_SESSION[QT]['register_mode']=='direct' )
  {
    if ( $_POST['conpwd']!=$_POST['pwd'] ) $error = $L['Password'].' '.$L['E_invalid'];
  }

  // check code
  if ( empty($error) )
  {
    if ( $_SESSION[QT]['register_safe']!='none' )
    {
    $strCode = strtoupper(strip_tags(trim($_POST['code'])));
    if ($strCode=='') $error = $L['Type_code'];
    if ( $_SESSION['textcolor']!=sha1($strCode) ) $error = $L['Type_code'];
    }
  }

  // --------
  // register user
  // --------

  if ( empty($error) )
  {
    include 'bin/class/qt_class_smtp.php';

    if ( $_SESSION[QT]['register_mode']=='backoffice' )
    {
    	// Send email
    	$strSubject = $_SESSION[QT]['site_name'].' - Registration request';
    	$strFile = GetLang().'mail_request.php';
    	if ( file_exists($strFile) ) include $strFile;
    	if ( empty($strMessage) )
    	{
    	$strMessage  = 'This user request access to the board '.$_SESSION[QT]['site_name'].'.'.PHP_EOL;
    	$strMessage .= 'Username: %1$s'.PHP_EOL.'Email: %2$s'.PHP_EOL;
    	$strMessage .= PHP_EOL.'Open administration page to add this user: '.$_SESSION[QT]['site_url'].'/qte_adm_users.php?title=%1$s&mail=%2$s';
    	}
      $strMessage = sprintf($strMessage,$_POST['title'],$_POST['mail']);
      $strMessage = wordwrap($strMessage,70,"\r\n");
      QTmail($_SESSION[QT]['admin_email'],$strSubject,$strMessage);
      $oHtml->PageMsg(NULL,'<h2>'.L('Request_completed').'</h2><p>'.L('Reg_mail').'</p>',0,'350px','login_header','login');
    }
    else
    {
	    // email code
	    if ( $_SESSION[QT]['register_mode']==='email' ) $_POST['pwd'] = 'QT'.rand(0,9).rand(0,9).rand(0,9).rand(0,9);

	    $id = $oDB->Nextid(TABUSER);
	    $oDB->Query('INSERT INTO '.TABUSER.' (id,username,lastname,pwd,role,emails,birthdate,status,children,firstdate) VALUES ('.$id.',"'.$_POST['title'].'","'.$_POST['title'].'","'.sha1($_POST['pwd']).'","U","'.$_POST['mail'].'","0","Z","'.$strChild.'","'.date('Ymd').'")');
	    $oDB->Query('INSERT INTO '.TABS2U.' (sid,userid,issuedate) VALUES (0,'.$id.',"'.date('Ymd').'")');
	    if ( $strChild!='0' )
	    {
	    $oDB->Query('INSERT INTO '.TABCHILD.' (id,childdate,parentmail) VALUES ('.$id.',"'.date('Ymd').'","'.$_POST['parentmail'].'")');
	    }

	    // Unregister global sys (will be recomputed on next page)
	    Unset($_SESSION[QT]['sys_members']);
	    Unset($_SESSION[QT]['sys_states']);

	    // send email
	    $strSubject = $_SESSION[QT]['site_name'].' - Welcome';
	    $strFile = GetLang().'mail_registred.php';
	    if ( file_exists($strFile) ) include $strFile;
	    if ( empty($strMessage) )$strMessage = "Please find here after your login and password to access the board {$_SESSION[QT]['site_name']}.\r\nLogin: %s\r\nPassword: %s";
      $strMessage = sprintf($strMessage,$_POST['title'],$_POST['pwd']);
      $strMessage = wordwrap($strMessage,70,"\r\n");
      QTmail($_POST['mail'],$strSubject,$strMessage);

	    // parent mail
	    if ( $_SESSION[QT]['register_coppa']=='1' && $strChild!='0' )
	    {
	      $strSubject = $_SESSION[QT]['site_name'].' - Welcome';
	      $strFile = GetLang().'mail_registred_coppa.php';
	      if ( file_exists($strFile) ) include $strFile;
        if ( empty($strMessage) ) $strMessage = "We inform you that your children has registered on the team {$_SESSION[QT]['site_name']}.\nLogin: %s\nPassword: %s\nYour agreement is required to activte this account.";
	      $strMessage = sprintf($strMessage,$_POST['title'],$_POST['pwd']);
        $strMessage = wordwrap($strMessage,70,"\r\n");
        QTmail($_POST['parentmail'],$strSubject,$strMessage);
	    }

	    // index
	    $oItem = new cItem($id);
	    $oItem->SaveKeywords($oItem->GetKeywords(GetFields('index_p')));

	    // END MESSAGE
	    if ($_SESSION[QT]['register_mode']=='email')
	    {
	      $oVIP->exiturl = 'qte_index.php';
	      $oVIP->exitname = ObjTrans('index','i',$_SESSION[QT]['index_name']);
        $oHtml->PageMsg(NULL,'<h2>'.$L['Register_completed'].'</h2><p>'.$L['Reg_mail'].'</p>');
	    }
	    else
	    {
	      $oVIP->exiturl = 'qte_login.php?dfltname='.urlencode($_POST['title']);
	      $oVIP->exitname = $L['Login'];
        $oHtml->PageMsg(NULL,'<h2>'.$L['Register_completed'].'</h2><p>&nbsp;</p>',2);
	    }
	  }
  }
}

// --------
// HTML START
// --------

$oHtml->scripts[] = '<script type="text/javascript">
function ValidateForm(theForm)
{
  if (theForm.title.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Choose_name'].'")); return false; }
  if (theForm.mail.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Your_mail'].'")); return false; }
  if (theForm.code.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Security'].'")); return false; }
  if (theForm.code.value=="QT") { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Security'].'")); return false; }
  return null;
}
function MinChar(strField,strValue)
{
  if ( strValue.length>0 && strValue.length<4 )
  {
  document.getElementById(strField+"_err").innerHTML="<p>'.$L['E_min_4_char'].'</p>";
  return null;
  }
  else
  {
  document.getElementById(strField+"_err").innerHTML="";
  return null;
  }
}
</script>
';
$oHtml->scripts_jq[] = '
$(function() {
  $("#title").blur(function() {
    $.post("qte_j_exists.php",
      {f:"username",v:$("#title").val(),e1:"'.$L['E_min_4_char'].'",e2:"'.$L['E_already_used'].'"},
      function(data)
      {
        if ( data.length>0 ) document.getElementById("title_err").innerHTML=data;
      });
  });
});
';

include 'qte_inc_hd.php';

// DEFAULT VALUE RECOVERY (na)

if ( !isset($_POST['title']) ) $_POST['title']='';
if ( !isset($_POST['pwd']) ) $_POST['pwd']='';
if ( !isset($_POST['conpwd']) ) $_POST['conpwd']='';
if ( !isset($_POST['mail']) ) $_POST['mail']='';
if ( !isset($_POST['parentmail']) ) $_POST['parentmail']='';

if ( !isset($_SESSION[QT]['register_mode']) ) $_SESSION[QT]['register_mode']='direct';
if ( !isset($_SESSION[QT]['register_safe']) ) $_SESSION[QT]['register_safe']='text';

if ( $_SESSION[QT]['register_safe']=='text' )
{
  $keycode = 'QT'.rand(0,9).rand(0,9).rand(0,9).rand(0,9);
  $_SESSION['textcolor'] = sha1($keycode);
}

if ( $_SESSION[QT]['register_coppa']=='1' &&  $strChild!='0' )
{
  echo '<div class="scrollmessage">';
  $strFile = GetLang().'/sys_rules_coppa.txt';
  if ( file_exists($strFile) ) { include $strFile; } else { echo 'Missing file:<br />',$strFile; }
  echo '</div>';
}

$oHtml->Msgbox($oVIP->selfname,array('style'=>'width:620px'),array('id'=>'login_header'));

echo '<form method="post" action="',Href(),'" onsubmit="return ValidateForm(this);">
<table class="hidden">
<tr class="hidden">
<td class="hidden" style="width:370px;"><div id="login">
<fieldset class="register">
<legend>',ObjTrans('field','username'),'</legend>
<span class="small">',$L['Choose_name'],'</span>&nbsp;<input type="text" id="title" name="title" size="20" maxlength="24" value="',$_POST['title'],'" onfocus="document.getElementById(\'title_err\').innerHTML=\'\';" /><br /><span id="title_err" class="error"></span><br />
<br />
';
if ( $_SESSION[QT]['register_mode']=='direct' )
{
  echo '<span class="small">',$L['Choose_password'],'</span>&nbsp;<input required type="password" id="pwd" name="pwd" pattern=".{4}.*" size="20" maxlength="24" value="',$_POST['pwd'],'" /><br /><span id="pwd_err" class="error"></span>',PHP_EOL;
  echo '<span class="small">',$L['Confirm_password'],'</span>&nbsp;<input required type="password" id="conpwd" name="conpwd" pattern=".{4}.*" size="20" maxlength="24" value="',$_POST['conpwd'],'" /><br /><span id="conpwd_err" class="error"></span>',PHP_EOL;
}
else
{
  echo '<span class="small">',$L['Password_by_mail'],'</span><br />',PHP_EOL;
}
echo '</fieldset>',PHP_EOL;
echo '<fieldset class="register">',PHP_EOL;
echo '<legend>',$L['Email'],'</legend>',PHP_EOL;
echo '<span class="small">',$L['Your_mail'],'</span>&nbsp;<input required type="email" id="mail" name="mail" size="30" maxlength="64" value="',$_POST['mail'],'" /><br />',PHP_EOL;
if ( $_SESSION[QT]['register_coppa']=='1' && $strChild!='0' ) echo ' <span class="small">',$L['Parent_mail'],'</span>&nbsp;<input required type="email" id="parentmail" name="parentmail" size="30" maxlength="64" value="',$_POST['parentmail'],'" /><br />',PHP_EOL;
echo '</fieldset>',PHP_EOL;
echo '<fieldset class="register">',PHP_EOL;
echo '<legend>',$L['Security'],'</legend>',PHP_EOL;
if ( $_SESSION[QT]['register_safe']=='image') echo '<img width="100" height="35" src="admin/qte_icode.php" alt="security" style="text-align:right" /> <input type="text" name="code" pattern=".{6}.*" size="8" maxlength="8" value="QT" /><br /><span class="small">',$L['Type_code'],'</span>',PHP_EOL;
if ( $_SESSION[QT]['register_safe']=='text') echo $keycode,'&nbsp;<input type="text" id="code" name="code" pattern=".{6}.*" size="8" maxlength="8" value="QT" /><br /><span class="small">',$L['Type_code'],'</span>',PHP_EOL;
echo '</fieldset>',PHP_EOL;
echo '<input type="hidden" name="child" value="',$strChild,'" />';
echo '<p>',(!empty($error) ? '<span class="error">'.$error.'</span>' : ''),' <input type="submit" name="ok" value="',$L['Register'],'" /></p>',PHP_EOL;
echo '</div></td>',PHP_EOL;
echo '<td class="hidden" style="width:20px;">&nbsp;</td>',PHP_EOL;
echo '<td class="hidden"><span class="small">',$L['Reg_help'],'</span></td>
</tr>
</table>
</form>
';

$oHtml->Msgbox(END);

// --------
// HTML END
// --------

include 'qte_inc_ft.php';