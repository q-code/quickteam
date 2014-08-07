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
if ( sUser::Role()!='A' ) die($L['R_admin']);

// ---------
// INITIALISE
// ---------

$s = -1; QThttpvar('s','int'); if ( $s<0 ) die('Missing parameters');

include Translate('@_adm.php');

$oVIP->selfurl  = 'qte_adm_section.php?s='.$s;
$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br />'.$L['Section_upd'];
$oVIP->exiturl  = 'qte_adm_sections.php';
$oVIP->exitname = '&laquo; '.$L['Sections'];

$arrStaff = GetUsers('M',-1,'all',0,20); // staff id,username,firstname,lastname (as array)
$bAjax = (count($arrStaff)>10); // use ajax if many staff members
$oSEC = new cSection($s);

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // CHECK MANDATORY VALUE

  $str = trim(strip_tags($_POST['title'])); if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
  $str = QTconv($str,'3',QTE_CONVERT_AMP,false);
  if ( empty($str) ) $error = $L['Title'].S.$L['E_invalid'];

  if ( empty($error) )
  {
    $oSEC->id = $s;
    $oSEC->pid = (int)$_POST['domain'];
    $oSEC->name = $str;
    $oSEC->type = (int)$_POST['type'];
    $oSEC->status = (int)$_POST['status'];
    // with ajax, validate the modname
    if ( $bAjax && isset($_POST['modname']) )
    {
      if ( $_POST['modname']!=$_POST['modnameold'] )
      {
      $arr = FormatUsers($arrStaff,'fl');
      $oSEC->modname = trim($_POST['modname']);
      $oSEC->modid = array_search($oSEC->modname,$arr);
      if ( $oSEC->modid==FALSE || empty($oSEC->modid) ) { $oSEC->modid=1; $oSEC->modname='Admin'; $warning=$L['Section_moderator'].S.$L['E_invalid']; }
      }
    }
    if ( isset($_POST['modid']) )
    {
      if ( $_POST['modid']!=$_POST['modidold'] )
      {
        $arr = FormatUsers($arrStaff,'fl');
        $oSEC->modname = $arr[$_POST['modid']];
        $oSEC->modid = $_POST['modid'];
      }
    }
  }

  // SAVE

  if ( empty($error) )
  {
    // update
    $oDB->Exec('UPDATE '.TABSECTION.' SET domainid='.$oSEC->pid.', title="'.$oSEC->name.'", type="'.$oSEC->type.'", status="'.$oSEC->status.'", modid='.$oSEC->modid.', modname="'.$oSEC->modname.'" WHERE id='.$oSEC->id);

    // translation
    $oDB->Exec('DELETE FROM '.TABLANG.' WHERE (objtype="sec" OR objtype="secdesc") AND objid="s'.$oSEC->id.'"');
    foreach ($_POST as $strKey => $strTranslation)
    {
      if ( substr($strKey,0,1)=='T' )
      {
        if ( !empty($strTranslation) )
        {
        if ( get_magic_quotes_gpc() ) $strTranslation = stripslashes($strTranslation);
        cLang::Add('sec',substr($strKey,1),'s'.$oSEC->id,$strTranslation);
        }
      }
      if ( substr($strKey,0,1)=='D' )
      {
        if ( !empty($strTranslation) )
        {
        if ( get_magic_quotes_gpc() ) $strTranslation = stripslashes($strTranslation);
        cLang::Add('secdesc',substr($strKey,1),'s'.$oSEC->id,$strTranslation);
        }
      }
    }

    // register section lang and description

    $_SESSION['L']['sec'] = cLang::Get('sec',QTiso(),'*');
    $_SESSION['L']['secdesc'] = cLang::Get('secdesc',QTiso(),'*');
    Unset($oVIP->sections);
    Unset($_SESSION[QT]['sys_notifysections']);

    // edit saved
    $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
  }
}

