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
if ( !sUser::CanView('M') ) die(Error(12));

// ---------
// INITIALISE
// ---------

include Translate(APP.'_adm.php');

$oVIP->selfurl = 'qte_adm_users_move.php';
$oVIP->selfname = $L['User_man'];
$oVIP->exiturl  = 'qte_adm_sections.php';
$oVIP->exitname = '&laquo; '.$L['Sections'];

$s = -1;
$n = 'lf';
$ps = 0; // section users page
$pa = 0; // all users page

QThttpvar('s n ps pa','int str int int');
$iN = memGet('sys_members');

if ( $s<0 ) die('Wrong id in '.$oVIP->selfurl);
// name display: l=lastname, f=firstnamen, u=username
// ex: lf, lfu, ulf
$strFirstname = ObjTrans('field','firstname');
$strLastname = ObjTrans('field','lastname');
$strUsername = ObjTrans('field','username');
$arrN = array('lf'=>"$strLastname $strFirstname",'lfu'=>"$strLastname $strFirstname ($strUsername)",'fl'=>"$strFirstname $strLastname",'flu'=>"$strFirstname $strLastname ($strUsername)",'u'=>"$strUsername",'ulf'=>"$strUsername ($strLastname $strFirstname)",'ufl'=>"$strUsername ($strFirstname $strLastname)");
if ( !array_key_exists($n,$arrN) ) die('Wrong parameter n');

$showsection = 'all'; if ( isset($_POST['showsection']) ) $showsection =$_POST['showsection'];
$showusers = ($iN>25 ? 'lost' : 'all'); if ( isset($_POST['showusers']) ) $showusers = $_POST['showusers'];
$showsystemstatus = 'all'; if ( isset($_POST['showsystemstatus']) ) $showsystemstatus = $_POST['showsystemstatus'];
$showsectionstatus = 'all'; if ( isset($_POST['showsectionstatus']) ) $showsectionstatus = $_POST['showsectionstatus'];

$strMoveInfo = '';

$oSEC = new cSection($s);
if ( $oSEC->status==1 && sUser::Role()==='M' ) die(Error(13));

// --------
// SUBMITTED
// --------

if ( isset($_POST['add']) && isset($_POST['sec_add']) )
{
  $i = 0;
  foreach($_POST['sec_add'] as $intKey)
  {
  if ( cItem::InSection($s,'add',$intKey) ) ++$i;
  }
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.sprintf(L('Added_member',$i),$oSEC->name) : 'E|'.$error);
  $oSEC->stats = cSection::UpdateStats($s);
  $oSEC->ReadStats();
}
if ( isset($_POST['rem']) && isset($_POST['sec_del']) )
{
  $i = 0; // users successfully removed
  $j = 0; // users unsuccessfully removed (because users without teams remains in section 0)
  foreach($_POST['sec_del'] as $intKey)
  {
  if ( cItem::InSection($s,'rem',$intKey) ) { ++$i; } else { ++$j; }
  }
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.sprintf(L('Removed_member',$i),$oSEC->name) : 'E|'.$error);
  $oSEC->stats = cSection::UpdateStats($s);
  $oSEC->ReadStats();
}

// --------
// LISTS
// --------

// initialize pager
$intMax = 100; //size of the list (and step size for the pager)
$intSMin = $ps*$intMax;
$intAMin = $pa*$intMax;

// list users (section)
$arr = array();
switch($showsection)
{
case 'role_A': $arr = GetUsers('A',$s,$showsectionstatus,$intSMin,$intMax); break;
case 'role_M': $arr = GetUsers('M',$s,$showsectionstatus,$intSMin,$intMax); break; // admin+coordinators
case 'role_U': $arr = GetUsers('U',$s,$showsectionstatus,$intSMin,$intMax); break;
case 'all': $arr = GetUsers('all',$s,$showsectionstatus,$intSMin,$intMax); break;
default : die('not yet developped');
}

$arrShowsection = FormatUsers($arr,$n,true); // format the result according to $n (and add the status)

// list users (all/filtered)

