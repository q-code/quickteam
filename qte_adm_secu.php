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
 * @version    3.0 build:20141222
 */

session_start();
require 'bin/qte_init.php';
include Translate(APP.'_adm.php');

if ( sUser::Role()!='A' ) die($L['E_admin']);

// ---------
// INITIALISE
// ---------

$oVIP->selfurl = 'qte_adm_secu.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_settings'].'</span><br />'.$L['Adm_security'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // check form

  $_SESSION[QT]['visitor_right']=$_POST['pal'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['visitor_right'].'" WHERE param="visitor_right"');

  $_SESSION[QT]['member_right']=$_POST['mal'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['member_right'].'" WHERE param="member_right"');

  $_SESSION[QT]['login_qtf']=trim($_POST['login_qtf']);
  if ( empty($_SESSION[QT]['login_qtf']) || strlen($_SESSION[QT]['login_qtf'])<3 ) $_SESSION[QT]['login_qtf']='0';
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['login_qtf'].'" WHERE param="login_qtf"');

  $_SESSION[QT]['login_qti']=trim($_POST['login_qti']);
  if ( empty($_SESSION[QT]['login_qti']) || strlen($_SESSION[QT]['login_qti'])<3 ) $_SESSION[QT]['login_qti']='0';
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['login_qti'].'" WHERE param="login_qti"');

  $_SESSION[QT]['login_qte_web']=$_POST['login_qte_web'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['login_qte_web'].'" WHERE param="login_qte_web"');

  $_SESSION[QT]['register_mode']=$_POST['regmode'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_POST['regmode'].'" WHERE param="register_mode"');

  $_SESSION[QT]['register_safe']=$_POST['regsafe'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_POST['regsafe'].'" WHERE param="register_safe"');

  $_SESSION[QT]['register_coppa']=$_POST['regcoppa'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_POST['regcoppa'].'" WHERE param="register_coppa"');

  $_SESSION[QT]['picture']=$_POST['picture'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_POST['picture'].'" WHERE param="picture"');

  if ( $_SESSION[QT]['picture']!='0' )
  {
    if ( isset($_POST['picturewidth']) )
    {
      $str = strip_tags(trim($_POST['picturewidth']));
      if ( !QTisbetween($str,20,200) ) { $error = $L['Max_picture_size'].S.$L['E_invalid'].' (20-200 pixels)'; }
      if ( empty($error) )
      {
      $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$str.'" WHERE param="picture_width"');
      $_SESSION[QT]['picture_width']=$str;
      }
    }
    if ( isset($_POST['pictureheight']) )
    {
      $str = strip_tags(trim($_POST['pictureheight']));
      if ( !QTisbetween($str,20,200) ) { $error = $L['Max_picture_size'].S.$L['E_invalid'].' (20-200 pixels)'; }
      if ( empty($error) )
      {
      $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$str.'" WHERE param="picture_height"');
      $_SESSION[QT]['picture_height']=$str;
      }
    }
    if ( isset($_POST['picturesize']) )
    {
      $str = strip_tags(trim($_POST['picturesize']));
      if ( !QTisbetween($str,10,100) ) { $error = $L['Max_picture_size'].S.$L['E_invalid'].' (10-100 kb)'; }
      if ( empty($error) )
      {
      $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$str.'" WHERE param="picture_size"');
      $_SESSION[QT]['picture_size']=$str;
      }
    }
  }

  $_SESSION[QT]['upload']=$_POST['upload'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_POST['upload'].'" WHERE param="upload"');

  if ( $_SESSION[QT]['upload']!='0' )
  {
    if ( isset($_POST['uploadsize']) )
    {
      $str = strip_tags(trim($_POST['uploadsize']));
      if ( !QTisbetween($str,100,10000) ) { $error = $L['Maximum'].S.$L['E_invalid'].' (100-10000 kb)'; }
      if ( empty($error) )
      {
      $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$str.'" WHERE param="uploadsize"');
      $_SESSION[QT]['uploadsize']=$str;
      }
    }
  }

  $_SESSION[QT]['show_calendar'] = $_POST['show_calendar'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['show_calendar'].'" WHERE param="show_calendar"');
  $_SESSION[QT]['show_stats'] = $_POST['show_stats'];
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['show_stats'].'" WHERE param="show_stats"');

  // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);
}