// --------
// HTML START
// --------

$strFile='';
if ( file_exists('document/section/'.$s.'.gif') ) $strFile = $s.'.gif';
if ( file_exists('document/section/'.$s.'.jpg') ) $strFile = $s.'.jpg';
if ( file_exists('document/section/'.$s.'.png') ) $strFile = $s.'.png';
if ( file_exists('document/section/'.$s.'.jpeg') ) $strFile = $s.'.jpeg';
if ( !empty($strFile) ) $str = '<option value="'.$strFile.'"'.(!empty($oSEC->d_logo) ? QSEL : '').'>'.$L['Specific_image'].'</option>';

$oHtml->scripts[] = '<script type="text/javascript">
function ValidateForm(theForm)
{
  if (theForm.title.value.length==0) { alert(qtHtmldecode("'.$L['E_mandatory'].': '.$L['Title'].'")); return false; }
  return null;
}
function switchimage(strId)
{
  var strDefault="'.$_SESSION[QT]['skin_dir'].'/ico_section_'.$oSEC->type.'_'.$oSEC->status.'.gif";
  var strSpecific="document/section/'.$strFile.'";
  document.getElementById(strId).src=(document.getElementById(strId).src.search(strDefault)==-1 ? strDefault : strSpecific);
  return null;
}
</script>
';
if ( $bAjax )
{
$oHtml->scripts_jq[] = '
var e0 = "'.L('No_result').'";
var e1 = "'.L('try_other_lettres').'";
$(function() {
  $( "#modname" ).autocomplete({
    minLength: 1,
    source: function(request, response) {
      $.ajax({
        url: "qte_j_name.php",
        dataType: "json",
        data: { term: request.term, r:"M", e0: e0, e1: e1 },
        success: function(data) { response(data); }
      });
    },
    select: function( event, ui ) {
      $( "#modname" ).val( ui.item.rItem );
      return false;
    }
  })
  .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
    return $( "<li>" )
      .data( "item.autocomplete", item )
      .append( "<a class=\"jvalue\">" + item.rItem + (item.rInfo=="" ? "" : " &nbsp;<span class=\"jinfo\">(" + item.rInfo + ")</span>") + "</a>" )
      .appendTo( ul );
  };
});
';
}

include APP.'_adm_inc_hd.php';

$arrDomains = memGet('sys_domains');
$arrDest = $arrDomains;
Unset($arrDest[$oSEC->pid]);

// FORM

$str = QTconv($oSEC->title,'I');
echo '<form method="post" action="',$oVIP->selfurl,'" onsubmit="return ValidateForm(this);">
<h2 class="subtitle">',$L['Definition'],'</h2>
<table class="t-data">
<tr class="t-data">
<td class="headfirst">Id</td>
<td>',$oSEC->id,'</td>
</tr>
<tr class="t-data">
<td class="headfirst"><label for="title">',$L['Title'],'</label></td>
<td><input required type="text" id="title" name="title" size="55" maxlength="64" value="',$str,'" style="background-color:#FFFF99;" onchange="bEdited=true;"/>',(strstr($str,'&amp;') ?  ' <span class="disabled">'.$oSEC->title.'</span>' : ''),'</td>
</tr>
<tr class="t-data">
<td class="headfirst">',$L['Domain'],'</td>
<td><select name="domain" onchange="bEdited=true;">
<option value="',$oSEC->pid,'"',QSEL,'>',$arrDomains[$oSEC->pid],'</option>',QTasTag($arrDest,'',array('format'=>$L['Move_to'].': %s')),'</select></td>
</tr>
</table>
';
echo '<h2 class="subtitle">',$L['Properties'],'</h2>
<table class="t-data">
<tr class="t-data">
<td class="headfirst"><label for="type">',$L['Type'],'</label></td>
<td><select id="type" name="type" onchange="bEdited=true;">
<option value="0"',($oSEC->type=='0' ? QSEL : ''),'>',$L['Section_type'][0],'</option>
<option value="1"',($oSEC->type=='1' ? QSEL : ''),'>',$L['Section_type'][1],'</option>
</select> ',$L['Status'],' <select id="status" name="status" onchange="bEdited=true;">
<option value="0"',($oSEC->status=='0' ? QSEL : ''),'>',$L['Section_status'][0],'</option>
<option value="1"',($oSEC->status=='1' ? QSEL : ''),'>',$L['Section_status'][1],'</option>
</select></td>
</tr>
';
if ( $bAjax )
{
echo '<tr class="tr">
<td class="headfirst">',$L['Section_moderator'],'</td>
<td><input type="hidden" name="modnameold" value="',$oSEC->modname,'"/>
<input type="text" name="modname" id="modname" value="',$oSEC->modname,'" size="32" maxlength="32" onchange="bEdited=true;"/>
</td>
</tr>
';
}
else
{
echo '<tr class="tr">
<td class="headfirst">',$L['Section_moderator'],'</td>
<td>
<input type="hidden" name="modidold" value="',$oSEC->modid,'"/>
<select name="modid" id="modid" onchange="bEdited=true;">',QTasTag(FormatUsers($arrStaff,'flu'),$oSEC->modid,array('current'=>$oSEC->modid,'classC'=>'bold')),'</select>
</td>
</tr>
';
}

