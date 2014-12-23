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
if ( !sUser::IsStaff() ) die($L['R_staff']);

$s = ''; // section $s can be '*' or [int] (after argument checking only [int] is allowed)
$q = '';
$label = false;
QThttpvar('s q label','str str str');
if ( $s==='*' || $s==='' ) $s=-1;
if ( !is_int($s) ) $s=(int)$s;
if ( $s<0 && empty($q) ) die('Missing argument $s or $q...');
if ( $label=='1' ) $label=true;

// ---------
// INITIALISE
// ---------
// Note: This returns pages of 100 items ($_SESSION[QT]['items_per_page'] replaced by 100)

include Translate(APP.'_email.php');

$strFlds  = ' u.id,u.title,u.firstname,u.midname,u.lastname,u.username,u.alias,u.privacy,u.emails';
$strFrom  = ' FROM '.TABUSER.' u INNER JOIN '.TABS2U.' l ON l.userid=u.id';
$strWhere = ' WHERE u.id>0';
$strGroup = 'all';
$strOrder = 'lastname';
$strDirec = 'ASC';
$intLimit = 0;
$intPage  = 1;

// security check 1
if ( isset($_GET['group']) ) $strGroup = strip_tags($_GET['group']);
if ( isset($_GET['order']) ) $strOrder = strip_tags($_GET['order']);
if ( isset($_GET['dir']) ) $strDirec = strip_tags($_GET['dir']);
if ( isset($_GET['page']) ) $intPage = intval(strip_tags($_GET['page']));
if ( isset($_GET['view']) ) $_SESSION[QT]['viewmode'] = strip_tags($_GET['view']);

// security check 2 (no long argument)
if ( isset($strGroup[4]) ) die('Invalid argument #group');
if ( isset($strOrder[12]) ) die('Invalid argument #order');
if ( isset($strDirec[4]) ) die('Invalid argument #dir');

$intLimit = ($intPage-1)*100;

// check search

if ( $s>=0 )
{
  $oSEC = new cSection($s);

  if ( $oSEC->type==1 && !sUser::IsStaff() )
  {
    $oHtml->PageMsg(NULL,$L['R_staff']);
  }
  if ( $oSEC->type==2 && sUser::Role()==='V' )
  {
    $oHtml->PageMsg(NULL,$L['R_user']);
  }

  $oVIP->selfname = $oSEC->name;
}
elseif ( !empty($q) )
{
  $oSEC = new cSection(); // section is null in case of search query
}
else
{
  die('Missing argument $s or $q...');
}

$oVIP->selfurl = 'qte_email.php';
$oVIP->selfname = L('Emails');

if ( $s>=0 && empty($q) )
{
  $strWhere .= ' AND l.sid='.$s;
  switch ($strGroup)
  {
    case 'all': break;
    case '0': $strWhere .= ' AND '.FirstCharCase('u.lastname','a-z').' AND '.FirstCharCase('u.firstname','a-z'); break;
    default:  $strWhere .= ' AND ('.FirstCharCase('u.lastname','u').'="'.$strGroup.'" OR '.FirstCharCase('u.firstname','u').'="'.$strGroup.'")'; break;
  }
  $strCount = 'SELECT count(*) as countid'.$strFrom.$strWhere;
}
elseif ( !empty($q) )
{
  $oSEC = new cSection(); // section is null in case of search query
  include 'qte_section_qry.php';
}
else
{
  die('Missing argument $s or $q...');
}

$strShowZ   = ''; if ( !$_SESSION[QT]['show_Z'] ) $strShowZ = ' AND u.status<>"Z"';

// COUNT Members

$oDB->Query($strCount);
$row = $oDB->Getrow();
$intCount = (int)$row['countid'];

// --------
// Pager
// --------

$strPager = MakePager(Href().'?'.GetUri('page'),$intCount,100,$intPage);
if ( !empty($strPager) ) { $strPager = $L['Page'].$strPager; } else { $strPager=S; }
if ( $intCount<$oSEC->members ) $strPager = '<span class="small">'.$intCount.' '.$L['Selected_from'].' '.$oSEC->members.' '.strtolower($L['Users']).'</span>'.($strPager==S ? '' : ' | '.$strPager);

// --------
// HTML START Emails prection and java are not used because only staffs can access the page
// --------

$oHtml->scripts = array();

include 'qte_inc_hd.php';

// Display description

if ( !empty($q) )
{
$oSEC->name = $oVIP->selfname;
$oSEC->members = $intCount;
}
$oSEC->ShowInfo('sectioninfo-left','sectioninfo','sectiondesc');

// Display letters bar
if ( empty($q) )
{
if ( $intCount>$_SESSION[QT]['items_per_page'] || isset($_GET['group']) ) echo PHP_EOL,HtmlLettres(Href().'?'.GetUri('group'),$strGroup,L('All'),'lettres clear'),PHP_EOL;
}

// Display no member

