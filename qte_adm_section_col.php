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
// FUNCTION
// ---------

function Changelist($arr,$action,$value='N')
{
  if ( !is_array($arr) ) die('FieldSort: arg #1 must be an array');
  if ( !is_string($action) ) die('FieldSort: arg #2 must be a string');
  if ( !is_string($value) ) die('FieldSort: arg #3 must be a string');

  switch ($action)
  {
  case 'add':
    if ( $value!='N' )
    {
    $arr[]=$value;
    $arr = array_unique($arr);
    }
    break;
  case 'del':
    if ( in_array($value,$arr) )
    {
      $keysrc = array_search($value,$arr);
      unset($arr[$keysrc]);
    }
    break;
  case 'left':
    if ( in_array($value,$arr) )
    {
      $keysrc = array_search($value,$arr);
      $valsrc = $arr[$keysrc];
      if ( $keysrc>0 )
      {
        $keydest = $keysrc-1;
        $valdest = $arr[$keydest];
        $arr[$keydest] = $valsrc;
        $arr[$keysrc] = $valdest;
      }
    }
    break;
  case 'right':
    if ( in_array($value,$arr) )
    {
      $keysrc = array_search($value,$arr);
      $valsrc = $arr[$keysrc];
      if ( $keysrc<count($arr)-1 )
      {
        $keydest = $keysrc+1;
        $valdest = $arr[$keydest];
        $arr[$keydest] = $valsrc;
        $arr[$keysrc] = $valdest;
      }
    }
    break;
  case 'first':
    if ( in_array($value,$arr) )
    {
      $keysrc = array_search($value,$arr);
      $valsrc = $arr[$keysrc];
      if ( $keysrc>0 )
      {
        $keydest = 0;
        $valdest = $arr[$keydest];
        $arr[$keydest] = $valsrc;
        $arr[$keysrc] = $valdest;
      }
    }
    break;
  case 'last':
    if ( in_array($value,$arr) )
    {
      $keysrc = array_search($value,$arr);
      $valsrc = $arr[$keysrc];
      if ( $keysrc<count($arr)-1 )
      {
        $keydest = count($arr)-1;
        $valdest = $arr[$keydest];
        $arr[$keydest] = $valsrc;
        $arr[$keysrc] = $valdest;
      }
    }
    break;
  }
  ksort($arr);
  Return $arr;
}

// ---------
// INITIALISE
// ---------

$s = -1; QThttpvar('s','int',true,true,false); if ( $s<0 ) die('missing section id...');

include Translate(APP.'_adm.php');

$oSEC = new cSection($s);
$oItem = new cItem(1);

$oVIP->selfurl = 'qte_adm_section_col.php';
$oVIP->selfname = $L['Section_upd'];
$oVIP->exiturl = 'qte_adm_section.php?s='.$s;
$oVIP->exitname = '&laquo; '.$L['Section'];

// --------
// SUBMITTED
// --------

if ( isset($_GET['a']) )
{
  switch($_GET['a'])
  {
  case $L['Add']:
    if ( substr($_GET['v'],0,1)!='-' )
    {
    if ( in_array($_GET['v'],$_SESSION['fields']) ) $error = $L['E_already_used'];
    if ( count($_SESSION['fields'])>=10 ) $error = $L['E_max_10'];
    if ( empty($error) )
    {
      $_SESSION['fields'] = Changelist($_SESSION['fields'],'add',$_GET['v']);
      if ( count($_SESSION['fields'])>6 ) $error = $L['E_more_than_5'];
    }
    }
    break;
  case 'del': $_SESSION['fields'] = Changelist($_SESSION['fields'],'del',$_GET['v']); unset($_SESSION['values']); break;
  case 'left': $_SESSION['fields'] = Changelist($_SESSION['fields'],'left',$_GET['v']); break;
  case 'right': $_SESSION['fields'] = Changelist($_SESSION['fields'],'right',$_GET['v']); break;
  case 'default': $_SESSION['fields'] = array('status_i','fullname','phones','emails_i','picture'); break;
  }
}