// --------
// HTML START
// --------

include APP.'_adm_inc_hd.php';

// FORM

echo '
<script type="text/javascript">
function picturedisabled(str)
{
  ctrl1 = document.getElementById("picturewidth");
  ctrl2 = document.getElementById("pictureheight");
  ctrl3 = document.getElementById("picturesize");
  if (str=="0")
  {
  ctrl1.disabled=true;
  ctrl2.disabled=true;
  ctrl3.disabled=true;
  }
  else
  {
  ctrl1.disabled=false; if (ctrl1.value.length==0) { ctrl1.value="100"; }
  ctrl2.disabled=false; if (ctrl2.value.length==0) { ctrl2.value="100"; }
  ctrl3.disabled=false; if (ctrl3.value.length==0) { ctrl3.value="12"; }
  }
  return null;
}
function uploaddisabled(str)
{
  ctrl1 = document.getElementById("uploadsize");
  if (str=="0")
  {
  ctrl1.disabled=true;
  }
  else
  {
  ctrl1.disabled=false; if (ctrl1.value.length==0) { ctrl1.value="500"; }
  }
  return null;
}
function ValidateForm(theForm)
{
  if (!theForm.picturewidth.disabled)
  {
    if (theForm.picturewidth.value.length < 1) { alert(qtHtmldecode("',$L['E_mandatory'],': ',$L['Maximum'],' pixels")); return false; }
  }
  if (!theForm.pictureheight.disabled)
  {
    if (theForm.pictureheight.value.length < 1) { alert(qtHtmldecode("',$L['E_mandatory'],': ',$L['Maximum'],' pixels")); return false; }
  }
  if (!theForm.picturesize.disabled)
  {
    if (theForm.picturesize.value.length < 1) { alert(qtHtmldecode("',$L['E_mandatory'],': ',$L['Maximum'],' Kb")); return false; }
  }
  if (!theForm.uploadsize.disabled)
  {
    if (theForm.uploadsize.value.length < 1) { alert(qtHtmldecode("',$L['E_mandatory'],': ',$L['Maximum'],' Kb")); return false; }
  }
  return null;
}
</script>
';

echo '<form method="post" action="',$oVIP->selfurl,'" onsubmit="return ValidateForm(this);">
<h2 class="subtitle">',$L['Public_access_level'],'</h2>
<table class="t-data">
<tr class="tr" title="',$L['H_Visitors_can'],'">
<td class="headfirst"><label for="pal">',$L['Visitors_can'],'</label></td>
<td><select id="pal" name="pal" onchange="bEdited=true;">',QTasTag($L['Pal'],(int)$_SESSION[QT]['visitor_right']),'</select></td>
</tr>
<tr class="tr" title="',$L['H_Members_can'],'">
<td class="headfirst"><label for="pal">',$L['Members_can'],'</label></td>
<td><select id="mal" name="mal" onchange="bEdited=true;">',QTasTag($L['Mal'],(int)$_SESSION[QT]['member_right']),'</select></td>
</tr>
</table>
';

if ( !isset($_SESSION[QT]['login_addon']) ) $_SESSION[QT]['login_addon']='0';
$str = 'Internal authority (default)';
$arrLoginAddOn=array('0'=>$str);
$arr = GetParam(false,'param LIKE "m_%:login"');
foreach($arr as $param=>$name)
{
  $sPrefix = str_replace(':login','',$param);
  if ( isset($_SESSION[QT][$sPrefix]) && $_SESSION[QT][$sPrefix]!=='0' ) $arrLoginAddOn[$sPrefix] = 'Module '.$name;
}
if ( count($arrLoginAddOn)>1 ) $str = '<select id="login_addon" name="login_addon" onchange="bEdited=true;">'.QTasTag($arrLoginAddOn,$_SESSION[QT]['login_addon']).'</select>';

