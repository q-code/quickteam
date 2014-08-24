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
 * @package    QuickTeam team
 * @author     Philippe Vandenberghe <info@qt-cute.org>
 * @copyright  2014 The PHP Group
 * @version    3.0 build:20140608
 */

session_start();
require 'bin/qte_init.php';
include Translate(APP.'_adm.php');

if ( sUser::Role()!='A' ) die($L['E_admin']);

$a = 'add'; QThttpvar('a','str');

// ---------
// INITIALISE
// ---------

$oVIP->selfurl = 'qte_adm_module.php';
$oVIP->selfname = $L['Adm_modules'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  $strName = strtolower(strip_tags(trim($_POST['name']))); if ( get_magic_quotes_gpc() ) $strName = stripslashes($strName);
  $strName = str_replace(' ','_',$strName);
  $strFile = 'qtem_'.$strName.'_'.($a=='rem' ? 'un' : '').'install.php';

  if ( file_exists($strFile) )
  {
    // exit
    $oVIP->exiturl = $strFile;
    $oVIP->exitname = $L['Module_'.$a];
    $oHtml->PageMsgAdm(NULL,'<p>'.$L['Module_name'].': '.$strName.'</p><p><a href="'.$strFile.'">'.L(($a=='rem' ? 'Uninstall' : 'Install')).'</a> &middot <a href="qte_adm_module.php?a=add">'.L('Cancel').'</a></p>');
  }
  else
  {
    $error = 'Module not found... ('.$strFile.')<br /><br />Possible cause: components of this module are not uploaded.';
  }
}

// --------
// HTML START
// --------

include APP.'_adm_inc_hd.php';

echo '<form method="post" action="',$oVIP->selfurl,'">
<table class="t-data">
<tr class="tr">
<td class="headfirst" style="width:200px;"><label for="name">',$L['Module_'.$a],'</label></td>
<td><input id="name" name="name" size="12" maxlength="24" value="" />&nbsp;<span class="help">',$L['Module_name'],'</span></td>
</tr>
<tr class="tr">
<td class="headfirst">&nbsp;</td>
<td><input type="hidden" name="a" value="',$a,'"/><input type="submit" id="ok" name="ok" value="',$L['Search'],'" /></td>
</tr>
</table>
</form>
';

// --------
// HTML END
// --------

echo '<script type="text/javascript">document.getElementById(\'name\').focus();</script>',PHP_EOL;

include APP.'_adm_inc_ft.php';