if ( isset($_GET['ok']) )
{
  $oSEC->forder = implode(';',$_SESSION['fields']);
  $oDB->Exec('UPDATE '.TABSECTION.' SET forder="'.$oSEC->forder.'" WHERE id='.$s);
  unset($_SESSION['fields']);

  // exit
  $_SESSION['pagedialog'] = (empty($error) ? 'O|'.$L['S_save'] : 'E|'.$error);
  $oHtml->Redirect($oVIP->exiturl);
}

// --------
// HTML START
// --------

if ( !isset($_SESSION['fields']) || !isset($_GET['a']) )
{
  $_SESSION['fields'] = array_keys(cField::ArrayFields($oSEC->forder,false)); // get fields key (including off fields)
}

include APP.'_adm_inc_hd.php';

// get list of fields
$arr = GetFLDnames(GetFLDs('status_i;fullname'));
$str = QTasOption($arr,'',array(),$_SESSION['fields']); // disable already used fields
$str .= '<optgroup label="'.L('Fields_personal').'">'.PHP_EOL;
$arr = GetFLDnames(GetFLDs('title;firstname;midname;lastname;alias;picture;address;phones;emails;emails_i;www;birthdate;age;nationality;sexe'));
$str .= QTasOption($arr,'',array(),$_SESSION['fields']);
$str .= '</optgroup>'.PHP_EOL;
$str .= '<optgroup label="'.L('Fields_team').'">'.PHP_EOL;
$arr = GetFLDnames(GetFLDs('teamid1;teamid2;teamrole1;teamrole2;teamdate1;teamdate2;teamvalue1;teamvalue2;teamflag1;teamflag2;descr'));
$str .= QTasOption($arr,'',array(),$_SESSION['fields']);
$str .= '</optgroup>'.PHP_EOL;
$str .= '<optgroup label="'.L('Fields_system').'">'.PHP_EOL;
$arr = GetFLDnames(GetFLDs('id;username;role;status;children;firstdate'));
$str .= QTasOption($arr,'',array(),$_SESSION['fields']);
$str .= '</optgroup>'.PHP_EOL;

$oSEC->descr .= (empty($oSEC->descr) ? '' : '<br />' ).'<span class="small">('.L('User',$oSEC->members).')</span>';
echo '<div style="height:100px; overflow:hidden;margin-bottom:5px">',$oSEC->ShowInfo('sectioninfo-left','sectioninfo','sectiondesc'),'</div>',PHP_EOL;

echo '<form method="get" action="',$oVIP->selfurl,'">';
echo $L['Columns'].' <select id="v" name="v">',$str,'</select>';
echo '<input type="hidden" name="s" value="',$s,'"/>';
echo '&nbsp;<input type="submit" name="a" value="',$L['Add'],'"/>';
echo ' <a href="',$oVIP->selfurl,'?s=',$s,'&amp;a=default">',L('Fields_default'),'</a>';
echo '<br/ ><br />';

echo '<table  class="t-data">';

// editor
echo '<tr class="tr">';
foreach ($_SESSION['fields'] as $strField)
{
echo '<td class="blanko" style="text-align:center; min-width:40px">
<a href="',$oVIP->selfurl,'?s=',$s,'&amp;a=left&amp;v=',$strField,'"><img src="admin/sort_left.gif" style="border-width:0" alt="&lt;" title="',L('Column_move_left'),'"/></a>
<a href="',$oVIP->selfurl,'?s=',$s,'&amp;a=del&amp;v=',$strField,'"><img src="admin/sort_del.gif" style="border-width:0" alt="x" title="',L('Delete'),'"/></a>
<a href="',$oVIP->selfurl,'?s=',$s,'&amp;a=right&amp;v=',$strField,'"><img src="admin/sort_right.gif" style="border-width:0" alt="&gt;" title="',L('Column_move_right'),'"/></a>
</td>';
}
echo '</tr>',PHP_EOL;

