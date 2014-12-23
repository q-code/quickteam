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
if ( !sUser::CanView('V2') ) { $oHtml->PageMsg(11); return; }
if ( isset($_POST['Maction']) &&  $_POST['Maction']=='email' ) $oHtml->Redirect('qte_email.php?'.GetUri('group,page'),L('Emails'));

$s = ''; // section $s can be '*' or [int] (after argument checking only [int] is allowed)
$q = '';
QThttpvar('s q','str str');
if ( $s==='*' || $s==='' ) $s=-1;
if ( !is_int($s) ) $s=(int)$s;
if ( $s<0 && empty($q) ) die('Missing argument $s or $q...');
$_SESSION[QT]['section']= $s; // previous section

// ---------
// INITIALISE
// ---------

$oVIP->selfurl = 'qte_section.php';
$oVIP->selfname = $L['Section'];
$oVIP->exitname = ObjTrans('index','i');

$strFlds  = ' u.*';
$strFrom  = ' FROM '.TABUSER.' u INNER JOIN '.TABS2U.' l ON l.userid=u.id';
$strWhere = ' WHERE u.id>0';
$strGroup = 'all';
$strOrder = 'lastname';
$strDirec = 'asc';
$intLimit = 0;
$intPage  = 1;

// security check 1
if ( isset($_GET['group']) ) $strGroup = strip_tags($_GET['group']);
if ( isset($_GET['order']) ) $strOrder = strip_tags($_GET['order']);
if ( isset($_GET['dir']) ) $strDirec = strtolower(strip_tags($_GET['dir']));
if ( isset($_GET['page']) ) $intPage = intval(strip_tags($_GET['page']));

// security check 2 (no long argument)
if ( isset($strGroup[7]) ) die('Invalid argument #group');
if ( isset($strOrder[12]) ) die('Invalid argument #order');
if ( isset($strDirec[4]) ) die('Invalid argument #dir');

$intLimit = ($intPage-1)*$_SESSION[QT]['items_per_page'];

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

// Staff preferences (POST from qte_p_menu)

if ( isset($_POST['Maction']) )
{
  if ( $_POST['Maction']=='add' ) $oHtml->Redirect('qte_adm_users_move.php?s='.$s,$L['User_man']);
  if ( $_POST['Maction']=='new' ) $oHtml->Redirect('qte_adm_users.php?s='.$s.'&amp;add',$L['User_add']);
  if ( $_POST['Maction']=='email' ) $oHtml->Redirect('qte_email.php?'.GetUri('page'),L('Emails'));
  if ( $_POST['Maction']=='show_Z' ) $_SESSION[QT]['show_Z']='1';
  if ( $_POST['Maction']=='hide_Z' ) $_SESSION[QT]['show_Z']='0';
  if ( isset($_POST['infofield']) ) $_SESSION[QT]['infofield']=$_POST['infofield']; // can be "" or '0'
}

// Initialize query

if ( !$_SESSION[QT]['show_Z'] ) $strWhere .= ' AND u.status<>"Z"';

if ( $s>=0 && empty($q) )
{
  $strWhere .= ' AND l.sid='.$s;
  switch ($strGroup)
  {
    case 'all': break;
    case '0': $strWhere = ' AND '.FirstCharCase('u.firstname','a-z').' AND '.FirstCharCase('u.lastname','a-z'); break;
    default:
      $arr = explode('|',$strGroup);
      $arrOr = array();
      foreach($arr as $str)
      {
      $i=strlen($str);
      $arrOr[] = FirstCharCase('u.firstname','u',$i).'="'.strtoupper($str).'"';
      $arrOr[] = FirstCharCase('u.lastname','u',$i).'="'.strtoupper($str).'"';
      }
      $strWhere = ' WHERE id>0 AND ('.implode(' OR ',$arrOr).')';
      break;
  }
  $strCount = 'SELECT count(*) as countid'.$strFrom.$strWhere;
  
  
}
elseif ( !empty($q) )
{
  include 'qte_section_qry.php';
}
else
{
  die('Missing argument $s or $q...');
}

// COUNT Members using memcacheQuery (result must be in field 'countid')

$intCount = memcacheQueryCount(0,$strCount);

// Usermenu

if ( sUser::IsStaff() ) include 'qte_inc_menu.php';

// Pager

$strPager = MakePager(Href().'?'.GetUri('page'),$intCount,$_SESSION[QT]['items_per_page'],$intPage);
if ( $strPager!='' ) { $strPager = $L['Page'].$strPager; } else { $strPager='&nbsp;'; }
if ( empty($q) && $intCount<$oSEC->members ) $strPager = '<span class="small">'.$intCount.' '.$L['Selected_from'].' '.$oSEC->members.' '.strtolower($L['Users']).'</span>'.($strPager==S ? '' : ' | '.$strPager);

