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
require 'bin/qte_init.php';
if ( !sUser::CanView('U') ) die(Error(11));
$id = -1; QThttpvar('id','int'); if ($id<0) die('Missing parameters');
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_profile.css" />';

// --------
// INITIALISE
// --------

include 'bin/class/qt_class_smtp.php';
include Translate(APP.'_reg.php');

$oVIP->selfurl = 'qte_user_rename.php';
$oVIP->selfname = $L['Change_name'];
$oVIP->exiturl = Href('qte_user.php').'?tt=s&amp;id='.$id;
$oVIP->exitname = '&laquo; '.$L['Profile'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // check name
  if ( empty($error) )
  {
    $strName = trim(strip_tags($_POST['title'])); if ( get_magic_quotes_gpc() ) $strName = stripslashes($strName);
    if ( !QTislogin($strName) ) $error = $L['E_invalid'];
    if ( empty($error) )
    {
    $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE username="'.$strName.'"');
    $row = $oDB->Getrow();
    if ( $row['countid']!=0 ) $error = $L['E_already_used'];
    }
  }

  // execute and exit
  if ( empty($error) )
  {
    $oDB->Exec('UPDATE '.TABUSER.' SET username="'.$strName.'" WHERE id='.$id);
    // index
    $oDB->Exec('DELETE FROM '.TABINDEX.' WHERE userid='.$id.' AND ufield="username"');
    $oDB->Exec('INSERT INTO '.TABINDEX.' (userid,ufield,ukey) VALUES ('.$id.',"username","'.$strName.'")');

    $oVIP->exiturl = Href('qte_login.php').'?dfltname='.$strName;
    $oVIP->exitname = '&laquo; '.$L['Login'];
    // exit
    $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
    $oHtml->Redirect($oVIP->exiturl);
  }
  else
  {
  	$_SESSION['pagedialog'] = 'E|'.$error;
  }
}

$oItem = new cItem($id);

// --------
// HTML START
// --------

include 'qte_inc_hd.php';

$oHtml->scripts_end[] = '
<script type="text/javascript">
function ValidateForm(theForm)
{
  if (theForm.title.value.length==0) { alert(qtHtmldecode("'.$L['Missing'].': '.$L['Username'].'")); return false; }
  return null;
}
</script>
';

$oHtml->scripts_jq[] = '
$(function() {
  $("#title").keyup(function() {
    if ($("#title").val().length>1)
    {
      $.post("qte_j_exists.php",
      {f:"username",v:$("#title").val(),e1:"'.$L['E_min_4_char'].'",e2:"'.$L['E_already_used'].'"},
      function(data) { if ( data.length>0 ) document.getElementById("title_err").innerHTML=data; });
    }
    else
    {
      document.getElementById("title_err").innerHTML="";
    }
  });
});
';

echo '<table class="profile">',PHP_EOL;
echo '<tr>',PHP_EOL;
echo '<td class="profileleft">';
echo UserPicture($oItem);
echo (empty($oItem->firstname) ? '' : '<p class="picture username">'.$oItem->firstname.'</p>');
echo '<p class="picture username">',$oItem->lastname,'</p>';
echo '<p class="picture userstatus">',$oItem->GetStatusIcon(),' ',$oItem->GetStatusName(),'</p>';
if ( sUser::Id()!=$id ) echo '<div class="warning">',$L['W_Somebody_else'],'</div>';
echo '</td>',PHP_EOL;
echo '<td>',PHP_EOL;

$oHtml->MsgBox($oVIP->selfname,'msgbox login,msgboxtitle login,msgboxbody login');

if ( !empty($error) ) echo '<p class="error">',$error,'</p>';
echo '<form method="post" action="',Href(),'" onsubmit="return ValidateForm(this);">
<input type="hidden" name="id" value="',$id,'" />
<h2>',$oItem->username,'</h2>
<p>',$L['Choose_name'],'</p>
<p><input type="text" id="title" name="title" size="20" maxlength="32" pattern=".{4}.*" onfocus="document.getElementById(\'title_err\').innerHTML=\'\';" /></p>
<p>
<span id="title_err" class="error"></span>
<input type="submit" name="ok" value="',$L['Save'],'" />
<input type="button" id="cancel" name="cancel" value="',L('Cancel'),'" onclick="window.location=\'',$oVIP->exiturl,'\';"/>
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