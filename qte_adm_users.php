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
 * @version    3.0 build:20140612
 */

session_start();
require 'bin/qte_init.php';
if ( sUser::Role()!='A' ) die(Error(13));

include Translate(APP.'_adm.php');
include Translate(APP.'_reg.php');

// ---------
// INITIALISE
// ---------

$strGroups='';
$intIPP=25; //items per page

$oVIP->selfurl = 'qte_adm_users.php';
$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br/>'.$L['Users'];
$oVIP->exiturl = 'qte_adm_users.php';
$oVIP->exitname = '&laquo; '.$L['Users'];

// INITIALISE

$oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' WHERE id>0');
$row = $oDB->Getrow();
$intUsers = (int)$row['countid'];

$oVIP->selfname = '<span class="upper">'.$L['Adm_content'].'</span><br />'.$L['Users'].' ('.$intUsers.')';

$strGroup = 'all';
$intLimit = 0;
$intPage  = 1;
$strOrder = 'lastname';
$strDirec = 'asc';
$strCateg = 'all';
$intChecked = -1; // allow checking an id (-1 means no check)

// security check 1
if ( isset($_GET['group']) ) $strGroup = strip_tags($_GET['group']);
if ( isset($_GET['page']) ) $intPage = intval(strip_tags($_GET['page']));
if ( isset($_GET['order']) ) $strOrder = strip_tags($_GET['order']);
if ( isset($_GET['dir']) ) $strDirec = strtolower(strip_tags($_GET['dir']));
if ( isset($_GET['cat']) ) $strCateg = strip_tags($_GET['cat']);

// security check 2 (no long argument)
if ( strlen($strGroup)>7 ) die('Invalid argument #group');
if ( strlen($strOrder)>12 ) die('Invalid argument #order');
if ( strlen($strDirec)>4 ) die('Invalid argument #dir');

$intLimit = ($intPage-1)*25;

$strDataCommand = L('selection').': <a class="datasetcontrol" onclick="datasetcontrol_click(\'t1_cb[]\',\'users_status\'); return false;" href="#">'.L('status').'</a> &middot; <a class="datasetcontrol" onclick="datasetcontrol_click(\'t1_cb[]\',\'users_role\'); return false;" href="#">'.L('role').'</a> &middot; <a class="datasetcontrol" onclick="datasetcontrol_click(\'t1_cb[]\',\'users_del\'); return false;" href="#">'.L('delete').'</a>
';

// User menu

include 'qte_inc_menu.php';

// Prepare to check the last created user (in case of user added in qte_inc_menu.php or if requested by URI)

if ( isset($_GET['cid']) )  $intChecked = (int)strip_tags($_GET['cid']); // allow checking an id. Note checklast overridres this id
if ( isset($_POST['cid']) ) $intChecked = (int)strip_tags($_POST['cid']);
if ( isset($_POST['checklast']) || isset($_GET['checklast']) )
{
  $oDB->Query('SELECT max(id) as countid FROM '.TABUSER); // Find last id. This overrides the cid value !
  $row = $oDB->Getrow();
  $intChecked = (int)$row['countid'];
}

// --------
// HTML START
// --------

$oHtml->scripts[] = '<script type="text/javascript" src="bin/js/qte_table.js"></script>
<script type="text/javascript">
function datasetcontrol_click(checkboxname,action)
{
  var checkboxes = document.getElementsByName(checkboxname);
  var n = 0;
  for (var i=0; i<checkboxes.length; ++i) if ( checkboxes[i].checked ) ++n;
  if ( n>0 )
  {
  var doc = document.getElementById("form_users");
  doc.form_users_action.value=action;
  doc.submit();
  return;
  }
  else
  {
  alert(qtHtmldecode("'.L('No_selected_row').'"));
  return false;
  }
}
</script>';
$oHtml->scripts_jq[] = '
$(function() {

  $("#t1 td:not(.c-checkbox,.tdname)").click(function() { qtCheckboxToggle(this.parentNode.id.substring(3)); });

  // CHECKBOX ALL ROWS

  $("input[id=\'t1_cb\']").click(function() { qtCheckboxAll("t1_cb","t1_cb[]",true); });

  // SHIFT-CLICK CHECKBOX

  var lastChecked1 = null;
  var lastChecked2 = null;
  $("input[name=\'t1_cb[]\']").click(function(event) {
    if(!lastChecked1)
    {
      lastChecked1 = this;
      qtHighlight("tr_"+this.id,this.checked);
      return;
    }
    if(event.shiftKey)
    {
      var start = $("input[name=\'t1_cb[]\']").index(this);
      var end = $("input[name=\'t1_cb[]\']").index(lastChecked1);
      for(var i=Math.min(start,end);i<=Math.max(start,end);++i)
      {
      $("input[name=\'t1_cb[]\']")[i].checked = lastChecked1.checked;
      qtHighlight("tr_"+$("input[name=\'t1_cb[]\']")[i].id,lastChecked1.checked);
      }
    }
    lastChecked1 = this;
    qtHighlight("tr_"+this.id,this.checked);
  });

});
';