// MAP MODULE

$bMap=false;
if ( UseModule('map') )
{
  include Translate('qtem_map.php');
  include 'qtem_map_lib.php';
  if ( QTgcanmap((empty($q) ? $s : 'S'),sUser::Role()) ) $bMap=true;
  if ( $bMap ) $oHtml->links[]='<link rel="stylesheet" type="text/css" href="qtem_map.css" />';

  if ( isset($_GET['hidemap']) ) $_SESSION[QT]['m_map_hidelist']=true;
  if ( isset($_GET['showmap']) ) $_SESSION[QT]['m_map_hidelist']=false;
  if ( !isset($_SESSION[QT]['m_map_hidelist']) ) $_SESSION[QT]['m_map_hidelist']=false;
}

// --------
// HTML START
// --------

include 'qte_inc_hd.php';

if ( !empty($strPageMenu) ) echo $strPageMenu;

// Display description

if ( !empty($q) )
{
$oSEC->name = $oVIP->selfname;
$oSEC->members = $intCount;
}
$oSEC->descr .= (strlen($oSEC->descr)<15 ? ' ' : '<br/>').'<span class="small">('.L('User',$oSEC->members).')</span>';
if ( isset($_SESSION[QT]['section_descr']) && $_SESSION[QT]['section_descr'] )
{
  $oSEC->ShowInfo('sectioninfo-left','sectioninfo','sectiondesc');
}
else
{
  $oSEC->ShowInfo('sectioninfo-left compact','sectioninfo compact','sectiondesc compact');
}

// Display letters bar (not for search result)

