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
if ( !sUser::CanView('U') ) die(Error(11));
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_profile.css"/>';
$id = -1; QThttpvar('id','int'); if ( $id<0 ) die('Missing id...');

// --------
// INITIALISE
// --------

include 'bin/class/qt_class_smtp.php';
include Translate('@_reg.php');

$oVIP->selfurl = 'qte_unregister.php';
$oVIP->selfname = $L['Unregister'];
$oVIP->exiturl = Href('qte_user.php').'?tt=s&amp;id='.$id;
$oVIP->exitname = '&laquo; '.$L['Profile'];

if ($id<2 ) $oHtml->PageMsg(NULL,$L['E_access'].'<br />Visitor and System administrator cannot be deleted.',);
if (sUser::Id()!=$id ) $oHtml->PageMsg(NULL,$L['E_access'].'<br />Only user himself can unregister. System coordinator can delete users.');

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // check password
  $oDB->Query('SELECT count(id) as countid FROM '.TABUSER.' WHERE id='.$id.' AND pwd="'.sha1($_POST['title']).'"');
  $row = $oDB->Getrow();
  if ($row['countid']==0) $error=$L['Password'].' '.$L['E_invalid'];

  // execute and exit
  if ( empty($error) )
  {
    $oDB->Query('SELECT * FROM '.TABUSER.' WHERE id='.$id);
    $row = $oDB->Getrow();
    $oItem = new cItem($row);
    $oItem->Delete(true); // delete and update stats in all sections

    $oVIP->exiturl = Href('qte_login.php').'?a=out';
    $_SESSION['pagedialog'] = 'O|'.$L['S_delete'];
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

$oItem = new cItem($id);

$oHtml->scripts[] = '
<script type="text/javascript">
function ValidateForm(theForm)
{
  if (theForm.title.value.length==0) { alert(qtHtmldecode("'.$L['Missing'].': '.$L['Password'].'")); return false; }
  return null;
}
</script>
';

include 'qte_inc_hd.php';

echo '
<table class="hidden">
<tr class="hidden">
<td class="hidden" style="width:175px;"><br />',AsImgBoxUser($oItem,'username'),'</td>
<td class="hidden">
';

$oHtml->Msgbox($oVIP->selfname,'msgbox login,msgboxtitle login,msgboxbody login');

$str = '<p class="left">'.$L['H_Unregister'].'</p>
<form method="post" action="'.Href().'" onsubmit="return ValidateForm(this);">
<p>'.$L['Password'].' <input type="password" id="title" name="title" pattern=".{4}.*" size="20" maxlength="32" /></p>
<p>
<span id="title_err" class="error"></span> <input type="submit" name="ok" value="'.$L['Ok'].'" />&nbsp;&nbsp;
<input type="hidden" name="id" value="'.$id.'" />
<input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
</p>
</form>
';
if ( $oItem->role!='U' ) $str = '<p>'.$oItem->username.' is a Staff member.<br />To unregister a staff member, an administrator must first change role to User, or use the delete function.</p>';
if ( $id<2 ) $str = '<p>Admin and Visitor cannot be removed...</p>';

if ( !empty($error) ) echo '<p id="infomessage" class="error">',$error,'</p>';
echo '<h2>',$oItem->fullname,'</h2>
<p class="bold">username: ',$oItem->username,'</p>
',$str;

$oHtml->Msgbox(END);

echo '
</td>
</tr>
</table>
';

// --------
// HTML END
// --------

$oHtml->scripts_end[] = '
<script type="text/javascript">
document.getElementById("title").focus();
</script>
';

include 'qte_inc_ft.php';