include APP.'_adm_inc_hd.php';

// Add user(s) form / children selector

echo '<p>',(empty($strPageMenu) ? '' : $strPageMenu.' | '),'<a href="qte_adm_users_imp.php">',$L['Users_import_csv'],'...</a>'.( $strCateg=='all' ? ' | <a href="qte_adm_users.php?cat=CH">'.L('Filter_children').'...</a>' : '').'</p>';
if ( !empty($strUserform) ) echo $strUserform;

// --------
// Category subform (children)
// --------

if ( $strCateg!='all' )
{
echo '<h1>',$L['Members_'.$strCateg],' (',$L['H_Members_'.$strCateg],')</h1>',PHP_EOL;
echo '<p><a href="qte_adm_users.php?cat=CH"'.($strCateg=='CH' ? ' onclick="return false;"' : '').'>'.L('Filter_all_children').'</a> | <a href="qte_adm_users.php?cat=SC"'.($strCateg=='SC' ? ' onclick="return false;"': '').'>'.L('Filter_without_agreement').'</a> | <a href="qte_adm_users.php?cat=all">'.L('Filter_remove').'</a></p>',PHP_EOL;
}

// --------
// Button line and pager
// --------

switch($strGroup)
{
  case 'all': $strWhere = ' WHERE id>0'; break;
  case '0': $strWhere = ' WHERE id>0 AND '.FirstCharCase('firstname','a-z').' AND '.FirstCharCase('lastname','a-z'); break;
  default:
    $arr = explode('|',$strGroup);
    $arrOr = array();
    foreach($arr as $str)
    {
    $arrOr[] = FirstCharCase('firstname','u',strlen($str)).'="'.strtoupper($str).'"';
    $arrOr[] = FirstCharCase('lastname','u',strlen($str)).'="'.strtoupper($str).'"';
    }
    $strWhere = ' WHERE id>0 AND ('.implode(' OR ',$arrOr).')';
    break;
}

if ( $strCateg=='CH' ) $strWhere .= ' AND id>1 AND children<>"0"'; //children
if ( $strCateg=='SC' ) $strWhere .= ' AND id>1 AND children="2"';  //sleeping children

$oDB->Query('SELECT count(id) as countid FROM '.TABUSER.$strWhere);
$row = $oDB->Getrow();
$intCount = $row['countid'];

// -- build pager --
$str = MakePager("qte_adm_users.php?cat=$strCateg&group=$strGroup&order=$strOrder&dir=$strDirec",$intCount,$intIPP,$intPage);
$strPager = (empty($str) ? '' : $L['Page'].$str);

if ( $intCount<$intUsers ) $strPager = '<span class="small">'.$intCount.' '.$L['Selected_from'].' '.$intUsers.' '.L('users').'</span>'.(empty($strPager) ? '' : ' | '.$strPager);

// -- Display lettres bar --
if ( $intCount>$intIPP || $strGroup!='all' )
{
  // optimize groups in lettres bar
  if ( $intCount>500 ) { $intChars=1; } else { $intChars=($intCount>$intIPP*2 ? 2 : 3); }
  // lettres bar
  $str = ObjTrans('field','lastname').' '.L('or').' '.strtolower(ObjTrans('field','firstname')).' '.L('starting_with').' ';
  echo PHP_EOL,HtmlLettres(Href().'?'.GetUri('group,page'),$strGroup,L('All'),'lettres clear',$str,$intChars),PHP_EOL;
}

// --------
// Memberlist
// --------

$table = new cTable('t1','t-user',$intCount);

