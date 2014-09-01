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

// Check application path

if ( !isset($qte_web_path) ) die('Missing variable $qte_web_path in your web page.');
if ( empty($qte_web_path) ) { $qte_web_path=''; } else { if ( substr($qte_web_path,-1,1)!='/' ) $qte_web_path .= '/';}
$qte_root = $qte_web_path;

// include ini

require $qte_root.'bin/qte_init.php';
if ( !sUser::CanView('V2') ) die(Error(11));

// ---------
// INITIALISE
// ---------

$intLimit = 0;
$strFlds  = ' u.*';
$strFrom  = ' FROM '.TABUSER.' u INNER JOIN '.TABS2U.' l ON l.userid=u.id';
$strWhere = ' WHERE u.id>0';
$strGroup = 'all';
$strOrder = 'lastname';
$strDirec = 'ASC';
$intPage  = 1;

// security check 1
if ( isset($_GET['s']) )     $qte_web_team = strip_tags($_GET['s']);
if ( isset($_GET['group']) ) $strGroup = strip_tags($_GET['group']);
if ( isset($_GET['order']) ) $strOrder = strip_tags($_GET['order']);
if ( isset($_GET['dir']) )   $strDirec = strip_tags($_GET['dir']);
if ( isset($_GET['page']) )  $intPage = intval(strip_tags($_GET['page']));

// security check 2 (no long argument)
if ( isset($strGroup[4]) ) die('Invalid argument #group'); // more than 4 char
if ( isset($strOrder[12]) ) die('Invalid argument #order');
if ( isset($strDirec[4]) ) die('Invalid argument #dir');

// web setting
if ( !isset($qte_web_team) ) die('Missing team id...');
if ( !isset($qte_web_head) ) $qte_web_head = true;
if ( !isset($qte_web_skin) ) $qte_web_skin = 'default';
if ( !isset($qte_web_view) ) $qte_web_view = 'N';
if ( !isset($qte_web_banner) ) $qte_web_banner = true;
if ( !isset($qte_web_logo) ) $qte_web_logo = false;
if ( !isset($qte_web_link) ) $qte_web_link = true;

// view setting
$_SESSION[QT]['viewmode'] = substr(strtoupper($qte_web_view),0,1);
$intLimit = ($intPage-1)*$_SESSION[QT]['items_per_page'];
$strShowZ = ''; if ( !$_SESSION[QT]['show_Z'] ) $strShowZ = ' AND u.status<>"Z"';

$oSEC = new cSection($qte_web_team);

if ( $oSEC->type==1 && !sUser::IsStaff() )
{
  // exit
  $oVIP->selfname = $L['Section'];
  $oVIP->exitname = ObjTrans('index','i');
  $oHtml->PageMsg(NULL,$L['R_staff'],0,'95%','msgboxtitle','msgbox',$qte_root);
}
if ( $oSEC->type==2 && sUser::Role()==='V' )
{
  // exit
  $oVIP->selfname = $L['Section'];
  $oVIP->exitname = ObjTrans('index','i');
  $oHtml->PageMsg(NULL,$L['R_user'],0,'95%','msgboxtitle','msgbox',$qte_root);
}

if ( !empty($qte_web_thispage) ) $oVIP->selfurl = $qte_web_thispage;

// COUNT Members

$strWhereGroup = '';
Switch ($strGroup)
{
  Case 'all': $strWhereGroup .= ' AND u.id>0'; Break;
  Case '0':   $strWhereGroup .= ' AND '.FirstCharCase('u.lastname','a-z'); Break;
  Default:    $strWhereGroup .= ' AND '.FirstCharCase('u.lastname','u').'="'.$strGroup.'"'; Break;
}
$oDB->Query('SELECT count(*) as countid FROM '.TABUSER.' u INNER JOIN '.TABS2U.' l ON l.userid=u.id WHERE l.sid='.$oSEC->id.$strShowZ.$strWhereGroup);
$row = $oDB->Getrow();
$intCount = $row['countid'];