// head
echo '<tr class="tr">';
foreach ($_SESSION['fields'] as $strField)
{
  $strLabel = ObjTrans('field',$strField);
  // exception
  if ( $strField=='emails_i' ) $strLabel=ObjTrans('field','emails');
  if ( $strField=='status_i' ) $strLabel=ObjTrans('field','status');

  echo '<td class="headfirst" style="text-align:center; padding:7px 3px">',$strLabel,'</td>';
}
echo '</tr>',PHP_EOL;

// value sample
$arr = memGet('sys_statuses');
echo '<tr class="tr">';

foreach ($_SESSION['fields'] as $strField)
{
  switch ($strField)
  {
  case 'status_i':
    echo '<td style="text-align:center">',AsImg($_SESSION[QT]['skin_dir'].'/'.$arr[$oItem->status]['icon']),'</td>',PHP_EOL;
    break;
  case 'status':
    echo '<td style="text-align:center">',$arr[$oItem->status]['statusname'],'</td>',PHP_EOL;
    break;
  case 'picture':
    if ( count($_SESSION['fields'])>6 )
    {
    echo '<td style="text-align:center; width:60px">',AsImg((empty($oItem->picture) ? '' : $oItem->picture),'',$oItem->fullname,'memberlistC'),'</td>',PHP_EOL;
    }
    else
    {
    echo '<td style="text-align:center; width:120px">',AsImg((empty($oItem->picture) ? '' : $oItem->picture),'',$oItem->fullname,'memberlistN'),'</td>',PHP_EOL;
    }
    break;
  case 'birthdate':
    echo '<td style="text-align:center">',( empty($oItem->birthdate) ? S : QTdatestr($oItem->birthdate,'$','',false) ),'</td>',PHP_EOL;
    break;
  case 'teamdate1':
    echo '<td style="text-align:center">',QTdatestr('now','$','',false),'</td>',PHP_EOL;
    break;
  case 'teamdate2':
    echo '<td style="text-align:center">',QTdatestr('now','$','',false),'</td>',PHP_EOL;
    break;
  case 'age':
    echo '<td style="text-align:center">',$oItem->age,'</td>',PHP_EOL;
    break;
  case 'role':
    echo '<td style="text-align:center">',$L['Userrole_'.$oItem->role],'</td>',PHP_EOL;
    break;
  case 'username':
    echo '<td style="text-align:center"><a href="" onclick="return false">',$oItem->username,'</a></td>',PHP_EOL;
    break;
  case 'emails':
    echo '<td class="colct small" style="text-align:center">',(empty($oItem->emails) ? S : str_replace(';','<br />',$oItem->emails)),'</td>',PHP_EOL;
    break;
  case 'emails_i':
    echo '<td style="text-align:center">',AsImg($_SESSION[QT]['skin_dir'].'/ico_user_e_1.gif','mail',(empty($oItem->emails) ? '' : $oItem->emails)),'</td>',PHP_EOL;
    break;
  case 'www':
    echo '<td class="colct small" style="text-align:center">',(empty($oItem->www) ? S : DropHttp(str_replace(';','<br />',$oItem->www))),'</td>',PHP_EOL;
    break;
  default:
    echo '<td style="text-align:center">',(empty($oItem->$strField) ? S : str_replace(' ; ','<br />',$oItem->$strField)),'</td>',PHP_EOL;
    break;
  }
}

echo '</tr>
</table>
<p style="margin:0 0 5px 0;text-align:center"><input type="submit" name="ok" value="',$L['Save'],'"/></p>
</form>
';

// --------
// HTML END
// --------

echo '<p><a href="',$oVIP->exiturl,'">',$oVIP->exitname,'</a></p>';

include APP.'_adm_inc_ft.php';