$arr = array();
switch($showusers)
{
case 'all': $arr = GetUsers('all',-1,$showsystemstatus,$intAMin,$intMax); break;
case 'role_A': $arr = GetUsers('A',-1,$showsystemstatus,$intAMin,$intMax); break;
case 'role_M': $arr = GetUsers('M',-1,$showsystemstatus,$intAMin,$intMax); break;
case 'role_U': $arr = GetUsers('U',-1,$showsystemstatus,$intAMin,$intMax); break;
case 'lost': $arr = GetUsers('lost',-1,$showsystemstatus,$intAMin,$intMax); break;
default :
  if ( is_numeric($showusers) ) {
  if ( $showusers>=0 ) {
  	$showusers = (int)$showusers;
    $arr = GetUsers('all',$showusers,$showsystemstatus,$intAMin,$intMax);
  }}
  break;
}

$arrShowusers = FormatUsers($arr,$n,true); // format the result according to $n (and add the status)

// list sections

$arr = GetSectionTitles('A',-1,$s); // list of other teams (attention, key id must be string)
$arrSections = array();
foreach ($arr as $intKey => $strValue) $arrSections['S'.$intKey]=$strValue;

// --------
// HTML START
// --------

$strStatuses='';
foreach(memGet('sys_statuses') as $key=>$arr) $strStatuses .= 'var status_'.$key.'="'.(isset($arr['statusname']) ? $arr['statusname'] : 'null' ).'"; var icon_'.$key.'="'.(isset($arr['icon']) ? $arr['icon'] : 'null' ).'";'.PHP_EOL;

$oHtml->scripts_jq[] = '
$(function() {
  $("#sec_del,#sec_add").change(function() {
    $.post("qte_j_user.php",
     {id:this.value,dir:"'.QTE_DIR_PIC.'",link:"'.L('Profile').'"},
     function(data)
     {
     if ( data.length>0 ) document.getElementById("title_err").innerHTML=addstatus(data);
     }
    );
  });
});
';
$oHtml->scripts[] = '<script type="text/javascript">
function addstatus(data)
{
var i = data.lastIndexOf("<status ");
if ( i<0 ) return data;
var status = data.substr(i+8,1);
'.$strStatuses.'
return data.replace("<status "+status+"/>", "<p class=\"small\"><img src=\"'.$_SESSION[QT]['skin_dir'].'/" + eval("icon_"+status) + "\" alt=\"" + status + "\" style=\"vertical-align:middle\" /> " + eval("status_"+status) + "</p>");
}
function filtersection_chg()
{
  var doc = document;
  if ( doc.getElementById("showsectionstatus") ) doc.getElementById("showsectionstatus").value="all";
  doc.getElementById("filtersection").click();
}
function filterusers_chg()
{
  var doc = document;
  if ( doc.getElementById("showsystemstatus") ) doc.getElementById("showsystemstatus").value="all";
  doc.getElementById("filtersection").click();
}
function page_click(id,p)
{
  var doc = document;
  if ( doc.getElementById(id) ) doc.getElementById(id).value=p;
  doc.getElementById("filtersection").click();
}
</script>
';

include APP.'_adm_inc_hd.php';

$oSEC->descr .= (empty($oSEC->descr) ? '' : '<br />' ).'<span class="small">('.L('User',$oSEC->members).')</span>';
echo $oSEC->ShowInfo('sectioninfo-left compact','sectioninfo compact','sectiondesc compact'),PHP_EOL;

// -- DISPLAY --

echo '
<form method="post" action="',$oVIP->selfurl,'?s=',$s,'">
<input type="hidden" id="s" name="s" value="',$s,'"/>
<input type="hidden" id="ps" name="ps" value="',$ps,'"/>
<input type="hidden" id="pa" name="pa" value="',$pa,'"/>
<p class="right clear" style="margin:2px 0 5px 0">',$L['Display_order'],': <select class="small" id="n" name="n" onchange="document.getElementById(\'nameformat\').click();">',QTasTag($arrN,$n),'</select><input type="submit" id="nameformat" name="nameformat" value="',$L['Ok'],'" class="small"/></p>
<script type="text/javascript">document.getElementById("nameformat").style.display="none";</script>

<table class="usersmove">
<tr style="vertical-align:top">
<th class="main">',$L['Section_members'],'</th>
<th class="reg">',$L['Registered_members'],'</th>
<th class="info">Info</th>
</tr>
<tr>
';

// COL 1: Section filter (if users>10)