// LETTRES BAR [$strGroups]

if ( $intCount>$_SESSION[QT]['items_per_page'] || isset($_GET['group']) )
{
  if ( $oSEC->members>500 ) { $intChars=1; } else { $intChars=($oSEC->members>((int)$_SESSION[QT]['items_per_page'])*2 ? 2 : 3); }
  $str = ObjTrans('field','lastname').' '.L('or').' '.strtolower(ObjTrans('field','firstname')).' '.L('starting_with').' ';
  $strGroups = HtmlLettres(Href().'?'.GetUri('group,page'),$strGroup,L('All'),'lettres clear',$str,$intChars);
}

// --------
// Pager
// --------

$strPager = MakePager("$oVIP->selfurl?s={$oSEC->id}&group=$strGroup",$intCount,$_SESSION[QT]['items_per_page'],$intPage);
if ( $strPager!='' ) $strPager = $L['Page'].$strPager;

// --------
// HTML START
// --------

if ( $qte_web_head )
{
  $oHtml->links = array(
  		'<link rel="shortcut icon" href="'.$qte_root,'skin/'.$qte_web_skin.'/qte_icon.ico" />',
   		'<link rel="stylesheet" type="text/css" href="'.$qte_root,'skin/'.$qte_web_skin.'/qt_base.css" />',
   		'<link rel="stylesheet" type="text/css" href="'.$qte_root,'skin/'.$qte_web_skin.'/qte_layout.css" />',
  		'<link rel="stylesheet" type="text/css" href="'.$qte_root,'skin/'.$qte_web_skin.'/qte_main.css" />'
  		);
  $oHtml->scripts = array('<script type="text/javascript" src="bin/js/qte_base.js"></script>');
  echo $oHtml->Head();
  echo $oHtml->Body();
}
else
{
  echo '
  <link rel="shortcut icon" href="',$qte_root,'skin/',$qte_web_skin,'/qte_icon.ico"/>
  <link rel="stylesheet" type="text/css" href="',$qte_root,'skin/',$qte_web_skin,'/qt_base.css"/>
  <link rel="stylesheet" type="text/css" href="',$qte_root,'skin/',$qte_web_skin,'/qte_layout.css"/>
  <link rel="stylesheet" type="text/css" href="'.$qte_root,'skin/'.$qte_web_skin.'/qte_main.css" />
  <script type="text/javascript" src="',$qte_root,'bin/js/qte_base.js"></script>
  ';
}

// --------
// Display banner
// --------

if ( $qte_web_banner ) EchoBanner($qte_root.'skin/'.$qte_web_skin.'/qte_logo.gif');

// --------
// Display logo
// --------

if ( $qte_web_logo ) echo '<div>',$oSEC->ShowInfo('sectioninfo-left','sectioninfo','sectiondesc'),'</div>',PHP_EOL;

// --------
// Display letters bar
// --------

if ( $intCount>$_SESSION[QT]['items_per_page'] || isset($_GET['group']) ) echo '<br /><table class="lettres"><tr class="lettres">',$strGroups,'</tr></table><br />',PHP_EOL;

// --------
// Display no members
// --------

if ( $intCount==0 )
{
  echo '<p>',$L['E_no_member'],'</p>';
  if ( $qte_web_head ) echo '</body></html>';
  exit;
}

// --------
// Display top pager
// --------

if ( $strPager )
{
echo '
<table class="hidden"><tr class="hidden"><td class="pager-zt">',$strPager,'</td></tr></table>
';
}

// --------
// Prepare fields
// --------

$arrFLD = GetFLDs($oSEC->forder);

$table = new cTable('t1','t-item',$intCount);

// check current order (if using default)

if ( !array_key_exists($strOrder,$arrFLD) )
{
  $strOrder='fullname'; if ( !array_key_exists($strOrder,$arrFLD) ) $strOrder='username';
}

