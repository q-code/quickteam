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
 * @version    3.0 build:20140612
 */

session_start();
require 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die($L['E_admin']);

// ------------

function FindDuplicates($arrValues,$bStrict=true)
{
  // Returns a string containing duplicates OR return false if no duplicate
  if ( !is_array($arrValues) ) die('FindDuplicates requiers an array');
  if ( !$bStrict ) $arrValues = array_map('strtoupper',$arrValues);
  $arrUniques = array_unique($arrValues);
  if ( count($arrValues)==count($arrUniques) ) return false;
  $arr = array();
  $str = '';
  foreach ($arrValues as $strValue)
  {
    if ( !isset($arr[$strValue]) ) { $arr[$strValue]=1; } else { $str .= (empty($str) ? '' : ', ').$strValue; }
  }
  return $str;
}

function SetFields($arrFields)
{
  if ( !is_array($arrFields) ) die('SetFields: Argument #1 must be an array.');

  global $oDB;

  $arrFields = array_unique($arrFields);

  $arrSetFieldsC = array('id','username','pwd','status','status_i','role');
  $arrSetFieldsU = array();
  $arrSetFieldsT = array();

  foreach (array('fullname','age','children','firstdate') as $strField)
  {
    if ( in_array($strField,$arrFields) ) $arrSetFieldsC[] = $strField;
  }
  foreach (array('picture','address','phones','emails','emails_i','www','title','firstname','midname','lastname','alias','birthdate','nationality','sexe') as $strField)
  {
    if ( in_array($strField,$arrFields) ) $arrSetFieldsU[] = $strField;
  }
  foreach (array('teamid1','teamid2','teamrole1','teamrole2','teamdate1','teamdate2','teamvalue1','teamvalue2','teamflag1','teamflag2','descr') as $strField)
  {
    if ( in_array($strField,$arrFields) ) $arrSetFieldsT[] = $strField;
  }

  $_SESSION[QT]['fields_c'] = implode(',',$arrSetFieldsC);
  $_SESSION[QT]['fields_u'] = implode(',',$arrSetFieldsU);
  $_SESSION[QT]['fields_t'] = implode(',',$arrSetFieldsT);

  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['fields_c'].'" WHERE param="fields_c"');
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['fields_u'].'" WHERE param="fields_u"');
  $oDB->Exec('UPDATE '.TABSETTING.' SET setting="'.$_SESSION[QT]['fields_t'].'" WHERE param="fields_t"');
}

// ---------
// INITIALISE
// ---------

include Translate(APP.'_adm.php');

include 'bin/qte_lang.php'; // this creates $arrLang

$strEditLang = QTiso();
if ( isset($_GET['editlang']) ) $strEditLang = $_GET['editlang'];
if ( isset($_POST['editlang']) ) $strEditLang = $_POST['editlang'];

$oVIP->selfurl = 'qte_adm_fields.php';
$oVIP->exiturl = 'qte_adm_fields.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_settings'].'</span><br />'.$L['Fields'];

// --------
// SUBMITTED
// --------