echo '<h2 class="subtitle">',$L['Authentication'],'</h2>
<table class="t-data">
<tr class="t-data">
<td class="headfirst">',L('Authority'),'</td>
<td>',$str,'</td>
</tr>
<tr class="t-data">
<td class="headfirst"><label for="login_qtf">',$L['Login_qtf'],'</label></td>
<td><input type="text" id="login_qtf" name="login_qtf" size="4" maxlength="4" value="',(empty($_SESSION[QT]['login_qtf']) ? '' : $_SESSION[QT]['login_qtf']),'" onchange="bEdited=true;"/>
 <span class="help">',$L['H_Login_qtf'],'</span> <a class="small" href="qte_adm_secu_help.php" target="_blank">',$L['Help'],'</a></td>
</tr>
<tr class="t-data">
<td class="headfirst"><label for="login_qti">',$L['Login_qti'],'</label></td>
<td><input type="text" id="login_qti" name="login_qti" size="4" maxlength="4" value="',(empty($_SESSION[QT]['login_qti']) ? '' : $_SESSION[QT]['login_qti']),'" onchange="bEdited=true;"/>
 <span class="help">',$L['H_Login_qti'],'</span> <a class="small" href="qte_adm_secu_help.php" target="_blank">',$L['Help'],'</a></td>
</tr>
<tr class="tr tr_o">
<td class="headfirst"><label for="login_qte_web">',$L['Login_qte_web'],'</label></td>
<td><select id="login_qte_web" name="login_qte_web" onchange="bEdited=true;">
<option value="0"',($_SESSION[QT]['login_qte_web']=='0' ? QSEL : ''),'>',$L['N'],'</option>
<option value="1"',($_SESSION[QT]['login_qte_web']=='1' ? QSEL : ''),'>',$L['Y'],'</option>
</select> <span class="help">',$L['H_Login_qte_web'],'</span></td>
</tr>
</table>
';
echo '<h2 class="subtitle">',$L['Registration'],'</h2>
<table class="t-data">
<tr class="t-data" title="',$L['Reg_mode'],'">
<td class="headfirst"><label for="regmode">',$L['Reg_mode'],'</label></td>
<td><select id="regmode" name="regmode" onchange="bEdited=true;">
',QTasTag(array('direct'=>L('Reg_direct'),'email'=>L('Reg_email'),'backoffice'=>L('Reg_backoffice')),$_SESSION[QT]['register_mode']),'
</select>
</tr>
<tr class="tr" title="',$L['H_Reg_security'],'">
<td class="headfirst"><label for="regsafe">',$L['Reg_security'],'</label></td>
<td><select id="regsafe" name="regsafe" onchange="bEdited=true;">
<option value="none"',($_SESSION[QT]['register_safe']=='none' ? QSEL : ''),'>',$L['None'],'</option>
<option value="text"',($_SESSION[QT]['register_safe']=='text' ? QSEL : ''),'>',$L['Text_code'],'</option>
<option value="image"',($_SESSION[QT]['register_safe']=='image' ? QSEL : ''),'>',$L['Image_code'],'</option>
</select></td>
</tr>
<tr class="tr" title="',$L['H_Register_coppa'],'">
<td class="headfirst"><label for="regcoppa">',$L['Register_coppa'],'</label></td>
<td><select id="regcoppa" name="regcoppa" onchange="bEdited=true;">
<option value="0"',($_SESSION[QT]['register_coppa']=='0' ? QSEL : ''),'>',$L['N'],'</option>
<option value="1"',($_SESSION[QT]['register_coppa']=='1' ? QSEL : ''),'>',$L['Y'],'</option>
</select></td>
</tr>
</table>
';
$arr = array(
  'M'=>$L['Y'].' ('.$L['Userrole_M'].')',
  'U'=>$L['Y'].' ('.$L['Userrole_U'].')',
  'V'=>$L['Y'].' ('.$L['Userrole_V'].')');