// === TABLE DEFINITION ===

foreach($arrFLD as $key=>$oField)
{
  // field definition
  if ( !isset($table->th[$key]) ) $table->th[$key] = new cTableHead($oField->name,'','th'.$key,'<a href="'.$oVIP->selfurl.'?page=1&amp;order='.$key.'&amp;dir=asc">%s</a>');
  // exception
  switch ($key)
  {
    case 'status_i': $table->th['status_i']->content = '&bull;'; $table->th['status_i']->link = '<a href="'.$oVIP->selfurl.'?page=1&amp;order=status&amp;dir=asc">%s</a>'; break;
    case 'status':   $table->th['status']->content = '&bull;'; break;
    case 'address':  $table->th['address']->link = ''; break;
    case 'descr':    $table->th['descr']->link = ''; break;
    case 'picture':  $table->th['picture']->link = ''; break;
    case 'phones':   $table->th['phones']->link = ''; break;
    case 'emails':   $table->th['emails']->link = ''; break;
    case 'emails_i': $table->th['emails_i']->link = ''; break;
    case 'coord':    $table->th['coord']->link = ''; break;
    case 'ufield':   $table->th['ufield']->content = L('Search_criteria'); break;
  }
}
// create column data (from headers identifiers) and add class to all
foreach($table->th as $key=>$th) { $table->td[$key] = new cTableData('td','','c-'.$key); }

// === TABLE START DISPLAY ===

echo PHP_EOL;
echo $table->Start().PHP_EOL;
echo '<thead>'.PHP_EOL;
echo $table->GetTHrow(2).PHP_EOL;
echo '</thead>'.PHP_EOL;
echo '<tbody>'.PHP_EOL;

if ( substr($strOrder,0,2)!='u.' ) $strOrder = 'u.'.$strOrder;
$strOrder .= ' '.strtoupper($strDirec);
$strOrder = str_replace('u.fullname','u.lastname',$strOrder);
$strOrder = str_replace('u.status_i','u.status',$strOrder);
$strOrder = str_replace('u.age','u.birthdate',$strOrder);
// second order
if ( !strstr($strOrder,'lastname') ) $strOrder .= ',u.lastname';

$oDB->Query( LimitSQL($strFlds.$strFrom.$strWhere,$strOrder,$intLimit,$_SESSION[QT]['items_per_page']) );

$strAlt='r1';
$arrRow=array(); // rendered row. To remove duplicate in seach result

while($row=$oDB->Getrow())
{
  if ( in_array((int)$row['id'], $arrRow) ) continue; // this remove duplicate users in case of search result
  if ( empty($row['lastname']) ) $row['lastname']='('.L('unknown').')';

  // prepare row

  $table->row = new cTableRow( 'tr_t1_cb'.$row['id'], 't-item '.$strAlt.' rowlight' );

  $oItem = new cItem($row,true);
  $arrRow[] = $oItem->id;
  $arrRendered = cSection::RenderFields($arrFLD,$oItem,$qte_root,$qte_web_link);

  // show table

  $table->SetTDcontent($arrRendered,false); // populate all td  (without creating extra td)
  echo $table->GetTDrow().PHP_EOL;

  if ( $strAlt==='r1' ) { $strAlt='r2'; } else { $strAlt='r1'; }


}

// === TABLE END DISPLAY ===

echo '</tbody>',PHP_EOL;
echo '</table>',PHP_EOL;

// --------
// Display bottom pager
// --------

if ( $strPager )
{
echo '
<p class="pager-zb">',$strPager,'</p>
';
}

// --------
// HTML END
// --------

echo '
<!-- COPYRIGHT LINE -->
<div class="footer">
<div class="footerright"><a href="',$_SESSION[QT]['site_url'],'/qte_index.php" class="footer_copy">',$_SESSION[QT]['site_name'],'</a></div>
</div>
<!-- END COPYRIGHT LINE -->
';

if ( $qte_web_head ) echo '</body></html>';