if ( $intCount!=0 )
{
  echo PHP_EOL,'<form id="form_users" method="post" action="qte_change.php"><input type="hidden" id="form_users_action" name="a" value=""/>',PHP_EOL;
  echo '<table class="hidden"><tr><td><p class="pager-zt"><img src="admin/selection_up.gif" style="width:10px;height:10px;vertical-align:bottom;margin:0 10px 0 13px" alt="|" />'.$strDataCommand.'</p></td><td><p class="pager-zt right">',$strPager,'</p></td></tr></table>',PHP_EOL;

  // === TABLE DEFINITION ===
  $table->activecol = $strOrder;
  $table->activelink = '<a  href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order='.$strOrder.'&amp;dir='.($strDirec=='asc' ? 'desc' : 'asc').'">%s</a><img class="i-sort '.$strDirec.'" src="bin/css/null.gif" alt="+"/>';
  $table->th['checkbox'] = new cTableHead(($table->rowcount<2 ? '&nbsp;' : '<input type="checkbox" name="t1_cb_all" id="t1_cb" />'),'','c-checkbox');
  $table->th['status_i']   = new cTableHead('<span title="'.L('Status').'">&bull;</span>','','c-status_i','<a  href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order=status&amp;dir=asc">%s</a>');
  //$table->th['fullname'] = new cTableHead(ObjTrans('field','fullname'),'','c-fullname','<a href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order=fullname&amp;dir=asc">%s</a>');
  $table->th['lastname'] = new cTableHead(ObjTrans('field','lastname'),'','c-lastname','<a href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order=lastname&amp;dir=asc">%s</a>');
  $table->th['firstname'] = new cTableHead(ObjTrans('field','firstname'),'','c-firstname','<a href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order=firstname&amp;dir=asc">%s</a>');
  $table->th['picture']  = new cTableHead('&nbsp;','','c-picture');
  $table->th['role']     = new cTableHead($L['Role'],'','c-role','<a  href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order=role&amp;dir=asc">%s</a>');
  $table->th['firstdate']= new cTableHead($L['Registration'],'','c-firstdate','<a  href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order=firstdate&amp;dir=asc">%s</a>');
  $table->th['username'] = new cTableHead(ObjTrans('field','username'),'','c-username','<a href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order=username&amp;dir=asc">%s</a>');
  //$table->th['id']       = new cTableHead('Id','','','<a  href="'.$oVIP->selfurl.'?cat='.$strCateg.'&amp;group='.$strGroup.'&amp;page=1&amp;order=id&amp;dir=asc">%s</a>');
  // create column data (from headers identifiers) and add class to all
  foreach($table->th as $key=>$th)
  {
    $table->td[$key] = new cTableData();
    $table->td[$key]->Add('class','c-'.$key);
  }

  // === TABLE START DISPLAY ===

  echo PHP_EOL;
  echo $table->Start().PHP_EOL;
  echo '<thead>'.PHP_EOL;
  echo $table->GetTHrow(2).PHP_EOL;
  echo '</thead>'.PHP_EOL;
  echo '<tbody>'.PHP_EOL;

  //-- LIMIT QUERY --
  $strState = 'id,username,status,role,title,firstname,midname,lastname,alias,ip,firstdate,picture FROM '.TABUSER.$strWhere;
  if ( $strOrder=='fullname' ) $strOrder = 'lastname';
  $oDB->Query( LimitSQL($strState,$strOrder.' '.strtoupper($strDirec),$intLimit,$intIPP+20) );
  // --------

  $strAlt='r1';
  $arrRow=array(); // rendered row. To remove duplicate in seach result
  $intRow=0; // count row displayed
  
  while($row=$oDB->Getrow())
  { 
    if ( in_array((int)$row['id'], $arrRow) ) continue; // this remove duplicate users in case of search result
 
    $arrRow[] = (int)$row['id'];
    if ( empty($row['lastname']) ) $row['lastname']='('.L('unknown').')';
    $bChecked = $row['id']==$intChecked;

    // prepare row
    $arr = memGet('sys_statuses');
    $table->row = new cTableRow( 'tr_t1_cb'.$row['id'], 't-item hover rowlight '.$strAlt.($bChecked ? ' checked' : '') );
    $table->td['checkbox']->content = '<input type="checkbox" name="t1_cb[]" id="t1_cb'.$row['id'].'" value="'.$row['id'].'"'.($bChecked ? QCHE : '').'/>'; if ($row['id']<2) $table->td['checkbox']->content = '&nbsp;';
    $table->td['status_i']->content = (isset($arr[$row['status']]) ? AsImg($_SESSION[QT]['skin_dir'].'/'.$arr[$row['status']]['icon'],$row['status'],$arr[$row['status']]['statusname']) : '&nbsp;');
    //$table->td['fullname']->content = cItem::MakeFullname($row['username'],$row['lastname'],$row['midname'],$row['firstname'],$row['title'],$row['alias']);
    $table->td['lastname']->content = '<a href="qte_user.php?id='.$row['id'].'">'.$row['lastname'].'</a>';;
    $table->td['firstname']->content = $row['firstname'];
    $table->td['picture']->content  = ( empty($row['picture']) ? '' : AsImgPopup('usr_'.$row['id'],$qte_root.QTE_DIR_PIC.$row['picture'],'(!)'));
    $table->td['role']->content     = L('Userrole_'.strtoupper($row['role']));
    $table->td['firstdate']->content= (empty($row['firstdate']) ? '&nbsp;' : QTdatestr($row['firstdate'],'Y-m-d',''));
    //$table->td['id']->content       = $row['id'];
    $table->td['username']->content = $row['username'];
    echo $table->GetTDrow().PHP_EOL;
    if ( $strAlt==='r1' ) { $strAlt='r2'; } else { $strAlt='r1'; }
    ++$intRow; if ( $intRow>=$intIPP ) break;

  }

  // === TABLE END DISPLAY ===

  echo '</tbody>',PHP_EOL;
  echo '</table>',PHP_EOL;
  echo '<table class="hidden"><tr>',($intRow>2 ? '<td><p class="pager-zb"><img src="admin/selection_down.gif" style="width:10px;height:10px;vertical-align:top;margin:0 10px 0 13px" alt="|" />'.$strDataCommand.'</p></td>' : ''),'<td><p class="pager-zb right">',$strPager,'</p></td></tr></table>',PHP_EOL;
  echo '</form>',PHP_EOL;

}
else
{
  if ( !empty($strPager) ) echo '<table class="hidden"><tr><td class="pager-zt right">',$strPager,'</td></tr></table>',PHP_EOL;
  $table->th[] = new cTableHead('&nbsp;');
  echo $table->GetEmptyTable('<p style="margin-left:10px;margin-right:10px">'.L('None').'...</p>',true,'','r1');
}