if ( empty($q) )
{
  if ( $oSEC->members>$_SESSION[QT]['items_per_page'] || isset($_GET['group']) )
  {
    if ( $oSEC->members>500 ) { $intChars=1; } else { $intChars=($oSEC->members>((int)$_SESSION[QT]['items_per_page'])*2 ? 2 : 3); }
    $str = ObjTrans('field','lastname').' '.L('or').' '.strtolower(ObjTrans('field','firstname')).' '.L('starting_with').' ';
    echo PHP_EOL,HtmlLettres(Href().'?'.GetUri('group,page'),$strGroup,L('All'),'lettres clear',$str,$intChars),PHP_EOL;
  }
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

echo '
<p class="pager-zt">',$strPager,'</p>
';

// --------
// Prepare fields
// --------

$oSEC->forder .= (empty($strInfofield) ? '' : ';'.$strInfofield ); // in case of search: ufield replaces infofield
$arrFLD = cField::ArrayFields($oSEC->forder); // only ON column
if ( $q=='kwd' ) $arrFLD['ufield']=new cField('ufield');

// check current order (if using default)

if ( !array_key_exists($strOrder,$arrFLD) )$strOrder='fullname'; if ( !array_key_exists($strOrder,$arrFLD) ) $strOrder='username';

if ( isset($arrFLD['status']) )
{
$arrStatusStyles=array();
foreach(memGet('sys_statuses') as $id=>$arrStatus) if ( !empty($arrStatus['color']) ) $arrStatusStyles[$id]=$arrStatus['color'];
}

$bLink = true;
if ( isset($qte_web_link) ) {
if ( !$qte_web_link ) {
  $bLink = false;
}}

// --------
// Display members
// --------

$table = new cTable('t1','t-item',$intCount);
$table->activecol = $strOrder;
$table->activelink = '<a  href="'.Href().'?'.GetUri('page,order,dir').'&amp;order='.$strOrder.'&amp;dir='.($strDirec=='asc' ? 'desc' : 'asc').'">%s</a><img class="i-sort '.$strDirec.'" src="bin/css/null.gif" alt="+"/>';

// === TABLE DEFINITION ===

foreach($arrFLD as $key=>$oField)
{
  // field definition
  if ( !isset($table->th[$key]) ) $table->th[$key] = new cTableHead($oField->name,'','c-'.$key,'<a href="'.Href().'?'.GetUri('page,order,dir').'&amp;order='.$key.'&amp;dir=asc">%s</a>');
  // exception
  switch ($key)
  {
    case 'status_i': $table->th['status_i']->content = '&bull;'; $table->th['status_i']->link = '<a href="'.Href().'?'.GetUri('page,order,dir').'&amp;order=status&amp;dir=asc">%s</a>'; break;
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

// prepare dynamic attributes for column 'status'
if ( isset($table->td['status']) )
{
  foreach(memGet('sys_statuses') as $id=>$arr) if ( !empty($arr['color']) ) $table->td['status']->dynamicValues[$id]='background-color:'.$arr['color'].';';
}

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

$oDB->Query( LimitSQL($strFlds.$strFrom.$strWhere,$strOrder,$intLimit,$_SESSION[QT]['items_per_page']+20) );

$strAlt='r1';
$arrRow=array(); // rendered row. To remove duplicate in seach result
$intRow=0; // count row displayed

while($row=$oDB->Getrow())
{

  if ( in_array((int)$row['id'], $arrRow) ) continue; // this remove duplicate users in case of search result
  if ( empty($row['lastname']) ) $row['lastname']='('.L('unknown').')';

	// prepare row
  $table->row = new cTableRow( 'tr_t1_cb'.$row['id'], 't-item hover rowlight '.$strAlt );

  $oItem = new cItem($row,true);
  $arrRow[] = $oItem->id;
  $arrRendered = cSection::RenderFields($arrFLD,$oItem,$qte_root,true);

  // coord

  if ( $bMap && !empty($row['y']) && !empty($row['y']) )
  {
    $y = (float)$row['y'];
    $x = (float)$row['x'];
    $strCoord = '<a class="gmappoint" href="javascript:void(0)"'.(!$_SESSION[QT]['m_map_hidelist'] ? ' onclick="gmapPan(\''.$y.','.$x.'\'); return false;"' : '').' title="'.$L['Coord'].': '.round($y,8).','.round($x,8).'"><img class="gmappoint" src="'.$_SESSION[QT]['skin_dir'].'/ico_user_m_1.gif" alt="G" title="'.$L['Coord_latlon'].' '.QTdd2dms($y).','.QTdd2dms($x).'" /></a>';
    $strLatLon = QTdd2dms($y).'<br />'.QTdd2dms($x);
    if ( empty($strCoord) ) $strCoord = '<img class="gmapnopoint" src="'.$_SESSION[QT]['skin_dir'].'/ico_user_m_0.gif" alt="x" title="No coordinates" />';

    // map points
    $str = QTconv(substr($oItem->fullname,0,50),'3',QTE_CONVERT_AMP);
    // add telephone and mail if not private
    if ( !strstr($oItem->privacy,"phones") ) $str .= '<br/>'.str_replace('; ','<br/>',$oItem->phones);
    $str .= '<br/>'.( !strstr($oItem->privacy,'emails') ? '<a class="gmap" href="mailto:'.$oItem->emails.'" onclick="infowindow.close()">'.$L['Email'].'</a> &middot; ' : '').'<a class="gmap" href="qte_user.php?id='.$oItem->id.'" onclick="infowindow.close()">'.L('map_View_item').'</a>';
    // add picture
    if ( !empty($oItem->picture) )
    {
    $str = '<table class="gmap"><tr><td>'.AsImg(QTE_DIR_PIC.$oItem->picture,'',$oItem->lastname,'gmap_userpreview').'</td><td>'.$str.'</td></tr></table>';
    }
    $oMapPoint = new cMapPoint($y,$x,QTconv(substr($oItem->firstname.' '.$oItem->lastname,0,50),'-4'),$str );
    if ( isset($_SESSION[QT]['m_map'][$s]['icon']) )        $oMapPoint->icon = $_SESSION[QT]['m_map'][$s]['icon'];
    if ( isset($_SESSION[QT]['m_map'][$s]['shadow']) )      $oMapPoint->shadow = $_SESSION[QT]['m_map'][$s]['shadow'];
    if ( isset($_SESSION[QT]['m_map'][$s]['printicon']) )   $oMapPoint->printicon = $_SESSION[QT]['m_map'][$s]['printicon'];
    if ( isset($_SESSION[QT]['m_map'][$s]['printshadow']) ) $oMapPoint->printshadow = $_SESSION[QT]['m_map'][$s]['printshadow'];
    $arrExtData[(int)$oItem->id] = $oMapPoint;

    // if ( $bMap && empty($strCoord) ) $strCoord = '<img class="ico i-user" src="'.$_SESSION[QT]['skin_dir'].'/ico_user_m_0.gif" alt="G" title="No coordinates" />';
    if ( !empty($strCoord) )
    {
      foreach(array('coord','fullname','lastname','firstname','emails_i','address','username') as $key )
      {
      if ( isset($arrRendered[$key]) ) { $arrRendered[$key] .= ' '.$strCoord; $strCoord=''; break; }
      }
    }
  }

  //ufield
  if ( isset($arrRendered['ufield']) && $q=='kwd' ) $arrRendered['ufield'] .= ': '.$v;

  // show table

  $table->SetTDcontent($arrRendered,false); // populate all td  (without creating extra td)
  if ( isset($table->td['status']) ) $table->td['status']->AddDynamicAttr('style',$oItem->status);

  echo $table->GetTDrow().PHP_EOL;

  if ( $strAlt==='r1' ) { $strAlt='r2'; } else { $strAlt='r1'; }
  ++$intRow; if ( $intRow>=$_SESSION[QT]['items_per_page'] ) break;
}

// === TABLE END DISPLAY ===

echo '</tbody>',PHP_EOL;
echo '</table>',PHP_EOL;

// --------
// Display bottom pager
// --------

// define csv options according to $intCount (max 10000)

$strCsv ='';
if ( sUser::Role()!='V' )
{
  if ( $intCount<=$_SESSION[QT]['items_per_page'] )
  {
  $strCsv = '<a href="'.Href('qte_section_csv.php').'?'.GetUri('page').'&amp;size=all&amp;n='.$intCount.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].'</a>';
  }
  else
  {
  $strCsv = '<a href="'.Href('qte_section_csv.php').'?'.GetUri('page').'&amp;size=p'.$intPage.'&amp;n='.$intCount.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].' <span class="small">('.strtolower($L['Page']).')</span></a>';
  if ( $intCount<=1000 )                   $strCsv .= ' &middot; <a href="'.Href('qte_section_csv.php').'?'.GetUri('page').'&amp;size=all&amp;n='.$intCount.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].' <span class="small">('.strtolower($L['All']).')</span></a>';
  if ( $intCount>1000 && $intCount<=2000 ) $strCsv .= ' &middot; <a href="'.Href('qte_section_csv.php').'?'.GetUri('page').'&amp;size=m1&amp;n='.$intCount.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].' <span class="small">(1-1000)</span></a> &middot; <a href="'.Href('qte_section_csv.php').'?'.GetUri('page').'&amp;size=m2&amp;n='.$intCount.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].' <span class="small">(1000-'.$intCount.')</span></a>';
  if ( $intCount>2000 && $intCount<=5000 ) $strCsv .= ' &middot; <a href="'.Href('qte_section_csv.php').'?'.GetUri('page').'&amp;size=m5&amp;n='.$intCount.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].' <span class="small">(1-5000)</span></a>';
  if ( $intCount>5000 )                    $strCsv .= ' &middot; <a href="'.Href('qte_section_csv.php').'?'.GetUri('page').'&amp;m=5&amp;n='.$intCount.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].' <span class="small">(1-5000)</span></a> &middot; <a href="'.Href('qte_section_csv.php').'?'.GetUri('page').'&amp;m=10&amp;n='.$intCount.'" class="tablecommand csv" title="'.$L['H_Csv'].'">'.$L['Csv'].' <span class="small">(5000-10000)</span></a>';
  }
}
if ( !empty($strCsv) )
{
  $strPager = $strCsv.( $strPager==S || empty($strPager) ? '' : ' &middot; '.$strPager );
}