if ( isset($_POST['ok']) )
{
  // about allowed dates (default is '2')
  // '0' = dates in the future area allowed,
  // '1' = reject dates in the future
  // '2' = reject dates in the future (except this year)

  // default settings (translation, format, is checked
  $arrFnewnames = array(
  'id'        => array('id','%s',true),
  'status'    => array('Status','%s',true),
  'status_i'  => array('Status icon','%s',true),
  'username'  => array('Username','%s',true),
  'pwd'       => array('Password','%s',true),
  'role'      => array('Role','%s',true),
  'picture'   => array('Picture','%s',false),
  'address'   => array('Address','%s',false),
  'phones'    => array('Phone','%s',false),
  'emails'    => array('E-mail','%s',false),
  'emails_i'  => array('E-mail icon','%s',false),
  'www'       => array('Website','%s',false),
  'children'  => array('Child','%s',false),
  'title'     => array('Title','%s',false),
  'firstname' => array('Firstname','%s',false),
  'midname'   => array('Middlename','%s',false),
  'lastname'  => array('Lastname','%s',false),
  'alias'     => array('Nickname','%s',false),
  'birthdate' => array('Birthdate','2',false),
  'nationality'=>array('Nationality','%s',false),
  'sexe'      => array('Sexe','M ; V',false),
  'firstdate' => array('Registration','%s',false),
  'age'       => array('Age','%s',false),
  'fullname'  => array('Full name','%s',false),
  'teamid1'   => array('Club id','%s',false),
  'teamid2'   => array('National id','%s',false),
  'teamrole1' => array('Function','%s',false),
  'teamrole2' => array('Level','%s',false),
  'teamdate1' => array('Join date','2',false),
  'teamdate2' => array('Medical date','2',false),
  'teamvalue1'=> array('Percent','%s',false),
  'teamvalue2'=> array('Hits','%s',false),
  'teamflag1' => array('Restriction','Yes ; No',false),
  'teamflag2' => array('Rescue','Yes ; No',false),
  'descr'     => array('Comment','%s',false));
  $arrFrenames = array(); // used to check duplicate names

  // read submitted values
  foreach ($arrFnewnames as $strKey => $arrValues)
  {
    if ( isset($_POST["R_$strKey"]) )
    {
      $str = trim($_POST["R_$strKey"]); if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      if ( empty($str) ) $str = $strKey;
      $arrFnewnames[$strKey][0]=$str;
      $arrFrenames[]=$str;
    }
    if ( isset($_POST["F_$strKey"]) )
    {
      $str = trim($_POST["F_$strKey"]); if ( get_magic_quotes_gpc() ) $str = stripslashes($str);
      if ( empty($str) ) $str='%s';
      if ( substr_count($str, '%')>1 ) $error = 'Maximum one characher % (use &amp;#037; to include the % character in a formula)';
      $arrFnewnames[$strKey][1]=$str;
    }
    if ( isset($_POST[$strKey]) )
    {
      $arrFnewnames[$strKey][2]=true;
    }
  }

  // Check duplicates (strict)
  $str = FindDuplicates($arrFrenames);
  if ( $str ) $error = $L['Duplicate_fieldname'].': '.$str;
  if ( empty($error) )
  {
    // Check duplicate (case insensitive)
    $str = FindDuplicates($arrFrenames,false);
    if ( $str ) $warning = $L['Duplicate_fieldname_possible'].' '.$str;
  }

  // exception for email_icon
  $arrFnewnames['emails_i'][0]=$arrFnewnames['emails'][0].S.strtolower($L['Icon']);
  $arrFnewnames['emails_i'][2]=$arrFnewnames['emails'][2];

  // Save
  if ( empty($error) )
  {
    $oDB->Exec('DELETE FROM '.TABLANG.' WHERE (objtype="field" OR objtype="ffield") AND objlang="'.$strEditLang.'"');
    $arrFields = array();
    foreach ($arrFnewnames as $strKey=>$arrFnewname)
    {
      cLang::Add('field',$strEditLang,$strKey,$arrFnewname[0]);
      cLang::Add('ffield',$strEditLang,$strKey,$arrFnewname[1]);
      if ( $arrFnewname[2] ) $arrFields[] = $strKey;
    }
    SetFields($arrFields);
  }

    // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);

}

// --------
// HTML START
// --------

$_SESSION['L']['field'] = cLang::Get('field',$strEditLang,'*');
$_SESSION['L']['ffield'] = cLang::Get('ffield',$strEditLang,'*');

include APP.'_adm_inc_hd.php';

echo '
<script type="text/javascript">
var bEdited=false;
function SetField(str)
{
	var trid = str.replace("cb_","tr_");
  if ( document.getElementById(trid) ) document.getElementById(trid).className="row"+(document.getElementById(str).checked==true ? "" : " disabled");
}
function qtEdited(bEdited,str)
{
  if ( bEdited )
  {
    if ( !confirm(str) ) return false;
  }
  return true;
}
</script>
';


$arrStr = array();
foreach ($arrLang as $strIso => $arr)
{
  if ( $strIso==$strEditLang ) { $arrStr[] = '<span class="fieldlang">'.$arr[1].'</span>'; } else { $arrStr[] = '<a class="fieldlang" href="'.$oVIP->selfurl.'?editlang='.$strIso.'" onclick="return qtEdited(bEdited,\''.$L['E_data_not_saved'].'\');">'.$arr[1].'</a> '; }
}

echo '
<form method="post" action="',$oVIP->selfurl,'">
';

echo '<table  class="t-data">
<colgroup span="5"><col width="30"></col><col width="90"></col><col width="140"></col><col width="130"></col><col></col></colgroup>
<tr class="tr">
<td class="blanko" colspan="2">&nbsp;</td>
<td class="blanko" style="text-align:left" colspan="3">',implode(' &middot; ',$arrStr),'</td>
</tr>
';
echo '<tr class="tr">
<th style="text-align:left">&nbsp;</th>
<th style="text-align:left">',$L['Fields'],'</th>
<th style="text-align:left">',$L['Name'],'</th>
<th style="text-align:left">',$L['Rename'],'</th>
<th style="text-align:left">',$L['Format'],'</th>
</tr>
';