echo '<td class="main">';
echo '<span class="small">',$L['Show'],':</span><br />';
echo '<select name="showsection" id="showsection" size="1" onchange="filtersection_chg();">';
echo '<option value="all"',($showsection=='all' ? QSEL : ''),'>',$L['Users_section'],'</option>';
if ( $oSEC->members>10 )
{
echo '<optgroup label="'.L('System_role').'">';
echo '<option value="role_M"',($showsection=='role_M' ? QSEL : ''),'>',$L['Userrole_Ms'],'</option>';
echo '<optgroup>';
}
echo '</select>';
if ( $oSEC->members>10 && (count($arrShowsection)>10 || $showsectionstatus!=='all') )
{
  // list of statuses as [key - status]
  $str = '<option value="all"'.($showsectionstatus==='all' ? QSEL : '').'>&nbsp;&nbsp;&nbsp;('.L('all').')</option>'.PHP_EOL;
  foreach(memGet('sys_statuses') as $key=>$arrStatus)
  {
  $str .= '<option value="'.$key.'" style="padding-right:5px;background:#ffffff url('.$_SESSION[QT]['skin_dir'].'/'.cVIP::GetStatusIconFile($key).') no-repeat right top"'.($showsectionstatus===$key ? QSEL : '').'>'.$key.'&nbsp;&nbsp;'.$arrStatus['statusname'].'</option>'.PHP_EOL;
  }
  // show selector
  echo '<select name="showsectionstatus" id="showsectionstatus" size="1" onchange="document.getElementById(\'filtermembers\').click();">',PHP_EOL;
  echo $str;
  echo '</select>',PHP_EOL;
}
echo '<input type="submit" id="filtersection" name="filtersection" value="',$L['Ok'],'" class="small"/>
<script type="text/javascript">document.getElementById("filtersection").style.display="none";</script>
<br /><br />',PHP_EOL;
echo '<select name="sec_del[]" id="sec_del" size="15" multiple="multiple">';
foreach($arrShowsection as $key=>$str)
{
	$arr = explode(';',$str); if ( !isset($arr[1]) ) $arr[1]='0';
	if ( empty($arr[0]) ) $arr[0] = '('.L('unknown').')';
	echo '<option value="'.$key.'" style="height:19px;background:#ffffff url('.$_SESSION[QT]['skin_dir'].'/'.cVIP::GetStatusIconFile($arr[1]).') no-repeat right top">'.$arr[0].'</option>'.PHP_EOL;
}
if ( count($arrShowsection)==0 ) echo '<option value="" disabled="disabled">',L('None'),'</option>';
echo '</select>',PHP_EOL;

	// pager
	$str = '';
	if ( $ps>0 ) $str = '<a href="javascript:void(0);" class="small" onclick="page_click(\'ps\',\''.($ps-1).'\');">'.L('previous').'</a>';
	if ( count($arrShowsection)>=$intMax ) $str .= (empty($str) ? '' : ' | ').'<a href="javascript:void(0);" class="small" onclick="page_click(\'ps\',\''.($ps+1).'\');">'.L('next').'</a>';
	echo '<p class="pager">',(empty($str) ? '&nbsp;' : $str.' '.$intMax),'</p>',PHP_EOL;

if ( count($arrShowsection)>0 ) echo '<input type="submit" id="rem" name="rem" value="',$L['User_section_del'],' &gt;" style="width:220px"/><br /><br />';
if ( $oSEC->members==0 || $s==0 )
{
echo '<span class="disabled">'.$L['Members_moveall'].'...</span>',PHP_EOL;
}
else
{
echo '<a href="qte_change.php?a=moveallmembers&amp;s='.$s.'&amp;exit2" class="small">'.$L['Members_moveall'].'...</a>',PHP_EOL;
}
echo '</td>',PHP_EOL;

// COL 2: All users

echo '<td class="reg">',PHP_EOL;

