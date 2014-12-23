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
if ( sUser::Role()!='A' ) die($L['R_admin']);

// ---------
// INITIALISE
// ---------

$id = '-1'; QThttpvar('id','str'); if ( $id=='-1' ) die('Missing id...');

include Translate(APP.'_adm.php');

$oVIP->selfurl = 'qte_adm_status.php';
$oVIP->exiturl = 'qte_adm_statuses.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br />'.$L['Status_upd'];
$oVIP->exitname = '&laquo; '.$L['Statuses'];
$arrStatuses = memGet('sys_statuses');

// --------
// SUBMITTED
// --------

if (isset($_POST['ok']))
{
  // check id
  if (!preg_match('/[A-Z]/',$id)) $error="Id $id ".$L['E_invalid'].' (A-Z)'; //A and Z can be edited (hidden input)

  // change id
  if ( empty($error) )
  {
    if ( $_POST['oldid']!=$id )
    {
    $error = cVIP::StatusChangeId($_POST['oldid'],$id);
    }
  }

  // check name
  if ( empty($error) )
  {
    $name = strip_tags(trim($_POST['name'])); if ( get_magic_quotes_gpc() ) $name = stripslashes($name);
    if ( $name=='' ) $error = 'Status name '.S.$L['E_invalid'];
    $name = QTconv($name,'3',QTE_CONVERT_AMP);
  }

  // check unic name
  if ( empty($error) )
  {
    if ( $_POST['oldname']!=$_POST['name'] )
    {
    $oDB->Query('SELECT count(*) as countid FROM '.TABSTATUS.' WHERE name="'.addslashes($name).'"');
    $row = $oDB->Getrow();
    if ($row['countid']>0) $warning = 'Name ['.$name.'] '.$L['E_already_used'];
    }
  }

  // check color
  if ( empty($error) )
  {
    $color = strip_tags(trim($_POST['color']));
  }

  // check icon
  if ( empty($error) )
  {
    $icon = strip_tags(trim($_POST['icon']));
    $icon = htmlspecialchars($icon,ENT_QUOTES);
    if ( $icon!=trim($_POST['icon']) ) $error = $L['Icon'].S.$L['E_invalid'];
  }

  // save

  if ( empty($error) )
  {
    $oDB->Exec('UPDATE '.TABSTATUS.' SET name="'.addslashes($name).'",color="'.$color.'",icon="'.$icon.'" WHERE id="'.$id.'"');

    //  save translation

    $oDB->Exec('DELETE FROM '.TABLANG.' WHERE (objtype="status" OR objtype="statusdesc") AND objid="'.$id.'"');

    foreach ($_POST as $strKey => $strTranslation)
    {
      if ( substr($strKey,0,1)=='T' )
      {
        if ( !empty($strTranslation) )
        {
        if ( get_magic_quotes_gpc() ) $strTranslation = stripslashes($strTranslation);
        cLang::Add('status',substr($strKey,1),$id,$strTranslation);
        }
      }
      if ( substr($strKey,0,1)=='D' )
      {
        if ( !empty($strTranslation) )
        {
        if ( get_magic_quotes_gpc() ) $strTranslation = stripslashes($strTranslation);
        cLang::Add('statusdesc',substr($strKey,1),$id,$strTranslation);
        }
      }
    }

    // Exit

    memUnset('sys_statuses');
    $oVIP->selfname = $L['Status_upd'];
    $oHtml->PageMsgAdm(NULL,$L['S_update'].'<br /><br /><span class="warning">'.$warning.'</span>',(!empty($warning) ? 0: 2));
  }
  else
  {
    $id = $_POST['oldid'];
  }
}

// --------
// HTML START
// --------

include APP.'_adm_inc_hd.php';