// calendar view (if allowed)

$strCal ='';
if ( cVIP::CanViewCalendar() )
{
  $i = (isset($s) ? (int)$s : -1);
  if ( $i<0 && isset($_SESSION[QT]['section']) && $_SESSION[QT]['section']>=0 ) $i=$_SESSION[QT]['section'];
  if ( $i>=0 ) $strCal = '<a href="'.Href('qte_calendar.php').'?s='.$i.'" class="tablecommand csv">'.$L['Birthdays_calendar'].'</a>';
}
if ( !empty($strCal) )
{
  $strPager = $strCal.( $strPager==S || empty($strPager) ? '' : ' &middot; '.$strPager );
}

// show table command

if ( !empty($strPager) && $strPager!=S )
{
echo '
<p class="pager-zb">',$strPager,'</p>
';
}

// --------
// Map module
// --------

if ( $bMap )
{
  echo PHP_EOL,'<!-- Map module -->',PHP_EOL;
  if ( count($arrExtData)==0 )
  {
    echo '<p class="gmap nomap">'.L('map_No_coordinates').'</p>';
    $bMap=false;
  }
  else
  {
    //select zoomto (maximum 20 items in the list)
    $str = '';
    if ( count($arrExtData)>1 )
    {
      $str = '<p class="gmap commands" style="margin:0 0 4px 0"><a class="gmap" href="javascript:void(0)" onclick="zoomToFullExtend(); return false;">'.$L['map_zoomtoall'].'</a> | '.L('Show').' <select class="gmap" id="zoomto" name="zoomto" size="1" onchange="gmapPan(this.value);">';
      $str .= '<option value="'.$_SESSION[QT]['m_map_gcenter'].'"> </option>';
      $i=0;
      foreach($arrExtData as $oMapPoint)
      {
      $str .= '<option value="'.$oMapPoint->y.','.$oMapPoint->x.'">'.$oMapPoint->title.'</option>';
      ++$i; if ( $i>20 ) break;
      }
      $str .= '</select></p>';
    }

    echo '<div class="gmap">',PHP_EOL;
    echo ($_SESSION[QT]['m_map_hidelist'] ? '' : $str.PHP_EOL.'<div id="map_canvas"></div>'.PHP_EOL);
    echo '<p class="gmap" style="margin:4px 0 0 0">',sprintf($L['map_items'],strtolower( L('User',count($arrExtData))),strtolower(L('User',$intCount)) ),'</p>',PHP_EOL;
    echo '</div>',PHP_EOL;

    // Show/Hide

    if ( $_SESSION[QT]['m_map_hidelist'] )
    {
    echo '<div class="canvashandler"><a class="canvashandler" href="',Href(),'?'.GetUri('showmap,hidemap').'&amp;showmap"><img class="canvashandler down" src="bin/css/null.gif" alt="+"/>',$L['map_Show_map'],'</a></div>',PHP_EOL;
    }
    else
    {
    echo '<div class="canvashandler"><a class="canvashandler" href="',Href(),'?'.GetUri('showmap,hidemap').'&amp;hidemap"><img class="canvashandler up" src="bin/css/null.gif" alt="-"/>',$L['map_Hide_map'],'</a></div>',PHP_EOL;
    }
  }
  echo '<!-- Map module end -->',PHP_EOL,PHP_EOL;
}

