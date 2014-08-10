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
* @copyright  2013 The PHP Group
* @version    3.0 build:20140608
*/

session_start();
require_once 'bin/qte_init.php';
include Translate('@_adm.php');

if ( sUser::Role()!='A' ) die(Error(13));

function arrShift($arr,$oObj,$strDir)
{
  // Shifts an element up/down in the list. Keys are changed into numeric (0..n) except when $oObj is not found or impossible to move
  $arrS = array_values($arr); // Keys are replaced by an integer (0..n)
  $i = array_search($oObj,$arrS); // Search postition of $oObj, false if not found
  if ( $i===FALSE ) return $arr;
  if ( $i==0 && $strDir=='up' ) return $arr;
  if ( $i==(count($arr)-1) && $strDir=='down' ) return $arr;
  $arrO = $arrS;
  $intDir = ($strDir=='up' ? -1 : 1);
  $arrO[$i+$intDir] = $arrS[$i];
  $arrO[$i] = $arrS[$i+$intDir];
  return $arrO;
}

// ---------
// INITIALISE
// ---------

$a='';
$d=-1;
$s=-1;
QThttpvar('a d s','str int int');

$oVIP->selfurl = 'qte_adm_sections.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br/>'.$L['Sections'];

// --------
// SUBMITTED
// --------

// REODER DOMAINS/SECTION (enabled by java drag and drop)

if ( isset($_POST['neworder']) )
{
  $arrO = explode(';',$_POST['neworder']); // format of the domain id is "dom_{i}"
  if ( count($arrO)>1 )
  {
    switch(substr($arrO[0],0,3))
    {
      case 'dom': foreach($arrO as $intKey=>$strId) $oDB->Exec('UPDATE '.TABDOMAIN.' SET vorder='.$intKey.' WHERE id='.substr($strId,4)); break;
      case 'sec': foreach($arrO as $intKey=>$strId) $oDB->Exec('UPDATE '.TABSECTION.' SET vorder='.$intKey.' WHERE id='.substr($strId,4)); break;
      default: die('invalid command');
    }
    memUnset('sys_domains');
    memUnset('sys_sections');
    $_SESSION['pagedialog'] = 'O|'.$L['S_update'];
  }
}

// ADD DOMAIN

if ( isset($_POST['add_dom']) )
{

  $oGP = new cGetPost($_POST['title'],64);
  if ( empty($oGP->e) ) $error = $L['Domain'].'/'.$L['Section'].' '.Error(1);

  if ( empty($error) )
  {
    require_once 'bin/class/qte_class_dom.php';
    cDomain::Create($oGP->e,null); // Set $error in case of db failure
  }
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_insert'] : 'E|'.$error);

}

// ADD SECTION

if ( isset($_POST['add_sec']) )
{
  $oGP = new cGetPost($_POST['title'],64);
  if ( empty($oGP->e) ) $error = $L['Domain'].'/'.$L['Section'].' '.Error(1);

  // Add section
  if ( empty($error) )
  {
    cSection::Create($oGP->e,(int)$_POST['indomain']); // Set $error in case of db failure
  }
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_insert'] : 'E|'.$error);

}

// Move domain/section

if ( !empty($a) )
{
  if ( $a=='d_up' || $a=='d_down' )
  {
    $oDB->Query('SELECT id FROM '.TABDOMAIN.' ORDER BY vorder');
    $arrList = array();
    while($row=$oDB->Getrow()) $arrList[]=intval($row['id']);
    $arrO = array_values(arrShift($arrList,$d,substr($a,2)));
    foreach($arrO as $intKey=>$intId)
    {
    if ( empty($error) ) $oDB->QueryErr('UPDATE '.TABDOMAIN.' SET vorder='.$intKey.' WHERE id='.$intId, $error);
    }
    memUnset('sys_domains');
    memUnset('sys_sections');
  }
  if ( $a=='f_up' || $a=='f_down' )
  {
    $oDB->Query('SELECT id FROM '.TABSECTION.' WHERE domainid='.$d.' ORDER BY vorder');
    $arrList = array();
    while($row=$oDB->Getrow()) $arrList[]=intval($row['id']);
    $arrO = array_values(arrShift($arrList,$s,substr($a,2)));
    foreach($arrO as $intKey=>$intId)
    {
    if ( empty($error) ) $oDB->QueryErr('UPDATE '.TABSECTION.' SET vorder='.$intKey.' WHERE id='.$intId, $error);
    }
    memUnset('sys_sections');
  }
}

// --------
// HTML START
// --------

$arrDomains = GetDomains();
if ( count($arrDomains)>50 ) {
  $warning='You have too much domains. Try to remove unused domains.'; $_SESSION['pagedialog'] = 'W|'.$warning;
}
$arrSections = GetSections('A',-2); // Optimisation: get all sections at once (grouped by domain)
if ( count($arrSections)>100 ) {
  $warning='You have too much sections. Try to remove unused sections.'; $_SESSION['pagedialog'] = 'W|'.$warning;
}