// Personnal content

echo '<tr class="tr">',PHP_EOL;
echo '<td class="blanko bold" colspan="5">',$L['Fields_personal'],'</td>',PHP_EOL;
echo '</tr>',PHP_EOL;

foreach (array('title','firstname','midname','lastname','alias','address','phones','emails','www','birthdate','nationality','sexe','picture') as $strKey)
{
  if ( strpos($_SESSION[QT]['fields_u'],$strKey)===FALSE ) { $bField=false; } else { $bField=true; }
  $strField = ObjTrans('field',$strKey);
  $strFormat = ObjTrans('ffield',$strKey,false);
  $strFormat = str_replace('&#037;','&amp;037;',$strFormat); if ( $strFormat=='%s' ) $strFormat=' ';
  echo '<tr id="tr_',$strKey,'" class="row',( !$bField ? ' disabled' : ''),'">',PHP_EOL;
  echo '<td class="cbx"><input type="checkbox" id="cb_',$strKey,'" name="',$strKey,'"',(!$bField ? '' : QCHE),' onclick="SetField(this.id)" onchange="bEdited=true;"/></td>',PHP_EOL;
  echo '<td class="key"><label for="',$strKey,'">'.$strKey,'</label></td>',PHP_EOL;
  echo '<td class="val">'.$strField,'</td>',PHP_EOL;
  echo '<td class="val"><input type="input" id="R_',$strKey,'" name="R_',$strKey,'" value="',QTconv($strField,'I'),'" size="20" maxlength="32" onchange="bEdited=true;"/></td>',PHP_EOL;
  if ( $strKey=='birthdate' )
  {
  echo '<td class="val"><select id="F_',$strKey,'" name="F_',$strKey,'">',QTasTag($L['No_future'],$strFormat,array('class'=>'small')),'</select> ';
  }
  elseif ( in_array($strKey,array('title','nationality','sexe')) )
  {
  echo '<td class="val"><input type="input" id="F_',$strKey,'" name="F_',$strKey,'" value="',$strFormat,'" size="30" maxlength="32" onchange="bEdited=true;"/> *';
  }
  else
  {
  echo '<td class="val">&nbsp;';
  }
  if ( $strKey=='picture' && $bField && $_SESSION[QT]['picture']=='0' ) echo '<span class="warning">',$L['Field_no_photo'],'</span>';
  echo '</td>',PHP_EOL;
  echo '</tr>',PHP_EOL;
}

// Computed field

echo '<tr class="tr">',PHP_EOL;
echo '<td class="blanko bold" colspan="5">',$L['Fields_computed'],'</td>',PHP_EOL;
echo '</tr>',PHP_EOL;

foreach (array('fullname','age','children','firstdate') as $strKey)
{
  if ( strpos($_SESSION[QT]['fields_c'],$strKey)===FALSE ) { $bField=false; } else { $bField=true; }
  $strField = ObjTrans('field',$strKey);
  $strFormat = ObjTrans('ffield',$strKey,false);
  $strFormat = str_replace('&#037;','&amp;037;',$strFormat); if ( $strFormat=='%s' ) $strFormat=' ';
  echo '<tr id="tr_',$strKey,'" class="row',( !$bField ? ' disabled' : ''),'">',PHP_EOL;
  echo '<td class="cbx"><input type="checkbox" id="cb_',$strKey,'" name="',$strKey,'"',(!$bField ? '' : QCHE),' onclick="SetField(this.id)" onchange="bEdited=true;"/></td>',PHP_EOL;
  echo '<td class="key"><label for="cb_',$strKey,'">'.$strKey,'</label></td>',PHP_EOL;
  echo '<td class="val">'.$strField,'</td>',PHP_EOL;
  echo '<td class="val"><input type="input" id="R_',$strKey,'" name="R_',$strKey,'" value="',QTconv($strField,'I'),'" size="20" maxlength="32" onchange="bEdited=true;"/></td>',PHP_EOL;
  if ( $strKey=='firstdate' )
  {
  echo '<td class="val"><select id="F_',$strKey,'" name="F_',$strKey,'">',QTasTag($L['No_future'],$strFormat,array('class'=>'small')),'</select> ';
  }
  else
  {
  echo '<td class="val"><input type="input" id="F_',$strKey,'" name="F_',$strKey,'" value="',$strFormat,'" size="30" maxlength="32" onchange="bEdited=true;"/> ';
  }
  echo '</tr>',PHP_EOL;
}