if ( $intCount==0 )
{
  $table = new cTable('t1','t-item',$intCount);
  $table->th['void'] = new cTableHead('&nbsp;');
  echo $table->GetEmptyTable('<p style="margin-left:10px;margin-right:10px">'.$L['E_no_member'].'...</p>',true,'','r1');
  if ( $oSEC->members>0 && sUser::IsStaff() && !empty($strShowZ) )
  {
    $oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' u INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.sid='.$oSEC->id.' AND u.status="Z"');
    $row = $oDB->Getrow();
    $i = intval($row);
    $arr = memGet('sys_statuses');
    if ( $i>0 ) echo '<p class="disabled">',$L['Hidden'],': ',strtolower(L('User',$i).' ('.$L['Status'].' '.$arr['Z']['statusname']),')</p>';
  }
  include 'qte_inc_ft.php';
  return;
}

// Display top pager

if ( $strPager )
{
echo '
<p class="pager-zt">',$strPager,'</p>
';
}
else
{
echo '<br />';
}

// --------
// Query members
// ----------

if ( substr($strOrder,0,2)!='u.' ) $strOrder = 'u.'.$strOrder;
$strOrder .= ' '.strtoupper($strDirec);
$strOrder = str_replace('u.fullname','u.lastname',$strOrder);
$strOrder = str_replace('u.status_i','u.status',$strOrder);
$strOrder = str_replace('u.age','u.birthdate',$strOrder);
// second order
if ( !strstr($strOrder,'lastname') ) $strOrder .= ',u.lastname';

$oDB->Query( LimitSQL($strFlds.$strFrom.$strWhere,$strOrder,$intLimit,100) );

$arrUsersEmails = array(); // store id->fullname+emails (if email exists). Support several emails for a user.
$arrEmailsUsers = array(); // store email->fullname <email> (if email exists). This is the DISTINCT emails (email is the key)
                           // To display only the emails, use the array keys (Array values contains the labels+emails)
$i=0;
while ( $row=$oDB->Getrow() )
{
  if ( isset($row['emails'][5]) )
  {
    $oItem = new cItem($row,true);  // privatise
    $arrUsersEmails[] = $oItem->fullname.'&nbsp;&nbsp;<a class="small" href="mailto:'.strip_tags($oItem->emails).'" title="'.$oItem->fullname.'">'.$oItem->emails.'</a>'.(strstr($oItem->privacy,'emails') ? ' <span class="small">('.$L['Hidden'].')</span>' : '');
    $arr = explode(';',strip_tags($oItem->emails));
    foreach($arr as $str) $arrEmailsUsers[trim($str)] = trim($oItem->fullname).' <'.trim($str).'>';
    ++$i;
  }
}

// Display members.

echo '<table class="t-item">
<tr class="t-data">
<th>&nbsp;</th>
<th style="vertical-align:top">',L('Emails'),'</th>
<th style="vertical-align:top">&nbsp;</th>
<th>&nbsp;</td>
</tr>
';
echo '<tr class="t-data">
<td class="colct colfirst">&nbsp;</td>
<td style="vertical-align:top">',sprintf($L['Emails_from'],$i,$intCount),'</td>
<td style="vertical-align:top">',sprintf($L['Emails_dist_from'],count($arrEmailsUsers),$intCount),'</td>
<td class="colct collast">&nbsp;</td>
</tr>
';
echo '<tr class="t-data">
<td class="colct colfirst">&nbsp;</td>
<td style="vertical-align:top">
',implode('<br />',$arrUsersEmails),'
</td>
<td style="vertical-align:top">
<textarea id="allemails" class="small" rows="',(count($arrEmailsUsers)>20 ? '40' : '15'),'" cols="50">',($label ? implode('; ',$arrEmailsUsers) : implode('; ',array_keys($arrEmailsUsers))),'</textarea>
<br />
<form method="get" id="add" action="',Href(),'">
<input type="checkbox" id="label" name="label" value="1" ',($label ? QCHE : ''),'onchange="document.getElementById(\'add\').submit();" /><label for="label">',$L['Emails_label'],'</label>
&nbsp;&middot;&nbsp;<a class="small" href="javascript:void(0)" onclick="selectall();">',L('Select'),'</a>';
foreach($_GET as $key=>$value) if ($key!=='ok' && $key!=='label') echo '<input type="hidden" name="',$key,'" value="',$value,'" />',PHP_EOL;
echo '<input type="submit" id="ok" name="ok" value="',$L['Ok'],'" />
</form>
<script type="text/javascript">document.getElementById("ok").style.display="none";</script>
</td>
<td class="colct collast">&nbsp;</td>
</tr>
</table>
<script type="text/javascript">
function selectall()
{
var d = document.getElementById("allemails");
d.focus();
d.select();
}
</script>
';

// --------
// Display bottom pager
// --------

if ( $strPager )
{
echo '
<p class="pager-zb">',$strPager,'</p>
';
}

echo '<p class="helpbox">',$L['Emails_help'],'</p>
';

// --------
// HTML END
// --------

include 'qte_inc_ft.php';