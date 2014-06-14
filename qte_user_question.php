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
* @copyright  2013 The PHP Group
* @version    3.0 build:20140608
*/

session_start();
require_once 'bin/qte_init.php';
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_profile.css" />';
if ( !sUser::CanView('U') ) die($L['E_member']);

// INITIALISE

include 'bin/class/qt_class_smtp.php';
include Translate('qte_reg.php');

$id = -1;
if ( isset($_GET['id']) ) $id = intval(strip_tags($_GET['id']));
if ( isset($_POST['id']) ) $id = intval(strip_tags($_POST['id']));
if ( $id<=0 ) die('Missing parameter');

$oVIP->selfurl = 'qte_user_question.php';
$oVIP->selfname = $L['Secret_question'];
$oVIP->exiturl = 'qte_user.php?id='.$id;
$oVIP->exitname = '&laquo; '.$L['Profile'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // CHECK VALUE and protection against injection

  $strQ = trim($_POST['secret_q']); if ( get_magic_quotes_gpc() ) $strQ = stripslashes($strQ);
  $strA = trim($_POST['secret_a']); if ( get_magic_quotes_gpc() ) $strA = stripslashes($strA);

  if ( empty($error) )
  {
    // save new password
    $oDB->Query('UPDATE '.TABUSER.' SET secret_q="'.QTconv($strQ,'3').'",secret_a="'.QTconv(strtolower($strA),'3').'" WHERE id='.$id);

    // exit
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

$oItem = new cItem($id);

$oHtml->scripts[] = '<script type="text/javascript">
function ValidateForm(theForm)
{
  if (theForm.secret_a.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Secret_question'].'")); return false; }
  return null;
}
</script>
';

include 'qte_p_header.php';

echo '<table class="profile">',PHP_EOL;
echo '<tr>',PHP_EOL;
echo '<td class="profileleft">';
echo  '<p class="picture username">'.UserFirstLastName($oItem,'<br/>').'</p>'.UserPicture($oItem).'<p class="picture userstatus">'.$oItem->GetStatusIcon().' '.$oItem->GetStatusName().'</p>';
if ( sUser::Id()!=$id ) echo '<div class="warning">',$L['W_Somebody_else'],'</div>';
echo '</td>',PHP_EOL;
echo '<td>',PHP_EOL;

$oHtml->Msgbox($oVIP->selfname,'msgbox login,msgboxtitle login,msgboxbody login');

echo '<form method="post" action="',Href(),'" onsubmit="return ValidateForm(this);">
<p><select id="secret_q" name="secret_q">',QTasTag($L['Secret_q'],$oItem->secret_q),'</select></p>
<p><input type="text" id="secret_a" name="secret_a" size="32" maxlength="255" value="',$oItem->secret_a,'"/></p>
<p>';
if ( !empty($error) ) echo '<span class="error">',$error,' </span>';
echo '<input type="submit" id="ok" name="ok" value="',$L['Save'],'"/>&nbsp;&nbsp;
<input type="button" id="cancel" name="cancel" value="'.L('Cancel').'" onclick="window.location=\''.$oVIP->exiturl.'\';"/>
<input type="hidden" name="id" value="',$id,'"/></p>
<p>',$L['H_Secret_question'],'</p>
</form>
';

$oHtml->Msgbox(END);

echo '
</td>
</tr>
</table>
';

// HTML END

include 'qte_p_footer.php';