echo '<h2 class="subtitle">',$L['Security_rules'],'</h2>
<table class="t-data">
<tr class="t-data" title="',$L['H_Allow_picture'],'">
<td class="headfirst"><label for="picture">',$L['Allow_picture'],'</label></td>
<td><select id="picture" name="picture" onchange="picturedisabled(this.value); bEdited=true;">
<option value="0"',($_SESSION[QT]['picture']=='0' ? QSEL : ''),'>',$L['N'],'</option>
<option value="jpg,jpeg"',($_SESSION[QT]['picture']=='jpg,jpeg' ? QSEL : ''),'>',$L['Y'],' (',$L['Jpg_only'],')</option>
<option value="gif,jpg,jpeg,png"'.($_SESSION[QT]['picture']=='gif,jpg,jpeg,png' ? QSEL : '').'>',$L['Y'],' (',$L['Gif_jpg_png'],')</option>
</select> ',$L['Maximum'],' <input type="text" id="picturewidth" name="picturewidth" size="3" maxlength="3" value="',$_SESSION[QT]['picture_width'],'"'.($_SESSION[QT]['picture']=='0' ? QDIS : '').' onchange="bEdited=true;"/> x <input type="text" id="pictureheight" name="pictureheight" size="3" maxlength="3" value="',$_SESSION[QT]['picture_height'],'"'.($_SESSION[QT]['picture']=='0' ? QDIS : '').' onchange="bEdited=true;"/> pixels, <input type="text" id="picturesize" name="picturesize" size="3" maxlength="3" value="',$_SESSION[QT]['picture_size'],'"'.($_SESSION[QT]['picture']=='0' ? QDIS : '').' onchange="bEdited=true;"/>Kb</td>
</tr>
<tr class="t-data" title="',$L['H_Allow_upload'],'">
<td class="headfirst"><label for="upload">',$L['Allow_upload'],'</label></td>
<td><select id="upload" name="upload" onchange="uploaddisabled(this.value); bEdited=true;">
<option value="0"',($_SESSION[QT]['upload']=='0' ? QSEL : ''),'>',$L['N'],'</option>
<option value="1"',($_SESSION[QT]['upload']=='1' ? QSEL : ''),'>',$L['Y'],'</option>
</select> ',$L['Maximum'],' <input type="text" id="uploadsize" name="uploadsize" size="5" maxlength="5" value="',$_SESSION[QT]['uploadsize'],'"'.($_SESSION[QT]['upload']=='0' ? QDIS : '').' onchange="bEdited=true;"/>Kb</td>
</tr>
<tr class="t-data" title="',$L['H_Show_calendar'],'">
<td class="headfirst"><label for="show_calendar">',$L['Show_calendar'],'</label></td>
<td><select id="show_calendar" name="show_calendar" onchange="bEdited=true;">',QTasTag($arr,$_SESSION[QT]['show_calendar']),'</select></td>
</tr>
';
echo '<tr title="',$L['H_Show_statistics'],'">
<td class="headfirst"><label for="show_stats">',$L['Show_statistics'],'</label></td>
<td><select id="show_stats" name="show_stats" onchange="bEdited=true;">',QTasTag($arr,$_SESSION[QT]['show_stats']),'</select></td>
</tr>
</table>
';
echo '<p style="margin:0 0 5px 0;text-align:center"><input type="submit" name="ok" value="',$L['Save'],'"/></p>
</form>
';

// --------
// HTML END
// --------

include APP.'_adm_inc_ft.php';