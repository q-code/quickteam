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
if ( $_SESSION[QT]['board_offline']=='1' ) { EchoPage(99); return; }
$oHtml->links['css'] = '<link rel="stylesheet" type="text/css" href="'.$_SESSION[QT]['skin_dir'].'/qte_profile.css" />';
$oHtml->scripts = array();

// --------
// INITIALISE
// --------

include GetLang().'qte_reg.php';

$c='2'; QThttpvar('c','str');

$oVIP->selfurl = 'qte_register.php';
$oVIP->selfname = $L['Register'];
$oVIP->exitname = '&laquo; '.$L['Register'];

// --------
// EXECUTE FORM
// --------

if ( isset($_POST['ok']) )
{
  if ( !isset($_POST['agreed']) )
  {
    include 'qte_p_header.php';
    $oHtml->Msgbox($oVIP->selfname,array(),array('id'=>'login_header'),array('id'=>'login'));
    $strFile=GetLang().'sys_not_agree.txt';
    if ( file_exists($strFile) ) { include $strFile; } else { echo 'Rules not agreed...'; }
    echo '<p><a href="',Href(),'?c='.$c.'">',$L['Register'],'</a></p>';
    $oHtml->Msgbox(END);
    include 'qte_p_footer.php';
    Exit;
  }
  $oHtml->Redirect('qte_form_reg.php?c='.$c,$L['Register']);
}

// --------
// HTML START
// --------

include 'qte_p_header.php';

echo '
<div class="scrollmessage">';
$strFile = GetLang().'sys_rules.txt';
if ( file_exists($strFile) ) { include $strFile; } else { echo "Missing file:<br />$strFile"; }
echo '</div>
';
echo '
<form method="post" action="',Href(),'"><p><input type="checkbox" id="agreed" name="agreed" /> <label for="agreed"><b>&nbsp;',$L['Agree'],'</b></label></p>
';
$oHtml->Msgbox($oVIP->selfname,array('style'=>'width:350px'),array('id'=>'login_header'),array('id'=>'login'));
echo '<p>';
if ( $_SESSION[QT]['register_coppa']=='1' )
{
echo '<input type="radio" id="child1" name="c" value="2"',($c=='2' ? QCHE : ''),' /><label for="child1">',$L['I_am_child'],'</label><br />';
echo '<input type="radio" id="child0" name="c" value="0"',($c=='0' ? QCHE : ''),' /><label for="child0">',$L['I_am_not_child'],'</label><br />';
}
else
{
echo $L['Proceed'];
}
echo '</p>
<p><input type="submit" name="ok" value="',$L['Ok'],'" /></p>
';
$oHtml->Msgbox(END);
echo'
</form>
';
// --------
// HTML END
// --------

include 'qte_p_footer.php';