$oHtml->scripts[] = '<script type="text/javascript">
function ValidateForm(theForm)
{
if (theForm.title.value.length==0) { alert(qtHtmldecode("'.$L['Missing'].': '.$L['Domain'].'/'.$L['Section'].'")); return false; }
return null;
}
function ToggleForms()
{
if ( document.getElementById("adddomain").style.display=="none" )
{
document.getElementById("adddomain").style.display="block";
document.getElementById("addsection").style.display="block";
}
else
{
document.getElementById("adddomain").style.display="none";
document.getElementById("addsection").style.display="none";
}
}
function orderbox(b)
{
var doc = document;
doc.getElementById("domorderbox").style.display=(b ? "block" : "none");
}
</script>
';

$oHtml->scripts_jq[] = '
$(function()
{
  // Return a helper with preserved width of cells
  var fixHelper = function(e, ui)
  {
    ui.children().each(function() { $(this).width($(this).width()); });
    return ui;
  };

  $("tbody.sortable").sortable({
    items:"tr",
    handle:"td:first",
    helper: fixHelper,
    axis: "y",
    containment:"parent",
    cursor: "n-resize",
    tolerance:"pointer",
    update: function(e,ui) {
      var arrOrder = ui.item.parent().sortable("toArray");
      document.getElementById("neworder").value=arrOrder.join(";");
      document.getElementById("neworder_save").click();
      }
  }).disableSelection();

  $(document).ready(function(){
    $("#domorder").sortable({
    axis: "y",
    cursor: "n-resize",
    containment: "parent",
    tolerance:"pointer",
    update: function() { var arrOrder = $("#domorder").sortable("toArray"); document.getElementById("neworder").value=arrOrder.join(";"); }
    }).disableSelection();
  });

});
';

if ( isset($_GET['add']) )
{
$oHtml->scripts_end[] = '<script type="text/javascript">ToggleForms();</script>
';
}

include APP.'_adm_inc_hd.php';

echo '
<p style="text-align:right"><a id="toggleforms" href="qte_adm_sections.php" onclick="ToggleForms(); return false;">',$L['Add'],' ',$L['Domain'],'/',$L['Section'],'...</a>
 | <a href="qte_adm_sections_stat.php">',$L['Update_stats'],'...</a></p>
</p>
<div id="adddomain">
<form method="post" action="qte_adm_sections.php" onsubmit="return ValidateForm(this);">
<table>
<tr>
<td style="width:120px;"><label for="domain">',$L['Domain_add'],'</label></td>
<td><input id="domain" name="title" type="text" size="30" maxlength="64"/></td>
<td style="text-align:right"><input id="add_dom" name="add_dom" type="submit" value="',$L['Add'],'"/></td>
</tr>
</table>
</form>
</div>
<div id="addsection">
<form method="post" action="qte_adm_sections.php" onsubmit="return ValidateForm(this);">
<table>
<tr>
<td style="width:120px;"><label for="section">',$L['Section_add'],'</label></td>
<td><input id="section" name="title" type="text" size="30" maxlength="64" class="small"/> <span class="small">',L('in_domain'),'</span> <select name="indomain" size="1" class="small">',QTasTag($arrDomains),'</select></td>
<td style="text-align:right"><input name="add_sec" type="submit" value="',$L['Add'],'"/></td>
</tr>
</table>
</form>
</div>
';
if ( !isset($_POST['title']) ) echo '<script type="text/javascript">ToggleForms();</script>';

echo '
<table class="t-sec">
<tr class="t-sec">
<th class="handler">&nbsp;</th>
<th style="text-align:left" colspan="2">',$L['Domain'],'/',$L['Section'],'</th>
<th>',$L['Userrole_M'],'</th>
<th class="c-action">',$L['Action'],'</th>
<th class="c-move">',$L['Move'],'</th>
<th>',$L['Users'],'</th>
</tr>
';

