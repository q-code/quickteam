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
require 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die($L['R_admin']);

// ---------
// INITIALISE
// ---------

include Translate(APP.'_adm.php');

$oVIP->selfurl = 'qte_adm_statuses.php';
$oVIP->exiturl = 'qte_adm_statuses.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br />'.$L['Statuses'];
$oVIP->exitname = $L['Statuses'];
$arrStatuses = memGet('sys_statuses');
  
// --------
// SUBMITTED
// --------

if ( isset($_POST['ok_add']) )
{
  // check id, name and duplicate id

  $id = strtoupper($_POST['id']);
  if ( !preg_match('/[A-Y]/',$id) ) $error="Id $id ".$L['E_invalid']." (A-Y)";
  $name = trim($_POST['name']); if ( get_magic_quotes_gpc() ) $name = stripslashes($name);
  if ( $name=='' ) $error = $L['Status'].S.$L['E_invalid'];

  if ( array_key_exists($id,$arrStatuses) ) $error = $L['Status'].' ['.$id.'] '.strtolower($L['E_already_used']);

  // add and exit

  if ( empty($error) )
  {
    $error = cVIP::StatusAdd($id,$name,'status_0.gif');
  }
  if ( empty($error) )
  {
    $oVIP->exiturl = 'qte_adm_status.php?id='.$id;
    $oVIP->exitname = $L['Status_upd'];
  }
  // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_insert'] : 'E|'.$error);
  $oHtml->Redirect($oVIP->exiturl);
}

// --------
// SUBMITTED for show
// --------

if ( isset($_POST['ok_show']) )
{
  if ( $_POST['show_Z']=='0' ) { $_SESSION[QT]['show_Z']='0'; }  else { $_SESSION[QT]['show_Z']='1'; }
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['show_Z'].'" WHERE param="show_Z"');
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);
}

// --------
// HTML START
// --------

include APP.'_adm_inc_hd.php';

echo '<form method="post" action="',$oVIP->selfurl,'">
<table class="t-item">
<tr>
<th class="center" style="width:30px">Id</th>
<th>&nbsp;</th>
<th>',$L['Status'],'</th>
<th>',$L['Status_background'],'</th>
<th class="center">',$L['Action'],'</th>
</tr>
';

foreach( $arrStatuses as $id=>$arrStatus  )
{
  echo '<tr class="rowlight">',PHP_EOL;
  echo '<td class="center">',$id,'</td>',PHP_EOL;
  echo '<td>',AsImg($_SESSION[QT]['skin_dir'].'/'.$arrStatus['icon'],'-',$arrStatus['statusname'],'ico i-status'),'</td>',PHP_EOL;
  echo '<td><a class="bold" href="qte_adm_status.php?id=',$id,'">',$arrStatus['statusname'],'</a>',(empty($arrStatus['statusdesc']) ? '' : '<span class="disabled"> &middot; '.$arrStatus['statusdesc'].'</span>'),'</td>',PHP_EOL;
  echo '<td',( empty($arrStatus['color']) ? '' : ' style="background-color:'.$arrStatus['color'].'"'),'>&nbsp;</td>',PHP_EOL;
  echo '<td class="c-action"><a href="qte_adm_status.php?id=',$id,'">',$L['Edit'],'</a>&nbsp;&middot;&nbsp;';
  if ( $id=='Z' ) { echo '<span class="disabled">',$L['Delete']; } else { echo '<a href="qte_change.php?a=status_del&amp;v=',$id,'">',$L['Delete'],'</a>'; }
  echo '</td>',PHP_EOL,'</tr>',PHP_EOL;
}

echo '
<tr>
<td class="colgroup"><input required type="text" name="id" value="" size="1" maxlength="1" class="small" onchange="bEdited=true;"/></td>
<td class="colgroup">&nbsp;</td>
<td class="colgroup" colspan="3"><input required type="text" name="name" value="" size="20" maxlength="24" class="small" onchange="bEdited=true;"/> <input type="submit" name="ok_add" value="',$L['Add'],'"/></td>
</tr>
</table>
</form>
';

echo '<h2>',$L['Display_options'],'</h2>';

echo '
<form method="post" action="',$oVIP->selfurl,'" onsubmit="return ValidateForm(this);">
<table  class="t-data">
<tr class="tr">
<td class="headfirst" style="width:150px"><label for="show_closed">',$L['Show_z'],'</label></td>
<td><select id="show_Z" name="show_Z" onchange="bEdited=true;">
<option value="0"',($_SESSION[QT]['show_Z']=='0' ? QSEL : ''),'>',$L['N'],'</option>
<option value="1"',($_SESSION[QT]['show_Z']=='1' ? QSEL : ''),'>',$L['Y'],'</option>
</select> <span class="small">',sprintf($L['H_Show_z'],$arrStatuses['Z']['statusname']),'</span></td>
<td><input type="submit" name="ok_show" value="',$L['Ok'],'"/></td>
</tr>
</table>
</form>
';

// --------
// HTML END
// --------

include APP.'_adm_inc_ft.php';