// Team content

echo '<tr class="tr">',PHP_EOL;
echo '<td class="blanko bold" colspan="5">',$L['Fields_team'],'</td>',PHP_EOL;
echo '</tr>',PHP_EOL;

foreach (array('teamid1','teamid2','teamrole1','teamrole2','teamdate1','teamdate2','teamvalue1','teamvalue2','teamflag1','teamflag2','descr') as $strKey)
{
  if ( strpos($_SESSION[QT]['fields_t'],$strKey)===FALSE ) { $bField=false; } else { $bField=true; }
  $strField = ObjTrans('field',$strKey);
  $strFormat = ObjTrans('ffield',$strKey,false);
  $strFormat = str_replace('&#037;','&amp;037;',$strFormat); if ( $strFormat=='%s' ) $strFormat=' ';
  echo '<tr id="tr_',$strKey,'" class="row',( !$bField ? ' disabled' : ''),'">',PHP_EOL;
  echo '<td class="cbx"><input type="checkbox" id="cb_',$strKey,'" name="',$strKey,'"',(!$bField ? '' : QCHE),' onclick="SetField(this.id)" onchange="bEdited=true;"/></td>',PHP_EOL;
  echo '<td class="key">'.$strKey,'</label></td>',PHP_EOL;
  echo '<td class="val">'.$strField,'</td>',PHP_EOL;
  echo '<td class="val"><input type="input" id="R_',$strKey,'" name="R_',$strKey,'" value="',QTconv($strField,'I'),'" size="20" maxlength="32" onchange="bEdited=true;"/></td>',PHP_EOL;
  if ( $strKey=='teamdate1' || $strKey=='teamdate2' )
  {
  echo '<td class="val"><select id="F_',$strKey,'" name="F_',$strKey,'">',QTasTag($L['No_future'],$strFormat,array('class'=>'small')),'</select> ';
  }
  else
  {
  echo '<td class="val"><input type="input" id="F_',$strKey,'" name="F_',$strKey,'" value="',$strFormat,'" size="30" maxlength="32" onchange="bEdited=true;"/> ';
  }
  echo (in_array($strKey,array('teamrole1','teamrole2','teamvalue1','teamvalue2','teamflag1','teamflag2')) ? '*' : ''),'</td>',PHP_EOL;
  echo '</tr>',PHP_EOL;
}

// Mandatory

echo '<tr class="tr">',PHP_EOL;
echo '<td class="blanko bold" colspan="5">',$L['Mandatory'],' (system)</td>',PHP_EOL;
echo '</tr>',PHP_EOL;

foreach (array('username','status') as $strKey)
{
  $strField = ObjTrans('field',$strKey);
  echo '<tr id="tr_',$strKey,'" class="row',( !$bField ? ' disabled' : ''),'">',PHP_EOL;
  echo '<td class="cbx"><input type="checkbox" ',QCHE,' disabled="disabled" onchange="bEdited=true;"/><input type="hidden" id="',$strKey,'" name="',$strKey,'" value="1"/></td>',PHP_EOL;
  echo '<td class="key">'.$strKey,'</td>',PHP_EOL;
  echo '<td class="val">'.$strField,'</td>',PHP_EOL;
  echo '<td class="val"><input type="input" id="R_',$strKey,'" name="R_',$strKey,'" value="',QTconv($strField,'I'),'" size="20" maxlength="32" class="small" onchange="bEdited=true;"/></td>',PHP_EOL;
  echo '<td class="val"><input type="input" id="F_',$strKey,'" name="F_',$strKey,'" value="" size="30" maxlength="32" class="small" onchange="bEdited=true;" style="visibility:hidden"/></td>',PHP_EOL;
  echo '</tr>',PHP_EOL;
}

echo '</table>
<p style="margin:0 0 5px 0;text-align:center"><input type="hidden" name="editlang" value="',$strEditLang,'"><input type="submit" name="ok" value="',$L['Save'],'"/></p>
</form>
';

echo '<p class="small">* ',$L['List_allowed'],'</p>';
echo '<p class="small">',$L['H_semicolon_format'],'</p>';

include APP.'_adm_inc_ft.php';