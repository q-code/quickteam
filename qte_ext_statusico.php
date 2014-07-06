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
include Translate('@_adm.php');

if ( sUser::Role()!='A' ) die($L['E_admin']);

// INITIALISE

$oVIP->selfurl = 'qte_ext_statusico.php';
$oVIP->exiturl = 'qte_adm_status.php';
$oVIP->selfname = 'Icons';
$oVIP->exitname = $L['Statuses'];

$arrFiles=array();
$arrStatuses=array();

// --------
// HTML START
// --------

$bJava=false;
include APP.'_adm_inc_hd.php';

// Browse image file

$intHandle = opendir($_SESSION[QT]['skin_dir']);

$i=0;
while (false !== ($file = readdir($intHandle)))
{
  $file=strtolower($file);
  if ( $file!='.' && $file!='..' ) {
    if ( substr($file,0,6)=='status' )
    {
    $arrStatuses[] = $file;
    }
    else
    {
    if ( substr($file,0,3)!='bg_' && substr($file,0,10)!='background' ) $arrFiles[] = $file;
    }
    $i++;
  }
}
closedir($intHandle);
sort($arrStatuses);
sort($arrFiles);

echo $_SESSION[QT]['skin_dir'],', ',$i,' files<br /><br />';

echo '
<table  class="hidden">
<tr>
<td style="width:250px;vertical-align:top">
';

echo '<table  class="hidden" style="background-color:#ffffff">
<groupcol><col></col><col style="width:120px"></col></groupcol>
<tr><td style="padding-left:4px"><b>Icon</b></td><td><b>File</b></td></tr>',PHP_EOL;
foreach($arrStatuses as $key => $val)
{
  if (strtolower(substr($val,-4,4))=='.gif')
  {
  echo '<tr><td style="padding-left:4px"><img src="',$_SESSION[QT]['skin_dir'],'/',$val,'"/></td><td class="td_icon">',$val,'</td></tr>',PHP_EOL;
  }
}
echo '</table>
';
echo '
</td>
<td style="width:20px;">
<td style="width:250px;vertical-align:top">
';
echo '<table  class="hidden" style="background-color:#ffffff">
<groupcol><col></col><col style="width:120px"></col></groupcol>
<tr><td style="padding-left:4px"><b>Icon</b></td><td><b>File</b></td></tr>',PHP_EOL;
foreach($arrFiles as $key => $val)
{
  if (strtolower(substr($val,-4,4))=='.gif')
  {
  echo '<tr><td style="padding-left:4px"><img src="',$_SESSION[QT]['skin_dir'],'/',$val,'"/></td><td class="td_icon">',$val,'</td></tr>',PHP_EOL;
  }
}
echo '</table>
';
echo '
</td>
<td>&nbsp;</td>
</tr>
</table>
';

// --------
// HTML END
// --------

include APP.'_adm_inc_ft.php';