$i=0;
$bSortableDomains = count($arrDomains)>1;
foreach($arrDomains as $intDomain=>$strDomain)
{
  echo '<tr class="t-sec">',PHP_EOL;
  echo '<td class="colgroup handler">',($bSortableDomains ? '<span class="draghandler" title="'.L('Move').'" onmousedown="orderbox(true); return false;">&nbsp;</span>' : '&nbsp;'),'</td>',PHP_EOL;
  echo '<td class="colgroup" colspan="2">',$strDomain,'</td>',PHP_EOL;
  echo '<td class="colgroup">&nbsp;</td>',PHP_EOL;
  echo '<td class="colgroup c-action"><a class="smalm" href="qte_adm_domain.php?d=',$intDomain,'">',$L['Edit'],'</a>';
  echo ' &middot; ',($intDomain==0 ? '<span class="disabled">'.$L['Delete'].'</span>' : '<a class="small" href="qte_change.php?a=deletedomain&amp;s='.$intDomain.'">'.$L['Delete'].'</a>'),'</td>';
  echo '<td class="colgroup c-move">';
  $strUp = '<img class="ctrl disabled" src="admin/ico_up.gif" alt="up" title="'.L('Up').'"/>';
  $strDw = '<img class="ctrl disabled" src="admin/ico_dw.gif" alt="down" title="'.L('Down').'"/>';
  if ( count($arrDomains)>1 )
  {
    if ( $i>0 ) $strUp = '<a class="popup_ctrl" href="qte_adm_sections.php?d='.$intDomain.'&amp;a=d_up"><img class="ctrl" src="admin/ico_up.gif" alt="up" title="'.L('Up').'"/></a>';
    if ( $i<count($arrDomains)-1 ) $strDw = '<a class="popup_ctrl" href="qte_adm_sections.php?d='.$intDomain.'&amp;a=d_down"><img class="ctrl" src="admin/ico_dw.gif" alt="dw" title="'.L('Down').'"/></a>';
  }
  echo $strUp.'&nbsp;'.$strDw;
  echo '</td>',PHP_EOL;
  echo '<td class="colgroup">&nbsp;</td>',PHP_EOL;
  echo '</tr>',PHP_EOL;

  $i++;
  $j = 0;

  if ( isset($arrSections[$intDomain]) ) {
  if ( count($arrSections[$intDomain])>0 ) {

    $bSortable = count($arrSections[$intDomain])>1;

    echo '<tbody ',($bSortable ? ' class="sortable"' : ''),'>',PHP_EOL;
    foreach($arrSections[$intDomain] as $intSecid=>$arrSection)
    {
      $oSEC = new cSection($arrSection);
      $strUp = '<img class="ctrl disabled" src="admin/ico_up.gif" alt="up" title="'.L('Up').'"/>';
      $strDw = '<img class="ctrl disabled" src="admin/ico_dw.gif" alt="down" title="'.L('Down').'"/>';
      echo '<tr class="t-sec hover" id="sec_'.$oSEC->id.'">';
      echo '<td class="handler">',($bSortable ? '<span class="draghandler" title="'.L('Move').'">&nbsp;</span>' : '&nbsp;'),'</td>',PHP_EOL;
      echo '<td class="c-icon">',AsImg($oSEC->GetIcon(),'S',$L['Ico_section_'.$oSEC->type.'_'.$oSEC->status],'i-sec'),'</td>';
      echo '<td><a class="bold" href="qte_adm_section.php?d=',$intSecid,'&amp;s=',$oSEC->id,'">',$oSEC->name,'</a><br /><span class="small">',$L['Section_type'][$oSEC->type],($oSEC->status=='1' ? '<br><span class="small">('.$L['Section_status'][1].')</span>' : ''),'</span></td>';
      echo '<td>',$oSEC->modname,'</td>';
      echo '<td class="c-action"><a class="small" href="qte_adm_section.php?s=',$oSEC->id,'">',$L['Edit'],'</a>';
      echo ' &middot; ',($intSecid==0 ? '<span class="disabled">'.$L['Delete'].'</span>' : '<a class="small" href="qte_change.php?a=deletesection&amp;s='.$intSecid.'">'.$L['Delete'].'</a>'),'</td>';
      echo '<td class="c-move">';
      if ( count($arrSections[$intDomain])>1 )
      {
        if ( $j>0 ) $strUp = '<a href="qte_adm_sections.php?d='.$intDomain.'&amp;s='.$intSecid.'&amp;a=f_up"><img class="ctrl" src="admin/ico_up.gif" alt="up" title="'.L('Up').'"/></a>';
        if ( $j<count($arrSections[$intDomain])-1 ) $strDw = '<a href="qte_adm_sections.php?d='.$intDomain.'&amp;s='.$intSecid.'&amp;a=f_down"><img class="ctrl" src="admin/ico_dw.gif" alt="dw" title="'.L('Down').'"/></a>';
      }
      echo $strUp.'&nbsp;'.$strDw;
      $j++;
      echo '</td>';
      echo '<td>',$oSEC->members,' &middot; <a class="small" href="qte_adm_users_move.php?s=',$oSEC->id,'">',$L['User_man'],'</a></td>';
      echo '</tr>',PHP_EOL;
    }

  }}

  echo '</tbody>',PHP_EOL;
}

echo '</table>
';

// DOMAIN ORDER TOOL

if ( count($arrDomains)>1 )
{

echo '
<div id="domorderbox">
<p class="top">Reorder domains<br/>(drag and drop to reorder)</p>
<ul id="domorder">
';
foreach($arrDomains as $intDomain=>$strDomain) echo '<li id="dom_'.$intDomain.'" class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>',(strlen($strDomain)>20 ? substr($strDomain,0,19).'...' : $strDomain),'</li>',PHP_EOL;
echo '</ul>
<form id="form_order" method="post" action="qte_adm_sections.php">
<p class="bottom"><input type="hidden" name="neworder" id="neworder" value="" /><input type="submit" id="neworder_save" name="neworder_save" value="',L('Save'),'" /><input type="button" name="neworder_cancel" value="',L('Cancel'),'" onclick="orderbox(false);"/></p>
</form>
</div>
';

}

// HTML END

include APP.'_adm_inc_ft.php';