// Define bottom page command (add csv to $intCount (max 10000))

$strCsv ='';
$oVIP->selfuri = GetUri('page');
if ( sUser::Role()!='V' )
{
  if ( $intCount<=$_SESSION[QT]['items_per_page'] )
  {
    $strCsv = '<a class="csv" href="'.Href('qte_adm_users_csv.php').'?'.$oVIP->selfuri.'&amp;n='.$intCount.'" title="'.$L['H_Csv'].'">'.$L['Csv'].'</a>';
  }
  else
  {
    $strCsv = '<a class="csv" href="'.Href('qte_adm_users_csv.php').'?'.$oVIP->selfuri.'&amp;size=p'.$intPage.'&amp;n='.$intCount.'" title="'.$L['H_Csv'].'">'.$L['Csv'].' ('.L('page').')</a>';
    if ( $intCount<=1000 )                   $strCsv .= ' &middot; <a class="csv" href="'.Href('qte_adm_users_csv.php').'?'.$oVIP->selfuri.'&amp;n='.$intCount.'" title="'.$L['H_Csv'].'">'.$L['Csv'].' ('.L('all').')</a>';
    if ( $intCount>1000 && $intCount<=2000 ) $strCsv .= ' &middot; <a class="csv" href="'.Href('qte_adm_users_csv.php').'?'.$oVIP->selfuri.'&amp;size=m1&amp;n='.$intCount.'" title="'.$L['H_Csv'].'">'.$L['Csv'].' (1-1000)</a> &middot; <a class="csv" href="'.Href('qte_adm_users_csv.php').'?'.$oVIP->selfuri.'&amp;size=m2&amp;n='.$intCount.'" title="'.$L['H_Csv'].'">'.$L['Csv'].' (1000-'.$intCount.')</a>';
    if ( $intCount>2000 && $intCount<=5000 ) $strCsv .= ' &middot; <a class="csv" href="'.Href('qte_adm_users_csv.php').'?'.$oVIP->selfuri.'&amp;size=m5&amp;n='.$intCount.'" title="'.$L['H_Csv'].'">'.$L['Csv'].' (1-5000)</a>';
    if ( $intCount>5000 )                    $strCsv .= ' &middot; <a class="csv" href="'.Href('qte_adm_users_csv.php').'?'.$oVIP->selfuri.'&amp;sier=m5&amp;n='.$intCount.'" title="'.$L['H_Csv'].'">'.$L['Csv'].' (1-5000)</a> &middot; < class="csv"a href="'.Href('qte_adm_users_csv.php').'?'.$oVIP->selfuri.'&amp;size=m10&amp;n='.$intCount.'" title="'.$L['H_Csv'].'">'.$L['Csv'].' (5000-10000)</a>';
  }
}
if ( !empty($strCsv) )
{
  echo '<p class="right">',$strCsv,'</p>',PHP_EOL;
}

// --------
// HTML END
// --------

include APP.'_adm_inc_ft.php';