echo '<table class="hidden">
<tr>
<td style="width:20px">',$id,'</td>
<td style="width:30px">',AsImg($_SESSION[QT]['skin_dir'].'/'.$arrStatuses[$id]['icon'],'-',$arrStatuses[$id]['statusdesc'],'ico i-status'),'</td>
<td style="width:100px;padding:3px 10px 3px 10px;text-align:center;background-color:',(isset($arrStatuses[$id]['color'][1]) ? $arrStatuses[$id]['color'] : 'transparent'),'; border-style:solid; border-color:#dddddd; border-width:1px">',$arrStatuses[$id]['statusname'],'</td>
<td>&nbsp;</td>
</tr>
</table>
<br />';
echo '<form method="POST" action="',$oVIP->selfurl,'">
<h2 class="subtitle">',$L['Definition'],'</h2>
<table class="t-data">
<tr>
<td class="headfirst" style="width:150px;"><label for="id">Id</label></td>
<td>
';
if ( ($id=='A') || ($id=='Z') )
{
  echo $id.'&nbsp;<input type="hidden" name="id" value="',$id,'"/>';
}
else
{
  echo '<input type="text" id="id" name="id" size="1" maxlength="1" value="',$id,'" onchange="bEdited=true;"/>';
}
echo '</td>
</tr>
';
$str = QTconv($arrStatuses[$id]['name'],'I');
echo '<tr>
<td class="headfirst"><label for="name">Name</label></td>
<td><input type="text" id="name" name="name" size="24" maxlength="24" value="',$str,'" style="background-color:#FFFF99" onchange="bEdited=true;"/>',(strstr($str,'&amp;') ?  ' <span class="disabled">'.$arrStatuses[$id]['name'].'</span>' : ''),'</td>
</tr>
';
echo '<tr>
<td class="headfirst"><label for="icon">Icon</label></td>
<td><input type="text" id="icon" name="icon" size="24" maxlength="64" value="',$arrStatuses[$id]['icon'],'" onchange="bEdited=true;"/>&nbsp;',AsImg($_SESSION[QT]['skin_dir'].'/'.$arrStatuses[$id]['icon'],'-',$arrStatuses[$id]['statusdesc'],'ico i-status'),'&nbsp;&nbsp;<a href="qte_ext_statusico.php" target="_blank">show icons</a></td>
</tr>
';
echo '<tr>
<td class="headfirst"><label for="color">',$L['Status_background'],'</label></td>
<td>
<input type="text" id="color" name="color" size="10" maxlength="24" value="',(empty($arrStatuses[$id]['color']) ? '#' : $arrStatuses[$id]['color']),'" onchange="document.getElementById(\'colorpicker\').value=document.getElementById(\'color\').value;bEdited=true;" />
&nbsp;<input type="color" id="colorpicker" size="10" maxlength="24" value="',(empty($arrStatuses[$id]['color']) ? '#ffffff' : $arrStatuses[$id]['color']),'" onchange="document.getElementById(\'color\').value=document.getElementById(\'colorpicker\').value;" /><br/>
<span class="small">',$L['H_Status_background'],'</span>
</td>
</tr>
</table>
';

echo '<h2 class="subtitle">',$L['Translations'],'</h2>

<table class="t-data">
<tr class="t-data">
<td class="headfirst">',$L['Title'],'</td>
<td><p style="margin:4px">',sprintf($L['E_no_translation'],ucfirst(str_replace('_',' ',$arrStatuses[$id]['name']))),'</p>
<table>
';
$arrTrans = cLang::Get('status','*',$id);
$arrDescTrans = cLang::Get('statusdesc','*',$id);
include 'bin/qte_lang.php'; // this creates $arrLang
foreach($arrLang as $strIso=>$arr)
{
  $str = '';
  if ( isset($arrTrans[$strIso]) ) {
  if ( !empty($arrTrans[$strIso]) ) {
    $str = QTconv($arrTrans[$strIso],'I');
  }}
  echo '
  <tr>
  <td style="width:30px"><span title="',$arr[1],'">',$arr[0],'</span></td>
  <td><input style="width:250px" class="small" title="',$L['Section'],' (',$strIso,')" type="text" id="T',$strIso,'" name="T',$strIso,'" size="30" maxlength="64" value="',$str,'" onchange="bEdited=true;"/>&nbsp;</td>
  </tr>
  ';
}
echo '</table>
</td>
</tr>
<tr class="t-data">
<td class="headfirst">',$L['Description'],'</td>
<td>
<table>';
foreach($arrLang as $strIso=>$arr)
{
  $str = '';
  if ( isset($arrDescTrans[$strIso]) ) {
  if ( !empty($arrDescTrans[$strIso]) ) {
    $str = QTconv($arrDescTrans[$strIso],'I');
  }}
  echo '
  <tr>
  <td style="width:30px"><span title="',$arr[1],'">',$arr[0],'</span></td>
  <td><textarea style="width:250px" class="small" title="',$L['Description'],' (',$strIso,')" id="D',$strIso,'" name="D',$strIso,'" cols="45" rows="2" onchange="bEdited=true;">',$str,'</textarea></td>
  </tr>
  ';
}
echo '</table>
</td>
</tr>
</table>
';
echo '<p class="submit">
<input type="hidden" name="oldid" value="',$id,'"/>
<input type="hidden" name="oldname" value="',QTconv($arrStatuses[$id]['name'],'1'),'"/>
<input type="submit" name="ok" value="',$L['Save'],'"/>
</p>
</form>
<p><a href="',$oVIP->exiturl,'" onclick="return qtEdited(bEdited,\''.$L['E_editing'].'\');">',$oVIP->exitname,'</a></p>
';

// --------
// HTML END
// --------

include APP.'_adm_inc_ft.php';