$strFields='';
foreach(cField::ArrayFieldnames($oSEC->forder) as $str) $strFields.='<span class="colname">'.$str.'</span> ';
echo '<tr class="tr">
<td class="headfirst">',$L['Columns'],'</td>
<td>',$strFields,' &middot; <a href="qte_adm_section_col.php?s=',$oSEC->id,'" onclick="return qtEdited(bEdited,\''.$L['E_editing'].'\');">',$L['Edit'],'</a></td>
</tr>
';

$str='';
$strFile='';
if ( file_exists('document/section/'.$s.'.gif') ) $strFile = $s.'.gif';
if ( file_exists('document/section/'.$s.'.jpg') ) $strFile = $s.'.jpg';
if ( file_exists('document/section/'.$s.'.png') ) $strFile = $s.'.png';
if ( file_exists('document/section/'.$s.'.jpeg') ) $strFile = $s.'.jpeg';
if ( !empty($strFile) ) $str = '<option value="'.$strFile.'"'.(!empty($oSEC->d_logo) ? QSEL : '').'>'.$L['Specific_image'].'</option>';

echo '<tr class="tr">
<td class="headfirst">',$L['Picture'],'</td>
<td>',AsImg($oSEC->GetLogo(),'S',$L['Ico_section_'.$oSEC->type.'_'.$oSEC->status],'ico i-sec','vertical-align:middle','','idlogo'),'&nbsp; <a class="small" href="qte_adm_section_img.php?id=',$s,'">',$L['Add'],'/',$L['Remove'],'</a>
</td>
</tr>
</table>
';

echo '<h2 class="subtitle">',$L['Translations'],'</h2>

<table class="t-data">
<tr class="t-data">
<td class="headfirst">',$L['Title'],'</td>
<td><p style="margin:4px">',sprintf($L['E_no_translation'],$oSEC->title),'</p>
<table>';
$arrTrans = cLang::Get('sec','*','s'.$oSEC->id);
$arrDescTrans = cLang::Get('secdesc','*','s'.$oSEC->id);
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
echo '<p class="submit"><input type="hidden" name="s" value="',$oSEC->id,'"/><input type="submit" name="ok" value="',$L['Save'],'"/>
</p>
</form>
';
echo '<p><a href="',$oVIP->exiturl,'" onclick="return qtEdited(bEdited,\''.$L['E_editing'].'\');">',$oVIP->exitname,'</a></p>
';

// --------
// HTML END
// --------

include APP.'_adm_inc_ft.php';