echo '<span class="small">',$L['Show'],':</span><br />',PHP_EOL;
echo '<select name="showusers" id="showusers" size="1" onchange="filterusers_chg();">',PHP_EOL;
if ( $iN>10 )
{
echo '<optgroup label="'.L('System').'">',PHP_EOL;
echo '<option value="all"',($showusers==='all' ? QSEL : ''),'>',$L['Users_reg'],'</option>';
echo '<option value="lost"',($showusers==='lost' ? QSEL : ''),'>',$L['Users_not_in_team'],'</option>',PHP_EOL;
echo '<option value="role_M"',($showusers==='role_M' ? QSEL : ''),'>',$L['Userrole_Ms'],'</option>',PHP_EOL;
echo '</optgroup>',PHP_EOL;
echo Sectionlist($showusers,$s);
}
else
{
echo '<option value="all"',($showusers==='all' ? QSEL : ''),'>',$L['Users_reg'],'</option>';
echo '<option value="lost"',($showusers==='lost' ? QSEL : ''),'>',$L['Users_not_in_team'],'</option>',PHP_EOL;
}
echo '</select>',PHP_EOL;
if ( $iN>10 && (count($arrShowusers)>10 || $showsystemstatus!=='all') )
{
  // list of statuses as [key - status]
  $str = '<option value="all"'.($showsystemstatus==='all' ? QSEL : '').'>&nbsp;&nbsp;&nbsp;('.L('all').')</option>'.PHP_EOL;
  foreach(memGet('sys_statuses') as $key=>$arrStatus)
  {
  $str .= '<option value="'.$key.'" style="padding-right:5px;background:#ffffff url('.$_SESSION[QT]['skin_dir'].'/'.cVIP::GetStatusIconFile($key).') no-repeat right top"'.($showsystemstatus===$key ? QSEL : '').'>'.$key.'&nbsp;&nbsp;'.$arrStatus['statusname'].'</option>'.PHP_EOL;
  }
  // show selector
  echo '<select name="showsystemstatus" id="showsystemstatus" size="1" onchange="document.getElementById(\'filtermembers\').click();">';
  echo $str;
  echo '</select>';
}
echo '<input type="submit" id="filtermembers" name="filtermembers" value="',$L['Ok'],'" class="small"/>
<script type="text/javascript">document.getElementById("filtermembers").style.display="none";</script>
<br /><br />',PHP_EOL;
echo '<select name="sec_add[]" id="sec_add" size="15" multiple="multiple">';
foreach($arrShowusers as $key=>$str)
{
	$arr = explode(';',$str); if ( !isset($arr[1]) ) $arr[1]='0';
	if ( empty($arr[0]) ) $arr[0] = '('.L('unknown').')';
	echo '<option value="'.$key.'" style="height:19px;background:#ffffff url('.$_SESSION[QT]['skin_dir'].'/'.cVIP::GetStatusIconFile($arr[1]).') no-repeat right top">'.$arr[0].'</option>'.PHP_EOL;
}
if ( count($arrShowusers)==0 ) echo '<option value="" disabled="disabled">',L('None'),'</option>';
echo '</select>',PHP_EOL;

	// pager
	$str = '';
	if ( $ps>0 ) $str = '<a href="javascript:void(0);" class="small" onclick="page_click(\'ps\',\''.($ps-1).'\');">'.L('previous').'</a>';
	if ( count($arrShowusers)>=$intMax ) $str .= (empty($str) ? '' : ' | ').'<a href="javascript:void(0);" class="small" onclick="page_click(\'ps\',\''.($ps+1).'\');">'.L('next').'</a>';
	echo '<p class="pager">',(empty($str) ? '&nbsp;' : $str.' '.$intMax),'</p>',PHP_EOL;

if ( count($arrShowusers)>0 ) echo '<input type="submit" id="add" name="add" value="&lt; ',$L['User_section_add'],'" style="width:220px"/><br /><br />',PHP_EOL;
echo '</td>',PHP_EOL;

// COL 3: Preview/Info

echo '<td class="info">';

  // DISPLAY Preview
  echo '<p style="font-weight:bold;color:#555555">',$L['Selected_user'],'</p>';
  echo '<script type="text/javascript"></script><noscript class="small">No preview...<br />Your browser does not support JavaScript</noscript>';
  echo '<p id="title_err"></p>',PHP_EOL;

echo '</td>',PHP_EOL;

// END COL

echo '</tr>
</table>
</form>
';

if ( $s==0 ) echo '<p class="small">'.$L['H_Moving_0'].'</p>';

// --------
// HTML END
// --------

if ( !$bShowtoc ) { $oVIP->exiturl='qte_index.php'; $oVIP->exitname=ObjTrans('index','i'); }

echo '<p><a href="',$oVIP->exiturl,'">',$oVIP->exitname,'</a> &middot; ',$L['Goto'],' <a href="qte_section.php?s=',$oSEC->id,'" target="_top">',$oSEC->name,'</a></p>';

include APP.'_adm_inc_ft.php';