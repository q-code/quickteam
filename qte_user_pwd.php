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
 * @category   Team
 * @package    QuickTeam
 * @author     Philippe Vandenberghe <info@qt-cute.org>
 * @copyright  2014 The PHP Group
 * @version    3.0 build:20140608
 */

session_start();
require_once 'bin/qte_init.php';
if ( !sUser::CanView('U') ) die(Error(11));
$id = -1; QThttpvar('id','int'); if ($id<0) die('Missing parameters');
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_profile.css" />';

// --------
// INITIALISE
// --------

include 'bin/class/qt_class_smtp.php';
include GetLang().'qte_reg.php';

$oVIP->selfurl = 'qte_user_pwd.php';
$oVIP->selfname = $L['Change_password'];
$oVIP->exiturl = Href('qte_user.php').'?tt=s&amp;id='.$id;
$oVIP->exitname = '&laquo; '.$L['Profile'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // check value
  if ( !QTispassword($_POST['oldpwd']) ) $error=$L['Old_password'].' '.$L['E_invalid'];
  if ( !QTispassword($_POST['newpwd']) ) $error=$L['New_password'].' '.$L['E_invalid'];
  if ( !QTispassword($_POST['conpwd']) ) $error=$L['Confirm_password'].' '.$L['E_invalid'];
  if ( $_POST['oldpwd']==$_POST['newpwd'] ) $error=$L['New_password'].' '.$L['E_invalid'];
  if ( $_POST['conpwd']!=$_POST['newpwd'] ) $error=$L['Confirm_password'].' '.$L['E_invalid'];

  // check old pwd
  if ( empty($error) )
  {
    $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE id='.$id.' AND pwd="'.sha1($_POST['oldpwd']).'"');
    $row = $oDB->Getrow();
    if ($row['countid']==0) $error=$L['Old_password'].' '.$L['E_invalid'];
  }

  // execute and exit
  if ( empty($error) )
  {
    $oDB->Exec('UPDATE '.TABUSER.' SET pwd="'.sha1($_POST['newpwd']).'" WHERE id='.$id);

    // send parent email (if coppa)
    if ( $_POST['children']!='0' && $_SESSION[QT]['register_coppa']=='1' )
    {
      $oDB->Query('SELECT parentmail FROM '.TABCHILD.' WHERE id='.$id);
      $row = $oDB->Getrow();
      $strSubject = $_SESSION[QT]['site_name'].' - New password';
      $strFile = GetLang().'mail_pwd_coppa.php';
      if ( file_exists($strFile) ) include $strFile;
      if ( empty($strMessage) ) $strMessage = "We inform you that your children has changed his/her password on the board {$_SESSION[QT]['site_name']}.\nLogin: %s\nPassword: %s";
      $strMessage = sprintf($strMessage,$_POST['username'],$_POST['newpwd']);
      $strMessage = wordwrap($strMessage,70,"\r\n");
      QTmail($row['parentmail'],QTconv($strSubject,'-4'),QTconv($strMessage,'-4'),QT_HTML_CHAR);
    }

    //exit
    $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
    $oHtml->Redirect($oVIP->exiturl);
  }
  else
  {
  	$_SESSION['pagedialog'] = 'E|'.$error;
  }
}

// --------
// HTML START
// --------

include 'qte_inc_hd.php';

$oHtml->scripts_end[] = '
<script type="text/javascript">
function ValidateForm(theForm)
{
  if (theForm.oldpwd.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Old_password'].'")); return false; }
  if (theForm.newpwd.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['New_password'].'")); return false; }
  if (theForm.conpwd.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Confirm_password'].'")); return false; }
  return null;
}
qtFocusEnd("oldpwd");
</script>';

// CHECK ACCESS RIGHT

if ( ( sUser::Role()!='A' ) && (sUser::Id()!=$id) ) die($L['R_user']);

$oItem = new cItem($id);

echo '<table class="profile">',PHP_EOL;
echo '<tr>',PHP_EOL;
echo '<td class="profileleft">';
echo '<p class="picture username">'.UserFirstLastName($oItem,'<br/>').'</p>';
echo UserPicture($oItem);
echo '<p class="picture userstatus">',$oItem->GetStatusIcon(),' ',$oItem->GetStatusName(),'</p>';
if ( sUser::Id()!=$id ) echo '<div class="warning">',$L['W_Somebody_else'],'</div>';
echo '</td>',PHP_EOL;
echo '<td>',PHP_EOL;

$oHtml->MsgBox($oVIP->selfname,'msgbox login,msgboxtitle login,msgboxbody login');

if ( !empty($error) ) echo '<p class="error">',$error,'</p>';
echo '<form method="post" action="',Href(),'" onsubmit="return ValidateForm(this);">
<p>',$L['Old_password'],'&nbsp;<input type="password" id="oldpwd" name="oldpwd" pattern=".{4}.*" size="20" maxlength="24" /></p>
<p>',$L['New_password'],'&nbsp;<input type="password" id="newpwd" name="newpwd" pattern=".{4}.*" size="20" maxlength="24" /></p>
<p>',$L['Confirm_password'],'&nbsp;<input type="password" id="conpwd" name="conpwd" pattern=".{4}.*" size="20" maxlength="24" /></p>
<p>
<input type="submit" id="ok" name="ok" value="',$L['Save'],'" />&nbsp;&nbsp;
<input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
<input type="hidden" name="id" value="',$id,'" />
<input type="hidden" name="username" value="',$oItem->username,'" />
<input type="hidden" name="children" value="',$oItem->coppa,'" />
</p>
</form>
';

$oHtml->Msgbox(END);

echo '
</td>
</tr>
</table>
';

// --------
// HTML END
// --------

include 'qte_inc_ft.php';