// --------
// HTML END
// --------

// MAP MODULE

if ( $bMap && !$_SESSION[QT]['m_map_hidelist'] )
{
  $gmap_shadow = false;
  $gmap_symbol = false;
  if ( !empty($_SESSION[QT]['m_map_gsymbol']) )
  {
    $arr = explode(' ',$_SESSION[QT]['m_map_gsymbol']);
    $gmap_symbol=$arr[0];
    if ( isset($arr[1]) ) $gmap_shadow=$arr[1];
  }

  // check new map center
  $y = floatval(QTgety($_SESSION[QT]['m_map_gcenter']));
  $x = floatval(QTgetx($_SESSION[QT]['m_map_gcenter']));

  // center on the first item
  foreach($arrExtData as $oMapPoint)
  {
    if ( !QTgemptycoord($oMapPoint) )
    {
    $y=$oMapPoint->y;
    $x=$oMapPoint->x;
    break;
    }
  }
  // update center
  $_SESSION[QT]['m_map_gcenter'] = $y.','.$x;

  $gmap_markers = array();
  $gmap_events = array();
  $gmap_functions = array();
  foreach($arrExtData as $oMapPoint)
  {
    if ( !QTgemptycoord($oMapPoint) )
    {
      $user_symbol = $gmap_symbol; // required to reset symbol on each user
      $user_shadow = $gmap_shadow;
      if ( !empty($oMapPoint->icon) ) $user_symbol = $oMapPoint->icon;
      if ( !empty($oMapPoint->shadow) ) $user_shadow = $oMapPoint->shadow;
      $gmap_markers[] = QTgmapMarker($oMapPoint->y.','.$oMapPoint->x,false,$user_symbol,$oMapPoint->title,$oMapPoint->info,$user_shadow);
    }
  }
  $gmap_functions[] = '
  function zoomToFullExtend()
  {
    if ( markers.length<2 ) return;
    var bounds = new google.maps.LatLngBounds();
    for (var i=markers.length-1; i>=0; i--) bounds.extend(markers[i].getPosition());
    map.fitBounds(bounds);
  }
  function showLocation(address)
  {
    if ( infowindow ) infowindow.close();
    geocoder.geocode( { "address": address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK)
      {
        map.setCenter(results[0].geometry.location);
        if ( marker )
        {
          marker.setPosition(results[0].geometry.location);
        } else {
          marker = new google.maps.Marker({map: map, position: results[0].geometry.location, draggable: true, animation: google.maps.Animation.DROP, title: "Move to define the default map center"});
        }
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
  ';
  include 'qtem_map_load.php';
}

include 'qte_inc_ft.php';